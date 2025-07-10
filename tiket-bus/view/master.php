<?php
// Cara pakai:
// 1. Di halaman konten, letakkan di paling atas: ob_start();
// 2. Tulis seluruh konten halaman seperti biasa (tanpa <html>, <head>, <body>, <footer>, </html> dll)
// 3. Di akhir file, sebelum include master.php, tambahkan:
//    $content = ob_get_clean();
//    include 'master.php';
//
// master.php akan otomatis menggabungkan header, konten, dan footer.

include 'header.php';
echo isset($content) ? $content : '';
include 'footer.php';
