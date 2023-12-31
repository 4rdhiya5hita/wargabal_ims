<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\KalenderBaliAPI;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('landing_page.widget');
    }

    public function docs_kalender()
    {
        return view('landing_page.docs_kalender');
    }

    public function dashboard_calendar()
    {
        $start = date('Y-m-d');
        $client = new Client();
        $response = $client->request('GET', 'https://wargabal-ims-4065061e96e3.herokuapp.com/api/searchHariRayaAPI?tanggal_mulai=' . $start . '&tanggal_selesai=' . $start . '&makna&pura');
        $data = json_decode($response->getBody()->getContents(), true);
        $tanggal_now = $data['result'][0]['tanggal'];
        $kalender = $data['result'][0]['kalender'];
        // dd($kalender[0][0]);

        if ($kalender && $kalender[0][0] != "-") {
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

        return view('dashboard.index_calendar', compact('tanggal_now', 'hari_raya_now', 'penamaan_hari_bali_now', 'makna_now', 'pura_now'));
    }

    public function search_hari_raya()
    {
        return view('hari_raya.index');
    }

    public function search_dewasa_ayu()
    {
        return view('dewasa_ayu.index');
    }

    public function search_otonan()
    {
        return view('otonan.index');
    }

    public function search_kalender()
    {
        return view('kalender.index');
    }

    public function wargabal_ims()
    {
        return view('wargabal_ims.index');
    }

}
