<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{    
    /**
     * Function permission   
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat profile|ubah profile',['only' => ['edit', 'update']]);
         $this->middleware('permission:lihat profile', ['only' => ['edit']]);
         $this->middleware('permission:ubah profile', ['only' => ['update']]);
    }

    /**
     * profile user
     *
     * @param  mixed $request
     * @return void
     */
    public function profile(Request $request)
    {
        return response()->json($request->user()->loadMissing('roles', 'roles.permissions'));
    }
    
    /**
     * Lihat data profile
     *
     * @param  mixed $request
     * @return void
     */
    public function edit(Request $request)
    {
        return response()->json($request->user()->loadMissing('roles', 'roles.permissions'));
    }
    
    /**
     * update profile
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function update(Request $request)
    {
        $request->validate(['name' => ['required'], 'password' => ['required', 'confirmed', 'min:8']]);
        try {

            auth()->user()->update([
                'name' => $request->name,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile successfully updated.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            info($th);
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed update profile.',
            ], 500);
        }
    }
}
