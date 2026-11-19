<?php

/**
 * MainYuk! - Automated Secure Deploy Webhook & Manager
 */

// Secret Token
$secretKey = 'rapip_deploy_secret_key_884219';
$receivedKey = $_SERVER['HTTP_X_DEPLOY_KEY'] ?? $_REQUEST['token'] ?? '';

if (empty($receivedKey) || !hash_equals($secretKey, $receivedKey)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

// Root directory tempat artisan berada
$rootDir = __DIR__;
if (!file_exists($rootDir . '/artisan') && file_exists(dirname($rootDir) . '/artisan')) {
    $rootDir = dirname($rootDir);
}

// Deteksi binary PHP 8.4/8.3 di server
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

$action = $_REQUEST['action'] ?? (isset($_GET['ping']) ? 'ping' : 'deploy');

// 1. Health check (ping)
if ($action === 'ping') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'message' => 'Deploy webhook is ready.',
        'php_web_version' => PHP_VERSION,
        'php_cli_binary' => $phpBin,
        'has_env' => file_exists($rootDir . '/.env'),
        'server_time' => date('Y-m-d H:i:s'),
    ]);
    exit;
}

// 2. Fix Key Only (jika cuma butuh generate APP_KEY)
if ($action === 'key_generate') {
    if (!file_exists($rootDir . '/.env') && file_exists($rootDir . '/.env.example')) {
        copy($rootDir . '/.env.example', $rootDir . '/.env');
    }
    $out = [];
    exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan key:generate --force 2>&1", $out);
    exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan config:clear 2>&1", $out);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'artisan' => $out]);
    exit;
}

// 3. Deploy via Git Pull (Paling cepat, aman dari ModSecurity & bebas upload)
$logs = [];
if (is_dir($rootDir . '/.git')) {
    exec("cd " . escapeshellarg($rootDir) . " && git fetch origin main 2>&1", $logs);
    exec("cd " . escapeshellarg($rootDir) . " && git reset --hard origin/main 2>&1", $logs);
} else {
    // Jika upload package via POST
    if (isset($_FILES['package']) && $_FILES['package']['error'] === UPLOAD_ERR_OK) {
        $destTar = $rootDir . '/deploy.tar.gz';
        @move_uploaded_file($_FILES['package']['tmp_name'], $destTar);
        exec("cd " . escapeshellarg($rootDir) . " && tar -xzf deploy.tar.gz 2>&1", $logs);
        @unlink($destTar);
    } else {
        // Download zip dari GitHub archive
        $zipUrl = 'https://codeload.github.com/Rapip-NA/Game/zip/refs/heads/main';
        $tmpZip = $rootDir . '/temp_update.zip';
        $content = @file_get_contents($zipUrl);
        if ($content) {
            file_put_contents($tmpZip, $content);
            exec("unzip -o -q " . escapeshellarg($tmpZip) . " -d " . escapeshellarg($rootDir) . " 2>&1", $logs);
            exec("cp -rf " . escapeshellarg($rootDir . '/Game-main/') . "* " . escapeshellarg($rootDir) . "/ 2>&1", $logs);
            exec("rm -rf " . escapeshellarg($tmpZip) . " " . escapeshellarg($rootDir . '/Game-main'), $logs);
        }
    }
}

// Pastikan .env ada dan memiliki APP_KEY
if (!file_exists($rootDir . '/.env')) {
    if (file_exists($rootDir . '/.env.example')) {
        copy($rootDir . '/.env.example', $rootDir . '/.env');
    }
}

// Cek apakah APP_KEY masih kosong di .env
$envContent = @file_get_contents($rootDir . '/.env') ?: '';
if (!str_contains($envContent, 'APP_KEY=base64:')) {
    exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan key:generate --force 2>&1", $logs);
}

// Pastikan storage folder permissions
@mkdir($rootDir . '/storage/framework/sessions', 0775, true);
@mkdir($rootDir . '/storage/framework/views', 0775, true);
@mkdir($rootDir . '/storage/framework/cache/data', 0775, true);
@mkdir($rootDir . '/storage/logs', 0775, true);
@mkdir($rootDir . '/bootstrap/cache', 0775, true);
@chmod($rootDir . '/storage', 0775);
@chmod($rootDir . '/bootstrap/cache', 0775);

// Jalankan artisan
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan config:clear 2>&1", $logs);
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan route:clear 2>&1", $logs);
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan view:clear 2>&1", $logs);
exec("cd " . escapeshellarg($rootDir) . " && $phpBin artisan migrate --force 2>&1", $logs);

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'Deployment & artisan tasks completed!',
    'php_bin' => $phpBin,
    'logs' => $logs,
]);
