<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PengalantakaController;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\WukuController;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KalenderBaliAPI extends Controller
{
    // public function get()
    // {
    //     $q = DB::table('kalender_hari_raya')->get();
    //     return response()->json([
    //         'message' => 'Success',
    //         'data' => $q,
    //     ], 200);
    // }

    public function tes()
    {
        $q = DB::select('CALL searchHariRaya(1, 2023)');
        return response()->json([
            'message' => 'Success',
            'data' => $q,
        ], 200);
    }

    public function searchHariRaya(Request $request)
    {
        // $tanggal = '2023-05-20';
        // $tanggal = Carbon::createFromFormat('Y-m-d', $request->input('tanggal'))->toDateString();
        // $tanggal = Carbon::createFromFormat('Y-m-d', $query)->toDateString();
        $tanggal = [];
        // $tanggal_mulai = '2023-01-15';
        // $tanggal_selesai = '2023-03-10';
        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));

        $tanggal = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $tanggal[] = $tanggal_mulai->toDateString();
            $tanggal_mulai->addDay();
        }

        if ($tanggal_mulai >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal_mulai < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $isPangelong = true;
            $noNgunaratri = 50;
            $isNgunaratri = false;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $isPangelong = true;
            $noNgunaratri = 22;
            $isNgunaratri = false;
        }

        // dd($tanggal);
        // WUKU dan WEWARAN
        $wukuController = new WukuController();
        $saptawaraWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();

        // HARI RAYA
        $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        $kalender = [];

        for ($i = 0; $i < count($tanggal); $i++) {
            $hasilAngkaWuku = $wukuController->getNoWuku($tanggal[$i], $angkaWuku, $refTanggal);
            $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
            $namaWuku = $wukuController->getNamaWuku($hasilWuku);

            $saptawara = $saptawaraWaraController->getSaptawara($tanggal[$i]);
            $uripSaptawara = $saptawaraWaraController->getUripSaptaWara($saptawara);
            $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);


            $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
            $uripPancawara = $pancaWaraController->getUripPancaWara($pancawara);
            $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);


            $triwara = $triWaraController->gettriwara($hasilAngkaWuku);
            $namatriwara = $triWaraController->getNamatriwara($triwara);

            $pengalantaka = $pengalantakaController->getPengalantaka($tanggal[$i], $refTanggal, $penanggal, $noNgunaratri);
            $hariSasih = $hariSasihController->getHariSasih($tanggal[$i], $refTanggal, $penanggal, $noNgunaratri);

            if ($tanggal[$i] > '2002-01-01' || $tanggal[$i] < '1992-01-01') {
                if (strtotime($tanggal[$i]) < strtotime($refTanggal)) {
                    $no_sasih = $hariSasihController->getSasihBefore1992(
                        $tanggal[$i], $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka
                    );
                    
                } else {
                    $no_sasih = $hariSasihController->getSasihAfter2002(
                        $tanggal[$i], $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka
                    );
                }
            } else {
                $no_sasih = $hariSasihController->getSasihBetween(
                    $tanggal[$i], $refTanggal, $penanggal, $noNgunaratri, $noSasih, $tahunSaka
                );
            }

            // dd($hariSasih);
            $hariRaya = $hariRayaController->getHariRaya($tanggal[$i],$hariSasih[0],$hariSasih[1],$pengalantaka,$no_sasih[0],$triwara,$pancawara,$saptawara,$hasilWuku);
            // $hariRaya = $hariRayaController->getHariRaya($pancawara,$saptawara,$hasilWuku);

            // // Menyimpan $tanggal[$i] dan $hariRaya ke dalam array $kalender
            // array_push($kalender, [
            //     'tanggal' => $tanggal[$i],
            //     'hariRaya' => $hariRaya,
            //     'namaWuku' => $namaWuku,
            //     'namaSaptawara' => $namaSaptawara,
            //     'namaPancawara' => $namaPancawara,
            // ]);

            array_push($kalender, [
                'tanggal' => $tanggal[$i],
                'hasilAngkaWuku' => $hasilAngkaWuku,    
                'saptawara' => $saptawara,    
                'namaSaptawara' => $namaSaptawara,    
                'pancawara' => $pancawara,    
                'namaPancawara' => $namaPancawara,    
                'hariRaya' => $hariRaya,    
            ]);
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'kalender' => $kalender,
                // 'tanggal' => $tanggal,
                // 'hariRaya' => $hariRaya,
                // 'namaWuku' => $namaWuku,
                // 'namaPancawara' => $namaPancawara,
                // 'namaSaptawara' => $namaSaptawara
            ]
        ], 200);

        // return response()->json([
        //     'message' => 'Success',
        //     'data' => [
        //         'tanggal' => implode(', ', $tanggal),
        //     ]
        // ], 200);

    }

    public function searchTanggalHariRaya(Request $request)
    {
        // $tanggal = '2023-05-20';
        // $tanggal = Carbon::createFromFormat('Y-m-d', $request->input('tanggal'))->toDateString();
        // $tanggal = Carbon::createFromFormat('Y-m-d', $query)->toDateString();
        $tanggal = [];
        // $tanggal_mulai = '2023-01-15';
        // $tanggal_selesai = '2023-03-10';
        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));

        $tanggal = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $tanggal[] = $tanggal_mulai->toDateString();
            $tanggal_mulai->addDay();
        }

        if ($tanggal_mulai >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal_mulai < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $isPangelong = true;
            $noNgunaratri = 50;
            $isNgunaratri = false;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $isPangelong = true;
            $noNgunaratri = 22;
            $isNgunaratri = false;
        }

        // dd($tanggal);
        // WUKU dan WEWARAN
        $wukuController = new WukuController();
        $saptawaraWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();

        //PROSES KE HARI RAYA


        // HARI RAYA
        $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        $kalender = [];

        for ($i = 0; $i < count($tanggal); $i++) {
            $hasilAngkaWuku = $wukuController->getNoWuku($tanggal[$i], $angkaWuku, $refTanggal);
            $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
            $namaWuku = $wukuController->getNamaWuku($hasilWuku);

            $saptawara = $saptawaraWaraController->getSaptawara($tanggal[$i]);
            $uripSaptawara = $saptawaraWaraController->getUripSaptaWara($saptawara);
            $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);


            $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
            $uripPancawara = $pancaWaraController->getUripPancaWara($pancawara);
            $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);


            $hariRaya = $hariRayaController->getHariRaya($pancawara,$saptawara,$hasilWuku);

            array_push($kalender, [
                'tanggal' => $tanggal[$i],
                'hasilAngkaWuku' => $hasilAngkaWuku,    
                // 'saptawara' => $saptawara,    
                'namaSaptawara' => $namaSaptawara,    
                // 'pancawara' => $pancawara,    
                'namaPancawara' => $namaPancawara,    
                'hariRaya' => $hariRaya,    
            ]);
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'kalender' => $kalender,
            ]
        ], 200);

        // return response()->json([
        //     'message' => 'Success',
        //     'data' => [
        //         'tanggal' => implode(', ', $tanggal),
        //     ]
        // ], 200);

    }
}
