# Sistem Login Go.Ket

Sistem login lengkap untuk aplikasi pemesanan tiket bus Go.Ket dengan fitur autentikasi, registrasi, dan manajemen session.

## 🚀 Fitur Utama

### 🔐 Login User
- Form login dengan username/email dan password
- Validasi di sisi frontend dan backend
- Redirect ke dashboard sesuai role (user/admin)
- Pencatatan riwayat login ke database

### 📝 Register
- Form pendaftaran lengkap (nama, email, username, password, dll.)
- Validasi input (password minimal 6 karakter, email unik, dll.)
- Password di-hash menggunakan bcrypt
- Role default: user

### 🛡️ Login Admin
- Menggunakan form login yang sama
- Dropdown pilihan "Login sebagai admin"
- Validasi role admin untuk akses dashboard admin
- Pemisahan akses user dan admin

### 🚪 Logout
- Tombol logout di dashboard
- Penghapusan session dan cookies
- Redirect ke halaman login
- Pencatatan logout ke database



## 🗂️ Struktur Database

### Tabel `users`
```sql
- id (Primary Key)
- nama (VARCHAR)
- email (VARCHAR, UNIQUE)
- username (VARCHAR, UNIQUE)
- password (VARCHAR, bcrypt)
- no_telepon (VARCHAR)
- alamat (TEXT)
- role (ENUM: 'admin', 'user')
- status (ENUM: 'aktif', 'nonaktif')
- created_at (TIMESTAMP)
```



## 📁 Struktur File

```
tiket-bus/
├── config/
│   └── database.php          # Konfigurasi database
├── controller/
│   └── AuthController.php    # Controller autentikasi
├── view/
│   ├── login.php             # Halaman login
│   ├── register.php          # Halaman register
│   ├── user/
│   │   └── dashboard.php     # Dashboard user
│   └── admin/
│       └── dashboard.php     # Dashboard admin
├── database/
│   └── tiket_bus.sql         # Schema database
├── setup_database.php        # Script setup database
└── README_LOGIN_SYSTEM.md    # Dokumentasi ini
```

## 🛠️ Instalasi dan Setup

### 1. Prerequisites
- PHP 7.4 atau lebih tinggi
- MySQL/MariaDB
- Web server (Apache/Nginx)
- XAMPP/WAMP/MAMP (untuk development)

### 2. Setup Database
```bash
# Jalankan script setup database
php setup_database.php
```

### 3. Konfigurasi Database
Edit file `config/database.php` sesuai dengan konfigurasi database Anda:
```php
$host = 'localhost';
$dbname = 'tiket_bus';
$username = 'root';
$password = '';
```

### 4. Akses Aplikasi
Buka browser dan akses:
```
http://localhost/tiket-bus/view/
```

## 🔑 Kredensial Default

### Admin
- **Username:** admin
- **Password:** admin123
- **Email:** admin@goket.com

### User Demo
- **Username:** user
- **Password:** user123
- **Email:** user@goket.com

## 🔒 Keamanan

### Password Hashing
- Menggunakan `password_hash()` dengan algoritma bcrypt
- Salt otomatis dan aman
- Verifikasi dengan `password_verify()`

### Session Management
- Session dimulai di setiap halaman
- Session timeout otomatis
- Penghapusan session saat logout

### Input Validation
- Validasi di sisi frontend (JavaScript)
- Validasi di sisi backend (PHP)
- Sanitasi input untuk mencegah XSS
- Prepared statements untuk mencegah SQL injection

### Access Control
- Middleware untuk mengecek login status
- Middleware untuk mengecek role admin
- Redirect otomatis untuk user yang tidak berhak

## 📱 Fitur UI/UX

### Responsive Design
- Menggunakan Bootstrap 5
- Responsive untuk desktop, tablet, dan mobile
- Sidebar yang collapsible

### User Experience
- Form validation real-time
- Password strength indicator
- Toggle password visibility
- Loading states dan feedback
- Alert messages untuk success/error

### Dashboard Features
- Statistik real-time
- Quick actions
- Recent activities
- Navigation yang intuitif

## 🔧 Customization

### Menambah Role Baru
1. Edit enum di tabel `users`
2. Update AuthController untuk handle role baru
3. Buat dashboard untuk role tersebut

### Mengubah Validasi
1. Edit method `register()` di AuthController
2. Update JavaScript validation di form
3. Sesuaikan pesan error

### Mengubah UI
1. Edit file CSS di `assets/css/style.css`
2. Update Bootstrap classes
3. Modifikasi layout sesuai kebutuhan

## 🐛 Troubleshooting

### Error Koneksi Database
- Pastikan MySQL berjalan
- Cek konfigurasi di `config/database.php`
- Pastikan database `tiket_bus` sudah dibuat

### Error Login
- Cek apakah user sudah terdaftar
- Pastikan password benar
- Cek status user (aktif/nonaktif)

### Error Session
- Pastikan session sudah dimulai
- Cek konfigurasi PHP session
- Pastikan cookies enabled

## 📈 Monitoring dan Logs



### Error Logs
- Error database tercatat di PHP error log
- Debug information untuk development
- Log file dapat diakses di server

## 🔄 Update dan Maintenance

### Backup Database
```sql
mysqldump -u root -p tiket_bus > backup.sql
```

### Update Password
```sql
UPDATE users SET password = '$2y$10$...' WHERE username = 'admin';
```



## 📞 Support

Untuk pertanyaan atau masalah:
1. Cek dokumentasi ini
2. Review error logs
3. Pastikan semua prerequisites terpenuhi
4. Test dengan kredensial default

## 📄 License

Sistem login ini dibuat untuk proyek Go.Ket dan dapat digunakan untuk keperluan pendidikan dan development.

---

**Dibuat dengan ❤️ untuk Go.Ket** 