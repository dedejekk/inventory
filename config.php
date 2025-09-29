<?php
// config.php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory.";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// -----------------------
// AMBIL DATA BARANG (opsional, beberapa file mengandalkan $barang)
// -----------------------
$barang = [];
$q_all = mysqli_query($conn, "SELECT * FROM barang");
if ($q_all) {
    while ($r = mysqli_fetch_assoc($q_all)) {
        $barang[] = $r;
    }
}

// -----------------------
// HELPERS kecil
// -----------------------
function safePost($k) {
    return isset($_POST[$k]) ? $_POST[$k] : null;
}
function colExists($conn, $table, $col) {
    $q = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$col'");
    return ($q && mysqli_num_rows($q) > 0);
}
function tableExists($conn, $table) {
    $q = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    return ($q && mysqli_num_rows($q) > 0);
}

// ================== TAMBAH BARANG ==================
if (isset($_POST['tambah'])) {
    $nama   = mysqli_real_escape_string($conn, safePost('nama'));
    $qty    = intval(safePost('qty'));
    $status = mysqli_real_escape_string($conn, safePost('status'));

    // Upload gambar
    $gambar = "";
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = __DIR__ . "/assets/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $gambar = time() . "_" . preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', basename($_FILES['gambar']['name']));
        $target_file = $target_dir . $gambar;
        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // gagal upload -> kosongkan nama gambar
            $gambar = "";
        }
    }

    $sql = "INSERT INTO barang (nama_barang, gambar, stok, status) VALUES ('$nama', '$gambar', $qty, '$status')";
    mysqli_query($conn, $sql);
    $barang_id = mysqli_insert_id($conn);

    // Jika saat tambah langsung tandai dipinjam, buat record peminjaman (default admin sebagai peminjam)
    if ($status === 'dipinjam') {
        $nama_peminjam = "Admin";
        $tgl_pinjam = date('Y-m-d');
        $catatan = '';
        mysqli_query($conn, "
            INSERT INTO peminjaman (barang_id, nama_peminjam, qty_pinjam, tgl_pinjam, catatan, status)
            VALUES ($barang_id, '$nama_peminjam', $qty, '$tgl_pinjam', '$catatan', 'dipinjam')
        ");
    }

    header("Location: index.php?msg=added");
    exit;
}

// ================== HAPUS BARANG ==================
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    // hapus gambar fisik jika ada
    $res = mysqli_query($conn, "SELECT gambar FROM barang WHERE id=$id LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $r = mysqli_fetch_assoc($res);
        if (!empty($r['gambar']) && file_exists(__DIR__ . "/assets/uploads/" . $r['gambar'])) {
            @unlink(__DIR__ . "/assets/uploads/" . $r['gambar']);
        }
    }
    // hapus barang (jika ada FK peminjaman with ON DELETE CASCADE, maka peminjaman juga akan terhapus)
    mysqli_query($conn, "DELETE FROM barang WHERE id=$id");
    header("Location: crud_barang.php?msg=deleted");
    exit;
}

// ================== EDIT BARANG ==================
if (isset($_POST['edit'])) {
    $id     = intval(safePost('id'));
    $nama   = mysqli_real_escape_string($conn, safePost('nama'));
    $qty    = intval(safePost('qty'));
    $status = mysqli_real_escape_string($conn, safePost('status'));

    // handle upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = __DIR__ . "/assets/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $gambar = time() . "_" . preg_replace('/[^A-Za-z0-9\-\_\.]/', '_', basename($_FILES['gambar']['name']));
        $target_file = $target_dir . $gambar;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // hapus gambar lama
            $old = mysqli_query($conn, "SELECT gambar FROM barang WHERE id=$id LIMIT 1");
            if ($old && mysqli_num_rows($old) > 0) {
                $or = mysqli_fetch_assoc($old);
                if (!empty($or['gambar']) && file_exists($target_dir . $or['gambar'])) {
                    @unlink($target_dir . $or['gambar']);
                }
            }
            $img_sql = " , gambar='$gambar' ";
        } else {
            $img_sql = "";
        }
    } else {
        $img_sql = "";
    }

    $sql = "UPDATE barang SET nama_barang='$nama', stok=$qty, status='$status' $img_sql WHERE id=$id";
    mysqli_query($conn, $sql);

    // Jika status diubah ke dipinjam -> catat peminjaman (jika belum ada peminjaman aktif untuk barang ini)
    if ($status === 'dipinjam') {
        // cek ada peminjaman aktif untuk barang ini?
        $check = mysqli_query($conn, "SELECT id FROM peminjaman WHERE barang_id=$id AND status='dipinjam' LIMIT 1");
        if (!$check || mysqli_num_rows($check) == 0) {
            $nama_peminjam = "Admin";
            $tgl_pinjam = date('Y-m-d');
            $catatan = '';
            mysqli_query($conn, "
                INSERT INTO peminjaman (barang_id, nama_peminjam, qty_pinjam, tgl_pinjam, catatan, status)
                VALUES ($id, '$nama_peminjam', $qty, '$tgl_pinjam', '$catatan', 'dipinjam')
            ");
        }
    }

    // Jika status diubah jadi tersedia -> tandai peminjaman aktif jadi dikembalikan
    if ($status === 'tersedia') {
        // update peminjaman aktif (bisa ada banyak, kita update semua yang masih dipinjam)
        if (colExists($conn, 'peminjaman', 'tgl_kembali')) {
            mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan', tgl_kembali=CURDATE() WHERE barang_id=$id AND status='dipinjam'");
        } else {
            mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan' WHERE barang_id=$id AND status='dipinjam'");
        }
    }

    header("Location: crud_barang.php?msg=updated");
    exit;
}

