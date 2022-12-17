<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecialistController extends Controller
{
    /**
     * Function untuk permission ACL 
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat spesialis|tambah spesialis|ubah spesialis|hapus spesialis', ['only' => ['listing', 'store', 'edit', 'update', 'destroy']]);
         $this->middleware('permission:tambah spesialis', ['only' => ['store']]);
         $this->middleware('permission:ubah spesialis', ['only' => ['edit', 'update']]);
         $this->middleware('permission:hapus spesialis', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Function untuk melihat listing spesialis
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $specialists = Specialist::query()
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $date = searchDate($request->search, 'd F Y', 'Y-m-d');
                        $query->where('name', 'like', '%' . $request->search . '%')
                            ->orwhere('doctor_count', 'like', '%' . $request->search . '%');
                        if($date != 'date invalid') {
                            $query->orWhere('created_at', 'like', '%' . $date . '%');
                        } 
                    });
                })
                ->when($request->filled('order_by'), function ($query) use ($request) {
                    $query->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');
                });
            
            $specialistPaginate = (new CustomPaginator(
                $specialists->clone()->forPage($currentPage, $itemPerpage)->get(),
                $specialists->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/specialist/listing');
    
            return response()->json($specialistPaginate);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve specialists'
            ], 500);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * SPESIALIS
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate(['name' => 'required']);

        try {
            $specialist = Specialist::create($validated_data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Specialist successfully added'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add specialist'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Specialist  $specialist
     * @return \Illuminate\Http\Response
     */
    public function show(Specialist $specialist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *  SPESIALIS
     * 
     * @param  \App\Models\Specialist  $specialist
     * @return \Illuminate\Http\Response
     */
    public function edit($specialist)
    {
        $specialist = Specialist::find($specialist);
        try {
            return response()->json($specialist);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve specialist'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * SPESIALIS
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Specialist  $specialist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Specialist $specialist)
    {
        $validated_data = $request->validate(['name' => 'required']);

        try {
            $specialist->update($validated_data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Specialist successfully updated'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update specialist'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * SPESIALIS
     * 
     * @param  \App\Models\Specialist  $specialist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Specialist $specialist)
    {
        try {
            $specialist->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Specialist successfully deleted',
            ]);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to delete specialist'
            ], 500);
        }
    }
}
