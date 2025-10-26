# Penjelasan Skrip `edit.php`

Skrip `edit.php` ini lebih kompleks daripada `create.php` karena memiliki dua tanggung jawab utama yang terjadi dalam dua fase:

1.  **Fase GET (Memuat Halaman):** Mengambil data mahasiswa yang ada dari database berdasarkan ID yang diterima dari URL, lalu menampilkan data tersebut di dalam formulir HTML.
2.  **Fase POST (Memproses Perubahan):** Menerima data baru dari formulir yang disubmit oleh pengguna, memvalidasinya, dan kemudian memperbarui (UPDATE) data tersebut di database.

## Alur Kerja Skrip

Skrip menangani kedua skenario (GET dan POST) dalam satu file.

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
<?php
// ... (Kode Fase 1 dijalankan terlebih dahulu, $id sudah didapat) ...

// 4. Cek jika ini adalah POST Request (form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 5. Ambil Data BARU dari Form
    // Variabel $nim, $nama, dll. yang tadi diisi dari database,
    // SEKARANG DITIMPA (OVERWRITE) dengan data baru dari $_POST
    $nim     = trim($_POST['nim'] ?? '');
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    // 6. Validasi Data BARU
    if ($nim === '' || $nama === '') {
        $err = 'NIM dan Nama wajib diisi.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Format email tidak valid.';
    } else {
        // 7. Proses UPDATE ke Database (jika validasi lolos)
        try {
            qparams(
                // Query UPDATE yang aman dengan 5 parameter
                'UPDATE public.mahasiswa
                   SET nim=$1, nama=$2, email=NULLIF($3, \'\'), jurusan=NULLIF($4, \'\')
                 WHERE id=$5', // $id (dari Fase 1) digunakan di sini
                [$nim, $nama, $email, $jurusan, $id]
            );
            
            // 8. Redirect jika sukses
            header('Location: index.php');
            exit;
        } catch (Throwable $e) {
            // 9. Tangkap Error (misal: NIM duplikat)
            $err = $e->getMessage();
        }
    }
    // Jika validasi GAGAL (langkah 6), $err akan terisi.
    // Skrip akan lanjut ke bagian HTML dan menampilkan $err
    // serta mengisi <input> dengan data BARU yang gagal tadi (sticky form).
}
?>

<!doctype html>
<?php if ($err): ?>
    <div class="alert error"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post">
    <input name="nim" value="<?= htmlspecialchars($nim) ?>" required>
    </form>