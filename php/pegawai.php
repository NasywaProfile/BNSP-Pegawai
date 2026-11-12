<?php
/**
 * Endpoint CRUD Data Pegawai dan statistik dashboard menggunakan PDO.
 */

require_once __DIR__ . '/koneksi.php';

requireLogin();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

/**
 * Mengambil dan memvalidasi input form pegawai dari POST.
 */
function getValidatedInput(): array
{
    $data = [
        'nip'           => trim($_POST['nip'] ?? ''),
        'nama'          => trim($_POST['nama'] ?? ''),
        'jabatan'       => trim($_POST['jabatan'] ?? ''),
        'departemen'    => trim($_POST['departemen'] ?? ''),
        'email'         => trim($_POST['email'] ?? ''),
        'no_hp'         => trim($_POST['no_hp'] ?? ''),
        'tanggal_masuk' => trim($_POST['tanggal_masuk'] ?? ''),
    ];

    foreach ($data as $field => $value) {
        if ($value === '') {
            jsonResponse(false, "Field {$field} wajib diisi.", null, 400);
        }
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Format email tidak valid.', null, 400);
    }

    return $data;
}

switch ($action) {
    case 'summary':
        $totalPegawai = (int) $pdo->query('SELECT COUNT(*) FROM pegawai')->fetchColumn();
        $totalDepartemen = (int) $pdo->query('SELECT COUNT(DISTINCT departemen) FROM pegawai')->fetchColumn();
        $terbaru = $pdo->query('SELECT id, nip, nama, jabatan, departemen FROM pegawai ORDER BY id DESC LIMIT 5')->fetchAll();

        jsonResponse(true, 'Data ringkasan berhasil dimuat.', [
            'total_pegawai'    => $totalPegawai,
            'total_departemen' => $totalDepartemen,
            'terbaru'          => $terbaru,
        ]);
        break;

    case 'list':
        $q = trim($_GET['q'] ?? '');
        if ($q !== '') {
            $like = "%{$q}%";
            $stmt = $pdo->prepare('
                SELECT * FROM pegawai
                WHERE nip LIKE ? OR nama LIKE ? OR jabatan LIKE ? OR departemen LIKE ?
                ORDER BY id DESC
            ');
            $stmt->execute([$like, $like, $like, $like]);
        } else {
            $stmt = $pdo->query('SELECT * FROM pegawai ORDER BY id DESC');
        }

        jsonResponse(true, 'Daftar pegawai berhasil dimuat.', $stmt->fetchAll());
        break;

    case 'detail':
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse(false, 'ID pegawai tidak valid.', null, 400);
        }

        $stmt = $pdo->prepare('SELECT * FROM pegawai WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $pegawai = $stmt->fetch();

        if (!$pegawai) {
            jsonResponse(false, 'Data pegawai tidak ditemukan.', null, 404);
        }

        jsonResponse(true, 'Detail pegawai berhasil dimuat.', $pegawai);
        break;

    case 'create':
        $input = getValidatedInput();

        $cek = $pdo->prepare('SELECT id FROM pegawai WHERE nip = ? LIMIT 1');
        $cek->execute([$input['nip']]);
        if ($cek->fetch()) {
            jsonResponse(false, 'NIP sudah terdaftar.', null, 409);
        }

        $stmt = $pdo->prepare('
            INSERT INTO pegawai (nip, nama, jabatan, departemen, email, no_hp, tanggal_masuk)
            VALUES (:nip, :nama, :jabatan, :departemen, :email, :no_hp, :tanggal_masuk)
        ');
        $stmt->execute($input);

        jsonResponse(true, 'Data pegawai berhasil ditambahkan.', ['id' => $pdo->lastInsertId()], 201);
        break;

    case 'update':
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse(false, 'ID pegawai tidak valid.', null, 400);
        }

        $input = getValidatedInput();

        $cek = $pdo->prepare('SELECT id FROM pegawai WHERE nip = ? AND id != ? LIMIT 1');
        $cek->execute([$input['nip'], $id]);
        if ($cek->fetch()) {
            jsonResponse(false, 'NIP sudah digunakan oleh pegawai lain.', null, 409);
        }

        $input['id'] = $id;
        $stmt = $pdo->prepare('
            UPDATE pegawai
            SET nip = :nip,
                nama = :nama,
                jabatan = :jabatan,
                departemen = :departemen,
                email = :email,
                no_hp = :no_hp,
                tanggal_masuk = :tanggal_masuk
            WHERE id = :id
        ');
        $stmt->execute($input);

        jsonResponse(true, 'Data pegawai berhasil diperbarui.');
        break;

    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            jsonResponse(false, 'ID pegawai tidak valid.', null, 400);
        }

        $stmt = $pdo->prepare('DELETE FROM pegawai WHERE id = ?');
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            jsonResponse(false, 'Data pegawai tidak ditemukan.', null, 404);
        }

        jsonResponse(true, 'Data pegawai berhasil dihapus.');
        break;

    default:
        jsonResponse(false, 'Aksi tidak dikenal.', null, 400);
}
