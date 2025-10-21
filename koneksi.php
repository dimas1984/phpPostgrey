<?php
// koneksi.php
// Pastikan ekstensi pgsql aktif di php.ini

function get_pg_connection(): PgSql\Connection {
    static $conn = null;
    if ($conn instanceof PgSql\Connection) {
        return $conn;
    }

    $connStr = "host=localhost port=5432 dbname=Belajar user=postgres password=12345 options='--client_encoding=UTF8'";
    $conn = @pg_connect($connStr);

    if (!$conn) {
        // Jangan pakai @ di produksi; untuk debug bisa tampilkan detail
        throw new RuntimeException("Koneksi PostgreSQL gagal. Periksa host/port/db/user/pass & ekstensi pgsql.");
    }
    return $conn;
}

/** Helper sederhana untuk aman menjalankan query dengan parameter */
function qparams(string $sql, array $params) {
    $conn = get_pg_connection();
    $res = @pg_query_params($conn, $sql, $params);
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}

function q(string $sql) {
    $conn = get_pg_connection();
    $res = @pg_query($conn, $sql);
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}
