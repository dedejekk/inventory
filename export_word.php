<?php
include 'config.php';

$bulan = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('m');
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : date('Y');

// Data pinjam
$sql_pinjam = "
    SELECT b.nama_barang, p.nama_peminjam, p.qty_pinjam, p.tgl_pinjam, p.status
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    WHERE MONTH(p.tgl_pinjam) = $bulan AND YEAR(p.tgl_pinjam) = $tahun
";
$res_pinjam = mysqli_query($conn, $sql_pinjam);

// Data kembali
$sql_kembali = "
    SELECT b.nama_barang, k.nama_peminjam, k.qty_kembali, k.tgl_pengembalian
    FROM pengembalian k
    JOIN barang b ON k.barang_id = b.id
    WHERE MONTH(k.tgl_pengembalian) = $bulan AND YEAR(k.tgl_pengembalian) = $tahun
";
$res_kembali = mysqli_query($conn, $sql_kembali);

$html = "<h2 style='text-align:center;'>Laporan Bulanan</h2>";
$html .= "<h3>Barang Dipinjam (".date("F Y", mktime(0,0,0,$bulan,1,$tahun)).")</h3>";
$html .= "<table border='1' cellspacing='0' cellpadding='5'>
<tr>
<th>No</th><th>Barang</th><th>Peminjam</th><th>Jumlah</th><th>Tanggal Pinjam</th><th>Status</th>
</tr>";

$no = 1;
while($row = mysqli_fetch_assoc($res_pinjam)) {
    $html .= "<tr>
        <td>".$no++."</td>
        <td>".$row['nama_barang']."</td>
        <td>".$row['nama_peminjam']."</td>
        <td>".$row['qty_pinjam']."</td>
        <td>".$row['tgl_pinjam']."</td>
        <td>".$row['status']."</td>
    </tr>";
}
$html .= "</table><br>";

$html .= "<h3>Barang Dikembalikan (".date("F Y", mktime(0,0,0,$bulan,1,$tahun)).")</h3>";
$html .= "<table border='1' cellspacing='0' cellpadding='5'>
<tr>
<th>No</th><th>Barang</th><th>Peminjam</th><th>Jumlah Kembali</th><th>Tanggal Pengembalian</th>
</tr>";

$no = 1;
while($row = mysqli_fetch_assoc($res_kembali)) {
    $html .= "<tr>
        <td>".$no++."</td>
        <td>".$row['nama_barang']."</td>
        <td>".$row['nama_peminjam']."</td>
        <td>".$row['qty_kembali']."</td>
        <td>".$row['tgl_pengembalian']."</td>
    </tr>";
}
$html .= "</table>";

$filename = "laporan_{$bulan}_{$tahun}";
exportToWord($filename, $html);
?>
