#!/bin/bash

# =============================================================================
# LMS E-Book Deployment Update Script
# =============================================================================
# Script untuk melakukan update aplikasi Laravel dengan backup otomatis
# dan health check untuk memastikan aplikasi berjalan dengan baik
# =============================================================================

set -e  # Exit on any error

# Konfigurasi
PROJECT_DIR="/var/www/lms-ebook"
BACKUP_DIR="/var/backups/lms-ebook"
LOG_FILE="/var/log/lms-ebook-deploy.log"
HEALTH_CHECK_URL="http://localhost/health-check"
MAX_BACKUP_KEEP=5

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fungsi logging
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Fungsi untuk membuat backup
create_backup() {
    local backup_name="backup_$(date +%Y%m%d_%H%M%S)"
    local backup_path="$BACKUP_DIR/$backup_name"
    
    log "Membuat backup ke: $backup_path"
    
    # Buat direktori backup jika belum ada
    mkdir -p "$BACKUP_DIR"
    
    # Backup kode aplikasi
    cp -r "$PROJECT_DIR" "$backup_path"
    
    # Backup database
    if [ -f "$PROJECT_DIR/.env" ]; then
        DB_NAME=$(grep DB_DATABASE "$PROJECT_DIR/.env" | cut -d '=' -f2)
        DB_USER=$(grep DB_USERNAME "$PROJECT_DIR/.env" | cut -d '=' -f2)
        DB_PASS=$(grep DB_PASSWORD "$PROJECT_DIR/.env" | cut -d '=' -f2)
        
        if [ ! -z "$DB_NAME" ]; then
            log "Backup database: $DB_NAME"
            mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$backup_path/database_backup.sql"
        fi
    fi
    
    # Simpan informasi backup
    echo "$backup_name" > "$PROJECT_DIR/.last_backup"
    
    success "Backup berhasil dibuat: $backup_name"
    
    # Hapus backup lama (keep only last 5)
    cleanup_old_backups
}

# Fungsi untuk membersihkan backup lama
cleanup_old_backups() {
    local backup_count=$(ls -1 "$BACKUP_DIR" | wc -l)
    
    if [ "$backup_count" -gt "$MAX_BACKUP_KEEP" ]; then
        log "Membersihkan backup lama (keep $MAX_BACKUP_KEEP terbaru)"
        ls -1t "$BACKUP_DIR" | tail -n +$((MAX_BACKUP_KEEP + 1)) | while read backup; do
            rm -rf "$BACKUP_DIR/$backup"
            log "Dihapus backup lama: $backup"
        done
    fi
}

# Fungsi health check
health_check() {
    log "Melakukan health check..."
    
    # Check if web server is running
    if ! pgrep -x "nginx" > /dev/null && ! pgrep -x "apache2" > /dev/null; then
        error "Web server tidak berjalan!"
        return 1
    fi
    
    # Check if PHP-FPM is running
    if ! pgrep -x "php-fpm" > /dev/null; then
        error "PHP-FPM tidak berjalan!"
        return 1
    fi
    
    # Check Laravel application
    cd "$PROJECT_DIR"
    if ! php artisan --version > /dev/null 2>&1; then
        error "Laravel application tidak dapat diakses!"
        return 1
    fi
    
    # Check database connection
    if ! php artisan migrate:status > /dev/null 2>&1; then
        error "Database connection gagal!"
        return 1
    fi
    
    # Check if application is accessible via HTTP (optional)
    if command -v curl > /dev/null; then
        if curl -f -s "$HEALTH_CHECK_URL" > /dev/null 2>&1; then
            success "HTTP health check berhasil"
        else
            warning "HTTP health check gagal - aplikasi mungkin tidak dapat diakses via web"
        fi
    fi
    
    success "Health check berhasil!"
    return 0
}

