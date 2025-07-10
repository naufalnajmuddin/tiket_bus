# Sistem Jadwal Bus Otomatis

Sistem ini memungkinkan jadwal perjalanan bus tersedia setiap hari secara otomatis tanpa perlu menambahkan data satu persatu setiap harinya.

## Fitur Utama

### 1. Template Jadwal
- **Template Jadwal**: Menyimpan pola jadwal yang akan diulang setiap hari
- **Jam Berangkat**: Waktu keberangkatan yang konsisten
- **Harga**: Harga tiket yang tetap
- **Hari Operasi**: Menentukan hari mana saja bus beroperasi
- **Status**: Aktif/nonaktif untuk mengontrol template

### 2. Generator Jadwal Otomatis
- **On-Demand Generation**: Jadwal dibuat saat user mencari tiket
- **Batch Generation**: Menghasilkan jadwal untuk beberapa hari sekaligus
- **Round-Robin Bus Selection**: Distribusi bus secara merata
- **Automatic Seat Creation**: Kursi dibuat otomatis sesuai kapasitas bus

### 3. Manajemen Jadwal
- **Admin Panel**: Interface untuk mengelola template jadwal
- **Cron Job**: Script untuk maintenance harian
- **Cleanup**: Menghapus jadwal lama secara otomatis

## Struktur Database

### Tabel `template_jadwal`
```sql
CREATE TABLE template_jadwal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rute_id INT NOT NULL,
    perusahaan_id INT NOT NULL,
    jam_berangkat TIME NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    hari_operasi SET('senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rute_id) REFERENCES rute(id),
    FOREIGN KEY (perusahaan_id) REFERENCES perusahaan_otobus(id)
);
```

## Cara Kerja Sistem

### 1. Pencarian Tiket
1. User mencari tiket dengan kota asal, tujuan, dan tanggal
2. Sistem mengecek apakah jadwal sudah ada untuk tanggal tersebut
3. Jika belum ada, sistem akan:
   - Mengambil template jadwal untuk rute tersebut
   - Memilih bus yang tersedia (round-robin)
   - Menghitung waktu tiba berdasarkan estimasi
   - Membuat jadwal baru dengan kursi yang tersedia
4. Menampilkan hasil pencarian

### 2. Generator Otomatis
1. **ScheduleGenerator**: Class utama untuk menghasilkan jadwal
2. **ensureSchedulesExist()**: Memastikan jadwal tersedia
3. **generateSchedulesForRoute()**: Menghasilkan jadwal untuk rute tertentu
4. **createScheduleFromTemplate()**: Membuat jadwal dari template

### 3. Seleksi Bus
- **Round-Robin**: Distribusi bus secara merata berdasarkan tanggal dan rute
- **Kapasitas**: Menggunakan kapasitas bus untuk jumlah kursi
- **Status**: Hanya bus aktif yang digunakan

## Setup dan Instalasi

### 1. Setup Database
```bash
# Jalankan setup database utama
php setup_database.php

# Jalankan setup template jadwal
php setup_template_database.php
```

### 2. Setup Cron Job
Tambahkan ke crontab untuk menjalankan generator harian:
```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut (jalan setiap hari jam 2 pagi)
0 2 * * * /usr/bin/php /path/to/tiket-bus/cron_generate_schedules.php
```

### 3. Konfigurasi Web Server
Pastikan folder `logs/` dapat ditulis oleh web server:
```bash
mkdir logs
chmod 755 logs
```

## File-File Penting

### Controller
- `controller/ScheduleGenerator.php`: Generator jadwal otomatis
- `controller/BusController.php`: Modifikasi untuk menggunakan sistem otomatis

### Script
- `cron_generate_schedules.php`: Script cron job harian
- `setup_template_database.php`: Setup database template

### View
- `view/admin/template-jadwal.php`: Halaman admin untuk mengelola template
- `view/cari-tiket.php`: Halaman pencarian tiket (sudah dimodifikasi)

### Database
- `database/template_jadwal.sql`: Struktur dan data template jadwal

## Penggunaan

