<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HariAPI extends Controller
{
    public function getHari($tanggal)
    {
        $hari = date('l', strtotime($tanggal));
        $hari = strtolower($hari);
        $hari = ucfirst($hari);
        return $hari;

        // example: $hari = "Sunday";
    }
}
