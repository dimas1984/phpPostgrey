# Penjelasan `dashboard.php`

Dokumen ini menjelaskan fungsi dan struktur kode dari file `dashboard.php`

---

## 📄 Kode Sumber
```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// misal ambil detail user jika perlu lagi dari DB
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><title>Dashboard</title></head>
<body>
  <h1>Selamat datang, <?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']) ?></h1>
  <p>Ini adalah halaman yang hanya bisa diakses setelah login.</p>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>
```

---

## 🧩 Fungsi Utama

File `dashboard.php` berfungsi sebagai **halaman utama setelah login**.  
Biasanya halaman ini **hanya dapat diakses oleh pengguna yang sudah login**.  
Jika pengguna belum login, sistem akan **mengalihkan (redirect)** ke halaman `login.php`.

---

## 🔍 Penjelasan Bagian Kode

### 1. Inisialisasi Sesi dan Cek Login

```php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
```

- `session_start()` memulai sesi PHP agar data user dapat dibaca dari `$_SESSION`.
- `if (!isset($_SESSION["user_id"]))` memeriksa apakah user sudah login.
- Jika belum login, pengguna diarahkan ke halaman `login.php` dengan perintah `header("Location: login.php")`.
- `exit;` digunakan untuk menghentikan eksekusi script setelah redirect (best practice).

---

### 2. Tampilan Dashboard (HTML)

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Selamat Datang di Dashboard</h1>
    <p>Anda login sebagai: <?= htmlspecialchars($_SESSION["username"]) ?></p>
    <a href="logout.php">Logout</a>
</body>
</html>
```

Penjelasan:
- Struktur HTML standar dengan heading dan teks selamat datang.
- `htmlspecialchars($_SESSION["username"])` digunakan untuk menampilkan nama user secara aman agar tidak bisa disusupi kode berbahaya (XSS).
- Terdapat tautan `logout.php` untuk keluar dari akun.

---

### 3. Alur Logika Sesi

```
[User Login Berhasil] 
      ↓
authenticate.php menyimpan $_SESSION['user_id'] dan $_SESSION['username']
      ↓
User mengakses dashboard.php
      ↓
Cek: apakah session user_id ada?
      ↓
- Jika tidak ada → redirect ke login.php
- Jika ada → tampilkan dashboard
```

---

## 💡 Kesimpulan

| Bagian | Fungsi | Penjelasan |
|--------|---------|------------|
| `session_start()` | Memulai sesi PHP | Wajib di awal file sebelum HTML |
| `$_SESSION["user_id"]` | Menyimpan identitas user | Menjadi tanda user sudah login |
| `header("Location: login.php")` | Redirect jika belum login | Keamanan akses |
| `htmlspecialchars()` | Mencegah XSS | Melindungi output HTML |
| `logout.php` | Menghapus sesi | Logout aman |

---


