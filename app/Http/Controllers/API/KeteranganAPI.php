<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AlaAyuningDewasa;
use App\Models\HariRaya;
use App\Models\User;
use Illuminate\Http\Request;

class KeteranganAPI extends Controller
{
    private function validasiKeterangan($api_key)
    {
        $user = User::where('api_key', $api_key)->first();

        if (!$user) {
            $valid = false;
        } else {
            $valid = true;
        }

        return $valid;
    }

    public function keteranganHariRaya(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = HariRaya::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganAlaAyuningDewasa(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = AlaAyuningDewasa::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }
    
}
