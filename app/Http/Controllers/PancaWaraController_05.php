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
        if ($pancaWara == 1) {
            $urip = 5;
        } elseif ($pancaWara == 2) {
            $urip = 9;
        } elseif ($pancaWara == 3) {
            $urip = 7;
        } elseif ($pancaWara == 4) {
            $urip = 4;
        } elseif ($pancaWara == 5) {
            $urip = 8;
        }
        return $urip;
    }

    public function getNamaPancaWara($i)
    {
        if ($i == 1) {
            $nama = 'Umanis';
        } elseif ($i == 2) {
            $nama = 'Pahing';
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

