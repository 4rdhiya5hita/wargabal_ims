<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\AstaWaraController_08;
use App\Http\Controllers\CaturWaraController_04;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DasaWaraController;
use App\Http\Controllers\DwiWaraController_02;
use App\Http\Controllers\EkaWaraController_01;
use App\Http\Controllers\HariSasihController;
use App\Http\Controllers\PancaWaraController_05;
use App\Http\Controllers\PurnamaTilemController;
use App\Http\Controllers\SadWaraController_06;
use App\Http\Controllers\SangaWaraController_09;
use App\Http\Controllers\SaptaWaraController_07;
use App\Http\Controllers\TriWaraController_03;
use App\Http\Controllers\ValidasiAPI;
use App\Http\Controllers\ValidasiTanggal;
use App\Http\Controllers\WukuController;
use App\Models\AlaAyuningDewasa;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;

class AlaAyuningDewasaAPI extends Controller
{
    public function cariAlaAyuningDewasa(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $user = User::where('api_key', $api_key)->first();
        $service_id = 2;

        $validasi_api = new ValidasiAPI();
        $result = $validasi_api->validasiAPI($user, $service_id);
        
        if ($result) {
            return $result;
        }

        $start = microtime(true);
        $tanggal_mulai = $request->input('tanggal_mulai');
        $tanggal_selesai = $request->input('tanggal_selesai');

        $validasi_tanggal = new ValidasiTanggal();
        $response = $validasi_tanggal->validasiTanggal($tanggal_mulai, $tanggal_selesai);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        list($tanggal_mulai, $tanggal_selesai) = $response;

        $makna = $request->has('beserta_keterangan');
        $ala_ayuning_dewasa = [];

        while ($tanggal_mulai <= $tanggal_selesai) {
            $ala_ayuning_dewasa[] = [
                'tanggal' => $tanggal_mulai->toDateString(),
                'ala_ayuning_dewasa' => $this->getAlaAyuningDewasa($tanggal_mulai->toDateString(), $makna),
            ];
            $tanggal_mulai->addDay();
        }

        $end = microtime(true);
        $executionTime = $end - $start;
        $executionTime = number_format($executionTime, 6);

        $response = [
            'pesan' => 'Sukses',
            'data' => $ala_ayuning_dewasa,
            'waktu_eksekusi' => $executionTime,
        ];

        return response()->json($response, 200);
    }

