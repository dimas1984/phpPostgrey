<?php
// register_process.php
session_start();
// require_once 'koneksi.php'; // pastikan path benar ke file koneksi kamu
require __DIR__ . '/koneksi.php';
try {
    $conn = get_pg_connection(); // <-- PENTING: dapatkan koneksi dari fungsi
} catch (Throwable $e) {
    // log untuk debugging (opsional)
    error_log('DB connection error: ' . $e->getMessage());
    header('Location: register.php?error=' . urlencode('Gagal koneksi ke database. Hubungi admin.'));
    exit;
}

// cek method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php?error=' . urlencode('Akses tidak valid.'));
    exit;
}

if (!isset($conn) || $conn === false || $conn === null) {
    error_log('DB connection missing in register_process.php');
    header('Location: register.php?error=' . urlencode('Gagal koneksi ke database. Hubungi admin.'));
    exit;
}

// CSRF token
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    header('Location: register.php?error=' . urlencode('Token CSRF tidak valid. Coba lagi.'));
    exit;
}

// ambil dan sanitasi input
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

// validasi sederhana
if ($username === '' || $password === '' || $password_confirm === '') {
    header('Location: register.php?error=' . urlencode('Username dan password harus diisi.'));
    exit;
}

if (strlen($username) < 3 || strlen($username) > 100) {
    header('Location: register.php?error=' . urlencode('Panjang username 3-100 karakter.'));
    exit;
}

if (strlen($password) < 6) {
    header('Location: register.php?error=' . urlencode('Password minimal 6 karakter.'));
    exit;
}

if ($password !== $password_confirm) {
    header('Location: register.php?error=' . urlencode('Password dan konfirmasi tidak sama.'));
    exit;
}

// cek apakah username sudah ada
$check_sql = 'SELECT id FROM public.users WHERE username = $1 LIMIT 1';
var_dump($check_sql);
$check_res = pg_query_params($conn, $check_sql, array($username));
// var_dump($check_res);
// var_dump(pg_num_rows($check_res));
// die;
if (!$check_res) {
    // log internal: pg_last_error($conn)
    header('Location: register.php?error=' . urlencode('Terjadi kesalahan server.'));
    exit;
}
if (pg_num_rows($check_res) > 0) {
    header('Location: register.php?error=' . urlencode('Username sudah digunakan.'));
    exit;
}

// hash password dan simpan
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$insert_sql = 'INSERT INTO users (username, password_hash, full_name) VALUES ($1, $2, $3)';
$insert_res = pg_query_params($conn, $insert_sql, array($username, $password_hash, $full_name));
// var_dump($insert_res);
// die;

if ($insert_res) {
    // opsional: hapus token CSRF agar tidak reuse
    unset($_SESSION['csrf_token']);
    header('Location: login.php?success=' . urlencode('Pendaftaran berhasil. Silakan login.'));
    exit;
} else {
    // log internal: pg_last_error($conn)
    header('Location: register.php?error=' . urlencode('Gagal menyimpan data. Coba lagi.'));
    exit;
}
