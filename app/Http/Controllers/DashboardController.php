<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\KalenderBaliAPI;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // // Buat klien GuzzleHt


        return view('dashboard.index');
    }
}
