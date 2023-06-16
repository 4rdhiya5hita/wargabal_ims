<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DwiWaraController_02 extends Controller
{
    public function getDwiWara($uripPancaWara, $uripSaptaWara)
    {
        $dwiWara = ($uripPancaWara + $uripSaptaWara) % 2;

        if ($dwiWara == 0) {
            $dwiWara = 1;
        } else {
            $dwiWara = 2;
        }

        return $dwiWara;
    }

    public function getNamaDwiWara($i)
    {
        
        if ($i == 1) {
            $nama = 'Menga';
        } elseif ($i == 2) {
            $nama = 'Pepet';
        }

        return $nama;
    }
}
