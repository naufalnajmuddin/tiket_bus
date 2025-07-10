<?php
require_once '../config/database.php';

class BusController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get all cities
    public function getAllCities() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM kota ORDER BY nama");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Search bus schedules
    public function searchBusSchedules($kotaAsal, $kotaTujuan, $tanggal) {
        try {
            // First, ensure schedules exist for the requested date
            $this->ensureSchedulesExist($kotaAsal, $kotaTujuan, $tanggal);
            
            $sql = "SELECT 
                        j.id as jadwal_id,
                        j.tanggal_berangkat,
                        j.jam_berangkat,
                        j.jam_tiba,
                        j.harga,
                        j.kursi_tersedia,
                        j.status as jadwal_status,
                        b.id as bus_id,
                        b.nomor_bus,
                        b.jenis_bus,
                        b.kapasitas,
                        b.fasilitas,
                        po.nama as nama_po,
                        po.alamat as po_alamat,
                        po.telepon as po_telepon,
                        k1.nama as kota_asal,
                        k2.nama as kota_tujuan,
                        r.jarak,
                        r.estimasi_waktu
                    FROM jadwal j
                    JOIN bus b ON j.bus_id = b.id
                    JOIN perusahaan_otobus po ON b.perusahaan_id = po.id
                    JOIN rute r ON j.rute_id = r.id
                    JOIN kota k1 ON r.kota_asal_id = k1.id
                    JOIN kota k2 ON r.kota_tujuan_id = k2.id
                    WHERE k1.nama = :kota_asal 
                    AND k2.nama = :kota_tujuan 
                    AND j.tanggal_berangkat = :tanggal
                    AND j.status = 'tersedia'
                    ORDER BY j.jam_berangkat";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':kota_asal' => $kotaAsal,
                ':kota_tujuan' => $kotaTujuan,
                ':tanggal' => $tanggal
            ]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Ensure schedules exist for the requested date
    private function ensureSchedulesExist($kotaAsal, $kotaTujuan, $tanggal) {
        try {
            // Check if schedules already exist for this route and date
            $sql = "SELECT COUNT(*) FROM jadwal j
                    JOIN rute r ON j.rute_id = r.id
                    JOIN kota k1 ON r.kota_asal_id = k1.id
                    JOIN kota k2 ON r.kota_tujuan_id = k2.id
                    WHERE k1.nama = :kota_asal 
                    AND k2.nama = :kota_tujuan 
                    AND j.tanggal_berangkat = :tanggal";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':kota_asal' => $kotaAsal,
                ':kota_tujuan' => $kotaTujuan,
                ':tanggal' => $tanggal
            ]);
            
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                // Generate schedules for this route and date
                $this->generateSchedulesForRoute($kotaAsal, $kotaTujuan, $tanggal);
            }
        } catch (PDOException $e) {
            // Handle error silently
        }
    }
    
    // Generate schedules for a specific route and date
    private function generateSchedulesForRoute($kotaAsal, $kotaTujuan, $tanggal) {
        try {
            // Get route ID
            $sql = "SELECT r.id, r.estimasi_waktu 
                    FROM rute r
                    JOIN kota k1 ON r.kota_asal_id = k1.id
                    JOIN kota k2 ON r.kota_tujuan_id = k2.id
                    WHERE k1.nama = :kota_asal AND k2.nama = :kota_tujuan AND r.status = 'aktif'";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':kota_asal' => $kotaAsal,
                ':kota_tujuan' => $kotaTujuan
            ]);
            
            $route = $stmt->fetch();
            
            if (!$route) {
                return false;
            }
            
            // Get template schedules for this route
            $sql = "SELECT * FROM template_jadwal 
                    WHERE rute_id = :rute_id AND status = 'aktif'
                    ORDER BY jam_berangkat";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':rute_id' => $route['id']]);
            $templates = $stmt->fetchAll();
            
            foreach ($templates as $template) {
                // Check if schedule already exists
                if (!$this->scheduleExists($route['id'], $tanggal, $template['jam_berangkat'])) {
                    $this->createScheduleFromTemplate($template, $route, $tanggal);
                }
            }
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Check if schedule already exists
    private function scheduleExists($routeId, $date, $jamBerangkat) {
        $sql = "SELECT COUNT(*) FROM jadwal 
                WHERE rute_id = :route_id AND tanggal_berangkat = :date AND jam_berangkat = :jam_berangkat";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':route_id' => $routeId,
            ':date' => $date,
            ':jam_berangkat' => $jamBerangkat
        ]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    // Create schedule from template
    private function createScheduleFromTemplate($template, $route, $date) {
        // Get available buses for this company
        $sql = "SELECT * FROM bus WHERE perusahaan_id = :perusahaan_id AND status = 'aktif'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':perusahaan_id' => $template['perusahaan_id']]);
        $buses = $stmt->fetchAll();
        
        if (empty($buses)) {
            return false;
        }
        
        // Select a bus (round-robin selection)
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
    
    // Select a bus for the schedule (round-robin selection)
    private function selectBus($buses, $routeId, $date) {
        // Simple round-robin selection based on route and date
        $index = (strtotime($date) + $routeId) % count($buses);
        return $buses[$index] ?? $buses[0] ?? null;
    }
    
    // Calculate arrival time based on departure time and estimated duration
    private function calculateArrivalTime($jamBerangkat, $estimasiWaktu) {
        $departureTime = strtotime($jamBerangkat);
        $arrivalTime = $departureTime + ($estimasiWaktu * 60); // Convert minutes to seconds
        
        // Handle next day arrival
        if (date('H:i:s', $arrivalTime) < $jamBerangkat) {
            $arrivalTime += 86400; // Add 24 hours
        }
        
        return date('H:i:s', $arrivalTime);
    }
    
    // Create seats for a schedule
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
    
    // Get bus schedule by ID
    public function getBusScheduleById($jadwalId) {
        try {
            $sql = "SELECT 
                        j.id as jadwal_id,
                        j.tanggal_berangkat,
                        j.jam_berangkat,
                        j.jam_tiba,
                        j.harga,
                        j.kursi_tersedia,
                        j.status as jadwal_status,
                        b.id as bus_id,
                        b.nomor_bus,
                        b.jenis_bus,
                        b.kapasitas,
                        b.fasilitas,
                        po.nama as nama_po,
                        po.alamat as po_alamat,
                        po.telepon as po_telepon,
                        k1.nama as kota_asal,
                        k2.nama as kota_tujuan,
                        r.jarak,
                        r.estimasi_waktu
                    FROM jadwal j
                    JOIN bus b ON j.bus_id = b.id
                    JOIN perusahaan_otobus po ON b.perusahaan_id = po.id
                    JOIN rute r ON j.rute_id = r.id
                    JOIN kota k1 ON r.kota_asal_id = k1.id
                    JOIN kota k2 ON r.kota_tujuan_id = k2.id
                    WHERE j.id = :jadwal_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':jadwal_id' => $jadwalId]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    // Get available seats for a schedule
    public function getAvailableSeats($jadwalId) {
        try {
            $sql = "SELECT id, nomor_kursi, status 
                    FROM kursi 
                    WHERE jadwal_id = :jadwal_id 
                    ORDER BY nomor_kursi";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':jadwal_id' => $jadwalId]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Check seat availability
    public function checkSeatAvailability($jadwalId, $nomorKursi) {
        try {
            $sql = "SELECT status FROM kursi 
                    WHERE jadwal_id = :jadwal_id AND nomor_kursi = :nomor_kursi";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':jadwal_id' => $jadwalId,
                ':nomor_kursi' => $nomorKursi
            ]);
            
            $result = $stmt->fetch();
            return $result ? $result['status'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    // Reserve seats
    public function reserveSeats($jadwalId, $nomorKursi) {
        try {
            $sql = "UPDATE kursi 
                    SET status = 'terpesan' 
                    WHERE jadwal_id = :jadwal_id AND nomor_kursi = :nomor_kursi AND status = 'tersedia'";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':jadwal_id' => $jadwalId,
                ':nomor_kursi' => $nomorKursi
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                // Update available seats count
                $this->updateAvailableSeatsCount($jadwalId);
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Update available seats count
    private function updateAvailableSeatsCount($jadwalId) {
        try {
            $sql = "UPDATE jadwal j 
                    SET kursi_tersedia = (
                        SELECT COUNT(*) 
                        FROM kursi k 
                        WHERE k.jadwal_id = j.id AND k.status = 'tersedia'
                    )
                    WHERE j.id = :jadwal_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':jadwal_id' => $jadwalId]);
        } catch (PDOException $e) {
            // Handle error silently
        }
    }
    
    // Get facilities as array
    public function getFacilitiesArray($facilitiesString) {
        if (empty($facilitiesString)) {
            return [];
        }
        return array_map('trim', explode(',', $facilitiesString));
    }
    
    // Calculate duration in hours and minutes
    public function calculateDuration($estimasiWaktu) {
        $hours = floor($estimasiWaktu / 60);
        $minutes = $estimasiWaktu % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } else {
            return "{$minutes} menit";
        }
    }
}
?> 