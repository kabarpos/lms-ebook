#!/bin/bash

# =============================================================================
# LMS E-Book Deployment Update Script
# =============================================================================
# Script untuk melakukan update aplikasi Laravel dengan backup otomatis
# dan health check untuk memastikan aplikasi berjalan dengan baik
# =============================================================================

set -e  # Exit on any error

# Deteksi lingkungan (Windows atau Linux/Produksi)
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" || "$OS" == "Windows_NT" ]]; then
    # Windows Development Environment
    PROJECT_DIR="$(pwd)"
    BACKUP_DIR="$PROJECT_DIR/storage/backups"
    LOG_FILE="storage/logs/deploy.log"
    HEALTH_CHECK_URL="http://localhost:8000/health"
    IS_WINDOWS=true
else
    # Linux Production Environment
    # Deteksi path project secara otomatis berdasarkan lokasi script
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
    BACKUP_DIR="$PROJECT_DIR/storage/backups"
    # Gunakan log file di project directory untuk menghindari permission issue
    LOG_FILE="$PROJECT_DIR/storage/logs/deploy.log"
    HEALTH_CHECK_URL="https://dscourse.top/health"
    IS_WINDOWS=false
fi

MAX_BACKUP_KEEP=5
BRANCH="main"  # Default branch

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fungsi logging
log() {
    mkdir -p "$(dirname "$LOG_FILE")"
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
    mkdir -p "$backup_path"
    
    # Backup kode aplikasi (exclude storage/backups untuk menghindari recursive copy)
    log "Backup file aplikasi..."
    rsync -av --exclude='storage/backups' --exclude='node_modules' --exclude='.git' \
          --exclude='vendor' --exclude='storage/logs' --exclude='storage/framework/cache' \
          --exclude='storage/framework/sessions' --exclude='storage/framework/views' \
          "$PROJECT_DIR/" "$backup_path/"
    
    # Backup database
    if [ -f "$PROJECT_DIR/.env" ]; then
        DB_NAME=$(grep DB_DATABASE "$PROJECT_DIR/.env" | cut -d '=' -f2)
        DB_USER=$(grep DB_USERNAME "$PROJECT_DIR/.env" | cut -d '=' -f2)
        DB_PASS=$(grep DB_PASSWORD "$PROJECT_DIR/.env" | cut -d '=' -f2)
        
        if [ ! -z "$DB_NAME" ]; then
            log "Backup database: $DB_NAME"
            mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$backup_path/database_backup.sql" 2>/dev/null || {
                log "Warning: Database backup gagal, melanjutkan tanpa backup database"
            }
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
    
    # Check if web server is running (skip for Windows development)
    if [ "$IS_WINDOWS" = false ]; then
        if ! pgrep -x "nginx" > /dev/null && ! pgrep -x "apache2" > /dev/null; then
            error "Web server tidak berjalan!"
            return 1
        fi
        
        # Check if PHP-FPM is running - try multiple versions and process names
        PHP_FPM_RUNNING=false
        
        # Check common PHP-FPM process names
        for php_process in "php-fpm" "php8.3-fpm" "php8.2-fpm" "php8.1-fpm" "php8.0-fpm" "php7.4-fpm"; do
            if pgrep -x "$php_process" > /dev/null 2>&1; then
                PHP_FPM_RUNNING=true
                log "PHP-FPM ditemukan: $php_process"
                break
            fi
        done
        
        # Also check by port (PHP-FPM usually runs on port 9000)
        if [ "$PHP_FPM_RUNNING" = false ] && netstat -tlnp 2>/dev/null | grep -q ":9000.*LISTEN"; then
            PHP_FPM_RUNNING=true
            log "PHP-FPM ditemukan pada port 9000"
        fi
        
        if [ "$PHP_FPM_RUNNING" = false ]; then
            error "PHP-FPM tidak berjalan!"
            log "Mencoba restart PHP-FPM..."
            
            # Try to restart PHP-FPM with different service names
            for service_name in "php-fpm" "php8.3-fpm" "php8.2-fpm" "php8.1-fpm" "php8.0-fpm" "php7.4-fpm"; do
                if systemctl is-enabled "$service_name" >/dev/null 2>&1; then
                    log "Mencoba restart $service_name..."
                    if systemctl restart "$service_name" 2>/dev/null; then
                        log "Berhasil restart $service_name"
                        PHP_FPM_RUNNING=true
                        break
                    else
                        warning "Gagal restart $service_name"
                    fi
                fi
            done
            
            if [ "$PHP_FPM_RUNNING" = false ]; then
                error "Tidak dapat menjalankan PHP-FPM!"
                return 1
            fi
        fi
    else
        # For Windows, check if Laravel dev server is running on port 8000
        if ! netstat -an | grep -q ":8000.*LISTENING"; then
            error "Laravel development server tidak berjalan di port 8000!"
            return 1
        fi
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
    
    # Check if application is accessible via HTTP
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
    
    # Fetch latest changes dengan force
    log "Mengambil perubahan terbaru dari repository..."
    git fetch origin --force
    
    # Reset ke remote branch untuk memastikan sinkronisasi
    log "Memastikan sinkronisasi dengan remote repository..."
    git reset --hard "origin/$BRANCH"
    
    # Cek apakah ada update setelah reset
    LOCAL=$(git rev-parse HEAD)
    REMOTE=$(git rev-parse "origin/$BRANCH" 2>/dev/null)
    
    if [ -z "$REMOTE" ]; then
        error "Branch '$BRANCH' tidak ditemukan di remote repository!"
        exit 1
    fi
    
    log "Local commit: $LOCAL"
    log "Remote commit: $REMOTE"
    
    if [ "$LOCAL" = "$REMOTE" ]; then
        success "Aplikasi berhasil disinkronkan dengan remote repository!"
    else
        warning "Masih ada perbedaan antara local dan remote setelah reset"
    fi
    
    # Install/update dependencies jika composer.json ada
    if [ -f "composer.json" ]; then
        log "Menjalankan composer install..."
        composer install --no-dev --optimize-autoloader
    fi
    
    # Install/update NPM dependencies jika package.json ada
    if [ -f "package.json" ]; then
        log "Menjalankan npm install..."
        npm ci --production
        npm run build
    fi
    
    # Jalankan migrasi database
    log "Menjalankan migrasi database..."
    php artisan migrate --force
    
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
    
    # Restart PHP-FPM (tanpa sudo, skip jika gagal)
    if systemctl is-active --quiet php8.2-fpm 2>/dev/null; then
        systemctl restart php8.2-fpm 2>/dev/null || log "Warning: Tidak bisa restart PHP 8.2-FPM (skip)"
        log "PHP-FPM direstart"
    elif systemctl is-active --quiet php8.1-fpm 2>/dev/null; then
        systemctl restart php8.1-fpm 2>/dev/null || log "Warning: Tidak bisa restart PHP 8.1-FPM (skip)"
        log "PHP-FPM direstart"
    fi
    
    # Restart web server (tanpa sudo, skip jika gagal)
    if systemctl is-active --quiet nginx 2>/dev/null; then
        systemctl reload nginx 2>/dev/null || log "Warning: Tidak bisa reload Nginx (skip)"
        log "Nginx direload"
    elif systemctl is-active --quiet apache2 2>/dev/null; then
        systemctl restart apache2 2>/dev/null || log "Warning: Tidak bisa restart Apache2 (skip)"
        log "Apache2 direstart"
    fi
    
    # Restart queue workers jika ada (tanpa sudo, skip jika gagal)
    if systemctl is-active --quiet laravel-worker 2>/dev/null; then
        systemctl restart laravel-worker 2>/dev/null || log "Warning: Tidak bisa restart Laravel worker (skip)"
        log "Laravel queue worker direstart"
    fi
    
    log "Services restart selesai (dengan atau tanpa privilege)"
}

