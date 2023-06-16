<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DasaWaraController extends Controller
{
    public function getDasawara($uripPancaWara, $uripSaptaWara)
    {
        $dasawara = (($uripPancaWara + $uripSaptaWara) % 10) + 1;
        return $dasawara;
    }

    public function getNamaDasawara($i)
    {
        
        if ($i == 1) {
            $nama = 'Pandita';
        } elseif ($i == 2) {
            $nama = 'Pati';
        } elseif ($i == 3) {
            $nama = 'Suka';
        } elseif ($i == 4) {
            $nama = 'Duka';
        } elseif ($i == 5) {
            $nama = 'Sri';
        } elseif ($i == 6) {
            $nama = 'Manu';
        } elseif ($i == 7) {
            $nama = 'Manusa';
        } elseif ($i == 8) {
            $nama = 'Raja';
        } elseif ($i == 9) {
            $nama = 'Dewa';
        } elseif ($i == 10) {
            $nama = 'Raksasa';
        }
        return $nama;
    }
}
