# Penjelasan `create.php` – Baris demi Baris

Dokumen ini menguraikan fungsi setiap baris kode pada file `create.php` milikmu. Untuk memudahkan, setiap baris diberi nomor, diikuti kodenya, lalu penjelasannya.

---

## Daftar Kode Asli dengan Nomor Baris

```php
   1: <?php
   2: require __DIR__ . '/koneksi.php';
   3: 
   4: $err = $ok = '';
   5: $nim = $nama = $email = $jurusan = '';
   6: 
   7: if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   8:     $nim     = trim($_POST['nim'] ?? '');
   9:     $nama    = trim($_POST['nama'] ?? '');
  10:     $email   = trim($_POST['email'] ?? '');
  11:     $jurusan = trim($_POST['jurusan'] ?? '');
  12: 
  13:     if ($nim === '' || $nama === '') {
  14:         $err = 'NIM dan Nama wajib diisi.';
  15:     } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  16:         $err = 'Format email tidak valid.';
  17:     } else {
  18:         try {
  19:             qparams(
  20:                 'INSERT INTO public.mahasiswa (nim, nama, email, jurusan) VALUES ($1, $2, NULLIF($3, \'\'), NULLIF($4, \'\'))',
  21:                 [$nim, $nama, $email, $jurusan]
  22:             );
  23:             header('Location: index.php');
  24:             exit;
  25:         } catch (Throwable $e) {
  26:             $err = $e->getMessage();
  27:         }
  28:     }
  29: }
  30: ?>
  31: <!doctype html>
  32: <html lang="id">
  33: <head>
  34:   <meta charset="utf-8">
  35:   <title>Tambah Mahasiswa</title>
  36:   <style>
  37:     body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:720px;margin:24px auto;padding:0 12px}
  38:     label{display:block;margin-top:10px}
  39:     input,select{width:100%;padding:8px;margin-top:4px}
  40:     .btn{padding:8px 12px;border:1px solid #999;border-radius:6px;background:#f6f6f6;text-decoration:none}
  41:     .alert{padding:10px;border-radius:6px;margin:10px 0}
  42:     .alert.error{background:#ffe9e9;border:1px solid #e99}
  43:   </style>
  44: </head>
  45: <body>
  46:   <h1>Tambah Mahasiswa</h1>
  47: 
  48:   <?php if ($err): ?>
  49:     <div class="alert error"><?= htmlspecialchars($err) ?></div>
  50:   <?php endif; ?>
  51: 
  52:   <form method="post">
  53:     <label>NIM
  54:       <input name="nim" value="<?= htmlspecialchars($nim) ?>" required>
  55:     </label>
  56:     <label>Nama
  57:       <input name="nama" value="<?= htmlspecialchars($nama) ?>" required>
  58:     </label>
  59:     <label>Email (opsional)
  60:       <input name="email" value="<?= htmlspecialchars($email) ?>" placeholder="nama@kampus.ac.id">
  61:     </label>
  62:     <label>Jurusan (opsional)
  63:       <input name="jurusan" value="<?= htmlspecialchars($jurusan) ?>">
  64:     </label>
  65: 
  66:     <p style="margin-top:16px">
  67:       <button class="btn" type="submit">Simpan</button>
  68:       <a class="btn" href="index.php">Kembali</a>
  69:     </p>
  70:   </form>
  71: </body>
  72: </html>
```

---

## Penjelasan Per Baris

### Baris 1

**Kode:** `<?php`

**Penjelasan:** Pembuka blok PHP.

### Baris 2

**Kode:** `require __DIR__ . '/koneksi.php';`

**Penjelasan:** Memuat file eksternal (konfigurasi/koneksi/helper) agar fungsi/variabel di dalamnya tersedia.

### Baris 3

**Kode:** `(kosong)`

**Penjelasan:** Baris kosong untuk keterbacaan.

### Baris 4

**Kode:** `$err = $ok = '';`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 5

