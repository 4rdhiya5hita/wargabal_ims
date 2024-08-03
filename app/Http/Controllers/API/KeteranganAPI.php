<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AlaAyuningDewasa;
use App\Models\Astawara;
use App\Models\Caturwara;
use App\Models\Dasawara;
use App\Models\Dwiwara;
use App\Models\EkaJalaRsi;
use App\Models\Ekawara;
use App\Models\HariRaya;
use App\Models\Ingkel;
use App\Models\Jejepan;
use App\Models\Lintang;
use App\Models\ListKeterangan;
use App\Models\Neptu;
use App\Models\PancaSudha;
use App\Models\Pancawara;
use App\Models\Pangarasan;
use App\Models\PengajuanKeterangan;
use App\Models\Pratiti;
use App\Models\Rakam;
use App\Models\Sadwara;
use App\Models\Sangawara;
use App\Models\Saptawara;
use App\Models\Triwara;
use App\Models\User;
use App\Models\WatekAlit;
use App\Models\WatekMadya;
use App\Models\Wuku;
use App\Models\Zodiak;
use Illuminate\Http\Request;

class KeteranganAPI extends Controller
{
    private function validasiKeterangan($api_key)
    {
        $user = User::where('api_key', $api_key)->first();

        if (!$user) {
            $valid = false;
        } else {
            $valid = true;
        }

        return $valid;
    }

