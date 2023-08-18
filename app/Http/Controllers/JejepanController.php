<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JejepanController extends Controller
{
    public function Jejepan($no_wuku)
    {
        $sadwara = $no_wuku % 6;

        if ($sadwara == 1) {
            return 'Mina';
        } elseif ($sadwara == 2) {
            return 'Taru';
        } elseif ($sadwara == 3) {
            return 'Sato';
        } elseif ($sadwara == 4) {
            return 'Patra';
        } elseif ($sadwara == 5) {
            return 'Wong';
        } else {
            return 'Paksi';
        }
    }
}
