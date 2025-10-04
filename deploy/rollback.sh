#!/bin/bash

# =============================================================================
# LMS E-Book Rollback Script
# =============================================================================
# Script untuk mengembalikan aplikasi ke backup sebelumnya
# jika terjadi masalah setelah update
# =============================================================================

set -e  # Exit on any error

# Deteksi environment dan set konfigurasi
if [[ "$OSTYPE" == "msys" || "$OSTYPE" == "cygwin" ]]; then
    # Windows environment
    PROJECT_DIR="$(pwd)"
    BACKUP_DIR="$PROJECT_DIR/storage/backups"
    LOG_FILE="$PROJECT_DIR/storage/logs/deploy.log"
    HEALTH_CHECK_URL="http://localhost:8000"
    IS_WINDOWS=true
else
    # Linux/Production environment
    PROJECT_DIR="/var/www/dscourse"
    BACKUP_DIR="/var/backups/dscourse"
    LOG_FILE="/var/log/dscourse-deploy.log"
    HEALTH_CHECK_URL="https://dscourse.top/health-check"
    IS_WINDOWS=false
fi

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

# Fungsi untuk menampilkan daftar commit
list_commits() {
    log "Daftar commit yang tersedia:"
    echo ""
    
    # Ambil 10 commit terakhir
    local commits=$(git log --oneline -10 --pretty=format:"%h %s (%cr)")
    
    if [ -z "$commits" ]; then
        error "Tidak ada commit yang tersedia!"
        exit 1
    fi
    
    local counter=1
    while IFS= read -r commit; do
        echo -e "${BLUE}[$counter]${NC} $commit"
        counter=$((counter + 1))
    done <<< "$commits"
    echo ""
}

