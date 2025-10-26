
<?php
require __DIR__ . '/koneksi.php';

$err = $ok = '';
$nim = $nama = $email = $jurusan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim     = trim($_POST['nim'] ?? '');
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if ($nim === '' || $nama === '') {
        $err = 'NIM dan Nama wajib diisi.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Format email tidak valid.';
    } else {
        try {
            qparams(
                'INSERT INTO public.mahasiswa (nim, nama, email, jurusan) VALUES ($1, $2, NULLIF($3, \'\'), NULLIF($4, \'\'))',
                [$nim, $nama, $email, $jurusan]
            );
            header('Location: index.php');
            exit;
        } catch (Throwable $e) {
            $err = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tambah Mahasiswa</title>
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:720px;margin:24px auto;padding:0 12px}
    label{display:block;margin-top:10px}
    input,select{width:100%;padding:8px;margin-top:4px}
    .btn{padding:8px 12px;border:1px solid #999;border-radius:6px;background:#f6f6f6;text-decoration:none}
    .alert{padding:10px;border-radius:6px;margin:10px 0}
    .alert.error{background:#ffe9e9;border:1px solid #e99}
  </style>
</head>
<body>
  <h1>Tambah Mahasiswa</h1>

  <?php if ($err): ?>
    <div class="alert error"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post">
    <label>NIM
      <input name="nim" value="<?= htmlspecialchars($nim) ?>" required>
    </label>
    <label>Nama
      <input name="nama" value="<?= htmlspecialchars($nama) ?>" required>
    </label>
    <label>Email (opsional)
      <input name="email" value="<?= htmlspecialchars($email) ?>" placeholder="nama@kampus.ac.id">
    </label>
    <label>Jurusan (opsional)
      <input name="jurusan" value="<?= htmlspecialchars($jurusan) ?>">
    </label>

    <p style="margin-top:16px">
      <button class="btn" type="submit">Simpan</button>
      <a class="btn" href="index.php">Kembali</a>
    </p>
  </form>
</body>
</html>
# Penjelasan Skrip `delete.php`

Skrip `delete.php` ini adalah *handler* atau pemroses *backend* murni yang bertugas untuk menghapus data mahasiswa dari database. Tidak seperti `create.php`, skrip ini tidak menghasilkan tampilan HTML apa pun. Tugasnya hanya menerima perintah, memprosesnya, dan kemudian mengarahkan pengguna kembali.

## Alur Kerja Skrip

Keseluruhan proses skrip ini sangat fokus pada keamanan dan validasi sebelum melakukan operasi penghapusan data.

```php
<?php
// Menyertakan file konfigurasi dan fungsi untuk koneksi database.
require __DIR__ . '/koneksi.php';

// 1. Validasi Metode Request
// Ini adalah langkah keamanan penting untuk mencegah CSRF (Cross-Site Request Forgery).
// Operasi yang mengubah data (seperti DELETE) HARUS dilakukan via POST, bukan GET.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Jika diakses langsung via URL (metode GET), tolak permintaan.
    http_response_code(405); // 405 Method Not Allowed
    exit('Method not allowed'); // Hentikan skrip
}

// 2. Validasi ID
// Mengambil 'id' dari data POST.
// (int) (...) adalah 'type casting', memaksa nilai menjadi integer.
// Jika $_POST['id'] tidak ada, '?? 0' akan memberinya nilai 0.
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    // Jika ID tidak valid (0 atau negatif), tolak permintaan.
    http_response_code(400); // 400 Bad Request
    exit('ID tidak valid.'); // Hentikan skrip
}

// 3. Proses Penghapusan Data
try {
    // Menjalankan query DELETE dengan parameter terikat ($1).
    // Ini aman dari SQL Injection.
    qparams('DELETE FROM public.mahasiswa WHERE id=$1', [$id]);
    
    // 4. Redirect jika Sukses
    // Jika query berhasil, kembalikan pengguna ke halaman daftar.
    header('Location: index.php');
    exit;
    
} catch (Throwable $e) {
    // 5. Penanganan Error Database
    // Jika query gagal (misal: karena ID-nya terkait dengan tabel lain via foreign key),
    // tangkap errornya dan tampilkan pesan yang aman.
    http_response_code(500); // 500 Internal Server Error
    echo 'Gagal menghapus: ' . htmlspecialchars($e->getMessage());
}
# Penjelasan Skrip `edit.php`

Skrip `edit.php` ini lebih kompleks daripada `create.php` karena memiliki dua tanggung jawab utama yang terjadi dalam dua fase:

1.  **Fase GET (Memuat Halaman):** Mengambil data mahasiswa yang ada dari database berdasarkan ID yang diterima dari URL, lalu menampilkan data tersebut di dalam formulir HTML.
2.  **Fase POST (Memproses Perubahan):** Menerima data baru dari formulir yang disubmit oleh pengguna, memvalidasinya, dan kemudian memperbarui (UPDATE) data tersebut di database.

## Alur Kerja Skrip

Skrip ini secara cerdas menangani kedua skenario (GET dan POST) dalam satu file.

### Fase 1: Memuat Data untuk Diedit (GET Request)

Bagian ini dieksekusi saat pengguna pertama kali membuka halaman (misalnya, dengan mengklik tombol "Edit" dari `index.php` yang mengarah ke `edit.php?id=5`).

```php
<?php
require __DIR__ . '/koneksi.php';

$err = ''; // Inisialisasi variabel error

// 1. Validasi ID dari URL (GET Request)
// ID diambil dari URL (?id=...)
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400); // 400 Bad Request
    exit('ID tidak valid.');
}

// 2. Ambil Data dari Database
try {
    // Menjalankan query SELECT yang aman (parameter $1)
    $res = qparams('SELECT id, nim, nama, email, jurusan FROM public.mahasiswa WHERE id=$1', [$id]);
    $row = pg_fetch_assoc($res); // Ambil satu baris data
    
    // Jika ID tidak ada di database
    if (!$row) {
        http_response_code(404); // 404 Not Found
        exit('Data tidak ditemukan.');
    }
} catch (Throwable $e) {
    // Tangani jika query SELECT gagal
    exit('Error: ' . htmlspecialchars($e->getMessage()));
}

// 3. Isi Variabel dari Database
// Variabel-variabel ini akan digunakan untuk mengisi <input> di formulir HTML
$nim = $row['nim'];
$nama = $row['nama'];
$email = $row['email'];
$jurusan = $row['jurusan'];

// ... (Kode PHP Fase 2 dilewati saat GET request) ...
?>

<input name="nim" value="<?= htmlspecialchars($nim) ?>" required>
