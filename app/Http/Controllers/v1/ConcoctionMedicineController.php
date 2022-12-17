<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommentRecipe;
use App\Models\Concoction;
use App\Models\ConcoctionMedicine;
use App\Models\NonConcoction;
use App\Models\Recipe;
use Illuminate\Http\Request;

class ConcoctionMedicineController extends Controller
{    
    /**
     * listing obat racikan
     *
     * @param  mixed $request
     * @param  mixed $concoction
     * @return void
     */
    public function listing(Request $request, Concoction $concoction)
    {
       try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $medicines = ConcoctionMedicine::query()
                ->where('concoction_id', $concoction->id)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where("name", "like", "%{$request->search}%")
                        ->orWhere("unit", "like", "%{$request->search}%")
                        ->orWhere("dose", "like", "%{$request->search}%")
                        ->orWhere("strength", "like", "%{$request->search}%");
                    
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $medicinePaginate = (new CustomPaginator(
                $medicines->clone()->forPage($currentPage, $itemPerPage)->get(),
                $medicines->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->withQueryString()
                ->withPath(env('APP_URL').'/visit/recipe/concoction/'.$concoction->id.'/listing-medicine');

            return response()->json($medicinePaginate);
       } catch (\Throwable $th) {
        //throw $th;
        info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve concoction medicines.'
            ], 500);
       }
    }
    
    /**
     * Menambah obat racikan
     *
     * @param  mixed $request
     * @param  mixed $concoction
     * @return void
     */
    public function store(Request $request, Recipe $recipe, Concoction $concoction)
    {
        $validated_data = $request->validate(['medicine_id' => 'required', 'name' => 'required', 'unit' => 'required', 'dose' => 'required', 'strength' => 'required']);
        try {
            $concoction->concoctionMedicines()->create($validated_data);

            $racikan = 0;
            foreach ($recipe->concoctions as $key => $concoctions) {
                $racikan = $racikan + $concoctions->concoctionMedicines()->count();
            }
            $nonracikan = NonConcoction::where('recipe_id', $recipe->id)->get()->count();
            $comment = CommentRecipe::where('recipe_id', $recipe->id)->get()->count();
            $concoction->recipe->update([
                'info' => ''.$racikan + $nonracikan.' Obat dan '.$comment.' Komentar'
            ]
            );

            $concoction->update([
                'medicine_count' => $concoction->concoctionMedicines()->count()
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Melakukan tambah obat pada jenis racikan.',
                'type' => 'Resep',
                'action' => 'Membuat'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Concoction medicine sucessfully added.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add concoction.'
            ], 500);
        }
    }
    
    /**
     * Melihat data detail obat racikan
     *
     * @param  mixed $concoctionMedicine
     * @return void
     */
    public function edit(ConcoctionMedicine $concoctionMedicine)
    {
        try {
            return response()->json($concoctionMedicine);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retireve concoction.'
            ], 500);
        }
    }

    /**
     * Mengubah obat racikan
     *
     * @param  mixed $request
     * @param  mixed $concoction
     * @return void
     */
    public function update(Request $request,Recipe $recipe, Concoction $concoction, ConcoctionMedicine $concoctionMedicine)
    {
        $validated_data = $request->validate(['name' => 'required', 'unit' => 'required', 'dose' => 'required', 'strength' => 'required']);
        try {
            $concoctionMedicine->update($validated_data);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Melakukan perubahan obat pada permintaan resep racikan.',
                'type' => 'Resep',
                'action' => 'Mengubah'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Concoction medicine sucessfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update concoction.'
            ], 500);
        }
    }
    
    /**
     * Menghapus obat racikan
     *
     * @param  mixed $concoctionMedicine
     * @return void
     */
    public function destroy(Recipe $recipe, Concoction $concoction, ConcoctionMedicine $concoctionMedicine)
    {
        try {
            $concoctionMedicine->delete();

            $racikan = 0;
            foreach ($recipe->concoctions as $key => $concoctions) {
                $racikan = $racikan + $concoctions->concoctionMedicines()->count();
            }
            $nonracikan = NonConcoction::where('recipe_id', $recipe->id)->get()->count();
            $comment = CommentRecipe::where('recipe_id', $recipe->id)->get()->count();
            $concoction->recipe->update([
                'info' => ''.$racikan + $nonracikan.' Obat dan '.$comment.' Komentar'
            ]
            );

            $concoction->update([
                'medicine_count' => $concoction->concoctionMedicines()->count()
            ]);

            ActivityLog::create([
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name,
                'unique_id' => $recipe->unique_id,
                'request_id' => $recipe->id,
                'visit_id' => $recipe->visit_id,
                'note' => 'Menghapus obat pada racikan resep.',
                'type' => 'Resep',
                'action' => 'Menghapus'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Concoction medicine sucessfully deleted.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to delete concoction.'
            ], 500);
        }
    }
}
