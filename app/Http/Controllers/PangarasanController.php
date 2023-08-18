<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PangarasanController extends Controller
{
    public function Pangarasan($urip_panca, $urip_sapta)
    {
        $pangarasan = $urip_sapta + $urip_panca;
        $cekPangarasan = $pangarasan % 10;

        $h_pangarasan = '';

        switch ($cekPangarasan) {
            case 0:
                $h_pangarasan = 'Laku Pandita Sakti';
                break;
            case 1:
                $h_pangarasan = 'Aras Tuding';
                break;
            case 2:
                $h_pangarasan = 'Aras Kembang';
                break;
            case 3:
                $h_pangarasan = 'Laku Bintang';
                break;
            case 4:
                $h_pangarasan = 'Laku Bulan';
                break;
            case 5:
                $h_pangarasan = 'Laku Surya';
                break;
            case 6:
                $h_pangarasan = 'Laku Air/Toya';
                break;
            case 7:
                $h_pangarasan = 'Laku Bumi';
                break;
            case 8:
                $h_pangarasan = 'Laku Api';
                break;
            case 9:
                $h_pangarasan = 'Laku Angin';
                break;
        }

        return $h_pangarasan;
    }
}