    public function listPengajuanKeterangans(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $pengajuan = PengajuanKeterangan::all();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $pengajuan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function listPengajuanKeterangansById(Request $request, $id)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $pengajuan = PengajuanKeterangan::where('id', $id)->get();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $pengajuan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function pengajuanKeterangans(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $validated = $request->validate([
                'user_web_id' => 'required',
                'key_id' => 'required',
                'key_name' => 'required',
                'item_id' => 'required',
                'item_name' => 'required',
                'keterangan' => 'required'
            ]);

            if ($validated) {
                $save_pengajuan = [
                    'user_web_id' => $request->user_web_id,
                    'key_id' => $request->key_id,
                    'key_name' => $request->key_name,
                    'item_id' => $request->item_id,
                    'item_name' => $request->item_name,
                    'keterangan' => $request->keterangan
                ];

                PengajuanKeterangan::create($save_pengajuan);

                return response()->json([
                    'pesan' => 'Sukses'
                ]);
            } else {
                return response()->json([
                    'pesan' => 'Gagal',
                    'data' => 'Seluruh input harus diisi'
                ]);
            }
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editPengajuanKeterangans(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;

        if ($valid) {
            $list_pengajuan = PengajuanKeterangan::all();
            $pengajuan = PengajuanKeterangan::find($id);

            if ($pengajuan->status_keterangan != $request->status_keterangan) {
                if ($request->status_keterangan == 1) {
                    foreach ($list_pengajuan as $item) {
                        if ($item->key_id == $pengajuan->key_id && $item->item_id == $pengajuan->item_id && $item->status_keterangan == 1) {
                            $item->status_keterangan = 2;
                            $item->save();
                        }
                    }
                }
                $status_keterangan_berubah = true;
            } else {
                $status_keterangan_berubah = false;
            }

            $pengajuan->status_pengajuan = $request->status_pengajuan;
            $pengajuan->status_keterangan = $request->status_keterangan;
            
            if ($pengajuan->status_pengajuan == $request->status_pengajuan) {
                $pengajuan->tanggal_validasi = $pengajuan->tanggal_validasi;
                $pengajuan->save();
            } else {
                $pengajuan->tanggal_validasi = $request->tanggal_validasi;
            }

            return response()->json([
                'pesan' => 'Sukses',
                'status_keterangan_berubah' => $status_keterangan_berubah,
                'data' => $pengajuan
            ]);

        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganHariRaya(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = HariRaya::all();
            $allNull = $keterangan->every(function ($item) {
                return is_null($item->description);
            });

            if ($allNull) {
                $keterangan = null;
            }
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganAlaAyuningDewasa(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = AlaAyuningDewasa::all();
            $allNull = $keterangan->every(function ($item) {
                return is_null($item->description);
            });

            if ($allNull) {
                $keterangan = null;
            }
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function listKeterangan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = ListKeterangan::all();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        }
    }

    public function keteranganIngkel(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Ingkel::all();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganJejepan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Jejepan::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganLintang(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Lintang::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganNeptu(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Neptu::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganEkaJalaRsi(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = EkaJalaRsi::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganWatekMadya(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = WatekMadya::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganWatekAlit(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = WatekAlit::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganRakam(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Rakam::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganPratiti(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Pratiti::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }


    public function keteranganPancaSudha(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = PancaSudha::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganPangarasan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Pangarasan::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganZodiak(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Zodiak::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganEkawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Ekawara::all();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganDwiwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Dwiwara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganTriwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Triwara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganCaturwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Caturwara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganPancawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Pancawara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganSadwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Sadwara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganSaptawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Saptawara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganAstawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Astawara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganSangawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Sangawara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganDasawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Dasawara::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function keteranganWuku(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $keterangan = Wuku::all();
            return response()->json([
                'pesan' => 'Sukses',
                'data' => $keterangan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editAlaAyuningDewasa(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $validated = $request->validate([
                'id' => 'required',
                'keterangan' => 'required'
            ]);

            if ($validated) {
                $id = $request->id;
                if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

                $alaAyuningDewasa = AlaAyuningDewasa::find($id);
                $alaAyuningDewasa->description = $keterangan;
                $alaAyuningDewasa->save();

                return response()->json([
                    'pesan' => 'Sukses',
                    'data' => $alaAyuningDewasa
                ]);
            } else {
                return response()->json([
                    'pesan' => 'Gagal',
                    'data' => 'Seluruh input harus diisi'
                ]);
            }
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editHariRaya(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $validated = $request->validate([
                'id' => 'required',
                'keterangan' => 'required'
            ]);

            if ($validated) {
                $id = $request->id;
                if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

                $hariRaya = HariRaya::find($id);
                $hariRaya->description = $keterangan;
                $hariRaya->save();

                return response()->json([
                    'pesan' => 'Sukses',
                    'data' => $hariRaya
                ]);
            } else {
                return response()->json([
                    'pesan' => 'Gagal',
                    'data' => 'Seluruh input harus diisi'
                ]);
            }
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editIngkel(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);

        if ($valid) {
            $validated = $request->validate([
                'id' => 'required',
                'keterangan' => 'required'
            ]);

            if ($validated) {
                $id = $request->id;
                if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

                $ingkel = Ingkel::find($id);
                $ingkel->keterangan = $keterangan;
                $ingkel->save();

                return response()->json([
                    'pesan' => 'Sukses',
                    'data' => $ingkel
                ]);
            } else {
                return response()->json([
                    'pesan' => 'Gagal',
                    'data' => 'Seluruh input harus diisi'
                ]);
            }
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editJejepan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $jejepan = Jejepan::find($id);
            $jejepan->keterangan = $keterangan;
            $jejepan->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $jejepan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editLintang(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $lintang = Lintang::find($id);
            $lintang->keterangan = $keterangan;
            $lintang->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $lintang
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editNeptu(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $neptu = Neptu::find($id);
            $neptu->keterangan = $keterangan;
            $neptu->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $neptu
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editEkaJalaRsi(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $ekaJalaRsi = EkaJalaRsi::find($id);
            $ekaJalaRsi->keterangan = $keterangan;
            $ekaJalaRsi->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $ekaJalaRsi
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editWatekMadya(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $watekMadya = WatekMadya::find($id);
            $watekMadya->keterangan = $keterangan;
            $watekMadya->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $watekMadya
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editWatekAlit(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $watekAlit = WatekAlit::find($id);
            $watekAlit->keterangan = $keterangan;
            $watekAlit->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $watekAlit
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editRakam(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $rakam = Rakam::find($id);
            $rakam->keterangan = $keterangan;
            $rakam->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $rakam
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editPratiti(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $pratiti = Pratiti::find($id);
            $pratiti->keterangan = $keterangan;
            $pratiti->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $pratiti
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editPancaSudha(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $pancaSudha = PancaSudha::find($id);
            $pancaSudha->keterangan = $keterangan;
            $pancaSudha->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $pancaSudha
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editPangarasan(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $pangarasan = Pangarasan::find($id);
            $pangarasan->keterangan = $keterangan;
            $pangarasan->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $pangarasan
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editZodiak(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $zodiak = Zodiak::find($id);
            $zodiak->keterangan = $keterangan;
            $zodiak->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $zodiak
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editEkawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $ekawara = Ekawara::find($id);
            $ekawara->keterangan = $keterangan;
            $ekawara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $ekawara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editDwiwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $dwiwara = Dwiwara::find($id);
            $dwiwara->keterangan = $keterangan;
            $dwiwara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $dwiwara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editTriwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $triwara = Triwara::find($id);
            $triwara->keterangan = $keterangan;
            $triwara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $triwara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editCaturwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $caturwara = Caturwara::find($id);
            $caturwara->keterangan = $keterangan;
            $caturwara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $caturwara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editPancawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $pancawara = Pancawara::find($id);
            $pancawara->keterangan = $keterangan;
            $pancawara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $pancawara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editSadwara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $sadwara = Sadwara::find($id);
            $sadwara->keterangan = $keterangan;
            $sadwara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $sadwara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editSaptawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $saptawara = Saptawara::find($id);
            $saptawara->keterangan = $keterangan;
            $saptawara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $saptawara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editAstawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $astawara = Astawara::find($id);
            $astawara->keterangan = $keterangan;
            $astawara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $astawara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editSangawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $sangawara = Sangawara::find($id);
            $sangawara->keterangan = $keterangan;
            $sangawara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $sangawara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editDasawara(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $dasawara = Dasawara::find($id);
            $dasawara->keterangan = $keterangan;
            $dasawara->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $dasawara
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }

    public function editWuku(Request $request)
    {
        $api_key = $request->header('x-api-key');
        $valid = $this->validasiKeterangan($api_key);
        $id = $request->id;
        if ($request->keterangan == '-') {
                    $keterangan = null;
                } else {
                    $keterangan = $request->keterangan;
                }

        if ($valid) {
            $wuku = Wuku::find($id);
            $wuku->keterangan = $keterangan;
            $wuku->save();

            return response()->json([
                'pesan' => 'Sukses',
                'data' => $wuku
            ]);
        } else {
            return response()->json([
                'pesan' => 'API Key tidak valid',
            ]);
        }
    }
}
