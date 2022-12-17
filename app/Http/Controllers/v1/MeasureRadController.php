<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\MeasureRadRequest;
use App\Models\MeasureRad;
use Illuminate\Http\Request;

class MeasureRadController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat group tindakan|tambah group tindakan|ubah group tindakan|hapus group tindakan', ['only' => ['listing', 'store', 'edit', 'update', 'destroy']]);
         $this->middleware('permission:lihat group tindakan', ['only' => ['listing']]);
         $this->middleware('permission:tambah group tindakan', ['only' => ['store']]);
         $this->middleware('permission:ubah group tindakan', ['only' => ['edit', 'update']]);
         $this->middleware('permission:hapus group tindakan', ['only' => ['destroy']]);
    }

    /**
     * Listing Measure Rads
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response json
     */
    public function listing(Request $request) 
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $measures = MeasureRad::query()
                ->when($request->filled('search'), function ($query) use($request) {
                    $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere(function ($query) use($request, $date) {
                            $query->where('sub_count', 'like', '%' . $request->search . '%');
                            if ($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            }
                        });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $measurePaginate = (new CustomPaginator(
                $measures->clone()->forPage($currentPage, $itemPerPage)->get(),
                $measures->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/master/radiology/group/listing');
                
            return response()->json($measurePaginate);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve measure rads.'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Measure Rads
     * 
     * @param  \Illuminate\Http\MeasureRadRequest  $request
     * @return \Illuminate\Http\Response json
     */
    public function store(MeasureRadRequest $request) 
    {
        try {
            $measure = MeasureRad::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Measure rad successfully added'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to add measure rads'
            ], 500);
        } 
    }

    /**
     * Send detail data for showing an edit page to updating them.
     * Measure Rads
     * 
     * @param  \App\Models\MeasureRad  $measureRad
     * @return \Illuminate\Http\Response json
     */
    public function edit(MeasureRad $measureRad)
    {
        try {
            return response()->json($measureRad);
        }

        catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieve measure rad'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Measure Rads
     * 
     * @param \App\Http\Requests\v1\MeasureRadRequest $request
     * @param  \App\Models\MeasureRad  $measureRad
     * @return \Illuminate\Http\Response json
     */
    public function update(MeasureRadRequest $request, MeasureRad $measureRad)
    {
        try {
            $measureRad->updateGroup($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Measure rad succesfully updated'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to update measure rad',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, MeasureRad $measureRad)
    {
        try {
            $validated = $request->validate(['group' => 'required']);

            $measureRad->delegateGroup($validated);
            $measureRad->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Measure rad successfully deleted'
            ]);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to delete measure rad',
            ], 500);
        }
    }
}
