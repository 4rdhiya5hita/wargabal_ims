<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidasiAPI;
use App\Models\Acara;
use App\Models\Piodalan;
use App\Models\Pura;
use App\Models\User;
use Illuminate\Http\Request;

class AcaraAPI extends Controller
{
    // buatkan fungsi untuk validasi acara setiap kali terhadi request ke API ini

    private function validasiAcara($api_key)
    {
        $user = User::where('api_key', $api_key)->first();

        if (!$user) {
            $valid = false;
        } else {
            $valid = true;
        }

        return $valid;
    }


    public function buatAcaraPiodalan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            $data = $request->all();
            $acara = Piodalan::create($data);
            return response()->json([
                'pesan' => 'Berhasil menambah data acara piodalan',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function lihatAcaraPiodalan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            $acara = Piodalan::all();
            return response()->json([
                'pesan' => 'Berhasil mengambil data acara piodalan',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function lihatAcaraPiodalanById(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            $data = $request->all();
            $acara = Piodalan::find($data['id']);
            return response()->json([
                'pesan' => 'Berhasil mengambil data acara piodalan',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function ubahAcaraPiodalan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);
        
        if ($valid) {
            $data = $request->all();
            $acara = Piodalan::find($data['id']);
            $acara->update($data);
            return response()->json([
                'pesan' => 'Berhasil mengubah data acara piodalan',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function hapusAcaraPiodalan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            $data = $request->all();
            $acara = Piodalan::find($data['id']);
            $acara->delete();
            return response()->json([
                'pesan' => 'Berhasil menghapus data acara piodalan',
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function lihatPura(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            // $acara = Pura::select('id', 'name', 'address')->get();
            $acara = Pura::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function lihatPuraById(Request $request, $id)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            // $acara = Pura::select('id', 'name', 'address')->get();
            $acara = Pura::find($id);
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function buatAcaraDetail(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            $data = $request->all();
            $acara = Acara::create($data);
            return response()->json([
                'pesan' => 'Berhasil menambah data acara detail',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function lihatAcaraDetail(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);

        if ($valid) {
            $acara = Acara::all();
            return response()->json([
                'pesan' => 'Berhasil mengambil data acara detail',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function lihatAcaraDetailById(Request $request, $id)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);
        
        if ($valid) {
            $acara = Acara::find($id);
            return response()->json([
                'pesan' => 'Berhasil mengambil data acara detail',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function ubahAcaraDetail(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);
        
        if ($valid) {
            $data = $request->all();
            $acara = Acara::find($data['id']);
            $acara->update($data);
            return response()->json([
                'pesan' => 'Berhasil mengubah data acara detail',
                'data' => $acara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function hapusAcaraDetail(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiAcara($api_key);
        
        if ($valid) {
            $data = $request->all();
            $acara = Acara::find($data['id']);
            $acara->delete();
            return response()->json([
                'pesan' => 'Berhasil menghapus data acara detail',
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }
}
