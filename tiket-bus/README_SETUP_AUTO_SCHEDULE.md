# Panduan Setup Sistem Jadwal Bus Otomatis

Panduan lengkap untuk menginstal dan mengkonfigurasi sistem jadwal bus otomatis yang dapat menghasilkan jadwal perjalanan setiap hari tanpa input manual.

## 🚀 Quick Start

### 1. Setup Otomatis (Recommended)
```bash
# Jalankan script setup otomatis
php run_setup.php
```

### 2. Setup Manual
```bash
# Setup database utama
php setup_database.php

# Setup template jadwal
php setup_template_database.php

# Buat folder logs
mkdir logs
chmod 755 logs
```

## 📋 Prerequisites

### Software Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Cron job support

### Database Configuration
Pastikan file `config/database.php` sudah dikonfigurasi dengan benar:
```php
<?php
$host = 'localhost';
$dbname = 'tiket_bus';
$username = 'your_username';
$password = 'your_password';
```

## 🔧 Setup Detail

### Step 1: Database Setup
```bash
# Jalankan setup database
php setup_database.php
```

**Output yang diharapkan:**
```
Setting up database...
✓ Database created successfully
✓ Tables created successfully
✓ Sample data inserted successfully
✓ Database setup completed!
```

### Step 2: Template Database Setup
```bash
# Jalankan setup template
php setup_template_database.php
```

**Output yang diharapkan:**
```
Setting up template schedule database...
✓ Template jadwal table created
✓ Sample template schedules inserted
✓ Initial schedules generated for next 7 days
✓ Template database setup completed successfully!
```

### Step 3: Cron Job Setup
```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut (jalan setiap hari jam 2 pagi)
0 2 * * * /usr/bin/php /path/to/tiket-bus/cron_generate_schedules.php
```

### Step 4: Permissions Setup
```bash
# Buat folder logs dan set permissions
mkdir logs
chmod 755 logs
chown www-data:www-data logs  # Untuk Linux/Ubuntu
```

## 🧪 Testing

### 1. Test Database Connection
```bash
php -r "
require 'config/database.php';
echo 'Database connection: OK\n';
"
```

### 2. Test Schedule Generation
```bash
php -r "
require 'config/database.php';
require 'controller/ScheduleGenerator.php';
\$gen = new ScheduleGenerator(\$pdo);
\$stats = \$gen->getScheduleStats();
echo 'Total schedules: ' . \$stats['total_schedules'] . '\n';
echo 'Today schedules: ' . \$stats['today_schedules'] . '\n';
"
```

### 3. Test Web Interface
1. Buka browser ke: `http://localhost/tiket-bus/`
2. Login sebagai admin: `admin` / `password`
3. Akses: `http://localhost/tiket-bus/view/admin/template-jadwal.php`
4. Test pencarian: `http://localhost/tiket-bus/view/cari-tiket.php`

## 📊 Monitoring

### 1. Log Files
```bash
# Monitor log real-time
tail -f logs/schedule_generation.log

# Cek log terbaru
tail -20 logs/schedule_generation.log
```

### 2. Database Statistics
```sql
-- Cek jumlah jadwal
SELECT COUNT(*) as total_schedules FROM jadwal;

-- Cek jadwal hari ini
SELECT COUNT(*) as today_schedules FROM jadwal WHERE tanggal_berangkat = CURDATE();

-- Cek template aktif
SELECT COUNT(*) as active_templates FROM template_jadwal WHERE status = 'aktif';
```

### 3. System Health Check
```bash
# Cek cron job
crontab -l

# Cek file permissions
ls -la logs/

# Cek PHP error log
tail -f /var/log/apache2/error.log  # Ubuntu
tail -f /var/log/httpd/error_log    # CentOS
```

## 🔧 Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Cek konfigurasi database
cat config/database.php

# Test connection manual
mysql -u username -p -h localhost tiket_bus
```

#### 2. Permission Denied
```bash
# Set permissions
chmod 755 logs/
chown www-data:www-data logs/  # Ubuntu
chown apache:apache logs/      # CentOS
```

#### 3. Cron Job Not Running
```bash
# Cek cron service
sudo systemctl status cron

# Test cron job manual
php /path/to/tiket-bus/cron_generate_schedules.php

# Cek cron logs
sudo tail -f /var/log/cron
```

#### 4. No Schedules Generated
```bash
# Cek template jadwal
mysql -u username -p -e "SELECT * FROM template_jadwal WHERE status = 'aktif';"