    public function getAlaAyuningDewasa($tanggal, $makna)
    {
        if ($tanggal >= '2000-01-01') {
            $refTanggal = '2000-01-01';
            $wuku = 10;
            $angkaWuku = 70;
            $tahunSaka = 1921;
            $noSasih = 7;
            $penanggal = 10;
            $noNgunaratri = 46;
        } elseif ($tanggal < '1992-01-01') {
            $refTanggal = '1970-01-01';
            $wuku = 5;
            $angkaWuku = 33;
            $tahunSaka = 1891;
            $noSasih = 7;
            $penanggal = 8;
            $noNgunaratri = 50;
        } else {
            $refTanggal = '1992-01-01';
            $wuku = 13;
            $angkaWuku = 88;
            $tahunSaka = 1913;
            $noSasih = 7;
            $penanggal = 11;
            $noNgunaratri = 22;
        }

        // Panggil semua controller yang dibutuhkan
        $wukuController = new WukuController();
        $ekaWaraController = new EkaWaraController_01();
        $dwiWaraController = new DwiWaraController_02();
        $triWaraController = new TriWaraController_03();
        $caturWaraController = new CaturWaraController_04();
        $pancaWaraController = new PancaWaraController_05();
        $sadWaraController = new SadWaraController_06();
        $saptaWaraController = new SaptaWaraController_07();
        $astaWaraController = new AstaWaraController_08();
        $sangaWaraController = new SangaWaraController_09();
        $dasaWaraController = new DasaWaraController();
        // $pengalantakaController = new PengalantakaController;
        $hariSasihController = new HariSasihController();
        $purnamaTilemController = new PurnamaTilemController();

        // Lakukan semua perhitungan hanya sekali
        $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, $angkaWuku, $refTanggal);
        $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);

        $saptawara = $saptaWaraController->getSaptawara($tanggal);
        $urip_saptawara = $saptaWaraController->getUripSaptaWara($saptawara);
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
        $alaAyuningDewasa = [];
        // $description = [];

        // 1. AgniAgungDoyanBasmi: Selasa Purnama dengan Asta Wara Brahma
        if (($saptawara === 3 && ($astawara === 6 || $purnama_tilem === 'Purnama'))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Agni Agung Doyan Basmi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Agni Agung Doyan Basmi',]);
            }
            // $description[] = $isi_description[0]->first();
        }

        // 2. Agni Agung Patra Limutan: Minggu dengan Asta Wara Brahma
        if ($saptawara === 1 && $astawara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Agni Agung Patra Limutan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Agni Agung Patra Limutan']);
            }
        }

        // 3. Amerta Akasa: Anggara Purnama
        if ($saptawara === 3 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Akasa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Akasa']);
            }
        }

        // 4. Amerta Bumi: Soma Wage Penanggal 1. Buda Pon Penanggal 10.
        if (($saptawara === 2 && $pancawara === 4 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($saptawara === 4 && $pancawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 10 || $sasihDay2 === 10))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Bumi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Bumi']);
            }
        }

        // 5. Amerta Bhuwana: Redite Purnama, Soma Purnama, dan Anggara Purnama
        if (($saptawara === 1 || $saptawara === 2 || $saptawara === 3) && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Bhuwana', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Bhuwana']);
            }
        }

        // 6. Amerta Dadi: Soma Beteng atau Purnama Kajeng
        if (($saptawara === 2 && $triwara === 2) || ($triwara === 3 && $purnama_tilem === 'Purnama')) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dadi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dadi']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Danta', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Danta']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dewa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dewa']);
            }
        }

        // 9. Amerta Dewa Jaya
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay1 === 12 || $sasihDay2 === 12)))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dewa Jaya', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dewa Jaya']);
            }
        }

        // 10. Amerta Dewata
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dewata', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Dewata']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Gati', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Gati']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Kundalini', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Kundalini']);
            }
        }

        // 13. Amerta Masa
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Masa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Masa']);
            }
        }

        // 14. Amerta Murti
        if ($saptawara === 4 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Murti', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Murti']);
            }
        }

        // 15. Amerta Pageh
        if ($saptawara === 7 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Pageh', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Pageh']);
            }
        }

        // 16. Amerta Pepageran
        if ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $astawara === 4)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Pepageran', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Pepageran']);
            }
        }

        // 17. Amerta Sari
        if ($saptawara === 4 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Sari', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Sari']);
            }
        }

        // 18. Amerta Wija
        if ($saptawara === 5 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Wija', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Wija']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Yoga', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Amerta Yoga']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Asuajag Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Asuajag Munggah']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Asuajag Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Asuajag Turun']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Asuasa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Asuasa']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ayu Bhadra', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ayu Bhadra']);
            }
        }

        // 24. Ayu Dana
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ayu Dana', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ayu Dana']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ayu Nulus', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ayu Nulus']);
            }
        }

        // 26. Babi Munggah
        if ($pancawara === 4 && $sadwara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Babi Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Babi Munggah']);
            }
        }

        // 27. Babi Turun
        if ($pancawara === 4 && $sadwara === 4) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Babi Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Babi Turun']);
            }
        }

        // 28. Banyu Milir
        if (
            ($saptawara === 1 && $wuku === 4) ||
            ($saptawara === 2 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 13)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Banyu Milir', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Banyu Milir']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Banyu Urug', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Banyu Urug']);
            }
        }

        // 30. Bojog Munggah
        if ($pancawara === 5 && $sadwara === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Bojog Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Bojog Munggah']);
            }
        }

        // 31. Bojog Turun
        if ($pancawara === 5 && $sadwara === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Bojog Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Bojog Turun']);
            }
        }

        // 32. Buda Gajah
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Buda Gajah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Buda Gajah']);
            }
        }

        // 33. Buda Ireng
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Tilem') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Buda Ireng', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Buda Ireng']);
            }
        }

        // 34. Buda Suka
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Tilem') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Buda Suka', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Buda Suka']);
            }
        }

        // 35. Carik Walangati
        if (
            $wuku === 1 || $wuku === 6 || $wuku === 10 || $wuku === 12 || $wuku === 24 ||
            $wuku === 25 || $wuku === 27 || $wuku === 28 || $wuku === 30 || $wuku === 7
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Carik Walangati', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Carik Walangati']);
            }
        }

        // 36. Catur Laba
        if (
            ($saptawara === 1 && $pancawara === 1) ||
            ($saptawara === 2 && $pancawara === 4) ||
            ($saptawara === 4 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 2)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Catur Laba', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Catur Laba']);
            }
        }

        // 37. Cintamanik
        if ($saptawara === 4 && ($wuku % 2 === 1)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Cintamanik', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Cintamanik']);
            }
        }

        // 38. Corok Kodong
        if ($saptawara === 5 && $pancawara === 5 && $wuku === 13) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Corok Kodong', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Corok Kodong']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'DagDig Karana', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'DagDig Karana']);
            }
        }

        // 40. Dasa Amertha
        if ($saptawara === 6 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dasa Amertha', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dasa Amertha']);
            }
        }

        // 41. Dasa Guna
        if ($saptawara === 4 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem')) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dasa Guna', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dasa Guna']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dauh Ayu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dauh Ayu']);
            }
        }

        // 43. Derman Bagia
        if ($saptawara === 2 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 12 || $sasihDay2 === 12)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Derman Bagia', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Derman Bagia']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Ngelayang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Ngelayang']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Satata', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Satata']);
            }
        }

        // 46. Dewa Werdhi
        if ($saptawara === 6 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Werdhi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Werdhi']);
            }
        }

        // 47. Dewa Mentas
        if ($saptawara === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Mentas', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dewa Mentas']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dewasa Ngelayang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dewasa Ngelayang']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dewasa Tanian', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dewasa Tanian']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dina Carik', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dina Carik']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dina Jaya', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dina Jaya']);
            }
        }

        // 52. Dina Mandi
        if (
            ($saptawara === 3 && $purnama_tilem === 'Purnama') ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 2) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 2))) ||
            ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 3) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 3)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dina Mandi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dina Mandi']);
            }
        }

        // 53. Dirgahayu
        if ($saptawara === 3 && $pancawara === 3 && $dasawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dirgahayu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dirgahayu']);
            }
        }

        // 54. DirghaYusa
        if ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Dirgha Yusa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Dirgha Yusa']);
            }
        }

        // 55. Gagak Anungsung Pati
        if (
            ($pengalantaka === 'Penanggal' && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($pengalantaka === 'Pengelong' && ($sasihDay1 === 14 || $sasihDay2 === 14))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Gagak Anungsung Pati', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Gagak Anungsung Pati']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Geheng Manyinget', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Geheng Manyinget']);
            }
        }

        // 57. Geni Agung
        if (
            ($saptawara === 1 && $pancawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 3 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Geni Agung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Geni Agung']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Geni Murub', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Geni Murub']);
            }
        }

        // 59. Geni Rawana
        if (
            (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11))) ||
            (($pengalantaka === 'Pangelong' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13)) || ($pengalantaka === 'Pangelong' && ($sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Geni Rawana', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Geni Rawana']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Geni Rawana Jejepan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Geni Rawana Jejepan']);
            }
        }

        // 61. Geni Rawana Rangkep
        if (
            (($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11 || $sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11)) || ($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13 || $sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Geni Rawana Rangkep', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Geni Rawana Rangkep']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Guntur Graha', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Guntur Graha']);
            }
        }

        // 63. Ingkel Macan
        if ($saptawara === 5 && $pancawara === 3 && $wuku === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ingkel Macan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ingkel Macan']);
            }
        }

        // 64. Istri Payasan
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Istri Payasan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Istri Payasan']);
            }
        }

        // 65. Jiwa Manganti
        if (($saptawara === 2 && $wuku === 19) || ($saptawara === 5 && ($wuku === 2 || $wuku === 20)) || ($saptawara === 6 && ($wuku === 25 || $wuku === 7)) || ($saptawara === 7 && $wuku === 30)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Jiwa Manganti', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Jiwa Manganti']);
            }
        }

        // 66. Kajeng Kipkipan
        if ($saptawara === 4 && ($wuku === 6 || $wuku === 30)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Kipkipan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Kipkipan']);
            }
        }

        // 67. Kajeng Kliwon Enyitan
        if ($triwara === 3 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 < 15 && $sasihDay1 > 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 < 15 && $sasihDay2 > 7))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Kliwon Enyitan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Kliwon Enyitan']);
            }
        }

        // 68. Kajeng Lulunan
        if ($triwara === 3 && $astawara === 5 && $sangawara === 9) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Lulunan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Lulunan']);
            }
        }

        // 69. Kajeng Rendetan
        if ($triwara === 3 && $pengalantaka === 'Penanggal' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Rendetan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Rendetan']);
            }
        }

        // 70. Kajeng Susunan
        if ($triwara === 3 && $astawara === 3 && $sangawara === 9) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Susunan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Susunan']);
            }
        }

        // 71. Kajeng Uwudan
        if ($triwara === 3 && $pengalantaka === 'Pangelong' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Uwudan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kajeng Uwudan']);
            }
        }

        // 72. Kala Alap
        if ($saptawara === 2 && $wuku === 22) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Alap', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Alap']);
            }
        }

        // 73. Kala Angin
        if ($saptawara === 1 && ($wuku === 17 || $wuku === 25 || $wuku === 28)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Angin', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Angin']);
            }
        }

        // 74. Kala Atat
        if (($saptawara === 1 && $wuku === 22) || ($saptawara === 3 && $wuku === 30) || ($saptawara === 4 && $wuku === 19)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Atat', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Atat']);
            }
        }

        // 75. Kala Awus
        if ($saptawara === 4 && $wuku === 28) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Awus', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Awus']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Bancaran', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Bancaran']);
            }
        }

        // 77. Kala Bangkung, Kala Nanggung
        if (
            $saptawara === 1 && $pancawara === 3 ||
            $saptawara === 2 && $pancawara === 2 ||
            $saptawara === 4 && $pancawara === 1 ||
            $saptawara === 7 && $pancawara === 4
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Bangkung, Kala Nanggung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Bangkung, Kala Nanggung']);
            }
        }

        // 78. Kala Beser
        if ($sadwara === 1 && $astawara === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Beser', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Beser']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Brahma', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Brahma']);
            }
        }

        // 80. Kala Bregala
        if ($saptawara === 2 && $wuku === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Bregala', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Bregala']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Buingrau', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Buingrau']);
            }
        }

        // 82. Kala Cakra
        if ($saptawara === 7 && $wuku === 23) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Cakra', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Cakra']);
            }
        }

        // 83. Kala Capika
        if ($saptawara === 1 && $wuku === 18 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Capika', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Capika']);
            }
        }

        // 84. Kala Caplokan
        if (($saptawara === 2 && ($wuku === 18 || $wuku === 9)) ||
            ($saptawara === 3 && $wuku === 19) ||
            ($saptawara === 4 && $wuku === 24) ||
            ($saptawara === 6 && $wuku === 12) ||
            ($saptawara === 7 && ($wuku === 9 || $wuku === 15 || $wuku === 1))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Caplokan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Caplokan']);
            }
        }

        // 85. Kala Cepitan
        if ($saptawara === 2 && $pancawara === 2 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Cepitan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Cepitan']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Dangastra', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Dangastra']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Dangu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Dangu']);
            }
        }

        // 88. Kala Demit
        if ($saptawara === 7 && $wuku === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Demit', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Demit']);
            }
        }

        // 89. Kala Empas Munggah
        if ($pancawara === 4 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Empas Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Empas Munggah']);
            }
        }

        // 90. Kala Empas Turun
        if ($pancawara === 4 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Empas Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Empas Turun']);
            }
        }

        // 91. Kala Gacokan
        if ($saptawara === 3 && $wuku === 19) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gacokan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gacokan']);
            }
        }

        // 92. Kala Garuda
        if ($saptawara === 3 && $wuku === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Garuda', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Garuda']);
            }
        }

        // 93. Kala Geger
        if (($saptawara === 5 || $saptawara === 7) && $wuku === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Geger', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Geger']);
            }
        }

        // 94. Kala Gotongan
        if (($saptawara === 6 && $pancawara === 5) ||
            ($saptawara === 7 && $pancawara === 1) ||
            ($saptawara === 1 && $pancawara === 2)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gotongan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gotongan']);
            }
        }

        // 95. Kala Graha
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 5)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Graha', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Graha']);
            }
        }

        // 96. Kala Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gumarang Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gumarang Munggah']);
            }
        }

        // 97. Kala Gumarang Turun
        if ($pancawara === 3 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gumarang Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Gumarang Turun']);
            }
        }

        // 98. Kala Guru
        if ($saptawara === 4 && $wuku === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Guru', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Guru']);
            }
        }

        // 99. Kala Ingsor
        if ($wuku === 4 || $wuku === 14 || $wuku === 24) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ingsor', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ingsor']);
            }
        }

        // 100. Kala Isinan
        if (($saptawara === 2 && ($wuku === 11 || $wuku === 17)) ||
            ($saptawara === 4 && $wuku === 30)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Isinan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Isinan']);
            }
        }

        // 101. Kala Jangkut
        if ($triwara === 3 && $dwiwara === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Jangkut', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Jangkut']);
            }
        }

        // 102. Kala Jengkang
        if ($saptawara === 1 && $pancawara === 1 && $wuku === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Jengkang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Jengkang']);
            }
        }

        // 103. Kala Jengking
        if ($sadwara === 3 && $astawara === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Jengking', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Jengking']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Katemu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Katemu']);
            }
        }

        // 105. Kala Keciran
        if ($saptawara === 4 && $wuku === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Keciran', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Keciran']);
            }
        }

        // 106. Kala Kilang-Kilung
        if (($saptawara === 2 && $wuku === 17) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kilang-Kilung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kilang-Kilung']);
            }
        }

        // 107. Kala Kingkingan
        if ($saptawara === 5 && $wuku === 17) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kingkingan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kingkingan']);
            }
        }

        // 108. Kala Klingkung
        if ($saptawara === 3 && $wuku === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Klingkung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Klingkung']);
            }
        }

        // 109. Kala Kutila Manik
        if ($triwara === 3 && $pancawara === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kutila Manik', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kutila Manik']);
            }
        }

        // 110. Kala Kutila
        if ($sadwara === 2 && $astawara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kutila', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Kutila']);
            }
        }

        // 111. Kala Luang
        if (($saptawara === 1 && ($wuku === 11 || $wuku === 12 || $wuku === 13)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 10 || $wuku === 8 || $wuku === 19 || $wuku === 23 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 18)) ||
            ($saptawara === 5 && ($wuku === 28 || $wuku === 29))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Luang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Luang']);
            }
        }

        // 112. Kala Lutung Megelut
        if (($saptawara === 1 && $wuku === 3) || ($saptawara === 4 && $wuku === 10)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Lutung Megelut', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Lutung Megelut']);
            }
        }

        // 113. Kala Lutung Megandong
        if ($saptawara === 5 && $pancawara === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Lutung Megandong', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Lutung Megandong']);
            }
        }

        // 114. Kala Macan
        if ($saptawara === 5 && $wuku === 19) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Macan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Macan']);
            }
        }

        // 115. Kala Mangap
        if ($saptawara === 1 && $pancawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mangap', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mangap']);
            }
        }

        // 116. Kala Manguneb
        if ($saptawara === 5 && $pancawara === 14) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Manguneb', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Manguneb']);
            }
        }

        // 117. Kala Matampak
        if (($saptawara === 4 && $wuku === 3) ||
            ($saptawara === 5 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 3) ||
            ($saptawara === 7 && ($wuku === 7 || $wuku === 24))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Matampak', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Matampak']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mereng', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mereng']);
            }
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Miled', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Miled']);
            }
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mina', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mina']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mretyu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mretyu']);
            }
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muas', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muas']);
            }
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncar', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncar']);
            }
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncrat', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncrat']);
            }
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngadeg', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngadeg']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mereng', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mereng']);
            }
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Miled', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Miled']);
            }
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mina', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mina']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mretyu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Mretyu']);
            }
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muas', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muas']);
            }
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncar', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncar']);
            }
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncrat', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Muncrat']);
            }
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngadeg', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngadeg']);
            }
        }

        // 126. Kala Ngamut
        if ($saptawara === 2 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngamut', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngamut']);
            }
        }

        // 127. Kala Ngruda
        if (($saptawara === 1 && ($wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 23 || $wuku === 10)) ||
            ($saptawara === 7 && ($wuku === 10))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngruda', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngruda']);
            }
        }

        // 128. Kala Ngunya
        if ($saptawara === 1 && $wuku === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngunya', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Ngunya']);
            }
        }

        // 129. Kala Olih
        if ($saptawara === 4 && $wuku === 24) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Olih', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Olih']);
            }
        }

        // 130. Kala Pacekan
        if ($saptawara === 3 && $wuku === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pacekan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pacekan']);
            }
        }

        // 131. Kala Pager
        if ($saptawara === 5 && $wuku === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pager', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pager']);
            }
        }

        // 132. Kala Panyeneng
        if (($saptawara === 1 && $wuku === 7) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Panyeneng', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Panyeneng']);
            }
        }

        // 133. Kala Pati
        if (($saptawara === 1 && ($wuku === 10 || $wuku === 2)) ||
            ($saptawara === 3 && ($wuku === 6 || $wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 10 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 17))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pati', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pati']);
            }
        }

        // 134. Kala Pati Jengkang
        if ($saptawara === 5 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pati Jengkang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pati Jengkang']);
            }
        }

        // 135. Kala Pegat
        if (
            $saptawara === 4 && $wuku === 12 ||
            $saptawara === 7 && ($wuku === 3 || $wuku === 18)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pegat', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Pegat']);
            }
        }

        // 136. Kala Prawani
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 3 && $wuku === 24) ||
            ($saptawara === 4 && $wuku === 2) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Prawani', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Prawani']);
            }
        }

        // 137. Kala Raja
        if ($saptawara === 5 && $wuku === 29) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Raja', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Raja']);
            }
        }

        // 138. Kala Rau
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 7 && ($wuku === 3 || $wuku === 4 || $wuku === 18)) ||
            ($saptawara === 6 && $wuku === 6)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Rau', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Rau']);
            }
        }

        // 139. Kala Rebutan
        if ($saptawara === 2 && $wuku === 26) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Rebutan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Rebutan']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Rumpuh', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Rumpuh']);
            }
        }

        // 141. Kala Sapuhau
        if (($saptawara === 2 && $wuku === 3) ||
            ($saptawara === 3 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sapuhau', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sapuhau']);
            }
        }

        // 142. Kala Sarang
        if ($wuku === 7 || $wuku === 17) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sarang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sarang']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Siyung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Siyung']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sor', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sor']);
            }
        }

        // 145. Kala Sudangastra
        if (($saptawara === 1 && $wuku === 24) ||
            ($saptawara === 3 && $wuku === 28) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 12)) ||
            ($saptawara === 5 && $wuku === 19) ||
            ($saptawara === 7 && $wuku === 6)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sudangastra', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sudangastra']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sudukan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sudukan']);
            }
        }

        // 147. Kala Sungsang
        if ($wuku === 27) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sungsang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Sungsang']);
            }
        }

        // 148. Kala Susulan
        if ($saptawara === 2 && $wuku === 11) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Susulan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Susulan']);
            }
        }

        // 149. Kala Suwung
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 3 && ($wuku === 8 || $wuku === 10)) ||
            ($saptawara === 4 && ($wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 11 || $wuku === 13 || $wuku === 14))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Suwung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Suwung']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tampak', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tampak']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Temah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Temah']);
            }
        }

        // 152. Kala Timpang
        if (($saptawara === 3 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 14) ||
            ($saptawara === 7 && $wuku === 2)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Timpang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Timpang']);
            }
        }

        // 153. Kala Tukaran
        if ($saptawara === 3 && ($wuku === 3 || $wuku === 8)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tukaran', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tukaran']);
            }
        }

        // 154. Kala Tumapel
        if ($wuku === 12 && ($saptawara === 3 || $saptawara === 4)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tumapel', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tumapel']);
            }
        }

        // 155. Kala Tumpar
        if (($saptawara === 3 && $wuku === 13) || ($saptawara === 4 && $wuku === 8)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tumpar', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Tumpar']);
            }
        }

        // 156. Kala Upa
        if ($sadwara === 4 && $triwara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Upa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Upa']);
            }
        }

        // 157. Kala Was
        if ($saptawara === 2 && $wuku === 17) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Was', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Was']);
            }
        }

        // 158. Kala Wikalpa
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 25)) || ($saptawara === 6 && ($wuku === 27 || $wuku === 30))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Wikalpa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Wikalpa']);
            }
        }

        // 159. Kala Wisesa
        if ($sadwara === 5 && $astawara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Wisesa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Wisesa']);
            }
        }

        // 160. Kala Wong
        if ($saptawara === 4 && $wuku === 20) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kala Wong', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kala Wong']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kaleburau', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kaleburau']);
            }
        }

        // 162. Kamajaya
        if ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3 || $sasihDay2 === 7)))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Kamajaya', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Kamajaya']);
            }
        }

        // 163. Karna Sula
        if (
            ($saptawara === 1 && ($sasihDay1 === 2 || $sasihDay2 === 2)) ||
            ($saptawara === 3 && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem'))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Karna Sula', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Karna Sula']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Karnasula', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Karnasula']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Lebur Awu', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Lebur Awu']);
            }
        }

        // 166. Lutung Magandong
        if ($saptawara === 5 && ($wuku === 3 || $wuku === 8 || $wuku === 13 || $wuku === 18 || $wuku === 23 || $wuku === 28)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Lutung Magandong', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Lutung Magandong']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Macekan Agung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Macekan Agung']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Macekan Lanang', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Macekan Lanang']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Macekan Wadon', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Macekan Wadon']);
            }
        }

        // 170. Merta Sula
        if ($saptawara === 5 && ($sasihDay1 === 7 || $sasihDay2 === 7)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Merta Sula', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Merta Sula']);
            }
        }

        // 171. Naga Naut
        if ($sasihDay1 === 'no_sasih' || $sasihDay2 === 'no_sasih') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Naga Naut', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Naga Naut']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Pamacekan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Pamacekan']);
            }
        }

        // 173. Panca Amerta
        if ($saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Panca Amerta', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Panca Amerta']);
            }
        }

        // 174. Panca Prawani
        if ($sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 12 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 12) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Panca Prawani', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Panca Prawani']);
            }
        }

        // 175. Panca Wedhi
        if ($saptawara === 2 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Panca Werdhi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Panca Werdhi']);
            }
        }

        // 176. Pati Paten
        if ($saptawara === 6 && (($sasihDay1 === 10 || $sasihDay2 === 10) || $purnama_tilem === 'Tilem')) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Pati Paten', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Pati Paten']);
            }
        }

        // 177. Patra Limutan
        if ($triwara === 3 && $purnama_tilem === 'Tilem') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Patra Limutan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Patra Limutan']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Pepedan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Pepedan']);
            }
        }

        // 179. Prabu Pendah
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Prabu Pendah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Prabu Pendah']);
            }
        }

        // 180. Prangewa
        if ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Prangewa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Prangewa']);
            }
        }

        // 181. Purnama Danta
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Purnama Danta', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Purnama Danta']);
            }
        }

        // 182. Purna Suka
        if ($saptawara === 6 && $pancawara === 1 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Purna Suka', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Purna Suka']);
            }
        }

        // 183. Purwani
        if ($sasihDay1 === 14 || $sasihDay2 === 14) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Purwani', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Purwani']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Purwanin Dina', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Purwanin Dina']);
            }
        }

        // 185. Rangda Tiga
        if ($wuku === 7 || $wuku === 8 || $wuku === 15 || $wuku === 16 || $wuku === 23 || $wuku === 24) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Rangda Tiga', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Rangda Tiga']);
            }
        }

        // 186. Rarung Pagelangan
        if ($saptawara === 5 && ($sasihDay1 === 6 || $sasihDay2 === 6)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Rarung Pagelangan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Rarung Pagelangan']);
            }
        }

        // 187. Ratu Magelung
        if ($saptawara === 4 && $wuku === 23) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Magelung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Magelung']);
            }
        }

        // 188. Ratu Mangure
        if ($saptawara === 5 && $wuku === 20) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Mangure', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Mangure']);
            }
        }

        // 189. Ratu Megambahan
        if ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 6))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Megambahan', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Megambahan']);
            }
        }

        // 190. Ratu Nanyingal
        if ($saptawara === 5 && $wuku === 21) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Nanyingal', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Nanyingal']);
            }
        }

        // 191. Ratu Ngemban Putra
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Ngemban Putra', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Ratu Ngemban Putra']);
            }
        }

        // 192. Rekatadala Ayudana
        if ($saptawara === 1 && ($sasihDay1 === 1 || $sasihDay1 === 6 || $sasihDay1 === 11 || $sasihDay1 === 2 || $sasihDay2 === 6 || $sasihDay2 === 11)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Rekatadala Ayudana', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Rekatadala Ayudana']);
            }
        }

        // 193. Salah Wadi
        if ($wuku === 1 || $wuku === 2 || $wuku === 6 || $wuku === 10 || $wuku === 11 || $wuku === 16 || $wuku === 19 || $wuku === 20 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 30) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Salah Wadi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Salah Wadi']);
            }
            // dd($description);
        }

        // 194. Sampar Wangke
        if ($saptawara === 2 && $sadwara === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sampar Wangke', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sampar Wangke']);
            }
        }

        // 195. Sampi Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 4) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sampi Gumarang Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sampi Gumarang Munggah']);
            }
        }

        // 196. Sampi Gumarang Turun
        if ($pancawara === 3 && $sadwara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sampi Gumarang Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sampi Gumarang Turun']);
            }
        }

        // 197. Sarik Agung
        if ($saptawara === 4 && ($wuku === 25 || $wuku === 4 || $wuku === 11 || $wuku === 18)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sarik Agung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sarik Agung']);
            }
        }

        // 198. Sarik Ketah
        if (($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sarik Ketah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sarik Ketah']);
            }
        }

        // 199. Sedana Tiba
        if ($saptawara === 5 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sedana Tiba', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sedana Tiba']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sedana Yoga', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sedana Yoga']);
            }
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Semut Sadulur', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Semut Sadulur']);
            }
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Siwa Sampurna', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Siwa Sampurna']);
            }
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sri Bagia', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sri Bagia']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sedana Yoga', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sedana Yoga']);
            }
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Semut Sadulur', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Semut Sadulur']);
            }
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Siwa Sampurna', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Siwa Sampurna']);
            }
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sri Bagia', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sri Bagia']);
            }
        }

        // 204. Sri Murti
        if ($sadwara === 5 && $astawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sri Murti', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sri Murti']);
            }
        }

        // 205. Sri Tumpuk
        if ($astawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Sri Tumpuk', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Sri Tumpuk']);
            }
        }

        // 206. Srigati
        if (($triwara === 3 && $pancawara === 1 && $sadwara === 3) ||
            ($triwara === 3 && $pancawara === 1 && $sadwara === 6)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Srigati', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Srigati']);
            }
        }

        // 207. Srigati Jenek
        if ($pancawara === 5 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Srigati Jenek', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Srigati Jenek']);
            }
        }

        // 208. Srigati Munggah
        if ($pancawara === 1 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Srigati Munggah', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Srigati Munggah']);
            }
        }

        // 209. Srigati Turun
        if ($pancawara === 1 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Srigati Turun', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Srigati Turun']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Subhacara', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Subhacara']);
            }
        }

        // 211. Swarga Menga
        if (($saptawara === 3 && $pancawara === 3 && $wuku === 3 &&
                (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 5 && $pancawara === 2 && $wuku === 4)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Swarga Menga', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Swarga Menga']);
            }
        }

        // 212. Taliwangke
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 23 || $wuku === 24 || $wuku === 25 || $wuku === 26)) ||
            ($saptawara === 3 && ($wuku === 1 || $wuku === 27 || $wuku === 28 || $wuku === 29 || $wuku === 30)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 3 || $wuku === 4 || $wuku === 6)) ||
            ($saptawara === 5 && ($wuku === 7 || $wuku === 8 || $wuku === 9 || $wuku === 10 || $wuku === 11 || $wuku === 17 || $wuku === 18 || $wuku === 20 || $wuku === 21)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 13 || $wuku === 14 || $wuku === 15 || $wuku === 16))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Taliwangke', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Taliwangke']);
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
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Titibuwuk', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Titibuwuk']);
            }
        }

        // 214. Tunut Masih
        if (($saptawara === 1 && $wuku === 18) ||
            ($saptawara === 2 && ($wuku === 12 || $wuku === 13 || $wuku === 27)) ||
            ($saptawara === 3 && ($wuku === 17 || $wuku === 24)) ||
            ($saptawara === 5 && $wuku === 1) ||
            ($saptawara === 6 && ($wuku === 19 || $wuku === 22))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Tunut Masih', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Tunut Masih']);
            }
        }

        // 215. Tutur Mandi
        if (($saptawara === 1 && $wuku === 26) ||
            ($saptawara === 5 && ($wuku === 3 || $wuku === 9 || $wuku === 15 || $wuku === 20 || $wuku === 21 || $wuku === 24)) ||
            ($saptawara === 6 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 24)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Tutur Mandi', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Tutur Mandi']);
            }
        }

        // 216. Uncal Balung
        if ($wuku === 12 || $wuku === 13 || (($wuku === 14 && $saptawara === 1) || ($wuku === 16 && $saptawara < 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Uncal Balung', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Uncal Balung']);
            }
        }

        // 217. Upadana Merta
        if (
            $saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 8 || $sasihDay1 === 6 || $sasihDay1 === 10)) ||
                ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 8 || $sasihDay2 === 6 || $sasihDay2 === 10)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Upadana Merta', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Upadana Merta']);
            }
        }

        // 218. Werdi Suka
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)) &&
            ($no_sasih === 1)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Werdi Suka', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Werdi Suka']);
            }
        }

        // 219. Wisesa
        if (
            $saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 13) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 13)) &&
            ($no_sasih === 1)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Wisesa', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Wisesa']);
            }
        }

        // 220. Wredhi Guna
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)) &&
            ($no_sasih === 1)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('ala_ayuning_dewasa', end($alaAyuningDewasa))->pluck('description')->first();
                array_push($alaAyuningDewasa, ['nama' => 'Wredhi Guna', 'keterangan' => $description]);
            } else {
                array_push($alaAyuningDewasa, ['nama' => 'Wredhi Guna']);
            }
        }

        // Remove leading comma and space
        // $alaAyuningDewasa = ltrim($alaAyuningDewasa, ', ');

        // if ($makna) {
        //     return response()->json([
        //         'alaAyuningDewasa' => $alaAyuningDewasa,
        //         'keterangan' => $description,
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'alaAyuningDewasa' => $alaAyuningDewasa,
        //     ], 200);
        // }
        return $alaAyuningDewasa;
    }
}
