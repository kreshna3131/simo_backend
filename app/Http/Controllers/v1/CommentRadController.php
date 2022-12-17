<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentRad;
use App\Models\RequestRad;
use Illuminate\Http\Request;

class CommentRadController extends Controller
{
    /**
     * Menampilkan comment per permintaan radiologi
     *
     * @param  mixed $visitId
     * @param  mixed $requestRad
     * @return void
     */
    public function showComment(RequestRad $requestRad)
    {
        try {
            $comments = CommentRad::where('request_rad_id', $requestRad->id)->orderBy('created_at', 'desc')->get();
            if(str_contains(strtolower(auth()->user()->roles->first()->name), 'rad') !== FALSE) {
                $requestRad->update([
                    'is_read_rad' => 1, 
                ]);
            }
            if (str_contains(strtolower(auth()->user()->roles->first()->name), 'rad') === FALSE) {
                $requestRad->update([
                    'is_read_doc' => 1, 
                ]);
            }

            return response()->json($comments);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve comment rad.',
            ], 500);
        }
    }
    
    /**
     * Tambah usulan atau komentar
     *
     * @param  mixed $request
     * @param  mixed $requestRad
     * @return void
     */
    public function storeComment(Request $request, RequestRad $requestRad)
    {
        $request->validate(['message' => 'required']);
        try {
            CommentRad::create([
                'request_rad_id' => $requestRad->id,
                'user_id' => auth()->user()->id,
                'message' => $request->message,
                'is_read_rad' => str_contains(strtolower(auth()->user()->roles->first()->name), 'rad') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'rad') === FALSE ? '1' : '0', 
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $requestRad->unique_id,
                'note' => 'Mengusulkan perubahan tindakan pada permintaan Radiologi.',
                'action' => 'Mengusulkan'
            ]);

            $countAction = $requestRad->actionRads->count();
            $countComment =  $requestRad->commentRads->count();
            $requestRad->update([
                'info' => $countAction. ' Tindakan dan '. $countComment . ' Komentar',
                'is_read_rad' => str_contains(strtolower(auth()->user()->roles->first()->name), 'rad') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'rad') === FALSE ? '1' : '0', 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment rad sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add comment rad.',
            ], 500);
        }
    }
}
