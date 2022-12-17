<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ActionRad;
use App\Models\ActionRadAttch;
use App\Models\ActivityLog;
use App\Models\RequestRad;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;

class ActionRadAttchController extends Controller
{    
    use UploadTrait;

    public function listing(RequestRad $requestRad, ActionRad $actionRad)
    {
        try {
            $attachments = $actionRad->actionRadAttchs;
            $count = $actionRad->actionRadAttchs()->count();

            return response()->json([
                'data' => $attachments,
                'total' => $count
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve action rad attch.',
            ], 500);
        }
    }

    /**
     * Menambahkan foto tiap tindakan radiologi
     *
     * @param  mixed $request
     * @param  mixed $requestRad
     * @param  mixed $actionRa
     * @return void
     */
    public function store(Request $request, RequestRad $requestRad, ActionRad $actionRad)
    {
        $request->validate(['photo' => ['required', 'image', 'max:6048']]);

        if ($actionRad->actionRadAttchs()->count() > 10) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Failed create action rad attch because there are already 10 items.',
            ]);
        }
        try {
            $actionRad->actionRadAttchs()->create([
                'photo_name' => $request->photo_name,
                'photo_path' => $this->uploadFile($request->file('photo')),
            ]);

            $actionRad->update([
                'attachment_count' => $actionRad->actionRadAttchs->count()
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $requestRad->visit_id,
                'unique_id' => $requestRad->unique_id,
                'request_id' => $requestRad->id,
                'note' => 'Menambah lampiran pada tindakan radiologi.',
                'type' => 'Radiologi',
                'action' => 'Menambah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad attch successfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed create action rad attch.',
            ], 500);
        }
    }
    
    /**
     * Lihat data attachment tindakan
     *
     * @param  mixed $actionRad
     * @return void
     */
    public function edit(ActionRadAttch $actionRadAttch)
    {
        try {
            return response()->json($actionRadAttch );
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve action rad attch.',
            ], 500);
        }
    }
    
    /**
     * Mengubah data attachment tindakan
     *
     * @param  mixed $request
     * @param  mixed $requestRad
     * @param  mixed $actionRad
     * @return void
     */
    public function update(Request $request, ActionRadAttch $actionRadAttch)
    {
        $data_validated = $request->validate(['photo_name' => 'required']);
        try {
            $actionRadAttch->update($data_validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad attch successfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update action rad attch.',
            ], 500);
        }
    }
    
    /**
     * Hapus foto hasil tindakan radiologi
     *
     * @param  mixed $actionRadAttch
     * @return void
     */
    public function destroy(ActionRad $actionRad, ActionRadAttch $actionRadAttch)
    {
        try {
            unlink(storage_path('app/public/'.$actionRadAttch->photo_path));
            $actionRadAttch->delete();

            $actionRad->update([
                'attachment_count' => $actionRad->actionRadAttchs->count()
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'visit_id' => $actionRad->requestRad->visit_id,
                'unique_id' => $actionRad->requestRad->unique_id,
                'request_id' => $actionRad->requestRad->id,
                'note' => 'Menghapus lampiran pada tindakan radiologi.',
                'type' => 'Radiologi',
                'action' => 'Menghapus'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Action rad attch successfully deleted.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed delete action rad attch.',
            ], 500);
        }
    }
}
