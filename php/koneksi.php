<?php
/**
 * Konfigurasi koneksi database MySQL menggunakan PDO
 * dan helper umum untuk respons JSON serta autentikasi sesi.
 */

session_start();

$dbHost = 'localhost';
$dbName = 'db_pegawai';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log('Database connection error: ' . $e->getMessage());
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Koneksi database gagal. Silakan periksa konfigurasi server atau database.',
    ]);
    exit;
}

/**
 * Mengirim respons JSON terstandar.
 */
function jsonResponse(bool $status, string $message, $data = null, int $httpCode = 200): void
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($httpCode);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ]);
    exit;
}

/**
 * Memastikan pengguna sudah login sebelum mengakses endpoint terproteksi.
 */
function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        jsonResponse(false, 'Sesi berakhir. Silakan login kembali.', null, 401);
    }
}
