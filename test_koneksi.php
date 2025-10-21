<?php
require __DIR__ . '/koneksi.php';
try {
  $conn = get_pg_connection();
  echo "Koneksi OK. Versi server: " . pg_parameter_status($conn, 'server_version');
} catch (Throwable $e) {
  echo "Koneksi gagal: " . $e->getMessage();
}