### 1. Untuk Admin
1. **Akses Admin Panel**: Login sebagai admin
2. **Kelola Template**: Tambah/edit/hapus template jadwal
3. **Set Hari Operasi**: Tentukan hari mana bus beroperasi
4. **Atur Harga**: Sesuaikan harga untuk setiap template

### 2. Untuk User
1. **Cari Tiket**: Masukkan kota asal, tujuan, dan tanggal
2. **Hasil Otomatis**: Jadwal akan tersedia untuk semua tanggal
3. **Pesan Tiket**: Pilih jadwal yang diinginkan

### 3. Maintenance
1. **Cron Job**: Jalankan harian untuk maintenance
2. **Log Monitoring**: Cek file log untuk monitoring
3. **Template Update**: Update template sesuai kebutuhan

## Keuntungan Sistem

### 1. Otomatisasi
- **Tidak perlu input manual**: Jadwal dibuat otomatis
- **Konsistensi**: Pola jadwal yang konsisten
- **Skalabilitas**: Mudah menambah rute baru

### 2. Fleksibilitas
- **Template Management**: Mudah mengubah pola jadwal
- **Hari Operasi**: Kontrol hari operasi per template
- **Status Control**: Aktif/nonaktif template

### 3. Efisiensi
- **Storage**: Tidak menyimpan data berlebihan
- **Performance**: Generate on-demand
- **Maintenance**: Cleanup otomatis

## Monitoring dan Troubleshooting

### 1. Log Files
- **Location**: `logs/schedule_generation.log`
- **Content**: Timestamp, action, status, statistics
- **Rotation**: Manual atau otomatis

### 2. Statistics
- **Total Schedules**: Jumlah jadwal di database
- **Today's Schedules**: Jadwal untuk hari ini
- **Future Schedules**: Jadwal untuk masa depan

### 3. Common Issues
- **No Schedules**: Cek template jadwal aktif
- **Wrong Times**: Cek estimasi waktu di rute
- **No Buses**: Cek status bus aktif
- **Cron Not Running**: Cek crontab dan permissions

## Contoh Penggunaan

### 1. Menambah Template Baru
```php
// Via Admin Panel
// 1. Pilih rute: Jakarta → Bandung
// 2. Pilih perusahaan: Sinar Jaya
// 3. Set jam: 07:00
// 4. Set harga: 160000
// 5. Pilih hari: Senin-Jumat
// 6. Status: Aktif
```

### 2. Generate Jadwal Manual
```php
require_once 'controller/ScheduleGenerator.php';
$generator = new ScheduleGenerator($pdo);

// Generate untuk 30 hari ke depan
$generator->generateSchedulesForNextDays(30);

// Generate untuk tanggal tertentu
$generator->generateSchedulesForDate('2024-01-15');
```

### 3. Monitoring
```bash
# Cek log terbaru
tail -f logs/schedule_generation.log

# Cek statistik
php -r "
require 'config/database.php';
require 'controller/ScheduleGenerator.php';
\$gen = new ScheduleGenerator(\$pdo);
print_r(\$gen->getScheduleStats());
"
```

## Best Practices

### 1. Template Management
- **Konsistensi**: Gunakan pola waktu yang konsisten
- **Variasi**: Sediakan variasi harga untuk waktu berbeda
- **Hari Operasi**: Sesuaikan dengan kebutuhan pasar

### 2. Performance
- **Batch Processing**: Generate jadwal dalam batch
- **Cleanup**: Hapus jadwal lama secara berkala
- **Indexing**: Pastikan index pada kolom pencarian

### 3. Monitoring
- **Log Rotation**: Rotasi log file secara berkala
- **Alert System**: Setup alert untuk error
- **Backup**: Backup template dan jadwal penting

## Kesimpulan

Sistem jadwal otomatis ini memberikan solusi yang efisien dan scalable untuk manajemen jadwal bus. Dengan menggunakan template dan generator otomatis, sistem dapat menyediakan jadwal untuk semua tanggal tanpa input manual, sambil tetap memberikan fleksibilitas untuk penyesuaian kebutuhan bisnis. 