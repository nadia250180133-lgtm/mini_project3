
# InventoryApp — Sistem Informasi Inventaris Barang

## 1. Deskripsi Project

InventoryApp adalah aplikasi berbasis web menggunakan PHP Native dan MySQL untuk mengelola persediaan barang, kategori, transaksi barang masuk, transaksi barang keluar, dan pengguna sistem.

Project ini dibuat sebagai tugas praktikum pemrograman web.

## 2. Tujuan Project

- Memudahkan pengelolaan data produk dan kategori.
- Mencatat transaksi barang masuk dan barang keluar.
- Memantau jumlah stok barang.
- Mengurangi kesalahan pencatatan persediaan.
- Menerapkan validasi input dan autentikasi pengguna.

## 3. Fitur Aplikasi

### Dashboard
- Menampilkan ringkasan informasi inventaris.

### Manajemen Produk
- Menampilkan daftar produk.
- Menambahkan produk.
- Mengubah data produk.
- Menghapus produk.
- Mencari dan memfilter produk.

### Manajemen Kategori
- Menampilkan dan mengelola kategori produk.

### Transaksi Stok
- Mencatat barang masuk.
- Mencatat barang keluar.
- Memperbarui jumlah stok sesuai transaksi.

### Manajemen Pengguna
- Mengelola data pengguna.
- Mendukung pembatasan akses sesuai implementasi aplikasi.

### Validasi
- Memeriksa input wajib.
- Mencegah data tidak valid sesuai aturan yang diterapkan.

## 4. Teknologi

- PHP Native
- MySQL
- Apache
- XAMPP
- phpMyAdmin
- Git dan GitHub

## 5. Struktur Folder

```text
mini_project3/
├── config/
│   └── database.php
├── database/
│   └── schema.sql
├── helpers/
│   ├── auth.php
│   ├── escape.php
│   └── validation.php
├── public/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── products/
│   │   ├── categories.php
│   │   ├── create.php
│   │   ├── delete.php
│   │   ├── edit.php
│   │   └── index.php
│   ├── stock/
│   │   ├── in.php
│   │   ├── out.php
│   │   └── test.php
│   └── users.php
├── views/
│   ├── header.php
│   └── footer.php
├── index.php
├── .gitignore
└── README.md
```

## 6. Persyaratan Sistem

- XAMPP
- PHP
- MySQL atau MariaDB
- Browser web

## 7. Cara Instalasi

1. Salin folder `mini_project3` ke `C:\xampp\htdocs\`.
2. Buka XAMPP Control Panel.
3. Jalankan Apache dan MySQL.
4. Buka `http://localhost/phpmyadmin`.
5. Buat database sesuai konfigurasi aplikasi.
6. Pilih database, lalu impor file `database/schema.sql`.
7. Periksa konfigurasi database di `config/database.php`.
8. Buka aplikasi melalui browser.

URL aplikasi:

`http://localhost/mini_project3/public/`

Jika belum terbuka, periksa konfigurasi URL dan file `index.php`.

## 8. Struktur Database

Struktur database disimpan dalam file `database/schema.sql`.

File tersebut digunakan untuk membuat tabel dan relasi yang diperlukan oleh aplikasi. Struktur tabel yang sebenarnya mengikuti isi file SQL tersebut.

## 9. Aturan Bisnis

1. Produk harus memiliki data sesuai kolom wajib.
2. SKU harus unik jika aturan tersebut diterapkan.
3. Produk dikelompokkan berdasarkan kategori.
4. Barang masuk menambah stok.
5. Barang keluar mengurangi stok.
6. Barang keluar tidak boleh melebihi stok jika validasi tersebut diterapkan.
7. Hak akses mengikuti autentikasi dan peran pengguna yang tersedia.

## 10. Pengujian Aplikasi

| No. | Pengujian | Hasil yang diharapkan |
|---|---|---|
| 1 | Membuka halaman login | Form login tampil |
| 2 | Login dengan data salah | Login ditolak |
| 3 | Input produk kosong | Validasi ditampilkan |
| 4 | Menambah produk | Data tersimpan |
| 5 | Mengubah produk | Data diperbarui |
| 6 | Menghapus produk | Data terhapus sesuai aturan |
| 7 | Barang masuk | Stok bertambah |
| 8 | Barang keluar | Stok berkurang |
| 9 | Barang keluar melebihi stok | Ditolak jika validasi aktif |

Hasil akhir pengujian harus mengikuti hasil pengujian aktual aplikasi.

## 11. Catatan Keamanan

- Jangan mengunggah password atau kredensial rahasia.
- Gunakan validasi input sisi server.
- Gunakan escaping saat menampilkan data.
- Batasi akses halaman sesuai peran pengguna.
- Gunakan database pengujian untuk percobaan.

## 12. Repository GitHub

https://github.com/nadia250180133-lgtm/mini_project3

## 13. Penutup

InventoryApp merupakan project pembelajaran pengembangan aplikasi inventaris menggunakan PHP Native dan MySQL. Dokumentasi ini menjelaskan fitur, struktur folder, instalasi, database, dan pengujian dasar aplikasi.
