<?php
session_start();
include "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil daftar barang yang masih dipinjam
$pinjam = mysqli_query($conn, "
    SELECT p.id, p.barang_id, p.nama_peminjam, p.qty_pinjam, p.tgl_pinjam, p.status,
           b.nama_barang, b.gambar
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    WHERE p.status='dipinjam'
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Pengembalian Barang</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="bg-white p-6 md:p-8 rounded-lg shadow-md w-full max-w-5xl mx-2">
    <h2 class="text-2xl font-bold text-green-700 mb-8 text-center">Form Pengembalian Barang</h2>

    <?php if (mysqli_num_rows($pinjam) > 0): ?>
      <div class="overflow-x-auto">
        <table class="w-full border-collapse bg-white shadow rounded-lg">
          <thead class="bg-green-700 text-white">
            <tr>
              <th class="px-6 py-3 text-left">Foto</th>
              <th class="px-6 py-3 text-left">Barang</th>
              <th class="px-6 py-3 text-center">Peminjam</th>
              <th class="px-6 py-3 text-center">Qty</th>
              <th class="px-6 py-3 text-center">Tanggal Pinjam</th>
              <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php while ($row = mysqli_fetch_assoc($pinjam)): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-3">
                  <?php if (!empty($row['gambar'])): ?>
                    <img src="assets/uploads/<?= htmlspecialchars($row['gambar']) ?>" 
                         alt="<?= htmlspecialchars($row['nama_barang']) ?>" 
                         class="h-14 w-14 object-cover rounded border">
                  <?php else: ?>
                    <span class="text-gray-400 italic">No Image</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-3 font-medium"><?= htmlspecialchars($row['nama_barang']) ?></td>
                <td class="px-6 py-3 text-center"><?= htmlspecialchars($row['nama_peminjam']) ?></td>
                <td class="px-6 py-3 text-center"><?= intval($row['qty_pinjam']) ?></td>
                <td class="px-6 py-3 text-center"><?= htmlspecialchars($row['tgl_pinjam']) ?></td>
                <td class="px-6 py-3 text-center">
                  <form action="config.php" method="POST" 
                        onsubmit="return confirm('Yakin barang ini sudah dikembalikan?')" 
                        class="flex flex-col md:flex-row md:items-center gap-2 justify-center">
                    
                    <input type="hidden" name="id_pinjam" value="<?= intval($row['id']) ?>">
                    <input type="hidden" name="barang_id" value="<?= intval($row['barang_id']) ?>">
                    <input type="hidden" name="qty_pinjam" value="<?= intval($row['qty_pinjam']) ?>">

                    <select name="qty_kembali" class="border rounded px-3 py-2">
                      <?php for ($i = 1; $i <= intval($row['qty_pinjam']); $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                      <?php endfor; ?>
                    </select>

                    <input type="date" name="tgl_pengembalian" 
                           value="<?= date('Y-m-d') ?>" 
                           required class="border rounded px-3 py-2">

                    <button type="submit" name="kembalikan" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                      Kembalikan
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-500">Tidak ada barang yang sedang dipinjam.</p>
    <?php endif; ?>

    <div class="flex justify-center mt-8">
      <a href="dashboard.php" 
         class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500">
        Kembali ke Dashboard
      </a>
    </div>
  </div>

</body>
</html>
