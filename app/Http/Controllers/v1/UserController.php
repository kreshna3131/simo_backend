<?php

namespace App\Http\Controllers\v1;

use App\Custom\Pagination\CustomPaginator;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\UserRequest;
use App\Models\Role;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class UserController extends Controller
{
    /**
     * Function untuk permission ACL
     *
     * @return void
     */
    function __construct()
    {
         $this->middleware('permission:lihat pengguna|tambah pengguna|ubah pengguna|hapus pengguna', ['only' => ['listing', 'store', 'edit', 'update', 'destroy']]);
         $this->middleware('permission:tambah pengguna', ['only' => ['store']]);
         $this->middleware('permission:ubah pengguna', ['only' => ['edit', 'update']]);
         $this->middleware('permission:hapus pengguna', ['only' => ['destroy']]);
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
     * Function untuk melihat listing user
     *
     * @param  mixed $request
     * @return void
     */
    public function listing(Request $request)
    {
        try {
            $itemPerpage = $request->pagination ? $request->pagination : 10;
            $currentPage = $request->get('page', 1);
            $users = User::select(
                "users.id", 
                "users.name as user_name", 
                "roles.name as role_name", 
                "specialists.name as specialist_name", 
                "users.email",
                "users.created_at",
                DB::raw('(CASE WHEN tbr_users.blocked = 1 THEN "Inactive" ELSE "Active" END) AS status'))
                ->leftJoin("model_has_roles", "users.id", "=", "model_has_roles.model_id")
                ->leftJoin("roles", "model_has_roles.role_id", "=", "roles.id")
                ->leftJoin("specialists", "users.specialist_id", "specialists.id")
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $date = searchDate($request->search, 'd F Y', 'Y-m-d');
                        $query->where('users.name', 'like', '%' . $request->search . '%');
                        $query->orWhere('roles.name', 'like', '%' . $request->search . '%');
                        $query->orWhere('specialists.name', 'like', '%' . $request->search . '%');
                        $query->orWhere('users.email', 'like', '%' . $request->search . '%');
                        if($date != 'date invalid') {
                            $query->orWhere('users.created_at', 'like', '%' . $date . '%');
                        }
                        $query->when($request->search == 'Active' || $request->search == 'Active', function ($query) {
                            $query->orwhere('blocked', 0);
                        });
                        $query->when($request->search == 'Inactive' || $request->search == 'inactive', function ($query) {
                            $query->orwhere('blocked', 1);
                        });

                    });
                })
                ->when($request->order_by == null, function ($query) use ($request) {
                    $query->orderBy('users.created_at', 'desc');
                })
                ->when($request->filled('order_by'), function ($query) use ($request) {
                    if($request->order_by == 'specialist_name') {
                        $query->orderBy('specialists.name', $request->order_dir);
                    }
                    if($request->order_by == 'role_name') {
                        $query->orderBy('roles.name', $request->order_dir);
                    }
                    if($request->order_by == 'user_name') {
                        $query->orderBy('users.name', $request->order_dir);
                    }
                });
            
            $userPaginate = (new CustomPaginator(
                $users->clone()->forPage($currentPage, $itemPerpage)->get(),
                $users->clone()->count(),
                $itemPerpage,
                $currentPage,
            )) 
            ->appends($request->all())
            ->withPath(env('APP_URL').'/specialist/listing');
    
            return response()->json($userPaginate);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve users'
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
     * Function untuk melihat listing role selain superuser 
     *
     * @return void
     */
    public function listingRole()
    {
        try {
            $roles = Role::where('name', '!=', 'Superuser')->where('name', '!=', 'Tidak punya role')->latest()->get();

            return response()->json($roles);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve roles'
            ], 500);
        }
    }

    /**
     * Function untuk melihat listing role dokter 
     *
     * @return void
     */
    public function listingDokter()
    {
        try {
            $doctors = User::isDoctor()->get();

            return response()->json($doctors);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve doctors'
            ], 500);
        }
    }

    /**
     * Function untuk melihat listing spesialis
     *
     * @return void
     */
    public function listingSpecialist()
    {
        try {
            $specialists = Specialist::latest()->get();

            return response()->json($specialists);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve specialists'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * USER
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create(collect($request->validated())->except('role')->toArray());

            $user->assignRole($request->role);

            info(collect($request->validated()));

            if($request->specialist_id) {
                Specialist::where('id', $request->specialist_id)->update([
                    'doctor_count' => DB::raw('doctor_count + 1'),
                ]);
            }

            $user->sendPasswordCreateNotification();

            return response()->json([
                'status'  => 'success',
                'message' => 'User successfully added'
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to add user'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * USER
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user)
    {
        $user = User::find($user);
        try {
            return response()->json($user);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to retrieve user'
            ], 500);
        }
    }

    /**
     * Function untuk mengirim email reset password
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function sendEmailReset(Request $request, User $user)
    {
        try {
            $token = app('auth.password.broker')->createToken($user);

            RateLimiter::attempt(
                'send-message:' . $user->id,
                $perMinute = 6,
                function () use ($user, $token) {
                    $user->sendPasswordResetNotification($token);
                },
            );

            if (RateLimiter::tooManyAttempts('send-message:' . $user->id, $perMinute = 6)) {
                $seconds = RateLimiter::availableIn('send-message:' . $user->id);

                setcookie('throttle-end-' . $user->id, now()->addSeconds($seconds), time() + $seconds, '/');
                return response()->json([
                    'status'   => 'throttle'
                ], 429);
            }

            return response()->json([
                'status'  => 'success',
            ]);
        } catch (\Exception $e) {
            info($e);
            return response()->json([
                'status'  => 'failed',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        try {
            if($request->specialist_id != $user->specialist_id) {
                Specialist::where('id', $user->specialist_id)->update([
                    'doctor_count' => DB::raw('doctor_count - 1'),
                ]);

                Specialist::where('id', $request->specialist_id)->update([
                    'doctor_count' => DB::raw('doctor_count + 1'),
                ]);
            }

            $user->update(collect($request->validated())->except('role')->toArray());

            $user->syncRoles($request->role);

            if($user->blocked != $request->blocked) {
                if (!$user->blocked ) {
                    Specialist::where('id', $user->specialist_id)->update([
                        'doctor_count' => DB::raw('doctor_count + 1'),
                    ]);
                }
                if ($user->blocked ) {
                    Specialist::where('id', $user->specialist_id)->update([
                        'doctor_count' => DB::raw('doctor_count - 1'),
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully updated',
            ]);
        } catch (\Throwable $th) {
            info($th->getMessage());
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to update user'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            Specialist::where('id', $user->specialist_id)->update([
                'doctor_count' => DB::raw('doctor_count - 1'),
            ]);
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully deleted',
            ]);
        } catch (\Throwable $th) {
            info($th);
            return response()->json([
                'status'  => 'failed',
                'message' => 'Failed to delete user'
            ], 500);
        }
    }
}
