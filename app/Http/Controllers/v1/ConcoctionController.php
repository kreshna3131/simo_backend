<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentRecipe;
use App\Models\Concoction;
use App\Models\NonConcoction;
use App\Models\Recipe;
use Illuminate\Http\Request;

class ConcoctionController extends Controller
{            
    /**
     * listing racikan 
     *
     * @param  mixed $recipe
     * @return void
     */
    public function listing(Request $request, Recipe $recipe)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $concoctions = Concoction::query()
                ->where('recipe_id', $recipe->id)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where("name", "like", "%{$request->search}%")
                        ->orWhere("total", "like", "%{$request->search}%")
                        ->orWhere("use_time", "like", "%{$request->search}%")
                        ->orWhere("medicine_count", "like", "%{$request->search}%")
                        ->orWhere("suggestion_use", "like", "%{$request->search}%")
                        ->orWhere("note", "like", "%{$request->search}%");
                    
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $concoctionPaginate = (new CustomPaginator(
                $concoctions->clone()->forPage($currentPage, $itemPerPage)->get(),
                $concoctions->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/visit/recipe/'.$recipe->id.'/concoction/listing');

            return response()->json($concoctionPaginate);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve concoction'
            ], 500);
        }
    }
    /**
     * Tambah racikan
     *
     * @param  mixed $request
     * @param  mixed $recipe
     * @return void
     */
    public function store(Request $request, Recipe $recipe)
    {
        $validated_data = $request->validate(['name' => 'required', 'total' => 'required', 'use_time' => 'required', 'suggestion_use' => 'required', 'note' => 'nullable']);

        try {
            $concoction = $recipe->concoctions()->create(collect($validated_data)->except('use_time')->toArray());

            $concoction->update([
                'use_time' => implode(', ', $request->use_time),
                'medicine_count' => 0
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Membuat racikan.',
                'type' => 'Resep',
                'action' => 'Membuat'
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Concoction sucessfully added.',
                'id'      => $concoction->id
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add concoction'
            ], 500);
        }
    }

    /**
     * Untuk melihat data detail racikan
     *
     * @param  mixed $recipe
     * @param  mixed $concoction
     * @return void
     */
    public function edit(Recipe $recipe, Concoction $concoction)
    {
        try {
            return response()->json($concoction);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve concoction'
            ], 500);
        }
    }
    
    /**
     * Untuk mengubah data detail racikan
     *
     * @param  mixed $request
     * @param  mixed $recipe
     * @param  mixed $concoction
     * @return void
     */
    public function update(Request $request, Recipe $recipe, Concoction $concoction)
    {
        $validated_data = $request->validate(['name' => 'required', 'total' => 'required', 'use_time' => 'required', 'suggestion_use' => 'required', 'note' => 'nullable']);

        try {
            $concoction->update(collect($validated_data)->except('use_time')->toArray());

            $concoction->update([
                'use_time' => implode(', ', $request->use_time)
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Melakukan perubahan di permintaan resep jenis racikan.',
                'type' => 'Resep',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Concoction sucessfully update.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update concoction'
            ], 500);
        }
    }

    public function destroy(Recipe $recipe, Concoction $concoction)
    {
        try {
            $concoction->delete();

            $racikan = 0;
            foreach ($recipe->concoctions as $key => $concoction) {
                $racikan = $racikan + $concoction->concoctionMedicines()->count();
            }
            $nonracikan = NonConcoction::where('recipe_id', $recipe->id)->get()->count();
            $comment = CommentRecipe::where('recipe_id', $recipe->id)->get()->count();
            $concoction->recipe->update([
                'info' => ''.$racikan + $nonracikan.' Obat dan '.$comment.' Komentar'
            ]
            );

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Menghapus racikan.',
                'type' => 'Resep',
                'action' => 'Menghapus'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Concoction sucessfully deleted.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to delete concoction'
            ], 500);
        }
    }
}
