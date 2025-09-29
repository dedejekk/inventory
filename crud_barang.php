<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "<script>
            alert('🚫 Akses ditolak! Halaman ini khusus admin.');
            window.location.href = 'index.php';
          </script>";
    exit;
}

// Ambil data barang
$query = mysqli_query($conn, "SELECT * FROM barang");
$barang = [];
while ($row = mysqli_fetch_assoc($query)) {
    $barang[] = $row;

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Inventory Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
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

  <!-- Layout utama (sidebar + konten) -->
  <div class="flex">

    <!-- Sidebar -->
    <aside class= "sidebar " space-y-4>
        <a href="index.php" class="block px-3 py-2 rounded-lg hover:bg-green-100">Dashboard</a>
        <a href="crud_barang.php" class="block px-3 py-2 rounded-lg hover:bg-green-100">crud_barang</a>
        <a href="laporan.php" class="block px-3 py-2 rounded-lg hover:bg-green-100">Laporan</a>
        <a href="login.php" class="block px-3 py-2 rounded-lg hover:bg-green-100">login</a>
      </nav>
    </aside>

  <div class="container mx-auto mt-8">

    <!-- Form Tambah Barang -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <h2 class="text-xl font-bold text-[#6b9071] mb-4">Tambah Barang</h2>
      <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-4">
        <div>
          <label class="block font-medium">Nama Barang</label>
          <input type="text" name="nama" required class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block font-medium">Qty</label>
          <input type="number" name="qty" required class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block font-medium">Status</label>
          <select name="status" class="w-full px-3 py-2 border rounded-lg">
            <option value="tersedia">Tersedia</option>
            <option value="dipinjam">Dipinjam</option>
          </select>
        </div>
        <div>
          <label class="block font-medium">Foto Barang</label>
          <input type="file" name="gambar" class="w-full">
        </div>
        <div class="col-span-2">
          <button type="submit" name="tambah"
            class="bg-[#0f2a1d] text-white px-6 py-2 rounded-lg hover:bg-[#6b9071]">
            Tambah
          </button>
        </div>
      </form>
    </div>

    <!-- Tabel Daftar Barang -->
    <h2 class="text-2xl font-bold mb-6 text-[#6b9071]">Daftar Barang</h2>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse text-sm md:text-base">
          <thead class="bg-[#0f2a1d] text-white">      <tr>
            <th class="px-4 py-2">Foto</th>
            <th class="px-4 py-2">Nama Barang</th>
            <th class="px-4 py-2">Qty</th>
            <th class="px-4 py-2">Total</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($barang) > 0): ?>
            <?php foreach ($barang as $item): ?>
              <tr class="border-b hover:bg-[#e0f7fa]">
                <td class="px-4 py-2 text-center">
                  <?php if (!empty($item['gambar'])): ?>
                  <img src="assets/uploads/<?= htmlspecialchars($item['gambar']) ?>" 
                  alt="<?= htmlspecialchars($item['nama_barang']) ?>" 
                  class="h-16 w-16 object-cover mx-auto rounded">
                  <?php else: ?>
                  <span class="text-gray-400 italic">No Image</span>
                  <?php endif; ?>
                  </td>
                  <td class="px-4 py-2 text-center"><?= $item['nama_barang'] ?></td>
                  <td class="px-4 py-2 text-center"><?= $item['stok'] ?></td>
                  <td class="px-4 py-2 text-center"><?= $item['stok'] ?> pcs</td>
                  <td class="px-4 py-2 text-center">
                  <span class="<?= $item['status'] == 'tersedia' ? 'status-tersedia' : 'status-dipinjam' ?>">
                    <?= ucfirst($item['status']) ?>
                  </span>
                  </td>
                <!-- Aksi -->
                <td class="px-4 py-2 text-center space-x-2">
                <a href="crud_barang.php?hapus=<?= $item['id'] ?>" 
                 onclick="return confirm('Yakin hapus barang ini?')"
                 class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                Hapus
                </a>
                <a href="edit.php?id=<?= $item['id'] ?>" 
                 class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                Edit
                </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-6 text-gray-500">Belum ada data barang</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
