public function searchHariRaya(Request $request)
{
    $tanggal_mulai = '2023-07-25';
    $tanggal_selesai = '2023-08-10';
    $tanggal_mulai = Carbon::parse($tanggal_mulai);
    $tanggal_selesai = Carbon::parse($tanggal_selesai);

    $kalender = [];

    while ($tanggal_mulai <= $tanggal_selesai) {
        $kalender[] = [
            'tanggal' => $tanggal_mulai->toDateString(),
            'hariRaya' => $this->getHariRaya($tanggal_mulai),
        ];
        $tanggal_mulai->addDay();
    }

    $response = [
        'message' => 'Success',
        'data' => [
            'kalender' => $kalender,
        ]
    ];

    return response()->json($response, 200);
}

private function getHariRaya($tanggal)
{
    // Panggil semua controller yang dibutuhkan
    $wukuController = new WukuController();
    $saptawaraWaraController = new SaptaWaraController_07();
    $pancaWaraController = new PancaWaraController_05();
    $triWaraController = new TriWaraController_03();
    $pengalantakaController = new PengalantakaController;
    $hariSasihController = new HariSasihController;
    $hariRayaController = new HariRayaController();

    // Lakukan semua perhitungan hanya sekali
    $hasilAngkaWuku = $wukuController->getNoWuku($tanggal, 88, '1992-01-01');
    $hasilWuku = $wukuController->getWuku($hasilAngkaWuku);
    $saptawara = $saptawaraWaraController->getSaptawara($tanggal);
    $namaSaptawara = $saptawaraWaraController->getNamaSaptaWara($saptawara);
    $pancawara = $pancaWaraController->getPancawara($hasilAngkaWuku);
    $namaPancawara = $pancaWaraController->getNamaPancaWara($pancawara);
    $triwara = $triWaraController->gettriwara($hasilAngkaWuku);
    $namaTriwara = $triWaraController->getNamatriwara($triwara);
    $pengalantaka = $pengalantakaController->getPengalantaka($tanggal, '1992-01-01', 11, 22);
    $hariSasih = $hariSasihController->getHariSasih($tanggal, '1992-01-01', 11, 22);
    $hariRaya = $hariRayaController->getHariRaya($tanggal, $hariSasih['penanggal_1'], $hariSasih['penanggal_2'], $pengalantaka, $hariSasih['no_sasih'], $triwara, $pancawara, $saptawara, $hasilWuku);

    return $hariRaya;
}

