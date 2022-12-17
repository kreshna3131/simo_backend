<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActionRad;
use App\Models\ActivityLog;
use App\Models\RequestRad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActionRadController extends Controller
{    
    /**
     * Listing tindakan permintaan radiologi
     *
     * @param  mixed $request
     * @return void
     */
    public function listingMeasure(Request $request, RequestRad $requestRad)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $order_by = $request->order_by ? $request->order_by : 'order_number';
            $order_dir = $request->order_dir ? $request->order_dir : 'asc';
            $currentPage = $request->get('page', 1);
            $actionRads = ActionRad::select(
                'action_rads.id as id', 
                'measure_rads.name as action_group', 
                'sub_measure_rads.name as name',
                'action_rads.order_number as order_number',
                'action_rads.attachment_count as attachment_count',
                'action_rads.status as status',
            )
            ->leftJoin("measure_rads", "action_rads.action_group_id", "=", "measure_rads.id")
            ->leftJoin("sub_measure_rads", "action_rads.action_id", "=", "sub_measure_rads.id")
            ->where('action_rads.request_rad_id', $requestRad->id)
            ->where('action_rads.is_add', 1)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function($query) use ($request) {
                    $query->where('measure_rads.name', 'like', '%' . $request->search . '%')
                            ->orwhere('sub_measure_rads.name', 'like', '%' . $request->search . '%');
                });
            })
            ->when($order_by, function ($query) use($order_by, $order_dir) {
                if($order_by == 'action_group') {
                    $query->orderBy('sub_measure_rads.name', $order_dir);
                }
                if($order_by == 'name') {
                    $query->orderBy('measure_rads.name', $order_dir);
                }
                if($order_by == 'order_number') {
                    $query->orderBy('action_rads.order_number', $order_dir);
                }
            });
            
            $actionRadPaginate = (new CustomPaginator(
                $actionRads->clone()->forPage($currentPage, $itemPerpage)->get(),
                $actionRads->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/radiology/'.$requestRad->id.'/measure/listing');

            return response()->json(array_merge(
                $actionRadPaginate->toArray(), 
                ['not_serve_count' => $actionRads->clone()->where('action_rads.status', 'unfinish')->count()]
            ));
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve mesaure rad.',
            ], 500);
        }
    }

    /**
     * listing tindakan untuk tambah tindakan
     *
     * @param  mixed $requestLab
     * @return void
     */
    public function listingMeasureForUpdate(RequestRad $requestRad)
    {
        try {
            $actionRads = ActionRad::query()
                ->where('request_rad_id', $requestRad->id)
                ->where('is_add', 1)
                ->get();
            
            $data = [];
            foreach ($actionRads as $key => $action) {
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
     * Update status tindakan radiologi
     *
     * @param  mixed $request
     * @param  mixed $visitId
     * @param  mixed $requestRad
     * @param  mixed $actionRad
     * @return void
     */
    public function updateStatusMeasure(Request $request, RequestRad $requestRad, ActionRad $actionRad)
    {
        $data_validated = $request->validate(['status' => 'required']);
        try {
            $actionRad->update($data_validated);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Radiologi.',
                'type' => 'Radiologi',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rad.',
            ], 500);
        }
    }

    /**
     * Tambah tindakan radiologi
     *
     * @param  mixed $request
     * @return void
     */
    public function storeMeasure(Request $request, RequestRad $requestRad)
    {
        try {
            $actionRads = ActionRad::where('request_rad_id', $requestRad->id)->get();

            $dataActionId = [];
            $dataActionGroupId = [];
            foreach ($actionRads as $key => $action) {
                $dataActionId[] = $action->action_id;
                $dataActionGroupId[] = $action->action_group_id;
            }

            foreach ($request->action as $key => $action) {
                if(in_array($action['action_id'], $dataActionId) && in_array($action['action_group_id'], $dataActionGroupId)) {
                    info([$action['action_id'], $action['action_group_id']]);
                    optional($actionRads->where('action_id', $action['action_id'])->where('action_group_id', $action['action_group_id'])->first())->update([
                        'is_add' => 1
                    ]);
                }

                $check = $actionRads->where('action_id', $action['action_id'])->where('action_group_id', $action['action_group_id']);
                if(count($check) == 0) {
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
                }
            }

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Radiologi.',
                'type' => 'Radiologi',
                'action' => 'Mengubah'
            ]);

            $countAction = $requestRad->actionRads->where('is_add', 1)->count();
            $countComment =  $requestRad->commentRads->count();
            $requestRad->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad succesfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add action rad.',
            ], 500);
        }
    }
    
    /**
     * Melihat detail tindakan radiology
     *
     * @param  mixed $actionRad
     * @return void
     */
    public function edit(RequestRad $requestRad, ActionRad $actionRad)
    {
        try {
            return response()->json($actionRad);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve action rad.',
            ], 500);
        }
    }

    /**
     * update nomor order by
     *
     * @return void
     */
    public function updateOrderMeasure(Request $request, RequestRad $requestRad, ActionRad $actionRad)
    {
        $data = $request->validate(['order_number' => 'required']);

        if($requestRad->actionRads->where('order_number', $request->order_number)->where('id', '!=', $actionRad->id)->count() > 0) {
            return response()->json([
                'message' => 'Order number has been used.',
            ], 422);
        }

        try {
            $actionRad->update($data);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Radiologi.',
                'type' => 'Radiologi',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rad.',
            ], 500);
        }
    }

    /**
     * update hasil per tindakan radiologi
     *
     * @param  mixed $request
     * @param  mixed $requestLab
     * @param  mixed $actionLab
     * @return void
     */
    public function updateResultMeasure(Request $request, RequestRad $requestRad, ActionRad $actionRad)
    {
        $data = $request->validate(['result' => 'required', 'status' => 'required']);
        try {
            $actionRad->update($data);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Mengisi hasil tindakan pada permintaan Radiologi.',
                'type' => 'Radiologi',
                'action' => 'Mengisi'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rad.',
            ], 500);
        }
    }

    /**
     * Hapus tindakan tetapi hanya mengganti statusnya menjadi false
     *
     * @return void
     */
    public function deleteMeasure(Request $request,RequestRad $requestRad, ActionRad $actionRad)
    {
        try {
            $actionRad->update([
                'is_add' => 0
            ]);

            $countAction = $requestRad->actionRads->where('is_add', 1)->count();
            $countComment =  $requestRad->commentRads->where('is_add', 1)->count();
            $requestRad->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Radiologi.',
                'type' => 'Radiologi',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad successfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rad.',
            ], 500);
        }
    }
}
