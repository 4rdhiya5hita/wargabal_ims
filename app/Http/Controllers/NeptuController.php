<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NeptuController extends Controller
{
    public function Neptu($urip_panca, $urip_sapta)
    {
        return $urip_panca + $urip_sapta;
    }
}
