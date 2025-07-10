-- Table: template_jadwal (schedule templates)
-- This table stores schedule templates that will be used to generate daily schedules

CREATE TABLE template_jadwal (
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
);

-- Insert sample template schedules

-- Jakarta - Bandung templates (Sinar Jaya)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(1, 1, '08:00:00', 150000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 1, '10:30:00', 180000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 1, '12:00:00', 120000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 1, '14:30:00', 140000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 1, '16:00:00', 200000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Jakarta - Bandung templates (Primajasa)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(1, 2, '09:00:00', 160000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 2, '11:30:00', 170000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 2, '13:00:00', 130000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 2, '15:30:00', 150000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(1, 2, '17:00:00', 190000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Jakarta - Surabaya templates (Sinar Jaya)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(2, 1, '19:00:00', 250000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(2, 1, '20:00:00', 300000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(2, 1, '21:00:00', 280000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Jakarta - Surabaya templates (Primajasa)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(2, 2, '18:30:00', 260000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(2, 2, '19:30:00', 290000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(2, 2, '20:30:00', 270000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Jakarta - Yogyakarta templates (Sinar Jaya)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(3, 1, '21:00:00', 200000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(3, 1, '22:00:00', 250000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(3, 1, '23:00:00', 220000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Jakarta - Yogyakarta templates (Lorena)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(3, 3, '20:30:00', 210000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(3, 3, '21:30:00', 240000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(3, 3, '22:30:00', 230000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Jakarta - Semarang templates (Kramat Jati)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(4, 4, '20:00:00', 180000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(4, 4, '21:00:00', 220000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(4, 4, '22:00:00', 200000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Bandung - Jakarta templates (Pahala Kencana)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(5, 5, '08:00:00', 150000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(5, 5, '10:30:00', 180000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(5, 5, '12:00:00', 120000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(5, 5, '14:30:00', 140000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(5, 5, '16:00:00', 200000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Bandung - Surabaya templates (Pahala Kencana)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(6, 5, '19:00:00', 220000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(6, 5, '20:00:00', 260000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(6, 5, '21:00:00', 240000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Surabaya - Jakarta templates (Sinar Jaya)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(7, 1, '19:00:00', 250000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(7, 1, '20:00:00', 300000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(7, 1, '21:00:00', 280000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Yogyakarta - Jakarta templates (Sinar Jaya)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(8, 1, '21:00:00', 200000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(8, 1, '22:00:00', 250000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(8, 1, '23:00:00', 220000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu');

-- Semarang - Jakarta templates (Kramat Jati)
INSERT INTO template_jadwal (rute_id, perusahaan_id, jam_berangkat, harga, hari_operasi) VALUES
(9, 4, '20:00:00', 180000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(9, 4, '21:00:00', 220000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'),
(9, 4, '22:00:00', 200000, 'senin,selasa,rabu,kamis,jumat,sabtu,minggu'); 