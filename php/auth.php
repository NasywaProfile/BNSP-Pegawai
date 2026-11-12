<?php
/**
 * Endpoint autentikasi: login, logout, dan cek sesi pengguna.
 */

require_once __DIR__ . '/koneksi.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            jsonResponse(false, 'Username dan password wajib diisi.', null, 400);
        }

        $stmt = $pdo->prepare('SELECT id, username, password, nama_lengkap FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            jsonResponse(false, 'Username atau password salah.', null, 401);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

        jsonResponse(true, 'Login berhasil.', [
            'username' => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
        ]);
        break;

    case 'logout':
        session_unset();
        session_destroy();
        jsonResponse(true, 'Logout berhasil.');
        break;

    case 'check':
        if (!empty($_SESSION['user_id'])) {
            jsonResponse(true, 'Sudah login.', [
                'username' => $_SESSION['username'],
                'nama_lengkap' => $_SESSION['nama_lengkap'],
            ]);
        }
        jsonResponse(false, 'Belum login.', null, 401);
        break;

    default:
        jsonResponse(false, 'Aksi autentikasi tidak dikenal.', null, 400);
}
