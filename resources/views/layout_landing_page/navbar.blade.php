<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
    <a href="#" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h1 class="m-0 text-primary"><i class="far fa-hospital me-3"></i>Kalender Bali</h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="{{ route('dashboard') }}" style="font-family: Bogota;" class="nav-item nav-link active">Home</a>
            <a href="#" style="font-family: Bogota;" class="nav-item nav-link">Tentang</a>
            <!-- <a href="#" style="font-family: Bogota;" class="nav-item nav-link">Service</a> -->
            <div class="nav-item dropdown">
                <a href="#" style="font-family: Bogota;" class="nav-link" data-bs-toggle="dropdown">Docs </a>
                <div class="dropdown-menu rounded-0 rounded-bottom m-0">
                    <a href="{{ route('docs_kalender') }}" style="font-family: Bogota;" class="dropdown-item">Dewasa Ayu</a>
                    <a href="#" style="font-family: Bogota;" class="dropdown-item">Hari Raya</a>
                    <a href="#" style="font-family: Bogota;" class="dropdown-item">Otonan</a>
                    <a href="#" style="font-family: Bogota;" class="dropdown-item">Pura</a>
                    <!-- <a href="#" style="font-family: Bogota;" class="dropdown-item">404 Page</a> -->
                </div>
            </div>
            <a href="#" style="font-family: Bogota;" class="nav-item nav-link">Kontak</a>
        </div>
        <a href="" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block" style="color: white;">Kalender Bali API<i class="fa fa-arrow-right ms-3"></i></a>
    </div>
</nav>