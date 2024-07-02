<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AlaAyuningDewasa;
use App\Models\EkaJalaSri;
use App\Models\HariRaya;
use App\Models\PancaSudha;
use App\Models\Pancawara;
use App\Models\Pangarasan;
use App\Models\Pratiti;
use App\Models\Saptawara;
use App\Models\User;
use App\Models\Wuku;
use App\Models\Zodiak;
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
    
    public function keteranganEkaJalaSri(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        
        if ($valid) {
            $keterangan = EkaJalaSri::all();
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

    public function keteranganPancaSudha(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = PancaSudha::all();
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

    public function keteranganPangarasan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Pangarasan::all();
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

    public function keteranganPratiti(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Pratiti::all();
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

    public function keteranganZodiak(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Zodiak::all();
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

    public function keteranganPancawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Pancawara::all();
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

    public function keteranganSaptawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Saptawara::all();
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

    public function keteranganWuku(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Wuku::all();
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
