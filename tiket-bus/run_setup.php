<?php
/**
 * Script untuk menjalankan setup lengkap sistem jadwal otomatis
 * Jalankan script ini untuk setup awal sistem
 */

echo "🚌 Setup Sistem Jadwal Bus Otomatis\n";
echo "=====================================\n\n";

// Check if required files exist
$requiredFiles = [
    'config/database.php',
    'setup_database.php',
    'setup_template_database.php'
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        echo "❌ Error: File $file tidak ditemukan\n";
        exit(1);
    }
}

// Step 1: Setup database utama
echo "📋 Step 1: Setup Database Utama\n";
echo "--------------------------------\n";

try {
    include 'setup_database.php';
    echo "✅ Database utama berhasil disetup\n\n";
} catch (Exception $e) {
    echo "❌ Error setup database utama: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Setup template database
echo "📋 Step 2: Setup Template Database\n";
echo "----------------------------------\n";

try {
    include 'setup_template_database.php';
    echo "✅ Template database berhasil disetup\n\n";
} catch (Exception $e) {
    echo "❌ Error setup template database: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 3: Create logs directory
echo "📋 Step 3: Setup Logs Directory\n";
echo "--------------------------------\n";

$logsDir = 'logs';
if (!is_dir($logsDir)) {
    if (mkdir($logsDir, 0755, true)) {
        echo "✅ Directory logs berhasil dibuat\n";
    } else {
        echo "⚠️ Warning: Gagal membuat directory logs\n";
    }
} else {
    echo "✅ Directory logs sudah ada\n";
}

// Step 4: Test the system
echo "📋 Step 4: Test Sistem\n";
echo "----------------------\n";

try {
    require_once 'config/database.php';
    require_once 'controller/ScheduleGenerator.php';
    
    $generator = new ScheduleGenerator($pdo);
    
    // Test generate schedules for today
    $today = date('Y-m-d');
    $result = $generator->generateSchedulesForDate($today);
    
    if ($result) {
        echo "✅ Test generate jadwal berhasil\n";
    } else {
        echo "⚠️ Warning: Test generate jadwal gagal\n";
    }
    
    // Get statistics
    $stats = $generator->getScheduleStats();
    echo "📊 Statistik jadwal:\n";
    echo "   - Total jadwal: {$stats['total_schedules']}\n";
    echo "   - Jadwal hari ini: {$stats['today_schedules']}\n";
    echo "   - Jadwal masa depan: {$stats['future_schedules']}\n";
    
} catch (Exception $e) {
    echo "❌ Error test sistem: " . $e->getMessage() . "\n";
}

echo "\n🎉 Setup Sistem Selesai!\n";
echo "========================\n\n";

echo "📝 Langkah Selanjutnya:\n";
echo "1. Setup cron job untuk maintenance harian:\n";
echo "   crontab -e\n";
echo "   # Tambahkan: 0 2 * * * /usr/bin/php " . realpath('cron_generate_schedules.php') . "\n\n";

echo "2. Akses admin panel untuk mengelola template:\n";
echo "   http://localhost/tiket-bus/view/admin/template-jadwal.php\n\n";

echo "3. Test pencarian tiket:\n";
echo "   http://localhost/tiket-bus/view/cari-tiket.php\n\n";

echo "4. Monitor log file:\n";
echo "   tail -f logs/schedule_generation.log\n\n";

echo "📚 Dokumentasi lengkap: README_AUTO_SCHEDULE.md\n";
echo "🔧 File penting:\n";
echo "   - controller/ScheduleGenerator.php\n";
echo "   - controller/BusController.php (dimodifikasi)\n";
echo "   - view/admin/template-jadwal.php\n";
echo "   - cron_generate_schedules.php\n\n";

echo "✅ Sistem siap digunakan!\n";
?> 