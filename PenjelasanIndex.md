# Penjelasan Skrip `index.php`

File `index.php` adalah halaman utama dari aplikasi CRUD (Create, Read, Update, Delete) ini. Fungsinya adalah untuk **Membaca (Read)** dan **menampilkan** semua data mahasiswa yang ada di database dalam bentuk tabel. Halaman ini juga menyediakan tautan/tombol untuk melakukan aksi lain (Tambah, Ubah, Hapus).

## Alur Kerja Skrip

Skrip ini, seperti skrip lainnya, dibagi menjadi dua bagian: logika pengambilan data dan logika penampilan data.

### 1. Bagian Logika PHP (Pengambilan Data)

Bagian ini dieksekusi di server untuk mengambil data dari database sebelum halaman HTML dibuat.

```php
<?php
// Menyertakan file konfigurasi dan fungsi untuk koneksi database.
require __DIR__ . '/koneksi.php';

// 1. Eksekusi Query
// Menjalankan fungsi 'q' (dari koneksi.php) untuk mengambil data.
// - to_char(...): Memformat kolom timestamp 'created_at' agar mudah dibaca.
// - ORDER BY id DESC: Mengurutkan data agar yang terbaru (ID terbesar) tampil di atas.
$res = q('SELECT id, nim, nama, email, jurusan, to_char(created_at, \'YYYY-MM-DD HH24:MI\') AS created_at
          FROM public.mahasiswa
          ORDER BY id DESC');

// 2. Pengambilan Data
// pg_fetch_all: Mengambil semua baris hasil query dan menyimpannya sebagai array.
// ?: []: Jika query tidak menghasilkan data (tabel kosong),
//      variabel $rows akan diisi dengan array kosong [] untuk mencegah error.
$rows = pg_fetch_all($res) ?: [];
?>