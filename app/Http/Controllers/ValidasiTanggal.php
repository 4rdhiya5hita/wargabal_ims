<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ValidasiTanggal extends Controller
{
    public function validasiTanggal($tanggal_mulai, $tanggal_selesai)
    {

        if ($tanggal_mulai === null && $tanggal_selesai === null) {
            return response()->json([
                'message' => 'Data tanggal mulai dan tanggal selesai tidak boleh kosong'
            ], 400);
        } else if ($tanggal_mulai === null) {
            return response()->json([
                'message' => 'Data tanggal mulai tidak boleh kosong'
            ], 400);
        } else if ($tanggal_selesai === null) {
            return response()->json([
                'message' => 'Data tanggal selesai tidak boleh kosong'
            ], 400);
        }

        // Validasi format tanggal
        if (!strtotime($tanggal_mulai) || ctype_digit($tanggal_mulai) || !strtotime($tanggal_selesai) || ctype_digit($tanggal_selesai)) {
            return response()->json([
                'message' => 'Data tanggal harus berupa data tanggal yang valid'
            ], 400);
        }

        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);

        // Validasi tanggal_mulai tidak boleh lebih dari tanggal_selesai
        if ($tanggal_selesai->lessThan($tanggal_mulai)) {
            return response()->json([
                'message' => 'Data tanggal_mulai tidak boleh lebih dari tanggal_selesai '
            ], 400);
        }

        return [$tanggal_mulai, $tanggal_selesai];
    }
}
