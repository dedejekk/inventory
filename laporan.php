<?php
include 'config.php';


// Ambil bulan & tahun dari form (default: bulan & tahun sekarang)
$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');



// Barang dipinjam di bulan & tahun yang dipilih
$sql_pinjam = "
    SELECT p.id, b.nama_barang, p.nama_peminjam, p.qty_pinjam, p.tgl_pinjam, p.status
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    WHERE MONTH(p.tgl_pinjam) = $bulan AND YEAR(p.tgl_pinjam) = $tahun
";
$data_pinjam = mysqli_query($conn, $sql_pinjam);

// Barang dikembalikan di bulan & tahun yang dipilih (meskipun pinjamnya bulan lain)
$sql_kembali = "
    SELECT k.id, b.nama_barang, k.nama_peminjam, k.qty_kembali, k.tgl_pengembalian
    FROM pengembalian k
    JOIN barang b ON k.barang_id = b.id
    WHERE MONTH(k.tgl_pengembalian) = $bulan AND YEAR(k.tgl_pengembalian) = $tahun
";


$data_kembali = mysqli_query($conn, $sql_kembali);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-8">

    <div class="w-full max-w-6xl bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">📊 Laporan Bulanan</h2>

        <!-- Filter Bulan & Tahun -->
        <form method="GET" action="" class="flex flex-wrap gap-4 justify-center mb-8">
            <div>
                <label class="block mb-1 font-semibold">Bulan:</label>
                <select name="bulan" class="px-3 py-2 border rounded-lg">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= ($i == $bulan ? 'selected' : '') ?>>
                            <?= date("F", mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">Tahun:</label>
                <select name="tahun" class="px-3 py-2 border rounded-lg">
                    <?php for ($t = date('Y') - 5; $t <= date('Y'); $t++): ?>
                        <option value="<?= $t ?>" <?= ($t == $tahun ? 'selected' : '') ?>><?= $t ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Tampilkan</button>
            </div>
        </form>

        <!-- Barang Dipinjam -->
        <h3 class="text-xl font-semibold mb-3 text-gray-700">
            📦 Barang Dipinjam (<?= date("F Y", mktime(0, 0, 0, $bulan, 1, $tahun)) ?>)
        </h3>
        <div class="overflow-x-auto mb-8">
            <table class="w-full border-collapse bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Barang</th>
                        <th class="px-4 py-2 text-left">Peminjam</th>
                        <th class="px-4 py-2 text-center">Jumlah</th>
                        <th class="px-4 py-2 text-center">Tanggal Pinjam</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($data_pinjam)): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2"><?= $no++ ?></td>
                        <td class="px-4 py-2"><?= $row['nama_barang'] ?></td>
                        <td class="px-4 py-2"><?= $row['nama_peminjam'] ?></td>
                        <td class="px-4 py-2 text-center"><?= $row['qty_pinjam'] ?></td>
                        <td class="px-4 py-2 text-center"><?= $row['tgl_pinjam'] ?></td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 rounded-full text-sm 
                                <?= $row['status'] == 'dipinjam' ? 'bg-blue-200 text-blue-800' : 'bg-green-200 text-green-800' ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Barang Dikembalikan -->
        <h3 class="text-xl font-semibold mb-3 text-gray-700">
            ✅ Barang Dikembalikan (<?= date("F Y", mktime(0, 0, 0, $bulan, 1, $tahun)) ?>)
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white shadow-md rounded-lg overflow-hidden">
                <thead class="bg-green-600 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Barang</th>
                        <th class="px-4 py-2 text-left">Peminjam</th>
                        <th class="px-4 py-2 text-center">Jumlah Kembali</th>
                        <th class="px-4 py-2 text-center">Tanggal Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($row = mysqli_fetch_assoc($data_kembali)): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2"><?= $no++ ?></td>
                        <td class="px-4 py-2"><?= $row['nama_barang'] ?></td>
                        <td class="px-4 py-2"><?= $row['nama_peminjam'] ?></td>
                        <td class="px-4 py-2 text-center"><?= $row['qty_kembali'] ?></td>
                        <td class="px-4 py-2 text-center"><?= $row['tgl_pengembalian'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="flex justify-between mt-6"> 
    <a href="index.php" 
       class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500">
       Kembali ke Dashboard
    <a href="export_word.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
   class="px-6 py-2 rounded-lg bg-green-500 text-white hover:bg-green-600">
   Unduh Word
</a>
<a href="export_excel.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" 
   class="px-6 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
   Unduh Excel
</a>

        </div>
        </div>
    </div>
</body>
</html>

