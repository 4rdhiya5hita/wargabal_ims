<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DewasaAyuController extends Controller
{
    public function searchDewasaAyuAPI(Request $request)
    {
        $start = microtime(true);

        // $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        // $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));
        $tanggal_mulai = '2023-08-01';
        $tanggal_selesai = '2023-08-05';
        
        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);
        $cacheKey = 'processed-data-' . $tanggal_mulai . '-' . $tanggal_selesai;

        $dewasa_ayu = [];

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $end = microtime(true);
            $executionTime = $end - $start;
            $executionTime = number_format($executionTime, 6);

            return response()->json([
                'message' => 'Data telah diambil dari cache.',
                'hari_raya' => $result,
                'waktu_eksekusi' => $executionTime
            ]);
        }

        while ($tanggal_mulai <= $tanggal_selesai) {
            $dewasa_ayu[] = [
                'tanggal' => $tanggal_mulai->toDateString(),
                'hariRaya' => $this->getDewasaAyu($tanggal_mulai->toDateString()),
            ];
            $tanggal_mulai->addDay();
        }
        
        $minutes = 60; // Durasi penyimpanan cache dalam menit
        Cache::put($cacheKey, $dewasa_ayu, $minutes); // Menyimpan hasil pemrosesan data dalam cache
        
        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'message' => 'Sukses',
            'dewasa_ayu' => $dewasa_ayu,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
        // return view('dashboard.index', compact('dewasa_ayu'));
    }

    public function getDewasaAyu($tanggal)
    {
        // // hariSasihController -> getHariSasih
        // $pengalantaka = $request->input('pengalantaka');
        // $sasihDay1 = $request->input('sasihDay1');
        // $sasihDay2 = $request->input('sasihDay2');

        // // hariSasihController -> getSasih
        // $no_sasih = $request->input('no_sasih');
        // $purnama_tilem = $request->input('h_purnama');
        // $purnama_tilem = $request->input('h_tilem');

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
        $ekaWaraController = new EkaWaraController_01();
        $dwiWaraController = new DwiWaraController_02();
        $triWaraController = new TriWaraController_03();
        $caturWaraController = new CaturWaraController_04();
        $pancaWaraController = new PancaWaraController_05();
        $sadWaraController = new SadWaraController_06();
        $saptawaraWaraController = new SaptaWaraController_07();
        $astaWaraController = new AstaWaraController_08();
        $sangaWaraController = new SangaWaraController_09();
        $dasaWaraController = new DasaWaraController();
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController();
        $purnamaTilemController = new PurnamaTilemController();
        
        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);

        $saptawara = $saptawaraWaraController->getSaptawara($tanggal);
        $urip_saptawara = $saptawaraWaraController->getUripSaptaWara($saptawara);
        $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
        $urip_pancawara = $pancaWaraController->getUripPancaWara($pancawara);
        $triwara = $triWaraController->gettriwara($hasilAngkaWuku);

        $ekawara = $ekaWaraController->getEkaWara($urip_pancawara, $urip_saptawara);
        // $namaEkawara = $triWaraController->getNamaEkaWara($ekawara);
        $dwiwara = $dwiWaraController->getDwiWara($urip_pancawara, $urip_saptawara);
        // $namaDwiwara = $dwiWaraController->getNamaDwiWara($dwiwara);
        $caturwara = $caturWaraController->getCaturWara($hasilAngkaWuku);
        // $namaCaturwara = $caturWaraController->getNamaCaturWara($caturwara);
        $sadwara = $sadWaraController->getSadWara($hasilAngkaWuku);
        // $namaSadwara = $sadWaraController->getNamaSadWara($sadwara);
        $astawara = $astaWaraController->getAstaWara($hasilAngkaWuku);
        // $namaAstawara = $astaWaraController->getNamaAstaWara($astawara);
        $sangawara = $sangaWaraController->getSangaWara($hasilAngkaWuku);
        // $namaSangawara = $sangaWaraController->getNamaSangaWara($sangawara);
        $dasawara = $dasaWaraController->getDasawara($urip_pancawara, $urip_saptawara);
        // $namaDasawara = $dasaWaraController->getNamaDasaWara($dasawara);

        $pengalantaka_dan_hariSasih = $hariSasihController->getHariSasih($tanggal, $refTanggal, $penanggal, $noNgunaratri);
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
        $purnama_tilem = $purnamaTilemController->getPurnamaTilem($pengalantaka_dan_hariSasih['pengalantaka'], $pengalantaka_dan_hariSasih['penanggal_1'], $pengalantaka_dan_hariSasih['penanggal_2']);
        // $no_sasih['no_sasih']
        $pengalantaka = $pengalantaka_dan_hariSasih['pengalantaka'];
        $sasihDay1 = $pengalantaka_dan_hariSasih['penanggal_1'];
        $sasihDay2 = $pengalantaka_dan_hariSasih['penanggal_2'];
        $dewasaAyu = '';

        // 1. AgniAgungDoyanBasmi: Selasa Purnama dengan Asta Wara Brahma
        if (($saptawara === 3 && ($astawara === 6 || $purnama_tilem === 'Purnama'))) {
            $dewasaAyu = ', Agni Agung Doyan Basmi';
        }

        // 2. AgniAgungPatraLimutan: Minggu dengan Asta Wara Brahma
        if ($saptawara === 1 && $astawara === 6) {
            $dewasaAyu .= ', Agni Agung Patra Limutan';
        }

        // 3. Amerta Akasa: Anggara Purnama
        if ($saptawara === 3 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Amerta Akasa';
        }

        // 4. Amerta Bumi: Soma Wage Penanggal 1. Buda Pon Penanggal 10.
        if (($saptawara === 2 && $pancawara === 4 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($saptawara === 4 && $pancawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 10 || $sasihDay2 === 10))
        ) {
            $dewasaAyu .= ', Amerta Bumi';
        }

        // 5. Amerta Bhuwana: Redite Purnama, Soma Purnama, dan Anggara Purnama
        if (($saptawara === 1 || $saptawara === 2 || $saptawara === 3) && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Amerta Bhuwana';
        }

        // 6. Amerta Dadi: Soma Beteng atau Purnama Kajeng
        if (($saptawara === 2 && $triwara === 2) || ($triwara === 3 && $purnama_tilem === 'Purnama')) {
            $dewasaAyu .= ', Amerta Dadi';
        }

        // 7. Amerta Danta
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 2 && (($purnama_tilem === 'Purnama') || (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 10)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 3 || $sasihDay1 === 10)))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7)))
        ) {
            $dewasaAyu .= ', Amerta Danta';
        }

        // 8. Amerta Dewa
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 6))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 7) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 7))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 3) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 3))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 2) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 2))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 5))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 1) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 1))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4)))
        ) {
            $dewasaAyu .= ', Amerta Dewa';
        }

        // 9. Amerta Dewa Jaya
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay1 === 12 || $sasihDay2 === 12)))) {
            $dewasaAyu .= ', Amerta Dewa Jaya';
        }

        // 10. Amerta Dewata
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            $dewasaAyu .= ', Amerta Dewata';
        }

        // 11. Amerta Gati
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 6 || $sasihDay1 === 3 || $sasihDay2 === 6 || $sasihDay2 === 3)))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 7 || $sasihDay2 === 7)))) ||
            ($saptawara === 3 && ($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3))) ||
            ($saptawara === 4 && ((($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3)))) ||
                ($saptawara === 5 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5)))) ||
                ($saptawara === 6 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1)))) ||
                ($saptawara === 7 && ($pengalantaka === 'Penanggal' && ($sasihDay1 === 7 || $sasihDay2 === 7 || $sasihDay1 === 4 || $sasihDay2 === 4)))
            )
        ) {
            $dewasaAyu .= ', Amerta Gati';
        }

        // 12. Amerta Kundalini
        if (
            ($saptawara === 2 && $wuku === 24 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 8 || $sasihDay2 === 8)) ||
            ($saptawara === 2 && $wuku === 29 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 7 || $sasihDay2 === 7 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 2 && $wuku === 8 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5)) ||
            ($saptawara === 4 && $wuku === 2 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 12 || $sasihDay2 === 12)) ||
            ($saptawara === 4 && $wuku === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 10 || $sasihDay2 === 10)) ||
            ($saptawara === 4 && $wuku === 8 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 4 && $wuku === 9 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 13 || $sasihDay2 === 13)) ||
            ($saptawara === 4 && $wuku === 15 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 12 || $sasihDay2 === 12)) ||
            ($saptawara === 5 && $wuku === 2 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 10 || $sasihDay2 === 10)) ||
            ($saptawara === 5 && $wuku === 20 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 5 && $wuku === 13 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 11 || $sasihDay2 === 11))
        ) {
            $dewasaAyu .= ', Amerta Kundalini';
        }

        // 13. Amerta Masa
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Amerta Masa';
        }

        // 14. Amerta Murti
        if ($saptawara === 4 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            $dewasaAyu .= ', Amerta Murti';
        }

        // 15. Amerta Pageh
        if ($saptawara === 7 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Amerta Pageh';
        }

        // 16. Amerta Pepageran
        if ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $astawara === 4)) {
            $dewasaAyu .= ', Amerta Pepageran';
        }

        // 17. Amerta Sari
        if ($saptawara === 4 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Amerta Sari';
        }

        // 18. Amerta Wija
        if ($saptawara === 5 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Amerta Wija';
        }

        // 19. Amerta Yoga
        if (
            ($saptawara === 2 && ($wuku === 2 || $wuku === 5 || $wuku === 14 || $wuku === 17 || $wuku === 20 || $wuku === 23 || $wuku === 26 || $wuku === 29)) ||
            ($saptawara === 5 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 5))) ||
            (($no_sasih === 10) && (($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4))) ||
            (($no_sasih === 12) && (($pengalantaka === 'Pangelong' && $sasihDay1 === 1) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 1)))
        ) {
            $dewasaAyu .= ', Amerta Yoga';
        }

        // 20. Asuajag Munggah
        if (
            ($saptawara === 1 && $wuku === 6) ||
            ($saptawara === 2 && $wuku === 23) ||
            ($saptawara === 3 && $wuku === 10) ||
            ($saptawara === 4 && $wuku === 27) ||
            ($saptawara === 5 && $wuku === 14) ||
            ($saptawara === 6 && $wuku === 1) ||
            ($saptawara === 7 && $wuku === 18)
        ) {
            $dewasaAyu .= ', Asuajag Munggah';
        }

        // 21. Asuajag Turun
        if (
            ($saptawara === 1 && $wuku === 21) ||
            ($saptawara === 2 && $wuku === 8) ||
            ($saptawara === 3 && $wuku === 25) ||
            ($saptawara === 4 && $wuku === 12) ||
            ($saptawara === 5 && $wuku === 29) ||
            ($saptawara === 6 && $wuku === 16) ||
            ($saptawara === 7 && $wuku === 3)
        ) {
            $dewasaAyu .= ', Asuajag Turun';
        }

        // 22. Asuasa
        if (
            ($saptawara === 1 && $wuku === 3) ||
            ($saptawara === 1 && $wuku === 15) ||
            ($saptawara === 2 && $wuku === 14) ||
            ($saptawara === 3 && $wuku === 7) ||
            ($saptawara === 4 && $wuku === 24) ||
            ($saptawara === 5 && $wuku === 11) ||
            ($saptawara === 6 && $wuku === 28)
        ) {
            $dewasaAyu .= ', Asuasa';
        }

        // 23. Ayu Bhadra
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 7 || $sasihDay1 === 10)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 7 || $sasihDay2 === 10)))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11)))
        ) {
            $dewasaAyu .= ', Ayu Bhadra';
        }

        // 24. Ayu Dana
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Ayu Dana';
        }

        // 25. Ayu Nulus
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 12 || $sasihDay1 === 13)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 12 || $sasihDay2 === 13)))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            $dewasaAyu .= ', Ayu Nulus';
        }

        // 26. Babi Munggah
        if ($pancawara === 4 && $sadwara === 1) {
            $dewasaAyu .= ', Babi Munggah';
        }

        // 27. Babi Turun
        if ($pancawara === 4 && $sadwara === 4) {
            $dewasaAyu .= ', Babi Turun';
        }

        // 28. Banyu Milir
        if (
            ($saptawara === 1 && $wuku === 4) ||
            ($saptawara === 2 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 13)
        ) {
            $dewasaAyu .= ', Banyu Milir';
        }

        // 29. Banyu Urung
        if (
            ($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 2 && ($wuku === 1 || $wuku === 2 || $wuku === 8 || $wuku === 10 || $wuku === 17 || $wuku === 18 || $wuku === 20 || $wuku === 22)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 5 || $wuku === 14 || $wuku === 16 || $wuku === 17 || $wuku === 18 || $wuku === 23 || $wuku === 21)) ||
            ($saptawara === 4 && ($wuku === 28 || $wuku === 5 || $wuku === 10 || $wuku === 19 || $wuku === 21)) ||
            ($saptawara === 5 && ($wuku === 5 || $wuku === 6 || $wuku === 15 || $wuku === 19 || $wuku === 20 || $wuku === 22 || $wuku === 24)) ||
            ($saptawara === 6 && ($wuku === 28 || $wuku === 29 || $wuku === 6 || $wuku === 11 || $wuku === 15 || $wuku === 17)) ||
            ($saptawara === 7 && ($wuku === 4 || $wuku === 8 || $wuku === 19))
        ) {
            $dewasaAyu .= ', Banyu Urung';
        }

        // 30. Bojog Munggah
        if ($pancawara === 5 && $sadwara === 5) {
            $dewasaAyu .= ', Bojog Munggah';
        }

        // 31. Bojog Turun
        if ($pancawara === 5 && $sadwara === 2) {
            $dewasaAyu .= ', Bojog Turun';
        }

        // 32. Buda Gajah
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Buda Gajah';
        }

        // 33. Buda Ireng
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Tilem') {
            $dewasaAyu .= ', Buda Ireng';
        }

        // 34. Buda Suka
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Tilem') {
            $dewasaAyu .= ', Buda Suka';
        }

        // 35. Carik Walangati
        if (
            $wuku === 1 || $wuku === 6 || $wuku === 10 || $wuku === 12 || $wuku === 24 ||
            $wuku === 25 || $wuku === 27 || $wuku === 28 || $wuku === 30 || $wuku === 7
        ) {
            $dewasaAyu .= ', Carik Walangati';
        }

        // 36. Catur Laba
        if (
            ($saptawara === 1 && $pancawara === 1) ||
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 4 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 2)
        ) {
            $dewasaAyu .= ', Catur Laba';
        }

        // 37. Cintamanik
        if ($saptawara === 4 && ($wuku % 2 === 1)) {
            $dewasaAyu .= ', Cintamanik';
        }

        // 38. Corok Kodong
        if ($saptawara === 5 && $pancawara === 5 && $wuku === 13) {
            $dewasaAyu .= ', Corok Kodong';
        }

        // 39. DagDig Karana
        if (
            ($saptawara === 1 && ($sasihDay1 === 2 || $sasihDay2 === 2)) ||
            ($saptawara === 2 && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($saptawara === 3 && ($sasihDay1 === 10 || $sasihDay2 === 10)) ||
            ($saptawara === 4 && ($sasihDay1 === 7 || $sasihDay2 === 7)) ||
            ($saptawara === 5 && ($sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 7 && ($sasihDay1 === 6 || $sasihDay2 === 6))
        ) {
            $dewasaAyu .= ', DagDig Karana';
        }

        // 40. Dasa Amertha
        if ($saptawara === 6 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            $dewasaAyu .= ', Dasa Amertha';
        }

        // 41. Dasa Guna
        if ($saptawara === 4 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem')) {
            $dewasaAyu .= ', Dasa Guna';
        }

        // 42. Dauh Ayu
        if (
            ($saptawara === 1 && ($sasihDay1 === 4 || $sasihDay2 === 4 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 2 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 5 || $sasihDay2 === 5)) ||
            ($saptawara === 3 && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 7 || $sasihDay2 === 7 || $sasihDay1 === 8 || $sasihDay2 === 8)) ||
            ($saptawara === 4 && ($sasihDay1 === 4 || $sasihDay2 === 4)) ||
            ($saptawara === 5 && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 4 || $sasihDay2 === 4)) ||
            ($saptawara === 6 && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 7 && ($sasihDay1 === 5 || $sasihDay2 === 5))
        ) {
            $dewasaAyu .= ', Dauh Ayu';
        }

        // 43. Derman Bagia
        if ($saptawara === 2 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 12 || $sasihDay2 === 12)) {
            $dewasaAyu .= ', Derman Bagia';
        }

        // 44. Dewa Ngelayang
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 3 || $sasihDay2 === 7)))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 13 || $sasihDay1 === 15)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 3 || $sasihDay2 === 13 || $sasihDay2 === 15)))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4)))
        ) {
            $dewasaAyu .= ', Dewa Ngelayang';
        }

        // 45. Dewa Satata
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            $dewasaAyu .= ', Dewa Satata';
        }

        // 46. Dewa Werdhi
        if ($saptawara === 6 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            $dewasaAyu .= ', Dewa Werdhi';
        }

        // 47. Dewa Mentas
        if ($saptawara === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) {
            $dewasaAyu .= ', Dewa Mentas';
        }

        // 48. Dewasa Ngelayang
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 8)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 8)))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3)))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            $dewasaAyu .= ', Dewasa Ngelayang';
        }

        // 49. Dewasa Tanian
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            $dewasaAyu .= ', Dewasa Tanian';
        }

        // 50. Dina Carik
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6)))
        ) {
            $dewasaAyu .= ', Dina Carik';
        }

        // 51. Dina Jaya
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7)))
        ) {
            $dewasaAyu .= ', Dina Jaya';
        }

        // 52. Dina Mandi
        if (
            ($saptawara === 3 && $purnama_tilem === 'Purnama') ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3)))
        ) {
            $dewasaAyu .= ', Dina Mandi';
        }

        // 53. Dirgahayu
        if ($saptawara === 3 && $pancawara === 3 && $dasawara === 1) {
            $dewasaAyu .= ', Dirgahayu';
        }

        // 54. DirghaYusa
        if ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            $dewasaAyu .= ', Dirgha Yusa';
        }

        // 55. Gagak Anungsung Pati
        if (
            ($pengalantaka === 'Penanggal' && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 14 || $sasihDay2 === 14))
        ) {
            $dewasaAyu .= ', Gagak Anungsung Pati';
        }

        // 56. Geheng Manyinget
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) ||
            ($saptawara === 2 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 7) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 7))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 10)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 10)))) ||
            ($saptawara === 4 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 10) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 10))) ||
            ($saptawara === 5 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 5))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 9)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 9))))
        ) {
            $dewasaAyu .= ', Geheng Manyinget';
        }

        // 57. Geni Agung
        if (
            ($saptawara === 1 && $pancawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 3 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14)))
        ) {
            $dewasaAyu .= ', Geni Agung';
        }

        // 58. Geni Murub
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6)))
        ) {
            $dewasaAyu .= ', Geni Murub';
        }

        // 59. Geni Rawana
        if (
            (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11))) ||
            (($pengalantaka === 'Pangelong' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13)) || ($pengalantaka === 'Pangelong' && ($sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            $dewasaAyu .= ', Geni Rawana';
        }

        // 60. Geni Rawana Jejepan
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6)))
        ) {
            $dewasaAyu .= ', Geni Rawana Jejepan';
        }

        // 61. Geni Rawana Rangkep
        if (
            (($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11 || $sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11)) || ($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13 || $sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            $dewasaAyu .= ', Geni Rawana Rangkep';
        }

        // 62. Guntur Graha
        if (
            ($saptawara === 4 && $wuku === 2) ||
            ($saptawara === 4 && $wuku === 5) ||
            ($saptawara === 5 && $wuku === 14) ||
            ($saptawara === 5 && $wuku === 18) ||
            ($saptawara === 7 && $wuku === 20) ||
            ($saptawara === 7 && $wuku === 26)
        ) {
            $dewasaAyu .= ', Guntur Graha';
        }

        // 63. Ingkel Macan
        if ($saptawara === 5 && $pancawara === 3 && $wuku === 7) {
            $dewasaAyu .= ', Ingkel Macan';
        }

        // 64. Istri Payasan
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) {
            $dewasaAyu .= ', Istri Payasan';
        }

        // 65. Jiwa Manganti
        if (($saptawara === 2 && $wuku === 19) || ($saptawara === 5 && ($wuku === 2 || $wuku === 20)) || ($saptawara === 6 && ($wuku === 25 || $wuku === 7)) || ($saptawara === 7 && $wuku === 30)) {
            $dewasaAyu .= ', Jiwa Manganti';
        }

        // 66. Kajeng Kipkipan
        if ($saptawara === 4 && ($wuku === 6 || $wuku === 30)) {
            $dewasaAyu .= ', Kajeng Kipkipan';
        }

        // 67. Kajeng Kliwon Enyitan
        if ($triwara === 3 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 < 15 && $sasihDay1 > 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 < 15 && $sasihDay2 > 7))) {
            $dewasaAyu .= ', Kajeng Kliwon Enyitan';
        }

        // 68. Kajeng Lulunan
        if ($triwara === 3 && $astawara === 5 && $sangawara === 9) {
            $dewasaAyu .= ', Kajeng Lulunan';
        }

        // 69. Kajeng Rendetan
        if ($triwara === 3 && $pengalantaka === 'Penanggal' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            $dewasaAyu .= ', Kajeng Rendetan';
        }

        // 70. Kajeng Susunan
        if ($triwara === 3 && $astawara === 3 && $sangawara === 9) {
            $dewasaAyu .= ', Kajeng Susunan';
        }

        // 71. Kajeng Uwudan
        if ($triwara === 3 && $pengalantaka === 'Pangelong' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            $dewasaAyu .= ', Kajeng Uwudan';
        }

        // 72. Kala Alap
        if ($saptawara === 2 && $wuku === 22) {
            $dewasaAyu .= ', Kala Alap';
        }

        // 73. Kala Angin
        if ($saptawara === 1 && ($wuku === 17 || $wuku === 25 || $wuku === 28)) {
            $dewasaAyu .= ', Kala Angin';
        }

        // 74. Kala Atat
        if (($saptawara === 1 && $wuku === 22) || ($saptawara === 3 && $wuku === 30) || ($saptawara === 4 && $wuku === 19)) {
            $dewasaAyu .= ', Kala Atat';
        }

        // 75. Kala Awus
        if ($saptawara === 4 && $wuku === 28) {
            $dewasaAyu .= ', Kala Awus';
        }

        // 76. Kala Bancaran
        if (
            $saptawara === 1 && $wuku === 11 ||
            $saptawara === 2 && $wuku === 1 ||
            $saptawara === 3 && ($wuku === 5 || $wuku === 11 || $wuku === 19) ||
            $saptawara === 5 && $wuku === 21 ||
            $saptawara === 6 && $wuku === 12 ||
            $saptawara === 7 && $wuku === 7
        ) {
            $dewasaAyu .= ', Kala Bancaran';
        }

        // 77. Kala Bangkung, Kala Nanggung
        if (
            $saptawara === 1 && $pancawara === 3 ||
            $saptawara === 2 && $pancawara === 2 ||
            $saptawara === 4 && $pancawara === 1 ||
            $saptawara === 7 && $pancawara === 4
        ) {
            $dewasaAyu .= ', Kala Bangkung, Kala Nanggung';
        }

        // 78. Kala Beser
        if ($sadwara === 1 && $astawara === 7) {
            $dewasaAyu .= ', Kala Beser';
        }

        // 79. Kala Brahma
        if (
            $saptawara === 1 && $wuku === 23 ||
            $saptawara === 3 && $wuku === 14 ||
            $saptawara === 4 && $wuku === 1 ||
            $saptawara === 6 && ($wuku === 4 || $wuku === 25 || $wuku === 30) ||
            $saptawara === 7 && $wuku === 13
        ) {
            $dewasaAyu .= ', Kala Brahma';
        }

        // 80. Kala Bregala
        if ($saptawara === 2 && $wuku === 2) {
            $dewasaAyu .= ', Kala Bregala';
        }

        // 81. Kala Buingrau
        if (($saptawara === 1 && $astawara === 2) ||
            ($saptawara === 2 && $astawara === 8) ||
            ($saptawara === 3 && $astawara === 5) ||
            ($saptawara === 4 && $astawara === 6) ||
            ($saptawara === 5 && $astawara === 3) ||
            ($saptawara === 6 && $astawara === 1) ||
            ($saptawara === 7 && $astawara === 4)
        ) {
            $dewasaAyu .= ', Kala Buingrau';
        }

        // 82. Kala Cakra
        if ($saptawara === 7 && $wuku === 23) {
            $dewasaAyu .= ', Kala Cakra';
        }

        // 83. Kala Capika
        if ($saptawara === 1 && $wuku === 18 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) {
            $dewasaAyu .= ', Kala Capika';
        }

        // 84. Kala Caplokan
        if (($saptawara === 2 && ($wuku === 18 || $wuku === 9)) ||
            ($saptawara === 3 && $wuku === 19) ||
            ($saptawara === 4 && $wuku === 24) ||
            ($saptawara === 6 && $wuku === 12) ||
            ($saptawara === 7 && ($wuku === 9 || $wuku === 15 || $wuku === 1))
        ) {
            $dewasaAyu .= ', Kala Caplokan';
        }

        // 85. Kala Cepitan
        if ($saptawara === 2 && $pancawara === 2 && $wuku === 18) {
            $dewasaAyu .= ', Kala Cepitan';
        }

        // 86. Kala Dangastra
        if (($saptawara === 1 && ($wuku === 4 || $wuku === 23)) ||
            ($saptawara === 2 && ($wuku === 10 || $wuku === 29)) ||
            ($saptawara === 3 && ($wuku === 14 || $wuku === 16 || $wuku === 18)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 20)) ||
            ($saptawara === 5 && $wuku === 11) ||
            ($saptawara === 6 && ($wuku === 4 || $wuku === 11 || $wuku === 25 || $wuku === 30)) ||
            ($saptawara === 7 && ($wuku === 13 || $wuku === 15 || $wuku === 17))
        ) {
            $dewasaAyu .= ', Kala Dangastra';
        }

        // 87. Kala Dangu
        if (($saptawara === 1 && ($wuku === 5 || $wuku === 13 || $wuku === 22 || $wuku === 27)) ||
            ($saptawara === 2 && $wuku === 18) ||
            ($saptawara === 3 && ($wuku === 3 || $wuku === 6 || $wuku === 11 || $wuku === 17)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 9 || $wuku === 19 || $wuku === 28)) ||
            ($saptawara === 5 && ($wuku === 7 || $wuku === 15 || $wuku === 24)) ||
            ($saptawara === 6 && ($wuku === 11 || $wuku === 21 || $wuku === 23 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 8 || $wuku === 10 || $wuku === 11 ||
                $wuku === 14 || $wuku === 16 || $wuku === 20 || $wuku === 25 ||
                $wuku === 29 || $wuku === 30))
        ) {
            $dewasaAyu .= ', Kala Dangu';
        }

        // 88. Kala Demit
        if ($saptawara === 7 && $wuku === 3) {
            $dewasaAyu .= ', Kala Demit';
        }

        // 89. Kala Empas Munggah
        if ($pancawara === 4 && $sadwara === 3) {
            $dewasaAyu .= ', Kala Empas Munggah';
        }

        // 90. Kala Empas Turun
        if ($pancawara === 4 && $sadwara === 6) {
            $dewasaAyu .= ', Kala Empas Turun';
        }

        // 91. Kala Gacokan
        if ($saptawara === 3 && $wuku === 19) {
            $dewasaAyu .= ', Kala Gacokan';
        }

        // 92. Kala Garuda
        if ($saptawara === 3 && $wuku === 2) {
            $dewasaAyu .= ', Kala Garuda';
        }

        // 93. Kala Geger
        if (($saptawara === 5 || $saptawara === 7) && $wuku === 7) {
            $dewasaAyu .= ', Kala Geger';
        }

        // 94. Kala Gotongan
        if (($saptawara === 6 && $pancawara === 5) ||
            ($saptawara === 7 && $pancawara === 1) ||
            ($saptawara === 1 && $pancawara === 2)
        ) {
            $dewasaAyu .= ', Kala Gotongan';
        }

        // 95. Kala Graha
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 5)
        ) {
            $dewasaAyu .= ', Kala Graha';
        }

        // 96. Kala Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 3) {
            $dewasaAyu .= ', Kala Gumarang Munggah';
        }

        // 97. Kala Gumarang Turun
        if ($pancawara === 3 && $sadwara === 6) {
            $dewasaAyu .= ', Kala Gumarang Turun';
        }

        // 98. Kala Guru
        if ($saptawara === 4 && $wuku === 2) {
            $dewasaAyu .= ', Kala Guru';
        }

        // 99. Kala Ingsor
        if ($wuku === 4 || $wuku === 14 || $wuku === 24) {
            $dewasaAyu .= ', Kala Ingsor';
        }

        // 100. Kala Isinan
        if (($saptawara === 2 && ($wuku === 11 || $wuku === 17)) ||
            ($saptawara === 4 && $wuku === 30)
        ) {
            $dewasaAyu .= ', Kala Isinan';
        }

        // 101. Kala Jangkut
        if ($triwara === 3 && $dwiwara === 2) {
            $dewasaAyu .= ', Kala Jangkut';
        }

        // 102. Kala Jengkang
        if ($saptawara === 1 && $pancawara === 1 && $wuku === 3) {
            $dewasaAyu .= ', Kala Jengkang';
        }

        // 103. Kala Jengking
        if ($sadwara === 3 && $astawara === 7) {
            $dewasaAyu .= ', Kala Jengking';
        }

        // 104. Kala Katemu
        if (($saptawara === 1 && ($wuku === 1 || $wuku === 9 || $wuku === 15)) ||
            ($saptawara === 2 && ($wuku === 3 || $wuku === 5 || $wuku === 17)) ||
            ($saptawara === 3 && ($wuku === 11 || $wuku === 16 || $wuku === 19 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 13 || $wuku === 29 || $wuku === 5 || $wuku === 7)) ||
            ($saptawara === 5 && ($wuku === 15 || $wuku === 1 || $wuku === 9)) ||
            ($saptawara === 6 && ($wuku === 17 || $wuku === 3)) ||
            ($saptawara === 7 && ($wuku === 16 || $wuku === 19 || $wuku === 27 || $wuku === 5 || $wuku === 11))
        ) {
            $dewasaAyu .= ', Kala Katemu';
        }

        // 105. Kala Keciran
        if ($saptawara === 4 && $wuku === 6) {
            $dewasaAyu .= ', Kala Keciran';
        }

        // 106. Kala Kilang-Kilung
        if (($saptawara === 2 && $wuku === 17) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            $dewasaAyu .= ', Kala Kilang-Kilung';
        }

        // 107. Kala Kingkingan
        if ($saptawara === 5 && $wuku === 17) {
            $dewasaAyu .= ', Kala Kingkingan';
        }

        // 108. Kala Klingkung
        if ($saptawara === 3 && $wuku === 1) {
            $dewasaAyu .= ', Kala Klingkung';
        }

        // 109. Kala Kutila Manik
        if ($triwara === 3 && $pancawara === 5) {
            $dewasaAyu .= ', Kala Kutila Manik';
        }

        // 110. Kala Kutila
        if ($sadwara === 2 && $astawara === 6) {
            $dewasaAyu .= ', Kala Kutila';
        }

        // 111. Kala Luang
        if (($saptawara === 1 && ($wuku === 11 || $wuku === 12 || $wuku === 13)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 10 || $wuku === 8 || $wuku === 19 || $wuku === 23 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 18)) ||
            ($saptawara === 5 && ($wuku === 28 || $wuku === 29))
        ) {
            $dewasaAyu .= ', Kala Luang';
        }

        // 112. Kala Lutung Megelut
        if (($saptawara === 1 && $wuku === 3) || ($saptawara === 4 && $wuku === 10)) {
            $dewasaAyu .= ', Kala Lutung Megelut';
        }

        // 113. Kala Lutung Megandong
        if ($saptawara === 5 && $pancawara === 5) {
            $dewasaAyu .= ', Kala Lutung Megandong';
        }

        // 114. Kala Macan
        if ($saptawara === 5 && $wuku === 19) {
            $dewasaAyu .= ', Kala Macan';
        }

        // 115. Kala Mangap
        if ($saptawara === 1 && $pancawara === 1) {
            $dewasaAyu .= ', Kala Mangap';
        }

        // 116. Kala Manguneb
        if ($saptawara === 5 && $pancawara === 14) {
            $dewasaAyu .= ', Kala Manguneb';
        }

        // 117. Kala Matampak
        if (($saptawara === 4 && $wuku === 3) ||
            ($saptawara === 5 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 3) ||
            ($saptawara === 7 && ($wuku === 7 || $wuku === 24))
        ) {
            $dewasaAyu .= ', Kala Matampak';
        }

        // 118. Kala Mereng
        if (($saptawara === 1 && ($wuku === 9 || $wuku === 24)) ||
            ($saptawara === 2 && ($wuku === 11 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 13)) ||
            ($saptawara === 4 && ($wuku === 15 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 2 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 21))
        ) {
            $dewasaAyu .= ', Kala Mereng';
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            $dewasaAyu .= ', Kala Miled';
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            $dewasaAyu .= ', Kala Mina';
        }

        // 121. Kala Mretyu
        if (($saptawara === 1 && ($wuku === 1 || $wuku === 18)) ||
            ($saptawara === 2 && ($wuku === 23)) ||
            ($saptawara === 3 && ($wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 1)) ||
            ($saptawara === 5 && ($wuku === 5)) ||
            ($saptawara === 6 && ($wuku === 9)) ||
            ($saptawara === 7 && ($wuku === 14))
        ) {
            $dewasaAyu .= ', Kala Mretyu';
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            $dewasaAyu .= ', Kala Muas';
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            $dewasaAyu .= ', Kala Muncar';
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            $dewasaAyu .= ', Kala Muncrat';
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            $dewasaAyu .= ', Kala Ngadeg';
        }

        // 118. Kala Mereng
        if (($saptawara === 1 && ($wuku === 9 || $wuku === 24)) ||
            ($saptawara === 2 && ($wuku === 11 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 13)) ||
            ($saptawara === 4 && ($wuku === 15 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 2 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 21))
        ) {
            $dewasaAyu .= ', Kala Mereng';
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            $dewasaAyu .= ', Kala Miled';
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            $dewasaAyu .= ', Kala Mina';
        }

        // 121. Kala Mretyu
        if (($saptawara === 1 && ($wuku === 1 || $wuku === 18)) ||
            ($saptawara === 2 && ($wuku === 23)) ||
            ($saptawara === 3 && ($wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 1)) ||
            ($saptawara === 5 && ($wuku === 5)) ||
            ($saptawara === 6 && ($wuku === 9)) ||
            ($saptawara === 7 && ($wuku === 14))
        ) {
            $dewasaAyu .= ', Kala Mretyu';
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            $dewasaAyu .= ', Kala Muas';
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            $dewasaAyu .= ', Kala Muncar';
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            $dewasaAyu .= ', Kala Muncrat';
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            $dewasaAyu .= ', Kala Ngadeg';
        }

        // 126. Kala Ngamut
        if ($saptawara === 2 && $wuku === 18) {
            $dewasaAyu .= ', Kala Ngamut';
        }

        // 127. Kala Ngruda
        if (($saptawara === 1 && ($wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 23 || $wuku === 10)) ||
            ($saptawara === 7 && ($wuku === 10))
        ) {
            $dewasaAyu .= ', Kala Ngruda';
        }

        // 128. Kala Ngunya
        if ($saptawara === 1 && $wuku === 3) {
            $dewasaAyu .= ', Kala Ngunya';
        }

        // 129. Kala Olih
        if ($saptawara === 4 && $wuku === 24) {
            $dewasaAyu .= ', Kala Olih';
        }

        // 130. Kala Pacekan
        if ($saptawara === 3 && $wuku === 5) {
            $dewasaAyu .= ', Kala Pacekan';
        }

        // 131. Kala Pager
        if ($saptawara === 5 && $wuku === 7) {
            $dewasaAyu .= ', Kala Pager';
        }

        // 132. Kala Panyeneng
        if (($saptawara === 1 && $wuku === 7) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            $dewasaAyu .= ', Kala Panyeneng';
        }

        // 133. Kala Pati
        if (($saptawara === 1 && ($wuku === 10 || $wuku === 2)) ||
            ($saptawara === 3 && ($wuku === 6 || $wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 10 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 17))
        ) {
            $dewasaAyu .= ', Kala Pati';
        }

        // 134. Kala Pati Jengkang
        if ($saptawara === 5 && $sadwara === 3) {
            $dewasaAyu .= ', Kala Pati Jengkang';
        }

        // 135. Kala Pegat
        if (
            $saptawara === 4 && $wuku === 12 ||
            $saptawara === 7 && ($wuku === 3 || $wuku === 18)
        ) {
            $dewasaAyu .= ', Kala Pegat';
        }

        // 136. Kala Prawani
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 3 && $wuku === 24) ||
            ($saptawara === 4 && $wuku === 2) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            $dewasaAyu .= ', Kala Prawani';
        }

        // 137. Kala Raja
        if ($saptawara === 5 && $wuku === 29) {
            $dewasaAyu .= ', Kala Raja';
        }

        // 138. Kala Rau
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 7 && ($wuku === 3 || $wuku === 4 || $wuku === 18)) ||
            ($saptawara === 6 && $wuku === 6)
        ) {
            $dewasaAyu .= ', Kala Rau';
        }

        // 139. Kala Rebutan
        if ($saptawara === 2 && $wuku === 26) {
            $dewasaAyu .= ', Kala Rebutan';
        }

        // 140. Kala Rumpuh
        if (($saptawara === 1 && ($wuku === 18 || $wuku === 30)) ||
            ($saptawara === 2 && ($wuku === 9 || $wuku === 20)) ||
            ($saptawara === 4 && ($wuku === 10 || $wuku === 19 || $wuku === 25 || $wuku === 26 || $wuku === 27)) ||
            ($saptawara === 5 && ($wuku === 13 || $wuku === 14 || $wuku === 17 || $wuku === 22 || $wuku === 24)) ||
            ($saptawara === 6 && ($wuku === 11 || $wuku === 12)) ||
            ($saptawara === 7 && ($wuku === 21 || $wuku === 23 || $wuku === 28 || $wuku === 29))
        ) {
            $dewasaAyu .= ', Kala Rumpuh';
        }

        // 141. Kala Sapuhau
        if (($saptawara === 2 && $wuku === 3) ||
            ($saptawara === 3 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            $dewasaAyu .= ', Kala Sapuhau';
        }

        // 142. Kala Sarang
        if ($wuku === 7 || $wuku === 17) {
            $dewasaAyu .= ', Kala Sarang';
        }

        // 143. Kala Siyung
        if (($saptawara === 1 && ($wuku === 2 || $wuku === 21)) ||
            ($saptawara === 2 && ($wuku === 1 || $wuku === 10 || $wuku === 25)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 20)) ||
            ($saptawara === 5 && ($wuku === 24 || $wuku === 26)) ||
            ($saptawara === 6 && $wuku === 28) ||
            ($saptawara === 7 && ($wuku === 15 || $wuku === 17))
        ) {
            $dewasaAyu .= ', Kala Siyung';
        }

        // 144. Kala Sor
        if (($saptawara === 1 && ($wuku === 3 || $wuku === 9 || $wuku === 15 || $wuku === 21 || $wuku === 27)) ||
            ($saptawara === 2 && ($wuku === 1 || $wuku === 2 || $wuku === 8 || $wuku === 6 ||
                $wuku === 11 || $wuku === 14 || $wuku === 16 || $wuku === 20 ||
                $wuku === 21 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 9 || $wuku === 1 || $wuku === 4 || $wuku === 7 || $wuku === 13 ||
                $wuku === 14 || $wuku === 24 || $wuku === 25 || $wuku === 29)) ||
            ($saptawara === 4 && ($wuku === 3 || $wuku === 8 || $wuku === 12 || $wuku === 13 ||
                $wuku === 18 || $wuku === 23 || $wuku === 24 || $wuku === 28 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 5 || $wuku === 11 || $wuku === 17 || $wuku === 23 || $wuku === 29)) ||
            ($saptawara === 6 && ($wuku === 10 || $wuku === 8 || $wuku === 3 || $wuku === 4 || $wuku === 13 ||
                $wuku === 16 || $wuku === 18 || $wuku === 22 || $wuku === 23 || $wuku === 28)) ||
            ($saptawara === 7 && ($wuku === 9 || $wuku === 3 || $wuku === 15 || $wuku === 21 || $wuku === 27))
        ) {
            $dewasaAyu .= ', Kala Sor';
        }

        // 145. Kala Sudangastra
        if (($saptawara === 1 && $wuku === 24) ||
            ($saptawara === 3 && $wuku === 28) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 12)) ||
            ($saptawara === 5 && $wuku === 19) ||
            ($saptawara === 7 && $wuku === 6)
        ) {
            $dewasaAyu .= ', Kala Sudangastra';
        }

        // 146. Kala Sudukan
        if (($saptawara === 1 && $wuku === 12) ||
            ($saptawara === 2 && ($wuku === 2 || $wuku === 3 || $wuku === 22 || $wuku === 25)) ||
            ($saptawara === 3 && ($wuku === 6 || $wuku === 8 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 20)) ||
            ($saptawara === 5 && $wuku === 21) ||
            ($saptawara === 6 && ($wuku === 5 || $wuku === 24 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 14 || $wuku === 15 || $wuku === 16 || $wuku === 17))
        ) {
            $dewasaAyu .= ', Kala Sudukan';
        }

        // 147. Kala Sungsang
        if ($wuku === 27) {
            $dewasaAyu .= ', Kala Sungsang';
        }

        // 148. Kala Susulan
        if ($saptawara === 2 && $wuku === 11) {
            $dewasaAyu .= ', Kala Susulan';
        }

        // 149. Kala Suwung
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 3 && ($wuku === 8 || $wuku === 10)) ||
            ($saptawara === 4 && ($wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 11 || $wuku === 13 || $wuku === 14))
        ) {
            $dewasaAyu .= ', Kala Suwung';
        }

        // 150. Kala Tampak
        if (($saptawara === 1 && ($wuku === 5 || $wuku === 13 || $wuku === 21 || $wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 3 || $wuku === 11 || $wuku === 19 || $wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 8 || $wuku === 16 || $wuku === 24)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 9 || $wuku === 17 || $wuku === 25)) ||
            ($saptawara === 5 && ($wuku === 14 || $wuku === 22 || $wuku === 30)) ||
            ($saptawara === 6 && ($wuku === 4 || $wuku === 12 || $wuku === 20 || $wuku === 28)) ||
            ($saptawara === 7 && ($wuku === 7 || $wuku === 15 || $wuku === 23))
        ) {
            $dewasaAyu .= ', Kala Tampak';
        }

        // 151. Kala Temah
        if (($saptawara === 1 && ($wuku === 14 || $wuku === 15 || $wuku === 28 || $wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 1 || $wuku === 2 || $wuku === 5 || $wuku === 7 || $wuku === 8 || $wuku === 9 ||
                $wuku === 13 || $wuku === 16 || $wuku === 20 || $wuku === 23 || $wuku === 30)) ||
            ($saptawara === 3 && ($wuku === 3 || $wuku === 10 || $wuku === 12 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 4 && ($wuku === 4 || $wuku === 11)) ||
            ($saptawara === 5 && ($wuku === 3 || $wuku === 5 || $wuku === 10 || $wuku === 12 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 6 && ($wuku === 3 || $wuku === 5 || $wuku === 9 || $wuku === 13 ||
                $wuku === 16 || $wuku === 20 || $wuku === 23 || $wuku === 30)) ||
            ($saptawara === 7 && ($wuku === 3 || $wuku === 14 || $wuku === 15 || $wuku === 29))
        ) {
            $dewasaAyu .= ', Kala Temah';
        }

        // 152. Kala Timpang
        if (($saptawara === 3 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 14) ||
            ($saptawara === 7 && $wuku === 2)
        ) {
            $dewasaAyu .= ', Kala Timpang';
        }

        // 153. Kala Tukaran
        if ($saptawara === 3 && ($wuku === 3 || $wuku === 8)) {
            $dewasaAyu .= ', Kala Tukaran';
        }

        // 154. Kala Tumapel
        if ($wuku === 12 && ($saptawara === 3 || $saptawara === 4)) {
            $dewasaAyu .= ', Kala Tumapel';
        }

        // 155. Kala Tumpar
        if (($saptawara === 3 && $wuku === 13) || ($saptawara === 4 && $wuku === 8)) {
            $dewasaAyu .= ', Kala Tumpar';
        }

        // 156. Kala Upa
        if ($sadwara === 4 && $triwara === 1) {
            $dewasaAyu .= ', Kala Upa';
        }

        // 157. Kala Was
        if ($saptawara === 2 && $wuku === 17) {
            $dewasaAyu .= ', Kala Was';
        }

        // 158. Kala Wikalpa
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 25)) || ($saptawara === 6 && ($wuku === 27 || $wuku === 30))) {
            $dewasaAyu .= ', Kala Wikalpa';
        }

        // 159. Kala Wisesa
        if ($sadwara === 5 && $astawara === 3) {
            $dewasaAyu .= ', Kala Wisesa';
        }

        // 160. Kala Wong
        if ($saptawara === 4 && $wuku === 20) {
            $dewasaAyu .= ', Kala Wong';
        }

        // 161. Kaleburau
        if (
            ($saptawara === 1 && ($wuku === 2 || $wuku === 3 || $wuku === 8 || $wuku === 14 || $wuku === 27 || $wuku === 30)) ||
            ($saptawara === 2 && ($triwara === 2 || $purnama_tilem === 'Tilem')) ||
            ($saptawara === 3 && ($wuku === 7 || $wuku === 13 || $wuku === 22 || $wuku === 25 || $wuku === 21)) ||
            ($saptawara === 4 && ($wuku === 17 || $wuku === 29 || $wuku === 21)) ||
            ($saptawara === 5 && $wuku === 20) ||
            ($saptawara === 6 && ($wuku === 6 || $wuku === 28)) ||
            ($saptawara === 7 && ($wuku === 18 || $wuku === 26))
        ) {
            $dewasaAyu .= ', Kaleburau';
        }

        // 162. Kamajaya
        if ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3 || $sasihDay2 === 7)))) {
            $dewasaAyu .= ', Kamajaya';
        }

        // 163. Karna Sula
        if (
            ($saptawara === 1 && ($sasihDay1 === 2 || $sasihDay2 === 2)) ||
            ($saptawara === 3 && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem'))
        ) {
            $dewasaAyu .= ', Karna Sula';
        }

        // 164. Karnasula
        if (
            ($saptawara === 2 && ($wuku === 1 || $wuku === 4 || $wuku === 7 || $wuku === 9)) ||
            ($saptawara === 3 && $wuku === 13) ||
            ($saptawara === 4 && $wuku === 11) ||
            ($saptawara === 5 && ($wuku === 8 || $wuku === 11)) ||
            ($saptawara === 6 && $wuku === 3) ||
            ($saptawara === 7 && ($wuku === 5 || $wuku === 10))
        ) {
            $dewasaAyu .= ', Karnasula';
        }

        // 165. Lebur Awu
        if (
            ($saptawara === 1 && $astawara === 2) ||
            ($saptawara === 2 && $astawara === 8) ||
            ($saptawara === 3 && $astawara === 5) ||
            ($saptawara === 4 && $astawara === 6) ||
            ($saptawara === 5 && $astawara === 3) ||
            ($saptawara === 6 && $astawara === 1) ||
            ($saptawara === 7 && $astawara === 4)
        ) {
            $dewasaAyu .= ', Lebur Awu';
        }

        // 166. Lutung Magandong
        if ($saptawara === 5 && ($wuku === 3 || $wuku === 8 || $wuku === 13 || $wuku === 18 || $wuku === 23 || $wuku === 28)) {
            $dewasaAyu .= ', Lutung Magandong';
        }

        // 167. Macekan Agung
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 5 || $sasihDay2 === 7)))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6)))
        ) {
            $dewasaAyu .= ', Macekan Agung';
        }

        // 168. Macekan Lanang
        if (
            ($saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 12)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 12)))) ||
            ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 11)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 11)))) ||
            ($saptawara === 3 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 11 || $sasihDay1 === 9)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 11 || $sasihDay2 === 9)))) ||
            ($saptawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 9) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 9))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 5 || $sasihDay2 === 7)))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 6) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 6)))
        ) {
            $dewasaAyu .= ', Macekan Lanang';
        }

        // 169. Macekan Wadon
        if (
            ($saptawara === 1 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 5))) ||
            ($saptawara === 2 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 11) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 11))) ||
            ($saptawara === 3 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 10) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 10))) ||
            ($saptawara === 4 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 9) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 9))) ||
            ($saptawara === 5 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 8) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 8))) ||
            ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 13) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 13)))
        ) {
            $dewasaAyu .= ', Macekan Wadon';
        }

        // 170. Merta Sula
        if ($saptawara === 5 && ($sasihDay1 === 7 || $sasihDay2 === 7)) {
            $dewasaAyu .= ', Merta Sula';
        }

        // 171. Naga Naut
        if ($sasihDay1 === 'no_sasih' || $sasihDay2 === 'no_sasih') {
            $dewasaAyu .= ', Naga Naut';
        }

        // 172. Pemacekan
        if (
            ($saptawara === 1 && ($sasihDay1 === 12 || $sasihDay2 === 12 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 2 && ($sasihDay1 === 11 || $sasihDay2 === 11 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 3 && ($sasihDay1 === 10 || $sasihDay2 === 10 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 4 && ($sasihDay1 === 9 || $sasihDay2 === 9 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 5 && ($sasihDay1 === 8 || $sasihDay2 === 8 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 6 && ($sasihDay1 === 7 || $sasihDay2 === 7 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 7 && ($sasihDay1 === 6 || $sasihDay2 === 6 || $sasihDay1 === 15 || $sasihDay2 === 15))
        ) {
            $dewasaAyu .= ', Pamacekan';
        }

        // 173. Panca Amerta
        if ($saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            $dewasaAyu .= ', Panca Amerta';
        }

        // 174. Panca Prawani
        if ($sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 12 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 12) {
            $dewasaAyu .= ', Panca Prawani';
        }

        // 175. Panca Wedhi
        if ($saptawara === 2 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            $dewasaAyu .= ', Panca Werdhi';
        }

        // 176. Pati Paten
        if ($saptawara === 6 && (($sasihDay1 === 10 || $sasihDay2 === 10) || $purnama_tilem === 'Tilem')) {
            $dewasaAyu .= ', Pati Paten';
        }

        // 177. Patra Limutan
        if ($triwara === 3 && $purnama_tilem === 'Tilem') {
            $dewasaAyu .= ', Patra Limutan';
        }

        // 178. Pepedan
        if (
            ($saptawara === 1 && ($wuku === 5 || $wuku === 9 || $wuku === 10 || $wuku === 11 || $wuku === 15 || $wuku === 20 ||
                $wuku === 21 || $wuku === 23 || $wuku === 25 || $wuku === 26 || $wuku === 27 || $wuku === 28 ||
                $wuku === 30 || $wuku === 22
            )) ||
            ($saptawara === 2 && ($wuku === 8 || $wuku === 14 || $wuku === 17 || $wuku === 18 || $wuku === 21 || $wuku === 22 ||
                $wuku === 24 || $wuku === 25 || $wuku === 26 || $wuku === 27 || $wuku === 29
            )) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 3 || $wuku === 5 || $wuku === 7 || $wuku === 10 || $wuku === 11 ||
                $wuku === 13 || $wuku === 14 || $wuku === 17 || $wuku === 18 || $wuku === 19 || $wuku === 20 ||
                $wuku === 22 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 26 || $wuku === 27 ||
                $wuku === 29 || $wuku === 30
            )) ||
            ($saptawara === 4 && ($wuku === 4 || $wuku === 5 || $wuku === 6 || $wuku === 7 || $wuku === 8 || $wuku === 9 ||
                $wuku === 11 || $wuku === 12 || $wuku === 15 || $wuku === 16 || $wuku === 18 || $wuku === 23 ||
                $wuku === 24 || $wuku === 27 || $wuku === 28 || $wuku === 30
            )) ||
            ($saptawara === 5 && ($wuku === 1 || $wuku === 3 || $wuku === 4 || $wuku === 7 || $wuku === 8 || $wuku === 9 ||
                $wuku === 11 || $wuku === 14 || $wuku === 19 || $wuku === 21 || $wuku === 23 || $wuku === 24 ||
                $wuku === 29
            )) ||
            ($saptawara === 6 && ($wuku === 2 || $wuku === 4 || $wuku === 14 || $wuku === 16 || $wuku === 19 || $wuku === 20 ||
                $wuku === 21 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 29
            )) ||
            ($saptawara === 7 && ($wuku === 2 || $wuku === 3 || $wuku === 7 || $wuku === 9 || $wuku === 10 || $wuku === 11 ||
                $wuku === 13 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 29 ||
                $wuku === 30
            ))
        ) {
            $dewasaAyu .= ', Pepedan';
        }

        // 179. Prabu Pendah
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) {
            $dewasaAyu .= ', Prabu Pendah';
        }

        // 180. Prangewa
        if ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) {
            $dewasaAyu .= ', Prangewa';
        }

        // 181. Purnama Danta
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Purnama Danta';
        }

        // 182. Purna Suka
        if ($saptawara === 6 && $pancawara === 1 && $purnama_tilem === 'Purnama') {
            $dewasaAyu .= ', Purna Suka';
        }

        // 183. Purwani
        if ($sasihDay1 === 14 || $sasihDay2 === 14) {
            $dewasaAyu .= ', Purwani';
        }

        // 184. Purwanin Dina
        if (
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 3 && $pancawara === 5) ||
            ($saptawara === 4 && $pancawara === 5) ||
            ($saptawara === 6 && $pancawara === 4) ||
            ($saptawara === 7 && $pancawara === 5)
        ) {
            $dewasaAyu .= ', Purwanin Dina';
        }

        // 185. Rangda Tiga
        if ($wuku === 7 || $wuku === 8 || $wuku === 15 || $wuku === 16 || $wuku === 23 || $wuku === 24) {
            $dewasaAyu .= ', Rangda Tiga';
        }

        // 186. Rarung Pagelangan
        if ($saptawara === 5 && ($sasihDay1 === 6 || $sasihDay2 === 6)) {
            $dewasaAyu .= ', Rarung Pagelangan';
        }

        // 187. Ratu Magelung
        if ($saptawara === 4 && $wuku === 23) {
            $dewasaAyu .= ', Ratu Magelung';
        }

        // 188. Ratu Mangure
        if ($saptawara === 5 && $wuku === 20) {
            $dewasaAyu .= ', Ratu Mangure';
        }

        // 189. Ratu Megambahan
        if ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 6))) {
            $dewasaAyu .= ', Ratu Megambahan';
        }

        // 190. Ratu Nanyingal
        if ($saptawara === 5 && $wuku === 21) {
            $dewasaAyu .= ', Ratu Nanyingal';
        }

        // 191. Ratu Ngemban Putra
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            $dewasaAyu .= ', Ratu Ngemban Putra';
        }

        // 192. Rekatadala Ayudana
        if ($saptawara === 1 && ($sasihDay1 === 1 || $sasihDay1 === 6 || $sasihDay1 === 11 || $sasihDay1 === 2 || $sasihDay2 === 6 || $sasihDay2 === 11)) {
            $dewasaAyu .= ', Rekatadala Ayudana';
        }

        // 193. Salah Wadi
        if ($wuku === 1 || $wuku === 2 || $wuku === 6 || $wuku === 10 || $wuku === 11 || $wuku === 16 || $wuku === 19 || $wuku === 20 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 30) {
            $dewasaAyu .= ', Salah Wadi';
        }

        // 194. Sampar Wangke
        if ($saptawara === 2 && $sadwara === 2) {
            $dewasaAyu .= ', Sampar Wangke';
        }

        // 195. Sampi Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 4) {
            $dewasaAyu .= ', Sampi Gumarang Munggah';
        }

        // 196. Sampi Gumarang Turun
        if ($pancawara === 3 && $sadwara === 1) {
            $dewasaAyu .= ', Sampi Gumarang Turun';
        }

        // 197. Sarik Agung
        if ($saptawara === 4 && ($wuku === 25 || $wuku === 4 || $wuku === 11 || $wuku === 18)) {
            $dewasaAyu .= ', Sarik Agung';
        }

        // 198. Sarik Ketah
        if (($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            $dewasaAyu .= ', Sarik Ketah';
        }

        // 199. Sedana Tiba
        if ($saptawara === 5 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) {
            $dewasaAyu .= ', Sedana Tiba';
        }

        // 200. Sedana Yoga
        if (($saptawara === 1 && ($sasihDay1 === 8 || $sasihDay2 === 8 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 2 && ($sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 3 && ($sasihDay1 === 7 || $sasihDay2 === 7)) ||
            ($saptawara === 4 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 5 && ($sasihDay1 === 4 || $sasihDay2 === 4 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 6 && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 7 && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15))
        ) {
            $dewasaAyu .= ', Sedana Yoga';
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            $dewasaAyu .= ', Semut Sadulur';
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            $dewasaAyu .= ', Siwa Sampurna';
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            $dewasaAyu .= ', Sri Bagia';
        }

        // 200. Sedana Yoga
        if (($saptawara === 1 && ($sasihDay1 === 8 || $sasihDay2 === 8 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 2 && ($sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 3 && ($sasihDay1 === 7 || $sasihDay2 === 7)) ||
            ($saptawara === 4 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 5 && ($sasihDay1 === 4 || $sasihDay2 === 4 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) ||
            ($saptawara === 6 && ($sasihDay1 === 1 || $sasihDay2 === 1 || $sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 7 && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15))
        ) {
            $dewasaAyu .= ', Sedana Yoga';
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            $dewasaAyu .= ', Semut Sadulur';
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            $dewasaAyu .= ', Siwa Sampurna';
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            $dewasaAyu .= ', Sri Bagia';
        }

        // 204. Sri Murti
        if ($sadwara === 5 && $astawara === 1) {
            $dewasaAyu .= ', Sri Murti';
        }

        // 205. Sri Tumpuk
        if ($astawara === 1) {
            $dewasaAyu .= ', Sri Tumpuk';
        }

        // 206. Srigati
        if (($triwara === 3 && $pancawara === 1 && $sadwara === 3) ||
            ($triwara === 3 && $pancawara === 1 && $sadwara === 6)
        ) {
            $dewasaAyu .= ', Srigati';
        }

        // 207. Srigati Jenek
        if ($pancawara === 5 && $sadwara === 6) {
            $dewasaAyu .= ', Srigati Jenek';
        }

        // 208. Srigati Munggah
        if ($pancawara === 1 && $sadwara === 3) {
            $dewasaAyu .= ', Srigati Munggah';
        }

        // 209. Srigati Turun
        if ($pancawara === 1 && $sadwara === 6) {
            $dewasaAyu .= ', Srigati Turun';
        }

        // 210. Subhacara
        if (($saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) ||
                ($pengalantaka === 'Penanggal' && ($sasihDay1 === 15 || $sasihDay2 === 15)))) ||
            ($saptawara === 2 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay2 === 2 ||
                $sasihDay1 === 7 || $sasihDay2 === 7 || $sasihDay1 === 8 || $sasihDay2 === 8)) ||
            ($saptawara === 4 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay2 === 2 ||
                $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 ||
                $sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($saptawara === 6 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1 ||
                $sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3)) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            $dewasaAyu .= ', Subhacara';
        }

        // 211. Swarga Menga
        if (($saptawara === 3 && $pancawara === 3 && $wuku === 3 &&
                (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 5 && $pancawara === 2 && $wuku === 4)
        ) {
            $dewasaAyu .= ', Swarga Menga';
        }

        // 212. Taliwangke
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 27 || $wuku === 28 || $wuku === 29 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 3 || $wuku === 4 || $wuku === 6)) ||
            ($saptawara === 5 && ($wuku === 7 || $wuku === 8 || $wuku === 9 || $wuku === 10 || $wuku === 11 || $wuku === 17 || $wuku === 18 || $wuku === 20 || $wuku === 21)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 13 || $wuku === 14 || $wuku === 15 || $wuku === 16))
        ) {
            $dewasaAyu .= ', Taliwangke';
        }

        // 213. Titibuwuk
        if (($saptawara === 1 && ($wuku === 18 || $wuku === 26 || $wuku === 27 || $wuku === 28 || $wuku === 30)) ||
            ($saptawara === 2 && ($wuku === 8 || $wuku === 9 || $wuku === 20)) ||
            ($saptawara === 3 && ($wuku === 7 || $wuku === 21 || $wuku === 1)) ||
            ($saptawara === 4 && ($wuku === 4 || $wuku === 5 || $wuku === 10 || $wuku === 15 || $wuku === 19 || $wuku === 25 || $wuku === 2)) ||
            ($saptawara === 5 && ($wuku === 6 || $wuku === 13 || $wuku === 17 || $wuku === 22 || $wuku === 24)) ||
            ($saptawara === 6 && ($wuku === 3 || $wuku === 12)) ||
            ($saptawara === 7 && ($wuku === 16 || $wuku === 21 || $wuku === 23 || $wuku === 29))
        ) {
            $dewasaAyu .= ', Titibuwuk';
        }

        // 214. Tunut Masih
        if (($saptawara === 1 && $wuku === 18) ||
            ($saptawara === 2 && ($wuku === 12 || $wuku === 13 || $wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 17 || $wuku === 24)) ||
            ($saptawara === 5 && $wuku === 1) ||
            ($saptawara === 6 && ($wuku === 19 || $wuku === 22))
        ) {
            $dewasaAyu .= ', Tunut Masih';
        }

        // 215. Tutur Mandi
        if (($saptawara === 1 && $wuku === 26) ||
            ($saptawara === 5 && ($wuku === 3 || $wuku === 9 || $wuku === 15 || $wuku === 20 || $wuku === 21 || $wuku === 24)) ||
            ($saptawara === 6 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 24)
        ) {
            $dewasaAyu .= ', Tutur Mandi';
        }

        // 216. Uncal Balung
        if ($wuku === 12 || $wuku === 13 || (($wuku === 14 && $saptawara === 1) || ($wuku === 16 && $saptawara < 5))) {
            $dewasaAyu .= ', Uncal Balung';
        }

        // 217. Upadana Merta
        if (
            $saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 8 || $sasihDay1 === 6 || $sasihDay1 === 10)) ||
                ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 8 || $sasihDay2 === 6 || $sasihDay2 === 10)))
        ) {
            $dewasaAyu .= ', Upadana Merta';
        }

        // 218. Werdi Suka
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)) &&
            ($no_sasih === 1)
        ) {
            $dewasaAyu .= ', Werdi Suka';
        }

        // 219. Wisesa
        if (
            $saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 13) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 13)) &&
            ($no_sasih === 1)
        ) {
            $dewasaAyu .= ', Wisesa';
        }

        // 220. Wredhi Guna
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)) &&
            ($no_sasih === 1)
        ) {
            $dewasaAyu .= ', Wredhi Guna';
        }

        // Remove leading comma and space
        $dewasaAyu = ltrim($dewasaAyu, ', ');

        return response()->json(['dewasaAyu' => $dewasaAyu], 200);
    }
}
