<!-- About Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                <div class="d-flex flex-column">
                    <!-- <img class="img-fluid rounded w-75 align-self-end" src="img/about-1.jpg" alt=""> -->

                    <!-- Carousel Start -->
                    <div class="carousel-container-back-back align-self-center">
                        <div class="carousel-back-back">
                        </div>
                    </div>
                    <div class="carousel-container-back align-self-end">
                        <div class="carousel-back">
                        </div>
                    </div>
                    <div class="carousel-container">
                        <div class="carousel-slide">
                        </div>
                    </div>
                    <!-- Carousel End -->
                </div>
            </div>
            <div class="col-lg-6 wow">
                <div class="landing-carousel mb-4">Sedikit Cerita tentang Kalender Bali</div>
                <div class="landing-description text-center">
                    Kalender Caka Bali adalah kalender yang dibuat atau diciptakan di Bali secara khusus dengan penggabungan dari semua sistim. Dengan mengacu pada pengguna kalender tersebut bagi pemakainya, dalam hal merencanakan suatu hal hari baik atau dewasa-ayu untuk suatu pelaksanaan kegiatan yang menyangkut tentang upacara keagamaan, seperti odalan di suatu pura akan selalu berpedoman pada kalender Caka Bali.
                    Dalam kalender Saka yang berlaku di Bali, jatuhnya bulan-bulan kabisat, tidak sama diantara para pengamat wariga. banyak varian dalam penggunaan sistem kabisat ini.
                </div>
                <a class="btn btn-primary rounded-pill py-3 px-5 mt-3" href="">Read More</a>
            </div>
        </div>
    </div>
</div>
<!-- About End -->

<script>
    $(document).ready(function() {
        var slides = $('.carousel-slide');
        var slidesBack = $('.carousel-back');
        var slideBackBack = $('.carousel-back-back');

        function loadRandomImage(element) {
            // random angka dari 1-14
            var randomIndex = Math.floor(Math.random() * 14) + 1;
            var imagePath = `img/carousel/random-${randomIndex}.jpg`;
            element.html(`<img src="${imagePath}" class="d-block w-100" alt="...">`);
        }

        function showSlide() {
            loadRandomImage(slides);
        }
        function showSlideBack() {
            loadRandomImage(slidesBack);
        }
        function showSlideBackBack() {
            loadRandomImage(slideBackBack);
        }

        // Mulai animasi
        showSlide();
        setInterval(showSlide, 3000);
        showSlideBack();
        setInterval(showSlideBack, 5000);
        showSlideBackBack();
        setInterval(showSlideBackBack, 4000);

    });
</script>