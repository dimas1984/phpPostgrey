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