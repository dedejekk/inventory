# 📦 Sistem Inventory Barang

Aplikasi sederhana untuk manajemen peminjaman & pengembalian barang menggunakan **PHP + MySQL**.
Fitur ini mendukung CRUD barang, peminjaman, pengembalian, serta laporan bulanan yang bisa diekspor ke **Excel/Word**.

---

## 🚀 Fitur Utama

* Login User & Admin
* CRUD Barang (tambah, edit, hapus, upload gambar)
* Peminjaman Barang
* Pengembalian Barang
* Laporan Bulanan:

  * Barang dipinjam
  * Barang dikembalikan
  * Ekspor ke Excel & Word

---

## 🛠️ Teknologi

* **PHP** (Native)
* **MySQL**
* **TailwindCSS** (UI)
* **phpMyAdmin** (opsional, untuk manajemen DB)

---

## 📂 Struktur Project

```
/inventory
│── config.php
│── dashboard.php
│── crud_barang.php
│── pinjam.php
│── pengembalian.php
│── laporan.php
│── export_excel.php
│── export_word.php
│── assets/
│   └── uploads/   (folder untuk gambar barang)
```

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/inventory.git
cd inventory
```

### 2. Setup Database

* Buat database baru di MySQL:

  ```sql
  CREATE DATABASE inventory;
  ```
* Import file `inventory.sql` ke MySQL (via phpMyAdmin atau command line):

  ```bash
  mysql -u root -p inventory < inventory.sql
  ```

### 3. Konfigurasi

Edit file `config.php` sesuai dengan koneksi MySQL kamu:

```php
$host = "localhost";
$user = "root"; // default XAMPP
$pass = "";     // default kosong
$db   = "inventory";
```

### 4. Jalankan

* Letakkan project di folder `htdocs` (jika pakai XAMPP).
* Buka browser:

  ```
  http://localhost/inventory/dashboard.php
  ```

---

## 🔑 Akun Default

* **Admin**

  * Username: `Muhamad`
  * Password: `dede`

*(Password disimpan dengan MD5 di database, ubah sesuai kebutuhan)*

---

## 📜 Lisensi

Proyek ini bersifat open-source. Silakan dikembangkan sesuai kebutuhan.
