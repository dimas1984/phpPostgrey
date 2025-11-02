# Proyek CRUD Mahasiswa Sederhana (PHP + PostgreSQL)

Ini adalah proyek aplikasi web sederhana untuk mengelola data mahasiswa menggunakan PHP natif (tanpa *framework*) dan database PostgreSQL. Proyek ini mencakup fungsionalitas dasar **CRUD** (Create, Read, Update, Delete).

## Fitur

* **Create**: Menambah data mahasiswa baru (NIM, Nama, Email, Jurusan).
* **Read**: Menampilkan semua data mahasiswa dalam tabel yang terurut.
* **Update**: Mengubah data mahasiswa yang sudah ada.
* **Delete**: Menghapus data mahasiswa dengan konfirmasi.

## Teknologi

* **Frontend**: HTML & CSS (inline).
* **Backend**: PHP (Natif).
* **Database**: PostgreSQL.

---

## Prasyarat

1.  Web Server (seperti Apache/Nginx, atau XAMPP/Laragon).
2.  PHP dengan ekstensi `pgsql` aktif di `php.ini`.
3.  Server Database PostgreSQL yang sedang berjalan.

---

## Instalasi & Setup

### 1. Database Setup

Proyek ini mengharapkan database dan tabel sudah ada.

**A. Buat Database**
Buat database di PostgreSQL dengan nama `Belajar`.

**B. Buat Tabel**
Jalankan *query* SQL berikut di database `Belajar` Anda untuk membuat tabel `mahasiswa`:

```sql
CREATE TABLE public.mahasiswa (
    id SERIAL PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    jurusan VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
