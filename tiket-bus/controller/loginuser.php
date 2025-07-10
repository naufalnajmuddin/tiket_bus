<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/tiket-bus/config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['username'];
    $password = $_POST['password'];
    $loginType = $_POST['loginType'] ?? 'user';

    // Cek user berdasarkan username atau email dan role
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = ? LIMIT 1");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail, $loginType]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['nama'] = $user['nama'];

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            header('Location: /tiket-bus/view/admin/dashboard.php');
        } else {
            header('Location: /tiket-bus/view/index.php');
        }
        exit();
    } else {
        $_SESSION['error'] = 'Username/email, password, atau role salah';
        header('Location: /tiket-bus/view/login.php');
        exit();
    }
} else {
    header('Location: /tiket-bus/view/login.php');
    exit();
}
?>
