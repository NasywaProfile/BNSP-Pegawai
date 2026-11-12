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
    // Koneksi awal ke server MySQL untuk memastikan database tersedia
    $pdo = new PDO(
        "mysql:host={$dbHost};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Buat database dan tabel otomatis jika belum ada
    $dbExists = $pdo->query("SHOW DATABASES LIKE '{$dbName}'")->fetch();
    if (!$dbExists) {
        $pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    $pdo->exec("USE `{$dbName}`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            nama_lengkap VARCHAR(100) NOT NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pegawai (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nip VARCHAR(30) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            jabatan VARCHAR(100) NOT NULL,
            departemen VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            no_hp VARCHAR(20) NOT NULL,
            tanggal_masuk DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Isi akun default jika tabel users masih kosong
    $userCount = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount === 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES (?, ?, ?)");
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'Administrator']);

        // Isi contoh data pegawai awal saat inisialisasi pertama
        $pegawaiCount = (int) $pdo->query("SELECT COUNT(*) FROM pegawai")->fetchColumn();
        if ($pegawaiCount === 0) {
            $seed = $pdo->prepare("
                INSERT INTO pegawai (nip, nama, jabatan, departemen, email, no_hp, tanggal_masuk)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $seed->execute(['19900101001', 'Budi Santoso', 'Staff IT', 'Teknologi Informasi', 'budi.santoso@kantor.id', '081234567890', '2022-03-15']);
            $seed->execute(['19920512002', 'Siti Rahmawati', 'HRD Specialist', 'Sumber Daya Manusia', 'siti.rahma@kantor.id', '081345678901', '2023-01-10']);
            $seed->execute(['19950820003', 'Andi Wijaya', 'Staff Keuangan', 'Keuangan', 'andi.wijaya@kantor.id', '081567890123', '2024-06-01']);
        }
    }
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Koneksi database gagal: ' . $e->getMessage(),
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
