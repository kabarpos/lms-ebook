#!/bin/bash

# =============================================================================
# Script untuk memperbaiki permission deployment di VPS Production
# =============================================================================

set -e

# Konfigurasi
PROJECT_DIR="/var/www/dscourse"
BACKUP_DIR="/var/backups/dscourse"
WEB_USER="www-data"  # Sesuaikan dengan web server user (nginx/apache)
DEPLOY_USER="dscourse"  # User yang menjalankan deployment

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Fungsi untuk memperbaiki permission project directory
fix_project_permissions() {
    log "Memperbaiki permission untuk project directory..."
    
    if [ ! -d "$PROJECT_DIR" ]; then
        error "Project directory tidak ditemukan: $PROJECT_DIR"
        return 1
    fi
    
    # Set ownership untuk project directory
    sudo chown -R $DEPLOY_USER:$WEB_USER "$PROJECT_DIR"
    
    # Set permission untuk directories
    sudo find "$PROJECT_DIR" -type d -exec chmod 755 {} \;
    
    # Set permission untuk files
    sudo find "$PROJECT_DIR" -type f -exec chmod 644 {} \;
    
    # Set permission khusus untuk storage dan bootstrap/cache
    sudo chmod -R 775 "$PROJECT_DIR/storage"
    sudo chmod -R 775 "$PROJECT_DIR/bootstrap/cache"
    
    # Set permission untuk artisan
    sudo chmod +x "$PROJECT_DIR/artisan"
    
    success "Permission project directory berhasil diperbaiki"
}

# Fungsi untuk setup backup directory
setup_backup_directory() {
    log "Setup backup directory..."
    
    # Buat backup directory jika belum ada
    sudo mkdir -p "$BACKUP_DIR"
    
    # Set ownership dan permission
    sudo chown -R $DEPLOY_USER:$DEPLOY_USER "$BACKUP_DIR"
    sudo chmod -R 755 "$BACKUP_DIR"
    
    success "Backup directory berhasil di-setup"
}

# Fungsi untuk setup log directory
setup_log_directory() {
    log "Setup log directory dalam project..."
    
    # Pastikan storage/logs directory ada
    sudo mkdir -p "$PROJECT_DIR/storage/logs"
    
    # Set ownership dan permission
    sudo chown -R $DEPLOY_USER:$WEB_USER "$PROJECT_DIR/storage/logs"
    sudo chmod -R 775 "$PROJECT_DIR/storage/logs"
    
    success "Log directory berhasil di-setup"
}

# Fungsi untuk memberikan sudo access untuk service management
setup_sudo_access() {
    log "Setup sudo access untuk service management..."
    
    # Buat sudoers file untuk deployment user
    sudo tee "/etc/sudoers.d/deploy-$DEPLOY_USER" > /dev/null <<EOF
# Allow $DEPLOY_USER to manage services for deployment
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl restart php8.2-fpm
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl restart php8.1-fpm
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl reload nginx
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl restart apache2
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl restart laravel-worker
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl is-active php8.2-fpm
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl is-active php8.1-fpm
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl is-active nginx
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl is-active apache2
$DEPLOY_USER ALL=(ALL) NOPASSWD: /bin/systemctl is-active laravel-worker
EOF
    
    success "Sudo access berhasil di-setup"
}

# Main function
main() {
    log "=== Memperbaiki Permission untuk Deployment ==="
    
    # Cek apakah script dijalankan dengan privilege yang cukup
    if [ "$EUID" -ne 0 ] && ! sudo -n true 2>/dev/null; then
        error "Script ini memerlukan sudo access. Jalankan dengan sudo atau pastikan user memiliki sudo access."
        exit 1
    fi
    
    # Cek apakah user deployment ada
    if ! id "$DEPLOY_USER" &>/dev/null; then
        error "User deployment tidak ditemukan: $DEPLOY_USER"
        exit 1
    fi
    
    # Cek apakah web user ada
    if ! id "$WEB_USER" &>/dev/null; then
        warning "Web user tidak ditemukan: $WEB_USER. Menggunakan $DEPLOY_USER sebagai fallback."
        WEB_USER="$DEPLOY_USER"
    fi
    
    # Jalankan perbaikan
    fix_project_permissions
    setup_backup_directory
    setup_log_directory
    setup_sudo_access
    
    success "=== Permission berhasil diperbaiki! ==="
    log "Sekarang Anda dapat menjalankan update.sh tanpa masalah permission"
}

# Jalankan script
main "$@"