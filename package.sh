#!/bin/bash
set -e

ZIP_NAME="bioskop-lara-cloudvea.zip"

echo "==> Membersihkan file cache sementara lokal..."
rm -f bootstrap/cache/*.php
rm -f storage/logs/*.log
rm -f "$ZIP_NAME"

echo "==> Memastikan symlink storage berbentuk relatif..."
if [ -L "public/storage" ]; then
    rm -f public/storage
fi
(cd public && ln -s ../storage/app/public storage)

echo "==> Memastikan izin eksekusi start.sh..."
chmod +x start.sh

echo "==> Mengompres project ke $ZIP_NAME..."
zip -r "$ZIP_NAME" . \
  -x "vendor/*" \
  -x "node_modules/*" \
  -x ".git/*" \
  -x ".agents/*" \
  -x ".env" \
  -x "*.zip" \
  -x "storage/logs/*.log" \
  -x "bootstrap/cache/*.php" \
  -x ".phpunit.cache/*" \
  -x "tests/*" \
  -x "public/templates/*"

echo "==> Berhasil dibuat: $ZIP_NAME"
ls -lh "$ZIP_NAME"
