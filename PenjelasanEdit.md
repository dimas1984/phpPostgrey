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