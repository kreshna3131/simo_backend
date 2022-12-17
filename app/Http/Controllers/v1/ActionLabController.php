<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActionLab;
use App\Models\ActivityLog;
use App\Models\RequestLab;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ActionLabController extends Controller
{    
    /**
     * listing tindakan laboratorium
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @param  mixed $requestLab
     * @return void
     */
    public function listingMeasure(Request $request, RequestLab $requestLab)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $actionLabs = ActionLab::query()
                ->where('request_lab_id', $requestLab->id)
                ->where('is_add', 1)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where(function($query) use ($request) {
                            $date = searchDate($request->search, 'd F Y', 'Y-m-d');
                            $query->where('action_group', 'like', '%' . $request->search . '%')
                                    ->orwhere('name', 'like', '%' . $request->search . '%')
                                    ->orwhere('status', 'like', '%' . $request->search . '%');
                            if($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            } 
                        });
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'order_number', $request->order_dir ? $request->order_dir : 'asc');
            
            $actionLabPaginate = (new CustomPaginator(
                $actionLabs->clone()->forPage($currentPage, $itemPerpage)->get(),
                $actionLabs->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/laboratorium/"'.$requestLab->id.'"/measure/listing');
    
            return response()->json(array_merge(
                $actionLabPaginate->toArray(), 
                ['not_serve_count' => $actionLabs->clone()->where('status', 'unfinish')->count()]
            ));
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve action lab.',
            ], 500);
        }
    }
    
    /**
     * listing tindakan untuk tambah tindakan
     *
     * @param  mixed $requestLab
     * @return void
     */
    public function listingMeasureForUpdate(RequestLab $requestLab)
    {
        try {
            $actionLabs = ActionLab::query()
                ->where('request_lab_id', $requestLab->id)
                ->where('is_add', 1)
                ->get();
            
            $data = [];
            foreach ($actionLabs as $key => $action) {
                $row['action_id'] = strval($action->action_id);
                $row['action_group_id'] = strval($action->action_group_id);
                $row['action_group'] = $action->action_group;
                $row['name'] = $action->name;
                $data[] = $row;
            }
            
            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve action lab.',
            ], 500);
        }
    }
    
    /**
     * Update status tindakan laboratorium
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @param  mixed $requestLab
     * @param  mixed $actionLab
     * @return void
     */
    public function updateStatusMeasure(Request $request, RequestLab $requestLab, ActionLab $actionLab)
    {
        $data_validated = $request->validate(['status' => 'required']);
        try {
            $actionLab->update($data_validated);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestLab->visit_id,
                'unique_id' => $requestLab->unique_id,
                'request_id' => $requestLab->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Laboratorium.',
                'type' => 'Laboratorium',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action lab sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action lab.',
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
     * Tambah tindakan laboratorium
     *
     * @param  mixed $request
     * @return void
     */
    public function storeMeasure(Request $request, RequestLab $requestLab)
    {
        $request->validate(['action_id' => 'required', 'action_group_id' => 'required', 'action_group' => 'required', 'name' => 'required']);
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $requestLab->visit_id);

            $addActionLis = addEditOrderLis($visits[0], $requestLab, $request->action_id, $request->name);

            if($addActionLis['metaData']['code'] == '200' || $addActionLis['metaData']['message'] == 'Pemeriksaan belum mapping' || $addActionLis['metaData']['message'] == 'Penambahan berhasil, terdapat pemeriksaan belum mapping') {
                $actionLabs = ActionLab::where('request_lab_id', $requestLab->id)->get();
    
                $data = [];
                foreach ($actionLabs as $key => $action) {
                    $data[] = $action->action_id;
                }
                
                foreach ($request->action_id as $key => $action_id) {
                    info($action_id);
                    if(!in_array($action_id, $data)) {
                        ActionLab::create([
                            'request_lab_id' => $requestLab->id,
                            'action_id' => $action_id,
                            'action_group_id' => $request->action_group_id[$key],
                            'action_group' => $request->action_group[$key],
                            'name' => $request->name[$key],
                            'status' => 'unfinish',
                            'is_add' => 1
                        ]);
                    }
    
                    if(in_array($action_id, $data)) {
                        $requestLab->actionLabs->where('action_id', $action_id)->first()->update([
                            'is_add' => 1
                        ]);
                    }
                }
    
                ActivityLog::create([
                    'user_name' => auth()->user()->name,
                    'user_role' => auth()->user()->roles->first()->name,
                    'visit_id' => $requestLab->visit_id,
                    'unique_id' => $requestLab->unique_id,
                    'request_id' => $requestLab->id,
                    'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Laboratorium.',
                    'type' => 'Laboratorium',
                    'action' => 'Mengubah'
                ]);
    
                $countAction = count($request->action_id);
                $countComment =  $requestLab->commentLabs->count();
                $requestLab->update([
                    'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $addActionLis['metaData']['message']
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $addActionLis['metaData']['code'] == '200' ? 'Penambahan Berhasil.' : $addActionLis['metaData']['message'],
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add action lab.',
            ], 500);
        }
    }
    
    /**
     * update nomor order by
     *
     * @return void
     */
    public function updateOrderMeasure(Request $request, RequestLab $requestLab, ActionLab $actionLab)
    {
        $data = $request->validate(['order_number' => 'required']);

        if($requestLab->actionLabs->where('order_number', $request->order_number)->where('id', '!=', $actionLab->id)->where('is_add', 1)->count() > 0) {
            return response()->json([
                'message' => 'Order number has been used.',
            ], 422);
        }

        try {
            $actionLab->update($data);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestLab->visit_id,
                'unique_id' => $requestLab->unique_id,
                'request_id' => $requestLab->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Laboratorium.',
                'type' => 'Laboratorium',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action lab sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action lab.',
            ], 500);
        }
    }
    
    /**
     * Hapus tindakan tetapi hanya mengganti statusnya menjadi false
     *
     * @return void
     */
    public function deleteMeasure(Request $request,RequestLab $requestLab, ActionLab $actionLab)
    {
        try {
            $header = $this->headerListing();
            $visits = Http::withHeaders(['x-tstamp' => $header['timestamp'], 'x-sign' => $header['sign']])->get(''.env('VISIT_URL').'/trx/kunjungan?kode=' . $requestLab->visit_id);

            $subActionLis = subEditOrderLis($visits[0], $requestLab, $actionLab);

            if($subActionLis['metaData']['code'] == '200' || $subActionLis['metaData']['message'] == 'Pemeriksaan belum mapping' || $subActionLis['metaData']['message'] == 'Pengurangan berhasil, terdapat pemeriksaan belum mapping') {
                $actionLab->update([
                    'is_add' => 0
                ]);
    
                $countAction = $requestLab->actionLabs->where('is_add', 1)->count();
                $countComment =  $requestLab->commentLabs->count();
                $requestLab->update([
                    'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
                ]);
    
                ActivityLog::create([
                    'user_name' => auth()->user()->name,
                    'user_role' => auth()->user()->roles->first()->name,
                    'visit_id' => $requestLab->visit_id,
                    'unique_id' => $requestLab->unique_id,
                    'request_id' => $requestLab->id,
                    'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Laboratorium.',
                    'type' => 'Laboratorium',
                    'action' => 'Mengubah'
                ]);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $subActionLis['metaData']['message']
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $subActionLis['metaData']['code'] == '200' ? 'Pengurangan Berhasil.' : $subActionLis['metaData']['message'],
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action lab.',
            ], 500);
        }
    }
}
