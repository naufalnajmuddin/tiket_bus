-- Database: tiket_bus
-- Description: Database untuk sistem pemesanan tiket bus Go.Ket

-- Create database
CREATE DATABASE IF NOT EXISTS tiket_bus;
USE tiket_bus;

-- Table: kota (cities)
CREATE TABLE kota (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    provinsi VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: perusahaan_otobus (bus companies)
CREATE TABLE perusahaan_otobus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: bus
CREATE TABLE bus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    perusahaan_id INT,
    nomor_bus VARCHAR(20) NOT NULL,
    jenis_bus ENUM('Economy', 'Executive', 'Super Executive', 'VIP') NOT NULL,
    kapasitas INT NOT NULL,
    fasilitas TEXT,
    status ENUM('aktif', 'maintenance', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (perusahaan_id) REFERENCES perusahaan_otobus(id)
);

-- Table: rute
CREATE TABLE rute (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kota_asal_id INT,
    kota_tujuan_id INT,
    jarak DECIMAL(10,2),
    estimasi_waktu INT, -- dalam menit
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kota_asal_id) REFERENCES kota(id),
    FOREIGN KEY (kota_tujuan_id) REFERENCES kota(id)
);

-- Table: jadwal
CREATE TABLE jadwal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bus_id INT,
    rute_id INT,
    tanggal_berangkat DATE NOT NULL,
    jam_berangkat TIME NOT NULL,
    jam_tiba TIME NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    kursi_tersedia INT NOT NULL,
    status ENUM('tersedia', 'penuh', 'dibatalkan') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (bus_id) REFERENCES bus(id),
    FOREIGN KEY (rute_id) REFERENCES rute(id)
);

-- Table: kursi
CREATE TABLE kursi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    jadwal_id INT,
    nomor_kursi INT NOT NULL,
    status ENUM('tersedia', 'terpesan', 'terisi') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jadwal_id) REFERENCES jadwal(id)
);

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_telepon VARCHAR(20),
    alamat TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- Table: pemesanan
CREATE TABLE pemesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    jadwal_id INT,
    kode_pemesanan VARCHAR(20) UNIQUE NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'dibayar', 'dibatalkan', 'selesai') DEFAULT 'pending',
    metode_pembayaran VARCHAR(50),
    tanggal_pemesanan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (jadwal_id) REFERENCES jadwal(id)
);

-- Table: detail_pemesanan
CREATE TABLE detail_pemesanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pemesanan_id INT,
    kursi_id INT,
    nama_penumpang VARCHAR(100) NOT NULL,
    no_ktp VARCHAR(16) NOT NULL,
    no_telepon VARCHAR(20),
    email VARCHAR(100),
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    usia INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pemesanan_id) REFERENCES pemesanan(id),
    FOREIGN KEY (kursi_id) REFERENCES kursi(id)
);

-- Insert sample data

-- Insert kota
INSERT INTO kota (nama, provinsi) VALUES
('Jakarta', 'DKI Jakarta'),
('Bandung', 'Jawa Barat'),
('Surabaya', 'Jawa Timur'),
('Yogyakarta', 'DI Yogyakarta'),
('Semarang', 'Jawa Tengah'),
('Malang', 'Jawa Timur'),
('Bali', 'Bali'),
('Medan', 'Sumatera Utara'),
('Palembang', 'Sumatera Selatan'),
('Makassar', 'Sulawesi Selatan');

-- Insert perusahaan otobus
INSERT INTO perusahaan_otobus (nama, alamat, telepon, email) VALUES
('Sinar Jaya', 'Jl. Sudirman No. 123, Jakarta', '021-12345678', 'info@sinarjaya.com'),
('Primajasa', 'Jl. Gatot Subroto No. 456, Jakarta', '021-87654321', 'info@primajasa.com'),
('Lorena', 'Jl. Thamrin No. 789, Jakarta', '021-11223344', 'info@lorena.com'),
('Kramat Jati', 'Jl. Hayam Wuruk No. 321, Jakarta', '021-44332211', 'info@kramatjati.com'),
('Pahala Kencana', 'Jl. Asia Afrika No. 654, Bandung', '022-12345678', 'info@pahalakencana.com');

-- Insert bus
INSERT INTO bus (perusahaan_id, nomor_bus, jenis_bus, kapasitas, fasilitas) VALUES
(1, 'SJ-001', 'Executive', 45, 'AC, Toilet, Charger, WiFi, Snack'),
(1, 'SJ-002', 'Super Executive', 40, 'AC, Toilet, Charger, WiFi, Snack, Reclining Seat'),
(2, 'PJ-001', 'Super Executive', 40, 'AC, Toilet, Charger, WiFi, Snack, Reclining Seat'),
(3, 'LR-001', 'Economy', 50, 'AC, Toilet'),
(4, 'KK-001', 'Executive', 45, 'AC, Toilet, Charger'),
(5, 'PK-001', 'Super Executive', 35, 'AC, Toilet, Charger, WiFi, Snack, Reclining Seat, Entertainment');

