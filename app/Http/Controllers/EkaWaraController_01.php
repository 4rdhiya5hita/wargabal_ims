<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EkaWaraController_01 extends Controller
{
    public function getEkaWara($uripPancaWara, $uripSaptaWara)
    {
        $ekaWara = ($uripPancaWara + $uripSaptaWara) % 2;

        return $ekaWara;
    }

    public function getNamaEkaWara($i)
    {

        if ($i == 0) {
            $nama = '-';
        } elseif ($i == 1) {
            $nama = 'Luang';
        }

        return $nama;
    }
}
