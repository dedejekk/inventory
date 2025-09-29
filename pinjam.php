<?php
include "config.php";

// Ambil daftar barang yang tersedia
$barang = mysqli_query($conn, "SELECT * FROM barang WHERE status='tersedia'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Peminjaman</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-6 md:p-8 rounded-lg shadow-md w-full max-w-lg mx-2">
    <h2 class="text-2xl font-bold text-green-700 mb-6 text-center">Form Peminjaman</h2>

    <form action="config.php" method="POST" class="space-y-4">
      <!-- Nama Peminjam -->
      <div>
        <label class="block font-medium">Nama Peminjam</label>
        <input type="text" name="nama_peminjam" required 
               class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200">
      </div>

      <!-- Pilih Barang -->
      <div>
        <label class="block font-medium">Pilih Barang</label>
        <select name="barang_id" required 
                class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200">
          <option value="">-- Pilih Barang --</option>
          <?php while ($row = mysqli_fetch_assoc($barang)): ?>
            <option value="<?= $row['id'] ?>">
              <?= $row['nama_barang'] ?> (Stok: <?= $row['stok'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Jumlah Pinjam -->
      <div>
        <label class="block font-medium">Jumlah Pinjam</label>
        <input type="number" name="qty_pinjam" min="1" required
               class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200">
      </div>

      <!-- Tanggal Pinjam -->
      <div>
        <label class="block font-medium">Tanggal Pinjam</label>
        <input type="date" name="tgl_pinjam" required
               class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200">
      </div>

      <!-- Catatan -->
      <div>
        <label class="block font-medium">Catatan (opsional)</label>
        <textarea name="catatan" rows="3" 
                  class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-green-200"></textarea>
      </div>

      <!-- Tombol -->
      <div class="flex justify-between">
        <a href="index.php" 
           class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500">
          Batal
        </a>
        <button type="submit" name="pinjam" 
                class="px-6 py-2 rounded-lg bg-green-700 text-white hover:bg-green-800">
          Pinjam
        </button>
      </div>
    </form>
  </div>

</body>
</html>
