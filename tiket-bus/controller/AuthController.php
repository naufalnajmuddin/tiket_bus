<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'].'/tiket-bus/config/database.php';

class AuthController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function login($username, $password, $loginType) {
        try {
            // Check if user exists by username or email
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'aktif'");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Username/email atau password salah'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Username/email atau password salah'];
            }
            
            // Check if user is trying to login as admin but doesn't have admin role
            if ($loginType === 'admin' && $user['role'] !== 'admin') {
                return ['success' => false, 'message' => 'Anda tidak memiliki akses admin'];
            }
            
            // Check if admin is trying to login as user
            if ($loginType === 'user' && $user['role'] === 'admin') {
                return ['success' => false, 'message' => 'Admin harus login sebagai admin'];
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            

            
            return ['success' => true, 'role' => $user['role']];
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }
    
    public function register($data) {
        try {
            // Validate required fields
            $required_fields = ['nama', 'email', 'username', 'password', 'confirm_password', 'no_telepon'];
            foreach ($required_fields as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field $field harus diisi"];
                }
            }
            
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Format email tidak valid'];
            }
            
            // Validate password length
            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Password minimal 6 karakter'];
            }
            
            // Validate password confirmation
            if ($data['password'] !== $data['confirm_password']) {
                return ['success' => false, 'message' => 'Konfirmasi password tidak cocok'];
            }
            
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email sudah terdaftar'];
            }
            
            // Check if username already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Username sudah digunakan'];
            }
            
            // Hash password
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (nama, email, username, password, no_telepon, alamat, role) 
                VALUES (?, ?, ?, ?, ?, ?, 'user')
            ");
            
            $stmt->execute([
                $data['nama'],
                $data['email'],
                $data['username'],
                $hashed_password,
                $data['no_telepon'],
                $data['alamat'] ?? ''
            ]);
            
            return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login'];
            
        } catch (PDOException $e) {
            error_log("Register error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan sistem'];
        }
    }
    
    public function logout() {
        // Destroy session
        session_destroy();
        
        // Clear cookies
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        return ['success' => true, 'message' => 'Logout berhasil'];
    }
    

    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ../view/login.php');
            exit();
        }
    }
    
    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            header('Location: ../view/login.php');
            exit();
        }
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController($pdo);
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                $result = $auth->login(
                    $_POST['username'] ?? '',
                    $_POST['password'] ?? '',
                    $_POST['loginType'] ?? 'user'
                );
                
                if ($result['success']) {
                    if ($result['role'] === 'admin') {
                        header('Location: ../view/admin/dashboard.php');
                    } else {
                        header('Location: ../view/user/dashboard.php');
                    }
                    exit();
                } else {
                    $_SESSION['error'] = $result['message'];
                    header('Location: ../view/login.php');
                    exit();
                }
                break;
                
            case 'register':
                $result = $auth->register($_POST);
                
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                    header('Location: ../view/login.php');
                } else {
                    $_SESSION['error'] = $result['message'];
                    header('Location: ../view/register.php');
                }
                exit();
                break;
                
            case 'logout':
                $result = $auth->logout();
                $_SESSION['success'] = $result['message'];
                header('Location: ../view/login.php');
                exit();
                break;
        }
    }
}
?> 