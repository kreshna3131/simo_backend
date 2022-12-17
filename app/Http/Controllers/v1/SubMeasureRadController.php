<?php

namespace App\Http\Controllers\v1;

use App\Models\MeasureRad;
use Illuminate\Http\Request;
use App\Models\SubMeasureRad;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Custom\Pagination\CustomPaginator;
use App\Http\Requests\v1\SubMeasureRadRequest;

class SubMeasureRadController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat sub group tindakan|tambah sub group tindakan|ubah sub group tindakan|hapus sub group tindakan', ['only' => ['listing', 'store', 'edit', 'update', 'destroy']]);
         $this->middleware('permission:lihat sub group tindakan', ['only' => ['listing']]);
         $this->middleware('permission:tambah sub group tindakan', ['only' => ['store']]);
         $this->middleware('permission:ubah sub group tindakan', ['only' => ['edit', 'update']]);
         $this->middleware('permission:hapus sub group tindakan', ['only' => ['destroy']]);
    }

    /**
     * Listing Sub Measure Rads
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response json
     */
    public function listing(Request $request)
    {
        try {
            $itemPerPage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $subMeasures = SubMeasureRad::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $date = searchDate($request->search, 'd/m/Y', 'Y-m-d');
                    $query->where('name', 'like', '%' . $request->search .'%')
                        ->orWhere(function ($query) use($request, $date) {
                            $query->where('group_name', 'like', '%' . $request->search .'%');
                            if ($date != 'date invalid') {
                                $query->orWhere('created_at', 'like', '%' . $date . '%');
                            }
                        });
                })
                ->when($request->filled('group'), function ($query) use ($request) {
                    $query->whereHas('measureRads', function ($query) use($request) {
                        $query->where('measure_rads.id', $request->group);
                    });
                })
                ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            $subMeasurePaginate = (new CustomPaginator(
                $subMeasures->clone()->forPage($currentPage, $itemPerPage)->get(),
                $subMeasures->clone()->count(),
                $itemPerPage,
                $currentPage
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL') . '/master/radiology/tindakan/listing');

            return response()->json($subMeasurePaginate);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive sub measure rads'
            ], 500);
        }
    }

    /**
     * Listing Measure Rads / Group for dropdown filter
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response json
     */
    public function listingGroup() 
    {
        try {
            $measure = MeasureRad::latest()->get();

            return response()->json($measure);
        }

        catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to retrieve measure rads dropdown'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Measure Rads
     * 
     * @param  \Illuminate\Http\SubMeasureRadRequest  $request
     * @return \Illuminate\Http\Response json
     */
    public function store(SubMeasureRadRequest $request)
    {
        try {
            $subMeasure = SubMeasureRad::create(collect($request->validated())->except('group')->toArray());
            $subMeasure->measureRads()->attach($request->group);

            foreach ($subMeasure->measureRads as $measureRad) {
                $measure[] = $measureRad->name;
            }

            $subMeasure->update([
                'group_name' => implode(', ', $measure)
            ]);

            collect($request->group)->map(function ($group) {
                MeasureRad::where('id', $group)->update([
                    'sub_count' => DB::raw('sub_count + 1')
                ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Measure rad successfully added'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to add sub measure rads'
            ], 500);
        }
    }

    /**
     * Send detail data for showing an edit page to updating them.
     * Measure Rads
     * 
     * @param  \App\Models\SubMeasureRad  $measureRad
     * @return \Illuminate\Http\Response json
     */
    public function edit(SubMeasureRad $subMeasureRad)
    {
        try {
            return response()->json($subMeasureRad);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive sub measure rad'
            ], 500);
        }
    }

    /**
     * Send detail data for showing an edit page to updating them.
     * Measure Rads
     * 
     * @param  \App\Models\SubMeasureRad  $measureRad
     * @return \Illuminate\Http\Response json
     */
    public function editGroup(SubMeasureRad $subMeasureRad)
    {
        try {
            $group = $subMeasureRad->measureRads;

            return response()->json($group);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrive sub measure rad'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * Measure Rads
     * 
     * @param \App\Http\Requests\v1\SubMeasureRadRequest $request
     * @param  \App\Models\SubMeasureRad  $subMeasureRad
     * @return \Illuminate\Http\Response json
     */
    public function update(SubMeasureRadRequest $request, SubMeasureRad $subMeasureRad)
    {
        try {
            foreach ($subMeasureRad->measureRads as $measureRad) {
                $measureRad->update([
                    'sub_count' => $measureRad->sub_count - 1
                ]);
            }

            foreach ($request->group as $group) {
                $model = MeasureRad::find($group);
                $name[] = $model->name;

                $model->update([
                    'sub_count' => $model->sub_count + 1
                ]);
            }

            $subMeasureRad->update(collect($request->validated())->except('group')->toArray() + ['group_name' => null]);
            $subMeasureRad->measureRads()->sync($request->group);
            $subMeasureRad->update([
                'group_name' => implode(', ', $name)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sub measure rad succesfully updated'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to update sub measure rad'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubMeasureRad  $subMeasureRad
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubMeasureRad $subMeasureRad)
    {
        try {
            foreach ($subMeasureRad->measureRads as $measureRad) {
                $measureRad->update([
                    'sub_count' => $measureRad->sub_count - 1
                ]);
            }

            $subMeasureRad->measureRads()->detach();
            $subMeasureRad->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Sub measure rad successfully deleted'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());

            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to delete sub measure rad'
            ], 500);
        }
    }
}
