<?php

namespace App\Http\Controllers\v1;

use DateTime;
use App\Models\RequestRehab;
use App\Models\ActionRehab;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Custom\Pagination\CustomPaginator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RequestRehabController extends Controller
{
    /**
     * Function permission   
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:melihat permintaan rehab medik di kunjungan|tambah permintaan rehab medik di kunjungan|mengelola permintaan di rehab medik',['only' => ['listingRehabUser', 'store', 'listingRehab']]);
         $this->middleware('permission:melihat permintaan rehab medik di kunjungan', ['only' => ['listingRehabUser']]);
         $this->middleware('permission:tambah permintaan rehab medik di kunjungan', ['only' => ['store']]);
         $this->middleware('permission:mengelola permintaan di rehab medik', ['only' => ['listingRehab']]);
         $this->middleware('permission:lihat histori rekam medis', ['only' => ['printHistoryRehab', 'previewHistoryRehab']]);
    }

     /**
     * Listing permintaan Rehab untuk user (selain Petugas Rehab)
     *
     * @param  Request $request
     * @param  integer $visitId
     * @return Response
     */
    public function listingRehabUser(Request $request, $visitId)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestRehabs = RequestRehab::query()
                ->where('visit_id', $visitId)
                ->when($request->filled('search'), function ($query) use($request) {
                    $query->where(function ($query) use($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('unique_id', 'like', '%' . $request->search . '%')
                            ->orWhere('info', 'like', '%' . $request->search . '%')
                            ->orWhere('created_by', 'like', '%' . $request->search . '%');
                            if ($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            }
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
                
            $requestRehabPaginate = (new CustomPaginator(
                $requestRehabs->clone()->forPage($currentPage, $itemPerPage)->get(),
                $requestRehabs->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/visit/'.$visitId.'/rehab/listing');

            return response()->json($requestRehabPaginate);
        }

        catch (\Throwable $th) {
            info($th->getMessage());
            
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive request rehab'
            ], 500);
        }
    }

    /**
     * Listing permintaan Rehab untuk Petugas Rehab
     *
     * @param  Request $request
     * @return Response
     */
    public function listingRehab(Request $request)
    {
        try {
            $date_start = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $date_end = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestRehabs = RequestRehab::query()
                ->when($request->filled('search'), function ($query) use($request) {
                    $query->where(function ($query) use($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('unique_id', 'like', '%' . $request->search . '%')
                            ->orWhere('info', 'like', '%' . $request->search . '%')
                            ->orWhere('created_by', 'like', '%' . $request->search . '%')
                            ->orWhere('updated_by', 'like', '%' . $request->search . '%')
                            ->orWhere('created_for', 'like', '%' . $request->search . '%');
                            if ($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            }
                    });
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->orWhere(function($query) use ($request) {
                        $query->where('status', $request->status);
                    });
                })
                ->when($request->filled('is_read'), function ($query) use ($request) {
                    $query->orWhere(function($query) use ($request) {
                        $query->where('is_read_rehab', $request->is_read);
                    });
                })
                ->when($request->filled('periode'), function ($query) use ($date_start, $date_end) {
                    $query->orWhere(function($query) use ($date_start, $date_end) {
                        $query->whereBetween('created_at', ["$date_start 00:00:00", "$date_end 23:59:59"]);

                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
            
                $requestRehabPaginate = (new CustomPaginator(
                    $requestRehabs->clone()->forPage($currentPage, $itemPerPage)->get(),
                    $requestRehabs->clone()->count(),
                    $itemPerPage,
                    $currentPage
                ))
                    ->withQueryString()
                    ->withPath(env('APP_URL').'/visit/rehab/listing');
    
                return response()->json(array_merge(
                    $requestRehabPaginate->toArray(), 
                    ['is_read_count' => $requestRehabs->clone()->where('is_read_rehab', 0)->count()]
                ));
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive request rehab'
            ], 500);
        }
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
     * Tambah permintaan Rehab Medic
     *
     * @param  Request $request
     * @param  integer $visitId
     * @return Response
     */
    public function store(Request $request, $visitId)
    {
        try {
            $validated = $request->validate(['rehab_id' => 'required']);

            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $nama = $visits->status() == 200 ? $visits[0]['nama'] : '';
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            if(RequestRehab::where('visit_id', $visitId)->count() == 0) {
                $visit_count = RequestRehab::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = RequestRehab::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }
            $lastTodayRequest = RequestRehab::query()->today()->latest()->first();
            $requestRehab = RequestRehab::create([
                'visit_id' => $visitId,
                'no_rm' => $norm,
                'user_id' => auth()->user()->id,
                'unique_id' => RequestRehab::generateUniqueId($lastTodayRequest),
                'status' => 'waiting',
                'created_by' => auth()->user()->name,
                'created_for' => $nama,
                'visit_number' => $visit_count
            ]);

            $rehabs = [];
            // info($request);
            foreach ($request->rehab_id as $key => $action) {
                // info($action);
                ActionRehab::create([
                    'request_rehab_id' => $requestRehab->id,
                    'action_id' => $request->rehab_id[$key],
                    'action_group_id' => $request->rehab_group_id[$key],
                    'action_group' => $request->rehab_group[$key],
                    'name' => $request->name[$key],
                    'status' => 'unfinish'
                ]);
                $rehabs[] = $request->name[$key];
            }
            
            info($requestRehab->id);
            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $requestRehab->unique_id,
                'visit_id' => $requestRehab->visit_id,
                'request_id' => $requestRehab->id,
                'note' => 'Membuat permintaan rehab medic untuk tindakan ' . implode(', ', $rehabs),
                'type' => 'Rehab Medic',
                'action' => 'Membuat'
            ]);

            $rehabCount = $requestRehab->actionRehabs->count();
            $commentCount = $requestRehab->commentRehabs->count();
            $requestRehab->update([
                "info" => $rehabCount. ' Tindakan dan '. $commentCount . ' Komentar'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request rehab successfully added'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed store request rehab'
            ], 500);
        }
    }

    /**
     * Untuk melihat data detail permintaan
     *
     * @param  mixed $requestLab
     * @return void
     */
    public function edit(RequestRehab $requestRehab)
    {
        try {
            return response()->json($requestRehab);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request rehab.',
            ], 500);
        }
    }

    /**
     * Updating Status Request Rehab
     *
     * @param  Request $request
     * @param  RequestRehab $requestRehab
     * @return Response
     */
    public function updateStatusRehab(Request $request, RequestRehab $requestRehab)
    {
        try {
            $validated = $request->validate(['status' => 'required']);
            $requestRehab->update($validated);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $requestRehab->unique_id,
                'visit_id' => $requestRehab->visit_id,
                'request_id' => $requestRehab->id,
                'note' => 'Mengubah permintaan rehab medic menjadi '. $requestRehab->logStatus($request),
                'type' => 'Rehab Medic',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request rehab status successfully updated'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update status request rehab'
            ], 500);
        }
    }

    public function printPDF(RequestRehab $requestRehab)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $requestRehab->visit_id);

            $pdf = Pdf::loadView('pdf.rehab', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'rehab' => $requestRehab,
            ]);

            return $pdf->download('Hasil Rehab Medik -'.$requestRehab->unique_id.'.pdf');
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf request rad.',
            ], 500);
        }
    }
    public function previewPDF(RequestRehab $requestRehab)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $requestRehab->visit_id);

            $pdf = Pdf::loadView('pdf.rehab', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'rehab' => $requestRehab,
            ]);

            return $pdf->stream();
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf request rad.',
            ], 500);
        }
    }

    /**
     * Print PDF E-Resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function printHistoryRehab($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $requestRehabs = RequestRehab::where('no_rm', $norm)->get();

            foreach ($requestRehabs as $key => $requestRehab) {
                $view = view('pdf.rehab')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'rehab' => $requestRehab,
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->download('Histori Rekam Medis Resep.pdf');
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf rehab.',
            ], 500);
        }
    }
        
    /**
     * Preview PDF E-Resep
     *
     * @param  mixed $recipe
     * @return void
     */
    public function previewHistoryRehab($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $requestRehabs = RequestRehab::where('no_rm', $norm)->get();

            foreach ($requestRehabs as $key => $requestRehab) {
                $view = view('pdf.rehab')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'rehab' => $requestRehab,
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->stream();
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf rehab.',
            ], 500);
        }
    }
}
