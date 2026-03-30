<?php
/**
 * Apply database indexes from database_indexes.sql
 * Run from CLI: php scripts/apply_database_indexes.php
 * Or from web (replace with your path): /scripts/apply_database_indexes.php
 *
 * Requires: mysqli extension
 * Uses .env or defaults from config
 */
$baseDir = dirname(__DIR__);
$envFile = $baseDir . DIRECTORY_SEPARATOR . '.env';

if (is_file($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val, " \t\n\r\0\x0B\"'");
            if ($key !== '' && !array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $val;
                putenv("$key=$val");
            }
        }
    }
}

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USERNAME') ?: 'auluslar_5bolge_ekurum';
$pass = getenv('DB_PASSWORD') ?: 'Ek571632.EKURUM';
$db   = getenv('DB_DATABASE') ?: 'u201870050_uuuumu';

$conn = @mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    fwrite(STDERR, "DB bağlantı hatası: " . mysqli_connect_error() . "\n");
    exit(1);
}

mysqli_set_charset($conn, 'utf8');

$sqlFile = $baseDir . DIRECTORY_SEPARATOR . 'database_indexes.sql';
if (!is_file($sqlFile)) {
    fwrite(STDERR, "database_indexes.sql bulunamadı: $sqlFile\n");
    exit(1);
}

$content = file_get_contents($sqlFile);
$lines = explode("\n", $content);
$statements = [];
$buf = '';
foreach ($lines as $line) {
    $line = preg_replace('/--.*$/', '', $line);
    $buf .= $line . "\n";
    if (substr(trim($line), -1) === ';') {
        $stmt = trim(rtrim(trim($buf), ';'));
        if (preg_match('/^\s*CREATE\s+INDEX/i', $stmt)) {
            $statements[] = $stmt;
        }
        $buf = '';
    }
}
if (trim($buf) && preg_match('/^\s*CREATE\s+INDEX/i', $buf)) {
    $statements[] = trim(rtrim(trim($buf), ';'));
}

$ok = 0;
$err = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '') continue;
    if (mysqli_query($conn, $stmt)) {
        preg_match('/CREATE\s+INDEX\s+(\S+)/i', $stmt, $m);
        $name = $m[1] ?? '?';
        echo "[OK] $name\n";
        $ok++;
    } else {
        $code = mysqli_errno($conn);
        $msg = mysqli_error($conn);
        if ($code == 1061) {
            echo "[SKIP] $msg (zaten var)\n";
        } else {
            echo "[HATA] $msg\n";
            $err++;
        }
    }
}

mysqli_close($conn);
echo "\nSonuç: $ok oluşturuldu" . ($err ? ", $err hata" : "") . "\n";
exit($err ? 1 : 0);
