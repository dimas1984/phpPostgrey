<?php
require __DIR__ . '/koneksi.php';

$err = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    exit('ID tidak valid.');
}

// Ambil data lama
try {
    $res = qparams('SELECT id, nim, nama, email, jurusan FROM public.mahasiswa WHERE id=$1', [$id]);
    $row = pg_fetch_assoc($res);
    if (!$row) {
        http_response_code(404);
        exit('Data tidak ditemukan.');
    }
} catch (Throwable $e) {
    exit('Error: ' . htmlspecialchars($e->getMessage()));
}

$nim = $row['nim'];
$nama = $row['nama'];
$email = $row['email'];
$jurusan = $row['jurusan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim     = trim($_POST['nim'] ?? '');
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if ($nim === '' || $nama === '') {
        $err = 'NIM dan Nama wajib diisi.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'Format email tidak valid.';
    } else {
        try {
            qparams(
                'UPDATE public.mahasiswa
                   SET nim=$1, nama=$2, email=NULLIF($3, \'\'), jurusan=NULLIF($4, \'\')
                 WHERE id=$5',
                [$nim, $nama, $email, $jurusan, $id]
            );
            header('Location: index.php');
            exit;
        } catch (Throwable $e) {
            $err = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Ubah Mahasiswa</title>
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:720px;margin:24px auto;padding:0 12px}
    label{display:block;margin-top:10px}
    input{width:100%;padding:8px;margin-top:4px}
    .btn{padding:8px 12px;border:1px solid #999;border-radius:6px;background:#f6f6f6;text-decoration:none}
    .alert{padding:10px;border-radius:6px;margin:10px 0}
    .alert.error{background:#ffe9e9;border:1px solid #e99}
  </style>
</head>
<body>
  <h1>Ubah Mahasiswa</h1>

  <?php if ($err): ?>
    <div class="alert error"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="post">
    <label>NIM
      <input name="nim" value="<?= htmlspecialchars($nim ??'') ?>" required>
    </label>
    <label>Nama
      <input name="nama" value="<?= htmlspecialchars($nama ?? '') ?>" required>
    </label>
    <label>Email (opsional)
      <input name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </label>
    <label>Jurusan (opsional)
      <input name="jurusan" value="<?= htmlspecialchars($jurusan ?? '') ?>">
    </label>

    <p style="margin-top:16px">
      <button class="btn" type="submit">Simpan Perubahan</button>
      <a class="btn" href="index.php">Batal</a>
    </p>
  </form>
</body>
</html>
