<?php

namespace App\Http\Controllers\v1;

use DateTime;
use App\Models\IcdTen;
use App\Models\IcdTenFill;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Custom\Pagination\CustomPaginator;
use App\Http\Requests\IcdTenFillerRequest;
use App\Models\IcdNine;

class IcdTenController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat icd 10|tambah icd 10|ubah icd 10', ['only' => ['listing', 'store', 'edit', 'update', 'updateStatus']]);
         $this->middleware('permission:lihat icd 10', ['only' => ['listing']]);
         $this->middleware('permission:tambah icd 10', ['only' => ['store']]);
         $this->middleware('permission:ubah icd 10', ['only' => ['edit', 'update']]);
         $this->middleware('permission:ubah status icd 10', ['only' => ['updateStatus']]);
    }

    /**
     * Function untuk generate header API SIMRS
     *
     * @return void
     */
    public function headerListing()
    {
        $tgl = new DateTime();
        $plaintext = $tgl->getTimeStamp();
        $strkey = "Kolega2022";
        $enc = $this->stringEncrypt($strkey, $plaintext);
        return [
            'timestamp' => $this->stringDecrypt($strkey, $enc),
            'sign' => $enc
        ];
    }

    /**
     * Function untuk enkripsi header API SIMRS
     *
     * @param  mixed $key
     * @param  mixed $data
     * @return void
     */
    public function stringEncrypt($key, $data)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('SHA256', $key));
        $iv = substr(hex2bin(hash('SHA256', $key)), 0, 16);
        $output = openssl_encrypt($data, $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return base64_encode($output);
    }

    /**
     * Function untuk dekrisp header API SIMRS
     *
     * @param  mixed $key
     * @param  mixed $data
     * @return void
     */
    public function stringDecrypt($key, $data)
    {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hex2bin(hash('SHA256', $key));
        $iv = substr(hex2bin(hash('SHA256', $key)), 0, 16);
        $output = openssl_decrypt(base64_decode($data), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
        return $output;
    }
    
    /**
     * Listing ICD 10 untuk User
     *
     * @param  Request $request
     * @param  integer $norm
     * @return Response
     */
    public function listing(Request $request, $visitId)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $icdTens = IcdTen::query()
                ->where('visit_id', $visitId)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('no_rm', 'like', '%' . $request->search . '%')
                            ->orWhere('kode', 'like', '%' . $request->search . '%')
                            ->orWhere('name', 'like', '%' . $request->search . '%')
                            ->orWhere('diagnosis_type', 'like', '%' . $request->search . '%')
                            ->orWhere('case', 'like', '%' . $request->search . '%')
                            ->orWhere('status', 'like', '%' . $request->search . '%')
                            ->orWhere('created_by', 'like', '%' . $request->search . '%')
                            ->orWhere('created_role', 'like', '%' . $request->search . '%');
                            if ($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
            
            $icdTenPaginate = (new CustomPaginator(
                $icdTens->clone()->forPage($currentPage, $itemPerPage)->get(),
                $icdTens->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/visit/'.$visitId.'/icd-ten/listing');

            return response()->json($icdTenPaginate);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive ICD 10'
            ], 500);
        }
    }

    /**
     * Listing Select (Dropdown) untuk menambah tindakan ICD-10
     *
     * @param  IcdTenFillerRequest $request
     * @param  IcdNine $icdNine
     * @param  integer $visitId
     * @return Response
     */
    public function listingDropdownFiller(IcdTenFillerRequest $request)
    {
        try {
            $icdTenFillers = IcdTenFill::query()
                ->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('kode', 'like', '%' . $request->search . '%')
                ->get();

            return response()->json($icdTenFillers);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive ICD 10'
            ], 500);
        }
    }

    /**
     * Menambah tindakan ICD-10
     *
     * @param  Request $request
     * @param  IcdNine $icdNine
     * @param  integer $visitId
     * @return Response
     */
    public function store(Request $request, $visitId)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            if(IcdNine::where('visit_id', $visitId)->count() == 0) {
                $visit_count = IcdNine::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = IcdNine::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }

            $validated = $request->validate([
                'name' => 'required', 
                'kode' => 'required',
                'diagnosis_type' => 'required',
                'case' => 'required',
                'status' => 'required'
            ]);

            $icdTen = IcdTen::create($validated + [
                'user_id' => auth()->user()->id,
                'no_rm' => $norm,
                'visit_id' => $visitId, 
                'created_by' => auth()->user()->name,
                'created_role' => auth()->user()->roles->first()->name,
                'visit_number' => $visit_count
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $icdTen->visit_id,
                'unique_id' => $icdTen->id,
                'request_id' => $icdTen->id,
                'note' => 'Menambah permintaan ICD 10 untuk '. $icdTen->name,
                'type' => 'ICD 10',
                'action' => 'Menambah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully store ICD 10'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed store ICD 10'
            ], 500);
        }
    }

    /**
     * Menampilkan form untuk mengupdate tindakan ICD-10
     *
     * @param  IcdTen $icdTen
     * @return Response
     */
    public function edit(IcdTen $icdTen)
    {
        try {
            return response()->json($icdTen);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive ICD 10'
            ], 500);
        }
    }

    /**
     * Mengubah tindakan ICD-10
     *
     * @param  Request $request
     * @param  IcdNine $icdNine
     * @param  integer $visitId
     * @return Response
     */
    public function update(Request $request, IcdTen $icdTen)
    {
        try {
            $validated = $request->validate([
                'name' => 'required', 
                'kode' => 'required',
                'diagnosis_type' => 'required',
                'case' => 'required',
                'status' => 'required'
            ]);

            $icdTen->update($validated);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $icdTen->visit_id,
                'unique_id' => $icdTen->id,
                'request_id' => $icdTen->id,
                'note' => 'Mengubah permintaan ICD 10',
                'type' => 'ICD 10',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully update ICD 10'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update ICD 10'
            ], 500);
        }
    }

    /**
     * Menghapus Tindakan ICD-10 (Set status menjadi false)
     *
     * @param  Request $request
     * @param  IcdTen $icdTen
     * @return Response
     */
    public function updateStatus(Request $request, IcdTen $icdTen)
    {
        try {
            $icdTen->update([
                'is_add' => 0
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $icdTen->visit_id,
                'unique_id' => $icdTen->id,
                'request_id' => $icdTen->id,
                'note' => 'Membatalkan permintaan ICD-10',
                'type' => 'ICD-10',
                'action' => 'Membatalkan'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed delete ICD-10'
            ], 500);
        }
    }
}
