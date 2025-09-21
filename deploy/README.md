# LMS E-Book Deployment Scripts

Dokumentasi lengkap untuk script deployment dan rollback aplikasi LMS E-Book.

## 📁 Struktur File

```
deploy/
├── update.sh      # Script untuk update/deployment aplikasi
├── rollback.sh    # Script untuk rollback ke versi sebelumnya
└── README.md      # Dokumentasi ini
```

## 🚀 Script Update (update.sh)

Script untuk melakukan deployment otomatis dengan fitur backup dan health check.

### Fitur Utama

- ✅ Backup otomatis sebelum update
- ✅ Update hanya file yang berubah (git-based)
- ✅ Health check setelah deployment
- ✅ Rollback otomatis jika health check gagal
- ✅ Logging lengkap
- ✅ Restart services otomatis

### Cara Penggunaan

#### 1. Upload Script ke VPS

```bash
# Upload script ke VPS
scp deploy/update.sh user@your-vps:/var/www/lms-ebook/
scp deploy/rollback.sh user@your-vps:/var/www/lms-ebook/

# Atau jika sudah ada di repository
git pull origin main
```

#### 2. Set Permission

```bash
chmod +x /var/www/lms-ebook/update.sh
chmod +x /var/www/lms-ebook/rollback.sh
```

#### 3. Jalankan Update

```bash
# Update dengan mode interaktif
./update.sh

# Update dengan branch tertentu
./update.sh --branch main

# Update dengan force (skip konfirmasi)
./update.sh --force

# Update tanpa restart services
./update.sh --no-restart

# Tampilkan bantuan
./update.sh --help
```

### Parameter yang Tersedia

| Parameter | Deskripsi |
|-----------|-----------|
| `--branch <name>` | Specify branch yang akan di-pull (default: main) |
| `--force` | Skip konfirmasi dan jalankan update langsung |
| `--no-restart` | Skip restart services setelah update |
| `--help` | Tampilkan bantuan |

### Contoh Penggunaan

```bash
# Update dari branch production
./update.sh --branch production

# Update dengan force mode (untuk automation)
./update.sh --force --branch main

# Update tanpa restart services
./update.sh --no-restart
```

## 🔄 Script Rollback (rollback.sh)

Script untuk mengembalikan aplikasi ke backup sebelumnya jika terjadi masalah.

### Fitur Utama

- ✅ Daftar backup yang tersedia
- ✅ Pilihan backup interaktif
- ✅ Emergency backup sebelum rollback
- ✅ Restore database (opsional)
- ✅ Health check setelah rollback
- ✅ Restart services otomatis

### Cara Penggunaan

#### 1. Tampilkan Daftar Backup

```bash
./rollback.sh --list
```

#### 2. Rollback Interaktif

```bash
# Rollback dengan pilihan interaktif
./rollback.sh

# Atau
./rollback.sh --rollback
```

#### 3. Rollback ke Backup Tertentu

```bash
# Rollback ke backup nomor 1 (terbaru)
./rollback.sh --rollback 1

# Rollback ke backup nomor 3
./rollback.sh --rollback 3
```

### Parameter yang Tersedia

| Parameter | Deskripsi |
|-----------|-----------|
| `--list` | Tampilkan daftar backup yang tersedia |
| `--rollback [num]` | Rollback ke backup tertentu (opsional nomor) |
| `--help` | Tampilkan bantuan |

### Contoh Penggunaan

```bash
# Lihat daftar backup
./rollback.sh --list

# Rollback interaktif
./rollback.sh

# Rollback langsung ke backup terbaru
./rollback.sh --rollback 1
```

## 📋 Workflow Deployment

### 1. Persiapan Awal

```bash
# 1. Pastikan repository sudah di-clone di VPS
cd /var/www/lms-ebook

# 2. Set permission script
chmod +x update.sh rollback.sh

# 3. Pastikan direktori backup ada
sudo mkdir -p /var/backups/lms-ebook
sudo chown www-data:www-data /var/backups/lms-ebook
```

### 2. Deployment Normal

```bash
# 1. Jalankan update
./update.sh --branch main

# 2. Monitor log jika diperlukan
tail -f /var/log/lms-ebook-deploy.log

# 3. Verifikasi aplikasi berjalan normal
curl -I http://your-domain.com
```

