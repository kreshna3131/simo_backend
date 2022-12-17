<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{    
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat activity log', ['only' => ['listing']]);
    }

    /**
     * listing ActivityLog
     *
     * @param  mixed $var
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $date_start = $request->periode ? Carbon::parse($request->periode[0])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $date_end = $request->periode ? Carbon::parse($request->periode[1])->setTimezone('Asia/Jakarta')->format('Y-m-d') : '';
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $logs = ActivityLog::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                        $query->where('user_name', 'like', '%' . $request->search . '%')
                            ->orwhere('user_role', 'like', '%' . $request->search . '%')
                            ->orwhere('note', 'like', '%' . $request->search . '%')
                            ->orwhere('action', 'like', '%' . $request->search . '%')
                            ->orwhere('unique_id', 'like', '%' . $request->search . '%');
                        if($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                        } 
                    });
                })
                ->when($request->filled('periode'), function ($query) use ($date_start, $date_end) {
                    info($date_end);
                    $query->whereBetween('created_at', [$date_start.' 00:00:00', $date_end.' 23:59:59']);
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
            
            $logPaginate = (new CustomPaginator(
                $logs->clone()->forPage($currentPage, $itemPerpage)->get(),
                $logs->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/activity-log/listing');
    
            return response()->json(array_merge(
                $logPaginate->toArray(), 
                ['today_count' => $logs->clone()->whereDate('created_at', Carbon::today())->count()]
            ));
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve logs'
            ], 500);
        }
    }
}
