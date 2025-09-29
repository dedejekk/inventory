<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "config.php";

// Barang tersedia
$q_tersedia = mysqli_query($conn, "SELECT * FROM barang WHERE status='tersedia'");
$barang_tersedia = [];
while ($row = mysqli_fetch_assoc($q_tersedia)) {
    $barang_tersedia[] = $row;
}

// Barang dipinjam (join peminjaman + barang)
$q_pinjam = mysqli_query($conn, "
    SELECT p.*, b.nama_barang, b.gambar 
    FROM peminjaman p
    JOIN barang b ON p.barang_id=b.id
    WHERE p.status='dipinjam'
");
$barang_dipinjam = [];
while ($row = mysqli_fetch_assoc($q_pinjam)) {
    $barang_dipinjam[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Inventory Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <!-- Navbar -->
  <nav class="bg-[#0f2a1d] p-4 text-white flex justify-between items-center">
    <h1 class="font-bold text-xl">Inventory Barang</h1>
    <div>
      <span class="mr-4">👋 <?= $_SESSION['username'] ?> (<?= $_SESSION['role'] ?>)</span>
      <a href="logout.php" class="bg-[#6b9071] text-[#0f2a1d] px-3 py-1 rounded hover:bg-white">Logout</a>
    </div>
  </nav>

  <div class="flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-4">
      <nav class="space-y-2">
        <a href="index.php" class="block px-3 py-2 rounded hover:bg-green-100">Dashboard</a>
        <a href="pinjam.php" class="block px-3 py-2 rounded hover:bg-green-100">Pinjam</a>
        <a href="pengembalian.php" class="block px-3 py-2 rounded hover:bg-green-100">Pengembalian</a>
        <a href="laporan.php" class="block px-3 py-2 rounded-lg hover:bg-green-100">Laporan</a>
        <a href="crud_barang.php" class="block px-3 py-2 rounded hover:bg-green-100">CRUD Barang</a>
      </nav>
    </aside>

    <!-- Konten -->
    <main class="flex-1 p-6 space-y-10">

      <!-- Tabel Barang Tersedia -->
      <div>
        <h2 class="text-2xl font-bold mb-4 text-green-700">Barang Tersedia</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="w-full border-collapse">
            <thead class="bg-[#0f2a1d] text-white">
              <tr>
                <th class="px-4 py-2">Foto</th>
                <th class="px-4 py-2">Nama Barang</th>
                <th class="px-4 py-2">Stok</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($barang_tersedia) > 0): ?>
                <?php foreach ($barang_tersedia as $row): ?>
                  <tr class="border-b hover:bg-green-50">
                    <td class="px-4 py-2 text-center">
                      <?php if (!empty($row['gambar'])): ?>
                        <img src="assets/uploads/<?= $row['gambar'] ?>" class="h-16 w-16 object-cover mx-auto rounded">
                      <?php else: ?>
                        <span class="text-gray-400 italic">No Image</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td class="px-4 py-2 text-center"><?= $row['stok'] ?> pcs</td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center py-6 text-gray-500">Tidak ada barang tersedia</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabel Barang Dipinjam -->
      <div>
        <h2 class="text-2xl font-bold mb-4 text-green-700">Barang Dipinjam</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <table class="w-full border-collapse">
            <thead class="bg-[#0f2a1d] text-white">
              <tr>
                <th class="px-4 py-2">Foto</th>
                <th class="px-4 py-2">Nama Barang</th>
                <th class="px-4 py-2">Peminjam</th>
                <th class="px-4 py-2">Jumlah</th>
                <th class="px-4 py-2">Tanggal Pinjam</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($barang_dipinjam) > 0): ?>
                <?php foreach ($barang_dipinjam as $row): ?>
                  <tr class="border-b hover:bg-green-50">
                    <td class="px-4 py-2 text-center">
                      <?php if (!empty($row['gambar'])): ?>
                        <img src="assets/uploads/<?= $row['gambar'] ?>" class="h-16 w-16 object-cover mx-auto rounded">
                      <?php else: ?>
                        <span class="text-gray-400 italic">No Image</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td class="px-4 py-2 text-center"><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                    <td class="px-4 py-2 text-center"><?= $row['qty_pinjam'] ?> pcs</td>
                    <td class="px-4 py-2 text-center"><?= $row['tgl_pinjam'] ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center py-6 text-gray-500">Belum ada barang dipinjam</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</body>
</html>
