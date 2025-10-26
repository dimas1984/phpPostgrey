# Penjelasan Skrip `create.php`

Ini adalah skrip PHP yang berfungsi sebagai halaman "Tambah Mahasiswa" lengkap. Skrip ini menangani dua tugas utama:
1.  **Menampilkan formulir (form)** HTML kepada pengguna.
2.  **Memproses data** yang dikirim dari formulir tersebut, memvalidasinya, dan menyimpannya ke database.

## Alur Kerja Skrip

Skrip ini dapat dibagi menjadi dua bagian utama: Logika PHP (backend) dan Tampilan HTML (frontend).

### 1. Bagian Logika PHP (Awal File)

Bagian ini dieksekusi pertama kali saat halaman dimuat.

```php
<?php
// Menyertakan file konfigurasi dan fungsi untuk koneksi database.
require __DIR__ . '/koneksi.php';

// 1. Inisialisasi Variabel Pesan
// $err untuk menampung pesan error
// $ok untuk menampung pesan sukses (meskipun di skrip ini tidak terpakai)
$err = $ok = '';

// 2. Inisialisasi Variabel Form
// Ini penting untuk "sticky form" dan menghindari error "undefined variable"
$nim = $nama = $email = $jurusan = '';

// 3. Pengecekan Metode Request
// Blok kode ini HANYA akan berjalan jika pengguna menekan tombol "Simpan" (mengirim form)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 4. Pengambilan & Pembersihan Data
    // Mengambil data dari form, membersihkan spasi di awal/akhir (trim)
    // '?? '' ' (null coalescing) digunakan untuk memberi nilai default string kosong
    // jika datanya tidak ada, untuk menghindari error.
    $nim     = trim($_POST['nim'] ?? '');
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    // 5. Validasi Data
    if ($nim === '' || $nama === '') {
        // Cek apakah field wajib diisi
        $err = 'NIM dan Nama wajib diisi.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Cek format email HANYA jika email diisi (karena opsional)
        $err = 'Format email tidak valid.';
    } else {
        // 6. Proses ke Database (jika validasi lolos)
        try {
            // qparams adalah fungsi kustom (dari koneksi.php)
            // yang menjalankan query dengan parameter terikat (mencegah SQL Injection)
            qparams(
                // NULLIF($3, '') akan memasukkan NULL ke database jika string-nya kosong
                'INSERT INTO public.mahasiswa (nim, nama, email, jurusan) VALUES ($1, $2, NULLIF($3, \'\'), NULLIF($4, \'\'))',
                [$nim, $nama, $email, $jurusan]
            );
            
            // 7. Redirect jika sukses
            // Langsung pindah ke halaman index.php setelah data berhasil disimpan
            header('Location: index.php');
            exit; // Menghentikan eksekusi skrip setelah redirect
            
        } catch (Throwable $e) {
            // 8. Tangkap Error Database
            // Jika terjadi error saat INSERT (misal: NIM duplikat),
            // pesan error dari database akan ditampung di variabel $err
            $err = $e->getMessage();
        }
    }
}
?>