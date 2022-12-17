<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentRehab;
use App\Models\RequestRehab;
use Illuminate\Http\Request;

class CommentRehabController extends Controller
{
    /**
     * Menampilkan comment per permintaan laboratorium
     *
     * @param  mixed $visitId
     * @param  mixed $requestRehab
     * @return void
     */
    public function showComment(RequestRehab $requestRehab)
    {
        try {
            $comments = CommentRehab::where('request_rehab_id', $requestRehab->id)->orderBy('created_at', 'desc')->get();
            if(str_contains(strtolower(auth()->user()->roles->first()->name), 'rehab') !== FALSE) {
                $requestRehab->update([
                    'is_read_rehab' => 1, 
                ]);
            }
            if (str_contains(strtolower(auth()->user()->roles->first()->name), 'rehab') === FALSE) {
                $requestRehab->update([
                    'is_read_doc' => 1, 
                ]);
            }

            return response()->json($comments);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve comment rehab.',
            ], 500);
        }
    }
    
    /**
     * Tambah usulan atau komentar
     *
     * @param  mixed $request
     * @param  mixed $requestRehab
     * @return void
     */
    public function storeComment(Request $request, RequestRehab $requestRehab)
    {
        $request->validate(['message' => 'required']);
        try {
            CommentRehab::create([
                'request_rehab_id' => $requestRehab->id,
                'user_id' => auth()->user()->id,
                'message' => $request->message,
                'is_read_rehab' => str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') === FALSE ? '1' : '0', 
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $requestRehab->unique_id,
                'note' => 'Mengusulkan perubahan tindakan pada permintaan Rehab.',
                'action' => 'Mengusulkan'
            ]);

            $countAction = $requestRehab->actionRehabs->count();
            $countComment =  $requestRehab->commentRehabs->count();
            $requestRehab->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar',
                'is_read_rehab' => str_contains(strtolower(auth()->user()->roles->first()->name), 'rehab') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'rehab') === FALSE ? '1' : '0', 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment rehab sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add comment rehab.',
            ], 500);
        }
    }
}