**Kode:** `$nim = $nama = $email = $jurusan = '';`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 6

**Kode:** `(kosong)`

**Penjelasan:** Baris kosong untuk keterbacaan.

### Baris 7

**Kode:** `if ($_SERVER['REQUEST_METHOD'] === 'POST') {`

**Penjelasan:** Kondisi `if` untuk menjalankan blok kode jika syarat terpenuhi.

### Baris 8

**Kode:** `    $nim     = trim($_POST['nim'] ?? '');`

**Penjelasan:** Mengambil nilai dari form yang dikirim dengan metode POST dan menyimpannya ke variabel.

### Baris 9

**Kode:** `    $nama    = trim($_POST['nama'] ?? '');`

**Penjelasan:** Mengambil nilai dari form yang dikirim dengan metode POST dan menyimpannya ke variabel.

### Baris 10

**Kode:** `    $email   = trim($_POST['email'] ?? '');`

**Penjelasan:** Mengambil nilai dari form yang dikirim dengan metode POST dan menyimpannya ke variabel.

### Baris 11

**Kode:** `    $jurusan = trim($_POST['jurusan'] ?? '');`

**Penjelasan:** Mengambil nilai dari form yang dikirim dengan metode POST dan menyimpannya ke variabel.

### Baris 12

**Kode:** `(kosong)`

**Penjelasan:** Baris kosong untuk keterbacaan.

### Baris 13

**Kode:** `    if ($nim === '' || $nama === '') {`

**Penjelasan:** Kondisi `if` untuk menjalankan blok kode jika syarat terpenuhi.

### Baris 14

**Kode:** `        $err = 'NIM dan Nama wajib diisi.';`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 15

**Kode:** `    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {`

**Penjelasan:** Kondisi `if` untuk menjalankan blok kode jika syarat terpenuhi.

### Baris 16

**Kode:** `        $err = 'Format email tidak valid.';`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 17

**Kode:** `    } else {`

**Penjelasan:** Bagian `else` yang berjalan jika kondisi `if` tidak terpenuhi.

### Baris 18

**Kode:** `        try {`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 19

**Kode:** `            qparams(`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 20

**Kode:** `                'INSERT INTO public.mahasiswa (nim, nama, email, jurusan) VALUES ($1, $2, NULLIF($3, \'\'), NULLIF($4, \'\'))',`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 21

**Kode:** `                [$nim, $nama, $email, $jurusan]`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 22

**Kode:** `            );`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 23

**Kode:** `            header('Location: index.php');`

**Penjelasan:** Mengalihkan (redirect) pengguna ke halaman lain setelah proses berhasil/gagal.

### Baris 24

**Kode:** `            exit;`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 25

**Kode:** `        } catch (Throwable $e) {`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 26

**Kode:** `            $err = $e->getMessage();`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 27

**Kode:** `        }`

**Penjelasan:** Penutup blok `{ }`.

### Baris 28

**Kode:** `    }`

**Penjelasan:** Penutup blok `{ }`.

### Baris 29

**Kode:** `}`

**Penjelasan:** Penutup blok `{ }`.

### Baris 30

**Kode:** `?>`

**Penjelasan:** Penutup blok PHP.

### Baris 31

**Kode:** `<!doctype html>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 32

**Kode:** `<html lang="id">`

**Penjelasan:** Tag pembuka dokumen HTML.

### Baris 33

**Kode:** `<head>`

**Penjelasan:** Bagian head: metadata, judul halaman, link CSS.

### Baris 34

**Kode:** `  <meta charset="utf-8">`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 35

**Kode:** `  <title>Tambah Mahasiswa</title>`

**Penjelasan:** Judul halaman (ditampilkan di tab browser).

### Baris 36

**Kode:** `  <style>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 37

**Kode:** `    body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:720px;margin:24px auto;padding:0 12px}`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 38

**Kode:** `    label{display:block;margin-top:10px}`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 39