# Fungsi untuk update aplikasi
update_application() {
    log "Memulai update aplikasi..."
    
    cd "$PROJECT_DIR"
    
    # Cek status git
    if [ ! -d ".git" ]; then
        error "Direktori ini bukan repository git!"
        exit 1
    fi
    
    # Simpan perubahan lokal jika ada
    if ! git diff-index --quiet HEAD --; then
        warning "Ada perubahan lokal yang belum di-commit"
        git stash push -m "Auto-stash before update $(date)"
        log "Perubahan lokal di-stash"
    fi
    
    # Fetch latest changes
    log "Mengambil perubahan terbaru dari repository..."
    git fetch origin
    
    # Cek apakah ada update
    LOCAL=$(git rev-parse HEAD)
    REMOTE=$(git rev-parse origin/main 2>/dev/null || git rev-parse origin/master)
    
    if [ "$LOCAL" = "$REMOTE" ]; then
        success "Aplikasi sudah up-to-date!"
        return 0
    fi
    
    # Show what will be updated
    log "Perubahan yang akan diterapkan:"
    git log --oneline "$LOCAL..$REMOTE"
    
    # Pull latest changes
    log "Menerapkan update..."
    git pull origin main 2>/dev/null || git pull origin master
    
    # Install/update dependencies jika composer.json berubah
    if git diff --name-only "$LOCAL" HEAD | grep -q "composer.json\|composer.lock"; then
        log "Composer dependencies berubah, menjalankan composer install..."
        composer install --no-dev --optimize-autoloader
    fi
    
    # Install/update NPM dependencies jika package.json berubah
    if git diff --name-only "$LOCAL" HEAD | grep -q "package.json\|package-lock.json"; then
        log "NPM dependencies berubah, menjalankan npm install..."
        npm ci --production
        npm run build
    fi
    
    # Jalankan migrasi jika ada perubahan database
    if git diff --name-only "$LOCAL" HEAD | grep -q "database/migrations"; then
        log "Ada migrasi database baru, menjalankan migrasi..."
        php artisan migrate --force
    fi
    
    # Clear cache
    log "Membersihkan cache..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    # Optimize aplikasi
    log "Mengoptimalkan aplikasi..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    success "Update aplikasi berhasil!"
}

# Fungsi untuk restart services
restart_services() {
    log "Restart services..."
    
    # Restart PHP-FPM
    if systemctl is-active --quiet php8.2-fpm; then
        systemctl restart php8.2-fpm
        log "PHP-FPM direstart"
    elif systemctl is-active --quiet php8.1-fpm; then
        systemctl restart php8.1-fpm
        log "PHP-FPM direstart"
    fi
    
    # Restart web server
    if systemctl is-active --quiet nginx; then
        systemctl reload nginx
        log "Nginx direload"
    elif systemctl is-active --quiet apache2; then
        systemctl restart apache2
        log "Apache2 direstart"
    fi
    
    # Restart queue workers jika ada
    if systemctl is-active --quiet laravel-worker; then
        systemctl restart laravel-worker
        log "Laravel queue worker direstart"
    fi
}

# Main execution
main() {
    log "=== Memulai deployment update ==="
    
    # Cek apakah script dijalankan sebagai user yang tepat
    if [ "$EUID" -eq 0 ]; then
        warning "Script dijalankan sebagai root. Pastikan permission file sudah benar."
    fi
    
    # Cek apakah direktori project ada
    if [ ! -d "$PROJECT_DIR" ]; then
        error "Direktori project tidak ditemukan: $PROJECT_DIR"
        exit 1
    fi
    
    # Buat backup sebelum update
    create_backup
    
    # Update aplikasi
    if update_application; then
        # Restart services
        restart_services
        
        # Health check
        if health_check; then
            success "=== Deployment berhasil! ==="
            log "Backup tersimpan di: $(cat $PROJECT_DIR/.last_backup)"
        else
            error "=== Health check gagal! ==="
            warning "Aplikasi mungkin tidak berjalan dengan baik."
            warning "Gunakan script rollback.sh jika diperlukan."
            exit 1
        fi
    else
        error "=== Update gagal! ==="
        warning "Gunakan script rollback.sh untuk mengembalikan ke versi sebelumnya."
        exit 1
    fi
}

# Jalankan script
main "$@"