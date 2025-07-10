# Go.Ket - Sistem Pemesanan Tiket Bus

Sistem pemesanan tiket bus online yang memudahkan pengguna untuk mencari, memilih, dan memesan tiket bus dengan mudah dan aman.

## 🚀 Fitur Utama

- **Pencarian Tiket**: Cari tiket berdasarkan kota asal, tujuan, dan tanggal
- **Pemilihan Kursi**: Pilih kursi secara visual dengan seat map interaktif
- **Pemesanan Online**: Proses pemesanan yang mudah dan cepat
- **Manajemen Data**: Sistem database yang terstruktur untuk data bus, jadwal, dan pemesanan

## 📋 Prasyarat

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- XAMPP/WAMP/MAMP (untuk development)

## 🛠️ Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd tiket-bus
```

### 2. Setup Database

#### A. Buat Database
1. Buka phpMyAdmin atau MySQL client
2. Buat database baru dengan nama `tiket_bus`
3. Import file `database/tiket_bus.sql`

#### B. Konfigurasi Database
1. Edit file `config/database.php`
2. Sesuaikan konfigurasi database:
```php
$host = 'localhost';
$dbname = 'tiket_bus';
$username = 'root';  // sesuaikan dengan username MySQL Anda
$password = '';      // sesuaikan dengan password MySQL Anda
```

### 3. Setup Web Server

#### A. XAMPP
1. Copy folder `tiket-bus` ke `htdocs/`
2. Akses melalui `http://localhost/tiket-bus/view/`

#### B. Apache/Nginx
1. Set document root ke folder `tiket-bus`
2. Pastikan mod_rewrite aktif (untuk Apache)

### 4. Verifikasi Instalasi
1. Buka browser dan akses `http://localhost/tiket-bus/view/`
2. Pastikan halaman utama muncul dengan benar
3. Test fitur pencarian tiket

## 📁 Struktur Database

### Tabel Utama:
- **kota**: Data kota asal dan tujuan
- **perusahaan_otobus**: Data perusahaan bus
- **bus**: Data bus dan fasilitasnya
- **rute**: Data rute perjalanan
- **jadwal**: Jadwal keberangkatan bus
- **kursi**: Status kursi untuk setiap jadwal
- **users**: Data pengguna sistem
- **pemesanan**: Data pemesanan tiket
- **detail_pemesanan**: Detail penumpang

## 🎯 Cara Penggunaan

### 1. Pencarian Tiket
1. Buka halaman utama
2. Klik "Cari Tiket"
3. Pilih kota asal dan tujuan
4. Pilih tanggal keberangkatan
5. Klik "Cari"

### 2. Pemesanan Tiket
1. Pilih bus dari hasil pencarian
2. Klik "Lihat Detail"
3. Pilih kursi yang diinginkan
4. Klik "Lanjutkan Pemesanan"
5. Isi data penumpang
6. Konfirmasi pembayaran

## 🔧 Konfigurasi

### Menambah Data Kota
```sql
INSERT INTO kota (nama, provinsi) VALUES ('Nama Kota', 'Nama Provinsi');
```

### Menambah Perusahaan Bus
```sql
INSERT INTO perusahaan_otobus (nama, alamat, telepon, email) 
VALUES ('Nama PO', 'Alamat', 'Telepon', 'email@domain.com');
```

### Menambah Jadwal
```sql
INSERT INTO jadwal (bus_id, rute_id, tanggal_berangkat, jam_berangkat, jam_tiba, harga, kursi_tersedia) 
VALUES (1, 1, '2025-01-25', '08:00:00', '14:00:00', 150000, 45);
```

## 🐛 Troubleshooting

### Masalah Koneksi Database
1. Periksa konfigurasi di `config/database.php`
2. Pastikan MySQL service berjalan
3. Periksa username dan password database

### Halaman Tidak Muncul
1. Periksa path file di web server
2. Pastikan PHP extension aktif
3. Periksa error log web server

### Gambar Tidak Muncul
1. Periksa path relatif gambar
2. Pastikan folder `assets/img/` ada
3. Periksa permission folder

## 📝 Log Perubahan

### v1.0.0 (2025-01-25)
- ✅ Halaman utama dengan desain responsif
- ✅ Halaman "Tentang Kami"
- ✅ Sistem pencarian tiket dengan database
- ✅ Halaman detail tiket dengan seat map
- ✅ Halaman pemesanan dengan form data penumpang
- ✅ Database structure dengan sample data

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail.

## 📞 Kontak

- **Email**: info@goket.com
- **Website**: https://goket.com
- **Telepon**: +62 21 1234 5678

---

**Go.Ket** - Sahabat setia perjalanan darat Anda 🚌 