# Cek bus aktif
mysql -u username -p -e "SELECT * FROM bus WHERE status = 'aktif';"

# Test generate manual
php -r "
require 'config/database.php';
require 'controller/ScheduleGenerator.php';
\$gen = new ScheduleGenerator(\$pdo);
\$result = \$gen->generateSchedulesForDate(date('Y-m-d'));
echo \$result ? 'Success' : 'Failed';
"
```

## 📁 File Structure

```
tiket-bus/
├── config/
│   └── database.php                 # Database configuration
├── controller/
│   ├── BusController.php            # Modified for auto schedule
│   └── ScheduleGenerator.php        # Auto schedule generator
├── database/
│   ├── tiket_bus.sql               # Main database schema
│   └── template_jadwal.sql         # Template schedule schema
├── view/
│   ├── admin/
│   │   ├── dashboard.php           # Admin dashboard (modified)
│   │   └── template-jadwal.php    # Template management
│   └── cari-tiket.php             # Search page (modified)
├── logs/                           # Log directory
├── cron_generate_schedules.php     # Cron job script
├── setup_database.php              # Main database setup
├── setup_template_database.php     # Template database setup
├── run_setup.php                   # Complete setup script
└── README_AUTO_SCHEDULE.md        # Complete documentation
```

## 🔄 Maintenance

### Daily Tasks
1. **Cron Job**: Otomatis berjalan setiap hari jam 2 pagi
2. **Log Rotation**: Monitor log file size
3. **Database Backup**: Backup template dan jadwal penting

### Weekly Tasks
1. **Template Review**: Cek dan update template jadwal
2. **Performance Check**: Monitor query performance
3. **Cleanup**: Hapus jadwal lama (otomatis)

### Monthly Tasks
1. **Statistics Review**: Analisis penggunaan sistem
2. **Template Optimization**: Optimasi pola jadwal
3. **Security Update**: Update dependencies

## 📈 Performance Tips

### 1. Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_jadwal_tanggal ON jadwal(tanggal_berangkat);
CREATE INDEX idx_template_rute ON template_jadwal(rute_id, status);
CREATE INDEX idx_bus_perusahaan ON bus(perusahaan_id, status);
```

### 2. PHP Configuration
```ini
; php.ini optimizations
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

### 3. Web Server Configuration
```apache
# Apache optimization
<Directory /var/www/tiket-bus>
    php_value memory_limit 256M
    php_value max_execution_time 300
</Directory>
```

## 🛡️ Security

### 1. File Permissions
```bash
# Set secure permissions
chmod 644 config/database.php
chmod 755 logs/
chmod 644 *.php
```

### 2. Database Security
```sql
-- Create dedicated user for application
CREATE USER 'tiket_bus'@'localhost' IDENTIFIED BY 'strong_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON tiket_bus.* TO 'tiket_bus'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Web Security
- Enable HTTPS
- Set secure headers
- Validate all inputs
- Use prepared statements (already implemented)

## 📞 Support

### Log Files Location
- **Application Log**: `logs/schedule_generation.log`
- **Web Server Log**: `/var/log/apache2/access.log`
- **Error Log**: `/var/log/apache2/error.log`

### Useful Commands
```bash
# Check system status
php -r "require 'config/database.php'; echo 'DB OK';"

# Generate schedules manually
php cron_generate_schedules.php

# Check template status
mysql -u username -p -e "SELECT COUNT(*) as active_templates FROM template_jadwal WHERE status = 'aktif';"

# Monitor real-time
tail -f logs/schedule_generation.log
```

## ✅ Checklist Setup

- [ ] Database connection configured
- [ ] Main database setup completed
- [ ] Template database setup completed
- [ ] Logs directory created with proper permissions
- [ ] Cron job configured
- [ ] Admin panel accessible
- [ ] Template management working
- [ ] Search functionality working
- [ ] Log monitoring setup
- [ ] Backup strategy implemented

## 🎯 Next Steps

1. **Customize Templates**: Sesuaikan template jadwal dengan kebutuhan
2. **Add Routes**: Tambah rute baru sesuai kebutuhan
3. **Monitor Performance**: Pantau performa sistem
4. **User Training**: Latih admin untuk menggunakan sistem
5. **Documentation**: Update dokumentasi sesuai kebutuhan

---

**Sistem siap digunakan!** 🚌✨

Untuk bantuan lebih lanjut, lihat dokumentasi lengkap di `README_AUTO_SCHEDULE.md` 