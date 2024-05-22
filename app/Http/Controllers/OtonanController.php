<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtonanController extends Controller
{
    public function searchOtonanAPI(Request $request) 
    {
        $start = microtime(true);

        // $tanggal_lahir = '2002-03-01';
        // $tahun_dicari = '2023';
        $tanggal_lahir = $request->input('tanggal_lahir');
        $tahun_dicari = $request->input('tahun_dicari');
        // dd($tanggal_lahir, $tahun_dicari);

        if ($tanggal_lahir === null && $tahun_dicari === null) {
            return response()->json([
                'message' => 'Data tanggal lahir dan tahun dicari tidak boleh kosong'
            ], 400);
        } else if ($tahun_dicari === null) {
            return response()->json([
                'message' => 'Data tahun dicari tidak boleh kosong'
            ], 400);
        } else if ($tanggal_lahir === null) {
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
    
        // Validasi tahun_dicari sebagai integer
        if (!ctype_digit($tahun_dicari)) {
            return response()->json([
                'message' => 'Tahun dicari harus berupa bilangan bulat (integer)'
            ], 400);
        }   

        // Validasi tahun_dicari tidak boleh lebih lampau dari tahun kelahiran
        if ($tahun_dicari < date('Y', strtotime($tanggal_lahir))) {
            return response()->json([
                'message' => 'Tahun dicari tidak boleh lebih lampau dari tahun kelahiran'
            ], 400);
        }

        $selisih = floor((strtotime("$tahun_dicari-01-01") - strtotime($tanggal_lahir)) / (60 * 60 * 24));
        #selisih untuk mengetahui berapa hari dari tanggal lahir sampai tahun yang dicari
        $bagi = ceil($selisih / 210); # ceil untuk membulatkan keatas
        $tambah = $bagi * 210;
        $otonan = date('Y-m-d', strtotime("$tanggal_lahir + $tambah days"));
        $otonan2 = date('Y-m-d', strtotime("$otonan + 210 days"));

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'otonan_terdekat' => [$otonan, $otonan2],
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }
}
