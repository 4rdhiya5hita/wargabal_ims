<?php

namespace App\Http\Controllers;

use App\Models\DewasaAyu;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DewasaAyuController extends Controller
{
    public function searchDewasaAyuAPI(Request $request)
    {
        $start = microtime(true);

        $tanggal_mulai = Carbon::parse($request->input('tanggal_mulai'));
        $tanggal_selesai = Carbon::parse($request->input('tanggal_selesai'));
        // $tanggal_mulai = '2023-08-01';
        // $tanggal_selesai = '2023-08-05';
        $makna = $request->has('keterangan');
        
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
                'dewasaAyu' => $this->getDewasaAyu($tanggal_mulai->toDateString(), $makna),
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

        // dd($response);

        return response()->json($response, 200);
        // return view('dashboard.index', compact('dewasa_ayu'));
    }

    public function getDewasaAyu($tanggal, $makna)
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
        $dewasaAyu = [];
        $keterangan = [];

        // 1. AgniAgungDoyanBasmi: Selasa Purnama dengan Asta Wara Brahma
        if (($saptawara === 3 && ($astawara === 6 || $purnama_tilem === 'Purnama'))) {
            $dewasaAyu[] = 'Agni Agung Doyan Basmi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
            // dd($keterangan);
        }

        // 2. AgniAgungPatraLimutan: Minggu dengan Asta Wara Brahma
        if ($saptawara === 1 && $astawara === 6) {
            $dewasaAyu[] = 'Agni Agung Patra Limutan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 3. Amerta Akasa: Anggara Purnama
        if ($saptawara === 3 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Amerta Akasa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 4. Amerta Bumi: Soma Wage Penanggal 1. Buda Pon Penanggal 10.
        if (($saptawara === 2 && $pancawara === 4 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($saptawara === 4 && $pancawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 10 || $sasihDay2 === 10))
        ) {
            $dewasaAyu[] = 'Amerta Bumi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 5. Amerta Bhuwana: Redite Purnama, Soma Purnama, dan Anggara Purnama
        if (($saptawara === 1 || $saptawara === 2 || $saptawara === 3) && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Amerta Bhuwana';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 6. Amerta Dadi: Soma Beteng atau Purnama Kajeng
        if (($saptawara === 2 && $triwara === 2) || ($triwara === 3 && $purnama_tilem === 'Purnama')) {
            $dewasaAyu[] = 'Amerta Dadi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Amerta Danta';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Amerta Dewa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 9. Amerta Dewa Jaya
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay1 === 12 || $sasihDay2 === 12)))) {
            $dewasaAyu[] = 'Amerta Dewa Jaya';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 10. Amerta Dewata
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            $dewasaAyu[] = 'Amerta Dewata';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Amerta Gati';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Amerta Kundalini';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 13. Amerta Masa
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Amerta Masa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 14. Amerta Murti
        if ($saptawara === 4 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            $dewasaAyu[] = 'Amerta Murti';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 15. Amerta Pageh
        if ($saptawara === 7 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Amerta Pageh';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 16. Amerta Pepageran
        if ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $astawara === 4)) {
            $dewasaAyu[] = 'Amerta Pepageran';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 17. Amerta Sari
        if ($saptawara === 4 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Amerta Sari';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 18. Amerta Wija
        if ($saptawara === 5 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Amerta Wija';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 19. Amerta Yoga
        if (
            ($saptawara === 2 && ($wuku === 2 || $wuku === 5 || $wuku === 14 || $wuku === 17 || $wuku === 20 || $wuku === 23 || $wuku === 26 || $wuku === 29)) ||
            ($saptawara === 5 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 5))) ||
            (($no_sasih === 10) && (($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4))) ||
            (($no_sasih === 12) && (($pengalantaka === 'Pangelong' && $sasihDay1 === 1) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 1)))
        ) {
            $dewasaAyu[] = 'Amerta Yoga';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Asuajag Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Asuajag Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Asuasa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Ayu Bhadra';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 24. Ayu Dana
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Ayu Dana';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Ayu Nulus';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 26. Babi Munggah
        if ($pancawara === 4 && $sadwara === 1) {
            $dewasaAyu[] = 'Babi Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 27. Babi Turun
        if ($pancawara === 4 && $sadwara === 4) {
            $dewasaAyu[] = 'Babi Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 28. Banyu Milir
        if (
            ($saptawara === 1 && $wuku === 4) ||
            ($saptawara === 2 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 13)
        ) {
            $dewasaAyu[] = 'Banyu Milir';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Banyu Urug';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 30. Bojog Munggah
        if ($pancawara === 5 && $sadwara === 5) {
            $dewasaAyu[] = 'Bojog Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 31. Bojog Turun
        if ($pancawara === 5 && $sadwara === 2) {
            $dewasaAyu[] = 'Bojog Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 32. Buda Gajah
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Buda Gajah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 33. Buda Ireng
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Tilem') {
            $dewasaAyu[] = 'Buda Ireng';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 34. Buda Suka
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Tilem') {
            $dewasaAyu[] = 'Buda Suka';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 35. Carik Walangati
        if (
            $wuku === 1 || $wuku === 6 || $wuku === 10 || $wuku === 12 || $wuku === 24 ||
            $wuku === 25 || $wuku === 27 || $wuku === 28 || $wuku === 30 || $wuku === 7
        ) {
            $dewasaAyu[] = 'Carik Walangati';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 36. Catur Laba
        if (
            ($saptawara === 1 && $pancawara === 1) ||
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 4 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 2)
        ) {
            $dewasaAyu[] = 'Catur Laba';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 37. Cintamanik
        if ($saptawara === 4 && ($wuku % 2 === 1)) {
            $dewasaAyu[] = 'Cintamanik';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 38. Corok Kodong
        if ($saptawara === 5 && $pancawara === 5 && $wuku === 13) {
            $dewasaAyu[] = 'Corok Kodong';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'DagDig Karana';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 40. Dasa Amertha
        if ($saptawara === 6 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            $dewasaAyu[] = 'Dasa Amertha';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 41. Dasa Guna
        if ($saptawara === 4 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem')) {
            $dewasaAyu[] = 'Dasa Guna';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dauh Ayu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 43. Derman Bagia
        if ($saptawara === 2 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 12 || $sasihDay2 === 12)) {
            $dewasaAyu[] = 'Derman Bagia';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dewa Ngelayang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dewa Satata';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 46. Dewa Werdhi
        if ($saptawara === 6 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            $dewasaAyu[] = 'Dewa Werdhi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 47. Dewa Mentas
        if ($saptawara === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) {
            $dewasaAyu[] = 'Dewa Mentas';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dewasa Ngelayang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dewasa Tanian';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dina Carik';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Dina Jaya';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 52. Dina Mandi
        if (
            ($saptawara === 3 && $purnama_tilem === 'Purnama') ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3)))
        ) {
            $dewasaAyu[] = 'Dina Mandi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 53. Dirgahayu
        if ($saptawara === 3 && $pancawara === 3 && $dasawara === 1) {
            $dewasaAyu[] = 'Dirgahayu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 54. DirghaYusa
        if ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            $dewasaAyu[] = 'Dirgha Yusa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 55. Gagak Anungsung Pati
        if (
            ($pengalantaka === 'Penanggal' && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 14 || $sasihDay2 === 14))
        ) {
            $dewasaAyu[] = 'Gagak Anungsung Pati';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Geheng Manyinget';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 57. Geni Agung
        if (
            ($saptawara === 1 && $pancawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 3 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14)))
        ) {
            $dewasaAyu[] = 'Geni Agung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Geni Murub';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 59. Geni Rawana
        if (
            (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11))) ||
            (($pengalantaka === 'Pangelong' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13)) || ($pengalantaka === 'Pangelong' && ($sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            $dewasaAyu[] = 'Geni Rawana';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Geni Rawana Jejepan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 61. Geni Rawana Rangkep
        if (
            (($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11 || $sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11)) || ($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13 || $sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            $dewasaAyu[] = 'Geni Rawana Rangkep';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Guntur Graha';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 63. Ingkel Macan
        if ($saptawara === 5 && $pancawara === 3 && $wuku === 7) {
            $dewasaAyu[] = 'Ingkel Macan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 64. Istri Payasan
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) {
            $dewasaAyu[] = 'Istri Payasan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 65. Jiwa Manganti
        if (($saptawara === 2 && $wuku === 19) || ($saptawara === 5 && ($wuku === 2 || $wuku === 20)) || ($saptawara === 6 && ($wuku === 25 || $wuku === 7)) || ($saptawara === 7 && $wuku === 30)) {
            $dewasaAyu[] = 'Jiwa Manganti';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 66. Kajeng Kipkipan
        if ($saptawara === 4 && ($wuku === 6 || $wuku === 30)) {
            $dewasaAyu[] = 'Kajeng Kipkipan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 67. Kajeng Kliwon Enyitan
        if ($triwara === 3 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 < 15 && $sasihDay1 > 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 < 15 && $sasihDay2 > 7))) {
            $dewasaAyu[] = 'Kajeng Kliwon Enyitan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 68. Kajeng Lulunan
        if ($triwara === 3 && $astawara === 5 && $sangawara === 9) {
            $dewasaAyu[] = 'Kajeng Lulunan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 69. Kajeng Rendetan
        if ($triwara === 3 && $pengalantaka === 'Penanggal' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            $dewasaAyu[] = 'Kajeng Rendetan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 70. Kajeng Susunan
        if ($triwara === 3 && $astawara === 3 && $sangawara === 9) {
            $dewasaAyu[] = 'Kajeng Susunan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 71. Kajeng Uwudan
        if ($triwara === 3 && $pengalantaka === 'Pangelong' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            $dewasaAyu[] = 'Kajeng Uwudan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 72. Kala Alap
        if ($saptawara === 2 && $wuku === 22) {
            $dewasaAyu[] = 'Kala Alap';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 73. Kala Angin
        if ($saptawara === 1 && ($wuku === 17 || $wuku === 25 || $wuku === 28)) {
            $dewasaAyu[] = 'Kala Angin';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 74. Kala Atat
        if (($saptawara === 1 && $wuku === 22) || ($saptawara === 3 && $wuku === 30) || ($saptawara === 4 && $wuku === 19)) {
            $dewasaAyu[] = 'Kala Atat';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 75. Kala Awus
        if ($saptawara === 4 && $wuku === 28) {
            $dewasaAyu[] = 'Kala Awus';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Bancaran';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 77. Kala Bangkung, Kala Nanggung
        if (
            $saptawara === 1 && $pancawara === 3 ||
            $saptawara === 2 && $pancawara === 2 ||
            $saptawara === 4 && $pancawara === 1 ||
            $saptawara === 7 && $pancawara === 4
        ) {
            $dewasaAyu[] = 'Kala Bangkung, Kala Nanggung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 78. Kala Beser
        if ($sadwara === 1 && $astawara === 7) {
            $dewasaAyu[] = 'Kala Beser';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 79. Kala Brahma
        if (
            $saptawara === 1 && $wuku === 23 ||
            $saptawara === 3 && $wuku === 14 ||
            $saptawara === 4 && $wuku === 1 ||
            $saptawara === 6 && ($wuku === 4 || $wuku === 25 || $wuku === 30) ||
            $saptawara === 7 && $wuku === 13
        ) {
            $dewasaAyu[] = 'Kala Brahma';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 80. Kala Bregala
        if ($saptawara === 2 && $wuku === 2) {
            $dewasaAyu[] = 'Kala Bregala';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Buingrau';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 82. Kala Cakra
        if ($saptawara === 7 && $wuku === 23) {
            $dewasaAyu[] = 'Kala Cakra';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 83. Kala Capika
        if ($saptawara === 1 && $wuku === 18 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) {
            $dewasaAyu[] = 'Kala Capika';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 84. Kala Caplokan
        if (($saptawara === 2 && ($wuku === 18 || $wuku === 9)) ||
            ($saptawara === 3 && $wuku === 19) ||
            ($saptawara === 4 && $wuku === 24) ||
            ($saptawara === 6 && $wuku === 12) ||
            ($saptawara === 7 && ($wuku === 9 || $wuku === 15 || $wuku === 1))
        ) {
            $dewasaAyu[] = 'Kala Caplokan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 85. Kala Cepitan
        if ($saptawara === 2 && $pancawara === 2 && $wuku === 18) {
            $dewasaAyu[] = 'Kala Cepitan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Dangastra';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Dangu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 88. Kala Demit
        if ($saptawara === 7 && $wuku === 3) {
            $dewasaAyu[] = 'Kala Demit';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 89. Kala Empas Munggah
        if ($pancawara === 4 && $sadwara === 3) {
            $dewasaAyu[] = 'Kala Empas Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 90. Kala Empas Turun
        if ($pancawara === 4 && $sadwara === 6) {
            $dewasaAyu[] = 'Kala Empas Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 91. Kala Gacokan
        if ($saptawara === 3 && $wuku === 19) {
            $dewasaAyu[] = 'Kala Gacokan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 92. Kala Garuda
        if ($saptawara === 3 && $wuku === 2) {
            $dewasaAyu[] = 'Kala Garuda';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 93. Kala Geger
        if (($saptawara === 5 || $saptawara === 7) && $wuku === 7) {
            $dewasaAyu[] = 'Kala Geger';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 94. Kala Gotongan
        if (($saptawara === 6 && $pancawara === 5) ||
            ($saptawara === 7 && $pancawara === 1) ||
            ($saptawara === 1 && $pancawara === 2)
        ) {
            $dewasaAyu[] = 'Kala Gotongan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 95. Kala Graha
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 5)
        ) {
            $dewasaAyu[] = 'Kala Graha';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 96. Kala Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 3) {
            $dewasaAyu[] = 'Kala Gumarang Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 97. Kala Gumarang Turun
        if ($pancawara === 3 && $sadwara === 6) {
            $dewasaAyu[] = 'Kala Gumarang Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 98. Kala Guru
        if ($saptawara === 4 && $wuku === 2) {
            $dewasaAyu[] = 'Kala Guru';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 99. Kala Ingsor
        if ($wuku === 4 || $wuku === 14 || $wuku === 24) {
            $dewasaAyu[] = 'Kala Ingsor';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 100. Kala Isinan
        if (($saptawara === 2 && ($wuku === 11 || $wuku === 17)) ||
            ($saptawara === 4 && $wuku === 30)
        ) {
            $dewasaAyu[] = 'Kala Isinan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 101. Kala Jangkut
        if ($triwara === 3 && $dwiwara === 2) {
            $dewasaAyu[] = 'Kala Jangkut';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 102. Kala Jengkang
        if ($saptawara === 1 && $pancawara === 1 && $wuku === 3) {
            $dewasaAyu[] = 'Kala Jengkang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 103. Kala Jengking
        if ($sadwara === 3 && $astawara === 7) {
            $dewasaAyu[] = 'Kala Jengking';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Katemu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 105. Kala Keciran
        if ($saptawara === 4 && $wuku === 6) {
            $dewasaAyu[] = 'Kala Keciran';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 106. Kala Kilang-Kilung
        if (($saptawara === 2 && $wuku === 17) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            $dewasaAyu[] = 'Kala Kilang-Kilung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 107. Kala Kingkingan
        if ($saptawara === 5 && $wuku === 17) {
            $dewasaAyu[] = 'Kala Kingkingan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 108. Kala Klingkung
        if ($saptawara === 3 && $wuku === 1) {
            $dewasaAyu[] = 'Kala Klingkung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 109. Kala Kutila Manik
        if ($triwara === 3 && $pancawara === 5) {
            $dewasaAyu[] = 'Kala Kutila Manik';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 110. Kala Kutila
        if ($sadwara === 2 && $astawara === 6) {
            $dewasaAyu[] = 'Kala Kutila';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 111. Kala Luang
        if (($saptawara === 1 && ($wuku === 11 || $wuku === 12 || $wuku === 13)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 10 || $wuku === 8 || $wuku === 19 || $wuku === 23 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 18)) ||
            ($saptawara === 5 && ($wuku === 28 || $wuku === 29))
        ) {
            $dewasaAyu[] = 'Kala Luang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 112. Kala Lutung Megelut
        if (($saptawara === 1 && $wuku === 3) || ($saptawara === 4 && $wuku === 10)) {
            $dewasaAyu[] = 'Kala Lutung Megelut';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 113. Kala Lutung Megandong
        if ($saptawara === 5 && $pancawara === 5) {
            $dewasaAyu[] = 'Kala Lutung Megandong';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 114. Kala Macan
        if ($saptawara === 5 && $wuku === 19) {
            $dewasaAyu[] = 'Kala Macan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 115. Kala Mangap
        if ($saptawara === 1 && $pancawara === 1) {
            $dewasaAyu[] = 'Kala Mangap';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 116. Kala Manguneb
        if ($saptawara === 5 && $pancawara === 14) {
            $dewasaAyu[] = 'Kala Manguneb';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 117. Kala Matampak
        if (($saptawara === 4 && $wuku === 3) ||
            ($saptawara === 5 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 3) ||
            ($saptawara === 7 && ($wuku === 7 || $wuku === 24))
        ) {
            $dewasaAyu[] = 'Kala Matampak';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 118. Kala Mereng
        if (($saptawara === 1 && ($wuku === 9 || $wuku === 24)) ||
            ($saptawara === 2 && ($wuku === 11 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 13)) ||
            ($saptawara === 4 && ($wuku === 15 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 2 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 21))
        ) {
            $dewasaAyu[] = 'Kala Mereng';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            $dewasaAyu[] = 'Kala Miled';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            $dewasaAyu[] = 'Kala Mina';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Mretyu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            $dewasaAyu[] = 'Kala Muas';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            $dewasaAyu[] = 'Kala Muncar';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            $dewasaAyu[] = 'Kala Muncrat';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            $dewasaAyu[] = 'Kala Ngadeg';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 118. Kala Mereng
        if (($saptawara === 1 && ($wuku === 9 || $wuku === 24)) ||
            ($saptawara === 2 && ($wuku === 11 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 13)) ||
            ($saptawara === 4 && ($wuku === 15 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 2 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 21))
        ) {
            $dewasaAyu[] = 'Kala Mereng';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            $dewasaAyu[] = 'Kala Miled';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            $dewasaAyu[] = 'Kala Mina';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Mretyu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            $dewasaAyu[] = 'Kala Muas';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            $dewasaAyu[] = 'Kala Muncar';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            $dewasaAyu[] = 'Kala Muncrat';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            $dewasaAyu[] = 'Kala Ngadeg';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 126. Kala Ngamut
        if ($saptawara === 2 && $wuku === 18) {
            $dewasaAyu[] = 'Kala Ngamut';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 127. Kala Ngruda
        if (($saptawara === 1 && ($wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 23 || $wuku === 10)) ||
            ($saptawara === 7 && ($wuku === 10))
        ) {
            $dewasaAyu[] = 'Kala Ngruda';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 128. Kala Ngunya
        if ($saptawara === 1 && $wuku === 3) {
            $dewasaAyu[] = 'Kala Ngunya';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 129. Kala Olih
        if ($saptawara === 4 && $wuku === 24) {
            $dewasaAyu[] = 'Kala Olih';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 130. Kala Pacekan
        if ($saptawara === 3 && $wuku === 5) {
            $dewasaAyu[] = 'Kala Pacekan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 131. Kala Pager
        if ($saptawara === 5 && $wuku === 7) {
            $dewasaAyu[] = 'Kala Pager';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 132. Kala Panyeneng
        if (($saptawara === 1 && $wuku === 7) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            $dewasaAyu[] = 'Kala Panyeneng';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 133. Kala Pati
        if (($saptawara === 1 && ($wuku === 10 || $wuku === 2)) ||
            ($saptawara === 3 && ($wuku === 6 || $wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 10 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 17))
        ) {
            $dewasaAyu[] = 'Kala Pati';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 134. Kala Pati Jengkang
        if ($saptawara === 5 && $sadwara === 3) {
            $dewasaAyu[] = 'Kala Pati Jengkang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 135. Kala Pegat
        if (
            $saptawara === 4 && $wuku === 12 ||
            $saptawara === 7 && ($wuku === 3 || $wuku === 18)
        ) {
            $dewasaAyu[] = 'Kala Pegat';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 136. Kala Prawani
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 3 && $wuku === 24) ||
            ($saptawara === 4 && $wuku === 2) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            $dewasaAyu[] = 'Kala Prawani';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 137. Kala Raja
        if ($saptawara === 5 && $wuku === 29) {
            $dewasaAyu[] = 'Kala Raja';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 138. Kala Rau
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 7 && ($wuku === 3 || $wuku === 4 || $wuku === 18)) ||
            ($saptawara === 6 && $wuku === 6)
        ) {
            $dewasaAyu[] = 'Kala Rau';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 139. Kala Rebutan
        if ($saptawara === 2 && $wuku === 26) {
            $dewasaAyu[] = 'Kala Rebutan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 140. Kala Rumpuh
        if (($saptawara === 1 && ($wuku === 18 || $wuku === 30)) ||
            ($saptawara === 2 && ($wuku === 9 || $wuku === 20)) ||
            ($saptawara === 4 && ($wuku === 10 || $wuku === 19 || $wuku === 25 || $wuku === 26 || $wuku === 27)) ||
            ($saptawara === 5 && ($wuku === 13 || $wuku === 14 || $wuku === 17 || $wuku === 22 || $wuku === 24)) ||
            ($saptawara === 6 && ($wuku === 11 || $wuku === 12)) ||
            ($saptawara === 7 && ($wuku === 21 || $wuku === 23 || $wuku === 28 || $wuku === 29))
        ) {
            $dewasaAyu[] = 'Kala Rumpuh';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 141. Kala Sapuhau
        if (($saptawara === 2 && $wuku === 3) ||
            ($saptawara === 3 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            $dewasaAyu[] = 'Kala Sapuhau';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 142. Kala Sarang
        if ($wuku === 7 || $wuku === 17) {
            $dewasaAyu[] = 'Kala Sarang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 143. Kala Siyung
        if (($saptawara === 1 && ($wuku === 2 || $wuku === 21)) ||
            ($saptawara === 2 && ($wuku === 1 || $wuku === 10 || $wuku === 25)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 20)) ||
            ($saptawara === 5 && ($wuku === 24 || $wuku === 26)) ||
            ($saptawara === 6 && $wuku === 28) ||
            ($saptawara === 7 && ($wuku === 15 || $wuku === 17))
        ) {
            $dewasaAyu[] = 'Kala Siyung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Sor';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 145. Kala Sudangastra
        if (($saptawara === 1 && $wuku === 24) ||
            ($saptawara === 3 && $wuku === 28) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 12)) ||
            ($saptawara === 5 && $wuku === 19) ||
            ($saptawara === 7 && $wuku === 6)
        ) {
            $dewasaAyu[] = 'Kala Sudangastra';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Sudukan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 147. Kala Sungsang
        if ($wuku === 27) {
            $dewasaAyu[] = 'Kala Sungsang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 148. Kala Susulan
        if ($saptawara === 2 && $wuku === 11) {
            $dewasaAyu[] = 'Kala Susulan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 149. Kala Suwung
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 3 && ($wuku === 8 || $wuku === 10)) ||
            ($saptawara === 4 && ($wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 11 || $wuku === 13 || $wuku === 14))
        ) {
            $dewasaAyu[] = 'Kala Suwung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Tampak';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kala Temah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 152. Kala Timpang
        if (($saptawara === 3 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 14) ||
            ($saptawara === 7 && $wuku === 2)
        ) {
            $dewasaAyu[] = 'Kala Timpang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 153. Kala Tukaran
        if ($saptawara === 3 && ($wuku === 3 || $wuku === 8)) {
            $dewasaAyu[] = 'Kala Tukaran';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 154. Kala Tumapel
        if ($wuku === 12 && ($saptawara === 3 || $saptawara === 4)) {
            $dewasaAyu[] = 'Kala Tumapel';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 155. Kala Tumpar
        if (($saptawara === 3 && $wuku === 13) || ($saptawara === 4 && $wuku === 8)) {
            $dewasaAyu[] = 'Kala Tumpar';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 156. Kala Upa
        if ($sadwara === 4 && $triwara === 1) {
            $dewasaAyu[] = 'Kala Upa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 157. Kala Was
        if ($saptawara === 2 && $wuku === 17) {
            $dewasaAyu[] = 'Kala Was';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 158. Kala Wikalpa
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 25)) || ($saptawara === 6 && ($wuku === 27 || $wuku === 30))) {
            $dewasaAyu[] = 'Kala Wikalpa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 159. Kala Wisesa
        if ($sadwara === 5 && $astawara === 3) {
            $dewasaAyu[] = 'Kala Wisesa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 160. Kala Wong
        if ($saptawara === 4 && $wuku === 20) {
            $dewasaAyu[] = 'Kala Wong';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Kaleburau';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 162. Kamajaya
        if ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3 || $sasihDay2 === 7)))) {
            $dewasaAyu[] = 'Kamajaya';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 163. Karna Sula
        if (
            ($saptawara === 1 && ($sasihDay1 === 2 || $sasihDay2 === 2)) ||
            ($saptawara === 3 && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem'))
        ) {
            $dewasaAyu[] = 'Karna Sula';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Karnasula';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Lebur Awu';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 166. Lutung Magandong
        if ($saptawara === 5 && ($wuku === 3 || $wuku === 8 || $wuku === 13 || $wuku === 18 || $wuku === 23 || $wuku === 28)) {
            $dewasaAyu[] = 'Lutung Magandong';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Macekan Agung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Macekan Lanang';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Macekan Wadon';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 170. Merta Sula
        if ($saptawara === 5 && ($sasihDay1 === 7 || $sasihDay2 === 7)) {
            $dewasaAyu[] = 'Merta Sula';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 171. Naga Naut
        if ($sasihDay1 === 'no_sasih' || $sasihDay2 === 'no_sasih') {
            $dewasaAyu[] = 'Naga Naut';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Pamacekan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 173. Panca Amerta
        if ($saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            $dewasaAyu[] = 'Panca Amerta';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 174. Panca Prawani
        if ($sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 12 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 12) {
            $dewasaAyu[] = 'Panca Prawani';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 175. Panca Wedhi
        if ($saptawara === 2 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            $dewasaAyu[] = 'Panca Werdhi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 176. Pati Paten
        if ($saptawara === 6 && (($sasihDay1 === 10 || $sasihDay2 === 10) || $purnama_tilem === 'Tilem')) {
            $dewasaAyu[] = 'Pati Paten';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 177. Patra Limutan
        if ($triwara === 3 && $purnama_tilem === 'Tilem') {
            $dewasaAyu[] = 'Patra Limutan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Pepedan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 179. Prabu Pendah
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) {
            $dewasaAyu[] = 'Prabu Pendah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 180. Prangewa
        if ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) {
            $dewasaAyu[] = 'Prangewa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 181. Purnama Danta
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Purnama Danta';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 182. Purna Suka
        if ($saptawara === 6 && $pancawara === 1 && $purnama_tilem === 'Purnama') {
            $dewasaAyu[] = 'Purna Suka';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 183. Purwani
        if ($sasihDay1 === 14 || $sasihDay2 === 14) {
            $dewasaAyu[] = 'Purwani';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 184. Purwanin Dina
        if (
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 3 && $pancawara === 5) ||
            ($saptawara === 4 && $pancawara === 5) ||
            ($saptawara === 6 && $pancawara === 4) ||
            ($saptawara === 7 && $pancawara === 5)
        ) {
            $dewasaAyu[] = 'Purwanin Dina';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 185. Rangda Tiga
        if ($wuku === 7 || $wuku === 8 || $wuku === 15 || $wuku === 16 || $wuku === 23 || $wuku === 24) {
            $dewasaAyu[] = 'Rangda Tiga';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 186. Rarung Pagelangan
        if ($saptawara === 5 && ($sasihDay1 === 6 || $sasihDay2 === 6)) {
            $dewasaAyu[] = 'Rarung Pagelangan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 187. Ratu Magelung
        if ($saptawara === 4 && $wuku === 23) {
            $dewasaAyu[] = 'Ratu Magelung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 188. Ratu Mangure
        if ($saptawara === 5 && $wuku === 20) {
            $dewasaAyu[] = 'Ratu Mangure';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 189. Ratu Megambahan
        if ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 6))) {
            $dewasaAyu[] = 'Ratu Megambahan';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 190. Ratu Nanyingal
        if ($saptawara === 5 && $wuku === 21) {
            $dewasaAyu[] = 'Ratu Nanyingal';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 191. Ratu Ngemban Putra
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            $dewasaAyu[] = 'Ratu Ngemban Putra';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 192. Rekatadala Ayudana
        if ($saptawara === 1 && ($sasihDay1 === 1 || $sasihDay1 === 6 || $sasihDay1 === 11 || $sasihDay1 === 2 || $sasihDay2 === 6 || $sasihDay2 === 11)) {
            $dewasaAyu[] = 'Rekatadala Ayudana';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 193. Salah Wadi
        if ($wuku === 1 || $wuku === 2 || $wuku === 6 || $wuku === 10 || $wuku === 11 || $wuku === 16 || $wuku === 19 || $wuku === 20 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 30) {
            $dewasaAyu[] = 'Salah Wadi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
            // dd($keterangan);
        }

        // 194. Sampar Wangke
        if ($saptawara === 2 && $sadwara === 2) {
            $dewasaAyu[] = 'Sampar Wangke';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 195. Sampi Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 4) {
            $dewasaAyu[] = 'Sampi Gumarang Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 196. Sampi Gumarang Turun
        if ($pancawara === 3 && $sadwara === 1) {
            $dewasaAyu[] = 'Sampi Gumarang Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 197. Sarik Agung
        if ($saptawara === 4 && ($wuku === 25 || $wuku === 4 || $wuku === 11 || $wuku === 18)) {
            $dewasaAyu[] = 'Sarik Agung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 198. Sarik Ketah
        if (($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            $dewasaAyu[] = 'Sarik Ketah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 199. Sedana Tiba
        if ($saptawara === 5 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) {
            $dewasaAyu[] = 'Sedana Tiba';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Sedana Yoga';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            $dewasaAyu[] = 'Semut Sadulur';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            $dewasaAyu[] = 'Siwa Sampurna';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            $dewasaAyu[] = 'Sri Bagia';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Sedana Yoga';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            $dewasaAyu[] = 'Semut Sadulur';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            $dewasaAyu[] = 'Siwa Sampurna';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            $dewasaAyu[] = 'Sri Bagia';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 204. Sri Murti
        if ($sadwara === 5 && $astawara === 1) {
            $dewasaAyu[] = 'Sri Murti';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 205. Sri Tumpuk
        if ($astawara === 1) {
            $dewasaAyu[] = 'Sri Tumpuk';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 206. Srigati
        if (($triwara === 3 && $pancawara === 1 && $sadwara === 3) ||
            ($triwara === 3 && $pancawara === 1 && $sadwara === 6)
        ) {
            $dewasaAyu[] = 'Srigati';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 207. Srigati Jenek
        if ($pancawara === 5 && $sadwara === 6) {
            $dewasaAyu[] = 'Srigati Jenek';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 208. Srigati Munggah
        if ($pancawara === 1 && $sadwara === 3) {
            $dewasaAyu[] = 'Srigati Munggah';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 209. Srigati Turun
        if ($pancawara === 1 && $sadwara === 6) {
            $dewasaAyu[] = 'Srigati Turun';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Subhacara';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 211. Swarga Menga
        if (($saptawara === 3 && $pancawara === 3 && $wuku === 3 &&
                (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 5 && $pancawara === 2 && $wuku === 4)
        ) {
            $dewasaAyu[] = 'Swarga Menga';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 212. Taliwangke
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 27 || $wuku === 28 || $wuku === 29 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 3 || $wuku === 4 || $wuku === 6)) ||
            ($saptawara === 5 && ($wuku === 7 || $wuku === 8 || $wuku === 9 || $wuku === 10 || $wuku === 11 || $wuku === 17 || $wuku === 18 || $wuku === 20 || $wuku === 21)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 13 || $wuku === 14 || $wuku === 15 || $wuku === 16))
        ) {
            $dewasaAyu[] = 'Taliwangke';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
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
            $dewasaAyu[] = 'Titibuwuk';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 214. Tunut Masih
        if (($saptawara === 1 && $wuku === 18) ||
            ($saptawara === 2 && ($wuku === 12 || $wuku === 13 || $wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 17 || $wuku === 24)) ||
            ($saptawara === 5 && $wuku === 1) ||
            ($saptawara === 6 && ($wuku === 19 || $wuku === 22))
        ) {
            $dewasaAyu[] = 'Tunut Masih';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 215. Tutur Mandi
        if (($saptawara === 1 && $wuku === 26) ||
            ($saptawara === 5 && ($wuku === 3 || $wuku === 9 || $wuku === 15 || $wuku === 20 || $wuku === 21 || $wuku === 24)) ||
            ($saptawara === 6 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 24)
        ) {
            $dewasaAyu[] = 'Tutur Mandi';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 216. Uncal Balung
        if ($wuku === 12 || $wuku === 13 || (($wuku === 14 && $saptawara === 1) || ($wuku === 16 && $saptawara < 5))) {
            $dewasaAyu[] = 'Uncal Balung';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 217. Upadana Merta
        if (
            $saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 8 || $sasihDay1 === 6 || $sasihDay1 === 10)) ||
                ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 8 || $sasihDay2 === 6 || $sasihDay2 === 10)))
        ) {
            $dewasaAyu[] = 'Upadana Merta';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 218. Werdi Suka
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)) &&
            ($no_sasih === 1)
        ) {
            $dewasaAyu[] = 'Werdi Suka';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 219. Wisesa
        if (
            $saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 13) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 13)) &&
            ($no_sasih === 1)
        ) {
            $dewasaAyu[] = 'Wisesa';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 220. Wredhi Guna
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)) &&
            ($no_sasih === 1)
        ) {
            $dewasaAyu[] = 'Wredhi Guna';
            $keterangan[] = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // Remove leading comma and space
        // $dewasaAyu = ltrim($dewasaAyu, ', ');

        if ($makna) {
            return response()->json([
                'dewasaAyu' => $dewasaAyu,
                'keterangan' => $keterangan,
            ], 200);
        } else {
            return response()->json([
                'dewasaAyu' => $dewasaAyu,
            ], 200);
        }
    }
}
