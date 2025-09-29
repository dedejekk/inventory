<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID barang dari URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php?msg=no_id");
    exit;
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    header("Location: dashboard.php?msg=not_found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
    <h2 class="text-2xl font-bold text-green-700 mb-6">Edit Barang</h2>

    <form action="config.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <!-- kirim id ke config.php -->
      <input type="hidden" name="id" value="<?= $barang['id'] ?>">

      <div>
        <label class="block font-medium">Nama Barang</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($barang['nama_barang']) ?>" 
               required class="w-full px-3 py-2 border rounded-lg">
      </div>
      <div>
        <label class="block font-medium">Qty</label>
        <input type="number" name="qty" value="<?= $barang['stok'] ?>" required 
               class="w-full px-3 py-2 border rounded-lg">
      </div>
      <div>
        <label class="block font-medium">Status</label>
        <select name="status" class="w-full px-3 py-2 border rounded-lg">
          <option value="tersedia" <?= $barang['status']=='tersedia'?'selected':'' ?>>Tersedia</option>
          <option value="dipinjam" <?= $barang['status']=='dipinjam'?'selected':'' ?>>Dipinjam</option>
        </select>
      </div>
      <div>
        <label class="block font-medium">Foto Barang</label>
        <?php if (!empty($barang['gambar'])): ?>
          <img src="assets/uploads<?= $barang['gambar'] ?>" alt="Foto lama" class="h-20 mb-2 rounded">
        <?php endif; ?>
        <input type="file" name="gambar" class="w-full">
      </div>
      <div class="flex justify-between">
        <a href="crud_barang.php" class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500">Batal</a>
        <button type="submit" name="edit" 
                class="px-6 py-2 rounded-lg bg-green-700 text-white hover:bg-green-800">
          Update
        </button>
      </div>
    </form>
  </div>

</body>
</html>
