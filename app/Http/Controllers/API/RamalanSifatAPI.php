<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\WukuController;
use App\Http\Controllers\ZodiakController;
use App\Models\Pancawara;
use App\Models\Saptawara;
use App\Models\User;
use App\Models\Wuku;
use App\Models\Zodiak;
use Illuminate\Http\Request;

class RamalanSifatAPI extends Controller
{
    public function ramalanSifat(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 8;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);
        
        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_lahir = $request->input('tanggal_lahir');

        if ($tanggal_lahir === null) {
            return response()->json([
                'message' => 'Data tanggal lahir tidak boleh kosong'
            ], 400);
        } 
    
        // Validasi format tanggal
        if (!strtotime($tanggal_lahir) || ctype_digit($tanggal_lahir) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_lahir)) {
            return response()->json([
                'message' => 'Data tanggal lahir harus berupa data tanggal yang valid'
            ], 400);
        }

        // Validasi tanggal_lahir tidak boleh lebih dari tanggal sekarang
        if (strtotime($tanggal_lahir) > strtotime(date('Y-m-d'))) {
            return response()->json([
                'message' => 'Data tanggal lahir tidak boleh lebih dari tanggal sekarang'
            ], 400);
        }
    
        $ramalan_sifat = $this->getRamalanSifat($tanggal_lahir);

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $ramalan_sifat,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
        // return view('dashboard.index', compact('kalender'));
    }

    private function getRamalanSifat($tanggal) {
        // dd($get_jejepan);
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $angkaWuku = 70;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $angkaWuku = 33;
        } else {
            $refTanggal = '1992-01-01';
            $angkaWuku = 88;
        }

        // Panggil semua controller yang dibutuhkan
        $wukuController = new WukuController();
        $pancaWaraController = new PancaWaraController_05();
        $saptaWaraController = new SaptaWaraController_07();
        $zodiakController = new ZodiakController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $namaPancawara = $pancaWaraController->getNamaPancawara($pancawara);
        $keteranganPancawara = Pancawara::where('nama', $namaPancawara)->first()->keterangan;

        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $namaSaptawara = $saptaWaraController->getNamaSaptawara($saptawara);
        $keteranganSaptawara = Saptawara::where('nama', $namaSaptawara)->first()->keterangan;

        $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $keteranganWuku = Wuku::where('nama', $namaWuku)->first()->keterangan;
        $zodiak = $zodiakController->Zodiak($tanggal);
        $keteranganZodiak = Zodiak::where('nama', $zodiak)->first()->keterangan;

        $ramalan_sifat = [
            'pancawara' => $namaPancawara,
            'sifat_pancawara' => $keteranganPancawara,
            'saptawara' => $namaSaptawara,
            'sifat_saptawara' => $keteranganSaptawara,
            'wuku' => $namaWuku,
            'sifat_wuku' => $keteranganWuku,
            'zodiak' => $zodiak,
            'sifat_zodiak' => $keteranganZodiak,
        ];

        return $ramalan_sifat;
    }
}
