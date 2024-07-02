<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Middleware\LogApiRequest;
use App\Models\ApiLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogRequestAPI extends Controller
{
    public function lihatLog(Request $request)
    {
        $start = microtime(true);

        $api_key = $request->header('x-api-key');
        if (!$api_key) {
            $response = [
                'pesan' => 'API Key tidak ditemukan',
            ];
            return response()->json($response, 200);
        }

        else {
            $user = User::where('api_key', $api_key)->first();
            if (!$user) {
                $response = [
                    'pesan' => 'API Key tidak valid (user tidak ditemukan)',
                ];
                return response()->json($response, 200);
            }
    
            else {
                $log = ApiLog::all();
        
                $executionTime = microtime(true) - $start;
                $executionTime = number_format($executionTime, 6);
                
                $response = [
                    'pesan' => 'Sukses',
                    'data' => $log,
                    'waktu_eksekusi' => $executionTime,
                ];
        
                return response()->json($response, 200);
            }
        }
    }

    public function lihatLogByUser(Request $request)
    {
        $start = microtime(true);

        $api_key = $request->header('x-api-key');
        if (!$api_key) {
            $response = [
                'pesan' => 'API Key tidak ditemukan',
            ];
            return response()->json($response, 200);
        }

        else {
            $user = User::where('api_key', $api_key)->first();
            if (!$user) {
                $response = [
                    'pesan' => 'API Key tidak valid (user tidak ditemukan)',
                ];
                return response()->json($response, 200);
            }
    
            else {
                $log = ApiLog::where('user_id', $user->id)->get();
        
                $executionTime = microtime(true) - $start;
                $executionTime = number_format($executionTime, 6);
                
                $response = [
                    'pesan' => 'Sukses',
                    'data' => $log,
                    'waktu_eksekusi' => $executionTime,
                ];
        
                return response()->json($response, 200);
            }
        }
    }
}
