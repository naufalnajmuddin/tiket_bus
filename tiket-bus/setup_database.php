<?php
// Setup Database untuk Sistem Login Go.Ket
// Jalankan file ini sekali untuk setup database

echo "=== Setup Database Go.Ket ===\n";
echo "Memulai setup database...\n\n";

// Database configuration
$host = 'localhost';
$dbname = 'tiket_bus';
$username = 'root';
$password = '';

try {
    // Connect to MySQL without database
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Koneksi ke MySQL berhasil\n";
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database '$dbname' berhasil dibuat/ditemukan\n";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    echo "✓ Koneksi ke database '$dbname' berhasil\n\n";
    
    // Read and execute SQL file
    $sql_file = 'database/tiket_bus.sql';
    if (file_exists($sql_file)) {
        $sql_content = file_get_contents($sql_file);
        
        // Split SQL by semicolon and execute each statement
        $statements = explode(';', $sql_content);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^(--|\/\*|#)/', $statement)) {
                // Skip stored procedure creation for now
                if (strpos($statement, 'DELIMITER') !== false || 
                    strpos($statement, 'CREATE PROCEDURE') !== false ||
                    strpos($statement, 'CALL GenerateKursi') !== false ||
                    strpos($statement, 'DROP PROCEDURE') !== false) {
                    continue;
                }
                
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Skip if table already exists or duplicate entry
                    if (strpos($e->getMessage(), 'already exists') === false && 
                        strpos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "⚠ Warning: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
        
        echo "✓ Database schema berhasil dibuat\n";
    } else {
        echo "✗ File SQL tidak ditemukan: $sql_file\n";
        exit(1);
    }
    
    // Create default admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin_exists = $stmt->fetch()['count'] > 0;
    
    if (!$admin_exists) {
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (nama, email, username, password, no_telepon, role) 
            VALUES ('Admin Go.Ket', 'admin@goket.com', 'admin', ?, '081234567890', 'admin')
        ");
        $stmt->execute([$hashed_password]);
        echo "✓ User admin default berhasil dibuat\n";
        echo "  Username: admin\n";
        echo "  Password: admin123\n";
    } else {
        echo "✓ User admin sudah ada\n";
    }
    
    // Create default user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE username = 'user'");
    $stmt->execute();
    $user_exists = $stmt->fetch()['count'] > 0;
    
    if (!$user_exists) {
        $hashed_password = password_hash('user123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (nama, email, username, password, no_telepon, role) 
            VALUES ('User Demo', 'user@goket.com', 'user', ?, '081234567891', 'user')
        ");
        $stmt->execute([$hashed_password]);
        echo "✓ User demo berhasil dibuat\n";
        echo "  Username: user\n";
        echo "  Password: user123\n";
    } else {
        echo "✓ User demo sudah ada\n";
    }
    
    echo "\n=== Setup Selesai ===\n";
    echo "Database berhasil disetup!\n";
    echo "Silakan akses aplikasi melalui browser.\n";
    echo "URL: http://localhost/tiket-bus/view/\n\n";
    
    echo "=== Kredensial Login ===\n";
    echo "Admin:\n";
    echo "  Username: admin\n";
    echo "  Password: admin123\n\n";
    echo "User:\n";
    echo "  Username: user\n";
    echo "  Password: user123\n\n";
    
} catch(PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?> 