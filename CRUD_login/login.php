<?php
session_start();
// jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; padding: 2rem; background:#f6f6f6; }
    .card { background:white; padding:1.5rem; border-radius:8px; max-width:400px; margin: 2rem auto; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    input[type="text"], input[type="password"] { width:100%; padding:0.5rem; margin:0.5rem 0; box-sizing:border-box; }
    button { padding:0.6rem 1rem; }
    .error { color: #b00020; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Masuk</h2>
    <?php if (!empty($_GET['error'])): ?>
      <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    <form action="authenticate.php" method="post" autocomplete="off">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required autofocus>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button type="submit">Login</button>
      <br>
      <br>

       <!-- <button type="submit">Daftar</button> -->
        <a class="btn" href="register.php">Register</a>
    </form>
  </div>
</body>
</html>
