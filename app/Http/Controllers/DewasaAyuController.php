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
        $dewasa_ayu = [];

        $cacheKey = 'processed-data-' . $tanggal_mulai . '-' . $tanggal_selesai;

        $tanggal_mulai = Carbon::parse($tanggal_mulai);
        $tanggal_selesai = Carbon::parse($tanggal_selesai);

        if ($tanggal_selesai->lessThan($tanggal_mulai)) {
            return response()->json(['message' => 'Input tanggal_mulai dan tanggal_selesai tidak valid'], 400);
        }

        // Mengecek apakah hasil pemrosesan data sudah ada dalam cache
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
            $end = microtime(true);
            $executionTime = $end - $start;
            $executionTime = number_format($executionTime, 6);

            return response()->json([
                'message' => 'Data telah diambil dari cache.',
                'dewasaAyu' => $result,
                'waktu_eksekusi' => $executionTime
            ]);
        }

        while ($tanggal_mulai <= $tanggal_selesai) {
            $dewasa_ayu[] = [
                'tanggal' => $tanggal_mulai->toDateString(),
                'data' => $this->getDewasaAyu($tanggal_mulai->toDateString(), $makna),
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
            'result' => $dewasa_ayu,
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
        // $keterangan = [];

        // 1. AgniAgungDoyanBasmi: Selasa Purnama dengan Asta Wara Brahma
        if (($saptawara === 3 && ($astawara === 6 || $purnama_tilem === 'Purnama'))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Agni Agung Doyan Basmi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Agni Agung Doyan Basmi',]);
            }
            // $keterangan[] = $isi_keterangan[0]->first();
        }

        // 2. Agni Agung Patra Limutan: Minggu dengan Asta Wara Brahma
        if ($saptawara === 1 && $astawara === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Agni Agung Patra Limutan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Agni Agung Patra Limutan']);
            }
        }

        // 3. Amerta Akasa: Anggara Purnama
        if ($saptawara === 3 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Akasa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Akasa']);
            }
        }

        // 4. Amerta Bumi: Soma Wage Penanggal 1. Buda Pon Penanggal 10.
        if (($saptawara === 2 && $pancawara === 4 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($saptawara === 4 && $pancawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 10 || $sasihDay2 === 10))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Bumi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Bumi']);
            }
        }

        // 5. Amerta Bhuwana: Redite Purnama, Soma Purnama, dan Anggara Purnama
        if (($saptawara === 1 || $saptawara === 2 || $saptawara === 3) && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Bhuwana', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Bhuwana']);
            }
        }

        // 6. Amerta Dadi: Soma Beteng atau Purnama Kajeng
        if (($saptawara === 2 && $triwara === 2) || ($triwara === 3 && $purnama_tilem === 'Purnama')) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dadi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dadi']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Danta', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Danta']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dewa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dewa']);
            }
        }

        // 9. Amerta Dewa Jaya
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay1 === 12 || $sasihDay2 === 12)))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dewa Jaya', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dewa Jaya']);
            }
        }

        // 10. Amerta Dewata
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dewata', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Dewata']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Gati', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Gati']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Kundalini', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Kundalini']);
            }
        }

        // 13. Amerta Masa
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Masa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Masa']);
            }
        }

        // 14. Amerta Murti
        if ($saptawara === 4 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Murti', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Murti']);
            }
        }

        // 15. Amerta Pageh
        if ($saptawara === 7 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Pageh', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Pageh']);
            }
        }

        // 16. Amerta Pepageran
        if ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $astawara === 4)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Pepageran', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Pepageran']);
            }
        }

        // 17. Amerta Sari
        if ($saptawara === 4 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Sari', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Sari']);
            }
        }

        // 18. Amerta Wija
        if ($saptawara === 5 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Wija', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Wija']);
            }
        }

        // 19. Amerta Yoga
        if (
            ($saptawara === 2 && ($wuku === 2 || $wuku === 5 || $wuku === 14 || $wuku === 17 || $wuku === 20 || $wuku === 23 || $wuku === 26 || $wuku === 29)) ||
            ($saptawara === 5 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 5) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 5))) ||
            (($no_sasih === 10) && (($pengalantaka === 'Pangelong' && $sasihDay1 === 4) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 4))) ||
            (($no_sasih === 12) && (($pengalantaka === 'Pangelong' && $sasihDay1 === 1) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 1)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Yoga', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Amerta Yoga']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Asuajag Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Asuajag Munggah']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Asuajag Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Asuajag Turun']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Asuasa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Asuasa']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ayu Bhadra', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ayu Bhadra']);
            }
        }

        // 24. Ayu Dana
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ayu Dana', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ayu Dana']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ayu Nulus', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ayu Nulus']);
            }
        }

        // 26. Babi Munggah
        if ($pancawara === 4 && $sadwara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Babi Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Babi Munggah']);
            }
        }

        // 27. Babi Turun
        if ($pancawara === 4 && $sadwara === 4) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Babi Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Babi Turun']);
            }
        }

        // 28. Banyu Milir
        if (
            ($saptawara === 1 && $wuku === 4) ||
            ($saptawara === 2 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 13)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Banyu Milir', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Banyu Milir']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Banyu Urug', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Banyu Urug']);
            }
        }

        // 30. Bojog Munggah
        if ($pancawara === 5 && $sadwara === 5) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Bojog Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Bojog Munggah']);
            }
        }

        // 31. Bojog Turun
        if ($pancawara === 5 && $sadwara === 2) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Bojog Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Bojog Turun']);
            }
        }

        // 32. Buda Gajah
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Buda Gajah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Buda Gajah']);
            }
        }

        // 33. Buda Ireng
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Tilem') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Buda Ireng', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Buda Ireng']);
            }
        }

        // 34. Buda Suka
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Tilem') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Buda Suka', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Buda Suka']);
            }
        }

        // 35. Carik Walangati
        if (
            $wuku === 1 || $wuku === 6 || $wuku === 10 || $wuku === 12 || $wuku === 24 ||
            $wuku === 25 || $wuku === 27 || $wuku === 28 || $wuku === 30 || $wuku === 7
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Carik Walangati', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Carik Walangati']);
            }
        }

        // 36. Catur Laba
        if (
            ($saptawara === 1 && $pancawara === 1) ||
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 4 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 2)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Catur Laba', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Catur Laba']);
            }
        }

        // 37. Cintamanik
        if ($saptawara === 4 && ($wuku % 2 === 1)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Cintamanik', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Cintamanik']);
            }
        }

        // 38. Corok Kodong
        if ($saptawara === 5 && $pancawara === 5 && $wuku === 13) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Corok Kodong', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Corok Kodong']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'DagDig Karana', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'DagDig Karana']);
            }
        }

        // 40. Dasa Amertha
        if ($saptawara === 6 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dasa Amertha', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dasa Amertha']);
            }
        }

        // 41. Dasa Guna
        if ($saptawara === 4 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem')) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dasa Guna', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dasa Guna']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dauh Ayu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dauh Ayu']);
            }
        }

        // 43. Derman Bagia
        if ($saptawara === 2 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 12 || $sasihDay2 === 12)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Derman Bagia', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Derman Bagia']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Ngelayang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Ngelayang']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Satata', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Satata']);
            }
        }

        // 46. Dewa Werdhi
        if ($saptawara === 6 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Werdhi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Werdhi']);
            }
        }

        // 47. Dewa Mentas
        if ($saptawara === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Mentas', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewa Mentas']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewasa Ngelayang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewasa Ngelayang']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewasa Tanian', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dewasa Tanian']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dina Carik', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dina Carik']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dina Jaya', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dina Jaya']);
            }
        }

        // 52. Dina Mandi
        if (
            ($saptawara === 3 && $purnama_tilem === 'Purnama') ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dina Mandi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dina Mandi']);
            }
        }

        // 53. Dirgahayu
        if ($saptawara === 3 && $pancawara === 3 && $dasawara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dirgahayu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dirgahayu']);
            }
        }

        // 54. DirghaYusa
        if ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dirgha Yusa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Dirgha Yusa']);
            }
        }

        // 55. Gagak Anungsung Pati
        if (
            ($pengalantaka === 'Penanggal' && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 14 || $sasihDay2 === 14))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Gagak Anungsung Pati', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Gagak Anungsung Pati']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geheng Manyinget', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geheng Manyinget']);
            }
        }

        // 57. Geni Agung
        if (
            ($saptawara === 1 && $pancawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 3 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Agung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Agung']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Murub', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Murub']);
            }
        }

        // 59. Geni Rawana
        if (
            (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11))) ||
            (($pengalantaka === 'Pangelong' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13)) || ($pengalantaka === 'Pangelong' && ($sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Rawana', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Rawana']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Rawana Jejepan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Rawana Jejepan']);
            }
        }

        // 61. Geni Rawana Rangkep
        if (
            (($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11 || $sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11)) || ($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13 || $sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Rawana Rangkep', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Geni Rawana Rangkep']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Guntur Graha', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Guntur Graha']);
            }
        }

        // 63. Ingkel Macan
        if ($saptawara === 5 && $pancawara === 3 && $wuku === 7) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ingkel Macan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ingkel Macan']);
            }
        }

        // 64. Istri Payasan
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Istri Payasan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Istri Payasan']);
            }
        }

        // 65. Jiwa Manganti
        if (($saptawara === 2 && $wuku === 19) || ($saptawara === 5 && ($wuku === 2 || $wuku === 20)) || ($saptawara === 6 && ($wuku === 25 || $wuku === 7)) || ($saptawara === 7 && $wuku === 30)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Jiwa Manganti', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Jiwa Manganti']);
            }
        }

        // 66. Kajeng Kipkipan
        if ($saptawara === 4 && ($wuku === 6 || $wuku === 30)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Kipkipan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Kipkipan']);
            }
        }

        // 67. Kajeng Kliwon Enyitan
        if ($triwara === 3 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 < 15 && $sasihDay1 > 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 < 15 && $sasihDay2 > 7))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Kliwon Enyitan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Kliwon Enyitan']);
            }
        }

        // 68. Kajeng Lulunan
        if ($triwara === 3 && $astawara === 5 && $sangawara === 9) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Lulunan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Lulunan']);
            }
        }

        // 69. Kajeng Rendetan
        if ($triwara === 3 && $pengalantaka === 'Penanggal' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Rendetan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Rendetan']);
            }
        }

        // 70. Kajeng Susunan
        if ($triwara === 3 && $astawara === 3 && $sangawara === 9) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Susunan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Susunan']);
            }
        }

        // 71. Kajeng Uwudan
        if ($triwara === 3 && $pengalantaka === 'Pangelong' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Uwudan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kajeng Uwudan']);
            }
        }

        // 72. Kala Alap
        if ($saptawara === 2 && $wuku === 22) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Alap', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Alap']);
            }
        }

        // 73. Kala Angin
        if ($saptawara === 1 && ($wuku === 17 || $wuku === 25 || $wuku === 28)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Angin', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Angin']);
            }
        }

        // 74. Kala Atat
        if (($saptawara === 1 && $wuku === 22) || ($saptawara === 3 && $wuku === 30) || ($saptawara === 4 && $wuku === 19)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Atat', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Atat']);
            }
        }

        // 75. Kala Awus
        if ($saptawara === 4 && $wuku === 28) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Awus', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Awus']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Bancaran', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Bancaran']);
            }
        }

        // 77. Kala Bangkung, Kala Nanggung
        if (
            $saptawara === 1 && $pancawara === 3 ||
            $saptawara === 2 && $pancawara === 2 ||
            $saptawara === 4 && $pancawara === 1 ||
            $saptawara === 7 && $pancawara === 4
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Bangkung, Kala Nanggung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Bangkung, Kala Nanggung']);
            }
        }

        // 78. Kala Beser
        if ($sadwara === 1 && $astawara === 7) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Beser', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Beser']);
            }
        }

        // 79. Kala Brahma
        if (
            $saptawara === 1 && $wuku === 23 ||
            $saptawara === 3 && $wuku === 14 ||
            $saptawara === 4 && $wuku === 1 ||
            $saptawara === 6 && ($wuku === 4 || $wuku === 25 || $wuku === 30) ||
            $saptawara === 7 && $wuku === 13
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Brahma', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Brahma']);
            }
        }

        // 80. Kala Bregala
        if ($saptawara === 2 && $wuku === 2) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Bregala', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Bregala']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Buingrau', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Buingrau']);
            }
        }

        // 82. Kala Cakra
        if ($saptawara === 7 && $wuku === 23) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Cakra', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Cakra']);
            }
        }

        // 83. Kala Capika
        if ($saptawara === 1 && $wuku === 18 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Capika', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Capika']);
            }
        }

        // 84. Kala Caplokan
        if (($saptawara === 2 && ($wuku === 18 || $wuku === 9)) ||
            ($saptawara === 3 && $wuku === 19) ||
            ($saptawara === 4 && $wuku === 24) ||
            ($saptawara === 6 && $wuku === 12) ||
            ($saptawara === 7 && ($wuku === 9 || $wuku === 15 || $wuku === 1))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Caplokan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Caplokan']);
            }
        }

        // 85. Kala Cepitan
        if ($saptawara === 2 && $pancawara === 2 && $wuku === 18) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Cepitan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Cepitan']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Dangastra', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Dangastra']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Dangu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Dangu']);
            }
        }

        // 88. Kala Demit
        if ($saptawara === 7 && $wuku === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Demit', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Demit']);
            }
        }

        // 89. Kala Empas Munggah
        if ($pancawara === 4 && $sadwara === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Empas Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Empas Munggah']);
            }
        }

        // 90. Kala Empas Turun
        if ($pancawara === 4 && $sadwara === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Empas Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Empas Turun']);
            }
        }

        // 91. Kala Gacokan
        if ($saptawara === 3 && $wuku === 19) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gacokan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gacokan']);
            }
        }

        // 92. Kala Garuda
        if ($saptawara === 3 && $wuku === 2) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Garuda', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Garuda']);
            }
        }

        // 93. Kala Geger
        if (($saptawara === 5 || $saptawara === 7) && $wuku === 7) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Geger', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Geger']);
            }
        }

        // 94. Kala Gotongan
        if (($saptawara === 6 && $pancawara === 5) ||
            ($saptawara === 7 && $pancawara === 1) ||
            ($saptawara === 1 && $pancawara === 2)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gotongan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gotongan']);
            }
        }

        // 95. Kala Graha
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 5)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Graha', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Graha']);
            }
        }

        // 96. Kala Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gumarang Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gumarang Munggah']);
            }
        }

        // 97. Kala Gumarang Turun
        if ($pancawara === 3 && $sadwara === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gumarang Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Gumarang Turun']);
            }
        }

        // 98. Kala Guru
        if ($saptawara === 4 && $wuku === 2) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Guru', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Guru']);
            }
        }

        // 99. Kala Ingsor
        if ($wuku === 4 || $wuku === 14 || $wuku === 24) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ingsor', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ingsor']);
            }
        }

        // 100. Kala Isinan
        if (($saptawara === 2 && ($wuku === 11 || $wuku === 17)) ||
            ($saptawara === 4 && $wuku === 30)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Isinan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Isinan']);
            }
        }

        // 101. Kala Jangkut
        if ($triwara === 3 && $dwiwara === 2) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Jangkut', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Jangkut']);
            }
        }

        // 102. Kala Jengkang
        if ($saptawara === 1 && $pancawara === 1 && $wuku === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Jengkang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Jengkang']);
            }
        }

        // 103. Kala Jengking
        if ($sadwara === 3 && $astawara === 7) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Jengking', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Jengking']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Katemu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Katemu']);
            }
        }

        // 105. Kala Keciran
        if ($saptawara === 4 && $wuku === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Keciran', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Keciran']);
            }
        }

        // 106. Kala Kilang-Kilung
        if (($saptawara === 2 && $wuku === 17) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kilang-Kilung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kilang-Kilung']);
            }
        }

        // 107. Kala Kingkingan
        if ($saptawara === 5 && $wuku === 17) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kingkingan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kingkingan']);
            }
        }

        // 108. Kala Klingkung
        if ($saptawara === 3 && $wuku === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Klingkung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Klingkung']);
            }
        }

        // 109. Kala Kutila Manik
        if ($triwara === 3 && $pancawara === 5) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kutila Manik', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kutila Manik']);
            }
        }

        // 110. Kala Kutila
        if ($sadwara === 2 && $astawara === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kutila', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Kutila']);
            }
        }

        // 111. Kala Luang
        if (($saptawara === 1 && ($wuku === 11 || $wuku === 12 || $wuku === 13)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 10 || $wuku === 8 || $wuku === 19 || $wuku === 23 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 18)) ||
            ($saptawara === 5 && ($wuku === 28 || $wuku === 29))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Luang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Luang']);
            }
        }

        // 112. Kala Lutung Megelut
        if (($saptawara === 1 && $wuku === 3) || ($saptawara === 4 && $wuku === 10)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Lutung Megelut', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Lutung Megelut']);
            }
        }

        // 113. Kala Lutung Megandong
        if ($saptawara === 5 && $pancawara === 5) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Lutung Megandong', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Lutung Megandong']);
            }
        }

        // 114. Kala Macan
        if ($saptawara === 5 && $wuku === 19) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Macan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Macan']);
            }
        }

        // 115. Kala Mangap
        if ($saptawara === 1 && $pancawara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mangap', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mangap']);
            }
        }

        // 116. Kala Manguneb
        if ($saptawara === 5 && $pancawara === 14) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Manguneb', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Manguneb']);
            }
        }

        // 117. Kala Matampak
        if (($saptawara === 4 && $wuku === 3) ||
            ($saptawara === 5 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 3) ||
            ($saptawara === 7 && ($wuku === 7 || $wuku === 24))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Matampak', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Matampak']);
            }
        }

        // 118. Kala Mereng
        if (($saptawara === 1 && ($wuku === 9 || $wuku === 24)) ||
            ($saptawara === 2 && ($wuku === 11 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 13)) ||
            ($saptawara === 4 && ($wuku === 15 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 2 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 21))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mereng', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mereng']);
            }
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Miled', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Miled']);
            }
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mina', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mina']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mretyu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mretyu']);
            }
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muas', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muas']);
            }
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncar', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncar']);
            }
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncrat', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncrat']);
            }
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngadeg', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngadeg']);
            }
        }

        // 118. Kala Mereng
        if (($saptawara === 1 && ($wuku === 9 || $wuku === 24)) ||
            ($saptawara === 2 && ($wuku === 11 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 13)) ||
            ($saptawara === 4 && ($wuku === 15 || $wuku === 30)) ||
            ($saptawara === 5 && ($wuku === 2 || $wuku === 17 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 21))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mereng', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mereng']);
            }
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Miled', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Miled']);
            }
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mina', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mina']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mretyu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Mretyu']);
            }
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muas', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muas']);
            }
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncar', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncar']);
            }
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncrat', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Muncrat']);
            }
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngadeg', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngadeg']);
            }
        }

        // 126. Kala Ngamut
        if ($saptawara === 2 && $wuku === 18) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngamut', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngamut']);
            }
        }

        // 127. Kala Ngruda
        if (($saptawara === 1 && ($wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 23 || $wuku === 10)) ||
            ($saptawara === 7 && ($wuku === 10))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngruda', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngruda']);
            }
        }

        // 128. Kala Ngunya
        if ($saptawara === 1 && $wuku === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngunya', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Ngunya']);
            }
        }

        // 129. Kala Olih
        if ($saptawara === 4 && $wuku === 24) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Olih', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Olih']);
            }
        }

        // 130. Kala Pacekan
        if ($saptawara === 3 && $wuku === 5) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pacekan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pacekan']);
            }
        }

        // 131. Kala Pager
        if ($saptawara === 5 && $wuku === 7) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pager', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pager']);
            }
        }

        // 132. Kala Panyeneng
        if (($saptawara === 1 && $wuku === 7) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Panyeneng', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Panyeneng']);
            }
        }

        // 133. Kala Pati
        if (($saptawara === 1 && ($wuku === 10 || $wuku === 2)) ||
            ($saptawara === 3 && ($wuku === 6 || $wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 10 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 17))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pati', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pati']);
            }
        }

        // 134. Kala Pati Jengkang
        if ($saptawara === 5 && $sadwara === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pati Jengkang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pati Jengkang']);
            }
        }

        // 135. Kala Pegat
        if (
            $saptawara === 4 && $wuku === 12 ||
            $saptawara === 7 && ($wuku === 3 || $wuku === 18)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pegat', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Pegat']);
            }
        }

        // 136. Kala Prawani
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 3 && $wuku === 24) ||
            ($saptawara === 4 && $wuku === 2) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Prawani', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Prawani']);
            }
        }

        // 137. Kala Raja
        if ($saptawara === 5 && $wuku === 29) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Raja', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Raja']);
            }
        }

        // 138. Kala Rau
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 7 && ($wuku === 3 || $wuku === 4 || $wuku === 18)) ||
            ($saptawara === 6 && $wuku === 6)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Rau', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Rau']);
            }
        }

        // 139. Kala Rebutan
        if ($saptawara === 2 && $wuku === 26) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Rebutan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Rebutan']);
            }
        }

        // 140. Kala Rumpuh
        if (($saptawara === 1 && ($wuku === 18 || $wuku === 30)) ||
            ($saptawara === 2 && ($wuku === 9 || $wuku === 20)) ||
            ($saptawara === 4 && ($wuku === 10 || $wuku === 19 || $wuku === 25 || $wuku === 26 || $wuku === 27)) ||
            ($saptawara === 5 && ($wuku === 13 || $wuku === 14 || $wuku === 17 || $wuku === 22 || $wuku === 24)) ||
            ($saptawara === 6 && ($wuku === 11 || $wuku === 12)) ||
            ($saptawara === 7 && ($wuku === 21 || $wuku === 23 || $wuku === 28 || $wuku === 29))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Rumpuh', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Rumpuh']);
            }
        }

        // 141. Kala Sapuhau
        if (($saptawara === 2 && $wuku === 3) ||
            ($saptawara === 3 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sapuhau', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sapuhau']);
            }
        }

        // 142. Kala Sarang
        if ($wuku === 7 || $wuku === 17) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sarang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sarang']);
            }
        }

        // 143. Kala Siyung
        if (($saptawara === 1 && ($wuku === 2 || $wuku === 21)) ||
            ($saptawara === 2 && ($wuku === 1 || $wuku === 10 || $wuku === 25)) ||
            ($saptawara === 4 && ($wuku === 1 || $wuku === 20)) ||
            ($saptawara === 5 && ($wuku === 24 || $wuku === 26)) ||
            ($saptawara === 6 && $wuku === 28) ||
            ($saptawara === 7 && ($wuku === 15 || $wuku === 17))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Siyung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Siyung']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sor', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sor']);
            }
        }

        // 145. Kala Sudangastra
        if (($saptawara === 1 && $wuku === 24) ||
            ($saptawara === 3 && $wuku === 28) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 12)) ||
            ($saptawara === 5 && $wuku === 19) ||
            ($saptawara === 7 && $wuku === 6)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sudangastra', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sudangastra']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sudukan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sudukan']);
            }
        }

        // 147. Kala Sungsang
        if ($wuku === 27) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sungsang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Sungsang']);
            }
        }

        // 148. Kala Susulan
        if ($saptawara === 2 && $wuku === 11) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Susulan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Susulan']);
            }
        }

        // 149. Kala Suwung
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 3 && ($wuku === 8 || $wuku === 10)) ||
            ($saptawara === 4 && ($wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 11 || $wuku === 13 || $wuku === 14))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Suwung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Suwung']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tampak', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tampak']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Temah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Temah']);
            }
        }

        // 152. Kala Timpang
        if (($saptawara === 3 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 14) ||
            ($saptawara === 7 && $wuku === 2)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Timpang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Timpang']);
            }
        }

        // 153. Kala Tukaran
        if ($saptawara === 3 && ($wuku === 3 || $wuku === 8)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tukaran', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tukaran']);
            }
        }

        // 154. Kala Tumapel
        if ($wuku === 12 && ($saptawara === 3 || $saptawara === 4)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tumapel', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tumapel']);
            }
        }

        // 155. Kala Tumpar
        if (($saptawara === 3 && $wuku === 13) || ($saptawara === 4 && $wuku === 8)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tumpar', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Tumpar']);
            }
        }

        // 156. Kala Upa
        if ($sadwara === 4 && $triwara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Upa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Upa']);
            }
        }

        // 157. Kala Was
        if ($saptawara === 2 && $wuku === 17) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Was', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Was']);
            }
        }

        // 158. Kala Wikalpa
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 25)) || ($saptawara === 6 && ($wuku === 27 || $wuku === 30))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Wikalpa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Wikalpa']);
            }
        }

        // 159. Kala Wisesa
        if ($sadwara === 5 && $astawara === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Wisesa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Wisesa']);
            }
        }

        // 160. Kala Wong
        if ($saptawara === 4 && $wuku === 20) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Wong', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kala Wong']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kaleburau', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kaleburau']);
            }
        }

        // 162. Kamajaya
        if ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3 || $sasihDay2 === 7)))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kamajaya', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Kamajaya']);
            }
        }

        // 163. Karna Sula
        if (
            ($saptawara === 1 && ($sasihDay1 === 2 || $sasihDay2 === 2)) ||
            ($saptawara === 3 && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem'))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Karna Sula', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Karna Sula']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Karnasula', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Karnasula']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Lebur Awu', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Lebur Awu']);
            }
        }

        // 166. Lutung Magandong
        if ($saptawara === 5 && ($wuku === 3 || $wuku === 8 || $wuku === 13 || $wuku === 18 || $wuku === 23 || $wuku === 28)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Lutung Magandong', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Lutung Magandong']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Macekan Agung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Macekan Agung']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Macekan Lanang', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Macekan Lanang']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Macekan Wadon', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Macekan Wadon']);
            }
        }

        // 170. Merta Sula
        if ($saptawara === 5 && ($sasihDay1 === 7 || $sasihDay2 === 7)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Merta Sula', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Merta Sula']);
            }
        }

        // 171. Naga Naut
        if ($sasihDay1 === 'no_sasih' || $sasihDay2 === 'no_sasih') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Naga Naut', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Naga Naut']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Pamacekan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Pamacekan']);
            }
        }

        // 173. Panca Amerta
        if ($saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Panca Amerta', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Panca Amerta']);
            }
        }

        // 174. Panca Prawani
        if ($sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 12 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 12) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Panca Prawani', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Panca Prawani']);
            }
        }

        // 175. Panca Wedhi
        if ($saptawara === 2 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Panca Werdhi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Panca Werdhi']);
            }
        }

        // 176. Pati Paten
        if ($saptawara === 6 && (($sasihDay1 === 10 || $sasihDay2 === 10) || $purnama_tilem === 'Tilem')) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Pati Paten', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Pati Paten']);
            }
        }

        // 177. Patra Limutan
        if ($triwara === 3 && $purnama_tilem === 'Tilem') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Patra Limutan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Patra Limutan']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Pepedan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Pepedan']);
            }
        }

        // 179. Prabu Pendah
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Prabu Pendah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Prabu Pendah']);
            }
        }

        // 180. Prangewa
        if ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Prangewa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Prangewa']);
            }
        }

        // 181. Purnama Danta
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purnama Danta', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purnama Danta']);
            }
        }

        // 182. Purna Suka
        if ($saptawara === 6 && $pancawara === 1 && $purnama_tilem === 'Purnama') {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purna Suka', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purna Suka']);
            }
        }

        // 183. Purwani
        if ($sasihDay1 === 14 || $sasihDay2 === 14) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purwani', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purwani']);
            }
        }

        // 184. Purwanin Dina
        if (
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 3 && $pancawara === 5) ||
            ($saptawara === 4 && $pancawara === 5) ||
            ($saptawara === 6 && $pancawara === 4) ||
            ($saptawara === 7 && $pancawara === 5)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purwanin Dina', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Purwanin Dina']);
            }
        }

        // 185. Rangda Tiga
        if ($wuku === 7 || $wuku === 8 || $wuku === 15 || $wuku === 16 || $wuku === 23 || $wuku === 24) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Rangda Tiga', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Rangda Tiga']);
            }
        }

        // 186. Rarung Pagelangan
        if ($saptawara === 5 && ($sasihDay1 === 6 || $sasihDay2 === 6)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Rarung Pagelangan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Rarung Pagelangan']);
            }
        }

        // 187. Ratu Magelung
        if ($saptawara === 4 && $wuku === 23) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Magelung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Magelung']);
            }
        }

        // 188. Ratu Mangure
        if ($saptawara === 5 && $wuku === 20) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Mangure', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Mangure']);
            }
        }

        // 189. Ratu Megambahan
        if ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 6))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Megambahan', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Megambahan']);
            }
        }

        // 190. Ratu Nanyingal
        if ($saptawara === 5 && $wuku === 21) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Nanyingal', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Nanyingal']);
            }
        }

        // 191. Ratu Ngemban Putra
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Ngemban Putra', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Ratu Ngemban Putra']);
            }
        }

        // 192. Rekatadala Ayudana
        if ($saptawara === 1 && ($sasihDay1 === 1 || $sasihDay1 === 6 || $sasihDay1 === 11 || $sasihDay1 === 2 || $sasihDay2 === 6 || $sasihDay2 === 11)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Rekatadala Ayudana', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Rekatadala Ayudana']);
            }
        }

        // 193. Salah Wadi
        if ($wuku === 1 || $wuku === 2 || $wuku === 6 || $wuku === 10 || $wuku === 11 || $wuku === 16 || $wuku === 19 || $wuku === 20 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 30) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Salah Wadi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Salah Wadi']);
            }
            // dd($keterangan);
        }

        // 194. Sampar Wangke
        if ($saptawara === 2 && $sadwara === 2) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sampar Wangke', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sampar Wangke']);
            }
        }

        // 195. Sampi Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 4) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sampi Gumarang Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sampi Gumarang Munggah']);
            }
        }

        // 196. Sampi Gumarang Turun
        if ($pancawara === 3 && $sadwara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sampi Gumarang Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sampi Gumarang Turun']);
            }
        }

        // 197. Sarik Agung
        if ($saptawara === 4 && ($wuku === 25 || $wuku === 4 || $wuku === 11 || $wuku === 18)) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sarik Agung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sarik Agung']);
            }
        }

        // 198. Sarik Ketah
        if (($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sarik Ketah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sarik Ketah']);
            }
        }

        // 199. Sedana Tiba
        if ($saptawara === 5 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sedana Tiba', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sedana Tiba']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sedana Yoga', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sedana Yoga']);
            }
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Semut Sadulur', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Semut Sadulur']);
            }
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Siwa Sampurna', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Siwa Sampurna']);
            }
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Bagia', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Bagia']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sedana Yoga', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sedana Yoga']);
            }
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Semut Sadulur', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Semut Sadulur']);
            }
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Siwa Sampurna', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Siwa Sampurna']);
            }
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Bagia', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Bagia']);
            }
        }

        // 204. Sri Murti
        if ($sadwara === 5 && $astawara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Murti', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Murti']);
            }
        }

        // 205. Sri Tumpuk
        if ($astawara === 1) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Tumpuk', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Sri Tumpuk']);
            }
        }

        // 206. Srigati
        if (($triwara === 3 && $pancawara === 1 && $sadwara === 3) ||
            ($triwara === 3 && $pancawara === 1 && $sadwara === 6)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati']);
            }
        }

        // 207. Srigati Jenek
        if ($pancawara === 5 && $sadwara === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati Jenek', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati Jenek']);
            }
        }

        // 208. Srigati Munggah
        if ($pancawara === 1 && $sadwara === 3) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati Munggah', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati Munggah']);
            }
        }

        // 209. Srigati Turun
        if ($pancawara === 1 && $sadwara === 6) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati Turun', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Srigati Turun']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Subhacara', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Subhacara']);
            }
        }

        // 211. Swarga Menga
        if (($saptawara === 3 && $pancawara === 3 && $wuku === 3 &&
                (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 5 && $pancawara === 2 && $wuku === 4)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Swarga Menga', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Swarga Menga']);
            }
        }

        // 212. Taliwangke
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 27 || $wuku === 28 || $wuku === 29 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 3 || $wuku === 4 || $wuku === 6)) ||
            ($saptawara === 5 && ($wuku === 7 || $wuku === 8 || $wuku === 9 || $wuku === 10 || $wuku === 11 || $wuku === 17 || $wuku === 18 || $wuku === 20 || $wuku === 21)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 13 || $wuku === 14 || $wuku === 15 || $wuku === 16))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Taliwangke', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Taliwangke']);
            }
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
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Titibuwuk', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Titibuwuk']);
            }
        }

        // 214. Tunut Masih
        if (($saptawara === 1 && $wuku === 18) ||
            ($saptawara === 2 && ($wuku === 12 || $wuku === 13 || $wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 17 || $wuku === 24)) ||
            ($saptawara === 5 && $wuku === 1) ||
            ($saptawara === 6 && ($wuku === 19 || $wuku === 22))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Tunut Masih', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Tunut Masih']);
            }
        }

        // 215. Tutur Mandi
        if (($saptawara === 1 && $wuku === 26) ||
            ($saptawara === 5 && ($wuku === 3 || $wuku === 9 || $wuku === 15 || $wuku === 20 || $wuku === 21 || $wuku === 24)) ||
            ($saptawara === 6 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 24)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Tutur Mandi', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Tutur Mandi']);
            }
        }

        // 216. Uncal Balung
        if ($wuku === 12 || $wuku === 13 || (($wuku === 14 && $saptawara === 1) || ($wuku === 16 && $saptawara < 5))) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Uncal Balung', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Uncal Balung']);
            }
        }

        // 217. Upadana Merta
        if (
            $saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 8 || $sasihDay1 === 6 || $sasihDay1 === 10)) ||
                ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 8 || $sasihDay2 === 6 || $sasihDay2 === 10)))
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Upadana Merta', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Upadana Merta']);
            }
        }

        // 218. Werdi Suka
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)) &&
            ($no_sasih === 1)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Werdi Suka', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Werdi Suka']);
            }
        }

        // 219. Wisesa
        if (
            $saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 13) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 13)) &&
            ($no_sasih === 1)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Wisesa', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Wisesa']);
            }
        }

        // 220. Wredhi Guna
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)) &&
            ($no_sasih === 1)
        ) {
            if($makna){
                $keterangan = DewasaAyu::where('nama', end($dewasaAyu))->pluck('keterangan')->first();
                array_push($dewasaAyu, ['dewasa_ayu' => 'Wredhi Guna', 'keterangan' => $keterangan]);
            } else {
                array_push($dewasaAyu, ['dewasa_ayu' => 'Wredhi Guna']);
            }
        }

        // Remove leading comma and space
        // $dewasaAyu = ltrim($dewasaAyu, ', ');

        // if ($makna) {
        //     return response()->json([
        //         'dewasaAyu' => $dewasaAyu,
        //         'keterangan' => $keterangan,
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'dewasaAyu' => $dewasaAyu,
        //     ], 200);
        // }
        return $dewasaAyu;
    }
}
