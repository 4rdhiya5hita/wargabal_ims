<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\KalenderBaliAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HariRayaBaliAPI extends Controller
{
    public function searchHariRayaAPI_byHariRaya(Request $request)
    {
        $kalenderBaliController = new KalenderBaliAPI;
        $search_by_hari_raya = $kalenderBaliController->searchHariRayaAPI($request);
    }
}
