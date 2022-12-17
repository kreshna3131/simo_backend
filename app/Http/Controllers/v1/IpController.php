<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\BannedIp;
use Illuminate\Http\Request;

class IpController extends Controller
{
    /**
     * Function melihat list ip yang dibanned  
     *
     * @return void
     */
    public function listIp()
    {
        try {
            $ip = BannedIp::all();
            
            return response()->json($ip);
        } catch (\Throwable $th) {
            info($th);

            return response()->json([
                'status' => 'failed',
                'message' => 'failed retrieve ip'
            ], 500);
        }
    }
}
