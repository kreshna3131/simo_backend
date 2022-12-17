<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentLab;
use App\Models\RequestLab;
use Illuminate\Http\Request;

class CommentLabController extends Controller
{    
    /**
     * Menampilkan comment per permintaan laboratorium
     *
     * @param  mixed $visitId
     * @param  mixed $requestLab
     * @return void
     */
    public function showComment(RequestLab $requestLab)
    {
        try {
            $comments = CommentLab::where('request_lab_id', $requestLab->id)->orderBy('created_at', 'desc')->get();
            if(str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') !== FALSE) {
                $requestLab->update([
                    'is_read_lab' => 1, 
                ]);
            }
            if (str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') === FALSE) {
                $requestLab->update([
                    'is_read_doc' => 1, 
                ]);
            }

            return response()->json($comments);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve comment lab.',
            ], 500);
        }
    }
    
    /**
     * Tambah usulan atau komentar
     *
     * @param  mixed $request
     * @param  mixed $requestLab
     * @return void
     */
    public function storeComment(Request $request, RequestLab $requestLab)
    {
        $request->validate(['message' => 'required']);
        try {
            CommentLab::create([
                'request_lab_id' => $requestLab->id,
                'user_id' => auth()->user()->id,
                'message' => $request->message,
                'is_read_lab' => str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') === FALSE ? '1' : '0', 
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $requestLab->unique_id,
                'note' => 'Mengusulkan perubahan tindakan pada permintaan Laboratorium.',
                'action' => 'Mengusulkan'
            ]);

            $countAction = $requestLab->actionLabs->count();
            $countComment =  $requestLab->commentLabs->count();
            $requestLab->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar',
                'is_read_lab' => str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'lab') === FALSE ? '1' : '0', 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment lab sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add comment lab.',
            ], 500);
        }
    }
}
