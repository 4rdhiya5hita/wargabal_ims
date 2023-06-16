<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaturWaraController_04 extends Controller
{
    public function getCaturWara($noWuku)
    {
        $caturWara = 0;

        if ($noWuku == 71 || $noWuku == 72 || $noWuku == 73) {
            $caturWara = 3;
        } elseif ($noWuku < 71) {
            $caturWara = $noWuku % 4;
        } else {
            $caturWara = ($noWuku + 2) % 4;
        }

        if ($caturWara == 0) {
            $caturWara = 4;
        }

        return $caturWara;
    }

    public function getNamaCaturWara($i)
    {

        if ($i == 1) {
            $nama = 'Sri';
        } elseif ($i == 2) {
            $nama = 'Laba';
        } elseif ($i == 3) {
            $nama = 'Jaya';
        } elseif ($i == 4) {
            $nama = 'Mandala';
        }

        return $nama;
    }
}
