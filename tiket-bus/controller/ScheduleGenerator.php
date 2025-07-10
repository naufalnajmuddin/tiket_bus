<?php
require_once '../config/database.php';

class ScheduleGenerator {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Generate schedules for a specific date
     */
    public function generateSchedulesForDate($date) {
        try {
            // Get all active routes
            $routes = $this->getActiveRoutes();
            
            foreach ($routes as $route) {
                $this->generateSchedulesForRoute($route, $date);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error generating schedules: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate schedules for a route on a specific date
     */
    private function generateSchedulesForRoute($route, $date) {
        // Get template schedules for this route
        $templateSchedules = $this->getTemplateSchedules($route['id']);
        
        foreach ($templateSchedules as $template) {
            // Check if schedule already exists for this date
            if (!$this->scheduleExists($route['id'], $date, $template['jam_berangkat'])) {
                $this->createScheduleFromTemplate($template, $route, $date);
            }
        }
    }
    
    /**
     * Get all active routes
     */
    private function getActiveRoutes() {
        $sql = "SELECT r.*, k1.nama as kota_asal, k2.nama as kota_tujuan 
                FROM rute r 
                JOIN kota k1 ON r.kota_asal_id = k1.id 
                JOIN kota k2 ON r.kota_tujuan_id = k2.id 
                WHERE r.status = 'aktif'";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get template schedules for a route
     */
    private function getTemplateSchedules($routeId) {
        $sql = "SELECT * FROM template_jadwal WHERE rute_id = :route_id AND status = 'aktif' ORDER BY jam_berangkat";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':route_id' => $routeId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if schedule already exists
     */
    private function scheduleExists($routeId, $date, $jamBerangkat) {
        $sql = "SELECT COUNT(*) FROM jadwal WHERE rute_id = :route_id AND tanggal_berangkat = :date AND jam_berangkat = :jam_berangkat";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':route_id' => $routeId,
            ':date' => $date,
            ':jam_berangkat' => $jamBerangkat
        ]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Create schedule from template
     */
    private function createScheduleFromTemplate($template, $route, $date) {
        // Get available buses for this route
        $buses = $this->getAvailableBuses($template['perusahaan_id']);
        
        if (empty($buses)) {
            return false;
        }
        
        // Select a bus (round-robin or random selection)
        $selectedBus = $this->selectBus($buses, $route['id'], $date);
        
        if (!$selectedBus) {
            return false;
        }
        
        // Calculate arrival time
        $jamTiba = $this->calculateArrivalTime($template['jam_berangkat'], $route['estimasi_waktu']);
        
        // Create the schedule
        $sql = "INSERT INTO jadwal (bus_id, rute_id, tanggal_berangkat, jam_berangkat, jam_tiba, harga, kursi_tersedia, status) 
                VALUES (:bus_id, :rute_id, :tanggal_berangkat, :jam_berangkat, :jam_tiba, :harga, :kursi_tersedia, 'tersedia')";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            ':bus_id' => $selectedBus['id'],
            ':rute_id' => $route['id'],
            ':tanggal_berangkat' => $date,
            ':jam_berangkat' => $template['jam_berangkat'],
            ':jam_tiba' => $jamTiba,
            ':harga' => $template['harga'],
            ':kursi_tersedia' => $selectedBus['kapasitas']
        ]);
        
        if ($result) {
            $jadwalId = $this->pdo->lastInsertId();
            $this->createSeatsForSchedule($jadwalId, $selectedBus['kapasitas']);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get available buses for a company
     */
    private function getAvailableBuses($perusahaanId) {
        $sql = "SELECT * FROM bus WHERE perusahaan_id = :perusahaan_id AND status = 'aktif'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':perusahaan_id' => $perusahaanId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Select a bus for the schedule (round-robin selection)
     */
    private function selectBus($buses, $routeId, $date) {
        // Simple round-robin selection based on route and date
        $index = (strtotime($date) + $routeId) % count($buses);
        return $buses[$index] ?? $buses[0] ?? null;
    }
    
    /**
     * Calculate arrival time based on departure time and estimated duration
     */
    private function calculateArrivalTime($jamBerangkat, $estimasiWaktu) {
        $departureTime = strtotime($jamBerangkat);
        $arrivalTime = $departureTime + ($estimasiWaktu * 60); // Convert minutes to seconds
        
        // Handle next day arrival
        if (date('H:i:s', $arrivalTime) < $jamBerangkat) {
            $arrivalTime += 86400; // Add 24 hours
        }
        
        return date('H:i:s', $arrivalTime);
    }
    
    /**
     * Create seats for a schedule
     */
    private function createSeatsForSchedule($jadwalId, $kapasitas) {
        for ($i = 1; $i <= $kapasitas; $i++) {
            $sql = "INSERT INTO kursi (jadwal_id, nomor_kursi, status) VALUES (:jadwal_id, :nomor_kursi, 'tersedia')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':jadwal_id' => $jadwalId,
                ':nomor_kursi' => $i
            ]);
        }
    }
    
    /**
     * Generate schedules for next 30 days
     */
    public function generateSchedulesForNextDays($days = 30) {
        $success = true;
        
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            if (!$this->generateSchedulesForDate($date)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Clean old schedules (older than 7 days)
     */
    public function cleanOldSchedules() {
        $cutoffDate = date('Y-m-d', strtotime('-7 days'));
        
        $sql = "DELETE FROM jadwal WHERE tanggal_berangkat < :cutoff_date";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':cutoff_date' => $cutoffDate]);
    }
    
    /**
     * Get schedule statistics
     */
    public function getScheduleStats() {
        $sql = "SELECT 
                    COUNT(*) as total_schedules,
                    COUNT(CASE WHEN tanggal_berangkat = CURDATE() THEN 1 END) as today_schedules,
                    COUNT(CASE WHEN tanggal_berangkat > CURDATE() THEN 1 END) as future_schedules
                FROM jadwal";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch();
    }
} 