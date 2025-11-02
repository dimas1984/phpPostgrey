# Penjelasan Kode `login.php` (Markdown)

Dokumen ini menjelaskan **fungsi setiap bagian** dari file `login.php` termasuk alur kerja
---

## Ringkasan Fungsi
Halaman ini:
1. **Memulai sesi** PHP.
2. **Mengecek status login** — jika sudah login, langsung **redirect** ke `dashboard.php`.
3. Menampilkan **form login** (username & password) yang mengirim data ke `authenticate.php` via **POST**.
4. Menampilkan **pesan error** (jika ada) dari query param `?error=...` menggunakan `htmlspecialchars` agar aman dari XSS.
5. Menyediakan **tautan pendaftaran** ke `register.php`.

---

## Potongan Kode & Penjelasan

```php
<?php
session_start();
// jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
```
- `session_start()` **wajib** dipanggil sebelum output HTML; ini memulai/lanjutkan sesi pengguna.
- `isset($_SESSION['user_id'])` digunakan untuk mendeteksi apakah user sudah login.
- Jika iya, `header('Location: dashboard.php')` melakukan **HTTP redirect** agar user tidak melihat form login lagi.
- `exit;` **menghentikan eksekusi** setelah redirect (praktik yang benar agar tidak ada HTML yang ikut terkirim).

```html
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; padding: 2rem; background:#f6f6f6; }
    ...
    input[type="text"], input[type="password"] { width:100%; padding:0.5rem; margin:0.5rem 0; box-sizing:border-box; }
    button { padding:0.6rem 1rem; }
    .error { color: #b00020; }
  </style>
</head>
```
- Struktur **HTML5** standar dengan `lang="id"` 
- `meta viewport` membantu **responsivitas** di perangkat mobile.
- Blok `<style>` memberikan **gaya** sederhana. Terdapat elipsis `...` di file—kemungkinan placeholder ringkasan style.

```php
<?php if (!empty($_GET['error'])): ?>
  <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>
```
- Jika ada parameter `error` pada URL, tampilkan sebagai **pesan kesalahan**.
- `htmlspecialchars` mencegah **XSS** dengan meng-escape karakter khusus.
- **Catatan:** Melewatkan pesan error via **query string** itu mudah, tapi bisa **terekspos** dan tercatat di log/riwayat browser. Alternatif yang lebih baik adalah **flashdata** via session.

```html
<form action="authenticate.php" method="post" autocomplete="off">
  <label for="username">Username</label>
  <input id="username" name="username" type="text" required>

  <label for="password">Password</label>
  <input id="password" name="password" type="password" required>

  <button type="submit">Login</button>
  <br><br>
  <!-- <button type="submit">Daftar</button> -->
  <a class="btn" href="register.php">Register</a>
</form>
```
- Form **menggunakan POST** (tepat untuk kredensial).
- `required` memastikan validasi HTML5 minimal di sisi klien.
- `autocomplete="off"` mencegah penyimpanan otomatis; kadang tidak ideal untuk **user experience** (lihat saran).
- Terdapat **tautan** ke halaman pendaftaran.

---

## Alur Kerja (Flow)
1. User mengakses `login.php` → `session_start()` aktif.
2. Jika `$_SESSION['user_id']` sudah ada → redirect ke `dashboard.php` (tidak perlu login ulang).
3. Jika belum login → form tampil.
4. User kirim form → request POST ke `authenticate.php`.
5. `authenticate.php` melakukan **validasi** (cek user di DB, verifikasi password, dsb). Jika gagal, redirect balik ke `login.php?error=...`.

Diagram singkat:

```
[login.php] --(cek session)--> [sudah login?]
        | ya -----------------> redirect dashboard.php
        | tidak
        v
   tampil form --POST--> authenticate.php --(validasi)-->
                                         | sukses: set $_SESSION[user_id], redirect dashboard
                                         | gagal : redirect login.php?error=...
```

---