**Kode:** `    input,select{width:100%;padding:8px;margin-top:4px}`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 40

**Kode:** `    .btn{padding:8px 12px;border:1px solid #999;border-radius:6px;background:#f6f6f6;text-decoration:none}`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 41

**Kode:** `    .alert{padding:10px;border-radius:6px;margin:10px 0}`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 42

**Kode:** `    .alert.error{background:#ffe9e9;border:1px solid #e99}`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 43

**Kode:** `  </style>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 44

**Kode:** `</head>`

**Penjelasan:** Penutup head.

### Baris 45

**Kode:** `<body>`

**Penjelasan:** Bagian utama konten halaman.

### Baris 46

**Kode:** `  <h1>Tambah Mahasiswa</h1>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 47

**Kode:** `(kosong)`

**Penjelasan:** Baris kosong untuk keterbacaan.

### Baris 48

**Kode:** `  <?php if ($err): ?>`

**Penjelasan:** Pembuka blok PHP.

### Baris 49

**Kode:** `    <div class="alert error"><?= htmlspecialchars($err) ?></div>`

**Penjelasan:** Meng-escape karakter khusus HTML agar aman dari XSS saat ditampilkan kembali.

### Baris 50

**Kode:** `  <?php endif; ?>`

**Penjelasan:** Pembuka blok PHP.

### Baris 51

**Kode:** `(kosong)`

**Penjelasan:** Baris kosong untuk keterbacaan.

### Baris 52

**Kode:** `  <form method="post">`

**Penjelasan:** Formulir HTML untuk mengirim data (method/action/atribut lain).

### Baris 53

**Kode:** `    <label>NIM`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 54

**Kode:** `      <input name="nim" value="<?= htmlspecialchars($nim) ?>" required>`

**Penjelasan:** Meng-escape karakter khusus HTML agar aman dari XSS saat ditampilkan kembali.

### Baris 55

**Kode:** `    </label>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 56

**Kode:** `    <label>Nama`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 57

**Kode:** `      <input name="nama" value="<?= htmlspecialchars($nama) ?>" required>`

**Penjelasan:** Meng-escape karakter khusus HTML agar aman dari XSS saat ditampilkan kembali.

### Baris 58

**Kode:** `    </label>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 59

**Kode:** `    <label>Email (opsional)`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 60

**Kode:** `      <input name="email" value="<?= htmlspecialchars($email) ?>" placeholder="nama@kampus.ac.id">`

**Penjelasan:** Meng-escape karakter khusus HTML agar aman dari XSS saat ditampilkan kembali.

### Baris 61

**Kode:** `    </label>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 62

**Kode:** `    <label>Jurusan (opsional)`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 63

**Kode:** `      <input name="jurusan" value="<?= htmlspecialchars($jurusan) ?>">`

**Penjelasan:** Meng-escape karakter khusus HTML agar aman dari XSS saat ditampilkan kembali.

### Baris 64

**Kode:** `    </label>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 65

**Kode:** `(kosong)`

**Penjelasan:** Baris kosong untuk keterbacaan.

### Baris 66

**Kode:** `    <p style="margin-top:16px">`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 67

**Kode:** `      <button class="btn" type="submit">Simpan</button>`

**Penjelasan:** Tombol aksi pada form. `type="submit"` untuk mengirim form.

### Baris 68

**Kode:** `      <a class="btn" href="index.php">Kembali</a>`

**Penjelasan:** Penentuan kelas CSS untuk styling elemen.

### Baris 69

**Kode:** `    </p>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 70

**Kode:** `  </form>`

**Penjelasan:** Baris kode ini mendukung struktur/fungsi halaman (lihat konteks sekitar).

### Baris 71

**Kode:** `</body>`

**Penjelasan:** Penutup body.

### Baris 72

**Kode:** `</html>`

**Penjelasan:** Tag penutup dokumen HTML.
