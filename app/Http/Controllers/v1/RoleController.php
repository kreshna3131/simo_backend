<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\RoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class RoleController extends Controller
{
    /**
     * Function permission ACL 
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat role|tambah role|ubah role|hapus role', ['only' => ['listing', 'store', 'edit', 'update', 'destroy']]);
         $this->middleware('permission:tambah role', ['only' => ['store']]);
         $this->middleware('permission:ubah role', ['only' => ['edit', 'update']]);
         $this->middleware('permission:hapus role', ['only' => ['destroy']]);
    }

    /**
     * Function untuk melihat listing role 
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $roles = Role::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $date = searchDate($request->search, 'd F Y', 'Y-m-d');
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('note', 'like', '%' . $request->search . '%');
                    if($date != 'date invalid') {
                        $query->orWhere('created_at', 'like', '%' . $date . '%');
                        $query->orWhere('updated_at', 'like', '%' . $date . '%');
                    } 
                });
            })
            ->orderBy($request->order_by ? $request->order_by : 'created_at', $request->order_dir ? $request->order_dir : 'desc');

            info($roles->latest()->clone()->forPage($currentPage, $itemPerpage)->get());
            $rolePaginate = (new CustomPaginator(
                $roles->latest()->clone()->forPage($currentPage, $itemPerpage)->get(),
                $roles->clone()->count(),
                $itemPerpage,
                $currentPage,
            ))
                ->appends($request->all())
                ->withPath(env('APP_URL').'/role/listing');
    
            return response()->json($rolePaginate);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve roles'
            ], 500);
        }

    }

    /**
     * Function untuk melihat list permission  
     *
     * @return void
     */
    public function listPermission()
    {
        try {
            $permissions = Permission::query()
            ->latest()
            ->get()
            ->groupBy(['group_permission', 'group'])
            ->toArray();
    
            return response()->json($permissions);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve roles'
            ], 500);
        }
    }

    /**
     * Function untuk menambah data role
     *
     * @param  mixed $request
     * @return void
     */
    public function store(RoleRequest $request)
    {
        try {
            $role = Role::create($request->validated() + ['guard_name' => 'web']);

            $role->givePermissionTo($request->permission);

            return response()->json([
                'status'  => 'success',
                'message' => 'Role successfully added'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add role'
            ], 500);
        }
    }

    /**
     * Function untuk lihat detail data role 
     *
     * @param  mixed $role
     * @return void
     */
    public function edit(Role $role)
    {
        $permissions = $role->permissions;
        try {
            return response()->json([
                'role' => $role,
                'permission' => $permissions->groupBy(['group_permission', 'group'])->toArray()
            ]);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed retrieved role',
            ], 500);
        }
    }

    /**
     * Function untuk mengubah data role
     *
     * @param  mixed $request
     * @param  mixed $role
     * @return void
     */
    public function update(RoleRequest $request, Role $role)
    {
        // $validated_data = $request->validate(['name' => 'required', 'note' => 'nullable|max:500', 'permission' => ['required', 'array']]);

        try {
            if($role->id == 1) {
                $role->update(collect($request->validated())->except('permission')->toArray() + ['guard_name' => 'web']);
            } else {
                $role->update($request->validated() + ['guard_name' => 'web']);
                $role->syncPermissions($request->permission);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Role successfully updated'
            ]);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update role'
            ], 500);
        }
    }

    /**
     * Function untuk menghapus data role 
     *
     * @param  mixed $role
     * @return void
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Role successfully deleted',
            ]);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to delete role'
            ], 500);
        }
    }
}
