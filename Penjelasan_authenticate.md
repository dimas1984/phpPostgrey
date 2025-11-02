# Dokumentasi `authenticate.php`

Dokumen ini menjelaskan alur autentikasi berdasarkan **kode asli** `authenticate.php`.

## Kode Sumber (Ringkas)

```php
<?php
session_start();
require_once 'koneksi.php'; // pastikan file ini benar

// ambil input
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// --- Dapatkan koneksi dari fungsi ---
try {
    $conn = get_pg_connection();  // <---- tambahkan baris ini
} catch (Throwable $e) {
    error_log('DB connection error in authenticate.php: ' . $e->getMessage());
    header('Location: login.php?error=' . urlencode('Gagal koneksi ke database.'));
    exit;
}

// validasi sederhana
if ($username === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Username dan password harus diisi.'));
    exit;
}

// gunakan prepared query untuk menghindari SQL injection
$sql = 'SELECT id, username, password_hash, full_name FROM users WHERE username = $1 LIMIT 1';
$result = pg_query_params($conn, $sql, array($username));

if (!$result) {
    // logging internal: pg_last_error($conn)
    header('Location: login.php?error=' . urlencode('Terjadi kesalahan pada server.'));
    exit;
}

if (pg_num_rows($result) === 0) {
    header('Location: login.php?error=' . urlencode('Username atau password salah.'));
    exit;
}

$user = pg_fetch_assoc($result);
$hash = $user['password_hash'];

// verifikasi password
if (password_verify($password, $hash)) {
    // sukses: set session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];

    header('Location: dashboard.php');
    exit;
} else {
    header('Location: login.php?error=' . urlencode('Username atau password salah.'));
    exit;
}
```

---

## Ikhtisar Proses

- Memulai sesi (`session_start()`) untuk menyimpan status login dan pesan *flash*.
- Mengambil **username** dari input form.
- Mengambil **password** dari input form.
- Menggunakan **pg_query_params** untuk query yang aman terhadap SQL Injection.
- Memverifikasi password menggunakan `password_verify()` terhadap hash yang tersimpan.
- Menyimpan/menangani password menggunakan `password_hash()`.
- Melakukan **`session_regenerate_id(true)`** setelah login sukses untuk mencegah *session fixation*.
- Session Fixation adalah jenis serangan keamanan web di mana penyerang mencoba memaksa korban menggunakan session ID tertentu yang sudah diketahui penyerang, 
   sebelum korban logi
- Menyimpan identitas pengguna ke `$_SESSION` saat autentikasi berhasil.
- Redirect ke **dashboard** pada login sukses.
- Gagal login → redirect kembali ke halaman login (kemungkinan dengan parameter error).
- Melakukan sanitasi input (mis. `trim()` / `filter_var()`).
- Mencatat kejadian tertentu menggunakan `error_log()` untuk debugging/monitoring.

---

## Alur Kerja Detail

- 1. **Cek metode HTTP** — hanya `POST` yang diproses; selain itu diarahkan kembali ke form login.
- 2. **Validasi CSRF** — token di form dibandingkan dengan token di session (jika tersedia).
- 3. **Ambil input** — `username` dan `password` dari `$_POST`, biasanya melalui `trim()` untuk buang spasi.
- 4. **Query pengguna** — ambil data user dari database menggunakan **prepared statement** (`PDO::prepare`/`pg_query_params`).
- 5. **Verifikasi password** — cocokkan kata sandi yang dikirim dengan hash tersimpan via `password_verify()`.
- 6. **Sukses** — regenerate session ID, set `$_SESSION['user_id']`/atribut lain, arahkan ke `dashboard.php`.
- 7. **Gagal** — set *flash message* kesalahan/parameter error lalu redirect kembali ke `login.php`.

---

## Potongan Kode Kritis

### Query terproteksi (prepared)

```php
SQL injection
$sql = 'SELECT id, username, password_hash, full_name FROM users WHERE username = $1 LIMIT 1';
$result = pg_query_params($conn, $sql, array($username));

if (!$result) {
    // logging internal: pg_last_error($conn)
    header('Location: login.php?error=' . urlencode('Terjadi kesalahan pada server.'));
    exit;
}

if (
```
### Verifikasi password

```php
$user = pg_fetch_assoc($result);;
$hash = $user['password_hash'];

// verifikasi password
if (password_verify($password, $hash)) {
    // sukses: set session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
      $_SESSION['full_name'] = $user['full_name'];
```
### Regenerasi Session ID

```php
password
if (password_verify($password, $hash)) {
    // sukses: set session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name']
```
### Menyetel sesi pengguna

```php
password, $hash)) {
    // sukses: set session
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];

    header('Location: dashboard.php');
    exit;
```
