<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{    
    /**
     * listing notifikasi
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $notifications = Notification::query()
                ->where('notifiable_id', auth()->user()->id);

            $notificationPaginate = (new CustomPaginator(
                $notifications->clone()->forPage($currentPage, $itemPerPage)->get(),
                $notifications->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/notification');

            return response()->json($notificationPaginate);
        }

        catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive notification.',
            ], 500);
        }
    }
    
    /**
     * Menghapus database saat read notifikasi
     *
     * @param  mixed $notification
     * @return void
     */
    public function readNotification($notificationId)
    {
        try {
            $notification = Notification::where('id', $notificationId)->first();
            $notification->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification successfully delete'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed delete notification.',
            ], 500);
        }
    }
}
