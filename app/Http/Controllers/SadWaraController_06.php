<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SadWaraController_06 extends Controller
{
    public function getSadWara($noWuku)
    {
        $sadwara = $noWuku % 6;

        if ($sadwara == 0) {
            $sadwara = 6;
        }

        return $sadwara;
    }

    public function getNamaSadWara($i)
    {

        if ($i == 1) {
            $nama = 'Tungleh';
        } elseif ($i == 2) {
            $nama = 'Aryang';
        } elseif ($i == 3) {
            $nama = 'Urukung';
        } elseif ($i == 4) {
            $nama = 'Paniron';
        } elseif ($i == 5) {
            $nama = 'Was';
        } elseif ($i == 6) {
            $nama = 'Mahulu';
        }

        return $nama;
    }
}
