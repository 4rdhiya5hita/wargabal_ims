<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HariRayaController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\WukuController;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarAPI extends Controller
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
        $tanggal = '2023-05-20';
        // $tanggal = Carbon::createFromFormat('Y-m-d', $request->input('tanggal'))->toDateString();
        // $tanggal = Carbon::createFromFormat('Y-m-d', $query)->toDateString();


        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal < '1992-01-01') {
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
        $wukuController = new WukuController();
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        // dd($namaWuku);

        $saptawaraWaraController = new SaptaWaraController_07();
        $saptawara = $saptawaraWaraController->getSaptawara($tanggal);
        // $uripSaptawara = $saptawaraWaraController->getUripSaptaWara($saptawara);
        $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);
        // dd($saptawara);

        $pancaWaraController = new PancaWaraController_05();
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        // $uripPancawara = $pancaWaraController->getUripPancaWara($pancawara);
        $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);


        $hariRayaController = new HariRayaController();
        $hariRaya = $hariRayaController->getHariRaya($pancawara, $saptawara, $hasilWuku);

        return response()->json([
            'message' => 'Success',
            'data' => [
                'hariRaya' => $hariRaya,
                'namaWuku' => $namaWuku,
                'namaPancawara' => $namaPancawara,
                'namaSaptawara' => $namaSaptawara
            ]
        ], 200);
        
    }
}
