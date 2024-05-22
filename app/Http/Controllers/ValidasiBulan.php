<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidasiBulan extends Controller
{
    public function validasiTanggal($tanggal_mulai, $tanggal_selesai, $bulan, $tahun)
    {
        // cek dimana pilih inputan bulan dan tahun atau tanggal mulai dan tanggal selesai
        if ($tanggal_mulai && $tanggal_selesai && !$bulan && !$tahun || $bulan && $tahun && !$tanggal_mulai && !$tanggal_selesai) {

            if ($bulan || $tahun) {
                if ($bulan && $tahun) {
                    // Validasi format bulan dan tahun
                    if (!ctype_digit($bulan) || !ctype_digit($tahun)) {
                        return response()->json([
                            'message' => 'Data bulan dan tahun harus berupa bilangan asli (integer)'
                        ], 400);
                    } else if ($bulan < 1 || $bulan > 12) {
                        return response()->json([
                            'message' => 'Data bulan harus berada diantara 1 sampai 12'
                        ], 400);
                    } else if ($tahun < 1) {
                        return response()->json([
                            'message' => 'Data tahun tidak boleh kurang dari 1'
                        ], 400);
                    } else {
                        $tanggal_mulai = Carbon::parse("$tahun-$bulan-01");
                        $tanggal_selesai = Carbon::parse("$tahun-$bulan-01")->endOfMonth();
                    }
                } else {
                    return response()->json([
                        'message' => 'Data bulan dan tahun tidak boleh kosong'
                    ], 400);
                }
            } else {

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
            }

            if ($tanggal_selesai->lessThan($tanggal_mulai)) {
                return response()->json([
                    'message' => 'Data tanggal_mulai tidak boleh lebih dari tanggal_selesai '
                ], 400);
            }

            return [$tanggal_mulai, $tanggal_selesai];

        } elseif (!$tanggal_mulai && !$tanggal_selesai && !$bulan && !$tahun) {
            return response()->json([
                'message' => 'Mohon masukkan inputan untuk melakukan perhitungan'
            ], 400);
        }
        
        else {
            return response()->json([
                'message' => 'Pilihlah salah satu metode inputan. \n 1 Menginputkan data tanggal mulai dan tanggal selesai. \n 2. Menginputkan data bulan dan tahun yang ingin dicari'
            ], 400);
        }
    }
}
