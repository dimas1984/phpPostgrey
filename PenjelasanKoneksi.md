
# Penjelasan `koneksi.php` – Baris demi Baris

File `koneksi.php` berfungsi untuk **mengatur koneksi ke PostgreSQL** serta menyediakan **helper function** untuk menjalankan query SQL dengan aman.  
Berikut penjelasan rinci setiap bagian dan baris kode.

---

## Cuplikan Kode Lengkap

```php
<?php
// koneksi.php
// Pastikan ekstensi pgsql aktif di php.ini

// 1. Fungsi Utama Koneksi
function get_pg_connection(): PgSql\Connection {
    // 'static $conn' adalah variabel statis. 
    // Nilainya tetap tersimpan di memori setelah fungsi selesai.
    // Ini memastikan koneksi database HANYA DIBUAT SEKALI per request.
    static $conn = null;
    
    // Jika koneksi sudah ada, langsung kembalikan koneksi yang sama.
    if ($conn instanceof PgSql\Connection) {
        return $conn;
    }

    // String koneksi untuk ke database PostgreSQL
    $connStr = "host=localhost port=5432 dbname=Belajar user=postgres password=12345 options='--client_encoding=UTF8'";
    
    // @pg_connect: Mencoba terhubung ke DB. 
    // Tanda '@' menekan pesan error default PHP.
    $conn = @pg_connect($connStr);

    // Jika koneksi gagal, hentikan skrip dengan melempar error (Exception)
    if (!$conn) {
        throw new RuntimeException("Koneksi PostgreSQL gagal. Periksa host/port/db/user/pass & ekstensi pgsql.");
    }
    
    // Kembalikan koneksi yang berhasil dibuat
    return $conn;
}

/** * 2. Helper untuk query AMAN (dengan parameter) 
 * Fungsi ini digunakan untuk CREATE, UPDATE, DELETE
 */
function qparams(string $sql, array $params) {
    // Ambil koneksi (yang sudah ada atau buat baru jika belum ada)
    $conn = get_pg_connection();
    
    // Jalankan query dengan parameter terpisah. 
    // Ini adalah metode paling aman untuk mencegah SQL INJECTION.
    $res = @pg_query_params($conn, $sql, $params);
    
    // Jika query gagal, hentikan skrip dan tampilkan pesan error
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}

/** * 3. Helper untuk query SEDERHANA (tanpa parameter) 
 * Fungsi ini digunakan untuk SELECT sederhana
 */
function q(string $sql) {
    $conn = get_pg_connection();
    
    // Jalankan query biasa (tanpa parameter)
    $res = @pg_query($conn, $sql);
    
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}

?>
```

---

## Penjelasan Per Bagian

### 🟦 Baris 1–3
```php
<?php
// koneksi.php
// Pastikan ekstensi pgsql aktif di php.ini
```
**Penjelasan:**
- Pembuka blok PHP.
- Komentar `// koneksi.php` memberi konteks file.
- Baris ketiga mengingatkan bahwa ekstensi `pgsql` **harus diaktifkan** di file `php.ini`, karena fungsi seperti `pg_connect()` dan `pg_query()` tidak tersedia tanpa ekstensi tersebut.

---

### 🟩 Baris 5–29 → Fungsi `get_pg_connection()`
Fungsi ini adalah **inti koneksi** ke PostgreSQL.

#### Baris 5
```php
function get_pg_connection(): PgSql\Connection {
```
Menentukan fungsi yang **mengembalikan objek bertipe `PgSql\Connection`** (fitur PHP 8).

#### Baris 7–10
```php
static $conn = null;
```
Variabel statis `$conn` hanya diinisialisasi sekali dan tetap tersimpan selama satu eksekusi skrip.  
→ Menghindari pembuatan koneksi berulang kali (lebih efisien).

#### Baris 12–14
```php
if ($conn instanceof PgSql\Connection) {
    return $conn;
}
```
Jika koneksi sudah dibuat sebelumnya, langsung gunakan kembali.

