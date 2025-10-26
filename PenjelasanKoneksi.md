<?php
// koneksi.php
// Pastikan ekstensi pgsql aktif di php.ini

// 1. Fungsi Utama Koneksi
function get_pg_connection(): PgSql\Connection {
    // 'static $conn' adalah variabel statis. 
    // Nilainya tetap tersimpan di memori setelah fungsi selesai.
    // Ini memastikan koneksi database HANYA DIBUAT SEKALI per request.
    static $conn = null;
    
    // Jika koneksi sudah ada, langsung kembalikan koneksi yang sama.
    if ($conn instanceof PgSql\Connection) {
        return $conn;
    }

    // String koneksi untuk ke database PostgreSQL
    $connStr = "host=localhost port=5432 dbname=Belajar user=postgres password=12345 options='--client_encoding=UTF8'";
    
    // @pg_connect: Mencoba terhubung ke DB. 
    // Tanda '@' menekan pesan error default PHP.
    $conn = @pg_connect($connStr);

    // Jika koneksi gagal, hentikan skrip dengan melempar error (Exception)
    if (!$conn) {
        throw new RuntimeException("Koneksi PostgreSQL gagal. Periksa host/port/db/user/pass & ekstensi pgsql.");
    }
    
    // Kembalikan koneksi yang berhasil dibuat
    return $conn;
}

/** * 2. Helper untuk query AMAN (dengan parameter) 
 * Fungsi ini digunakan untuk CREATE, UPDATE, DELETE
 */
function qparams(string $sql, array $params) {
    // Ambil koneksi (yang sudah ada atau buat baru jika belum ada)
    $conn = get_pg_connection();
    
    // Jalankan query dengan parameter terpisah. 
    // Ini adalah metode paling aman untuk mencegah SQL INJECTION.
    $res = @pg_query_params($conn, $sql, $params);
    
    // Jika query gagal, hentikan skrip dan tampilkan pesan error
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}

/** * 3. Helper untuk query SEDERHANA (tanpa parameter) 
 * Fungsi ini digunakan untuk SELECT sederhana
 */
function q(string $sql) {
    $conn = get_pg_connection();
    
    // Jalankan query biasa (tanpa parameter)
    $res = @pg_query($conn, $sql);
    
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}

?>