# Main execution
main() {
    # Parse arguments
    while [[ $# -gt 0 ]]; do
        case $1 in
            --help|-h)
                show_help
                exit 0
                ;;
            --branch)
                BRANCH="$2"
                shift 2
                ;;
            *)
                shift
                ;;
        esac
    done
    
    log "=== Memulai deployment update ==="
    log "Branch yang akan digunakan: $BRANCH"
    
    # Skip backup untuk development environment (Windows)
    if [ "$IS_WINDOWS" = true ]; then
        log "Development environment terdeteksi - melewati backup"
    else
        # Cek apakah script dijalankan sebagai user yang tepat
        if [ "$EUID" -eq 0 ]; then
            warning "Script dijalankan sebagai root. Pastikan permission file sudah benar."
        fi
        
        # Buat backup sebelum update (hanya untuk production)
        create_backup
    fi
    
    # Cek apakah direktori project ada
    if [ ! -d "$PROJECT_DIR" ]; then
        error "Direktori project tidak ditemukan: $PROJECT_DIR"
        exit 1
    fi
    
    # Update aplikasi
    if update_application; then
        # Restart services (hanya untuk production)
        if [ "$IS_WINDOWS" = false ]; then
            restart_services
        fi
        
        # Health check
        if health_check; then
            success "=== Deployment berhasil! ==="
            if [ "$IS_WINDOWS" = false ]; then
                log "Backup tersimpan di: $(cat $PROJECT_DIR/.last_backup)"
            fi
        else
            error "=== Health check gagal! ==="
            warning "Aplikasi mungkin tidak berjalan dengan baik."
            if [ "$IS_WINDOWS" = false ]; then
                warning "Gunakan script rollback.sh jika diperlukan."
            fi
            exit 1
        fi
    else
        error "=== Update gagal! ==="
        if [ "$IS_WINDOWS" = false ]; then
            warning "Gunakan script rollback.sh untuk mengembalikan ke versi sebelumnya."
        fi
        exit 1
    fi
}

# Fungsi show_help
show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --help, -h         Tampilkan bantuan ini"
    echo "  --branch <name>    Specify branch yang akan di-pull (default: main)"
    echo ""
    echo "Deskripsi:"
    echo "  Script untuk melakukan update aplikasi Laravel dengan:"
    echo "  - Backup otomatis (hanya production)"
    echo "  - Git pull untuk mendapatkan update terbaru"
    echo "  - Update dependencies (composer & npm)"
    echo "  - Database migration"
    echo "  - Cache clearing"
    echo "  - Health check"
    echo ""
    echo "Contoh penggunaan:"
    echo "  $0                    # Update dari branch main"
    echo "  $0 --branch develop   # Update dari branch develop"
    echo "  $0 --help            # Tampilkan bantuan"
    echo ""
}

# Jalankan script
main "$@"