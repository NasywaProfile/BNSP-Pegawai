/**
 * Logika antarmuka dan AJAX (jQuery) untuk aplikasi Pencatatan Data Pegawai.
 */

$(document).ready(function () {
    const page = $('body').data('page');

    if (page === 'login') {
        initLogin();
    } else if (page) {
        cekSesi(function (user) {
            $('#namaUser').text(user.nama_lengkap || user.username);
            if (page === 'dashboard') initDashboard();
            if (page === 'pegawai') initDaftarPegawai();
            if (page === 'form') initFormPegawai();
            if (page === 'detail') initDetailPegawai();
        });
    }

    // Tombol logout pada navbar
    $(document).on('click', '#btnLogout', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'php/auth.php?action=logout',
            method: 'POST',
            dataType: 'json',
            complete: function () {
                window.location.href = 'login.html';
            }
        });
    });
});

/**
 * Menghindari karakter HTML berbahaya saat menampilkan data ke DOM.
 */
function escapeHtml(text) {
    return $('<div>').text(text ?? '').html();
}

/**
 * Menampilkan pesan notifikasi Bootstrap sederhana.
 */
function showAlert(selector, message, type) {
    const html = `
        <div class="alert alert-${type} alert-dismissible fade show py-2" role="alert">
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    $(selector).html(html);
}

/**
 * Mengambil parameter query string dari URL.
 */
function getUrlParam(name) {
    return new URLSearchParams(window.location.search).get(name);
}

/**
 * Mengecek apakah pengguna sudah login; jika belum, arahkan ke halaman login.
 */
function cekSesi(onSuccess) {
    $.ajax({
        url: 'php/auth.php?action=check',
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res.status && res.data) {
                onSuccess(res.data);
            } else {
                window.location.href = 'login.html';
            }
        },
        error: function () {
            window.location.href = 'login.html';
        }
    });
}

/**
 * Inisialisasi halaman Login dengan validasi form JS & AJAX.
 */
function initLogin() {
    // Jika sudah login, langsung arahkan ke dashboard
    $.getJSON('php/auth.php?action=check', function (res) {
        if (res.status) {
            window.location.href = 'dashboard.html';
        }
    });

    $('#formLogin').on('submit', function (e) {
        e.preventDefault();

        const username = $('#username').val().trim();
        const password = $('#password').val();

        // Validasi JavaScript sederhana
        if (username === '' || password === '') {
            showAlert('#alertBox', 'Username dan password wajib diisi.', 'warning');
            return;
        }

        $.ajax({
            url: 'php/auth.php?action=login',
            method: 'POST',
            dataType: 'json',
            data: { username: username, password: password },
            success: function (res) {
                if (res.status) {
                    window.location.href = 'dashboard.html';
                } else {
                    showAlert('#alertBox', res.message, 'danger');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Gagal melakukan login.';
                showAlert('#alertBox', msg, 'danger');
            }
        });
    });
}

/**
 * Inisialisasi halaman Dashboard.
 */
function initDashboard() {
    $.ajax({
        url: 'php/pegawai.php?action=summary',
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (!res.status) return;
            $('#totalPegawai').text(res.data.total_pegawai);
            $('#totalDepartemen').text(res.data.total_departemen);

            const tbody = $('#tabelTerbaru');
            tbody.empty();

            if (res.data.terbaru.length === 0) {
                tbody.append('<tr><td colspan="4" class="text-center text-muted">Belum ada data pegawai.</td></tr>');
                return;
            }

            $.each(res.data.terbaru, function (_, item) {
                tbody.append(`
                    <tr>
                        <td>${escapeHtml(item.nip)}</td>
                        <td><a href="detail_pegawai.html?id=${item.id}" class="text-decoration-none">${escapeHtml(item.nama)}</a></td>
                        <td>${escapeHtml(item.jabatan)}</td>
                        <td>${escapeHtml(item.departemen)}</td>
                    </tr>
                `);
            });
        }
    });
}

/**
 * Inisialisasi halaman Daftar Pegawai (Tabel, Pencarian, dan Hapus).
 */
function initDaftarPegawai() {
    function loadData(keyword = '') {
        $.ajax({
            url: 'php/pegawai.php?action=list',
            method: 'GET',
            dataType: 'json',
            data: { q: keyword },
            success: function (res) {
                const tbody = $('#tabelPegawai');
                tbody.empty();

                if (!res.status || res.data.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center text-muted">Data pegawai tidak ditemukan.</td></tr>');
                    return;
                }

                $.each(res.data, function (index, p) {
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${escapeHtml(p.nip)}</td>
                            <td>${escapeHtml(p.nama)}</td>
                            <td>${escapeHtml(p.jabatan)}</td>
                            <td>${escapeHtml(p.departemen)}</td>
                            <td class="text-nowrap">
                                <a href="detail_pegawai.html?id=${p.id}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="form_pegawai.html?id=${p.id}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <button class="btn btn-sm btn-outline-danger btn-hapus" data-id="${p.id}" data-nama="${escapeHtml(p.nama)}">Hapus</button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function () {
                showAlert('#alertBox', 'Gagal memuat data pegawai.', 'danger');
            }
        });
    }

    loadData();

    $('#btnCari').on('click', function () {
        loadData($('#inputCari').val().trim());
    });

    $('#inputCari').on('keyup', function (e) {
        if (e.key === 'Enter') {
            loadData($(this).val().trim());
        }
    });

    $(document).on('click', '.btn-hapus', function () {
        const id = $(this).data('id');
        const nama = $(this).data('nama');

        if (!confirm(`Yakin ingin menghapus data pegawai "${nama}"?`)) {
            return;
        }

        $.ajax({
            url: 'php/pegawai.php?action=delete',
            method: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function (res) {
                showAlert('#alertBox', res.message, 'success');
                loadData($('#inputCari').val().trim());
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Gagal menghapus data.';
                showAlert('#alertBox', msg, 'danger');
            }
        });
    });
}

/**
 * Inisialisasi halaman Tambah / Edit Pegawai dengan validasi JavaScript.
 */
function initFormPegawai() {
    const id = getUrlParam('id');

    if (id) {
        $('#judulForm').text('Edit Pegawai');
        $('#id').val(id);

        $.ajax({
            url: 'php/pegawai.php?action=detail',
            method: 'GET',
            dataType: 'json',
            data: { id: id },
            success: function (res) {
                if (!res.status) {
                    showAlert('#alertBox', res.message, 'danger');
                    return;
                }
                const p = res.data;
                $('#nip').val(p.nip);
                $('#nama').val(p.nama);
                $('#jabatan').val(p.jabatan);
                $('#departemen').val(p.departemen);
                $('#email').val(p.email);
                $('#no_hp').val(p.no_hp);
                $('#tanggal_masuk').val(p.tanggal_masuk);
            },
            error: function () {
                showAlert('#alertBox', 'Data pegawai tidak ditemukan.', 'danger');
            }
        });
    }

    $('#formPegawai').on('submit', function (e) {
        e.preventDefault();

        const payload = {
            id: $('#id').val(),
            nip: $('#nip').val().trim(),
            nama: $('#nama').val().trim(),
            jabatan: $('#jabatan').val().trim(),
            departemen: $('#departemen').val().trim(),
            email: $('#email').val().trim(),
            no_hp: $('#no_hp').val().trim(),
            tanggal_masuk: $('#tanggal_masuk').val()
        };

        // Validasi form menggunakan JavaScript
        if (!payload.nip || !payload.nama || !payload.jabatan || !payload.departemen || !payload.email || !payload.no_hp || !payload.tanggal_masuk) {
            showAlert('#alertBox', 'Semua field wajib diisi.', 'warning');
            return;
        }

        if (payload.nip.length < 5) {
            showAlert('#alertBox', 'NIP minimal 5 karakter.', 'warning');
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(payload.email)) {
            showAlert('#alertBox', 'Format email tidak valid.', 'warning');
            return;
        }

        if (!/^[0-9]{8,15}$/.test(payload.no_hp)) {
            showAlert('#alertBox', 'Nomor HP harus berupa angka (8-15 digit).', 'warning');
            return;
        }

        const action = payload.id ? 'update' : 'create';

        $.ajax({
            url: `php/pegawai.php?action=${action}`,
            method: 'POST',
            dataType: 'json',
            data: payload,
            success: function (res) {
                if (res.status) {
                    window.location.href = 'pegawai.html';
                } else {
                    showAlert('#alertBox', res.message, 'danger');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'Gagal menyimpan data pegawai.';
                showAlert('#alertBox', msg, 'danger');
            }
        });
    });
}

/**
 * Inisialisasi halaman Detail Pegawai.
 */
function initDetailPegawai() {
    const id = getUrlParam('id');
    if (!id) {
        showAlert('#alertBox', 'ID pegawai tidak ditemukan.', 'warning');
        return;
    }

    $.ajax({
        url: 'php/pegawai.php?action=detail',
        method: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function (res) {
            if (!res.status) {
                showAlert('#alertBox', res.message, 'danger');
                return;
            }
            const p = res.data;
            $('#detailNip').text(p.nip);
            $('#detailNama').text(p.nama);
            $('#detailJabatan').text(p.jabatan);
            $('#detailDepartemen').text(p.departemen);
            $('#detailEmail').text(p.email);
            $('#detailNoHp').text(p.no_hp);
            $('#detailTanggalMasuk').text(p.tanggal_masuk);
            $('#btnEditDetail').attr('href', `form_pegawai.html?id=${p.id}`);
        },
        error: function () {
            showAlert('#alertBox', 'Data pegawai tidak ditemukan.', 'danger');
        }
    });
}
