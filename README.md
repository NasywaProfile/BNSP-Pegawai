# Aplikasi Pencatatan Data Pegawai (Sertifikasi BNSP)

Aplikasi berbasis web untuk pencatatan dan pengelolaan data pegawai (CRUD) yang dibangun menggunakan arsitektur **Client-Server Asynchronous (AJAX)**. Proyek ini disusun untuk memenuhi standar Uji Kompetensi Keahlian / Sertifikasi Profesi BNSP skema **Junior Web Developer / Pemrograman Web**.

---

## 📋 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Arsitektur & Teknologi](#-arsitektur--teknologi)
3. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
4. [Struktur Folder Proyek](#-struktur-folder-proyek)
5. [Panduan Uji Asesmen BNSP (Naskah Presenter)](#-panduan-uji-asesmen-bnsp-naskah-presenter)
   * [Soal 1 (TS - Task Skills)](#soal-1-ts---task-skills)
   * [Soal 2 (TMS - Task Management Skills)](#soal-2-tms---task-management-skills)
   * [Soal 3 (CMS - Contingency Management Skills)](#soal-3-cms---contingency-management-skills)
   * [Soal 4 (JRES - Job Role / Environment Skills)](#soal-4-jres---job-role--environment-skills)
   * [Soal 5 (Transfer Skills)](#soal-5-transfer-skills)

---

## 🚀 Fitur Utama
* **Autentikasi Aman:** Sistem Login & Logout menggunakan sesi PHP (`$_SESSION`) dan enkripsi password Bcrypt (`password_verify`).
* **Dashboard Statistik:** Ringkasan jumlah pegawai dan daftar 5 pegawai terbaru.
* **Manajemen Pegawai (CRUD):** Tambah, lihat detail, perbarui data, dan hapus pegawai.
* **Pencarian Real-Time:** Filter data berdasarkan NIP, nama, atau departemen.
* **Tanpa Reload Halaman:** Seluruh interaksi form dan data tabel menggunakan jQuery AJAX.
* **Keamanan Berlapis:** Prepared Statement PDO (cegah SQL Injection) dan sanitasi XSS (`escapeHtml`).

---

## 🛠️ Arsitektur & Teknologi
* **Frontend:** HTML5, CSS3 kustom, Bootstrap 5.3, Bootstrap Icons, jQuery 3.7.1
* **Backend:** PHP Native (PDO MySQL, REST-like JSON API)
* **Database:** MySQL / MariaDB (via phpMyAdmin / XAMPP)

---

## ⚙️ Panduan Instalasi & Menjalankan

1. **Clone / Pindahkan Folder:**
   Pastikan folder proyek berada di direktori web server XAMPP:
   ```text
   C:\xampp\htdocs\BNSP_Pegawai
   ```

2. **Jalankan Apache & MySQL:**
   Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache** dan **MySQL**.

3. **Impor Database:**
   * Buka browser dan akses `http://localhost/phpmyadmin`.
   * Buat database baru bernama `db_pegawai` (atau biarkan script SQL membuatnya otomatis).
   * Klik tab **Import**, pilih file `database.sql` dari folder proyek, lalu klik **Go / Import**.

4. **Buka Aplikasi:**
   Akses melalui browser di alamat:
   ```text
   http://localhost/BNSP_Pegawai/
   ```

5. **Akun Login Bawaan:**
   * **Username:** `admin`
   * **Password:** `admin123`

---

## 📁 Struktur Folder Proyek

```text
BNSP_Pegawai/
├── index.html              # Pengalih otomatis ke login.html
├── login.html              # Halaman antarmuka login
├── dashboard.html          # Halaman ringkasan statistik
├── pegawai.html            # Halaman daftar tabel & pencarian pegawai
├── form_pegawai.html       # Halaman form tambah & edit pegawai
├── detail_pegawai.html     # Halaman informasi detail pegawai
├── database.sql            # Skema tabel database dan data awal
├── assets/
│   ├── pegawai.svg         # Ikon visual aplikasi
│   └── vendor/             # Library pihak ketiga (terisolasi)
│       ├── bootstrap/
│       ├── bootstrap-icons/
│       └── jquery/
├── css/
│   └── style.css           # Styling kustom & animasi
├── js/
│   └── app.js              # Logika interaksi frontend, validasi, & AJAX
└── php/                    # Backend API pemroses data
    ├── koneksi.php         # Inisialisasi PDO, Session, & helper JSON
    ├── auth.php            # Endpoint login, logout, & cek sesi
    └── pegawai.php         # Endpoint CRUD & ringkasan pegawai
```

---

## 🎙️ Panduan Uji Asesmen BNSP (Naskah Presenter)

Bagian ini dirancang sebagai panduan lisan saat Anda diuji dan diobservasi oleh Asesor BNSP. Setiap poin dilengkapi rujukan file, nomor baris, dan cuplikan kodenya.

---

### Soal 1 (TS - Task Skills)
> **Pertanyaan Asesor:**  
> *"Jelaskan langkah-langkah yang Anda lakukan dalam membangun tampilan antarmuka (UI) suatu form data, mulai dari penyusunan struktur HTML, penerapan CSS agar tampilan responsif, hingga penerapan perintah eksekusi (JavaScript) untuk validasi dan interaksi pengguna pada form tersebut?"*

#### 🗣️ Naskah Presentasi:
> "Baik Bapak/Ibu Asesor, dalam membangun form pegawai ini, saya menerapkan 3 langkah terstruktur:
>
> **1. Struktur HTML Semantik & Form Kontrol:**  
> Pada file `form_pegawai.html`:
> * Pada **baris 47–48**, saya mematikan validasi bawaan browser dan membuat input tersembunyi `id="id"` agar satu form ini bisa fleksibel digunakan untuk fungsi Tambah maupun Edit:
> ```html
> <form id="formPegawai" novalidate>
>     <input type="hidden" id="id">
> ```
> * Pada **baris 43**, saya menyiapkan wadah alert dinamis:
> ```html
> <div id="alertBox"></div>
> ```
>
> **2. CSS & Layout Responsif:**
> * Grid responsif saya atur di `form_pegawai.html` **baris 50–51** menggunakan class Bootstrap `.row` dan `.col-md-6` agar form otomatis menjadi 2 kolom di laptop dan 1 kolom di HP:
> ```html
> <div class="row">
>     <div class="col-md-6 mb-2">
> ```
> * Efek animasinya saya buat di `css/style.css` **baris 45–47**:
> ```css
> .fade-in {
>     animation: fadeIn 0.35s ease-in-out;
> }
> ```
>
> **3. Perintah Eksekusi JavaScript & Validasi:**  
> Pada file `js/app.js`:
> * Pada **baris 270–271**, saya mencegah reload halaman menggunakan `e.preventDefault()`:
> ```javascript
> $('#formPegawai').on('submit', function (e) {
>     e.preventDefault();
> ```
> * Pada **baris 284–303**, saya memvalidasi form secara bertingkat (panjang NIP minimal 5, Regex format email, dan nomor HP numerik):
> ```javascript
> if (payload.nip.length < 5) {
>     showAlert('#alertBox', 'NIP minimal 5 karakter.', 'warning');
>     return;
> }
> const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
> if (!emailRegex.test(payload.email)) { ... }
> ```
> * Pada **baris 307–311**, data dikirimkan menggunakan AJAX `POST` ke backend PHP tanpa reload halaman:
> ```javascript
> $.ajax({
>     url: `php/pegawai.php?action=${action}`,
>     method: 'POST',
>     data: payload,
> ```"

---

### Soal 2 (TMS - Task Management Skills)
> **Pertanyaan Asesor:**  
> *"Bagaimana Anda mengatur dan menyusun fungsi, file, serta struktur folder proyek agar tetap rapi dan terorganisir, sekaligus mengintegrasikan library atau komponen pre-existing (misalnya Bootstrap, jQuery, atau PDO) ke dalam proyek tanpa mengganggu struktur yang sudah disusun?"*

#### 🗣️ Naskah Presentasi:
> "Terkait pengaturan proyek dan komponen eksternal:
>
> **1. Struktur Folder (Separation of Concerns):**
> * Saya memisahkan file berdasarkan perannya: file HTML di folder utama, CSS di `/css`, JS di `/js`, dan backend di `/php`.
> * Khusus library pihak ketiga (Bootstrap dan jQuery), saya isolasi di dalam folder `assets/vendor/` agar tidak tercampur dan tidak tertimpa dengan kode buatan saya sendiri.
>
> **2. Urutan Pemuatan Library di Frontend:**  
> Pada file `form_pegawai.html`:
> * Pada **baris 8–10**, CSS Bootstrap dipanggil sebelum CSS kustom di `<head>` agar gaya kustom dapat menimpa gaya default:
> ```html
> <link href="assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
> <link href="css/style.css" rel="stylesheet">
> ```
> * Pada **baris 106–108**, jQuery dan Bootstrap JS dipanggil sebelum `app.js` di akhir `<body>`:
> ```html
> <script src="assets/vendor/jquery/jquery-3.7.1.min.js"></script>
> <script src="assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
> <script src="js/app.js"></script>
> ```
>
> **3. Integrasi PDO Terpusat di Backend:**
> * Inisialisasi koneksi PDO hanya saya buat satu kali di `php/koneksi.php` **baris 15**:
> ```php
> $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [...]);
> ```
> * File backend lain seperti `pegawai.php` cukup menyertakannya di **baris 6**:
> ```php
> require_once __DIR__ . '/koneksi.php';
> ```"

---

### Soal 3 (CMS - Contingency Management Skills)
> **Pertanyaan Asesor:**  
> *"Apa yang akan Anda lakukan apabila pada saat pengujian ditemukan bahwa perintah eksekusi program (misalnya validasi form atau animasi) tidak berjalan sesuai harapan, atau proses pemrograman terstruktur (CRUD) mengalami error saat berinteraksi dengan database? Bagaimana cara Anda menelusuri penyebab dan memperbaikinya?"*

#### 🗣️ Naskah Presentasi:
> "Jika terjadi error saat pengujian, saya menelusurinya secara sistematis:
>
> **1. Jika Masalah di Frontend (Validasi / Animasi Macet):**
> * Saya tekan **F12** &rarr; buka tab **Console** untuk membaca pesan error merah JavaScript seperti *SyntaxError* atau *TypeError*.
> * Saya cek tab **Elements** untuk memastikan ID selektor form (`#formPegawai`) atau kelas CSS `.fade-in` terpasang dengan benar di elemen HTML.
>
> **2. Jika Masalah di Komunikasi AJAX:**
> * Saya buka tab **Network** filter **Fetch/XHR** dan memeriksa **HTTP Status Code**:
>   * `400` = Input ada yang kosong / salah validasi server.
>   * `401` = Sesi login habis / belum login.
>   * `409` = NIP sudah terdaftar duplikat.
>   * `500` = Error di script PHP server.
> * Lalu saya klik tab **Response** untuk membaca pesan error JSON yang dikirimkan server.
>
> **3. Jika Masalah di Database (PDO / SQL):**  
> Pada file `php/koneksi.php` **baris 20–25**, saya sudah mengaktifkan penanganan eksepsi:
> ```php
> PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
> ```
> ```php
> } catch (PDOException $e) {
>     error_log('Database connection error: ' . $e->getMessage());
> ```
> * Jika query database bermasalah, pesan error langsung tercatat di log server (`error_log`), dan saya bisa menyalin query tersebut untuk diuji langsung di phpMyAdmin."

---

### Soal 4 (JRES - Job Role / Environment Skills)
> **Pertanyaan Asesor:**  
> *"Guidelines atau best practice penulisan kode apa saja yang Anda terapkan (misalnya penamaan variabel/fungsi, indentasi, dan pemberian komentar) agar kode yang Anda hasilkan mudah dibaca, dipahami, dan dipelihara oleh developer lain dalam sebuah tim kerja?"*

#### 🗣️ Naskah Presentasi:
> "Saya menerapkan best practice standar industri agar kode mudah dipahami oleh anggota tim lain:
>
> **1. Konvensi Penamaan yang Konsisten:**
> * Di JavaScript (`js/app.js` **baris 161**), fungsi menggunakan `camelCase`:
> ```javascript
> function initDaftarPegawai() { ... }
> ```
> * Di PHP (`php/pegawai.php` **baris 12**), konstanta menggunakan `UPPERCASE`:
> ```php
> const ALLOWED_DEPARTEMEN = [ ... ];
> ```
>
> **2. Dokumentasi & Komentar Standar:**  
> Di setiap awal fungsi penting saya sertakan komentar standar JSDoc/PHPDoc, contohnya di `php/koneksi.php` **baris 35–38**:
> ```php
> /**
>  * Mengirim respons JSON terstandar.
>  */
> function jsonResponse(bool $status, string $message, $data = null, int $httpCode = 200): void
> ```
>
> **3. Standar Keamanan Tim:**
> * **Cegah SQL Injection:** Semua query database di `php/pegawai.php` **baris 107–110** menggunakan PDO Prepared Statements:
> ```php
> $stmt = $pdo->prepare('INSERT INTO pegawai (...) VALUES (:nip, :nama, ...)');
> $stmt->execute($input);
> ```
> * **Cegah XSS:** Semua data dari database dibungkus fungsi sanitasi di `js/app.js` **baris 37–39** sebelum ditampilkan ke DOM:
> ```javascript
> function escapeHtml(text) {
>     return $('<div>').text(text ?? '').html();
> }
> ```"

---

### Soal 5 (Transfer Skills)
> **Pertanyaan Asesor:**  
> *"Jika aplikasi yang Anda kerjakan ini perlu dikembangkan untuk mengelola jenis data yang berbeda (misalnya dari data barang menjadi data siswa atau data transaksi), bagaimana Anda akan menyesuaikan struktur pemrograman terstruktur, tampilan antarmuka, dan library yang sudah digunakan agar tetap dapat diterapkan pada konteks aplikasi yang baru tersebut?"*

#### 🗣️ Naskah Presentasi:
> "Karena arsitektur aplikasi ini sudah dirancang modular, saya hanya perlu menyesuaikan 4 titik tanpa merombak sistem utama:
>
> **1. Database:**  
> Tabel `pegawai` diubah menjadi tabel `barang` dengan kolom: `id, kode_barang, nama_barang, kategori, harga, stok`.
>
> **2. Backend (`php/barang.php`):**  
> Mengadopsi alur `php/pegawai.php` **baris 107–111**:
> * Mengganti nama kolom SQL `nip, nama` menjadi `kode_barang, nama_barang, harga, stok`.
> * Validasi email diubah menjadi validasi angka `is_numeric()` untuk harga dan stok.
> * File `koneksi.php` dan `auth.php` **tidak perlu diubah sama sekali**.
>
> **3. Form HTML (`form_barang.html`):**  
> Mengadopsi struktur `form_pegawai.html` **baris 52–54**:
> * Input NIP diganti menjadi Kode Barang:
> ```html
> <label for="kode_barang">Kode Barang</label>
> <input type="text" id="kode_barang" class="form-control">
> ```
> * Dan input tanggal diganti menjadi input angka bertipe `number` untuk harga dan stok.
>
> **4. JavaScript (`js/app.js`):**  
> Mengadopsi fungsi `js/app.js` **baris 273–281**:
> * Membuat fungsi `initFormBarang()` dengan payload barang, memvalidasi agar harga lebih dari 0, lalu mengirimkan AJAX ke `php/barang.php`.
> * Fungsi helper seperti `showAlert()`, `escapeHtml()`, dan `cekSesi()` tetap dipakai langsung tanpa diubah."