### 3. Jika Terjadi Masalah

```bash
# 1. Lihat daftar backup
./rollback.sh --list

# 2. Rollback ke backup terbaru
./rollback.sh --rollback 1

# 3. Atau rollback interaktif
./rollback.sh
```

## 🔧 Konfigurasi

### Direktori yang Digunakan

- **Project Directory**: `/var/www/lms-ebook`
- **Backup Directory**: `/var/backups/lms-ebook`
- **Log File**: `/var/log/lms-ebook-deploy.log`

### Services yang Di-manage

- **PHP-FPM**: `php8.2-fpm` atau `php8.1-fpm`
- **Web Server**: `nginx` atau `apache2`
- **Queue Worker**: `laravel-worker` (jika ada)

### File yang Tidak Akan Ditimpa

- `.env` (konfigurasi environment)
- `storage/` (file upload dan cache)

## 📊 Monitoring dan Logging

### Log File

Semua aktivitas deployment dan rollback dicatat di:
```
/var/log/lms-ebook-deploy.log
```

### Melihat Log

```bash
# Lihat log terbaru
tail -f /var/log/lms-ebook-deploy.log

# Lihat log hari ini
grep "$(date +%Y-%m-%d)" /var/log/lms-ebook-deploy.log

# Lihat log error saja
grep "ERROR" /var/log/lms-ebook-deploy.log
```

### Health Check

Script akan melakukan health check otomatis:

1. ✅ Laravel application status
2. ✅ Database connection
3. ✅ File permissions
4. ✅ Cache clearing
5. ✅ HTTP response check

## 🚨 Troubleshooting

### Masalah Umum

#### 1. Permission Denied

```bash
# Fix permission script
chmod +x update.sh rollback.sh

# Fix permission direktori
sudo chown -R www-data:www-data /var/www/lms-ebook
```

#### 2. Database Connection Error

```bash
# Cek konfigurasi .env
cat .env | grep DB_

# Test koneksi database
php artisan migrate:status
```

#### 3. Git Pull Error

```bash
# Reset local changes
git reset --hard HEAD
git clean -fd

# Kemudian jalankan update lagi
./update.sh
```

#### 4. Services Tidak Start

```bash
# Cek status services
systemctl status php8.2-fpm
systemctl status nginx

# Restart manual jika diperlukan
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

### Emergency Recovery

Jika semua backup gagal:

```bash
# 1. Clone fresh dari repository
cd /var/www/
sudo rm -rf lms-ebook-broken
sudo mv lms-ebook lms-ebook-broken
sudo git clone https://github.com/your-repo/lms-ebook.git

# 2. Copy konfigurasi penting
sudo cp lms-ebook-broken/.env lms-ebook/
sudo cp -r lms-ebook-broken/storage lms-ebook/

# 3. Set permission
sudo chown -R www-data:www-data lms-ebook
sudo chmod -R 755 lms-ebook

# 4. Install dependencies
cd lms-ebook
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📝 Best Practices

### 1. Sebelum Deployment

- ✅ Test di environment staging terlebih dahulu
- ✅ Backup database manual jika ada perubahan schema
- ✅ Informasikan tim tentang maintenance window
- ✅ Siapkan rollback plan

### 2. Selama Deployment

- ✅ Monitor log secara real-time
- ✅ Test aplikasi setelah deployment
- ✅ Verifikasi semua fitur utama berfungsi
- ✅ Check performance aplikasi

### 3. Setelah Deployment

- ✅ Monitor error log selama 24 jam
- ✅ Backup otomatis sudah berjalan
- ✅ Update dokumentasi jika ada perubahan
- ✅ Inform tim bahwa deployment berhasil

## 🔐 Security Notes

- Script harus dijalankan dengan user yang memiliki akses ke direktori web
- Jangan commit file `.env` ke repository
- Pastikan backup directory memiliki permission yang tepat
- Monitor log file untuk aktivitas mencurigakan

## 📞 Support

Jika mengalami masalah:

1. Cek log file: `/var/log/lms-ebook-deploy.log`
2. Jalankan health check manual: `php artisan migrate:status`
3. Cek status services: `systemctl status php8.2-fpm nginx`
4. Hubungi tim development jika masalah persisten

---

**Catatan**: Selalu test script di environment staging sebelum digunakan di production!