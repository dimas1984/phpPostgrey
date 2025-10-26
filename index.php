<?php
require __DIR__ . '/koneksi.php';

// Ambil semua data
$res = q('SELECT id, nim, nama, email, jurusan, to_char(created_at, \'YYYY-MM-DD HH24:MI\') AS created_at
          FROM public.mahasiswa
          ORDER BY id DESC');

$rows = pg_fetch_all($res) ?: [];
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>CRUD Mahasiswa (PHP + PostgreSQL)</title>
  <style>
    body {
      font-family: system-ui, Segoe UI, Roboto, Arial, sans-serif;
      max-width: 980px;
      margin: 24px auto;
      padding: 0 12px
    }

    table {
      border-collapse: collapse;
      width: 100%
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 8px
    }

    th {
      background: #f6f6f6;
      text-align: left
    }

    a.btn {
      padding: 6px 10px;
      border: 1px solid #999;
      border-radius: 6px;
      text-decoration: none
    }

    .row-actions a {
      margin-right: 8px
    }
  </style>
</head>

<body>
  <h1>Data Mahasiswa</h1>

  <p><a class="btn" href="create.php">+ Tambah Mahasiswa</a></p>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>NIM</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Jurusan</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
      <tr>
        <td colspan="7">Belum ada data.</td>
      </tr>
      <?php else: foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['id']) ?></td>
        <td><?= htmlspecialchars($r['nim']) ?></td>
        <td><?= htmlspecialchars($r['nama']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= htmlspecialchars($r['jurusan']) ?></td>
        <td><?= htmlspecialchars($r['created_at']) ?></td>
        <td class="row-actions">
          <a class="btn" href="edit.php?id=<?= urlencode($r['id']) ?>">Ubah</a>
          <!-- <form action="delete.php" method="post" style="display:inline" onsubmit="return confirm('Hapus data ini?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
            <button class="btn" type="submit">Hapus</button>
          </form> -->
          <a href="#" class="btn" onclick="if(confirm('Hapus data ini?')) { 
       document.getElementById('deleteForm<?= $r['id'] ?>').submit(); 
   }">Hapus</a>

          <form id="deleteForm<?= $r['id'] ?>" action="delete.php" method="post" style="display:none;">
            <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
          </form>

        </td>
      </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</body>

</html>