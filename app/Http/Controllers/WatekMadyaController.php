<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WatekMadyaController extends Controller
{
    public function WatekMadya($urip_panca, $urip_sapta)
    {
        $madya = $urip_sapta + $urip_panca;
        $cekMadya = $madya % 5;

        $h_madya = '';

        switch ($cekMadya) {
            case 0:
                $h_madya = 'Wong';
                break;
            case 1:
                $h_madya = 'Gajah';
                break;
            case 2:
                $h_madya = 'Watu';
                break;
            case 3:
                $h_madya = 'Buta';
                break;
            case 4:
                $h_madya = 'Suku';
                break;
        }

        return $h_madya;
    }
}
