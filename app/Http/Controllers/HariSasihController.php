<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HariSasihController extends Controller
{
    public function getHariSasih($tanggal, $refTanggal, $refPenanggal, $refNgunaratri)
    {
        $selisih = intval(date_diff(date_create($tanggal), date_create($refTanggal))->format('%a'));
        $jumlahNgunaratri = floor(($selisih + $refNgunaratri) / 63);

        // Jumlah berikut ini sering digunakan dalam pencarian Ngunaratri dan Pengalantaka
        $carijumlahNgunaratriDanPenanggal = ($selisih + $refNgunaratri) % 63;

        if ($carijumlahNgunaratriDanPenanggal === 0) {
            $jumlahNgunaratri--;
        }
        
        $jumlah = $refPenanggal + $selisih + $jumlahNgunaratri;
        
        // Cari Pengalantaka
        if (floor(($jumlah - 1) / 15) % 2 === 0) {
            $pengalantaka = 'Pangelong';
        } else {
            $pengalantaka = 'Penanggal';
        }
        
        // Cari Penanggal 1
        $penanggal = ($jumlah % 15);
        if ($penanggal === 0) {
            $penanggal = 15;
        } elseif ($penanggal < 0) {
            $penanggal += 15;
        }
        
        // Cari Penanggal 2
        $penanggal2 = 0;
        if ($carijumlahNgunaratriDanPenanggal === 0) {
            $penanggal2 = $penanggal + 1;
        }
        if ($penanggal2 === 16) {
            $penanggal2 = 1;
        }
        
        return [
            'penanggal_1' => $penanggal,
            'penanggal_2' => $penanggal2,
            'pengalantaka' => $pengalantaka,
        ];
        
    }


    public function getSasihBefore1992($tanggal, $refTanggal, $refPenanggal, $refNgunaratri, $refSasih, $refTahunSaka)
    {
        $isNampih = FALSE;
        $selisih = strtotime($tanggal) - strtotime($refTanggal);
        $jumlahNgunaratri = floor(($selisih + $refNgunaratri) / 63);

        if (($selisih + $refNgunaratri) % 63 == 0) {
            $jumlahNgunaratri--;
        }

        $penambahanSasih = floor(($selisih + $refPenanggal + 14 + $jumlahNgunaratri) / 30);
        $hasilTahun = $refTahunSaka;

        $i = 0;
        $i2 = $refSasih;

        if ($penambahanSasih < 0) {
            $penambahanSasih = abs($penambahanSasih);
        }

        while ($i < $penambahanSasih) {
            $i++;
            $i2--;
            $i2 = $i2 % 12;

            if ($i2 == 0) {
                $i2 = 12;
            }

            if ($i2 == 9) {
                $hasilTahun--;
            }

            if ($isNampih) {
                $isNampih = false;
            } else {
                $hasilTahun = $hasilTahun % 19;
                if ($hasilTahun == 0 || $hasilTahun == 6 || $hasilTahun == 11) {
                    if ($i2 == 12) {
                        $i2++;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 3 || $hasilTahun == 8 || $hasilTahun == 14 || $hasilTahun == 16) {
                    if ($i2 == 1) {
                        $i2++;
                        $isNampih = true;
                    }
                }
            }
        }

        if ($i2 == 0) {
            $i2 = 12;
        }

        $no_sasih = $i2;
        $hasil_tahun = $hasilTahun;

        return [
            'no_sasih' => $no_sasih,
            'hasil_tahun' => $hasil_tahun
        ];
    }



    public function getSasihAfter2002($tanggal, $refTanggal, $refPenanggal, $refNgunaratri, $refSasih, $refTahunSaka)
    {
        $isNampih = FALSE;
        $selisih = strtotime($tanggal) - strtotime($refTanggal);
        $jumlahNgunaratri = floor(($selisih + $refNgunaratri) / 63);

        if (($selisih + $refNgunaratri) % 63 == 0) {
            $jumlahNgunaratri--;
        }

        $penambahanSasih = floor(($selisih + $refPenanggal + 14 + $jumlahNgunaratri) / 30);
        $hasilTahun = $refTahunSaka;

        $i = 0;
        $i2 = $refSasih;

        while ($i < $penambahanSasih) {
            $i++;
            $i2++;
            $i2 = $i2 % 12;

            if ($i2 == 0) {
                $i2 = 12;
            }

            if ($i2 == 10) {
                $hasilTahun++;
            }

            if ($isNampih) {
                $isNampih = false;
            } else {
                $hasilTahun = $hasilTahun % 19;
                if ($hasilTahun == 0 || $hasilTahun == 6 || $hasilTahun == 11) {
                    if ($i2 == 12) {
                        $i2--;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 3 || $hasilTahun == 8 || $hasilTahun == 14 || $hasilTahun == 16) {
                    if ($i2 == 1) {
                        $i2--;
                        $isNampih = true;
                    }
                }
            }
        }

        if ($i2 == 0) {
            $i2 = 12;
        }

        $no_sasih = $i2;
        $hasil_tahun = $hasilTahun;

        return [
            'no_sasih' => $no_sasih,
            'hasil_tahun' => $hasil_tahun
        ];
    }


    public function getSasihBetween($tanggal, $refTanggal, $refPenanggal, $refNgunaratri, $refSasih, $refTahunSaka)
    {
        $isNampih = FALSE;
        $selisih = strtotime($tanggal) - strtotime($refTanggal);
        $jumlahNgunaratri = floor(($selisih + $refNgunaratri) / 63);

        if (($selisih + $refNgunaratri) % 63 == 0) {
            $jumlahNgunaratri--;
        }

        $penambahanSasih = floor(($selisih + $refPenanggal + 14 + $jumlahNgunaratri) / 30);
        $hasilTahun = $refTahunSaka;

        $i = 0;
        $i2 = $refSasih;

        while ($i < $penambahanSasih) {
            $i++;
            $i2++;
            $i2 = $i2 % 12;

            if ($i2 == 0) {
                $i2 = 12;
            }

            if ($i2 == 10) {
                $hasilTahun++;
            }

            if ($isNampih) {
                $isNampih = false;
            } else {                
                $hasilTahun = $hasilTahun % 19;
                if ($hasilTahun == 2 || $hasilTahun == 10) {
                    if ($i2 == 12) {
                        $i2--;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 4) {
                    if ($i2 == 4) {
                        $i2--;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 7) {
                    if ($i2 == 2) {
                        $i2--;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 13) {
                    if ($i2 == 11) {
                        $i2--;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 15) {
                    if ($i2 == 3) {
                        $i2--;
                        $isNampih = true;
                    }
                } elseif ($hasilTahun == 18) {
                    if ($i2 == 1) {
                        $i2--;
                        $isNampih = true;
                    }
                }
            }
        }

        if ($i2 == 0) {
            $i2 = 12;
        }

        $no_sasih = $i2;
        $hasil_tahun = $hasilTahun;

        return [
            'no_sasih' => $no_sasih,
            'hasil_tahun' => $hasil_tahun
        ];

    }
}