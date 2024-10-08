<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ValidasiAPI;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtonanAPI extends Controller
{
    public function cariOtonan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 5;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);
        
        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_lahir = $request->input('tanggal_lahir');
        $tahun_dicari = $request->input('tahun_dicari');

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

        // cache otonan
        $otonan = Cache::remember('otonan_' . $tanggal_lahir . '_' . $tahun_dicari, now()->addDays(31), function () use ($tanggal_lahir, $tahun_dicari) {
            $tahun_tanggal_lahir = date('Y', strtotime($tanggal_lahir));
            if ($tahun_tanggal_lahir == $tahun_dicari)
            {
                // tambah tanggal lahir dengan 210 hari
                $otonan = date('Y-m-d', strtotime("$tanggal_lahir + 210 days"));
                $otonan_perayaan_ke = 1;
                $string1 = 'perayaan otonan ke ' . $otonan_perayaan_ke;
                $otonan2 = date('Y-m-d', strtotime("$otonan + 210 days"));
                $otonan2_perayaan_ke = 2;
                $string2 = 'perayaan otonan ke ' . $otonan2_perayaan_ke;

                // sudah merayakan otonan berapa kali sesuai tahun dicari
                $jumlah_otonan = 0;
                
            } else {
                $selisih = floor((strtotime("$tahun_dicari-01-01") - strtotime($tanggal_lahir)) / (60 * 60 * 24));
                #selisih untuk mengetahui berapa hari dari tanggal lahir sampai tahun yang dicari
                $bagi = ceil($selisih / 210); # ceil untuk membulatkan keatas
                $tambah = $bagi * 210;
                $otonan = date('Y-m-d', strtotime("$tanggal_lahir + $tambah days"));
                $otonan_perayaan_ke =  $bagi + 0;
                $string1 = 'perayaan otonan ke ' . $otonan_perayaan_ke;
                $otonan2 = date('Y-m-d', strtotime("$otonan + 210 days"));
                $otonan2_perayaan_ke = $bagi + 1;
                $string2 = 'perayaan otonan ke ' . $otonan2_perayaan_ke;
    
                // sudah merayakan otonan berapa kali sesuai tahun dicari
                $jumlah_otonan = $bagi - 1;
                if ($jumlah_otonan < 0) {
                    $jumlah_otonan = 0;
                }
            }
            
            $result = [
                'otonan_terdekat_pertama' => $otonan,
                'perayaan_terdekat_pertama' => $string1,
                'otonan_terdekat_kedua' => $otonan2,
                'perayaan_terdekat_kedua' => $string2,
                'keterangan' => 'sudah merayakan otonan sebanyak ' . $jumlah_otonan . ' kali',
            ];
            return $result;
        });

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'otonan_terdekat_pertama' => $otonan['otonan_terdekat_pertama'],
            'perayaan_terdekat_pertama' => $otonan['perayaan_terdekat_pertama'],
            'otonan_terdekat_kedua' => $otonan['otonan_terdekat_kedua'],
            'perayaan_terdekat_kedua' => $otonan['perayaan_terdekat_kedua'],
            'keterangan' => $otonan['keterangan'],
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }
}
