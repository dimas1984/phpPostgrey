<?php
// register.php
session_start();

// jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// buat CSRF token sederhana jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ambil pesan error/sukses dari query string (opsional)
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Daftar Akun</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; background:#f4f6f8; padding:2rem; }
    .card { background:#fff; max-width:480px; margin:2rem auto; padding:1.5rem; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
    input[type="text"], input[type="password"] { width:100%; padding:0.6rem; margin:0.4rem 0 1rem 0; box-sizing:border-box; }
    button { padding:0.6rem 1rem; cursor:pointer; }
    .error { color:#b00020; margin-bottom:0.8rem; }
    .success { color:#1b7a1b; margin-bottom:0.8rem; }
    small { color:#666; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Buat Akun Baru</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form action="register_process.php" method="post" autocomplete="off" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <label for="username">Username</label><br>
      <input id="username" name="username" type="text" required minlength="3" maxlength="100">

      <label for="full_name">Nama Lengkap</label><br>
      <input id="full_name" name="full_name" type="text" maxlength="200">

      <label for="password">Password</label><br>
      <input id="password" name="password" type="password" required minlength="6">

      <label for="password_confirm">Konfirmasi Password</label><br>
      <input id="password_confirm" name="password_confirm" type="password" required minlength="6">

      <small>Minimal 6 karakter untuk password.</small>
      <br><br>
      <button type="submit">Daftar</button>
      &nbsp; <a href="login.php">Kembali ke Login</a>
    </form>
  </div>
</body>
</html>
