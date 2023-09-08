<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerhitunganKomponenController extends Controller
{
    public function perhitunganKomponen()
    {
        // dd($get_jejepan);
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $isPangelong = true;
            $noNgunaratri = 46;
            $isNgunaratri = false;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $isPangelong = true;
            $noNgunaratri = 50;
            $isNgunaratri = false;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $isPangelong = true;
            $noNgunaratri = 22;
            $isNgunaratri = false;
        }

        // Panggil semua controller yang dibutuhkan
        $wukuController = new WukuController();
        $saptaWaraController = new SaptaWaraController_07();
        $pancaWaraController = new PancaWaraController_05();
        $triWaraController = new TriWaraController_03();
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController;
        $hariRayaController = new HariRayaController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
        $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        // $namaWuku = $wukuController->getNamaWuku($hasilWuku);
        $namaSaptawara = $saptaWaraController->getNamaSaptaWara($saptawara);
        $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);
        $namaTriwara = $triWaraController->getNamatriwara($triwara);

        // $pengalantaka_dan_hariSasih = $pengalantakaController->getPengalantaka($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        // $hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);

        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
        // HASIL DARI KODE DIATAS: 
        // return [
        //     'penanggal_1' => $penanggal,
        //     'penanggal_2' => $penanggal2,
        //     'pengalantaka' => $pengalantaka,
        // ];

        if ($tanggal > '2002-01-01' || $tanggal < '1992-01-01') {
            if (strtotime($tanggal) < strtotime($refTanggal)) {
                $no_sasih = $hariSasihController->getSasihBefore1992(
                    $tanggal,
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            } else {
                $no_sasih = $hariSasihController->getSasihAfter2002(
                    $tanggal,
                    $refTanggal,
                    $penanggal,
                    $noNgunaratri,
                    $noSasih,
                    $tahunSaka
                );
            }
        } else {
            $no_sasih = $hariSasihController->getSasihBetween(
                $tanggal,
                $refTanggal,
                $penanggal,
                $noNgunaratri,
                $noSasih,
                $tahunSaka
            );
        }
        // dd($namaWuku, $namaSaptawara, $namaPancawara, $namaTriwara);
        $hariRaya = $hariRayaController->getHariRaya($tanggal, $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2'], $pengalantaka_dan_hariSasih['pengalantaka'], $no_sasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);
        $piodalan = $namaSaptawara . ' ' . $namaPancawara . ' ' . $namaWuku;
    }
}
