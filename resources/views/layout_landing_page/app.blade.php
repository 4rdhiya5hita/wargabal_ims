<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>Kalender Bali</title>
    <meta name="language" content="id" />
    <meta name="Description" content="widget kalender bali; Kalender Bali untuk penentuan ala ayuning dewasa perkawinan berdasarkan fuzifikasi mamdani; Penentuan hari baik perkawinan di bali menggunakan logika fuzzy berdasarkan kalender bali;">
    <meta name="keywords" content="widget kalender bali; kalender bali; wariga; hari baik perkawinan; padewasan;  calender bali; logika fuzzy; metode mamdani; hari baik; hari perkawinan; bali">
    <meta name="generator" content="widget kalender bali; penentuan hari baik perkawinan di bali menggunakan logika fuzzy berdasarkan kalender bali">
    <meta name="Content-Type" content="widget kalender bali, kalender bali, wariga, padewasan,  calender bali, logika fuzzy, metode mamdani, hari baik, hari perkawinan, bali">

    <!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="https://www.kalenderbali.info/asset/css/ie.css" media="screen, projection" />
	<![endif]-->
    <!--[if lt IE 8]>
   <link rel="stylesheet" type="text/css" href="https://www.kalenderbali.info/asset/css/all.css" media="all" />
   <![endif]-->
    <link rel="stylesheet" href="{{ asset('css/navbar/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    @yield('style')
</head>

<body>
    @include('layout_landing_page.navbar')
    <div class="bg-all">
        <div class="bg-landing">
            <div class="row">
                @yield('content')
            </div>
        </div>
    </div>
    <div class="bg-all">
        <div class="bg-landing2">
            @yield('content2')
        </div>
    </div>
    @include('layout_landing_page.footer')
</body>

@yield('script')