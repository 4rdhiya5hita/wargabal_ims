<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class TransactionAPI extends Controller
{
    public function daftarServis()
    {
        $servis = Service::all();
        return response()->json($servis);
    }
}
