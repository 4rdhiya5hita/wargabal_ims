<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PancaSudhaController extends Controller
{
    public function Pancasudha($pancawara, $saptawara)
    {
        $h_pancasuda = '';

        if (
            ($saptawara == 1 && $pancawara == 2) || 
            ($saptawara == 4 && $pancawara == 2) || 
            ($saptawara == 2 && $pancawara == 4) || 
            ($saptawara == 6 && $pancawara == 5) || 
            ($saptawara == 3 && $pancawara == 1) ||
            ($saptawara == 7 && $pancawara == 3)
        ) {
            $h_pancasuda = 'Wisesa Segara';
        } elseif (
            ($saptawara == 2 && $pancawara == 1) || 
            ($saptawara == 5 && $pancawara == 4) || 
            ($saptawara == 6 && $pancawara == 2) || 
            ($saptawara == 7 && $pancawara == 5)
        ) {
            $h_pancasuda = 'Tunggak Semi';
        } elseif (
            ($saptawara == 1 && $pancawara == 4) || 
            ($saptawara == 3 && $pancawara == 3) || 
            ($saptawara == 4 && $pancawara == 4) || 
            ($saptawara == 5 && $pancawara == 1) || 
            ($saptawara == 7 && $pancawara == 2)
        ) {
            $h_pancasuda = 'Satria Wibawa';
        } elseif (
            ($saptawara == 1 && $pancawara == 1) || 
            ($saptawara == 2 && $pancawara == 3) || 
            ($saptawara == 3 && $pancawara == 5) || 
            ($saptawara == 4 && $pancawara == 1) || 
            ($saptawara == 6 && $pancawara == 4)
        ) {
            $h_pancasuda = 'Sumur Sinaba';
        } elseif (
            ($saptawara == 1 && $pancawara == 3) || 
            ($saptawara == 2 && $pancawara == 2) || 
            ($saptawara == 4 && $pancawara == 3) || 
            ($saptawara == 5 && $pancawara == 5) || 
            ($saptawara == 7 && $pancawara == 1)
        ) {
            $h_pancasuda = 'Bumi Kepetak';
        } elseif (
            ($saptawara == 2 && $pancawara == 5) || 
            ($saptawara == 3 && $pancawara == 2) || 
            ($saptawara == 5 && $pancawara == 3) || 
            ($saptawara == 6 && $pancawara == 1) || 
            ($saptawara == 7 && $pancawara == 4)
        ) {
            $h_pancasuda = 'Satria Wirang';
        } elseif (
            ($saptawara == 1 && $pancawara == 5) || 
            ($saptawara == 3 && $pancawara == 4) || 
            ($saptawara == 4 && $pancawara == 5) || 
            ($saptawara == 5 && $pancawara == 2) || 
            ($saptawara == 6 && $pancawara == 3)
        ) {
            $h_pancasuda = 'Lebu Katiub Angin';
        }

        return $h_pancasuda;
    }
}
