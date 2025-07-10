<?php
/**
 * Script untuk setup database dengan template jadwal
 * Jalankan script ini setelah setup database utama
 */

require_once 'config/database.php';

try {
    echo "Setting up template schedule database...\n";
    
    // Create template_jadwal table
    $sql = "CREATE TABLE IF NOT EXISTS template_jadwal (
        id INT PRIMARY KEY AUTO_INCREMENT,
        rute_id INT NOT NULL,
        perusahaan_id INT NOT NULL,
        jam_berangkat TIME NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        hari_operasi SET('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu') DEFAULT 'senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (rute_id) REFERENCES rute(id),
        FOREIGN KEY (perusahaan_id) REFERENCES perusahaan_otobus(id)
    )";
    
    $pdo->exec($sql);
    echo "✓ Template jadwal table created\n";
    
    // Check if template data already exists
    $count = $pdo->query("SELECT COUNT(*) FROM template_jadwal")->fetchColumn();
    
    if ($count == 0) {
        echo "Inserting sample template schedules...\n";
        
        // Insert sample template schedules
        $templates = [
            // Jakarta - Bandung templates (Sinar Jaya)
            [1, 1, '08:00:00', 150000],
            [1, 1, '10:30:00', 180000],
            [1, 1, '12:00:00', 120000],
            [1, 1, '14:30:00', 140000],
            [1, 1, '16:00:00', 200000],
            
            // Jakarta - Bandung templates (Primajasa)
            [1, 2, '09:00:00', 160000],
            [1, 2, '11:30:00', 170000],
            [1, 2, '13:00:00', 130000],
            [1, 2, '15:30:00', 150000],
            [1, 2, '17:00:00', 190000],
            
            // Jakarta - Surabaya templates (Sinar Jaya)
            [2, 1, '19:00:00', 250000],
            [2, 1, '20:00:00', 300000],
            [2, 1, '21:00:00', 280000],
            
            // Jakarta - Surabaya templates (Primajasa)
            [2, 2, '18:30:00', 260000],
            [2, 2, '19:30:00', 290000],
            [2, 2, '20:30:00', 270000],
            
            // Jakarta - Yogyakarta templates (Sinar Jaya)
            [3, 1, '21:00:00', 200000],
            [3, 1, '22:00:00', 250000],
            [3, 1, '23:00:00', 220000],
            
            // Jakarta - Yogyakarta templates (Lorena)
            [3, 3, '20:30:00', 210000],
            [3, 3, '21:30:00', 240000],
            [3, 3, '22:30:00', 230000],
            
            // Jakarta - Semarang templates (Kramat Jati)
            [4, 4, '20:00:00', 180000],
            [4, 4, '21:00:00', 220000],
            [4, 4, '22:00:00', 200000],
            
            // Bandung - Jakarta templates (Pahala Kencana)
            [5, 5, '08:00:00', 150000],
            [5, 5, '10:30:00', 180000],
            [5, 5, '12:00:00', 120000],
            [5, 5, '14:30:00', 140000],
            [5, 5, '16:00:00', 200000],
            
            // Bandung - Surabaya templates (Pahala Kencana)
            [6, 5, '19:00:00', 220000],
            [6, 5, '20:00:00', 260000],
            [6, 5, '21:00:00', 240000],
            
            // Surabaya - Jakarta templates (Sinar Jaya)
            [7, 1, '19:00:00', 250000],
            [7, 1, '20:00:00', 300000],
            [7, 1, '21:00:00', 280000],
            
            // Yogyakarta - Jakarta templates (Sinar Jaya)
            [8, 1, '21:00:00', 200000],
            [8, 1, '22:00:00', 250000],
            [8, 1, '23:00:00', 220000],
            
            // Semarang - Jakarta templates (Kramat Jati)
            [9, 4, '20:00:00', 180000],
            [9, 4, '21:00:00', 220000],
            [9, 4, '22:00:00', 200000]
        ];
        
        $sql = "INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        foreach ($templates as $template) {
            $stmt->execute($template);
        }
        
        echo "✓ Sample template schedules inserted\n";
    } else {
        echo "✓ Template schedules already exist\n";
    }
    
    // Generate initial schedules for next 7 days
    echo "Generating initial schedules...\n";
    
    require_once 'controller/ScheduleGenerator.php';
    $scheduleGenerator = new ScheduleGenerator($pdo);
    
    $success = $scheduleGenerator->generateSchedulesForNextDays(7);
    
    if ($success) {
        echo "✓ Initial schedules generated for next 7 days\n";
    } else {
        echo "⚠ Some schedules failed to generate\n";
    }
    
    // Get statistics
    $stats = $scheduleGenerator->getScheduleStats();
    echo "Schedule statistics:\n";
    echo "- Total schedules: {$stats['total_schedules']}\n";
    echo "- Today's schedules: {$stats['today_schedules']}\n";
    echo "- Future schedules: {$stats['future_schedules']}\n";
    
    echo "\n✅ Template database setup completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Set up a cron job to run 'cron_generate_schedules.php' daily\n";
    echo "2. Access admin panel to manage template schedules\n";
    echo "3. Test the search functionality to see automatic schedule generation\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?> 