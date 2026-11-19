#!/bin/sh
set -e

echo "=== Menyiapkan Direktori & Cache ==="
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache/data \
         storage/logs \
         bootstrap/cache

# Pastikan symlink storage tersedia
php artisan storage:link || true

# Jalankan migrasi database jika database terkonfigurasi
if [ -n "$DB_DATABASE" ] || [ -n "$DATABASE_URL" ] || [ -n "$DB_URL" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "=== Menjalankan Migrasi Database ==="
    php artisan migrate --force || echo "Migrasi gagal atau database belum siap, melanjutkan proses start..."

    # Jalankan seeder jika RUN_SEEDER diset ke true pada environment
    if [ "$RUN_SEEDER" = "true" ]; then
        echo "=== Menjalankan Database Seeder ==="
        php artisan db:seed --force || echo "Seeder gagal atau sudah dijalankan..."
    fi
fi

# Cache konfigurasi, route, dan view untuk performa production
echo "=== Optimasi Cache Laravel ==="
php artisan config:clear || true
php artisan route:cache || true
php artisan view:cache || true

# Menjalankan server Laravel pada port yang ditentukan Cloudvea ($PORT)
TARGET_PORT="${PORT:-8000}"
echo "=== Menjalankan Laravel di 0.0.0.0:$TARGET_PORT ==="
exec php artisan serve --host=0.0.0.0 --port="$TARGET_PORT" --no-reload