# Fungsi untuk memilih commit
select_commit() {
    list_commits
    
    local commit_array=($(git log --oneline -10 --pretty=format:"%h"))
    local commit_count=${#commit_array[@]}
    
    if [ "$commit_count" -eq 0 ]; then
        error "Tidak ada commit yang tersedia!"
        exit 1
    fi
    
    # Jika ada parameter, gunakan sebagai pilihan
    if [ ! -z "$1" ]; then
        if [[ "$1" =~ ^[0-9]+$ ]] && [ "$1" -ge 1 ] && [ "$1" -le "$commit_count" ]; then
            selected_commit="${commit_array[$((1-1))]}"
            log "Menggunakan commit: $selected_commit"
            return 0
        else
            error "Pilihan commit tidak valid: $1"
            exit 1
        fi
    fi
    
    # Interactive selection
    echo -n "Pilih commit yang akan digunakan (1-$commit_count) atau 'latest' untuk commit terbaru: "
    read choice
    
    if [ "$choice" = "latest" ]; then
        selected_commit="${commit_array[0]}"
    elif [[ "$choice" =~ ^[0-9]+$ ]] && [ "$choice" -ge 1 ] && [ "$choice" -le "$commit_count" ]; then
        selected_commit="${commit_array[$((choice-1))]}"
    else
        error "Pilihan tidak valid!"
        exit 1
    fi
    
    log "Commit yang dipilih: $selected_commit"
}

# Fungsi untuk konfirmasi rollback
confirm_rollback() {
    warning "PERINGATAN: Rollback akan mengganti semua file aplikasi dengan backup!"
    warning "Semua perubahan setelah backup akan hilang!"
    echo ""
    echo -n "Apakah Anda yakin ingin melanjutkan rollback? (yes/no): "
    read confirmation
    
    if [ "$confirmation" != "yes" ]; then
        log "Rollback dibatalkan oleh user"
        exit 0
    fi
}

# Fungsi untuk backup current state sebelum rollback
backup_current_state() {
    local emergency_backup="emergency_backup_$(date +%Y%m%d_%H%M%S)"
    local emergency_path="$BACKUP_DIR/$emergency_backup"
    
    log "Membuat emergency backup dari state saat ini: $emergency_backup"
    
    mkdir -p "$BACKUP_DIR"
    cp -r "$PROJECT_DIR" "$emergency_path"
    
    success "Emergency backup dibuat: $emergency_backup"
}

# Fungsi untuk restore aplikasi
restore_application() {
    local backup_path="$BACKUP_DIR/$selected_backup"
    
    if [ ! -d "$backup_path" ]; then
        error "Backup tidak ditemukan: $backup_path"
        exit 1
    fi
    
    log "Memulai restore dari backup: $selected_backup"
    
    # Stop services untuk mencegah konflik
    log "Menghentikan services sementara..."
    
    if systemctl is-active --quiet php8.2-fpm; then
        systemctl stop php8.2-fpm
    elif systemctl is-active --quiet php8.1-fpm; then
        systemctl stop php8.1-fpm
    fi
    
    if systemctl is-active --quiet laravel-worker; then
        systemctl stop laravel-worker
    fi
    
    # Backup current state
    backup_current_state
    
    # Restore files
    log "Mengembalikan file aplikasi..."
    
    # Hapus direktori lama (kecuali .env dan storage)
    find "$PROJECT_DIR" -mindepth 1 -maxdepth 1 ! -name '.env' ! -name 'storage' -exec rm -rf {} +
    
    # Copy files dari backup (kecuali .env dan storage)
    find "$backup_path" -mindepth 1 -maxdepth 1 ! -name '.env' ! -name 'storage' -exec cp -r {} "$PROJECT_DIR/" \;
    
    # Set proper permissions
    chown -R www-data:www-data "$PROJECT_DIR"
    chmod -R 755 "$PROJECT_DIR"
    chmod -R 775 "$PROJECT_DIR/storage"
    chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
    
    success "File aplikasi berhasil dikembalikan"
}

# Fungsi untuk restore database
restore_database() {
    local backup_path="$BACKUP_DIR/$selected_backup"
    local db_backup="$backup_path/database_backup.sql"
    
    if [ ! -f "$db_backup" ]; then
        warning "Database backup tidak ditemukan, skip restore database"
        return 0
    fi
    
    if [ ! -f "$PROJECT_DIR/.env" ]; then
        error "File .env tidak ditemukan!"
        return 1
    fi
    
    # Baca konfigurasi database
    DB_NAME=$(grep DB_DATABASE "$PROJECT_DIR/.env" | cut -d '=' -f2)
    DB_USER=$(grep DB_USERNAME "$PROJECT_DIR/.env" | cut -d '=' -f2)
    DB_PASS=$(grep DB_PASSWORD "$PROJECT_DIR/.env" | cut -d '=' -f2)
    
    if [ -z "$DB_NAME" ]; then
        warning "Konfigurasi database tidak ditemukan, skip restore database"
        return 0
    fi
    
    log "Mengembalikan database: $DB_NAME"
    
    # Konfirmasi restore database
    echo -n "Apakah Anda ingin restore database juga? (yes/no): "
    read db_confirmation
    
    if [ "$db_confirmation" = "yes" ]; then
        # Backup database saat ini
        log "Backup database saat ini..."
        mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$backup_path/current_database_backup.sql"
        
        # Restore database
        mysql -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$db_backup"
        success "Database berhasil dikembalikan"
    else
        log "Skip restore database"
    fi
}

# Fungsi untuk restart services
restart_services() {
    log "Restart services..."
    
    # Start PHP-FPM
    if systemctl is-enabled --quiet php8.2-fpm; then
        systemctl start php8.2-fpm
        log "PHP-FPM distart"
    elif systemctl is-enabled --quiet php8.1-fpm; then
        systemctl start php8.1-fpm
        log "PHP-FPM distart"
    fi
    
    # Restart web server
    if systemctl is-active --quiet nginx; then
        systemctl reload nginx
        log "Nginx direload"
    elif systemctl is-active --quiet apache2; then
        systemctl restart apache2
        log "Apache2 direstart"
    fi
    
    # Start queue workers jika ada
    if systemctl is-enabled --quiet laravel-worker; then
        systemctl start laravel-worker
        log "Laravel queue worker distart"
    fi
}

# Fungsi health check setelah rollback
health_check() {
    log "Melakukan health check setelah rollback..."
    
    cd "$PROJECT_DIR"
    
    # Check Laravel application
    if ! php artisan --version > /dev/null 2>&1; then
        error "Laravel application tidak dapat diakses!"
        return 1
    fi
    
    # Check database connection
    if ! php artisan migrate:status > /dev/null 2>&1; then
        error "Database connection gagal!"
        return 1
    fi
    
    # Clear cache
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    
    success "Health check berhasil!"
    return 0
}

# Fungsi untuk menampilkan bantuan
show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -l, --list              Tampilkan daftar commit yang tersedia"
    echo "  -r, --rollback [NUM]    Rollback ke commit tertentu (NUM = nomor commit)"
    echo "  -h, --help              Tampilkan bantuan ini"
    echo ""
    echo "Examples:"
    echo "  $0 --list              # Tampilkan daftar commit"
    echo "  $0 --rollback          # Rollback interaktif"
    echo "  $0 --rollback 1        # Rollback ke commit nomor 1"
    echo ""
}

# Main execution
main() {
    case "${1:-}" in
        -l|--list)
            list_commits
            exit 0
            ;;
        -r|--rollback)
            log "=== Memulai rollback ==="
            select_commit "$2"
            confirm_rollback
            backup_current_state
            restore_application
            restart_services
            if health_check; then
                success "=== Rollback berhasil! ==="
                log "Aplikasi telah dikembalikan ke commit: $selected_commit"
            else
                error "=== Health check gagal setelah rollback! ==="
                warning "Periksa log aplikasi untuk detail error."
            fi
            ;;
        -h|--help)
            show_help
            exit 0
            ;;
        "")
            # Default behavior - interactive rollback
            log "=== Memulai rollback ==="
            select_commit
            confirm_rollback
            backup_current_state
            restore_application
            restart_services
            if health_check; then
                success "=== Rollback berhasil! ==="
                log "Aplikasi telah dikembalikan ke commit: $selected_commit"
            else
                error "=== Health check gagal setelah rollback! ==="
                warning "Periksa log aplikasi untuk detail error."
            fi
            ;;
        *)
            error "Option tidak dikenal: $1"
            show_help
            exit 1
            ;;
    esac
}

# Cek apakah script dijalankan sebagai user yang tepat
if [ "$EUID" -eq 0 ]; then
    warning "Script dijalankan sebagai root. Pastikan permission file sudah benar."
fi

# Cek apakah direktori project ada
if [ ! -d "$PROJECT_DIR" ]; then
    error "Direktori project tidak ditemukan: $PROJECT_DIR"
    exit 1
fi

# Jalankan script
main "$@"