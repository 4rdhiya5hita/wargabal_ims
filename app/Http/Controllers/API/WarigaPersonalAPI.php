<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AstaWaraController_08;
use App\Http\Controllers\CaturWaraController_04;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DasaWaraController;
use App\Http\Controllers\DwiWaraController_02;
use App\Http\Controllers\EkaWaraController_01;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PurnamaTilemController;
use App\Http\Controllers\SadWaraController_06;
use App\Http\Controllers\SangaWaraController_09;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\ValidasiTanggal;
use App\Http\Controllers\WukuController;
use App\Models\DewasaAyu;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WarigaPersonalAPI extends Controller
{
    public function cariWarigaPersonal(Request $request)
    {
        // dd($request->all());
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 7;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);

        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_lahir = $request->input('tanggal_lahir');
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        $validasi_tanggal = new ValidasiTanggal();
        $response = $validasi_tanggal->validasiTanggal($tanggal_mulai, $tanggal_selesai);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        list($tanggal_mulai, $tanggal_selesai) = $response;

        
        if ($tanggal_lahir === null) {
            return response()->json([
                'message' => 'Data tanggal lahir tidak boleh kosong'
            ], 400);
        } 
    
        // Validasi format tanggal
        if (!strtotime($tanggal_lahir) || ctype_digit($tanggal_lahir)) {
            return response()->json([
                'message' => 'Data tanggal lahir harus berupa data tanggal yang valid'
            ], 400);
        }

        // Validasi tanggal_mulai tidak boleh kurang dari tanggal_lahir
        if (strtotime($tanggal_mulai) < strtotime($tanggal_lahir)) {
            return response()->json([
                'message' => 'Data tanggal dicari tidak boleh lebih lampau dari tanggal lahir'
            ], 400);
        }

        // $tanggal_lahir_urip = $this->getTanggalLahirUrip($tanggal_lahir);
        // cache
        $tanggal_lahir_urip = Cache::remember('tanggal_lahir_urip_' . $tanggal_lahir, now()->addDays(31), function () use ($tanggal_lahir) {
            return $this->getTanggalLahirUrip($tanggal_lahir);
        });

        $wariga_personal = Cache::remember('wariga_personal_' . $tanggal_lahir . '_' . $tanggal_mulai . '_' . $tanggal_selesai, now()->addDays(31), function () use ($tanggal_mulai, $tanggal_selesai, $tanggal_lahir_urip) {
            $wariga_personal_cache = [];
            while ($tanggal_mulai <= $tanggal_selesai) {
                $wariga = $this->getWarigaPersonal($tanggal_mulai->toDateString(), $tanggal_lahir_urip);
                if ($wariga == 'Guru') {
                    $keterangan = 'Guru (hari baik) diwakili oleh angka 1';
                } elseif ($wariga == 'Ratu') {
                    $keterangan = 'Ratu (hari baik) diwakili oleh angka 2';
                } elseif ($wariga == 'Lara') {
                    $keterangan = 'Lara (hari buruk) diwakili oleh angka 3';
                } elseif ($wariga == 'Pati') {
                    $keterangan = 'Pati (hari buruk) diwakili oleh angka 4';
                }
                
                $wariga_personal_cache[] = [
                    'tanggal' => $tanggal_mulai->toDateString(),
                    'wariga' => $wariga,
                    'keterangan' => $keterangan
                ];
                $tanggal_mulai->addDay();
            }

            return $wariga_personal_cache;
        });

        $hasil_wariga_personal = [
            'tanggal_lahir' => $tanggal_lahir,
            'wariga_personal' => $wariga_personal,
        ];

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $hasil_wariga_personal,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }

    public function getTanggalLahirUrip($tanggal_lahir)
    {
        if ($tanggal_lahir >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $angkaWuku = 70;
        } elseif ($tanggal_lahir < '1992-01-01') {
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

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal_lahir, $angkaWuku, $refTanggal);

        $saptawara = $saptaWaraController->getSaptawara($tanggal_lahir);
        $urip_saptawara = $saptaWaraController->getUripSaptaWara($saptawara);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $urip_pancawara = $pancaWaraController->getUripPancaWara($pancawara);

        $tanggal_lahir_urip = [
            'urip_saptawara' => $urip_saptawara,
            'urip_pancawara' => $urip_pancawara,
        ];

        return $tanggal_lahir_urip;
    }

    public function getWarigaPersonal($tanggal, $tanggal_lahir_urip)
    {
        // dd($tanggal_lahir_urip);
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

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);

        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $urip_saptawara = $saptaWaraController->getUripSaptaWara($saptawara);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $urip_pancawara = $pancaWaraController->getUripPancaWara($pancawara);

        $wariga_personal = ($urip_pancawara + $urip_saptawara + $tanggal_lahir_urip['urip_saptawara'] + $tanggal_lahir_urip['urip_pancawara']) % 4;
        if ($wariga_personal == 0 || $wariga_personal == 4) {
            return "Pati";
        } elseif ($wariga_personal == 1) {
            return "Guru";
        } elseif ($wariga_personal == 2) {
            return "Ratu";
        } elseif ($wariga_personal == 3) {
            return "Lara";
        }

    }
}
