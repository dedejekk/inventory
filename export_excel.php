<?php
include 'config.php';

$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Ambil data pinjam
$sql_pinjam = "
    SELECT b.nama_barang, p.nama_peminjam, p.qty_pinjam, p.tgl_pinjam, p.status
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    WHERE MONTH(p.tgl_pinjam) = $bulan AND YEAR(p.tgl_pinjam) = $tahun
";
$res_pinjam = mysqli_query($conn, $sql_pinjam);

// Ambil data kembali
$sql_kembali = "
    SELECT b.nama_barang, k.nama_peminjam, k.qty_kembali, k.tgl_pengembalian
    FROM pengembalian k
    JOIN barang b ON k.barang_id = b.id
    WHERE MONTH(k.tgl_pengembalian) = $bulan AND YEAR(k.tgl_pengembalian) = $tahun
";
$res_kembali = mysqli_query($conn, $sql_kembali);

$data = [];

// Gabungkan data pinjam
while ($row = mysqli_fetch_assoc($res_pinjam)) {
    $data[] = [
        $row['nama_barang'],
        $row['nama_peminjam'],
        $row['qty_pinjam'],
        $row['tgl_pinjam'],
        $row['status'],
        "Pinjam"
    ];
}

// Gabungkan data kembali
while ($row = mysqli_fetch_assoc($res_kembali)) {
    $data[] = [
        $row['nama_barang'],
        $row['nama_peminjam'],
        $row['qty_kembali'],
        $row['tgl_pengembalian'],
        "Dikembalikan",
        "Kembali"
    ];
}

$headers = ["Barang", "Peminjam", "Jumlah", "Tanggal", "Status", "Jenis"];
$filename = "laporan_{$bulan}_{$tahun}";

exportToExcel($filename, $data, $headers);
?>
