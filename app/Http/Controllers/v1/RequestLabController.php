<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActionLab;
use App\Models\ActivityLog;
use App\Models\Recipe;
use App\Models\RequestLab;
use App\Models\RequestRad;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequestLabController extends Controller
{    
    /**
     * Function permission   
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:melihat permintaan laboratorium di kunjungan|tambah permintaan laboratorium di kunjungan|mengelola permintaan di laboratorium|melihat hasil permintaan di laboratorium',['only' => ['listingLaboratoriumUser', 'store', 'listingLaboratorium', 'listingResultLaboratorium']]);
         $this->middleware('permission:melihat permintaan laboratorium di kunjungan', ['only' => ['listingLaboratoriumUser']]);
         $this->middleware('permission:tambah permintaan laboratorium di kunjungan', ['only' => ['store']]);
         $this->middleware('permission:mengelola permintaan di laboratorium', ['only' => ['listingLaboratorium']]);
         $this->middleware('permission:melihat hasil permintaan di laboratorium', ['only' => ['listingResultLaboratorium']]);
         $this->middleware('permission:lihat histori rekam medis', ['only' => ['printHistoryLab', 'previewHistoryLab']]);
    }
    
    /**
     * Listing permintaan laboratorium untuk user (selain laboratorium)
     *
     * @param  mixed $var
     * @return void
     */
    public function listingLaboratoriumUser(Request $request, $visitId)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestLabs = RequestLab::query()
                ->where('visit_id', $visitId)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('unique_id', 'like', '%' . $request->search . '%')
                            ->orwhere('info', 'like', '%' . $request->search . '%')
                            ->orwhere('created_by', 'like', '%' . $request->search . '%');
                        if($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                        } 
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
                ->withPath(env('APP_URL').'/visit/'.$visitId.'/laboratorium/listing');
    
            return response()->json($requestLabPaginate);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request lab.',
            ], 500);
        }
    }

    /**
     * Listing permintaan laboratorium untuk laboratorium
     *
     * @param  mixed $var
     * @return void
     */
    public function listingLaboratorium(Request $request)
    {
        try {
            $date_start = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $date_end = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestLabs = RequestLab::query()
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
                    $query->where('status', $request->status);
                })
                ->when($request->filled('is_read'), function ($query) use ($request) {
                    $query->where('is_read_lab', $request->is_read);
                })
                ->when($request->filled('periode'), function ($query) use ($date_start, $date_end) {
                    $query->whereBetween('created_at', ["$date_start 00:00:00", "$date_end 23:59:59"]);
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
            
            $requestLabPaginate = (new CustomPaginator(
                $requestLabs->clone()->forPage($currentPage, $itemPerpage)->get(),
                $requestLabs->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/visit/laboratorium/listing');
    
            return response()->json(array_merge(
                $requestLabPaginate->toArray(), 
                ['is_read_count' => $requestLabs->clone()->where('is_read_lab', 0)->count()]
            ));
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request lab.',
            ], 500);
        }
    }

    /**
     * Listing hasil permintaan laboratorium untuk laboratorium
     *
     * @param  mixed $var
     * @return void
     */
    public function listingResultLaboratorium(Request $request)
    {
        try {
            $date_start = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $date_end = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $requestLabs = RequestLab::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('unique_id', 'like', '%' . $request->search . '%')
                            ->orwhere('info', 'like', '%' . $request->search . '%')
                            ->orwhere('created_for', 'like', '%' . $request->search . '%')
                            ->orwhere('created_by', 'like', '%' . $request->search . '%');
                        if($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                            $query->orWhere('done_at', 'like', '%' . $date . '%');
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
                        $query->where('is_read_lab', $request->is_read);
                    });
                })
                ->when($request->filled('periode'), function ($query) use ($date_start, $date_end) {
                    $query->orWhere(function($query) use ($date_start, $date_end) {
                        $query->whereBetween('created_at', ["$date_start 00:00:00", "$date_end 23:59:59"]);

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
                ->withPath(env('APP_URL').'/visit/laboratorium/listingResult');
    
            return response()->json(array_merge(
                $requestLabPaginate->toArray(), 
                ['done_count' => $requestLabs->clone()->where('status', 'done')->count()]
            ));
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request lab.',
            ], 500);
        }
    }

    public function listingLaboratoriumAssesment($visitId)
    {
        try {
            $header = $this->headerListing();
            $requestLabs = RequestLab::where('visit_id', $visitId)->where('status', 'done')->get();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $name = $visits->status() == 200 ? $visits[0]['nama'] : '';

            $data = [];
            foreach ($requestLabs as $key => $request) {
                $row['id'] = $request->id;
                $row['unique_id'] = $request->unique_id;
                $row['name'] = $name;
                $row['norm'] = $norm;
                $data[] = $row;
            }

            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request lab.',
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
        $request->validate(['action_id' => 'required', 'action_group_id' => 'required', 'action_group' => 'required', 'name' => 'required']);
        try {
            $header = $this->headerListing();
            $lastTodayRequest = RequestLab::query()->today()->latest()->first();
            $unique_id = RequestLab::generateUniqueId($lastTodayRequest);
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            if(RequestLab::where('visit_id', $visitId)->count() == 0) {
                $visit_count = RequestLab::where('no_rm', $norm)->groupBy('visit_id')->get()->count() + 1;
            } else {
                $visit_count = RequestLab::where('no_rm', $norm)->groupBy('visit_id')->get()->count();
            }
            if ($visits->status() == 200) {
                $nama = $visits[0]['nama'];
                $patient_type = $visits[0]['kodejamin'];
            }

            $saveOrder = saveOrderLis($visits[0], $unique_id, $patient_type, auth()->user()->name, auth()->user()->id, $request->action_id, $request->name);

            if($saveOrder['metaData']['message'] != 'Nomor order tersebut sudah ada') {
                $requestLab = RequestLab::create([
                    'visit_id' => $visitId, 
                    'no_rm' => $norm, 
                    'user_id' => auth()->user()->id,
                    'laboratorium_id' => $saveOrder['response']['no_laboratorium'],
                    'patient_type' => $patient_type == 'NON5' ? '1' : '2',
                    'unique_id' => $unique_id,
                    'status' => 'waiting',
                    'created_by' => auth()->user()->name,
                    'created_for' => $nama,
                    'visit_number' => $visit_count
                ]);
    
                $measure = [];
                foreach ($request->action_id as $key => $action_id) {
                    ActionLab::create([
                        'request_lab_id' => $requestLab->id,
                        'action_id' => $action_id,
                        'action_group_id' => $request->action_group_id[$key],
                        'action_group' => $request->action_group[$key],
                        'name' => $request->name[$key],
                        'status' => 'unfinish',
                        'is_add' => 1
                    ]);
                    $measure[] = $request->name[$key];
                }
    
                ActivityLog::create([
                    'user_name' => auth()->user()->name,
                    'user_role' => auth()->user()->roles->first()->name,
                    'visit_id' => $requestLab->visit_id,
                    'unique_id' => $requestLab->unique_id,
                    'request_id' => $requestLab->id,
                    'note' => 'Membuat permintaan laboratorium untuk tindakan '. implode(', ', $measure),
                    'type' => 'Laboratorium',
                    'action' => 'Membuat'
                ]);
    
                $countAction = $requestLab->actionLabs->count();
                $countComment =  $requestLab->commentLabs->count();
                $requestLab->update([
                    'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $saveOrder['metaData']['message']
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $saveOrder['metaData']['code'] == '200' ? 'Penambahan Berhasil.' : $saveOrder['metaData']['message'],
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add request lab.',
            ], 500);
        }
    }
    
    /**
     * Untuk melihat data detail permintaan
     *
     * @param  mixed $requestLab
     * @return void
     */
    public function edit(RequestLab $requestLab)
    {
        try {
            return response()->json($requestLab);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve request lab.',
            ], 500);
        }
    }
    
    /**
     * Ubah status request laboratorium
     *
     * @return void
     */
    public function updateStatusRequest(Request $request, RequestLab $requestLab)
    {
        $validate_data = $request->validate(['status' => 'required']);
        try {
            $requestLab->update($validate_data);
            
            if($requestLab->status == 'done') {
                $requestLab->update([
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
                        return 'Batal';

                    default:
                        return 'Menunggu';
                }
            }

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestLab->visit_id,
                'unique_id' => $requestLab->unique_id,
                'request_id' => $requestLab->id,
                'note' => 'Mengubah permintaan laboratorium menjadi '. statusName($request->status),
                'type' => 'Laboratorium',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Request lab successfully updated',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update request lab.',
            ], 500);
        }
    }
    
    /**
     * Untuk check sudah diread atau belum pada header
     *
     * @return void
     */
    public function checkIsRead($visitId)
    {
        try {
            $requestLab = RequestLab::where('visit_id', $visitId)->get();
            $requestRad = RequestRad::where('visit_id', $visitId)->get();
            $eResep = Recipe::where('visit_id', $visitId)->get();

            if($requestLab->count() == 0) {
                $is_read_lab = 1;
            } else if($requestLab->where('is_read_doc', 0)->count() > 0) {
                $is_read_lab = 0;
            } else {
                $is_read_lab = 1;
            }

            if($requestRad->count() == 0) {
                $is_read_rad = 1;
            } else if($requestRad->where('is_read_doc', 0)->count() > 0) {
                $is_read_rad = 0;
            } else {
                $is_read_rad = 1;
            }

            if($requestRad->count() == 0) {
                $is_read_res = 1;
            } else if($requestRad->where('is_read_doc', 0)->count() > 0) {
                $is_read_res = 0;
            } else {
                $is_read_res = 1;
            }

            $data['is_read_lab'] = $is_read_lab;
            $data['is_read_rad'] = $is_read_rad;
            $data['is_read_res'] = $is_read_res;

            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve is read.',
            ], 500);
        }
    }
    
    /**
     * print pdf history rekam medis laboratorium
     *
     * @param  mixed $visitId
     * @return void
     */
    public function printHistoryLab($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $requestLabs = RequestLab::where('no_rm', $norm)->get();

            foreach ($requestLabs as $key => $requestLab) {
                $view = view('pdf.laboratorium')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'laboratorium' => $requestLab,
                ]);
                $html .= $view->render();
            }
            $pdf = Pdf::loadHTML($html);

            return $pdf->download('Histori Rekam Medis Laboratorium.pdf');
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
    public function previewHistoryLab($visitId)
    {
        try {
            $html = '';
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $visitId);
            $norm = $visits->status() == 200 ? $visits[0]['norm'] : '';
            $requestLabs = RequestLab::where('no_rm', $norm)->get();

            foreach ($requestLabs as $key => $requestLab) {
                $view = view('pdf.laboratorium')->with([
                    'visits' => $visits->status() == 200 ? $visits[0] : [],
                    'laboratorium' => $requestLab,
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
    
    public function resultRequestLab()
    {
        try {
            $data = [
                "status" => true,
                "message" => "success",
                "file" => "cetak.cetakHasil",
                "result" => [
                "data_pasien" => [
                "id" => 414,
                "mrn" => "2208007048",
                "patient_type" => [
                "uid" => "b57df12b-19b1-4410-a90b-10c87c9482b6",
                "patient_type" => "Pasien Umum"
                ],
                "guarantor" => "8538b7b2-52b8-4622-a460-45455d25abb8",
                "members_number" => "",
                "referral_type" => "92669d11-b6c8-4ade-8f96-f12ed5f7a766",
                "uid_ward" => "dc3c7c5f-32ad-42a1-9b50-dc0f36795559",
                "uid_class" => null,
                "uid_doctor_referral" => null,
                "uid_facility_referral" => null,
                "uid_doctor" => "21c53c05-7e44-41ad-9d6a-d2b13fc022cc",
                "is_cyto" => false,
                "reg_num" => "0408220001",
                "registration_date" => "2022-08-04 00:04:28",
                "created_by" => "e7344d44-7727-46d5-b91c-d223c0f5ddc2",
                "uid_updated_by" => "e7344d44-7727-46d5-b91c-d223c0f5ddc2",
                "uid" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "4535b220-3b62-11eb-adc1-0242ac120002",
                "created_at" => "2022-08-04 00:04:28",
                "updated_at" => "2022-08-04 00:05:27",
                "cancelation_remark" => null,
                "cancelation_by" => null,
                "cancelation_date" => null,
                "is_bridge" => false,
                "room_number" => null,
                "source" => null,
                "no_reg" => null,
                "sign_fast" => false,
                "fast_note" => "",
                "is_pregnant" => false,
                "is_mcu" => false,
                "payment_" => [
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_payment_method" => "8dc1dbec-10a3-4041-9c9b-40df8e16d1f3",
                "card_number" => null,
                "payment_method" => [
                    "uid" => "8dc1dbec-10a3-4041-9c9b-40df8e16d1f3",
                    "payment_method" => "Claim"
                ]
                ],
                "doctor_incharge" => [
                "uid" => "21c53c05-7e44-41ad-9d6a-d2b13fc022cc",
                "name" => "dr. IDA BAGUS PUTU SWISNA",
                "path_sign" => null
                ],
                "ward" => [
                "uid" => "dc3c7c5f-32ad-42a1-9b50-dc0f36795559",
                "ward" => "IGD"
                ],
                "class" => null,
                "patient" => [
                "mrn" => "2208007048",
                "title" => "Mrs",
                "name" => "ERWIN. A",
                "gender" => "M",
                "dob" => "1998-07-15 00:00:00",
                "pob" => "-",
                "address" => "JL ULUWATU II HOTEL BALI YURIS",
                "district" => null,
                "postal_code" => null,
                "phone" => "",
                "uid_blood_type" => null,
                "membership_date" => "2022-08-04 00:04:57",
                "uid" => "25a5b247-9532-4ea3-b437-0831e47e8760",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "4535b8d8-3b62-11eb-adc1-0242ac120002",
                "spouse" => null,
                "nationality" => null,
                "is_bridge" => false
                ],
                "patient_sample" => [
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "ea79c683-567d-4962-8041-fc973c802a8f",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "6052f5ef-dacf-40d0-8860-00cc5db45a8d",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "50726c13-8479-40c0-83b7-c59643a7d441",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "d1c36e6e-2b49-4351-8fb1-5ea743d9c757",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "62b09ab8-ff66-4bb4-8202-8587829d587a",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "a1d03179-061e-4fc7-b8d1-7c66e7185778",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "14f7eaca-d7de-4d12-a5e5-8b4417c9124b",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "0b222234-ff56-420c-921c-93bdf120c266",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "158225f0-4ced-4688-8cc8-90b174a28735",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "3748c704-d117-4b63-a2f6-f5c8a9dc9c19",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "b514dcef-db59-4357-883f-ba711369e7d6",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "30be5c69-5c0d-4458-a536-cf9cbaf7d834",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "5a140c1b-ef8b-4d24-9d3e-fccf66bc6945",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "fcd5667c-d28f-4d77-b88c-dbc1456a69ac",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "62e46177-8dd4-4914-b8ca-dc783199795d",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "8bc3c0ab-b45c-4915-b65e-016f3f4bb427",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "67fedb41-c1b3-40c4-86f9-67f2c8c7a944",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "84241480-2bdc-4a9b-9e05-d1e3b34d80c7",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ],
                [
                "sample_num" => "0408220001",
                "mrn" => "2208007048",
                "uid_registration" => "041d5ca3-f962-4a76-af89-d3d0281149a3",
                "uid_test" => "1d8f5420-67f9-4d41-bc13-1e6aaca86316",
                "taken_date" => "2022-08-04 00:08:25",
                "uid" => "e218a19c-71bc-4996-ab8c-6c5fef6a2406",
                "enabled" => true,
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_object" => "fb22bc31-7319-4373-b6fb-9118b9d1c90b",
                "uid_paket" => null,
                "uid_panel" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "type_ref" => 1,
                "qty_print" => 1,
                "uid_by" => "17e272bd-206d-4a45-b838-fb3ae87850a6",
                "is_read" => false,
                "read_by" => null,
                "test_specimen" => null
                ]
                ],
                "doctor_referal" => null,
                "referal_employee" => null,
                "fasility_referal" => null,
                "comment_sample" => [],
                "patient_diagnose" => null,
                "guarantor_patient" => [
                "uid" => "8538b7b2-52b8-4622-a460-45455d25abb8",
                "name" => "SELF-PAY"
                ]
                ],
                "umur" => "24 Th",
                "profile" => [
                "profile_name" => "RS NNNNNN",
                "uid_profile_type" => "3099ae0e-1502-492d-892f-e89a07e3c4a0",
                "address" => "NNNNNNNN",
                "phone" => "NNNNNNNN",
                "email" => "",
                "logo" => "logors.png",
                "city" => "NNNNNN",
                "uid_person_in_charge" => "969180bd-f48f-4662-bf3a-9ff5d1eee037",
                "url_logo" =>
               "data:image/png;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFB
               QQEBQoHBwYIDAoMDAsKCwsNDhIQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/2wBDAQMEBAUE
               BQkFBQkUDQsNFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFB
               QUFBT/wAARCAL1A4MDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8
               QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhc
               YGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl
               5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwE
               AAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExB
               hJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVl
               dYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcb
               HyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9U6KKKACiiigAooooAKKK
               KACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo
               ooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA
               CiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo
               AKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii
               gAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK
               KKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigA
               ooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK
               KACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoo
               ooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA
               Cik3D6VBNfW9v/AKyeOP8A33AoHFOWxYoqvDewTf6ueOT/AHGBqfdQDTjuLRRRQIKKKKACiiigAooooAKK
               KKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAo
               oooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKK
               ACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAbkUBvyrlfiB8TvDPwv0dtU8T6xa6TZ5wjTuN0jdlRerN7
               AE18PfGP/AIKBa/4m+0af8PrNvDuntuRtVvUV7yQHIDRx5KxDoctuPPKqa5a2Jp0NZM+iyrIMfnE7YaHu9ZPR
               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
               1rUrzV9SuP9ZeX0zSyNyTjJPC5JwowBngAV5NfMacNKerP07JuA8Vi7Vce/Zw7faf6L+tD6P+Nn7dfi34hebpv
               hFZPCGhNlTdKytfXKkY5bBWEdfuZboQwORXzI2+SR3d2llkYu8kjFmdiclmJOSSe5/Gil+tfPVa9Ss7zdz9uy7Kc
               HldP2WEpqPfu/VhS0UVzHrhRRRTGFFFIWoFe24fWvS/gD8dtY+AvjRNVtPMvdFutseqaWp/4+IhnDpnpIuS
               Qe/KnrlfMFuIv+ey/99CpBWsJzpyUo6NHFisLQx1GWHrrmjJH7UeE/Fek+NvD1hrei3kd/pt9EJoJ4myGU/wA
               vp2xitivzN/Y4/aSPwi8SJ4Y1yeT/AIRHWLgbZGYkadcuQBIAekTk/NjgE7uhY1+lsMizRh0wVI4I9K+yw+IjiIcy3P
               5Xz7Ja2SYt0Z6xesX3X+ZNRRRXWfNBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABR
               RRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF
               FFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU
               UUAFFFFABRRRQA3cfSuL+Jnxf8J/CPRG1HxPrEOnRtkQw53T3DAZ2xRj5nbHYD3ryv9r/AOPnij4IeGtNk8O6
               Es66kzwNrk7bobGTA2AxgZZmBOMkL8vc4B/NzxR4r1rxvrdxrGv6rdavqU337q6csVHXao6Kv+yoAry8Vjlh3y
               RV5H6Lw3whUzmCxVaajSv03dvy+f3HvHxz/bc8W/FDzdK8OGbwh4dkBR/LcfbrhSvId1J8scniM54B39q+b4
               okt4tioqIv8KrgVJSHivmKlapVd5u5/QGXZZhMrpeywsFFfi/VjqKKK5z1gpKWkpgJRVrSdLvfEGqW+m6bZXGp
               ahcHENnaxNLJJ0zhQM4GQSeg78V9WfB//gn34g8RfZ9T8fah/wAI7ZNhv7KsiJLtxwQHkBKR98hdx/2genVS
               w9Ss7QXzPCzLOsDlMOfFVEuy3b+R8nWVndapqEVlp9pcahezf6q1s4Wmmk9cIoLHGR0HevoT4c/sJ/Erxr9n
               utUS08IaZIuWfUXMt1jt+5Tjp/ecEZ5Gciv0A+G/wZ8G/CXTxZ+F9DtdNU43zqu+aUgYy8rZdz7k12+2vco5ZC
               OtV3f4H4/mfiFiarcMvhyR7vV/dt+Z8m+Ff+Cc/gTTVLa5rOteIJG6p9oFrGP90RBW/NjXTX37A3whvItqaRqFu
               6rtWSHVLgH6kF8H8RX0bik2+teisLQStyI+DqcR5vUlzyxMr+Tt+R8H/Eb/AIJx3ljZy3PgbxI1/Op3Lp+ubVyPRZ
               o04wOmUPQDI618jeK/B+teBdcuNI8RaVdaRqVv1t7pMZHZlboyn+8pI/EV+1P8Veb/ABt+Bnhv44eGJNL1u3
               2XMas1nqUIAuLSTHDIe4zjKng9CK4cRlsJq9PRn2WS8dYvDTVLMPfp9/tL/M/IuwurjR7iWXT7i40+WYbXks5
               WhZx6MUIyOT19TUAhT7RNNsXzZG3PJt+ZyeSWPc11vxO+Gmu/CTxne+GvEEWy7t/njuI1/d3UJJCSpzwDg/
               KfukEdq5bpXzcuaD5JdD97w9ShiIKvRs1JXTQUtFFYnYFFFFMBvfilpK7n4TfBXxb8atc/s/w1p7TW8bqLvU7gb
               LW1U5OWb+JsD7i5bkZwOa0hGVR8kVqcmIxFLCUnWryUYrqzg5JUjjd3dVRfmZmbAH4mvZPhd+yX8SvipHF
               d2ej/ANh6U7Afb9d32wK5wzJHtLuccjIVT2avt34F/sbeDfhAttqF/EvifxPGoY6jexjZE3fyYiSIxknnlvVjX0CqBRg
               DA9q96hlnWs/kj8bzjxAabpZZD/t6X6L/AD+4+R/A3/BOjwfpBSbxPrepeIp8Ddbwv9ktwR6BPnxnsXI9q9l0L9l
               n4T+G9hs/AOimVfu3FzarPN0x/rH3N+teq0cV68MPSp7RR+X4rPMzxjvWryfzsvuWhx9x8IfBNzAYZfCejyxN1
               RrKMj/0GvNfGX7E/wAJvF1vKkHhqLw9cMvy3Ghn7KUPZgi/IT/vKRXvVJgVpKlTkrOKOSjmONw8uelWkn6s/
               Kb9oD9lfxR8DLh7qRG1/wAJzHEWrQxnMRx924QDCZ5ww+U99pIB+iv2Gv2lv7Ys4Phr4lu/+JpbJt0a8mcZu
               oVBJgJ7vGo47lBnkqTX2Bq+kWevaZcadqNtDe2VzE0M1vOgdJEYYKkHqCD096/Nn9p79mvUv2fPEEHinwrc
               XCeGGuVltbqFj52k3G4GNCxPKliAjH/dbtu8epQlg6ntqPw9UfqGCzejxVhP7KzK0a32J935/wBa+p+mqsT2pa
               8R/ZZ/aCt/jt4DWW6McHifTQsGqWq8AvjiVBn7j8kehDDORXtmea9qE1UipR2Z+U4zCVsDXnh66tKLsx9FFF
               WcgUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF
               FABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU
               UAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBz/jTwXpXxC8L6hoGu
               Wsd7pt7GYpoW9D0IPZgcEEcggGvyq+P3wF1v4B+LP7PvfMvdFumY6Zq23i4QZPlyYGFmUDkYwQMr3C/rni
               ua+IHw/0L4neF7zw/wCIrBL/AEy6X542JVgwOQyMOVYEAhgcgjiuDFYVYiHmtj7HhviKrkVfX3qUviX6rz/M/G
               SlzXufx8/ZH8VfBe6lv7OK48SeE937vUreMtPAOeLhFHygf89F+X1C14VG6SR70dXRvmDL0NfJ1KU6T5ZKzP6
               XwOYYXMqKrYWakn+Hqh3NJSf3F/jZgir3LE4CgdSSSOnWvdvg7+xv8QPix5V7c2jeEdCZv+PzV4GW4kAOCUt
               zhsdeX2joRkUU6U6rtFXFjsywuX03VxU1Ffn8jwn/AJaIibneRgiRqpLOxOAqgdST2H86+mPgv+wn4w+IXkah4
               oZvBuhtzsZVe/lXttQgrEPd8nsU719j/Bn9lnwL8FUS50ywbUNd2gPrGot5s7cYOz+GMH0QAHPOa9ixgccV7t
               DLVHWrr5H4znXH9StejlkeVfzPf5LocB8Lfgb4M+Ddi1v4Z0WCzmkVVuL1lD3NxtzgySn5m6nA6DPAFd/ilFFe
               3GKirRVkfktavVxE3UrScpPq9RaKKKswCiiigAooooA8X/ab/Z+svjv4He3Ty7TxJp+6bS75l+6/8UbnGSjgAEeoB
               6gV+V2qaXe6Bql1pupWsmn6lZymG5tZlAeKQdQcHHpz0III4Nftu2a+ev2kv2SdF+OR/tmwuP7D8XRRCNL3a
               WiuUGcRzJnkZPDD5h6kZB8jHYP23vw+I/TOEuKllMvqmMb9k9n/ACv/ACPzFxRXoHj79n34ifDO9a21rwlqU
               sfOL3S7d7y3fGMkPGpKjkffCk84FcnaeEPEV8/lWvhfXrqRukdvpNy7H8FQ1806U4vladz9+pZhhK1NVadWLi
               +t0ZXWgn7v+0wC+pJIAAHc5I/E19AfDr9h34m+OJIZdSsYPCOmt9+bU33z7f8AZhQ8nH95lx+lfZ/wU/ZE8C/B
               iSK/gtZNd8RqPm1fUvnZDznyk+7EOT90ZPck8120cvrVNZKyPks14yy3LU1Tl7SfaO3zex8pfs//ALDWvfEKSDV
               /HK3Xhrw7w6aerbL27XIPzcfuUI4/v8n7hGa/QTwl4O0bwLoFroug6ZbaXptqu2K1tYwiDuTgdycknuTzW0q7
               V/wpeK+koYanQXurXufgmcZ9jc6qc+IlaK2itkLRRRXWfOhRRRQAUUUUAN96y/EXh3TvFei3ukaraQ3+nXkT
               QT29wgdJEYYKsD1FatI31xRa44ycGpRdmj8xvH3gvxR+xF8a9P1/Q/OvfDsrlbKaVsrd25OZLKVuzgKCrH0Vuc
               OK/RH4c+PtH+KHg/TfEuhXC3WnXyb0P8SMCQyMM8MrAqR2IIo+I3w50L4qeEr3w74jslvdOugMrkq0bDlX
               RhyrA8gjpXh3wJ/Z98b/ALOvjq6tNK1yDxP8O9UJaa2u8wXllKAAkqgApJkZDgFMgKQMjB8+nTlh52irxf4H22
               OzDDZ5glPEPlxNNWv0nH/NH03RRRXoHxAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABR
               RRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFF
               FFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUU
               UUAFFFFABRRRQAUUUUARSxiQFWG5CMFTXkfiz9k34T+M7yW8v8AwbZQ3czmSWbTi1m8rHqzGIruOSeT
               XsGKOlZyhGatJXOrD4rEYWXNh5uL7ptHmvgP9nP4c/DW4S70Dwlp9rfxrsW+kj865A9PNfLfrXpWAowBxSZ
               pacYqKtFWIrYitiZc9abk+7dxaKKKswCiiigAooooAKKKKACiiigAooooARlDdQD9RSeWn91fyp1FAxKWiigQUU
               UUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRR
               QAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFF
               ABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU
               AFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA
               UUUUAFFFFABRRRQAUUUUAFFFFABSUVy3jjxYvhf+w0Dfv9T1S3sIl9d7Ev8A+OK5/AU0nJ2REpKCuzqqKSlp
               FhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUU
               AFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA
               UUUlABzWXrfiPTvDdt9o1K9t7OI8bpXC5PoPU1w/xX+Llv4Fj+w2W261mZciNuVhU9GfH6DvXzPrWuah4j1B
               73UrqS9uG/ikOcewHRR7CuinRctXsfKZnn1LBSdKmuaf4I+jtU/aO8MWcmy2jvL//AG44gi/mxB/SqUP7TWh
               O/wC902/iX+8Ah/8AZq+cKK6vq9M+QlxLjm7ppeVj7A8NfFnwx4rkSKy1ONLlulvcZjcn0AbGT9M12AYHpzX
               wfXqnwx+N974buItP1qaS80pmCiaQlpLceuerL7dfT0rGeHsrxPey/iVVpKnilbzR9P0VWs7uK+toriCVZYJEDpI
               pyGUjIIPpViuM+7TTV0LRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo
               oASjNN+pzXyP8AtSftif8ACE3l54Q8FSrJrkP7u+1Nl3JZkgHZGDw0mCOeVXoctkDswmDq42qqVFXZ5mPzChl
               tF1q7svxZ9D+Pvi/4P+GcHmeJNfs9LdlLJBJLmaQDrsjGWb8BXhutf8FCPh7p8xSw0zW9VTP+ujt0iU+4DuG/M
               Cvz91TVb3XNUuNQ1K9uNQ1C4OZbq6laSRyfUk1Vx3Jr9Dw/C+HjG9eTk/LY/KMXxpi6kmsPFRj56s/QjS/+Ch
               ngO6uNl7ouu2Cf89fKikH1IV8/pXtXw9+O3gX4ofJ4d8RWd7d7dzWbMYrkD1MTgNj3xX5G8CpLa4ls7iK4tpZ
               Le4jbek0LmN4yOjKQcg/SqxHC+Fkv3MnF+eqFheM8ZTl+/ipR+5n7WenpS/yr4g/Zf/bPupr6y8JfEG7+0ec6w
               2WvOPm3EgLHPjryQBJ/31/er7eVgygg5FfnuMwNbA1fZVl/kz9Vy3MsPmlH21B+q6odRRRXAesFFFFABRRR
               QAUUUUAFFFFACe/evlX4zfEBPEn7WXws8FWlxvi0e7a+vFXBxO0LGNT6ERhj9JQa9++JnxA0/wCGPgfV/E2p
               t/otjCXEYPzSyHhI192YgD618Efsl3mofEz9qyLxFqrC4vdl5qtw3oSvlgLnsvmqo9ABX0OV4XmpVsXP4YRf3tHy
               OdY7lrUMDTfvTkr+iZ+kY6Cloor54+uCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo
               AKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii
               gArn/ABx4oh8H+F77VZRvMKfu4+m9zwq/mR+tb5rwv9pzVnSz0TS1+5JK9w/vsAUfq5rSnHmmkeXmWKeE
               ws6q3SPCtS1K41jULi9upWlu7hy7yN3J/kMfyFVqKK9Y/EJSc5OUndhRRRTJCiiigD3X9nXx03my+GLuXeuDNZ
               sx6AcvGPp1H/Aq98XtXxN4P1d9A8V6Rfq+z7PcoW/3ScN07bSa+2VPyj3FebXjaV+5+r8N4yWIwzpzd3BjqKK
               K5j64KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA8S/ax+Mk3wg+FtxPpz7
               Nf1RzY6e3BMTFSXlwf7ign03FQeDX5dyO8kjOzs7sxYszEkknJJJ6nPc19O/8FAvFFzq3xhsNF3/6FpOmoyx/9N
               ZWZnPv8qxfka+YBxX65w9hI4fBqpb3p638uh+CcVY+eKx8qV/dhol+YUUUV9SfFBRRRQAfejPr0r9HP2IvjRL8
               RPh/L4f1W4afW/D4SPzJCS01sciNyT1IwVP+6CetfnH7ivcP2MfFVx4b/aC8PxI+y01ZJtPuF7EGNpE49d8aD8T
               Xz2e4OOKwcpdY6r5H1nDePngswgl8M3Z/M/UOiiivxs/oQKKKKACiiigAoopKADpUckixIWY7VHU+lOZgOvFf
               EH7Yn7VXnfbPAHg693JzDq2qW7+hw1vGw79nYHjleu7HfgcDVx1ZUqa9X2R5GZ5lRyzDutVfou7PNf2wP2g
               k+LnixNC0W483wlo8pKSLjF3cgFTMGzyoBKrjrlj0Ir0L/gnL4VaTWvGPiJ4vkhhgsIZiv8TEvIoPsBEfxFfG2eD3r9
               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
               5l3HUUm4etLQMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAopKNw9R+dAhaS
               ozPGv8AGv51mX/i7RNLDfbdXsLTb1865RMfmapQk9kZupCO8kam0FfmAP4Vx+vfB7wP4omabVvCWjX8rN
               uaSaxjLE+pOMmsXVP2lPhdou/7X460QuvBjt7tZmH1VCT+lcPrn7dHwq0gP9n1K+1Z1/hsrGT5voXCj9a7qOF
               xrd6UJfJM8rE47LUrV6kbedmem6P8Efh/4fnE2n+DdEtZlO5ZI7GPcD6g44rtI4UjwqIqKvQKMCvjvXv+CjejRxv
               /AGJ4N1C6fs2oXEcH5hN/868v8Sft/fEXVJXXTbLRtFt2+7theeVfq7OFP/fFerHJMzxLvOLXqzxZ8R5Pg1alJP8A
               wo/RbcF6nFcl4u+LPg7wGpOv+JNN0puojuLhRIfomdx/AV+XPij49/ETxo2dV8Z6vKv/ADxt7g28f4pFtU/iK4M
               r+8d25dmLM3ck9ST3Oa9mhwnP/l/U+4+fxPHEFph6X3n6D+OP+Cg3gnRInTw1puoeJ7n+BmU2cBPu0i7x/
               wB8Gvnfx5+298SvF8jRafd2/hWyb/lnp0YeUgjoZXBP4qFrwBs8d6OnavpsNkOBw2vJzPuz47GcTZli9HU5V/d
               0/wCCWdT1O+1u8e91O9uNQvZPv3F5M00j/VmJJ/GqxYnijk0Z2178YxirRVj5iUpTd5asSiiiqMxeT1pDjPFLu6
               19Pfsdfszr8SNUXxl4ktN/hixlK21nMoK38y8HcCOYlP8A30wx0DA8ONxlLA0XVqbL8Wenl+Aq5jXVCitX+B6L+
               xb+zI+mrb/ELxXabLyRd2kWEyfNAp/5eHB6MR90dgSepG37OXimRgCMADFP71+LY3GVMdWdao/TyP6Jy3
               L6WW4dUKS23fdi0UUVwnqBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA
               UUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB
               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
               UUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR
               RQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF
               FABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFJS0UAJRtHpS0UAJS0UUAFFFFABRRRQA
               UUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAB
               RRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAF
               FFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU
               UUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR
               RQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF
               FABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU
               UAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ
               AUUUUAFFFFABRSUtABRRSZoAWiiigAooooAKKKKACiiigAopKKAFooooAKKTNLQAUUUlAC0UUUAFFFJQAt
               FJkUtABRRRQAUUUUAFFFFABRSUtABRRSUALRRSUALRRSUALRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA
               BRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA
               FFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAlc
               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
               b8zO0Rxx7n+ddLqXh3S9Zlhlv8AT7W9kh+488KuV+hI4rSAHAA4radXmS02PnMDlH1OpVkpe7PofFvw/t3vP
               HGhRRJvf7bF+QYEn8gfyr7SXIVc1mWnhrSdP1CW+t9OtYLuTO+eOFVc55OSBnk1q0qlT2jNspy15bCUZS5m
               2effGLwD/wAJx4aY26AarZ5ltm/vf3k/ED8wK+aPBmuXXhfxZp97a7luI5lRo9vLKWAZCPcfrjvX2vjg1kf8Ino/9
               p/2l/Zdn9vzu+0eSu/PruxnNOFXljyvY5swyZYrERxNKXLJbkPjSGW98G61FCjPLJYzKkY6klDgV8nfDeF7jx5oCx
               KzN9tjbavoGyT9MA19m/yrMsvDGk6XfS3tpp1rbXUmd80MKqzZ5OSB60U6nImu5rmGVvHVqVZStyfiao6C
               myf6pvpTqKwPoWtLHwxJYXEesPZeU32tbjyfL7792NuPXNfccI/dr64FZp8MaV/an9p/2ba/b/8An58pd+fXdj
               Oa1ttbVKnPbyPn8qyt5dKo3K/MxaKKKxPoQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooo
               oAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAC
               iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA
               KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig
               AooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK
               KKACiiigAooooAp6nqVto+nXF9ezx2tpbxtLNNKwVI0AJLMTwAAOtfMviP8A4KE+AdLvHh0zTdZ12Ff+Xq3gS
               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
               POs1wtbAypUp80pW0XQ/KOHMlxuHzGFatTcYxvq/Q9pooor8yP2cKKKKACiiigAooooAKKKKACiiigAooooA
               KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig
               AooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKK
               SloAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKA
               CiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo
               AKKKKACiiigAooooAKKKKACiiigAooooAKKKKAM/VtZsdDsZL3Ur230+zj+/PdSrHGvOBlm4H/wBeud/4XH4D
               A48aeH//AAaQf/F151+1p8Edd+N3gfT9P8P3sEN7Y3guvst05SGcbSuCwBwRnIyPX618m/8ADBXxU2n5ND/
               8D2/+N172BwODxFLnrV1CXY+UzHM8fha3Jh8K5x7n3t/wuXwEf+Z18Pf+DSD/AOLpf+Fx+A/+h18P/wDg0g/
               +Kr8itc0m48P6xqem3e37Vp9xLazeW2V3oxVsHHIyDXu2nfsL/EzVNPtb6JNE+zzxLMm69YHaygjI8v3Fe3Wy
               DCYdJ1cRyp7ab/ifO4finHYuUo0MLzOO9nsfo1oPijSfFNu9xo+qWWq26ttaWynWVQfQlSRmsPxB8XvBnhXx
               DBoWr+KNL0/V5tuyzuLlFk+YgLkE8ZJ4z1rwv9kX9mfxV8FfEGu6z4jv7RPttqtqmn2MrSK+GDCWQlRyOQAP
               77V8a/tFStefHTx09w/ms2pypub0XCgfQAAfhXm4PKaOMxU6FOreMVdPuerj8+xOBwVPEVaPLKTtZ9D9bo
               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
               k00rp20PisdhaOEyvD1qE17SDTdmr66/gfpVourW2vaTZalZSrcWV3ClxDMhyHR1DKwPcEEV88/8FAP+SFJ/
               wBhW3/k9R/sHfEw+MPhS/h+6lV9Q8OTfZQu7LG1YboWIxwB86f9s/epP2/z/wAWLi/7C1t/J6+Rw+HeFzWF
               GX2ZL8z73F4qONySpiI/ah+J4B/wT5/5LRqf/YIk/wDRsdfotX50/wDBPj/ktGp/9giT/wBGx1+i1dPEn/Iwl6I5eE
               P+RYvViV4v+2N/ybf4x/65wf8ApRFXtFeL/tjf8m3+Mf8ArnB/6URV4eD/AN5p+q/M+lzL/cq3+F/kfE37Ff8Ayc
               V4d/643P8A6Jav1Br8vv2K/wDk4rw7/wBcbn/0S1fqDX0fE3++r/Cv1PkeC/8AkXy/xMWiiivkT78KKKKACiiigA
               ooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK
               KACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigApr
               /db6U6kblSKBPY/HH4sf8lM8bf8AYavv/R71+uPgb/kS9C/68YP/AEWtfDfxM/Yg8d+IPi3rE2lNYv4d1bUJLv8A
               tKWcBrdJW3uGjxkspLYxkHAyRk4+9tJsY9L021s4v9VbxLEv0VQB/Kvr88xlDFUaEaUrtLX8D8/4ZwOJwmJxM
               q8OVN6fiWW6DPWvy7/bG8IXfhP4+eI5ZYtlpq3l6hayYOHVkVXH1Dq/6etfqK2e1eZfHL4C6B8c/D8VlqvmW
               moWu42OpW4BktmYDIweGU4GVPXA6EAjy8nx8cvxPtJ/C9H/AJns8Q5XPNMJ7Ol8cXdf5HEfse/GTQvGPw
               n0LQPtsNvrmh2kdhLYzOFlZI1CJIoz8ylQOR3BFexeNPHmheANCutW17U7fTrK3QuXmcAtjoFHVmJ4AHJPFf
               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
               AUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA
               BRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA
               FFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFJS0UAFFFFABRRRQAUUUUAJS0UUAFFFFABRRRQAUUUUAF
               FFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU
               UUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRR
               RQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFF
               FABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU
               UAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQ
               AUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFA
               BRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQA3sOKX+GiigQdKKKKQC0UUUxhRRRQAUUUUAFFFFA
               BRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUA
               FFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFADSfmx2oI
               oooEhm47TRnb0ooppKxnd3HrQaKKXUtMj3dTTlOeTziiilu2F/esSUUUUygooooAKKKKAP//Z",
                "person_in_charge" => [
                "name" => "dr. NI KETUT PUSPA SARI, Sp. PK",
                "effective_date" => null,
                "phone" => "081234567351",
                "photo" => null,
                "uid" => "969180bd-f48f-4662-bf3a-9ff5d1eee037",
                "uid_profile" => "0b71770f-c539-4665-8615-159b3d181a83",
                "uid_employee_type" => "f12b2acd-6f84-4c7d-8d90-a8bc5215485d",
                "uid_employee_specialist" => null,
                "file_sign" => "525790298Ttd_dr-Ni-Ketut-Puspa-Sari,-Sp.PK.png",
                "path_sign" => null
                ]
                ],
                "printed" => "04/08/2022 17:41 Oleh : WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "approved" => "04/08/2022 00:12 Oleh : WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "verified" => "-",
                "verify_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "print_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "pemeriksaan" => [
                "PATOLOGI KLINIK" => [
                [
                "id" => 3,
                "type" => 1,
                "name" => "Complete Blood Count",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "position_departement" => "01",
                "position" => "01.01.03",
                "group" => null,
                "result_input_type" => null,
                "uid_group" => null,
                "uid" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "alias_code" => "undefined",
                "alias_name" => null,
                "keterangan" => "",
                "childs" => [
                [
                "id" => 1,
                "uid" => "ea79c683-567d-4962-8041-fc973c802a8f",
                "type" => 3,
                "name" => "Hemoglobin",
                "alias_code" => "HGB",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.05",
                "uid_unit" => "da9a3060-ecec-4896-bf6d-97e464bddf43",
                "unit" => "g/dL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "ddf72e6c-e9e7-48b0-a707-5a67facfdd68",
                "method" => "Cyanide free Hb",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "12.55-18.99",
                "flag" => "-",
                "value" => "15.37",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 2,
                "uid" => "158225f0-4ced-4688-8cc8-90b174a28735",
                "type" => 3,
                "name" => "Hematokrit",
                "alias_code" => "HCT",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.06",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "b81bb956-af6f-4521-bbb4-9c60087049c0",
                "method" => "HCT diukur",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "38.3-49.3",
                "flag" => "-",
                "value" => "44.5",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 3,
                "uid" => "14f7eaca-d7de-4d12-a5e5-8b4417c9124b",
                "type" => 3,
                "name" => "Eritrosit",
                "alias_code" => "RBC",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.07",
                "uid_unit" => "4111a5f3-ce3c-4342-a90c-428ba8d28485",
                "unit" => "10^6/uL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "893a66de-632e-4e12-aeec-0874acc7b1c9",
                "method" => "Electronic impedance",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "4.33-5.72",
                "flag" => "-",
                "value" => "4.82",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 6,
                "uid" => "62e46177-8dd4-4914-b8ca-dc783199795d",
                "type" => 3,
                "name" => "MCV",
                "alias_code" => "MCV",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.08",
                "uid_unit" => "14cc566e-6fa6-4b07-85ca-e054c1bf2544",
                "unit" => "fL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "78.3-95.5",
                "flag" => "-",
                "value" => "92.4",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 7,
                "uid" => "67fedb41-c1b3-40c4-86f9-67f2c8c7a944",
                "type" => 3,
                "name" => "MCH",
                "alias_code" => "MCH",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.09",
                "uid_unit" => "63e1f954-43a3-4499-b194-0bd762b4b05a",
                "unit" => "pg",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "25.9-33.2",
                "flag" => "-",
                "value" => "31.9",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 8,
                "uid" => "1d8f5420-67f9-4d41-bc13-1e6aaca86316",
                "type" => 3,
                "name" => "MCHC",
                "alias_code" => "MCHC",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.10",
                "uid_unit" => "da9a3060-ecec-4896-bf6d-97e464bddf43",
                "unit" => "g/dL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "33-35.3",
                "flag" => "-",
                "value" => "34.5",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 5,
                "uid" => "62b09ab8-ff66-4bb4-8202-8587829d587a",
                "type" => 3,
                "name" => "Trombosit",
                "alias_code" => "PLT",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.11",
                "uid_unit" => "e270ed0f-284c-43c5-bc84-49309c359df7",
                "unit" => "10^3/mm^3",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "893a66de-632e-4e12-aeec-0874acc7b1c9",
                "method" => "Electronic impedance",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "147-352",
                "flag" => "-",
                "value" => "219",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 4,
                "uid" => "50726c13-8479-40c0-83b7-c59643a7d441",
                "type" => 3,
                "name" => "Leukosit",
                "alias_code" => "WBC",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.12",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "bbb93b36-d91e-4879-9ce8-84cee300cd3f",
                "method" => "Fluoroscence",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "3.53-9.52",
                "flag" => "H",
                "value" => "12.04",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 9,
                "uid" => "b514dcef-db59-4357-883f-ba711369e7d6",
                "type" => 3,
                "name" => "DIFF COUNT",
                "alias_code" => "DIFF",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.15",
                "uid_unit" => null,
                "unit" => null,
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => null,
                "keterangan" => null,
                "keterangan_alpha" => "",
                "nilai_normal" => "-",
                "flag" => null,
                "value" => null,
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => null,
                "info_remark" => "",
                "childs" => [
                [
                "id" => 10,
                "uid" => "188d7122-e7fc-46db-9026-2fbff9a75649",
                "type" => 1,
                "name" => "Neutrofil",
                "alias_code" => "NE%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.0",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "40.62-70.51",
                "flag" => "H",
                "value" => "73.33",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 12,
                "uid" => "04aa8899-f884-4c13-bf6c-833176c7e40c",
                "type" => 1,
                "name" => "Basofil",
                "alias_code" => "BA%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.1",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0-0.11",
                "flag" => "H",
                "value" => "0.31",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 13,
                "uid" => "d65d0b85-1b3e-4832-96e4-47af5ee45707",
                "type" => 1,
                "name" => "Limfosit",
                "alias_code" => "LY%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.2",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "20.23-43.53",
                "flag" => "L",
                "value" => "13.70",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 14,
                "uid" => "8c8cd981-f86c-4c01-8f7b-9fff7ee766cd",
                "type" => 1,
                "name" => "Eosinofil",
                "alias_code" => "EO%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.3",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0.84-7.67",
                "flag" => "-",
                "value" => "2.02",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 15,
                "uid" => "5b5aa761-32d0-47c6-9d1b-a39f3df332b1",
                "type" => 1,
                "name" => "Monosit",
                "alias_code" => "MO%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.4",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "5.23-13.22",
                "flag" => "-",
                "value" => "10.64",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 322,
                "uid" => "dec86c97-e90b-47ad-b0e4-d2bdff33e186",
                "type" => 1,
                "name" => "NE#",
                "alias_code" => "NE#",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.0",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "1.85-5.94",
                "flag" => "H",
                "value" => "8.83",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 323,
                "uid" => "dddfd6cd-1794-4936-9e90-7b947ff330b1",
                "type" => 1,
                "name" => "BA#",
                "alias_code" => "BA#",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.1",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0-0.04",
                "flag" => "-",
                "value" => "0.04",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 324,
                "uid" => "7ee971b7-2850-4a4e-a573-ef71d42fe3b8",
                "type" => 1,
                "name" => "LY#",
                "alias_code" => "LY#",
                "alias_name" => null,
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.2",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => null,
                "keterangan_alpha" => "",
                "nilai_normal" => "1.15-3.13",
                "flag" => "-",
                "value" => "1.65",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 325,
                "uid" => "aef5503f-83e0-4828-a912-e35b52db99bc",
                "type" => 1,
                "name" => "EO#",
                "alias_code" => "EO#",
                "alias_name" => null,
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.3",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => null,
                "keterangan_alpha" => "",
                "nilai_normal" => "0.05-0.5",
                "flag" => "-",
                "value" => "0.24",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 326,
                "uid" => "77772681-d752-456b-bbfb-cb05b0fb40b7",
                "type" => 1,
                "name" => "MO#",
                "alias_code" => "MO#",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.16.4",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0.25-1.06",
                "flag" => "H",
                "value" => "1.28",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ]
                ],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ]
                ],
                "is_formated" => false
                ]
            ],
            "PATOLOGI KLINIK" => [
                [
                "id" => 3,
                "type" => 1,
                "name" => "Complete Blood Count",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "position_departement" => "01",
                "position" => "01.01.03",
                "group" => null,
                "result_input_type" => null,
                "uid_group" => null,
                "uid" => "6d7381b3-df6a-4c62-a42e-3ae6315715fb",
                "alias_code" => "undefined",
                "alias_name" => null,
                "keterangan" => "",
                "childs" => [
                [
                "id" => 1,
                "uid" => "ea79c683-567d-4962-8041-fc973c802a8f",
                "type" => 3,
                "name" => "Hemoglobin",
                "alias_code" => "HGB",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.05",
                "uid_unit" => "da9a3060-ecec-4896-bf6d-97e464bddf43",
                "unit" => "g/dL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "ddf72e6c-e9e7-48b0-a707-5a67facfdd68",
                "method" => "Cyanide free Hb",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "12.55-18.99",
                "flag" => "-",
                "value" => "15.37",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 2,
                "uid" => "158225f0-4ced-4688-8cc8-90b174a28735",
                "type" => 3,
                "name" => "Hematokrit",
                "alias_code" => "HCT",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.06",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "b81bb956-af6f-4521-bbb4-9c60087049c0",
                "method" => "HCT diukur",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "38.3-49.3",
                "flag" => "-",
                "value" => "44.5",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 3,
                "uid" => "14f7eaca-d7de-4d12-a5e5-8b4417c9124b",
                "type" => 3,
                "name" => "Eritrosit",
                "alias_code" => "RBC",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.07",
                "uid_unit" => "4111a5f3-ce3c-4342-a90c-428ba8d28485",
                "unit" => "10^6/uL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "893a66de-632e-4e12-aeec-0874acc7b1c9",
                "method" => "Electronic impedance",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "4.33-5.72",
                "flag" => "-",
                "value" => "4.82",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 6,
                "uid" => "62e46177-8dd4-4914-b8ca-dc783199795d",
                "type" => 3,
                "name" => "MCV",
                "alias_code" => "MCV",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.08",
                "uid_unit" => "14cc566e-6fa6-4b07-85ca-e054c1bf2544",
                "unit" => "fL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "78.3-95.5",
                "flag" => "-",
                "value" => "92.4",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 7,
                "uid" => "67fedb41-c1b3-40c4-86f9-67f2c8c7a944",
                "type" => 3,
                "name" => "MCH",
                "alias_code" => "MCH",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.09",
                "uid_unit" => "63e1f954-43a3-4499-b194-0bd762b4b05a",
                "unit" => "pg",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "25.9-33.2",
                "flag" => "-",
                "value" => "31.9",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 8,
                "uid" => "1d8f5420-67f9-4d41-bc13-1e6aaca86316",
                "type" => 3,
                "name" => "MCHC",
                "alias_code" => "MCHC",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.10",
                "uid_unit" => "da9a3060-ecec-4896-bf6d-97e464bddf43",
                "unit" => "g/dL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "33-35.3",
                "flag" => "-",
                "value" => "34.5",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 5,
                "uid" => "62b09ab8-ff66-4bb4-8202-8587829d587a",
                "type" => 3,
                "name" => "Trombosit",
                "alias_code" => "PLT",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.11",
                "uid_unit" => "e270ed0f-284c-43c5-bc84-49309c359df7",
                "unit" => "10^3/mm^3",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "893a66de-632e-4e12-aeec-0874acc7b1c9",
                "method" => "Electronic impedance",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "147-352",
                "flag" => "-",
                "value" => "219",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 4,
                "uid" => "50726c13-8479-40c0-83b7-c59643a7d441",
                "type" => 3,
                "name" => "Leukosit",
                "alias_code" => "WBC",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.12",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => "bbb93b36-d91e-4879-9ce8-84cee300cd3f",
                "method" => "Fluoroscence",
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "3.53-9.52",
                "flag" => "H",
                "value" => "12.04",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "childs" => [],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 9,
                "uid" => "b514dcef-db59-4357-883f-ba711369e7d6",
                "type" => 3,
                "name" => "DIFF COUNT",
                "alias_code" => "DIFF",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.15",
                "uid_unit" => null,
                "unit" => null,
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => null,
                "keterangan" => null,
                "keterangan_alpha" => "",
                "nilai_normal" => "-",
                "flag" => null,
                "value" => null,
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => null,
                "info_remark" => "",
                "childs" => [
                [
                "id" => 10,
                "uid" => "188d7122-e7fc-46db-9026-2fbff9a75649",
                "type" => 1,
                "name" => "Neutrofil",
                "alias_code" => "NE%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.0",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "40.62-70.51",
                "flag" => "H",
                "value" => "73.33",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 12,
                "uid" => "04aa8899-f884-4c13-bf6c-833176c7e40c",
                "type" => 1,
                "name" => "Basofil",
                "alias_code" => "BA%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.1",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0-0.11",
                "flag" => "H",
                "value" => "0.31",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 13,
                "uid" => "d65d0b85-1b3e-4832-96e4-47af5ee45707",
                "type" => 1,
                "name" => "Limfosit",
                "alias_code" => "LY%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.2",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "20.23-43.53",
                "flag" => "L",
                "value" => "13.70",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 14,
                "uid" => "8c8cd981-f86c-4c01-8f7b-9fff7ee766cd",
                "type" => 1,
                "name" => "Eosinofil",
                "alias_code" => "EO%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.3",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0.84-7.67",
                "flag" => "-",
                "value" => "2.02",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 15,
                "uid" => "5b5aa761-32d0-47c6-9d1b-a39f3df332b1",
                "type" => 1,
                "name" => "Monosit",
                "alias_code" => "MO%",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.15.4",
                "uid_unit" => "63949031-5bec-42e5-b1b8-7c9976aaaa9d",
                "unit" => "%",
                "uid_specimen" => "d9d29d64-a487-49e5-8355-3d5b9f03bdec",
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "5.23-13.22",
                "flag" => "-",
                "value" => "10.64",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 322,
                "uid" => "dec86c97-e90b-47ad-b0e4-d2bdff33e186",
                "type" => 1,
                "name" => "NE#",
                "alias_code" => "NE#",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.0",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "1.85-5.94",
                "flag" => "H",
                "value" => "8.83",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 323,
                "uid" => "dddfd6cd-1794-4936-9e90-7b947ff330b1",
                "type" => 1,
                "name" => "BA#",
                "alias_code" => "BA#",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.1",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0-0.04",
                "flag" => "-",
                "value" => "0.04",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 324,
                "uid" => "7ee971b7-2850-4a4e-a573-ef71d42fe3b8",
                "type" => 1,
                "name" => "LY#",
                "alias_code" => "LY#",
                "alias_name" => null,
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.2",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => null,
                "keterangan_alpha" => "",
                "nilai_normal" => "1.15-3.13",
                "flag" => "-",
                "value" => "1.65",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 325,
                "uid" => "aef5503f-83e0-4828-a912-e35b52db99bc",
                "type" => 1,
                "name" => "EO#",
                "alias_code" => "EO#",
                "alias_name" => null,
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => "06214f42-365a-4f1b-ac5c-d919ac8af800",
                "group" => "PK - HAEMATOLOGY",
                "position" => "01.01.16.3",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => null,
                "keterangan_alpha" => "",
                "nilai_normal" => "0.05-0.5",
                "flag" => "-",
                "value" => "0.24",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ],
                [
                "id" => 326,
                "uid" => "77772681-d752-456b-bbfb-cb05b0fb40b7",
                "type" => 1,
                "name" => "MO#",
                "alias_code" => "MO#",
                "alias_name" => "null",
                "uid_departement" => "61a027b7-93d2-4ba2-b184-9216d9e7f209",
                "departement" => "PATOLOGI KLINIK",
                "uid_group" => null,
                "group" => null,
                "position" => "01.01.16.4",
                "uid_unit" => "6f399441-b30d-42a7-8165-8ae70605a140",
                "unit" => "10^3/uL",
                "uid_specimen" => null,
                "uid_method" => null,
                "method" => null,
                "uid_result_input_type" => "20602a4d-d1cf-4fea-b302-29ea0634b840",
                "formula_nilai" => false,
                "is_formula" => null,
                "result_input_type" => 1,
                "comment" => [],
                "keterangan" => "null",
                "keterangan_alpha" => "",
                "nilai_normal" => "0.25-1.06",
                "flag" => "H",
                "value" => "1.28",
                "value_string" => "",
                "value_memo" => null,
                "acc_by" => "WHEMPY LEKSONO ABIMANYU, A.Md.AK",
                "info_remark" => "",
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ]
                ],
                "role_base" => [],
                "role_text" => "",
                "status_use_reference" => true,
                "is_formated" => false
                ]
                ],
                "is_formated" => false
                ]
                ]
                ],
                "memo" => [],
                "formated" => [],
                "QRCode" =>
               "data:image/png;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWx
               ucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjgwIiBoZWlnaHQ9Ij
               gwIiB2aWV3Qm94PSIwIDAgODAgODAiPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCIg
               ZmlsbD0iI2ZmZmZmZiIvPjxnIHRyYW5zZm9ybT0ic2NhbGUoMy44MSkiPjxnIHRyYW5zZm9ybT0idHJhbnNs
               YXRlKDAsMCkiPjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTggMC4yNUw4IDAuNzVBMC4yNSAwLjI1I
               DAgMCAwIDguMjUgMUw4Ljc1IDFBMC4yNSAwLjI1IDAgMCAxIDkgMS4yNUw5IDEuNzVBMC4yNSAwLjI1I
               DAgMCAxIDguNzUgMkw4LjI1IDJBMC4yNSAwLjI1IDAgMCAwIDggMi4yNUw4IDQuNzVBMC4yNSAwLjI1ID
               AgMCAwIDguMjUgNUwxMS43NSA1QTAuMjUgMC4yNSAwIDAgMCAxMiA0Ljc1TDEyIDMuMjVBMC4yNS
               AwLjI1IDAgMCAxIDEyLjI1IDNMMTIuNzUgM0EwLjI1IDAuMjUgMCAwIDAgMTMgMi43NUwxMyAwLjI1QT
               AuMjUgMC4yNSAwIDAgMCAxMi43NSAwTDEyLjI1IDBBMC4yNSAwLjI1IDAgMCAwIDEyIDAuMjVMMTIgMi
               43NUEwLjI1IDAuMjUgMCAwIDEgMTEuNzUgM0wxMS4yNSAzQTAuMjUgMC4yNSAwIDAgMSAxMSAyLjc1
               TDExIDIuMjVBMC4yNSAwLjI1IDAgMCAwIDEwLjc1IDJMMTAuMjUgMkEwLjI1IDAuMjUgMCAwIDEgMTAg
               MS43NUwxMCAwLjI1QTAuMjUgMC4yNSAwIDAgMCA5Ljc1IDBMOC4yNSAwQTAuMjUgMC4yNSAwIDAg
               MCA4IDAuMjVaTTkgMi4yNUw5IDMuNzVBMC4yNSAwLjI1IDAgMCAwIDkuMjUgNEwxMC43NSA0QTAuMj
               UgMC4yNSAwIDAgMCAxMSAzLjc1TDExIDMuMjVBMC4yNSAwLjI1IDAgMCAwIDEwLjc1IDNMMTAuMjUg
               M0EwLjI1IDAuMjUgMCAwIDEgMTAgMi43NUwxMCAyLjI1QTAuMjUgMC4yNSAwIDAgMCA5Ljc1IDJMOS4
               yNSAyQTAuMjUgMC4yNSAwIDAgMCA5IDIuMjVaTTggNi4yNUw4IDYuNzVBMC4yNSAwLjI1IDAgMCAwIDg
               uMjUgN0w4Ljc1IDdBMC4yNSAwLjI1IDAgMCAxIDkgNy4yNUw5IDcuNzVBMC4yNSAwLjI1IDAgMCAxIDguN
               zUgOEw4LjI1IDhBMC4yNSAwLjI1IDAgMCAwIDggOC4yNUw4IDguNzVBMC4yNSAwLjI1IDAgMCAxIDcuNzU
               gOUw3LjI1IDlBMC4yNSAwLjI1IDAgMCAxIDcgOC43NUw3IDguMjVBMC4yNSAwLjI1IDAgMCAwIDYuNzUgO
               Ew2LjI1IDhBMC4yNSAwLjI1IDAgMCAwIDYgOC4yNUw2IDguNzVBMC4yNSAwLjI1IDAgMCAxIDUuNzUgOU
               w0LjI1IDlBMC4yNSAwLjI1IDAgMCAxIDQgOC43NUw0IDguMjVBMC4yNSAwLjI1IDAgMCAwIDMuNzUgOE
               wwLjI1IDhBMC4yNSAwLjI1IDAgMCAwIDAgOC4yNUwwIDguNzVBMC4yNSAwLjI1IDAgMCAwIDAuMjUgO
               UwxLjc1IDlBMC4yNSAwLjI1IDAgMCAxIDIgOS4yNUwyIDkuNzVBMC4yNSAwLjI1IDAgMCAwIDIuMjUgMTB
               MMi43NSAxMEEwLjI1IDAuMjUgMCAwIDEgMyAxMC4yNUwzIDEwLjc1QTAuMjUgMC4yNSAwIDAgMSAyL
               jc1IDExTDEuMjUgMTFBMC4yNSAwLjI1IDAgMCAxIDEgMTAuNzVMMSAxMC4yNUEwLjI1IDAuMjUgMCAwI
               DAgMC43NSAxMEwwLjI1IDEwQTAuMjUgMC4yNSAwIDAgMCAwIDEwLjI1TDAgMTAuNzVBMC4yNSAwLjI
               1IDAgMCAwIDAuMjUgMTFMMC43NSAxMUEwLjI1IDAuMjUgMCAwIDEgMSAxMS4yNUwxIDExLjc1QTAu
               MjUgMC4yNSAwIDAgMSAwLjc1IDEyTDAuMjUgMTJBMC4yNSAwLjI1IDAgMCAwIDAgMTIuMjVMMCAxMi
               43NUEwLjI1IDAuMjUgMCAwIDAgMC4yNSAxM0w0Ljc1IDEzQTAuMjUgMC4yNSAwIDAgMCA1IDEyLjc1TD
               UgMTIuMjVBMC4yNSAwLjI1IDAgMCAxIDUuMjUgMTJMNS43NSAxMkEwLjI1IDAuMjUgMCAwIDEgNiAxMi
               4yNUw2IDEyLjc1QTAuMjUgMC4yNSAwIDAgMCA2LjI1IDEzTDcuNzUgMTNBMC4yNSAwLjI1IDAgMCAxIDg
               gMTMuMjVMOCAxMy43NUEwLjI1IDAuMjUgMCAwIDAgOC4yNSAxNEw5Ljc1IDE0QTAuMjUgMC4yNSAwI
               DAgMCAxMCAxMy43NUwxMCAxMy4yNUEwLjI1IDAuMjUgMCAwIDEgMTAuMjUgMTNMMTAuNzUgMT
               NBMC4yNSAwLjI1IDAgMCAxIDExIDEzLjI1TDExIDE0Ljc1QTAuMjUgMC4yNSAwIDAgMSAxMC43NSAxNUw
               5LjI1IDE1QTAuMjUgMC4yNSAwIDAgMCA5IDE1LjI1TDkgMTUuNzVBMC4yNSAwLjI1IDAgMCAwIDkuMjUg
               MTZMMTEuNzUgMTZBMC4yNSAwLjI1IDAgMCAxIDEyIDE2LjI1TDEyIDE2Ljc1QTAuMjUgMC4yNSAwIDAg
               MCAxMi4yNSAxN0wxMi43NSAxN0EwLjI1IDAuMjUgMCAwIDEgMTMgMTcuMjVMMTMgMTcuNzVBMC4y
               NSAwLjI1IDAgMCAxIDEyLjc1IDE4TDExLjI1IDE4QTAuMjUgMC4yNSAwIDAgMSAxMSAxNy43NUwxMSAxNy
               4yNUEwLjI1IDAuMjUgMCAwIDAgMTAuNzUgMTdMMTAuMjUgMTdBMC4yNSAwLjI1IDAgMCAwIDEwIDE
               3LjI1TDEwIDE3Ljc1QTAuMjUgMC4yNSAwIDAgMCAxMC4yNSAxOEwxMC43NSAxOEEwLjI1IDAuMjUgMCA
               wIDEgMTEgMTguMjVMMTEgMTguNzVBMC4yNSAwLjI1IDAgMCAxIDEwLjc1IDE5TDEwLjI1IDE5QTAuMjU
               gMC4yNSAwIDAgMCAxMCAxOS4yNUwxMCAxOS43NUEwLjI1IDAuMjUgMCAwIDEgOS43NSAyMEw5LjI1I
               DIwQTAuMjUgMC4yNSAwIDAgMSA5IDE5Ljc1TDkgMTcuMjVBMC4yNSAwLjI1IDAgMCAwIDguNzUgMTd
               MOC4yNSAxN0EwLjI1IDAuMjUgMCAwIDAgOCAxNy4yNUw4IDIwLjc1QTAuMjUgMC4yNSAwIDAgMCA4LjI
               1IDIxTDkuNzUgMjFBMC4yNSAwLjI1IDAgMCAwIDEwIDIwLjc1TDEwIDIwLjI1QTAuMjUgMC4yNSAwIDAgM
               SAxMC4yNSAyMEwxMC43NSAyMEEwLjI1IDAuMjUgMCAwIDAgMTEgMTkuNzVMMTEgMTkuMjVBMC4y
               NSAwLjI1IDAgMCAxIDExLjI1IDE5TDExLjc1IDE5QTAuMjUgMC4yNSAwIDAgMSAxMiAxOS4yNUwxMiAxOS4
               3NUEwLjI1IDAuMjUgMCAwIDAgMTIuMjUgMjBMMTIuNzUgMjBBMC4yNSAwLjI1IDAgMCAxIDEzIDIwLjI1T
               DEzIDIwLjc1QTAuMjUgMC4yNSAwIDAgMCAxMy4yNSAyMUwxMy43NSAyMUEwLjI1IDAuMjUgMCAwIDA
               gMTQgMjAuNzVMMTQgMjAuMjVBMC4yNSAwLjI1IDAgMCAxIDE0LjI1IDIwTDE0Ljc1IDIwQTAuMjUgMC4y
               NSAwIDAgMSAxNSAyMC4yNUwxNSAyMC43NUEwLjI1IDAuMjUgMCAwIDAgMTUuMjUgMjFMMTUuNzU
               gMjFBMC4yNSAwLjI1IDAgMCAwIDE2IDIwLjc1TDE2IDIwLjI1QTAuMjUgMC4yNSAwIDAgMSAxNi4yNSAyM
               EwxNi43NSAyMEEwLjI1IDAuMjUgMCAwIDAgMTcgMTkuNzVMMTcgMTkuMjVBMC4yNSAwLjI1IDAgMCA
               wIDE2Ljc1IDE5TDEzLjI1IDE5QTAuMjUgMC4yNSAwIDAgMSAxMyAxOC43NUwxMyAxOC4yNUEwLjI1IDAu
               MjUgMCAwIDEgMTMuMjUgMThMMTMuNzUgMThBMC4yNSAwLjI1IDAgMCAwIDE0IDE3Ljc1TDE0IDE3Lj
               I1QTAuMjUgMC4yNSAwIDAgMCAxMy43NSAxN0wxMy4yNSAxN0EwLjI1IDAuMjUgMCAwIDEgMTMgMTY
               uNzVMMTMgMTYuMjVBMC4yNSAwLjI1IDAgMCAwIDEyLjc1IDE2TDEyLjI1IDE2QTAuMjUgMC4yNSAwIDA
               gMSAxMiAxNS43NUwxMiAxNC4yNUEwLjI1IDAuMjUgMCAwIDEgMTIuMjUgMTRMMTIuNzUgMTRBMC4y
               NSAwLjI1IDAgMCAxIDEzIDE0LjI1TDEzIDE0Ljc1QTAuMjUgMC4yNSAwIDAgMCAxMy4yNSAxNUwxNC43NS
               AxNUEwLjI1IDAuMjUgMCAwIDEgMTUgMTUuMjVMMTUgMTUuNzVBMC4yNSAwLjI1IDAgMCAwIDE1LjI1I
               DE2TDE1Ljc1IDE2QTAuMjUgMC4yNSAwIDAgMSAxNiAxNi4yNUwxNiAxNi43NUEwLjI1IDAuMjUgMCAwID
               AgMTYuMjUgMTdMMTYuNzUgMTdBMC4yNSAwLjI1IDAgMCAxIDE3IDE3LjI1TDE3IDE3Ljc1QTAuMjUgMC
               4yNSAwIDAgMCAxNy4yNSAxOEwxNy43NSAxOEEwLjI1IDAuMjUgMCAwIDEgMTggMTguMjVMMTggMTg
               uNzVBMC4yNSAwLjI1IDAgMCAwIDE4LjI1IDE5TDE4Ljc1IDE5QTAuMjUgMC4yNSAwIDAgMCAxOSAxOC43
               NUwxOSAxOC4yNUEwLjI1IDAuMjUgMCAwIDEgMTkuMjUgMThMMTkuNzUgMThBMC4yNSAwLjI1IDAgM
               CAwIDIwIDE3Ljc1TDIwIDE2LjI1QTAuMjUgMC4yNSAwIDAgMSAyMC4yNSAxNkwyMC43NSAxNkEwLjI1IDA
               uMjUgMCAwIDAgMjEgMTUuNzVMMjEgMTUuMjVBMC4yNSAwLjI1IDAgMCAwIDIwLjc1IDE1TDIwLjI1IDE1
               QTAuMjUgMC4yNSAwIDAgMCAyMCAxNS4yNUwyMCAxNS43NUEwLjI1IDAuMjUgMCAwIDEgMTkuNzUg
               MTZMMTkuMjUgMTZBMC4yNSAwLjI1IDAgMCAxIDE5IDE1Ljc1TDE5IDEzLjI1QTAuMjUgMC4yNSAwIDAg
               MCAxOC43NSAxM0wxOC4yNSAxM0EwLjI1IDAuMjUgMCAwIDAgMTggMTMuMjVMMTggMTQuNzVBMC
               4yNSAwLjI1IDAgMCAxIDE3Ljc1IDE1TDE3LjI1IDE1QTAuMjUgMC4yNSAwIDAgMSAxNyAxNC43NUwxNyAx
               My4yNUEwLjI1IDAuMjUgMCAwIDAgMTYuNzUgMTNMMTYuMjUgMTNBMC4yNSAwLjI1IDAgMCAxIDE2I
               DEyLjc1TDE2IDEyLjI1QTAuMjUgMC4yNSAwIDAgMSAxNi4yNSAxMkwxNy43NSAxMkEwLjI1IDAuMjUgMC
               AwIDAgMTggMTEuNzVMMTggMTEuMjVBMC4yNSAwLjI1IDAgMCAxIDE4LjI1IDExTDE5Ljc1IDExQTAuMjU
               gMC4yNSAwIDAgMSAyMCAxMS4yNUwyMCAxMS43NUEwLjI1IDAuMjUgMCAwIDAgMjAuMjUgMTJMMj
               AuNzUgMTJBMC4yNSAwLjI1IDAgMCAwIDIxIDExLjc1TDIxIDguMjVBMC4yNSAwLjI1IDAgMCAwIDIwLjc1ID
               hMMjAuMjUgOEEwLjI1IDAuMjUgMCAwIDAgMjAgOC4yNUwyMCA5Ljc1QTAuMjUgMC4yNSAwIDAgMSA
               xOS43NSAxMEwxOC4yNSAxMEEwLjI1IDAuMjUgMCAwIDEgMTggOS43NUwxOCA5LjI1QTAuMjUgMC4yN
               SAwIDAgMSAxOC4yNSA5TDE4Ljc1IDlBMC4yNSAwLjI1IDAgMCAwIDE5IDguNzVMMTkgOC4yNUEwLjI1IDA
               uMjUgMCAwIDAgMTguNzUgOEwxNi4yNSA4QTAuMjUgMC4yNSAwIDAgMCAxNiA4LjI1TDE2IDguNzVBM
               C4yNSAwLjI1IDAgMCAwIDE2LjI1IDlMMTYuNzUgOUEwLjI1IDAuMjUgMCAwIDEgMTcgOS4yNUwxNyA5Ljc
               1QTAuMjUgMC4yNSAwIDAgMCAxNy4yNSAxMEwxNy43NSAxMEEwLjI1IDAuMjUgMCAwIDEgMTggMTAu
               MjVMMTggMTAuNzVBMC4yNSAwLjI1IDAgMCAxIDE3Ljc1IDExTDE2LjI1IDExQTAuMjUgMC4yNSAwIDAgM
               SAxNiAxMC43NUwxNiAxMC4yNUEwLjI1IDAuMjUgMCAwIDAgMTUuNzUgMTBMMTMuMjUgMTBBMC4y
               NSAwLjI1IDAgMCAwIDEzIDEwLjI1TDEzIDEwLjc1QTAuMjUgMC4yNSAwIDAgMCAxMy4yNSAxMUwxNS43
               NSAxMUEwLjI1IDAuMjUgMCAwIDEgMTYgMTEuMjVMMTYgMTEuNzVBMC4yNSAwLjI1IDAgMCAxIDE1Ljc
               1IDEyTDE1LjI1IDEyQTAuMjUgMC4yNSAwIDAgMCAxNSAxMi4yNUwxNSAxMi43NUEwLjI1IDAuMjUgMCA
               wIDEgMTQuNzUgMTNMMTQuMjUgMTNBMC4yNSAwLjI1IDAgMCAxIDE0IDEyLjc1TDE0IDEyLjI1QTAuMjU
               gMC4yNSAwIDAgMCAxMy43NSAxMkw5LjI1IDEyQTAuMjUgMC4yNSAwIDAgMSA5IDExLjc1TDkgMTEuMj
               VBMC4yNSAwLjI1IDAgMCAwIDguNzUgMTFMOC4yNSAxMUEwLjI1IDAuMjUgMCAwIDEgOCAxMC43NUw
               4IDEwLjI1QTAuMjUgMC4yNSAwIDAgMSA4LjI1IDEwTDkuNzUgMTBBMC4yNSAwLjI1IDAgMCAxIDEwIDEw
               LjI1TDEwIDEwLjc1QTAuMjUgMC4yNSAwIDAgMCAxMC4yNSAxMUwxMC43NSAxMUEwLjI1IDAuMjUgMC
               AwIDAgMTEgMTAuNzVMMTEgMTAuMjVBMC4yNSAwLjI1IDAgMCAwIDEwLjc1IDEwTDEwLjI1IDEwQTAu
               MjUgMC4yNSAwIDAgMSAxMCA5Ljc1TDEwIDkuMjVBMC4yNSAwLjI1IDAgMCAxIDEwLjI1IDlMMTEuNzUg
               OUEwLjI1IDAuMjUgMCAwIDAgMTIgOC43NUwxMiA4LjI1QTAuMjUgMC4yNSAwIDAgMCAxMS43NSA4TD
               ExLjI1IDhBMC4yNSAwLjI1IDAgMCAxIDExIDcuNzVMMTEgNi4yNUEwLjI1IDAuMjUgMCAwIDAgMTAuNzUg
               NkwxMC4yNSA2QTAuMjUgMC4yNSAwIDAgMCAxMCA2LjI1TDEwIDYuNzVBMC4yNSAwLjI1IDAgMCAxIDk
               uNzUgN0w5LjI1IDdBMC4yNSAwLjI1IDAgMCAxIDkgNi43NUw5IDYuMjVBMC4yNSAwLjI1IDAgMCAwIDguN
               zUgNkw4LjI1IDZBMC4yNSAwLjI1IDAgMCAwIDggNi4yNVpNMTIgNi4yNUwxMiA2Ljc1QTAuMjUgMC4yNSA
               wIDAgMCAxMi4yNSA3TDEyLjc1IDdBMC4yNSAwLjI1IDAgMCAwIDEzIDYuNzVMMTMgNi4yNUEwLjI1IDAu
               MjUgMCAwIDAgMTIuNzUgNkwxMi4yNSA2QTAuMjUgMC4yNSAwIDAgMCAxMiA2LjI1Wk05IDguMjVMOS
               A4Ljc1QTAuMjUgMC4yNSAwIDAgMCA5LjI1IDlMOS43NSA5QTAuMjUgMC4yNSAwIDAgMCAxMCA4Ljc1T
               DEwIDguMjVBMC4yNSAwLjI1IDAgMCAwIDkuNzUgOEw5LjI1IDhBMC4yNSAwLjI1IDAgMCAwIDkgOC4yNV
               pNMTMgOC4yNUwxMyA4Ljc1QTAuMjUgMC4yNSAwIDAgMCAxMy4yNSA5TDEzLjc1IDlBMC4yNSAwLjI1I
               DAgMCAwIDE0IDguNzVMMTQgOC4yNUEwLjI1IDAuMjUgMCAwIDAgMTMuNzUgOEwxMy4yNSA4QTAu
               MjUgMC4yNSAwIDAgMCAxMyA4LjI1Wk02IDkuMjVMNiA5Ljc1QTAuMjUgMC4yNSAwIDAgMCA2LjI1IDEw
               TDYuNzUgMTBBMC4yNSAwLjI1IDAgMCAwIDcgOS43NUw3IDkuMjVBMC4yNSAwLjI1IDAgMCAwIDYuNzU
               gOUw2LjI1IDlBMC4yNSAwLjI1IDAgMCAwIDYgOS4yNVpNNiAxMS4yNUw2IDExLjc1QTAuMjUgMC4yNSAw
               IDAgMCA2LjI1IDEyTDYuNzUgMTJBMC4yNSAwLjI1IDAgMCAwIDcgMTEuNzVMNyAxMS4yNUEwLjI1IDAu
               MjUgMCAwIDAgNi43NSAxMUw2LjI1IDExQTAuMjUgMC4yNSAwIDAgMCA2IDExLjI1Wk0xMyAxMy4yNUw
               xMyAxMy43NUEwLjI1IDAuMjUgMCAwIDAgMTMuMjUgMTRMMTMuNzUgMTRBMC4yNSAwLjI1IDAgMC
               AwIDE0IDEzLjc1TDE0IDEzLjI1QTAuMjUgMC4yNSAwIDAgMCAxMy43NSAxM0wxMy4yNSAxM0EwLjI1IDA
               uMjUgMCAwIDAgMTMgMTMuMjVaTTE1IDEzLjI1TDE1IDE0Ljc1QTAuMjUgMC4yNSAwIDAgMCAxNS4yNS
               AxNUwxNS43NSAxNUEwLjI1IDAuMjUgMCAwIDEgMTYgMTUuMjVMMTYgMTUuNzVBMC4yNSAwLjI1IDA
               gMCAwIDE2LjI1IDE2TDE2Ljc1IDE2QTAuMjUgMC4yNSAwIDAgMSAxNyAxNi4yNUwxNyAxNi43NUEwLjI1ID
               AuMjUgMCAwIDAgMTcuMjUgMTdMMTcuNzUgMTdBMC4yNSAwLjI1IDAgMCAxIDE4IDE3LjI1TDE4IDE3Lj
               c1QTAuMjUgMC4yNSAwIDAgMCAxOC4yNSAxOEwxOC43NSAxOEEwLjI1IDAuMjUgMCAwIDAgMTkgMTcu
               NzVMMTkgMTcuMjVBMC4yNSAwLjI1IDAgMCAwIDE4Ljc1IDE3TDE4LjI1IDE3QTAuMjUgMC4yNSAwIDAg
               MSAxOCAxNi43NUwxOCAxNi4yNUEwLjI1IDAuMjUgMCAwIDAgMTcuNzUgMTZMMTcuMjUgMTZBMC4yN
               SAwLjI1IDAgMCAxIDE3IDE1Ljc1TDE3IDE1LjI1QTAuMjUgMC4yNSAwIDAgMCAxNi43NSAxNUwxNi4yNSAx
               NUEwLjI1IDAuMjUgMCAwIDEgMTYgMTQuNzVMMTYgMTMuMjVBMC4yNSAwLjI1IDAgMCAwIDE1Ljc1ID
               EzTDE1LjI1IDEzQTAuMjUgMC4yNSAwIDAgMCAxNSAxMy4yNVpNMjAgMTkuMjVMMjAgMTkuNzVBMC4y
               NSAwLjI1IDAgMCAwIDIwLjI1IDIwTDIwLjc1IDIwQTAuMjUgMC4yNSAwIDAgMCAyMSAxOS43NUwyMSAxO
               S4yNUEwLjI1IDAuMjUgMCAwIDAgMjAuNzUgMTlMMjAuMjUgMTlBMC4yNSAwLjI1IDAgMCAwIDIwIDE5Lj
               I1Wk0xOCAyMC4yNUwxOCAyMC43NUEwLjI1IDAuMjUgMCAwIDAgMTguMjUgMjFMMTguNzUgMjFBMC
               4yNSAwLjI1IDAgMCAwIDE5IDIwLjc1TDE5IDIwLjI1QTAuMjUgMC4yNSAwIDAgMCAxOC43NSAyMEwxOC4y
               NSAyMEEwLjI1IDAuMjUgMCAwIDAgMTggMjAuMjVaTTAgMC4yNUwwIDYuNzVBMC4yNSAwLjI1IDAgMC
               AwIDAuMjUgN0w2Ljc1IDdBMC4yNSAwLjI1IDAgMCAwIDcgNi43NUw3IDAuMjVBMC4yNSAwLjI1IDAgMCA
               wIDYuNzUgMEwwLjI1IDBBMC4yNSAwLjI1IDAgMCAwIDAgMC4yNVpNMSAxLjI1TDEgNS43NUEwLjI1IDAu
               MjUgMCAwIDAgMS4yNSA2TDUuNzUgNkEwLjI1IDAuMjUgMCAwIDAgNiA1Ljc1TDYgMS4yNUEwLjI1IDAu
               MjUgMCAwIDAgNS43NSAxTDEuMjUgMUEwLjI1IDAuMjUgMCAwIDAgMSAxLjI1Wk0yIDIuMjVMMiA0Ljc1
               QTAuMjUgMC4yNSAwIDAgMCAyLjI1IDVMNC43NSA1QTAuMjUgMC4yNSAwIDAgMCA1IDQuNzVMNSAyL
               jI1QTAuMjUgMC4yNSAwIDAgMCA0Ljc1IDJMMi4yNSAyQTAuMjUgMC4yNSAwIDAgMCAyIDIuMjVaTTE0I
               DAuMjVMMTQgNi43NUEwLjI1IDAuMjUgMCAwIDAgMTQuMjUgN0wyMC43NSA3QTAuMjUgMC4yNSAwI
               DAgMCAyMSA2Ljc1TDIxIDAuMjVBMC4yNSAwLjI1IDAgMCAwIDIwLjc1IDBMMTQuMjUgMEEwLjI1IDAuMj
               UgMCAwIDAgMTQgMC4yNVpNMTUgMS4yNUwxNSA1Ljc1QTAuMjUgMC4yNSAwIDAgMCAxNS4yNSA2T
               DE5Ljc1IDZBMC4yNSAwLjI1IDAgMCAwIDIwIDUuNzVMMjAgMS4yNUEwLjI1IDAuMjUgMCAwIDAgMTkuN
               zUgMUwxNS4yNSAxQTAuMjUgMC4yNSAwIDAgMCAxNSAxLjI1Wk0xNiAyLjI1TDE2IDQuNzVBMC4yNSAwL
               jI1IDAgMCAwIDE2LjI1IDVMMTguNzUgNUEwLjI1IDAuMjUgMCAwIDAgMTkgNC43NUwxOSAyLjI1QTAuMj
               UgMC4yNSAwIDAgMCAxOC43NSAyTDE2LjI1IDJBMC4yNSAwLjI1IDAgMCAwIDE2IDIuMjVaTTAgMTQuMjV
               MMCAyMC43NUEwLjI1IDAuMjUgMCAwIDAgMC4yNSAyMUw2Ljc1IDIxQTAuMjUgMC4yNSAwIDAgMCA3
               IDIwLjc1TDcgMTQuMjVBMC4yNSAwLjI1IDAgMCAwIDYuNzUgMTRMMC4yNSAxNEEwLjI1IDAuMjUgMCA
               wIDAgMCAxNC4yNVpNMSAxNS4yNUwxIDE5Ljc1QTAuMjUgMC4yNSAwIDAgMCAxLjI1IDIwTDUuNzUgMj
               BBMC4yNSAwLjI1IDAgMCAwIDYgMTkuNzVMNiAxNS4yNUEwLjI1IDAuMjUgMCAwIDAgNS43NSAxNUwxLj
               I1IDE1QTAuMjUgMC4yNSAwIDAgMCAxIDE1LjI1Wk0yIDE2LjI1TDIgMTguNzVBMC4yNSAwLjI1IDAgMCAwI
               DIuMjUgMTlMNC43NSAxOUEwLjI1IDAuMjUgMCAwIDAgNSAxOC43NUw1IDE2LjI1QTAuMjUgMC4yNSAw
               IDAgMCA0Ljc1IDE2TDIuMjUgMTZBMC4yNSAwLjI1IDAgMCAwIDIgMTYuMjVaIiBmaWxsPSIjMDAwMDAwI
               i8+PC9nPjwvZz48L3N2Zz4K",
                "language" => [
                "title_head" => "HASIL PEMERIKSAAN LABORATORIUM",
                "regis_date" => "Tgl.Pendaftaran",
                "name" => "Nama Pasien",
                "mrn" => "No. SID/PID",
                "tgl_lahir" => "Tgl Lahir",
                "sender" => "Pengirim",
                "gender" => "Jenis Kelamin",
                "address" => "Alamat",
                "phone" => "No.Tlp",
                "exam" => "Pemeriksaan",
                "result" => "Hasil",
                "nilai_normal" => "Nilai Normal",
                "satuan" => "Satuan",
                "metode" => "Metode",
                "note" => "Catatan",
                "fast" => "Puasa",
                "not_fast" => "Tidak Puasa",
                "validation" => "Divalidasi oleh,",
                "date_sample" => "Waktu Pengambilan Sample",
                "print_by" => "Dicetak oleh",
                "formatDob" => "d/m/Y",
                "y" => "th ",
                "m" => "bln ",
                "d" => "hr",
                "M" => "Laki-laki",
                "F" => "Perempuan",
                "pregnant" => "(Hamil)",
                "dateTime" => "d/m/Y H:i"
                ],
                "lang" => "id"
                ]
               ];
            
        return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve result request lab.',
            ], 500);
        }
    }
}

