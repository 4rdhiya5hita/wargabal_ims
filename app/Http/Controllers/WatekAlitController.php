<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WatekAlitController extends Controller
{
    public function WatekAlit($urip_panca, $urip_sapta)
    {
        $alit = $urip_sapta + $urip_panca;
        $cekAlit = $alit % 4;

        $h_alit = '';

        switch ($cekAlit) {
            case 0:
                $h_alit = 'Lintah';
                break;
            case 1:
                $h_alit = 'Uler';
                break;
            case 2:
                $h_alit = 'Gajah';
                break;
            case 3:
                $h_alit = 'Lembu';
                break;
        }

        return $h_alit;
    }
}
