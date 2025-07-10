<?php
/**
 * Cron job script untuk menghasilkan jadwal bus otomatis
 * Script ini dapat dijalankan setiap hari untuk memastikan jadwal tersedia
 */

require_once 'config/database.php';
require_once 'controller/ScheduleGenerator.php';

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Initialize schedule generator
$scheduleGenerator = new ScheduleGenerator($pdo);

// Log file
$logFile = 'logs/schedule_generation.log';
$logDir = dirname($logFile);

// Create log directory if it doesn't exist
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    writeLog("Starting schedule generation process...");
    
    // Generate schedules for next 7 days
    $success = $scheduleGenerator->generateSchedulesForNextDays(7);
    
    if ($success) {
        writeLog("Successfully generated schedules for next 7 days");
    } else {
        writeLog("Error: Failed to generate some schedules");
    }
    
    // Clean old schedules (older than 7 days)
    $cleanResult = $scheduleGenerator->cleanOldSchedules();
    if ($cleanResult) {
        writeLog("Successfully cleaned old schedules");
    } else {
        writeLog("Warning: Failed to clean old schedules");
    }
    
    // Get statistics
    $stats = $scheduleGenerator->getScheduleStats();
    writeLog("Schedule statistics - Total: {$stats['total_schedules']}, Today: {$stats['today_schedules']}, Future: {$stats['future_schedules']}");
    
    writeLog("Schedule generation process completed successfully");
    
} catch (Exception $e) {
    writeLog("Error: " . $e->getMessage());
    exit(1);
}

echo "Schedule generation completed. Check log file for details.\n"; 