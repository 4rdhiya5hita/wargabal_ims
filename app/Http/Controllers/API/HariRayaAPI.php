<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\ValidasiTanggal;
use App\Http\Controllers\WukuController;
use App\Models\HariRaya;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HariRayaAPI extends Controller
{
    public function cariHariRaya(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 1;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);
        
        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        $validasi_tanggal = new ValidasiTanggal();
        $response = $validasi_tanggal->validasiTanggal($tanggal_mulai, $tanggal_selesai);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        list($tanggal_mulai, $tanggal_selesai) = $response;

        $beserta_keterangan = $request->has('beserta_keterangan');
        $hari_raya = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $hari_raya[] = [
                'tanggal' => $tanggal_mulai->toDateString(),
                'hari_raya' => $this->getHariRaya(
                    $tanggal_mulai->toDateString(),
                    $beserta_keterangan
                ),
            ];
            $tanggal_mulai->addDay();
        }

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $hari_raya,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }

    private function getHariRaya($tanggal, $beserta_keterangan)
    {
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $noNgunaratri = 46;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $noNgunaratri = 50;
        } else {
            $refTanggal = '1992-01-01';
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $noNgunaratri = 22;
        }

        // Panggil semua controller yang dibutuhkan
        $wukuController = new WukuController();
        $saptaWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);

        $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        // $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $namaSaptawara = $saptaWaraController->getNamaSaptaWara($saptawara);
        $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);

        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);

        if ($tanggal > '2002-01-01' || $tanggal < '1992-01-01') {
            if (strtotime($tanggal) < strtotime($refTanggal)) {
                $no_sasih = $hariSasihController->getSasihBefore1992(
                    $tanggal,
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            } else {
                $no_sasih = $hariSasihController->getSasihAfter2002(
                    $tanggal,
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            }
        } else {
            $no_sasih = $hariSasihController->getSasihBetween(
                $tanggal,
                $refTanggal,
                $penanggal,
                $noNgunaratri,
                $noSasih,
                $tahunSaka
            );
        }
        // dd($namaWuku, $namaSaptawara, $namaPancawara, $namaTriwara);
        $hariRaya = $hariRayaController->getHariRaya($tanggal, $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2'], $pengalantaka_dan_hariSasih['pengalantaka'], $no_sasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);
        $rerainan = $namaSaptawara . ' ' . $namaPancawara . ' ' . $namaWuku;
        // dd($hariRaya, $rerainan);

        $hariRayaLengkap = [];
        // Perjikaan kalau dalam satu hari, hari raya nya lebih dari satu, misal Kajeng Kliwon dan Sugian Bali
        if (is_array($hariRaya) && count($hariRaya) > 1) {
            // dd('full');
            foreach ($hariRaya as $value) {
                $data_hari_raya = HariRaya::where('hari_raya', $value)->get();

                foreach ($data_hari_raya as $item) {
                    $ambil_keterangan = $item->description;
                }
                // Perjikaan sesuai parameter urlnya
                if ($beserta_keterangan) {
                    array_push($hariRayaLengkap, ['nama' => $value, 'keterangan' => $ambil_keterangan]);
                } else {
                    array_push($hariRayaLengkap, ['nama' => $value]);
                }
            }
        } else {
            // Perjikaan kalau dalam satu hari, hari raya nya hanya satu saja misal Hari Raya Saraswati saja, Galungan saja
            if ($hariRaya[0] != '-') {
                // dd('yes');
                $data_hari_raya = HariRaya::where('hari_raya', $hariRaya[0])->get();

                foreach ($data_hari_raya as $item) {
                    $ambil_keterangan = $item->description;
                    // Perjikaan sesuai parameter urlnya
                    if ($beserta_keterangan) {
                        array_push($hariRayaLengkap, ['nama' => $hariRaya[0], 'keterangan' => $ambil_keterangan]);
                    } else {
                        array_push($hariRayaLengkap, ['nama' => $hariRaya[0]]);
                    }
                }
            } else {
                // dd('no');
                array_push($hariRayaLengkap, '-');
            }
        }

        return $hariRayaLengkap;
    }
}
