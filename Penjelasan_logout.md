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
4. **Menghapus cookie sesi** (jika ada).
5. **Mengalihkan pengguna** kembali ke halaman `login.php` setelah logout berhasil.

---

## Penjelasan Per Bagian

- `session_start();` — Memulai sesi agar server dapat mengakses data pengguna yang sedang aktif.
- `session_unset();` — Menghapus seluruh variabel yang tersimpan dalam `$_SESSION`.
- `session_destroy();` — Menghancurkan sesi aktif sehingga tidak dapat digunakan kembali.
- `setcookie(session_name(), '', time() - 3600, '/');` — Menghapus cookie sesi dari browser (jika digunakan).
- `header('Location: login.php');` — Mengarahkan pengguna ke halaman login setelah logout.
- `exit;` — Menghentikan eksekusi script setelah melakukan redirect (praktik terbaik).

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

