<?php

namespace App\Http\Controllers\v1;

use App\Models\ActionRehab;
use App\Models\RequestRehab;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Custom\Pagination\CustomPaginator;
use App\Models\ActivityLog;

class ActionRehabController extends Controller
{
    /**
     * Listing Tindakan Rehab Medic per Permintaan
     *
     * @param  RequestRehab $requestRehab
     * @param  Request $requestRehab
     * @return Response
     */
    public function listingMeasure(Request $request, RequestRehab $requestRehab)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $actionRehabs = ActionRehab::query()
                ->where('request_rehab_id', $requestRehab->id)
                ->where('is_add', 1)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('action_id', 'like', '%' . $request->search . '%')
                            ->orWhere('action_group_id', 'like', '%' . $request->search . '%')
                            ->orWhere('action_group', 'like', '%' . $request->search . '%')
                            ->orWhere('name', 'like', '%' . $request->search . '%');
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'order_number', $request->order_dir ? $request->order_dir : 'asc');

            $actionRehabPaginate = (new CustomPaginator(
                $actionRehabs->clone()->forPage($currentPage, $itemPerPage)->get(),
                $actionRehabs->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/rehab/'.$requestRehab->id.'/measure/listing');

            return response()->json(array_merge(
                $actionRehabPaginate->toArray(),
                ['not_serve_count' => $actionRehabs->clone()->where('status', 'unfinish')->count()]
            ));
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive measure rehab'
            ], 500);
        }
    }

    /**
     * Update Status Tindakan Rehab Medic
     *
     * @param  RequestRehab $requestRehab
     * @param  ActionRehab $actionRehab
     * @param  Request $request
     * @return Response
     */
    public function updateStatusMeasure(RequestRehab $requestRehab, ActionRehab $actionRehab, Request $request)
    {
        try {
            $validated = $request->validate(['status' => 'required']);
            $actionRehab->update($validated);
            
            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $requestRehab->unique_id,
                'visit_id' => $requestRehab->visit_id,
                'request_id' => $requestRehab->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Rehab Medic',
                'type' => 'Rehab Medic',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status measure rehab successfully updated'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive measure rehab'
            ], 500);
        }
    }

    /**
     * listing tindakan untuk tambah tindakan
     *
     * @param  mixed $requestLab
     * @return void
     */
    public function listingMeasureForUpdate(RequestRehab $requestRehab)
    {
        try {
            $actionRehabs = ActionRehab::query()
                ->where('request_rehab_id', $requestRehab->id)
                ->where('is_add', 1)
                ->get();
            
            $data = [];
            foreach ($actionRehabs as $key => $action) {
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
     * Tambah tindakan rehab medic
     *
     * @param  mixed $request
     * @return void
     */
    public function storeMeasure(Request $request, RequestRehab $requestRehab)
    {
        $request->validate(['rehab_id' => 'required', 'rehab_group_id' => 'required', 'rehab_group' => 'required', 'name' => 'required']);
        try {
            $actionRehabs = ActionRehab::where('request_rehab_id', $requestRehab->id)->get();

            $data = [];
            foreach ($actionRehabs as $key => $rehab) {
                $data[] = $rehab->action_id;
            }
            
            foreach ($request->rehab_id as $key => $rehab_id) {
                if(!in_array($rehab_id, $data)) {
                    ActionRehab::create([
                        'request_rehab_id' => $requestRehab->id,
                        'action_id' => $rehab_id,
                        'action_group_id' => $request->rehab_group_id[$key],
                        'action_group' => $request->rehab_group[$key],
                        'name' => $request->name[$key],
                        'status' => 'unfinish',
                        'is_add' => 1
                    ]);
                }

                if(in_array($rehab_id, $data)) {
                    $requestRehab->actionRehabs->where('action_id', $rehab_id)->first()->update([
                        'is_add' => 1
                    ]);
                }
            }
            
            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRehab->visit_id,
                'unique_id' => $requestRehab->unique_id,
                'request_id' => $requestRehab->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Rehab Medic.',
                'type' => 'Rehab Medic',
                'action' => 'Mengubah'
            ]);

            $countAction = count($request->rehab_id);
            $countComment =  $requestRehab->commentRehabs->count();
            $requestRehab->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rehab succesfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add action rehab.',
            ], 500);
        }
    }

    /**
     * update nomor order by
     *
     * @return void
     */
    public function updateOrderMeasure(Request $request, RequestRehab $requestRehab, ActionRehab $actionRehab)
    {
        $data = $request->validate(['order_number' => 'required']);

        if($requestRehab->actionRehabs->where('order_number', $request->order_number)->where('id', '!=', $actionRehab->id)->where('is_add', 1)->count() > 0) {
            return response()->json([
                'message' => 'Order number has been used.',
            ], 422);
        }

        try {
            $actionRehab->update($data);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRehab->visit_id,
                'unique_id' => $requestRehab->unique_id,
                'request_id' => $requestRehab->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Rehab Medic.',
                'type' => 'Rehab Medic',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rehab sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rehab.',
            ], 500);
        }
    }

    /**
     * Hapus tindakan tetapi hanya mengganti statusnya menjadi false
     *
     * @return void
     */
    public function deleteMeasure(Request $request, RequestRehab $requestRehab, ActionRehab $actionRehab)
    {
        try {
            $actionRehab->update([
                'is_add' => 0
            ]);

            $countAction = $requestRehab->actionRehabs->where('is_add', 1)->count();
            $countComment =  $requestRehab->commentRehabs->where('is_add', 1)->count();
            $requestRehab->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar'
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRehab->visit_id,
                'unique_id' => $requestRehab->unique_id,
                'request_id' => $requestRehab->id,
                'note' => 'Mengubah atau memperbaiki tindakan pada permintaan Rehab Medic.',
                'type' => 'Rehab Medic',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rehab successfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rehab.',
            ], 500);
        }
    }
}
