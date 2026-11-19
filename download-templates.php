<?php

$targetDir = __DIR__ . '/public/templates';
$checkFile = $targetDir . '/js/main.js';

if (file_exists($checkFile)) {
    echo "Templates already present at public/templates, skipping download.\n";
    exit(0);
}

echo "=== Downloading templates from Google Drive ===\n";

$fileId = '1JWO4uBdn4bjp7cZk75SNBzyx-DHZNGPZ';
$cookieFile = sys_get_temp_dir() . '/gdrive_cookies_' . uniqid() . '.txt';
$zipFile = sys_get_temp_dir() . '/templates_' . uniqid() . '.zip';

$url = 'https://drive.google.com/uc?export=download&id=' . $fileId;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$html = curl_exec($ch);
curl_close($ch);

if (preg_match('/name="uuid" value="([^"]+)"/', $html, $matches)) {
    $uuid = $matches[1];
    $downloadUrl = 'https://drive.usercontent.google.com/download?id=' . $fileId . '&export=download&confirm=t&uuid=' . $uuid;
} else {
    $downloadUrl = $url;
}

echo "Downloading archive...\n";

$fp = fopen($zipFile, 'w+');
$ch = curl_init($downloadUrl);
curl_setopt($ch, CURLOPT_TIMEOUT, 300);
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp);
@unlink($cookieFile);

if (!file_exists($zipFile) || filesize($zipFile) < 1000000) {
    echo "ERROR: Download failed or file too small (HTTP $httpCode).\n";
    @unlink($zipFile);
    exit(1);
}

echo "Downloaded " . round(filesize($zipFile) / (1024 * 1024), 2) . " MB. Extracting...\n";

$zip = new ZipArchive;
if ($zip->open($zipFile) === true) {
    $zip->extractTo(__DIR__ . '/public');
    $zip->close();
    if (PHP_OS_FAMILY !== 'Windows') {
        @exec('find ' . escapeshellarg(__DIR__ . '/public/templates') . ' -name "*Zone.Identifier*" -delete');
    }
    echo "Templates extracted successfully to public/templates!\n";
} else {
    echo "ERROR: Failed to open zip archive.\n";
    @unlink($zipFile);
    exit(1);
}

@unlink($zipFile);
