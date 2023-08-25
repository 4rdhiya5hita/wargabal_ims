<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtonanController extends Controller
{
    public function searchOtonanAPI(Request $request) 
    {
        $start = microtime(true);

        $tanggal_lahir = '2002-03-01';
        $tahun_dicari = '2023';
        // $tanggal_lahir = $request->input('tanggal_lahir');
        // $tahun_dicari = $request->input('tahun_dicari');

        $selisih = floor((strtotime("$tahun_dicari-01-01") - strtotime($tanggal_lahir)) / (60 * 60 * 24));
        $bagi = ceil($selisih / 210);
        $tambah = $bagi * 210;
        $otonan = date('Y-m-d', strtotime("$tanggal_lahir + $tambah days"));
        $otonan2 = date('Y-m-d', strtotime("$otonan + 210 days"));

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'message' => 'Sukses',
            'otonan_terdekat' => [$otonan, $otonan2],
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);

    }
}
