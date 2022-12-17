<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RehabMedic;
use Illuminate\Http\Request;

class RehabMedicController extends Controller
{    
    // /**
    //  * listing rehab medic
    //  *
    //  * @param  mixed $request
    //  * @param  mixed $visitId
    //  * @return void
    //  */
    // public function listing(Request $request, $visitId)
    // {
    //     try {
    //         $itemPerPage = $request->pagination ? $request->pagination : 10;
    //         $currentPage = $request->get('page', 1);
    //         $rehabs = RehabMedic::query()
    //             ->where('visit_id', $visitId)
    //             ->when($request->filled('search'), function ($query) use ($request) {
    //                 $date = searchDate($request->search, 'd F Y', 'Y-m-d');
    //                 $query->where(function ($query) use($request, $date) {
    //                     $query->where("rehab_id", "like", "%{$request->search}%")
    //                         ->orWhere("unique_id", "like", "%{$request->search}%")
    //                         ->orWhere("rehab_group_id", "like", "%{$request->search}%")
    //                         ->orWhere("rehab_group", "like", "%{$request->search}%")
    //                         ->orWhere("name", "like", "%{$request->search}%")
    //                         ->orWhere("created_by", "like", "%{$request->search}%");
    //                     if($date != 'date invalid') {
    //                         $query->orWhere("created_at", "like", "%{$date}%");
    //                     }
    //                 });
    //             })
    //             ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

    //         $rehabPaginate = (new CustomPaginator(
    //             $rehabs->clone()->forPage($currentPage, $itemPerPage)->get(),
    //             $rehabs->clone()->count(),
    //             $itemPerPage,
    //             $currentPage
    //         ))
    //             ->withQueryString()
    //             ->withPath(env('APP_URL').'/visit/'.$visitId.'/rehab-medic/listing');

    //         return response()->json($rehabPaginate);
    //     } 
        
    //     catch (\Throwable $th) {
    //         info($th->getMessage());
    //         return response()->json([
    //             'status' => 'failed',
    //             'message' => 'Failed retrieve rehab medic'
    //         ], 500);
    //     }
    // }
    
    // /**
    //  * Menambahkan rehab medic 
    //  *
    //  * @param  mixed $request
    //  * @param  mixed $visitId
    //  * @return void
    //  */
    // public function store(Request $request, $visitId)
    // {
    //     try {
    //         $lastTodayRehab = RehabMedic::query()->today()->latest()->first();
    //         $unique_id = RehabMedic::generateUniqueId($lastTodayRehab);
    //         info($unique_id);

    //         $rehabMedic = [];
    //         foreach ($request->rehab_id as $key => $rehab) {
    //             RehabMedic::create([
    //                 'unique_id' => $unique_id,
    //                 'user_id' => auth()->user()->id,
    //                 'visit_id' => $visitId,
    //                 'rehab_id' => $request->rehab_id[$key],
    //                 'rehab_group_id' => $request->rehab_group_id[$key],
    //                 'rehab_group' => $request->rehab_group[$key],
    //                 'name' => $request->name[$key],
    //                 'created_by' => auth()->user()->name,
    //                 'status' => 'waiting'
    //             ]);
    //             $rehabMedic[] = $request->name[$key];
    //         }

    //         ActivityLog::create([
    //             'user_name' => auth()->user()->name,
    //             'user_role' => auth()->user()->roles->first()->name,
    //             'unique_id' => $unique_id,
    //             'visit_id' => $visitId,
    //             'note' => 'Membuat permintaan rehab medic untuk tindakan '.implode(', ', $rehabMedic).'.',
    //             'type' => 'Rehab Medic',
    //             'action' => 'Membuat'
    //         ]);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Rehab medic sucessfully added.',
    //         ]);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         info($th);
    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => 'Failed to add rehab medic'
    //         ], 500);
    //     }
    // }
    
    // /**
    //  * Untuk update status rehab medic
    //  *
    //  * @param  mixed $request
    //  * @param  mixed $rehabMedic
    //  * @return void
    //  */
    // public function updateStatus(Request $request, RehabMedic $rehabMedic)
    // {
    //     $data_validated = $request->validate(['status' => 'required']);
    //     try {
    //         $rehabMedic->update($data_validated);

    //         ActivityLog::create([
    //             'user_name' => auth()->user()->name,
    //             'user_role' => auth()->user()->roles->first()->name,
    //             'unique_id' => $rehabMedic->unique_id,
    //             'visit_id' => $rehabMedic->visit_id,
    //             'note' => 'Mengubah status permintaan rehab medic menjadi '. $rehabMedic->logStatus($data_validated['status']),
    //             'type' => 'Rehab Medic',
    //             'action' => 'Mengubah'
    //         ]);

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Rehab medic sucessfully updated.',
    //         ]);
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         info($th);
    //         return response()->json([
    //             'status'  => 'failed',
    //             'message' => 'Failed to update rehab medic'
    //         ], 500);
    //     }
    // }
}
