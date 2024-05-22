<?php

namespace App\Http\Controllers\API;

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
use App\Models\DewasaAyu;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Http\Request;

class DewasaAyuAPI extends Controller
{
    public function cariDewasaAyu(Request $request)
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
                'kalender' => $this->getAlaAyuningDewasa($tanggal_mulai->toDateString(), $makna),
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
        $wuku = $wukuController->getWuku($hasilAngkaWuku);

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
        $dewasaAyu = [];
        // $description = [];

        // 1. AgniAgungDoyanBasmi: Selasa Purnama dengan Asta Wara Brahma
        if (($saptawara === 3 && ($astawara === 6 || $purnama_tilem === 'Purnama'))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Agni Agung Doyan Basmi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Agni Agung Doyan Basmi',]);
            }
            // $description[] = $isi_description[0]->first();
        }

        // 2. Agni Agung Patra Limutan: Minggu dengan Asta Wara Brahma
        if ($saptawara === 1 && $astawara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Agni Agung Patra Limutan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Agni Agung Patra Limutan']);
            }
        }

        // 3. Amerta Akasa: Anggara Purnama
        if ($saptawara === 3 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Akasa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Akasa']);
            }
        }

        // 4. Amerta Bumi: Soma Wage Penanggal 1. Buda Pon Penanggal 10.
        if (($saptawara === 2 && $pancawara === 4 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($saptawara === 4 && $pancawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 10 || $sasihDay2 === 10))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Bumi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Bumi']);
            }
        }

        // 5. Amerta Bhuwana: Redite Purnama, Soma Purnama, dan Anggara Purnama
        if (($saptawara === 1 || $saptawara === 2 || $saptawara === 3) && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Bhuwana', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Bhuwana']);
            }
        }

        // 6. Amerta Dadi: Soma Beteng atau Purnama Kajeng
        if (($saptawara === 2 && $triwara === 2) || ($triwara === 3 && $purnama_tilem === 'Purnama')) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dadi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dadi']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Danta', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Danta']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dewa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dewa']);
            }
        }

        // 9. Amerta Dewa Jaya
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) || ($pengalantaka === 'Penanggal' && ($sasihDay1 === 12 || $sasihDay2 === 12)))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dewa Jaya', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dewa Jaya']);
            }
        }

        // 10. Amerta Dewata
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dewata', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Dewata']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Gati', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Gati']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Kundalini', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Kundalini']);
            }
        }

        // 13. Amerta Masa
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Masa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Masa']);
            }
        }

        // 14. Amerta Murti
        if ($saptawara === 4 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 12) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 12))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Murti', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Murti']);
            }
        }

        // 15. Amerta Pageh
        if ($saptawara === 7 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Pageh', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Pageh']);
            }
        }

        // 16. Amerta Pepageran
        if ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $astawara === 4)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Pepageran', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Pepageran']);
            }
        }

        // 17. Amerta Sari
        if ($saptawara === 4 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Sari', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Sari']);
            }
        }

        // 18. Amerta Wija
        if ($saptawara === 5 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Wija', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Wija']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Yoga', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Amerta Yoga']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Asuajag Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Asuajag Munggah']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Asuajag Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Asuajag Turun']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Asuasa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Asuasa']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ayu Bhadra', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ayu Bhadra']);
            }
        }

        // 24. Ayu Dana
        if ($saptawara === 6 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ayu Dana', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ayu Dana']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ayu Nulus', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ayu Nulus']);
            }
        }

        // 26. Babi Munggah
        if ($pancawara === 4 && $sadwara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Babi Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Babi Munggah']);
            }
        }

        // 27. Babi Turun
        if ($pancawara === 4 && $sadwara === 4) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Babi Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Babi Turun']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Banyu Milir', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Banyu Milir']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Banyu Urug', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Banyu Urug']);
            }
        }

        // 30. Bojog Munggah
        if ($pancawara === 5 && $sadwara === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Bojog Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Bojog Munggah']);
            }
        }

        // 31. Bojog Turun
        if ($pancawara === 5 && $sadwara === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Bojog Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Bojog Turun']);
            }
        }

        // 32. Buda Gajah
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Buda Gajah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Buda Gajah']);
            }
        }

        // 33. Buda Ireng
        if ($saptawara === 4 && $pancawara === 4 && $purnama_tilem === 'Tilem') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Buda Ireng', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Buda Ireng']);
            }
        }

        // 34. Buda Suka
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Tilem') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Buda Suka', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Buda Suka']);
            }
        }

        // 35. Carik Walangati
        if (
            $wuku === 1 || $wuku === 6 || $wuku === 10 || $wuku === 12 || $wuku === 24 ||
            $wuku === 25 || $wuku === 27 || $wuku === 28 || $wuku === 30 || $wuku === 7
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Carik Walangati', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Carik Walangati']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Catur Laba', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Catur Laba']);
            }
        }

        // 37. Cintamanik
        if ($saptawara === 4 && ($wuku % 2 === 1)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Cintamanik', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Cintamanik']);
            }
        }

        // 38. Corok Kodong
        if ($saptawara === 5 && $pancawara === 5 && $wuku === 13) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Corok Kodong', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Corok Kodong']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'DagDig Karana', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'DagDig Karana']);
            }
        }

        // 40. Dasa Amertha
        if ($saptawara === 6 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dasa Amertha', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dasa Amertha']);
            }
        }

        // 41. Dasa Guna
        if ($saptawara === 4 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem')) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dasa Guna', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dasa Guna']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dauh Ayu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dauh Ayu']);
            }
        }

        // 43. Derman Bagia
        if ($saptawara === 2 && ($sasihDay1 === 2 || $sasihDay2 === 2 || $sasihDay1 === 3 || $sasihDay2 === 3 || $sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 12 || $sasihDay2 === 12)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Derman Bagia', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Derman Bagia']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Ngelayang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Ngelayang']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Satata', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Satata']);
            }
        }

        // 46. Dewa Werdhi
        if ($saptawara === 6 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Werdhi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Werdhi']);
            }
        }

        // 47. Dewa Mentas
        if ($saptawara === 5 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 5 || $sasihDay2 === 5 || $sasihDay1 === 15 || $sasihDay2 === 15)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Mentas', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewa Mentas']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewasa Ngelayang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewasa Ngelayang']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewasa Tanian', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dewasa Tanian']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dina Carik', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dina Carik']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dina Jaya', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dina Jaya']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dina Mandi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dina Mandi']);
            }
        }

        // 53. Dirgahayu
        if ($saptawara === 3 && $pancawara === 3 && $dasawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dirgahayu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dirgahayu']);
            }
        }

        // 54. DirghaYusa
        if ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dirgha Yusa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Dirgha Yusa']);
            }
        }

        // 55. Gagak Anungsung Pati
        if (
            ($pengalantaka === 'Penanggal' && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($pengalantaka === 'Pangelong' && ($sasihDay1 === 1 || $sasihDay2 === 1)) ||
            ($pengalantaka === 'Pangelong' && ($sasihDay1 === 6 || $sasihDay2 === 6)) ||
            ($pengalantaka === 'Pangelong' && ($sasihDay1 === 14 || $sasihDay2 === 14))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Gagak Anungsung Pati', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Gagak Anungsung Pati']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geheng Manyinget', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geheng Manyinget']);
            }
        }

        // 57. Geni Agung
        if (
            ($saptawara === 1 && $pancawara === 1 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10))) ||
            ($saptawara === 3 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) ||
            ($saptawara === 4 && $pancawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Agung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Agung']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Murub', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Murub']);
            }
        }

        // 59. Geni Rawana
        if (
            (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11))) ||
            (($pengalantaka === 'Pangelong' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13)) || ($pengalantaka === 'Pangelong' && ($sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Rawana', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Rawana']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Rawana Jejepan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Rawana Jejepan']);
            }
        }

        // 61. Geni Rawana Rangkep
        if (
            (($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 11 || $sasihDay2 === 2 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 11)) || ($saptawara === 3 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay1 === 4 || $sasihDay1 === 9 || $sasihDay1 === 13 || $sasihDay2 === 3 || $sasihDay2 === 4 || $sasihDay2 === 9 || $sasihDay2 === 13)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Rawana Rangkep', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Geni Rawana Rangkep']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Guntur Graha', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Guntur Graha']);
            }
        }

        // 63. Ingkel Macan
        if ($saptawara === 5 && $pancawara === 3 && $wuku === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ingkel Macan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ingkel Macan']);
            }
        }

        // 64. Istri Payasan
        if ($saptawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 8) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 8))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Istri Payasan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Istri Payasan']);
            }
        }

        // 65. Jiwa Manganti
        if (($saptawara === 2 && $wuku === 19) || ($saptawara === 5 && ($wuku === 2 || $wuku === 20)) || ($saptawara === 6 && ($wuku === 25 || $wuku === 7)) || ($saptawara === 7 && $wuku === 30)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Jiwa Manganti', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Jiwa Manganti']);
            }
        }

        // 66. Kajeng Kipkipan
        if ($saptawara === 4 && ($wuku === 6 || $wuku === 30)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Kipkipan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Kipkipan']);
            }
        }

        // 67. Kajeng Kliwon Enyitan
        if ($triwara === 3 && $pancawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 < 15 && $sasihDay1 > 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 < 15 && $sasihDay2 > 7))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Kliwon Enyitan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Kliwon Enyitan']);
            }
        }

        // 68. Kajeng Lulunan
        if ($triwara === 3 && $astawara === 5 && $sangawara === 9) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Lulunan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Lulunan']);
            }
        }

        // 69. Kajeng Rendetan
        if ($triwara === 3 && $pengalantaka === 'Penanggal' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Rendetan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Rendetan']);
            }
        }

        // 70. Kajeng Susunan
        if ($triwara === 3 && $astawara === 3 && $sangawara === 9) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Susunan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Susunan']);
            }
        }

        // 71. Kajeng Uwudan
        if ($triwara === 3 && $pengalantaka === 'Pangelong' && ($saptawara === 1 || $saptawara === 4 || $saptawara === 7)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Uwudan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kajeng Uwudan']);
            }
        }

        // 72. Kala Alap
        if ($saptawara === 2 && $wuku === 22) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Alap', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Alap']);
            }
        }

        // 73. Kala Angin
        if ($saptawara === 1 && ($wuku === 17 || $wuku === 25 || $wuku === 28)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Angin', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Angin']);
            }
        }

        // 74. Kala Atat
        if (($saptawara === 1 && $wuku === 22) || ($saptawara === 3 && $wuku === 30) || ($saptawara === 4 && $wuku === 19)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Atat', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Atat']);
            }
        }

        // 75. Kala Awus
        if ($saptawara === 4 && $wuku === 28) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Awus', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Awus']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Bancaran', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Bancaran']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Bangkung, Kala Nanggung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Bangkung, Kala Nanggung']);
            }
        }

        // 78. Kala Beser
        if ($sadwara === 1 && $astawara === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Beser', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Beser']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Brahma', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Brahma']);
            }
        }

        // 80. Kala Bregala
        if ($saptawara === 2 && $wuku === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Bregala', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Bregala']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Buingrau', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Buingrau']);
            }
        }

        // 82. Kala Cakra
        if ($saptawara === 7 && $wuku === 23) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Cakra', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Cakra']);
            }
        }

        // 83. Kala Capika
        if ($saptawara === 1 && $wuku === 18 && $pengalantaka === 'Penanggal' && ($sasihDay1 === 3 || $sasihDay2 === 3)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Capika', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Capika']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Caplokan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Caplokan']);
            }
        }

        // 85. Kala Cepitan
        if ($saptawara === 2 && $pancawara === 2 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Cepitan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Cepitan']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Dangastra', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Dangastra']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Dangu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Dangu']);
            }
        }

        // 88. Kala Demit
        if ($saptawara === 7 && $wuku === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Demit', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Demit']);
            }
        }

        // 89. Kala Empas Munggah
        if ($pancawara === 4 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Empas Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Empas Munggah']);
            }
        }

        // 90. Kala Empas Turun
        if ($pancawara === 4 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Empas Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Empas Turun']);
            }
        }

        // 91. Kala Gacokan
        if ($saptawara === 3 && $wuku === 19) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gacokan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gacokan']);
            }
        }

        // 92. Kala Garuda
        if ($saptawara === 3 && $wuku === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Garuda', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Garuda']);
            }
        }

        // 93. Kala Geger
        if (($saptawara === 5 || $saptawara === 7) && $wuku === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Geger', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Geger']);
            }
        }

        // 94. Kala Gotongan
        if (($saptawara === 6 && $pancawara === 5) ||
            ($saptawara === 7 && $pancawara === 1) ||
            ($saptawara === 1 && $pancawara === 2)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gotongan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gotongan']);
            }
        }

        // 95. Kala Graha
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 5)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Graha', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Graha']);
            }
        }

        // 96. Kala Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gumarang Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gumarang Munggah']);
            }
        }

        // 97. Kala Gumarang Turun
        if ($pancawara === 3 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gumarang Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Gumarang Turun']);
            }
        }

        // 98. Kala Guru
        if ($saptawara === 4 && $wuku === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Guru', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Guru']);
            }
        }

        // 99. Kala Ingsor
        if ($wuku === 4 || $wuku === 14 || $wuku === 24) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ingsor', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ingsor']);
            }
        }

        // 100. Kala Isinan
        if (($saptawara === 2 && ($wuku === 11 || $wuku === 17)) ||
            ($saptawara === 4 && $wuku === 30)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Isinan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Isinan']);
            }
        }

        // 101. Kala Jangkut
        if ($triwara === 3 && $dwiwara === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Jangkut', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Jangkut']);
            }
        }

        // 102. Kala Jengkang
        if ($saptawara === 1 && $pancawara === 1 && $wuku === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Jengkang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Jengkang']);
            }
        }

        // 103. Kala Jengking
        if ($sadwara === 3 && $astawara === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Jengking', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Jengking']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Katemu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Katemu']);
            }
        }

        // 105. Kala Keciran
        if ($saptawara === 4 && $wuku === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Keciran', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Keciran']);
            }
        }

        // 106. Kala Kilang-Kilung
        if (($saptawara === 2 && $wuku === 17) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kilang-Kilung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kilang-Kilung']);
            }
        }

        // 107. Kala Kingkingan
        if ($saptawara === 5 && $wuku === 17) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kingkingan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kingkingan']);
            }
        }

        // 108. Kala Klingkung
        if ($saptawara === 3 && $wuku === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Klingkung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Klingkung']);
            }
        }

        // 109. Kala Kutila Manik
        if ($triwara === 3 && $pancawara === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kutila Manik', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kutila Manik']);
            }
        }

        // 110. Kala Kutila
        if ($sadwara === 2 && $astawara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kutila', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Kutila']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Luang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Luang']);
            }
        }

        // 112. Kala Lutung Megelut
        if (($saptawara === 1 && $wuku === 3) || ($saptawara === 4 && $wuku === 10)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Lutung Megelut', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Lutung Megelut']);
            }
        }

        // 113. Kala Lutung Megandong
        if ($saptawara === 5 && $pancawara === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Lutung Megandong', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Lutung Megandong']);
            }
        }

        // 114. Kala Macan
        if ($saptawara === 5 && $wuku === 19) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Macan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Macan']);
            }
        }

        // 115. Kala Mangap
        if ($saptawara === 1 && $pancawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mangap', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mangap']);
            }
        }

        // 116. Kala Manguneb
        if ($saptawara === 5 && $pancawara === 14) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Manguneb', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Manguneb']);
            }
        }

        // 117. Kala Matampak
        if (($saptawara === 4 && $wuku === 3) ||
            ($saptawara === 5 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 3) ||
            ($saptawara === 7 && ($wuku === 7 || $wuku === 24))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Matampak', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Matampak']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mereng', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mereng']);
            }
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Miled', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Miled']);
            }
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mina', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mina']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mretyu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mretyu']);
            }
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muas', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muas']);
            }
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncar', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncar']);
            }
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncrat', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncrat']);
            }
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngadeg', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngadeg']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mereng', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mereng']);
            }
        }

        // 119. Kala Miled
        if ($saptawara === 2 && $pancawara === 16) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Miled', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Miled']);
            }
        }

        // 120. Kala Mina
        if ($saptawara === 6 && ($wuku === 8 || $wuku === 14)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mina', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mina']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mretyu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Mretyu']);
            }
        }

        // 122. Kala Muas
        if (($saptawara === 1 && ($wuku === 4)) ||
            ($saptawara === 2 && ($wuku === 27)) ||
            ($saptawara === 7 && ($wuku === 16))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muas', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muas']);
            }
        }

        // 123. Kala Muncar
        if ($saptawara === 4 && ($wuku === 11)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncar', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncar']);
            }
        }

        // 124. Kala Muncrat
        if ($saptawara === 2 && $pancawara === 3 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncrat', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Muncrat']);
            }
        }

        // 125. Kala Ngadeg
        if (($saptawara === 1 && ($wuku === 15 || $wuku === 17)) ||
            ($saptawara === 2 && ($wuku === 19 || $wuku === 28)) ||
            ($saptawara === 6 && ($wuku === 12 || $wuku === 30))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngadeg', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngadeg']);
            }
        }

        // 126. Kala Ngamut
        if ($saptawara === 2 && $wuku === 18) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngamut', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngamut']);
            }
        }

        // 127. Kala Ngruda
        if (($saptawara === 1 && ($wuku === 29)) ||
            ($saptawara === 2 && ($wuku === 23 || $wuku === 10)) ||
            ($saptawara === 7 && ($wuku === 10))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngruda', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngruda']);
            }
        }

        // 128. Kala Ngunya
        if ($saptawara === 1 && $wuku === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngunya', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Ngunya']);
            }
        }

        // 129. Kala Olih
        if ($saptawara === 4 && $wuku === 24) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Olih', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Olih']);
            }
        }

        // 130. Kala Pacekan
        if ($saptawara === 3 && $wuku === 5) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pacekan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pacekan']);
            }
        }

        // 131. Kala Pager
        if ($saptawara === 5 && $wuku === 7) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pager', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pager']);
            }
        }

        // 132. Kala Panyeneng
        if (($saptawara === 1 && $wuku === 7) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Panyeneng', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Panyeneng']);
            }
        }

        // 133. Kala Pati
        if (($saptawara === 1 && ($wuku === 10 || $wuku === 2)) ||
            ($saptawara === 3 && ($wuku === 6 || $wuku === 14 || $wuku === 27)) ||
            ($saptawara === 4 && ($wuku === 2 || $wuku === 10 || $wuku === 26)) ||
            ($saptawara === 7 && ($wuku === 17))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pati', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pati']);
            }
        }

        // 134. Kala Pati Jengkang
        if ($saptawara === 5 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pati Jengkang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pati Jengkang']);
            }
        }

        // 135. Kala Pegat
        if (
            $saptawara === 4 && $wuku === 12 ||
            $saptawara === 7 && ($wuku === 3 || $wuku === 18)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pegat', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Pegat']);
            }
        }

        // 136. Kala Prawani
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 3 && $wuku === 24) ||
            ($saptawara === 4 && $wuku === 2) ||
            ($saptawara === 5 && $wuku === 19)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Prawani', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Prawani']);
            }
        }

        // 137. Kala Raja
        if ($saptawara === 5 && $wuku === 29) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Raja', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Raja']);
            }
        }

        // 138. Kala Rau
        if (($saptawara === 1 && $wuku === 1) ||
            ($saptawara === 7 && ($wuku === 3 || $wuku === 4 || $wuku === 18)) ||
            ($saptawara === 6 && $wuku === 6)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Rau', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Rau']);
            }
        }

        // 139. Kala Rebutan
        if ($saptawara === 2 && $wuku === 26) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Rebutan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Rebutan']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Rumpuh', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Rumpuh']);
            }
        }

        // 141. Kala Sapuhau
        if (($saptawara === 2 && $wuku === 3) ||
            ($saptawara === 3 && $wuku === 27) ||
            ($saptawara === 4 && $wuku === 28) ||
            ($saptawara === 6 && $wuku === 30)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sapuhau', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sapuhau']);
            }
        }

        // 142. Kala Sarang
        if ($wuku === 7 || $wuku === 17) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sarang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sarang']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Siyung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Siyung']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sor', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sor']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sudangastra', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sudangastra']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sudukan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sudukan']);
            }
        }

        // 147. Kala Sungsang
        if ($wuku === 27) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sungsang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Sungsang']);
            }
        }

        // 148. Kala Susulan
        if ($saptawara === 2 && $wuku === 11) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Susulan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Susulan']);
            }
        }

        // 149. Kala Suwung
        if (($saptawara === 2 && $wuku === 2) ||
            ($saptawara === 3 && ($wuku === 8 || $wuku === 10)) ||
            ($saptawara === 4 && ($wuku === 5 || $wuku === 6 || $wuku === 16 || $wuku === 19)) ||
            ($saptawara === 7 && ($wuku === 11 || $wuku === 13 || $wuku === 14))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Suwung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Suwung']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tampak', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tampak']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Temah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Temah']);
            }
        }

        // 152. Kala Timpang
        if (($saptawara === 3 && $wuku === 1) ||
            ($saptawara === 6 && $wuku === 14) ||
            ($saptawara === 7 && $wuku === 2)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Timpang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Timpang']);
            }
        }

        // 153. Kala Tukaran
        if ($saptawara === 3 && ($wuku === 3 || $wuku === 8)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tukaran', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tukaran']);
            }
        }

        // 154. Kala Tumapel
        if ($wuku === 12 && ($saptawara === 3 || $saptawara === 4)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tumapel', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tumapel']);
            }
        }

        // 155. Kala Tumpar
        if (($saptawara === 3 && $wuku === 13) || ($saptawara === 4 && $wuku === 8)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tumpar', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Tumpar']);
            }
        }

        // 156. Kala Upa
        if ($sadwara === 4 && $triwara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Upa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Upa']);
            }
        }

        // 157. Kala Was
        if ($saptawara === 2 && $wuku === 17) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Was', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Was']);
            }
        }

        // 158. Kala Wikalpa
        if (($saptawara === 2 && ($wuku === 22 || $wuku === 25)) || ($saptawara === 6 && ($wuku === 27 || $wuku === 30))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Wikalpa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Wikalpa']);
            }
        }

        // 159. Kala Wisesa
        if ($sadwara === 5 && $astawara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Wisesa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Wisesa']);
            }
        }

        // 160. Kala Wong
        if ($saptawara === 4 && $wuku === 20) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Wong', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kala Wong']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kaleburau', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kaleburau']);
            }
        }

        // 162. Kamajaya
        if ($saptawara === 4 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 2 || $sasihDay1 === 3 || $sasihDay1 === 7)) || ($pengalantaka === 'Penanggal' && ($sasihDay2 === 2 || $sasihDay2 === 3 || $sasihDay2 === 7)))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kamajaya', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Kamajaya']);
            }
        }

        // 163. Karna Sula
        if (
            ($saptawara === 1 && ($sasihDay1 === 2 || $sasihDay2 === 2)) ||
            ($saptawara === 3 && ($sasihDay1 === 9 || $sasihDay2 === 9)) ||
            ($saptawara === 7 && ($purnama_tilem === 'Purnama' || $purnama_tilem === 'Tilem'))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Karna Sula', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Karna Sula']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Karnasula', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Karnasula']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Lebur Awu', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Lebur Awu']);
            }
        }

        // 166. Lutung Magandong
        if ($saptawara === 5 && ($wuku === 3 || $wuku === 8 || $wuku === 13 || $wuku === 18 || $wuku === 23 || $wuku === 28)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Lutung Magandong', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Lutung Magandong']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Macekan Agung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Macekan Agung']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Macekan Lanang', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Macekan Lanang']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Macekan Wadon', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Macekan Wadon']);
            }
        }

        // 170. Merta Sula
        if ($saptawara === 5 && ($sasihDay1 === 7 || $sasihDay2 === 7)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Merta Sula', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Merta Sula']);
            }
        }

        // 171. Naga Naut
        if ($sasihDay1 === 'no_sasih' || $sasihDay2 === 'no_sasih') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Naga Naut', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Naga Naut']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Pamacekan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Pamacekan']);
            }
        }

        // 173. Panca Amerta
        if ($saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Panca Amerta', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Panca Amerta']);
            }
        }

        // 174. Panca Prawani
        if ($sasihDay1 === 4 || $sasihDay1 === 8 || $sasihDay1 === 12 || $sasihDay2 === 4 || $sasihDay2 === 8 || $sasihDay2 === 12) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Panca Prawani', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Panca Prawani']);
            }
        }

        // 175. Panca Wedhi
        if ($saptawara === 2 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Panca Werdhi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Panca Werdhi']);
            }
        }

        // 176. Pati Paten
        if ($saptawara === 6 && (($sasihDay1 === 10 || $sasihDay2 === 10) || $purnama_tilem === 'Tilem')) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Pati Paten', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Pati Paten']);
            }
        }

        // 177. Patra Limutan
        if ($triwara === 3 && $purnama_tilem === 'Tilem') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Patra Limutan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Patra Limutan']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Pepedan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Pepedan']);
            }
        }

        // 179. Prabu Pendah
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 14) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 14))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Prabu Pendah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Prabu Pendah']);
            }
        }

        // 180. Prangewa
        if ($saptawara === 3 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 1) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 1))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Prangewa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Prangewa']);
            }
        }

        // 181. Purnama Danta
        if ($saptawara === 4 && $pancawara === 5 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purnama Danta', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purnama Danta']);
            }
        }

        // 182. Purna Suka
        if ($saptawara === 6 && $pancawara === 1 && $purnama_tilem === 'Purnama') {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purna Suka', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purna Suka']);
            }
        }

        // 183. Purwani
        if ($sasihDay1 === 14 || $sasihDay2 === 14) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purwani', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purwani']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purwanin Dina', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Purwanin Dina']);
            }
        }

        // 185. Rangda Tiga
        if ($wuku === 7 || $wuku === 8 || $wuku === 15 || $wuku === 16 || $wuku === 23 || $wuku === 24) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Rangda Tiga', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Rangda Tiga']);
            }
        }

        // 186. Rarung Pagelangan
        if ($saptawara === 5 && ($sasihDay1 === 6 || $sasihDay2 === 6)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Rarung Pagelangan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Rarung Pagelangan']);
            }
        }

        // 187. Ratu Magelung
        if ($saptawara === 4 && $wuku === 23) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Magelung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Magelung']);
            }
        }

        // 188. Ratu Mangure
        if ($saptawara === 5 && $wuku === 20) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Mangure', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Mangure']);
            }
        }

        // 189. Ratu Megambahan
        if ($saptawara === 7 && (($pengalantaka === 'Pangelong' && $sasihDay1 === 6) || ($pengalantaka === 'Pangelong' && $sasihDay2 === 6))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Megambahan', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Megambahan']);
            }
        }

        // 190. Ratu Nanyingal
        if ($saptawara === 5 && $wuku === 21) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Nanyingal', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Nanyingal']);
            }
        }

        // 191. Ratu Ngemban Putra
        if ($saptawara === 6 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Ngemban Putra', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Ratu Ngemban Putra']);
            }
        }

        // 192. Rekatadala Ayudana
        if ($saptawara === 1 && ($sasihDay1 === 1 || $sasihDay1 === 6 || $sasihDay1 === 11 || $sasihDay1 === 2 || $sasihDay2 === 6 || $sasihDay2 === 11)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Rekatadala Ayudana', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Rekatadala Ayudana']);
            }
        }

        // 193. Salah Wadi
        if ($wuku === 1 || $wuku === 2 || $wuku === 6 || $wuku === 10 || $wuku === 11 || $wuku === 16 || $wuku === 19 || $wuku === 20 || $wuku === 24 || $wuku === 25 || $wuku === 27 || $wuku === 30) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Salah Wadi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Salah Wadi']);
            }
            // dd($description);
        }

        // 194. Sampar Wangke
        if ($saptawara === 2 && $sadwara === 2) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sampar Wangke', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sampar Wangke']);
            }
        }

        // 195. Sampi Gumarang Munggah
        if ($pancawara === 3 && $sadwara === 4) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sampi Gumarang Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sampi Gumarang Munggah']);
            }
        }

        // 196. Sampi Gumarang Turun
        if ($pancawara === 3 && $sadwara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sampi Gumarang Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sampi Gumarang Turun']);
            }
        }

        // 197. Sarik Agung
        if ($saptawara === 4 && ($wuku === 25 || $wuku === 4 || $wuku === 11 || $wuku === 18)) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sarik Agung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sarik Agung']);
            }
        }

        // 198. Sarik Ketah
        if (($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 7 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sarik Ketah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sarik Ketah']);
            }
        }

        // 199. Sedana Tiba
        if ($saptawara === 5 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 7) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 7))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sedana Tiba', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sedana Tiba']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sedana Yoga', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sedana Yoga']);
            }
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Semut Sadulur', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Semut Sadulur']);
            }
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Siwa Sampurna', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Siwa Sampurna']);
            }
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Bagia', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Bagia']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sedana Yoga', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sedana Yoga']);
            }
        }

        // 201. Semut Sadulur
        if (($saptawara === 6 && $pancawara === 3) ||
            ($saptawara === 7 && $pancawara === 4) ||
            ($saptawara === 1 && $pancawara === 5)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Semut Sadulur', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Semut Sadulur']);
            }
        }

        // 202. Siwa Sampurna
        if (($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 4) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 4))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5))) ||
            ($saptawara === 5 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Siwa Sampurna', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Siwa Sampurna']);
            }
        }

        // 203. Sri Bagia
        if (($saptawara === 2 && ($wuku === 6 || $wuku === 15 || $wuku === 21)) ||
            ($saptawara === 4 && $wuku === 4) ||
            ($saptawara === 7 && ($wuku === 1 || $wuku === 25))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Bagia', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Bagia']);
            }
        }

        // 204. Sri Murti
        if ($sadwara === 5 && $astawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Murti', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Murti']);
            }
        }

        // 205. Sri Tumpuk
        if ($astawara === 1) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Tumpuk', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Sri Tumpuk']);
            }
        }

        // 206. Srigati
        if (($triwara === 3 && $pancawara === 1 && $sadwara === 3) ||
            ($triwara === 3 && $pancawara === 1 && $sadwara === 6)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati']);
            }
        }

        // 207. Srigati Jenek
        if ($pancawara === 5 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati Jenek', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati Jenek']);
            }
        }

        // 208. Srigati Munggah
        if ($pancawara === 1 && $sadwara === 3) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati Munggah', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati Munggah']);
            }
        }

        // 209. Srigati Turun
        if ($pancawara === 1 && $sadwara === 6) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati Turun', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Srigati Turun']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Subhacara', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Subhacara']);
            }
        }

        // 211. Swarga Menga
        if (($saptawara === 3 && $pancawara === 3 && $wuku === 3 &&
                (($pengalantaka === 'Penanggal' && $sasihDay1 === 11) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 11))) ||
            ($saptawara === 5 && $pancawara === 2 && $wuku === 4)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Swarga Menga', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Swarga Menga']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Taliwangke', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Taliwangke']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Titibuwuk', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Titibuwuk']);
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
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Tunut Masih', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Tunut Masih']);
            }
        }

        // 215. Tutur Mandi
        if (($saptawara === 1 && $wuku === 26) ||
            ($saptawara === 5 && ($wuku === 3 || $wuku === 9 || $wuku === 15 || $wuku === 20 || $wuku === 21 || $wuku === 24)) ||
            ($saptawara === 6 && $wuku === 2) ||
            ($saptawara === 7 && $wuku === 24)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Tutur Mandi', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Tutur Mandi']);
            }
        }

        // 216. Uncal Balung
        if ($wuku === 12 || $wuku === 13 || (($wuku === 14 && $saptawara === 1) || ($wuku === 16 && $saptawara < 5))) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Uncal Balung', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Uncal Balung']);
            }
        }

        // 217. Upadana Merta
        if (
            $saptawara === 1 && (($pengalantaka === 'Penanggal' && ($sasihDay1 === 1 || $sasihDay1 === 8 || $sasihDay1 === 6 || $sasihDay1 === 10)) ||
                ($pengalantaka === 'Penanggal' && ($sasihDay2 === 1 || $sasihDay2 === 8 || $sasihDay2 === 6 || $sasihDay2 === 10)))
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Upadana Merta', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Upadana Merta']);
            }
        }

        // 218. Werdi Suka
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 10) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 10)) &&
            ($no_sasih === 1)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Werdi Suka', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Werdi Suka']);
            }
        }

        // 219. Wisesa
        if (
            $saptawara === 4 && $pancawara === 2 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 13) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 13)) &&
            ($no_sasih === 1)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Wisesa', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Wisesa']);
            }
        }

        // 220. Wredhi Guna
        if (
            $saptawara === 4 && $pancawara === 4 && (($pengalantaka === 'Penanggal' && $sasihDay1 === 5) || ($pengalantaka === 'Penanggal' && $sasihDay2 === 5)) &&
            ($no_sasih === 1)
        ) {
            if ($makna) {
                $description = AlaAyuningDewasa::where('dewasa_ayu', end($dewasaAyu))->pluck('description')->first();
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Wredhi Guna', 'keterangan' => $description]);
            } else {
                array_push($dewasaAyu, ['ala_ayuning_dewasa' => 'Wredhi Guna']);
            }
        }

        // Remove leading comma and space
        // $dewasaAyu = ltrim($dewasaAyu, ', ');

        // if ($makna) {
        //     return response()->json([
        //         'dewasaAyu' => $dewasaAyu,
        //         'keterangan' => $description,
        //     ], 200);
        // } else {
        //     return response()->json([
        //         'dewasaAyu' => $dewasaAyu,
        //     ], 200);
        // }
        return $dewasaAyu;
    }
}
