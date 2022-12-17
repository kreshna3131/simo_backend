<?php

namespace App\Http\Controllers\v1;

use DateTime;
use App\Models\IcdNine;
use App\Models\ActivityLog;
use App\Models\IcdNineFill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Custom\Pagination\CustomPaginator;
use App\Http\Requests\IcdNineFillerRequest;
use App\Models\IcdTen;
use App\Models\Soap;
use Barryvdh\DomPDF\Facade\Pdf;

class IcdNineController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
        $this->middleware('permission:lihat icd 9|tambah icd 9|ubah icd 9', ['only' => ['listing', 'store', 'edit', 'update', 'updateStatus']]);
        $this->middleware('permission:lihat icd 9', ['only' => ['listing']]);
        $this->middleware('permission:tambah icd 9', ['only' => ['store']]);
        $this->middleware('permission:ubah icd 9', ['only' => ['edit', 'update']]);
        $this->middleware('permission:ubah status icd 9', ['only' => ['updateStatus']]);
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
     * listing assesment untuk ICD
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @return void
     */
    public function listingAssesment(Request $request, $visitId)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $itemPerPage = $request->pagination ? $request->pagination : 5;
            $currentPage = $request->get('page', 1);
            $assesments = Soap::select(
                'soaps.id as soap_id',
                'assesments.id as assesment_id',
                'sub_assesments.id as sub_assesment_id',
                'templates.type as sub_assesment_type',
                'sub_assesments.created_at as created_at'
            )
                ->leftJoin("assesments", "soaps.id", "=", "assesments.soap_id")
                ->leftJoin("sub_assesments", "assesments.id", "=", "sub_assesments.assesment_id")
                ->leftJoin("templates", "sub_assesments.template_id", "=", "templates.id")
                ->where('soaps.no_rm', $norm)
                ->orderBy($request->order_by ? $request->order_by : 'sub_assesments.created_at', $request->order_dir ? $request->order_dir : 'desc');

            $assesmentPaginate = (new CustomPaginator(
                $assesments->clone()->forPage($currentPage, $itemPerPage)->get(),
                $assesments->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL') . '/visit/' . $visitId . '/icd/listing-assesment');
            
            return response()->json($assesmentPaginate);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive listing assesment.',
            ], 500);
        }
    }

    /**
     * Listing ICD 9 untuk User
     *
     * @param  Request $request
     * @param  integer $visitId
     * @return Response
     */
    public function listing(Request $request, $visitId)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $icdNines = IcdNine::query()
                ->where('visit_id', $visitId)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('no_rm', 'like', '%' . $request->search . '%')
                            ->orWhere('kode', 'like', '%' . $request->search . '%')
                            ->orWhere('name', 'like', '%' . $request->search . '%')
                            ->orWhere('created_by', 'like', '%' . $request->search . '%')
                            ->orWhere('created_role', 'like', '%' . $request->search . '%');
                        if ($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                        }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $icdNinePaginate = (new CustomPaginator(
                $icdNines->clone()->forPage($currentPage, $itemPerPage)->get(),
                $icdNines->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL') . '/visit/' . $visitId . '/icd-nine/listing');

            return response()->json($icdNinePaginate);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive icd nine.',
            ], 500);
        }
    }

    /**
     * Listing Select (Dropdown) untuk menambah tindakan ICD-9
     *
     * @param  IcdNineFillerRequest $request
     * @return Response
     */
    public function listingDropdownFiller(IcdNineFillerRequest $request)
    {
        try {
            $icdNineFillers = IcdNineFill::query()
                ->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('kode', 'like', '%' . $request->search . '%')
                ->get();

            info($icdNineFillers);

            return response()->json($icdNineFillers);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive ICD 9'
            ], 500);
        }
    }

    /**
     * Menambah tindakan ICD-9
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
            if (IcdNine::where('visit_id', $visitId)->count() == 0) {
                $visit_count = IcdNine::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = IcdNine::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }

            $validated = $request->validate(['name' => 'required', 'kode' => 'required']);

            $icdNine = IcdNine::create($validated + [
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
                'visit_id' => $icdNine->visit_id,
                'unique_id' => $icdNine->id,
                'request_id' => $icdNine->id,
                'note' => 'Menambah permintaan ICD-9 untuk ' . $icdNine->name,
                'type' => 'ICD-9',
                'action' => 'Menambah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully update ICD 9'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed store ICD 9'
            ], 500);
        }
    }

    /**
     * Menampilkan form untuk mengupdate tindakan ICD-9
     *
     * @param  IcdNine $icdNine
     * @return Response
     */
    public function edit(IcdNine $icdNine)
    {
        try {
            return response()->json($icdNine);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive ICD 9'
            ], 500);
        }
    }

    /**
     * Menampilkan form untuk mengupdate tindakan ICD-9
     *
     * @param  IcdNine $icdNine
     * @return Response
     */
    public function update(Request $request, IcdNine $icdNine)
    {
        try {
            $validated = $request->validate(['name' => 'required', 'kode' => 'required']);

            $icdNine->update($validated);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $icdNine->visit_id,
                'unique_id' => $icdNine->id,
                'request_id' => $icdNine->id,
                'note' => 'Mengubah permintaan ICD-9',
                'type' => 'ICD-9',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully store ICD 9'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive ICD 9'
            ], 500);
        }
    }

    /**
     * Menghapus Tindakan ICD-9 (Set status menjadi false)
     *
     * @param  Request $request
     * @param  IcdNine $icdNine
     * @return Response
     */
    public function updateStatus(Request $request, IcdNine $icdNine)
    {
        try {
            $icdNine->update([
                'is_add' => 0
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $icdNine->visit_id,
                'unique_id' => $icdNine->id,
                'request_id' => $icdNine->id,
                'note' => 'Membatalkan permintaan ICD-9',
                'type' => 'ICD-9',
                'action' => 'Membatalkan'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'ICD nine deleted succesfully.',
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed delete icd nine.',
            ], 500);
        }
    }

    /**
     * print history rekam medis icd
     *
     * @param  mixed $visitId
     * @return void
     */
    public function printHistoryIcd($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $loop_visit = '';
            if(IcdNine::where('no_rm', $norm)->groupBy('visit_id')->count() > 0) {
                $loop_visit = IcdNine::where('no_rm', $norm)->groupBy('visit_id')->get();
            } else {
                $loop_visit = IcdTen::where('no_rm', $norm)->groupBy('visit_id')->get();
            }

            info($loop_visit);

            foreach ($loop_visit as $key => $loop) {
                $icdNines = IcdNine::where('visit_id', $loop->visit_id)->get();
                $icdTens = IcdTen::where('visit_id', $loop->visit_id)->get();
                $view = view('pdf.icd')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'icdNines' => $icdNines,
                    'icdTens' => $icdTens,
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->download('Histori Rekam Medis ICD.pdf');
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download history icd.',
            ], 500);
        }
    }

    /**
     * print history rekam medis icd
     *
     * @param  mixed $visitId
     * @return void
     */
    public function previewHistoryIcd($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $loop_visit = '';
            if(IcdNine::where('no_rm', $norm)->groupBy('visit_id')->count() > 0) {
                $loop_visit = IcdNine::where('no_rm', $norm)->groupBy('visit_id')->get();
            } else {
                $loop_visit = IcdTen::where('no_rm', $norm)->groupBy('visit_id')->get();
            }

            info($loop_visit);

            foreach ($loop_visit as $key => $loop) {
                $icdNines = IcdNine::where('visit_id', $loop->visit_id)->get();
                $icdTens = IcdTen::where('visit_id', $loop->visit_id)->get();
                $view = view('pdf.icd')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'icdNines' => $icdNines,
                    'icdTens' => $icdTens,
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->stream();
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download history icd.',
            ], 500);
        }
    }
}
