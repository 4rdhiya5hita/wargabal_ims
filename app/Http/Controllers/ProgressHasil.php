<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\KalenderBaliAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;
use GuzzleHttp\Client;


class ProgressHasil extends Controller
{
    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function process_search_hari_raya(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');
        // dd($tanggal_mulai, $tanggal_selesai);

        $url = 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;
        // $url = 'http://localhost:8000/api/searchHariRayaAPI' . '?tanggal_mulai=' . $tanggal_mulai . '&tanggal_selesai=' . $tanggal_selesai;

        // Mengambil data dari API eksternal Alpha Vantage menggunakan metode get()
        $response = Http::get($url);

        // Memeriksa status code response untuk memastikan permintaan berhasil
        if ($response->successful()) {
            // Menampilkan hasil respons dari API
            $kalender = $response->json();
            // dd($kalender);
        } else {
            echo "Gagal mengambil data dari API.";
        }

        
        return view('hari_raya.search_hari_raya', compact('kalender'));
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
