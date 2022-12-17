<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentRecipe;
use App\Models\ConcoctionMedicine;
use App\Models\NonConcoction;
use App\Models\Recipe;
use Illuminate\Http\Request;

class NonConcoctionController extends Controller
{    
    /**
     * Listing non racikan
     *
     * @param  mixed $request
     * @param  mixed $recipe
     * @return void
     */
    public function listing(Request $request, Recipe $recipe)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
                $currentPage = $request->get('page', 1);
                $medicines = NonConcoction::query()
                    ->where('recipe_id', $recipe->id)
                    ->when($request->filled('search'), function ($query) use ($request) {
                        $query->where("medicine_name", "like", "%{$request->search}%")
                            ->orWhere("medicine_unit", "like", "%{$request->search}%")
                            ->orWhere("medicine_use_time", "like", "%{$request->search}%")
                            ->orWhere("medicine_suggestion_use", "like", "%{$request->search}%")
                            ->orWhere("medicine_note", "like", "%{$request->search}%")
                            ->orWhere("medicine_quantity", "like", "%{$request->search}%");
                        
                    })
                    ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
    
                $medicinePaginate = (new CustomPaginator(
                    $medicines->clone()->forPage($currentPage, $itemPerPage)->get(),
                    $medicines->clone()->count(),
                    $itemPerPage,
                    $currentPage
                ))
                    ->withQueryString()
                    ->withPath(env('APP_URL').'/visit/recipe/'.$recipe->id.'/non-concoction/listing');
    
                return response()->json($medicinePaginate);
           } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve non concoction medicines.'
            ], 500);
           }
    }
    
    /**
     * Tambah data non racikan
     *
     * @param  mixed $request
     * @param  mixed $recipe
     * @return void
     */
    public function store(Request $request, Recipe $recipe)
    {
        $validated_data = $request->validate(['medicine_id' => 'required', 'medicine_name' => 'required', 'medicine_unit' => 'required', 'medicine_use_time' => 'required', 'medicine_suggestion_use' => 'required', 'medicine_quantity' => 'required', 'medicine_note' => 'nullable']);
        try {
            $nonConcoction = $recipe->nonConcoctions()->create(collect($validated_data)->except('medicine_use_time')->toArray());

            $nonConcoction->update([
                'medicine_use_time' => implode(', ', $request->medicine_use_time)
            ]);

            $racikan = 0;
            foreach ($recipe->concoctions as $key => $concoctions) {
                $racikan = $racikan + $concoctions->concoctionMedicines()->count();
            }
            $nonracikan = NonConcoction::where('recipe_id', $recipe->id)->get()->count();
            $comment = CommentRecipe::where('recipe_id', $recipe->id)->get()->count();
            $recipe->update([
                'info' => ''.$racikan + $nonracikan.' Obat dan '.$comment.' Komentar'
            ]
            );

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Melakukan penambahan obat pada resep non racikan.',
                'type' => 'Resep',
                'action' => 'Membuat'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Non concoction medicine sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add non concoction medicine.'
            ], 500);
        }
    }
    
    /**
     * Lihat detail data non racikan
     *
     * @param  mixed $nonConcoction
     * @return void
     */
    public function edit(NonConcoction $nonConcoction)
    {
        try {
            return response()->json($nonConcoction);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retireve non concoction medicine.'
            ], 500);
        }
    }
    
    /**
     * update data non racikan
     *
     * @param  mixed $request
     * @param  mixed $nonConcoction
     * @return void
     */
    public function update(Request $request, NonConcoction $nonConcoction)
    {
        $validated_data = $request->validate(['medicine_name' => 'required', 'medicine_unit' => 'required', 'medicine_use_time' => 'required', 'medicine_suggestion_use' => 'required', 'medicine_quantity' => 'required', 'medicine_note' => 'nullable']);
        try {
            $nonConcoction->update(collect($validated_data)->except('medicine_use_time')->toArray());

            $nonConcoction->update([
                'medicine_use_time' => implode(', ', $request->medicine_use_time)
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $nonConcoction->recipe->unique_id,
                'request_id' => $nonConcoction->recipe->id,
                'visit_id' => $nonConcoction->recipe->visit_id,
                'note' => 'Melakukan perubahan di permintaan resep jenis non racikan.',
                'type' => 'Resep',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Non concoction medicine sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update non concoction medicine.'
            ], 500);
        }
    }
    
    /**
     * Hapus data non racikan
     *
     * @param  mixed $nonConcoction
     * @return void
     */
    public function destroy(Recipe $recipe, NonConcoction $nonConcoction)
    {
        try {
            $nonConcoction->delete();

            $racikan = 0;
            foreach ($recipe->concoctions as $key => $concoctions) {
                $racikan = $racikan + $concoctions->concoctionMedicines()->count();
            }
            $nonracikan = NonConcoction::where('recipe_id', $recipe->id)->get()->count();
            $comment = CommentRecipe::where('recipe_id', $recipe->id)->get()->count();
            $recipe->update([
                'info' => ''.$racikan + $nonracikan.' Obat dan '.$comment.' Komentar'
            ]
            );

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Menghapus obat non racikan.',
                'type' => 'Resep',
                'action' => 'Menghapus'
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Non concoction medicine sucessfully deleted.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to delete non concoction medicine.'
            ], 500);
        }
    }
}