#### Baris 16–17
```php
$connStr = "host=localhost port=5432 dbname=Belajar user=postgres password=12345 options='--client_encoding=UTF8'";
```
Membuat **connection string** yang menentukan:
- Host database (`localhost`)
- Port (`5432`)
- Nama database (`Belajar`)
- User (`postgres`)
- Password (`12345`)
- Encoding UTF-8

> ⚠️ Catatan: Kredensial sebaiknya disimpan di **.env** atau **config terpisah** agar tidak terekspos di repositori.

#### Baris 19–21
```php
$conn = @pg_connect($connStr);
```
- Fungsi `pg_connect()` membuat koneksi ke PostgreSQL.
- Tanda `@` menekan pesan error bawaan PHP agar bisa ditangani secara manual dengan `try/catch`.

#### Baris 23–26
```php
if (!$conn) {
    throw new RuntimeException("Koneksi PostgreSQL gagal. ...");
}
```
Jika koneksi gagal, lempar exception agar bisa ditangkap oleh pemanggil (lebih bersih dibanding `die()` atau `exit`).

#### Baris 28
```php
return $conn;
```
Mengembalikan koneksi PostgreSQL yang sudah berhasil dibuat.

---

### 🟧 Baris 32–46 → Fungsi `qparams()`
Helper function untuk menjalankan query dengan parameter (aman dari SQL Injection).

#### Baris 35
```php
$conn = get_pg_connection();
```
Mengambil koneksi aktif (dari fungsi sebelumnya).

#### Baris 38
```php
$res = @pg_query_params($conn, $sql, $params);
```
Menjalankan query dengan **parameter terpisah** (prepared statement secara implisit).  
Contoh penggunaan:
```php
qparams("INSERT INTO mahasiswa(nim, nama) VALUES($1, $2)", [$nim, $nama]);
```

#### Baris 41–44
```php
if ($res === false) {
    throw new RuntimeException("Query gagal: " . pg_last_error($conn));
}
```
Jika query gagal, tampilkan error PostgreSQL terakhir.

#### Baris 45
```php
return $res;
```
Mengembalikan resource hasil query.

---

### 🟨 Baris 49–62 → Fungsi `q()`
Helper untuk query sederhana **tanpa parameter**.

#### Baris 51
```php
$conn = get_pg_connection();
```
Mengambil koneksi PostgreSQL yang sama.

#### Baris 54
```php
$res = @pg_query($conn, $sql);
```
Menjalankan query biasa (misalnya: `SELECT * FROM mahasiswa`).

#### Baris 56–59
Menangani error jika query gagal.

#### Baris 60
Mengembalikan hasil query (`PgSql\Result`).

---

## 💡 Rangkuman Fungsi

| Fungsi | Tujuan | Aman dari SQL Injection | Keterangan |
|--------|---------|--------------------------|-------------|
| `get_pg_connection()` | Membuat koneksi PostgreSQL dan menyimpannya secara statis | ✅ | Dipanggil otomatis oleh fungsi lain |
| `qparams($sql, $params)` | Menjalankan query dengan parameter (INSERT/UPDATE/DELETE) | ✅ | Gunakan placeholder `$1, $2, ...` |
| `q($sql)` | Menjalankan query sederhana tanpa parameter (SELECT) | ⚠️ Tidak | Pastikan tidak ada input user langsung |

---

## 🔒 Best Practices

1. Simpan kredensial di **environment variables**.
2. Hindari hard-coded password (`12345`).
3. Gunakan **pg_query_params()** untuk semua query input user.
4. Gunakan **exception handling** di file pemanggil untuk menangkap error dari `RuntimeException`.

---

## 🧩 Contoh Penggunaan

```php
require 'koneksi.php';

try {
    qparams("INSERT INTO mahasiswa (nim, nama) VALUES ($1, $2)", ['A11.2025.0001', 'Dimas Pratama']);
    echo "Data berhasil ditambahkan!";
} catch (RuntimeException $e) {
    echo "Terjadi kesalahan: " . $e->getMessage();
}
```
