#!/bin/sh

# Menyiapkan direktori storage & bootstrap cache
mkdir -p storage/framework/sessions \
         storage/framework/views \
         storage/framework/cache/data \
         storage/logs \
         bootstrap/cache 2>/dev/null || true

chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# Pastikan symlink storage tersedia
php artisan storage:link 2>/dev/null || true

# Jalankan migrasi database jika database terkonfigurasi
if [ -n "$DB_DATABASE" ] || [ -n "$DATABASE_URL" ] || [ -n "$DB_URL" ] || [ "$DB_CONNECTION" = "sqlite" ]; then
    echo "=== Menjalankan Migrasi Database ==="
    php artisan migrate --force || echo "Migrasi gagal atau database belum siap, melanjutkan proses start..."

    if [ "$RUN_SEEDER" = "true" ]; then
        echo "=== Menjalankan Database Seeder ==="
        php artisan db:seed --force || echo "Seeder gagal atau sudah dijalankan..."
    fi
fi

# Jalankan server Laravel pada port yang ditentukan Cloudvea ($PORT)
TARGET_PORT="${PORT:-8000}"
echo "=== Menjalankan Laravel di 0.0.0.0:$TARGET_PORT ==="
exec php artisan serve --host=0.0.0.0 --port="$TARGET_PORT" --no-reload
