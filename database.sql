CREATE DATABASE IF NOT EXISTS db_pegawai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_pegawai;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL
);

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
);

-- Akun default: admin / admin123
INSERT IGNORE INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$LOX7fKM14/uyNlaNRUpXpOTIDSZnccngBopbrhRXnEMhGFw.Cpv5O', 'Administrator');

-- Contoh data pegawai awal
INSERT IGNORE INTO pegawai (nip, nama, jabatan, departemen, email, no_hp, tanggal_masuk) VALUES
('19900101001', 'Budi Santoso', 'Staff IT', 'Teknologi Informasi', 'budi.santoso@kantor.id', '081234567890', '2022-03-15'),
('19920512002', 'Siti Rahmawati', 'HRD Specialist', 'Sumber Daya Manusia', 'siti.rahma@kantor.id', '081345678901', '2023-01-10'),
('19950820003', 'Andi Wijaya', 'Staff Keuangan', 'Keuangan', 'andi.wijaya@kantor.id', '081567890123', '2024-06-01');
