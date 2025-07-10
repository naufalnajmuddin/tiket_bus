<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tiket-bus/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nama = $_POST['nama'];
$username = $_POST['username'];
$email = $_POST['email'];
$no_telepon = $_POST['no_telepon'];
$alamat = $_POST['alamat'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Password dan Konfirmasi Password tidak sama';
    header('Location: /tiket-bus/view/register.php');
    exit();
}

// Cek email sudah terdaftar
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetchColumn() > 0) {
    $_SESSION['error'] = 'Email sudah terdaftar';
    header('Location: /tiket-bus/view/register.php');
    exit();
}

// Cek username sudah terdaftar
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetchColumn() > 0) {
    $_SESSION['error'] = 'Username sudah terdaftar';
    header('Location: /tiket-bus/view/register.php');
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (nama, username, email, no_telepon, alamat, password, role) VALUES (?, ?, ?, ?, ?, ?, 'user')");
$stmt->execute([$nama, $username, $email, $no_telepon, $alamat, $hashed_password]);

$_SESSION['success'] = 'Registrasi berhasil, silakan login';
header('Location: /tiket-bus/view/login.php');
exit();
?>