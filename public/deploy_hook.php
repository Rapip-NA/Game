<?php

/**
 * MainYuk! - Secure Deploy Webhook
 * Automated GitHub Actions deployment handler.
 */

// Konfigurasi Token Keamanan
$secretKey = 'rapip_deploy_secret_key_884219';

$receivedKey = $_SERVER['HTTP_X_DEPLOY_KEY'] ?? $_REQUEST['token'] ?? '';

if (empty($receivedKey) || !hash_equals($secretKey, $receivedKey)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

// 1. Health check endpoint (GET ?ping=1)
if (isset($_GET['ping'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'message' => 'Deploy webhook is ready.',
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s'),
    ]);
    exit;
}

// 2. Upload handling (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['package'])) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No deploy package attached.']);
    exit;
}

if ($_FILES['package']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Upload failed with code: ' . $_FILES['package']['error']]);
    exit;
}

// Root directory proyek (tempat file artisan berada)
$rootDir = __DIR__;
if (!file_exists($rootDir . '/artisan') && file_exists(dirname($rootDir) . '/artisan')) {
    $rootDir = dirname($rootDir);
}

$tmpUploaded = $_FILES['package']['tmp_name'];
$destTar = $rootDir . '/deploy.tar.gz';

if (!@move_uploaded_file($tmpUploaded, $destTar)) {
    @copy($tmpUploaded, $destTar);
}

if (!file_exists($destTar)) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Failed to write package to destination.']);
    exit;
}

// Ekstrak arsip (tidak akan menimpa .env karena .env dikecualikan saat pembuatan tar)
$extractLogs = [];
$returnCode = 0;
exec("cd " . escapeshellarg($rootDir) . " && tar -xzf deploy.tar.gz 2>&1", $extractLogs, $returnCode);
@unlink($destTar);

// Deteksi binary PHP terbaik di server (8.4 / 8.3 / 8.2)
$phpBin = 'php';
foreach ([
    '/usr/local/php84/bin/php',
    '/usr/local/php83/bin/php',
    '/usr/local/php82/bin/php',
    '/usr/local/bin/php84',
    '/usr/local/bin/php83',
    '/usr/local/bin/ea-php84',
    '/usr/local/bin/ea-php83',
    '/opt/alt/php84/usr/bin/php',
    '/opt/alt/php83/usr/bin/php',
    '/opt/cpanel/ea-php84/root/usr/bin/php',
    '/opt/cpanel/ea-php83/root/usr/bin/php',
    '/usr/bin/php84',
    '/usr/bin/php83',
    '/usr/bin/php'
] as $candidate) {
    if (@is_executable($candidate)) {
        $v = @shell_exec("$candidate -r 'echo PHP_VERSION;' 2>/dev/null");
        if ($v && (str_starts_with($v, '8.4') || str_starts_with($v, '8.3') || str_starts_with($v, '8.2'))) {
            $phpBin = $candidate;
            break;
        }
    }
}

// Pastikan file .env ada jika belum pernah dibuat sama sekali
$envCreated = false;
if (!file_exists($rootDir . '/.env') && file_exists($rootDir . '/.env.example')) {
    @copy($rootDir . '/.env.example', $rootDir . '/.env');
    @exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan key:generate --force 2>&1");
    $envCreated = true;
}

// Pastikan struktur folder storage ada & writable
@mkdir($rootDir . '/storage/framework/sessions', 0775, true);
@mkdir($rootDir . '/storage/framework/views', 0775, true);
@mkdir($rootDir . '/storage/framework/cache/data', 0775, true);
@mkdir($rootDir . '/storage/logs', 0775, true);
@mkdir($rootDir . '/bootstrap/cache', 0775, true);
@chmod($rootDir . '/storage', 0775);
@chmod($rootDir . '/bootstrap/cache', 0775);

// Jalankan artisan commands
$artisanOutput = [];
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan config:clear 2>&1", $artisanOutput);
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan route:clear 2>&1", $artisanOutput);
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan view:clear 2>&1", $artisanOutput);
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan migrate --force 2>&1", $artisanOutput);

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Aplikasi berhasil di-deploy!',
    'php_binary' => $phpBin,
    'php_version' => PHP_VERSION,
    'env_status' => $envCreated ? 'created_new' : 'preserved_existing',
    'artisan_logs' => $artisanOutput,
]);
