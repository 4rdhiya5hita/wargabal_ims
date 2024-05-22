<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PancaWaraController_05 extends Controller
{
    public function getPancawara($noWuku)
    {
        $noPancawara = ($noWuku % 5) + 1;
        return $noPancawara;
    }

    public function getUripPancaWara($pancaWara)
    {
        switch ($pancaWara) {
            case 1:
                $urip = 5;
                break;
            case 2:
                $urip = 9;
                break;
            case 3:
                $urip = 7;
                break;
            case 4:
                $urip = 4;
                break;
            case 5:
                $urip = 8;
                break;
        }
        
        return $urip;
    }

    public function getNamaPancaWara($i)
    {
        if ($i == 1) {
            $nama = 'Umanis';
        } elseif ($i == 2) {
            $nama = 'Paing';
        } elseif ($i == 3) {
            $nama = 'Pon';
        } elseif ($i == 4) {
            $nama = 'Wage';
        } elseif ($i == 5) {
            $nama = 'Kliwon';
        }
        return $nama;
    }
}