// ================== UBAH STATUS VIA GET ==================
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id     = intval($_GET['id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']); // tersedia / dipinjam
    mysqli_query($conn, "UPDATE barang SET status='$status' WHERE id=$id");
    header("Location: index.php?msg=status_changed");
    exit;
}

// ================== PROSES PINJAM ==================
if (isset($_POST['pinjam'])) {
    $barang_id     = intval(safePost('barang_id'));
    $nama_peminjam = mysqli_real_escape_string($conn, safePost('nama_peminjam'));
    $qty_pinjam    = intval(safePost('qty_pinjam'));
    $tgl_pinjam    = mysqli_real_escape_string($conn, safePost('tgl_pinjam'));
    $catatan       = mysqli_real_escape_string($conn, safePost('catatan'));

    // cek stok
    $cek = mysqli_query($conn, "SELECT stok FROM barang WHERE id=$barang_id LIMIT 1");
    $row = ($cek && mysqli_num_rows($cek) > 0) ? mysqli_fetch_assoc($cek) : null;

    if ($row && $row['stok'] >= $qty_pinjam && $qty_pinjam > 0) {
        // kurangi stok
        mysqli_query($conn, "UPDATE barang SET stok = stok - $qty_pinjam WHERE id=$barang_id");

        // simpan peminjaman (tgl_kembali kosong dulu)
        $sql = "INSERT INTO peminjaman (barang_id, nama_peminjam, qty_pinjam, tgl_pinjam, catatan, status)
                VALUES ($barang_id, '$nama_peminjam', $qty_pinjam, '$tgl_pinjam', '$catatan', 'dipinjam')";
        mysqli_query($conn, $sql);

        header("Location: dashboard.php?msg=pinjam_ok");
    } else {
        header("Location: pinjam.php?error=stok_kurang");
    }
    exit;
}

// ================== PENGEMBALIAN BARANG ==================
if (isset($_POST['kembalikan'])) {
    // ambil input POST (aman)
    $id_pinjam = intval(safePost('id_pinjam'));
    $barang_id = intval(safePost('barang_id'));
    $qty_kembali = intval(safePost('qty_kembali'));
    $tgl_kembali = !empty(safePost('tgl_kembali')) ? mysqli_real_escape_string($conn, safePost('tgl_kembali')) : date('Y-m-d');

    // jika barang_id/qty_kembali belum dikirim, ambil dari peminjaman
    if ($id_pinjam > 0 && ($barang_id === 0 || $qty_kembali === 0)) {
        $q = mysqli_query($conn, "SELECT barang_id, qty_pinjam FROM peminjaman WHERE id = $id_pinjam LIMIT 1");
        if ($q && mysqli_num_rows($q) > 0) {
            $r = mysqli_fetch_assoc($q);
            if ($barang_id === 0) $barang_id = intval($r['barang_id']);
            if ($qty_kembali === 0) $qty_kembali = intval($r['qty_pinjam']); // default kembalikan semua
        }
    }

    // validasi akhir
    if ($id_pinjam <= 0 || $barang_id <= 0 || $qty_kembali <= 0) {
        header("Location: pengembalian.php?error=invalid_data");
        exit;
    }

    // Ambil qty_pinjam saat ini
    $q2 = mysqli_query($conn, "SELECT qty_pinjam FROM peminjaman WHERE id = $id_pinjam LIMIT 1");
    $current_qty = 0;
    if ($q2 && mysqli_num_rows($q2) > 0) {
        $d = mysqli_fetch_assoc($q2);
        $current_qty = intval($d['qty_pinjam']);
    }

    // jika pengembalian >= current_qty -> seluruh dikembalikan
    if ($qty_kembali >= $current_qty) {
        if (colExists($conn, 'peminjaman', 'tgl_kembali')) {
            mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan', qty_pinjam=0, tgl_kembali='$tgl_kembali' WHERE id=$id_pinjam");
        } else {
            mysqli_query($conn, "UPDATE peminjaman SET status='dikembalikan', qty_pinjam=0 WHERE id=$id_pinjam");
        }
    } else {
        // hanya sebagian dikembalikan -> kurangi qty_pinjam
        $sisa = $current_qty - $qty_kembali;
        mysqli_query($conn, "UPDATE peminjaman SET qty_pinjam = $sisa WHERE id = $id_pinjam");
    }

    // tambahkan stok barang kembali
    mysqli_query($conn, "UPDATE barang SET stok = stok + $qty_kembali WHERE id = $barang_id");

    // simpan riwayat ke tabel pengembalian bila ada
    if (tableExists($conn, 'pengembalian')) {
        mysqli_query($conn, "
            INSERT INTO pengembalian (peminjaman_id, barang_id, nama_peminjam, qty_kembali, tgl_pinjam, tgl_pengembalian)
            SELECT id, barang_id, nama_peminjam, $qty_kembali, tgl_pinjam, '$tgl_kembali'
            FROM peminjaman
            WHERE id = $id_pinjam
        ");
    }

    header("Location: pengembalian.php?msg=success");
    exit;
}
// -------------------------
// FUNGSI HELPER (opsional)
// -------------------------

// Escape string untuk keamanan
function esc($value) {
    global $conn;
    return mysqli_real_escape_string($conn, $value);
}

// Ambil semua data dengan query (return array)
function getData($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result) {
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

// Buat export Excel sederhana
function exportToExcel($filename, $headers, $data) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");

    // Header kolom
    echo implode("\t", $headers) . "\n";

    // Data baris
    foreach ($data as $row) {
        echo implode("\t", $row) . "\n";
    }
    exit;
}
// Fungsi Export ke Word
function exportToWord($filename, $html) {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=$filename.doc");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<html>";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
    echo "<body>";
    echo $html;
    echo "</body>";
    echo "</html>";
}
?>
