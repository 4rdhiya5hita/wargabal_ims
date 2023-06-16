<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AstaWaraController_08 extends Controller
{
    public function getAstaWara($noWuku)
    {
        $astawara = 0;

        if ($noWuku == 71 || $noWuku == 72 || $noWuku == 73) {
            $astawara = 7;
        } elseif ($noWuku < 71) {
            $astawara = $noWuku % 8;
        } else {
            $astawara = ($noWuku + 6) % 8;
        }

        if ($astawara == 0) {
            $astawara = 8;
        }

        return $astawara;
    }

    public function getNamaAstaWara($i)
    {

        if ($i == 1) {
            $nama = 'Sri';
        } elseif ($i == 2) {
            $nama = 'Indra';
        } elseif ($i == 3) {
            $nama = 'Guru';
        } elseif ($i == 4) {
            $nama = 'Yama';
        } elseif ($i == 5) {
            $nama = 'Ludra';
        } elseif ($i == 6) {
            $nama = 'Brahma';
        } elseif ($i == 7) {
            $nama = 'Kala';
        } elseif ($i == 8) {
            $nama = 'Uma';
        }

        return $nama;
    }
}
