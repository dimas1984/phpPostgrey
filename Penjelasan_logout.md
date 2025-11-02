# Penjelasan `logout.php`

Dokumen ini menjelaskan fungsi dan alur kerja file `logout.php` berdasarkan kode yang diunggah.

---

## Kode Sumber
```php
<?php
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header('Location: login.php');
exit;
```

---

## Fungsi Utama

File `logout.php` digunakan untuk **mengakhiri sesi login pengguna** agar mereka keluar dari sistem dengan aman.

Langkah-langkah umum yang dilakukan:
1. **Memulai sesi** (`session_start()`) untuk mengakses sesi aktif.
2. **Menghapus data sesi** (`$_SESSION` dikosongkan).
3. **Menghancurkan sesi** dengan `session_destroy()`.
4. **Menghapus cookie sesi**.
5. **Mengalihkan pengguna** kembali ke halaman `login.php` setelah logout berhasil.

---

## Penjelasan Per Bagian

- `session_start();` — Memulai sesi agar server dapat mengakses data pengguna yang sedang aktif.
- `session_unset();` — Menghapus seluruh variabel yang tersimpan dalam `$_SESSION`.
- `session_destroy();` — Menghancurkan sesi aktif sehingga tidak dapat digunakan kembali.
- `setcookie(session_name(), '', time() - 3600, '/');` — menghapus cookie dengan waktu kedaluwarsa yang sudah lewat.
- `header('Location: login.php');` — Mengarahkan pengguna ke halaman login setelah logout.
- `exit;` — Menghentikan eksekusi script setelah melakukan redirect (praktik terbaik).
- `ini_get();` — membaca nilai konfigurasi PHP (dari php.ini).
- `session.use_cookies` — adalah konfigurasi bawaan PHP yang menentukan apakah session ID dikirim dan disimpan lewat cookie atau tidak.
- `session_get_cookie_params()` — digunakan untuk mengambil semua pengaturan (parameter) yang dipakai PHP saat membuat cookie sesi (session cookie).
---
## 📦 Penjelasan Setiap Parameter

| Parameter | Arti | Contoh Nilai | Penjelasan Lengkap |
|------------|------|---------------|-------------------|
| **`$params["path"]`** | Jalur (path) URL tempat cookie berlaku | `'/'` atau `'/admin'` | Menentukan area situs yang memiliki akses ke cookie. Jika diset `'/'`, cookie berlaku untuk seluruh situs. Jika diset `'/admin'`, cookie hanya berlaku di URL yang diawali `/admin`. Saat logout, path harus sama agar cookie bisa terhapus dengan benar. |
| **`$params["domain"]`** | Domain tempat cookie berlaku | `'example.com'` | Menentukan domain mana yang boleh menerima cookie ini. Misalnya `app.example.com` berbeda dari `example.com`. Jika domain tidak sama, browser tidak akan menghapus cookie lama. |
| **`$params["secure"]`** | Hanya kirim cookie melalui koneksi HTTPS | `true` atau `false` | Jika `true`, cookie **hanya akan dikirim lewat HTTPS**, bukan HTTP biasa. Ini mencegah kebocoran cookie di jaringan tidak aman. |
| **`$params["httponly"]`** | Cegah cookie diakses lewat JavaScript | `true` atau `false` | Jika `true`, cookie **tidak bisa diakses lewat JavaScript** (`document.cookie`), sehingga lebih aman dari serangan **XSS (Cross-Site Scripting)**. |

---

## Ilustrasi Alur Logout

```
[user klik tombol Logout]
        ↓
[logout.php dipanggil]
        ↓
session_start()
        ↓
hapus $_SESSION dan hancurkan sesi
        ↓
redirect ke login.php
```

---

## Best Practice

✅ Tambahkan `exit;` setelah `header('Location: ...')` untuk menghentikan eksekusi PHP.  
✅ Gunakan HTTPS agar cookie sesi aman dari pencurian.  
✅ Regenerasi ID sesi saat login (`session_regenerate_id(true)`) untuk mencegah session fixation.  
✅ Jangan menampilkan HTML setelah `session_destroy()` + redirect.  