INSERT INTO `tb_piodalan` (`id`, `piodalan`, `arti`, `pura`) VALUES
(83, 'Redite Paing Pahang', 'Tidak ada informasi yang tersedia.', 'Pura Pasek Tohjiwa Kekeran. Mengwi. Pura Pasek Sandra Peguyangan Badung.'),
(84, 'Anggara Wage Pahang', 'Tidak ada informasi yang tersedia.', 'Pura Batu Madeg (Meru Tumpang Sanga) di Besakih, Pura Hyang Tibha i Batuan Sakah.'),
(85, 'Buda Keliwon Pahang', 'Tidak ada informasi yang tersedia.', 'Pura Luhur Puncak Padang Dawa Baturiti Tabanan. Pura Silayukti Padangbai-Karangasem. Pura Aer Jeruk Sukawati, Pura Dangin Pasar Batuan-Sukawati. Pura Penataran di Batuyang-Batubulan. Pura Desa Lembeng Ketewel-Sukawati, Pura Pasek Bendesa Dukuh-Kediri-Tabanan, Pura Kawitan Dalem Sukawati Gianyar, Pura Kresek Banyuning-Buleleng. Pura Puseh di Bebandem-Karangasem. Merajan Pasek Kubayan-Gaji. Merajan pasek Gelgel Jeroan Abang-Songan. Merajan Pasek Subrata Temaga. Merajan Pasek Gelgel Bungbungan. Pura Sad Kahyangan Batu Medahu Swana Nusa Penida. Pura Buda Kliwon Penatih-Denpasar. Pura Penataran Dukuh Nagasari Bebandem Karangasem. Pura Pasek Bendesa Tagtag Paguyangan. Pura Pulasari Sibang Gede Abiansemal. Pura Batur Sari Ubud. Pura Penataran Agung Sukawati.'),
(86, 'Soma Keliwon Krulut', 'Tidak ada informasi yang tersedia.', 'Pura Pasel Gelgel Kekeran Mngwi Badung. Merajan Pasek Subadra Kramas-Gianyar.'),
(87, 'Tilem Ketiga', 'Tidak ada informasi yang tersedia.', 'Aci-aci Penguripan Gumi di Pura Ulun Kulkul Besakih.'),
(88, 'Hari Tumpek Krurut', 'Tidak ada informasi yang tersedia.', 'Pura Pasek Gelgel Br Tengah Buleleng. Pura Dalem Pemuteran di Desa Jelantik Tojan - Klungkung. Pura Pedarmaan Bhujangga Waisnawa di Besakih. Pura Taman Sari Desa Gunungsari Penebel - Tabanan. Pura Dalem Tarukan di Bebalang Bangli. Pura Benua Kangin Besakih. Pura Merajan Kanginan (Ida Betara Empu Beradah) di Besakih.'),
(89, 'Redite Umanis Merakih', 'Tidak ada informasi yang tersedia.', 'Pura Parangan Tengah Banjar Ceningan Kangin - Lembongan , Nusa Penida. Pura Dalem Celuk Sukawati - Gianyar.'),
(90, 'Buda Wage Merakih', 'Tidak ada informasi yang tersedia.', 'Pura Bendesa Mas Kepisah - Pedungan - Denpasar Selatan. Pura Natih Banjar Kalah - Batubulan, Pr. Puseh. Pura Desa Silakarang - Singapadu. Pura dalem Petitenget - Kerobokan - Kuta. Pura Dalem Pulasari - Samplangan - Gianyar, Pura Kubayan - Kepisah - Pedungan - Denpasar - Selatan. Pura Pasek gelgel Banjar Tanahpegat - Tabanan, Pr. Paibon Banjar Bengkel - Sumerta - Denpasar. Pura Pasek Lumintang - Denpasar, Pr. Panti Penyarikan Medahan - Sanding - Tampaksiring, Pr. Pasar Agung Banjar Dauh Peken - Kaba-kaba - Tabanan.'),
(91, 'Anggar Kasih Tambir', 'Tidak ada informasi yang tersedia.', 'Pura Dalem Puri Batuan - Sukawati. Pura Dalem Kediri Silakarang - Singapadu. Pura dalem di Desa Sukawati. Pura Dalem di Desa Singakerta - Ubud. Pura dalem Lembeng - Ketewel - Sukawati. Pura Paibon Pasek Tangkas - Peliatan - Ubud. Pura Puseh ngukuhin - Keramas - Gianyar. Pura Pemerajan Agung Ki Telabah, Tuakilang - Tabanan. Pura Karang Buncing di Blahbatuh. Pura Dalem Bubunan di Desa - Seririt Buleleng. Pura Desa Badung di Kota Denpasar. Merajan Pasek Gelgel Gobleg Desa - Kayuputih - Turupinghe - Banjar - Buleleng. Pura Luwur Pedengenan Bedha - Bongan - Tabanan Mr. Dukuh Sebudi , Mr. Pasek Ngukuhin - Keramas. Pura Pucak Payongan Banjar Lungsiakan - Desa Kedewatan - Ubud - Gianyar, Pura Tanah Kilap " Griya Anyar" - Suwung Kawuh - Denpasar Selatan. Pura Selukat Desa Keramas - Blahbatuh - Gianyar. Pura Dalem Tampuagan, Desa Peninjoan - Tembuku - Bangli. Pura Waturenggong Desa Taro. Pura Dalem Bentuyung, Ubud. Pura Puseh Ubud. Pura Dalem Peliatan Ubud.'),
(92, 'Buda Umanis Tambir', 'Tidak ada informasi yang tersedia.', 'Pura Sari Bankar Titih Kapal.'),
(93, 'Purnama Kapat', 'Tidak ada informasi yang tersedia.', 'Bhatara Tiga Sakti (Padmasana) di Penataran Agung Besakih. Pura Meru Cakra Lombok. Pura Lempuyang Madya Karangasem. Pura Penerejon di Kintamani. Pura Pulaki Buleleng. Pura Tirta Emupul di Tampak Siring. Pr. Puseh-Pr. Desa. Pura Penataran. Pura Luhuring Akasa, Bhatara Hyang Basukih di Cemenggoan Sukawati. Pura Tirta di Negari Singapadu. Pura Puseh-Pr. Desa. Pura Penataran di Desa Tangsub Sukawati. Pura Penataran Agung di Tegalalangm. Pura Desa Denjalan, Tegaltamu, Tegehe, Batuyang, dan Batuaji Batubulan. Pura Puseh di Singakerta Ubud. Pura Nataran Sanding Tampaksiring. Pura Bakung Ceningan Nusa Penida. Pura Pasek Getas Kawan Kedewatan Ubud. Pura Agung Dukuh Sakti Pangku/Subamia Braban. Pura Pasek Gelgel Carik Selemadeg Tabanan. Pura Pasek Gelgel Klating Dukuh Kerambitan. Pura Pasek Bendesa Mas Gadungan Selemadeng. Pura Agung Pasek Tohjiwa Wanasari Selemadeg Pr. Penataran Pasek Kayu Putih Bandem Karangsem. Pura Puseh Werdi Agung emoga Bolang Mangondow Sulut. Pura Dukuh Segening Wangsiang Karangasem. Pura Dalem Kahyangan Arya Gajahpare di Sukaluwih Tejakula Buleleng. Pura Pasraman Suci di Renon Denpasar. Pura Penaratan Agung Kertabumi Taman Mini Indonesia Indah Jakarta Timur. Pura Puru Luwur Waisnawa, Asah Badung-Sepang Buleleng, Pura Ulun Danu Batur di Songan Kintamani. Pura Agung Surya Bhuwana Skyline Jayapura Papua. Pura Dalem Bengkel Ubung Denpasar, Merajan Suci Geriya Penataran Gemeh Denpasar. Pura Pejenggali di Tegalalang. Pura Pasek Bendesa Gadungan Selemadeg Tabanan. Pura Panti Pasek Gelgel Desa Meliling Kerambitan. Pura Pajenengan Dukuh Ogan Desa Sangkan Gunung Rendang Karangasem. Pura Dadia Dukuh Segening, Desa Swastika Buana Seputih Banyak Lampung Tengah. Pura Dukuh Sakti Blatung Kuruh Kerambitan. Pura Gumang (bukit Juru) Desa Bugbug Karangasem. Pura Kawitan Arya Samping Banjar Langon Kapal. Pura Pejenengan Pulasari Desa Dukuh Sidemen Karangasem. Pura Segara Penimbangan Dr. Bakti Segara Buleleng Kota. Pura Puseh Yeh Ulakan Suana Nusa Penida. Pr. Mentik Ring Gunung Lebah Batur Kintamani. Pura Pasek Tangkas Kori Agung Kuruh Kerambitan. Pr. Penataran Ubud. Pura Luhur Giri Kusuma Daun Peken Blahkiuh Abiansemal, Pemerajan Agung Jambe Guwang Sukawati, Banjar Taman Sari Busungbiu Buleleng, Pura Dalem Puri di Batuan Sukawati Pura Dalem Kediri Silakarang Singapadu, Pura Dalem di Desa Sukawati. Pura Dalem di Desa Singakerta Ubud.');
