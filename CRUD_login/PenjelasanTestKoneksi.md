
# Penjelasan Kode PHP – Cek Koneksi PostgreSQL (Baris demi Baris)

Di bawah ini adalah pemaparan per baris untuk skrip PHP yang melakukan **uji koneksi PostgreSQL** menggunakan fungsi bantu dari `koneksi.php`, lalu menampilkan versi server jika berhasil.

## Cuplikan Kode dengan Nomor Baris

```php
 1: <?php
 2: require __DIR__ . '/koneksi.php';
 3: try {
 4:     $conn = get_pg_connection();
 5:     echo "Koneksi OK. Versi server: " . pg_parameter_status($conn, 'server_version');
 6: } catch (Throwable $e) {
 7:     echo "Koneksi gagal: " . $e->getMessage();
 8: }
```

## Penjelasan Per Baris

### Baris 1
**Kode:** `<?php`  
**Penjelasan:** Pembuka blok PHP. Semua perintah setelah ini diproses oleh interpreter PHP.

---

### Baris 2
**Kode:** `require __DIR__ . '/koneksi.php';`  
**Penjelasan:**
- `require` mewajibkan file eksternal untuk dimuat. Jika file tidak ditemukan atau terjadi error, eksekusi **dihentikan** (fatal error).  
- `__DIR__` adalah *magic constant* yang menunjuk ke **direktori file saat ini**. Ini membuat path relatif lebih aman/portabel.  
- `'/koneksi.php'` adalah file yang (umumnya) berisi **fungsi utilitas koneksi PostgreSQL**, misalnya `get_pg_connection()` yang mengembalikan resource/handle koneksi (`PgSql\Connection`).

> Jika kamu ingin eksekusi **tidak** berhenti saat file hilang, gunakan `include` (namun untuk dependensi penting seperti koneksi, `require` lebih tepat).

---

### Baris 3
**Kode:** `try {`  
**Penjelasan:** Memulai blok **`try`** untuk menangkap kemungkinan exception/error saat membuat koneksi atau menjalankan fungsi PostgreSQL. Pasangannya ada di blok `catch` pada baris 6.

---

### Baris 4
**Kode:** `$conn = get_pg_connection();`  
**Penjelasan:**
- Memanggil fungsi `get_pg_connection()` (didefinisikan di `koneksi.php`).  
- Fungsi ini diharakan membuka koneksi ke PostgreSQL (mis. `pg_connect` atau `new PDO('pgsql:...')`) dan **mengembalikan handle/objek koneksi**.  
- Hasilnya disimpan ke variabel `$conn` untuk digunakan pada operasi berikutnya.

> Jika `get_pg_connection()` gagal dan melempar exception (atau mengembalikan `false`), alur akan berpindah ke blok `catch` (baris 6).

---

### Baris 5
**Kode:** `echo "Koneksi OK. Versi server: " . pg_parameter_status($conn, 'server_version');`  
**Penjelasan:**
- Jika koneksi berhasil, baris ini mencetak pesan sukses ke output.  
- `pg_parameter_status($conn, 'server_version')` mengambil **nilai parameter runtime** dari server PostgreSQL, yaitu **versi server** (mis. `16.3`).  
- Operator `.` menggabungkan string: `"Koneksi OK. Versi server: "` + hasil fungsi menjadi satu kalimat.

> `pg_parameter_status()` cocok untuk memeriksa info server seperti `server_encoding`, `client_encoding`, dan lain-lain. Di sini difokuskan ke `server_version` untuk bukti koneksi aktif.

---

### Baris 6
**Kode:** `} catch (Throwable $e) {`  
**Penjelasan:**
- Menutup blok `try` dan membuka blok `catch`.  
- Menangkap semua jenis kesalahan/pengecualian yang **menurunkan dari `Throwable`** (mencakup `Exception` dan `Error`). Ini memastikan pesan kesalahan dapat ditangani dengan baik.

---

### Baris 7
**Kode:** `echo "Koneksi gagal: " . $e->getMessage();`  
**Penjelasan:**
- Jika terjadi error pada blok `try`, baris ini akan **menampilkan alasan kegagalan**.  
- `$e->getMessage()` berisi pesan error yang diberikan exception (mis. kesalahan kredensial, host tidak bisa dijangkau, dsb.).

> Untuk produksi, sebaiknya **jangan tampilkan pesan internal** langsung ke pengguna. Simpan ke log dan tampilkan pesan yang lebih umum.  

---

### Baris 8
**Kode:** `}`  
**Penjelasan:** Menutup blok `catch` (dan keseluruhan struktur `try/catch`).

---

## Catatan Tambahan (Best Practices)

- **Konfigurasi Koneksi:** Di dalam `koneksi.php`, simpan kredensial (host, port, dbname, user, password) melalui **variabel lingkungan** (`.env`) dan **jangan commit ke repo publik**.
- **Error Handling:** Pertimbangkan penggunaan *custom exception* di `get_pg_connection()` agar log lebih terstruktur.
- **Health Check Endpoint:** Skrip ini dapat dijadikan endpoint **/health** untuk memantau status DB—batasi akses (IP allowlist/basic auth) agar tidak terekspos publik.
- **PDO vs pg\_connect:** Jika memakai **PDO**, ganti fungsi `pg_parameter_status` dengan query `SELECT version();` atau `SHOW server_version;` menggunakan prepared statement.

---

## Contoh Implementasi `get_pg_connection()` (Ilustratif)

> contoh yg lain.

```php
// koneksi.php
<?php
function get_pg_connection(): \PgSql\Connection {
    $connStr = sprintf(
        "host=%s port=%s dbname=%s user=%s password=%s",
        getenv('PGHOST') ?: 'localhost',
        getenv('PGPORT') ?: '5432',
        getenv('PGDATABASE') ?: 'appdb',
        getenv('PGUSER') ?: 'appuser',
        getenv('PGPASSWORD') ?: 'secret'
    );
    $conn = pg_connect($connStr);
    if (!$conn) {
        throw new \RuntimeException('Gagal konek ke PostgreSQL');
    }
    return $conn;
}
```