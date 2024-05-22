<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurnamaTilemController extends Controller
{
    public function getPurnamaTilem($pengalantaka, $sasihDay1, $sasihDay2)
    {
        $purnama_tilem = '-';

        if (($sasihDay1 === 15 && $pengalantaka === 'Penanggal') || ($sasihDay2 === 15 && $pengalantaka === 'Penanggal')) {
            $purnama_tilem = 0;
        } elseif (($sasihDay1 === 15 && $pengalantaka === 'Pangelong') || ($sasihDay2 === 15 && $pengalantaka === 'Pangelong')) {
            $purnama_tilem = 1;
        }

        return $purnama_tilem;
    }
}
