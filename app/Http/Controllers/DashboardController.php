<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\KalenderBaliAPI;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('dashboard.index');
    }

    public function search_hari_raya()
    {
        return view('hari_raya.index');
    }

    public function search_dewasa_ayu()
    {
        return view('dewasa_ayu.index');
    }
}
