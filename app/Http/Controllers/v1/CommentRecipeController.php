<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentRecipe;
use App\Models\ConcoctionMedicine;
use App\Models\NonConcoction;
use App\Models\Recipe;
use Illuminate\Http\Request;

class CommentRecipeController extends Controller
{
    /**
     * Menampilkan comment per permintaan radiologi
     *
     * @param  mixed $visitId
     * @param  mixed $requestRad
     * @return void
     */
    public function showComment(Recipe $recipe)
    {
        try {
            $comments = CommentRecipe::where('recipe_id', $recipe->id)->orderBy('created_at', 'desc')->get();
            if(str_contains(strtolower(auth()->user()->roles->first()->name), 'apo') !== FALSE) {
                $recipe->update([
                    'is_read_apo' => 1, 
                ]);
            }
            if (str_contains(strtolower(auth()->user()->roles->first()->name), 'apo') === FALSE) {
                $recipe->update([
                    'is_read_doc' => 1, 
                ]);
            }

            return response()->json($comments);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve comment recipe.',
            ], 500);
        }
    }
    
    /**
     * Tambah usulan atau komentar
     *
     * @param  mixed $request
     * @param  mixed $recipe
     * @return void
     */
    public function storeComment(Request $request, Recipe $recipe)
    {
        $request->validate(['message' => 'required']);
        try {
            CommentRecipe::create([
                'recipe_id' => $recipe->id,
                'user_id' => auth()->user()->id,
                'message' => $request->message,
                'is_read_apo' => str_contains(strtolower(auth()->user()->roles->first()->name), 'apo') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'apo') === FALSE ? '1' : '0', 
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'note' => 'Mengusulkan perubahan resep pada E-Resep.',
                'action' => 'Mengusulkan'
            ]);

            $racikan = 0;
            foreach ($recipe->concoctions as $key => $concoction) {
                $racikan = $racikan + $concoction->concoctionMedicines()->count();
            }
            $nonracikan = NonConcoction::where('recipe_id', $recipe->id)->get()->count();
            $comment = CommentRecipe::where('recipe_id', $recipe->id)->get()->count();
            $recipe->update([
                'info' => ''.$racikan + $nonracikan.' Obat dan '.$comment.' Komentar',
                'is_read_apo' => str_contains(strtolower(auth()->user()->roles->first()->name), 'apo') !== FALSE ? '1' : '0', 
                'is_read_doc' => str_contains(strtolower(auth()->user()->roles->first()->name), 'apo') === FALSE ? '1' : '0', 
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Comment recipe sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed add comment recipe.',
            ], 500);
        }
    }
}
