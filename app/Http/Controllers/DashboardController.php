<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\KalenderBaliAPI;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $start = date('Y-m-d');
        $client = new Client();
        $response = $client->request('GET', 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI?tanggal_mulai=' . $start . '&tanggal_selesai=' . $start . '&makna&pura');
        $data = json_decode($response->getBody()->getContents(), true);
        $tanggal_now = $data['result'][0]['tanggal'];
        $kalender = $data['result'][0]['kalender'];

        if ($kalender) {
            $hari_raya_now = $kalender[0]['hari_raya'];
            $penamaan_hari_bali_now = $kalender[0]['penamaan_hari_bali'];
            $makna_now = $kalender[0]['makna'];
            $pura_now = $kalender[0]['pura'];
        } else {
            $hari_raya_now = 'Tidak ada hari raya';
            $penamaan_hari_bali_now = 'Tidak ada penamaan hari bali';
            $makna_now = 'Tidak ada makna';
            $pura_now = 'Tidak ada pura';
        }

        return view('dashboard.index', compact('tanggal_now', 'hari_raya_now', 'penamaan_hari_bali_now', 'makna_now', 'pura_now'));
    }

    public function search_hari_raya()
    {
        return view('hari_raya.index');
    }

    public function search_dewasa_ayu()
    {
        return view('dewasa_ayu.index');
    }

    public function buy_api() 
    {
        return view('buy_api.index');
    }
}
