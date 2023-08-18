<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\KalenderBaliAPI;
use App\Models\Piodalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use GuzzleHttp\Client;

use function PHPUnit\Framework\isNull;

class ProgressHasil extends Controller
{
    public function process_search_hari_raya(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $makna = $request->has('makna');
        $pura = $request->has('pura');
        // dd($makna);

        if($makna && $pura) {
            // echo "makna pura";
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&makna&pura';
        }elseif($makna){
            // echo "makna";
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&makna';
        }elseif($pura){
            // echo "pura";
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&pura';
        }else{
            // echo "null";
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;
        }
        // $url = 'http://localhost:8000/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;

        $response = Http::get($url);

        // Memeriksa status code response untuk memastikan permintaan berhasil
        if ($response->successful()) {
            // Menampilkan hasil respons dari API
            $kalender = $response->json();
            // dd($kalender);

            return response()->json($kalender, 200);
            // return view('hari_raya.hari_raya', compact('kalender', 'makna_piodalan'));
        } else {
            echo "Gagal mengambil data dari API.";
        }
    }

    public function process_search_dewasa_ayu(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        $keterangan = $request->has('keterangan');

        if($keterangan) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchDewasaAyuAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&keterangan';
        }else{
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchDewasaAyuAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;
        }
        // $url = 'http://localhost:8000/api/searchDewasaAyuAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;

        $response = Http::get($url);

        // Memeriksa status code response untuk memastikan permintaan berhasil
        if ($response->successful()) {
            // Menampilkan hasil respons dari API
            $dewasa_ayu = $response->json();
            // dd($kalender);

            return response()->json($dewasa_ayu, 200);
            // return view('hari_raya.hari_raya', compact('kalender', 'makna_piodalan'));
        } else {
            echo "Gagal mengambil data dari API.";
        }
    }

    public function process_search_kalender(Request $request)
    {
        // $tanggal_mulai = $request->input('tanggal_mulai');
        // $tanggal_selesai = $request->input('tanggal_selesai');
        
        $tanggal_mulai = '2023-01-20';
        $tanggal_selesai = '2023-01-21';
        $lengkap = $request->has('lengkap');
        $ingkel = $request->has('ingkel');
        $jejepan = $request->has('jejepan');
        $lintang = $request->has('lintang');
        $pancasudha = $request->has('pancasudha');
        $pangarasan = $request->has('pangarasan');
        $rakam = $request->has('rakam');
        $watek_madya = $request->has('watek_madya');
        $watek_alit = $request->has('watek_alit');
        $neptu = $request->has('neptu');
        $ekajalarsi = $request->has('ekajalarsi');
        $zodiak = $request->has('zodiak');
        $pratiti = $request->has('pratiti');
        
        // dd($jejepan);
        // if($jejepan) {
        //     dd("berhasil");
        // }

        if($lengkap) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&lengkap';
        }elseif($ingkel) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&ingkel=ingkel';
        }elseif($jejepan) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&jejepan=jejepan';
        }elseif($lintang) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&lintang=lintang';
        }elseif($pancasudha) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&pancasudha=pancasudha';
        }elseif($pangarasan) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&pangarasan=pangarasan';
        }elseif($rakam) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&rakam=rakam';
        }elseif($watek_madya) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&watek_madya=watek_madya';
        }elseif($watek_alit) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&watek_alit=watek_alit';
        }elseif($neptu) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&neptu=neptu';
        }elseif($ekajalarsi) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&ekajalarsi=ekajalarsi';
        }elseif($zodiak) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&zodiak=zodiak';
        }elseif($pratiti) {
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai . '&pratiti=pratiti';
        }else{
            $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;
        }
        // $url = 'http://localhost:8000/api/searchKalenderAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;

        // dd($url);
        $response = Http::get($url);

        // Memeriksa status code response untuk memastikan permintaan berhasil
        if ($response->successful()) {
            // Menampilkan hasil respons dari API
            $kalender = $response->json();
            // dd($kalender);

            return response()->json($kalender, 200);
            // return view('hari_raya.hari_raya', compact('kalender', 'makna_piodalan'));
        } else {
            echo "Gagal mengambil data dari API.";
        }
    }

    public function getHasilProgress()
    {
        return view('tasks.progressHasil');
    }

    public function getProgress()
    {
        // $client = new Client();
        // $responses = $client->get('http://localhost:8000/api/searchHariRaya'); // Ganti URL API dengan URL yang sesuai
        // // $responses = $client->get('http://localhost:8000/api/tes'); // Ganti URL API dengan URL yang sesuai
        // $data = json_decode($responses->getBody(), true);
        // $data = $data['data'];
        // dd($data);

        // Set header untuk SSE
        $response = new StreamedResponse(function () {
            $progress = 0;

            // Simulasikan proses pemrosesan berjalan
            while ($progress <= 100) {
                //     $kalenderController = new KalenderBaliAPI;
                //     $kalender = $kalenderController->searchHariRaya();
                echo "data: $progress\n\n";
                // echo $kalender[$progress]; // Data yang akan dikirim ke klien

                ob_flush();
                flush();

                // Tunggu 1 detik sebelum mengirim data berikutnya (simulasi proses)
                sleep(1);

                $progress += 1; // Update nilai progress
            }
        });

        // Set header untuk SSE
        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
