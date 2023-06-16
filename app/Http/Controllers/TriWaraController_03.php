<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TriWaraController_03 extends Controller
{
    public function getTriWara($noWuku)
    {
        $triWara = $noWuku % 3;

        if ($triWara == 0) {
            $triWara = 3;
        }

        return $triWara;
    }

    public function getNamaTriWara($i)
    {

        if ($i == 1) {
            $nama = 'Pasah';
        } elseif ($i == 2) {
            $nama = 'Beteng';
        } elseif ($i == 3) {
            $nama = 'Kajeng';
        }

        return $nama;
    }
}
