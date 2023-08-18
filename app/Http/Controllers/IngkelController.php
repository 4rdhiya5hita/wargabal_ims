<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IngkelController extends Controller
{
    public function Ingkel($no_wuku)
    {
        if ($no_wuku == 1 || $no_wuku == 7 || $no_wuku == 13 || $no_wuku == 19 || $no_wuku == 25) {
            return 'Wong';
        } elseif ($no_wuku == 2 || $no_wuku == 8 || $no_wuku == 14 || $no_wuku == 20 || $no_wuku == 26) {
            return 'Sato';
        } elseif ($no_wuku == 3 || $no_wuku == 9 || $no_wuku == 15 || $no_wuku == 21 || $no_wuku == 27) {
            return 'Mina';
        } elseif ($no_wuku == 4 || $no_wuku == 10 || $no_wuku == 16 || $no_wuku == 22 || $no_wuku == 28) {
            return 'Manuk';
        } elseif ($no_wuku == 5 || $no_wuku == 11 || $no_wuku == 17 || $no_wuku == 23 || $no_wuku == 29) {
            return 'Taru';
        } else {
            return 'Buku';
        }
    }
}