-- Insert rute
INSERT INTO rute (kota_asal_id, kota_tujuan_id, jarak, estimasi_waktu) VALUES
(1, 2, 150.5, 360), -- Jakarta - Bandung
(1, 3, 750.0, 900), -- Jakarta - Surabaya
(1, 4, 500.0, 600), -- Jakarta - Yogyakarta
(1, 5, 450.0, 540), -- Jakarta - Semarang
(2, 1, 150.5, 360), -- Bandung - Jakarta
(2, 3, 600.0, 720), -- Bandung - Surabaya
(3, 1, 750.0, 900), -- Surabaya - Jakarta
(4, 1, 500.0, 600), -- Yogyakarta - Jakarta
(5, 1, 450.0, 540); -- Semarang - Jakarta

-- Insert jadwal untuk hari ini dan beberapa hari ke depan
INSERT INTO jadwal (bus_id, rute_id, tanggal_berangkat, jam_berangkat, jam_tiba, harga, kursi_tersedia) VALUES
-- Jakarta - Bandung
(1, 1, CURDATE(), '08:00:00', '14:00:00', 150000, 12),
(2, 1, CURDATE(), '10:30:00', '16:30:00', 180000, 8),
(3, 1, CURDATE(), '12:00:00', '18:00:00', 120000, 25),
(4, 1, CURDATE(), '14:30:00', '20:30:00', 140000, 15),
(5, 1, CURDATE(), '16:00:00', '22:00:00', 200000, 5),

-- Jakarta - Bandung (besok)
(1, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '08:00:00', '14:00:00', 150000, 20),
(2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', '16:30:00', 180000, 15),
(3, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '12:00:00', '18:00:00', 120000, 30),
(4, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:30:00', '20:30:00', 140000, 25),
(5, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '16:00:00', '22:00:00', 200000, 10),

-- Jakarta - Surabaya
(1, 2, CURDATE(), '19:00:00', '10:00:00', 250000, 20),
(2, 2, CURDATE(), '20:00:00', '11:00:00', 300000, 15),

-- Jakarta - Yogyakarta
(1, 3, CURDATE(), '21:00:00', '07:00:00', 200000, 18),
(2, 3, CURDATE(), '22:00:00', '08:00:00', 250000, 12);

-- Insert sample user
INSERT INTO users (nama, email, username, password, no_telepon, role) VALUES
('Admin Go.Ket', 'admin@goket.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin'),
('John Doe', 'john@example.com', 'johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567891', 'user'),
('Jane Smith', 'jane@example.com', 'janesmith', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567892', 'user');

-- Insert kursi untuk setiap jadwal
-- Function untuk generate kursi
DELIMITER //
CREATE PROCEDURE GenerateKursi(IN jadwal_id INT, IN kapasitas INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= kapasitas DO
        INSERT INTO kursi (jadwal_id, nomor_kursi, status) VALUES (jadwal_id, i, 'tersedia');
        SET i = i + 1;
    END WHILE;
END //
DELIMITER ;

-- Generate kursi untuk semua jadwal
CALL GenerateKursi(1, 45); -- SJ-001 Executive
CALL GenerateKursi(2, 40); -- SJ-002 Super Executive
CALL GenerateKursi(3, 40); -- PJ-001 Super Executive
CALL GenerateKursi(4, 50); -- LR-001 Economy
CALL GenerateKursi(5, 45); -- KK-001 Executive
CALL GenerateKursi(6, 35); -- PK-001 Super Executive
CALL GenerateKursi(7, 45); -- SJ-001 Executive (besok)
CALL GenerateKursi(8, 40); -- SJ-002 Super Executive (besok)
CALL GenerateKursi(9, 50); -- LR-001 Economy (besok)
CALL GenerateKursi(10, 45); -- KK-001 Executive (besok)
CALL GenerateKursi(11, 35); -- PK-001 Super Executive (besok)
CALL GenerateKursi(12, 45); -- SJ-001 Executive (Surabaya)
CALL GenerateKursi(13, 40); -- SJ-002 Super Executive (Surabaya)
CALL GenerateKursi(14, 45); -- SJ-001 Executive (Yogyakarta)
CALL GenerateKursi(15, 40); -- SJ-002 Super Executive (Yogyakarta)

-- Drop procedure after use
DROP PROCEDURE GenerateKursi; 