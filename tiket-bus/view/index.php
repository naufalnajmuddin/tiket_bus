<?php
ob_start();
?>
<!-- Konten utama halaman di sini, tanpa <html>, <head>, <body>, <footer>, </html> -->
    <!-- jumbotron -->
    <section id="jumbotron" class="jumbotron-fluid background-jumbotron">
        <div class="container-fluid py-5 text-center">
            <p class="p-5"></p>
            <h1 class="display-5">Pesan Tiket Bus Cepat, Mudah, dan Aman!</h1>
            <p class="col-md-8 fs-4"></p>
            <a href="cari-tiket.php" class="btn btn-outline-info">Cari Tiket</a>
        </div>
    </section>

    <!-- layanan -->
    <section id="layanan">
        <div class="row text-center">
            <div class="col-12 p-5">
                <h2 class="display-4">Layanan Kami</h2>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <span class="fa-stack fa-2x">
                    <i class="fas fa-circle fa-stack-2x bg-blue"></i>
                    <i class="fas fa-bolt fa-stack-1x text-white"></i>
                </span>
                <h3 class="mt-4 bg-blue">Pemesanan Online</h3>
                <p>Nikmati kemudahan pesan tiket melalui aplikasi atau website kami.</p>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <span class="fa-stack fa-2x">
                    <i class="fas fa-circle fa-stack-2x bg-blue"></i>
                    <i class="fas fa-ticket fa-stack-1x text-white"></i>
                </span>
                <h3 class="mt-4 bg-blue">Atur Perjalanan</h3>
                <p>Pilih rute perjalanan dan tanggal keberangkatan sesuai keinginan anda.</p>
            </div>
            <div class="col-12 col-sm-6 col-lg-4">
                <span class="fa-stack fa-2x">
                    <i class="fas fa-circle fa-stack-2x bg-blue"></i>
                    <i class="fas fa-bus fa-stack-1x text-white"></i>
                </span>
                <h3 class="mt-4 bg-blue">Banyak Pilihan</h3>
                <p>Lihat daftar bus dari berbagai perusahaan, harga, dan jam keberangkatan sesuai dengan keinginan anda.
                </p>
            </div>
        </div>
    </section>

    <!-- tentang -->
    <section id="tentang" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center">
                    <img src="../assets/img/travel.png" alt="Gambar Globe" class="img-fluid" style="max-width: 400px;">
                </div>
                <div class="col-md-6">
                    <p class="deskripsi"><em><strong>Dengan Go.Ket, Anda tidak hanya memesan tiket, tapi </strong></em>
                        juga
                        mendapatkan pengalaman
                        perjalanan yang lebih tenang dan efisien.
                        Tidak ada lagi stres karena kehabisan tiket, perubahan jadwal, atau sistem pembayaran yang
                        merepotkan.
                        Semua diatur dalam satu platform yang cepat, aman, dan terpercaya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- destinasi -->
    <section id="destinasi" class="container">
        <div class="row text-center">
            <div class="col-12 p-4">
                <h2 class="display-4">Destinasi Favorit</h2>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../assets/img/Gedung_Sate.jpg" alt="">
                    <div class="card-body">
                        <h4 class="card-title">Bandung</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../assets/img/tugu-jogja.jpg" alt="">
                    <div class="card-body">
                        <h4 class="card-title">Yogyakarta</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../assets/img/bali.jpg" alt="">
                    <div class="card-body">
                        <h4 class="card-title">Bali</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3 mt-1">
                <a href="cari-tiket.php" class="btn btn-secondary">Cari Tiket</a>
            </div>
        </div>
    </section>

    <!-- about -->
    <section id="tentang" class="bg-light pb-5">
        <div class="container">
            <div class="col-12 p-4">
                <h2 class="display-4 text-center">Tentang Kami</h2>
            </div>
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3 class="display-5">Go.Ket</h3>
                    <p>Go.Ket hadir sebagai solusi modern untuk perjalanan darat Anda. Kami memahami betapa repotnya
                        memesan tiket bus secara manual,
                        kehabisan kursi, atau salah jadwal. Dengan Go.Ket, semua proses perjalanan jadi lebih praktis,
                        cepat, dan aman.</p>
                    <p>Cukup dengan satu platform, Anda bisa memilih rute, jadwal, harga, dan perusahaan bus sesuai
                        keinginan.
                        Tak hanya memudahkan, kami juga berkomitmen memberikan pengalaman perjalanan yang nyaman dan
                        bebas stres, mulai dari pemesanan hingga Anda tiba di tujuan.</p>
                    <p>Percayakan perjalanan Anda kepada Go.Ket sahabat setia perjalanan darat Anda.</p>
                </div>
                <div class="col-12 col-md-6">
                    <img src="../assets/img/bus.png" alt="go.ket" width="100%">
                </div>
            </div>
        </div>
    </section>
<?php
$content = ob_get_clean();
include 'master.php';
?>