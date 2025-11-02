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
