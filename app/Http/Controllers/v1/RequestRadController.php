<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActionRad;
use App\Models\ActivityLog;
use App\Models\MeasureRad;
use App\Models\RequestRad;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequestRadController extends Controller
{
    /**
     * Function permission   
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:melihat permintaan radiologi di kunjungan|tambah permintaan radiologi di kunjungan|mengelola permintaan di radiologi',['only' => ['listingRadiologyUser', 'store', 'listingRadiology']]);
         $this->middleware('permission:melihat permintaan radiologi di kunjungan', ['only' => ['listingRadiologyUser']]);
         $this->middleware('permission:tambah permintaan radiologi di kunjungan', ['only' => ['store']]);
         $this->middleware('permission:mengelola permintaan di radiologi', ['only' => ['listingRadiology']]);
         $this->middleware('permission:lihat histori rekam medis', ['only' => ['printHistoryRad', 'previewHistoryRad']]);
    }
    /**
     * Listing permintaan radiologi untuk user (selain radiologi)
     *
     * @param  mixed $var
     * @return void
     */
    public function listingRadiologyUser(Request $request, $visitId)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestLabs = RequestRad::query()
                ->where('visit_id', $visitId)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where(function($query) use ($request) {
                            $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                            $query->where('unique_id', 'like', '%' . $request->search . '%')
                                ->orwhere('info', 'like', '%' . $request->search . '%')
                                ->orwhere('created_by', 'like', '%' . $request->search . '%');
                            if($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            } 
                        });
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
            
            $requestLabPaginate = (new CustomPaginator(
                $requestLabs->clone()->forPage($currentPage, $itemPerpage)->get(),
                $requestLabs->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/visit/'.$visitId.'/radiology/listing');
    
            return response()->json(array_merge(
                $requestLabPaginate->toArray(), 
                ['is_read_count' => $requestLabs->clone()->where('is_read_doc', 0)->count()]
            ));
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request rad.',
            ], 500);
        }
    }

    /**
     * Listing permintaan radiologi untuk radiologi
     *
     * @param  mixed $var
     * @return void
     */
    public function listingRadiology(Request $request)
    {
        try {
            $date_start = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $date_end = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestLabs = RequestRad::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('unique_id', 'like', '%' . $request->search . '%')
                            ->orwhere('info', 'like', '%' . $request->search . '%')
                            ->orwhere('created_for', 'like', '%' . $request->search . '%')
                            ->orwhere('created_by', 'like', '%' . $request->search . '%');
                        if($date != 'date invalid') {
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
                        $query->where('is_read_rad', $request->is_read);
                    });
                })
                ->when($request->filled('periode'), function ($query) use ($date_start, $date_end) {
                    $query->orWhere(function($query) use ($date_start, $date_end) {
                        $query->whereBetween('created_at', [$date_start, $date_end]);

                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
            
            $requestLabPaginate = (new CustomPaginator(
                $requestLabs->clone()->forPage($currentPage, $itemPerpage)->get(),
                $requestLabs->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/visit/radiology/listing');
    
            return response()->json(array_merge(
                $requestLabPaginate->toArray(), 
                ['is_read_count' => $requestLabs->clone()->where('is_read_rad', 0)->count()]
            ));
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request rad.',
            ], 500);
        }
    }
    
    /**
     * Listing tindakan untuk tambah permintaan radiologi
     *
     * @return void
     */
    public function listingAllMeasure(Request $request)
    {
        try {
            $measureRad = MeasureRad::select(
                'measure_rads.id as measure_id', 
                'measure_rads.name as measure_name', 
                'sub_measure_rads.id as sub_measure_id',
                'sub_measure_rads.name as sub_measure_name' 
            )
            ->leftJoin("measure_rad_sub_measure_rad", "measure_rads.id", "=", "measure_rad_sub_measure_rad.measure_id")
            ->leftJoin("sub_measure_rads", "measure_rad_sub_measure_rad.sub_measure_id", "=", "sub_measure_rads.id")
            ->when($request->filled('nama'), function ($query) use($request) {
                $query->where('sub_measure_rads.name', 'like', '%' . $request->nama . '%');
            })
            ->when($request->filled('group'), function ($query) use($request) {
                $query->where('measure_rads.id', $request->group);
            })
            ->get();
            
            $data = [];
            foreach ($measureRad as $key => $measure) {
                if($measure->sub_measure_id) {
                    $row['action_id'] = strval($measure->sub_measure_id);
                    $row['action_group_id'] = strval($measure->measure_id);
                    $row['action_group'] = $measure->measure_name;
                    $row['name'] = $measure->sub_measure_name;
                    $data[] = $row;
                }
            }

            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve measure rad.',
            ], 500);
        }
    }
    
    /**
     * Listing group radiologi
     *
     * @return void
     */
    public function listingGroupRadiology()
    {
        try {
            $measures = MeasureRad::all();

            return response()->json($measures);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve group measure.',
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
     * Tambah permintaan laboratorium
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @return void
     */
    public function store(Request $request, $visitId)
    {
        $request->validate(['action' => 'required']);
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $nama = $visits->status() == 200 ? $visits[0]['nama'] : '';
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            if(RequestRad::where('visit_id', $visitId)->count() == 0) {
                $visit_count = RequestRad::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = RequestRad::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }
            $lastTodayRequest = RequestRad::query()->today()->latest()->first();
            $requestRad = RequestRad::create([
                'visit_id' => $visitId, 
                'no_rm' => $norm, 
                'user_id' => auth()->user()->id,
                'unique_id' => RequestRad::generateUniqueId($lastTodayRequest),
                'status' => 'waiting',
                'created_by' => auth()->user()->name,
                'created_for' => $nama,
                'visit_number' => $visit_count
            ]);

            $measure = [];
            foreach ($request->action as $key => $action) {
                info($action);
                ActionRad::create([
                    'request_rad_id' => $requestRad->id,
                    'action_id' => $action['action_id'],
                    'action_group_id' => $action['action_group_id'],
                    'action_group' => $action['action_group'],
                    'name' => $action['name'],
                    'status' => 'unfinish',
                    'attachment_count' => 0,
                    'is_add' => 1
                ]);
                $measure[] = $action['name'];
            }

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Membuat permintaan radiologi untuk tindakan '. implode(', ', $measure),
                'type' => 'Radiologi',
                'action' => 'Membuat'
            ]);

            $countAction = $requestRad->actionRads->count();
            $countComment =  $requestRad->commentRads->count();
            $requestRad->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request rad successfully added',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add request rad.',
            ], 500);
        }
    }

    /**
     * Untuk melihat data detail permintaan radiologi
     *
     * @param  mixed $requestLab
     * @return void
     */
    public function edit(RequestRad $requestRad)
    {
        try {
            return response()->json($requestRad);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request rad.',
            ], 500);
        }
    }
    
    /**
     * Ubah status request radiologi
     *
     * @return void
     */
    public function updateStatusRequest(Request $request, RequestRad $requestRad)
    {
        $validate_data = $request->validate(['status' => 'required']);
        try {
            $requestRad->update($validate_data);

            if($requestRad->status == 'done') {
                $requestRad->update([
                    'done_at' => now()
                ]);
            }

            function statusName($req) {
                switch ($req) {
                    case 'fixing':
                        return 'Perbaikan';

                    case 'progress':
                        return 'Proses';

                    case 'done':
                        return 'Selesai';

                    case 'cancel':
                        return 'Batalkan';

                    default:
                        return 'Menunggu';
                }
            }

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Mengubah permintaan radiologi menjadi '. statusName($request->status),
                'type' => 'Radiologi',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request rad successfully updated',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update request rad.',
            ], 500);
        }
    }

    public function printPDF(RequestRad $requestRad)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $requestRad->visit_id);
            $actionRad = $requestRad->actionRads;

            $pdf = Pdf::loadView('pdf.resultRadiology', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'radiology' => $requestRad,
                'actions' => $actionRad
            ]);

            return $pdf->download('Hasil Radiologi-'.$requestRad->unique_id.'.pdf');
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download pdf request rad.',
            ], 500);
        }
    }
    public function previewPDF(RequestRad $requestRad)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $requestRad->visit_id);
            $actionRad = $requestRad->actionRads;

            $pdf = Pdf::loadView('pdf.resultRadiology', [
                'visits' => $visits->status() == 200 ? $visits[0] : [],
                'radiology' => $requestRad,
                'actions' => $actionRad
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
     * print pdf history rekam medis laboratorium
     *
     * @param  mixed $visitId
     * @return void
     */
    public function printHistoryRad($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $requestRads = RequestRad::where('no_rm', $norm)->get();

            foreach ($requestRads as $key => $requestRad) {
                $actionRad = $requestRad->actionRads;
                $view = view('pdf.resultRadiology')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'radiology' => $requestRad,
                    'actions' => $actionRad
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->download('Histori Rekam Medis Radiologi.pdf');
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed download history.',
            ], 500);
        }
    }
    
    /**
     * Preview hasil pdf history rekam medis
     *
     * @param  mixed $visitId
     * @return void
     */
    public function previewHistoryRad($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $requestRads = RequestRad::where('no_rm', $norm)->get();

            foreach ($requestRads as $key => $requestRad) {
                $actionRad = $requestRad->actionRads;
                $view = view('pdf.resultRadiology')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'radiology' => $requestRad,
                    'actions' => $actionRad
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
                'message' => 'Failed download history.',
            ], 500);
        }
    }
}
