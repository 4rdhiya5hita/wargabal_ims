<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SangaWaraController_09 extends Controller
{
    public function getSangaWara($noWuku)
    {
        $sangawara = 0;

        if ($noWuku <= 4) {
            $sangawara = 1;
        } else {
            $sangawara = ($noWuku + 6) % 9;
        }

        if ($sangawara === 0) {
            $sangawara = 9;
        }

        return $sangawara;
    }

    public function getNamaSangaWara($i)
    {
        
        if ($i === 1) {
            $nama = 'Dangu';
        } elseif ($i === 2) {
            $nama = 'Jangur';
        } elseif ($i === 3) {
            $nama = 'Gigis';
        } elseif ($i === 4) {
            $nama = 'Nohan';
        } elseif ($i === 5) {
            $nama = 'Ogan';
        } elseif ($i === 6) {
            $nama = 'Erangan';
        } elseif ($i === 7) {
            $nama = 'Urungan';
        } elseif ($i === 8) {
            $nama = 'Tulus';
        } elseif ($i === 9) {
            $nama = 'Dadi';
        }

        return $nama;
    }
}
