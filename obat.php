<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Simpan / Update Obat
if (isset($_POST['simpan'])) {
    $id_obat   = $_POST['id_obat'];
    $nama_obat = $_POST['nama_obat'];
    $satuan    = $_POST['satuan'];
    $stok      = $_POST['stok'];

    if ($id_obat == "") {
        $query = "INSERT INTO obat (nama_obat, satuan, stok) VALUES ('$nama_obat', '$satuan', '$stok')";
    } else {
        $query = "UPDATE obat SET nama_obat='$nama_obat', satuan='$satuan', stok='$stok' WHERE id_obat='$id_obat'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: obat.php");
        exit();
    } else {
        die("Gagal menyimpan data obat: " . mysqli_error($koneksi));
    }
}

// Hapus Obat
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (mysqli_query($koneksi, "DELETE FROM obat WHERE id_obat='$id'")) {
        header("Location: obat.php");
        exit();
    } else {
        die("Gagal menghapus data obat: " . mysqli_error($koneksi));
    }
}

// Ambil Data untuk Edit
$edit_data = ['id_obat'=>'', 'nama_obat'=>'', 'satuan'=>'', 'stok'=>''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM obat WHERE id_obat='$id'");
    if ($res && mysqli_num_rows($res) > 0) {
        $edit_data = mysqli_fetch_assoc($res);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Obat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-notes-medical"></i>
            <h2>Rekam Medis</h2>
        </div>
        <nav>
            <a href="index.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="pasien.php"><i class="fa-solid fa-user-injured"></i> Data Pasien</a>
            <a href="dokter.php"><i class="fa-solid fa-user-doctor"></i> Data Dokter</a>
            <a href="obat.php" class="active"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <a href="rekam_medis.php"><i class="fa-solid fa-file-medical"></i> Rekam Medis</a>
        </nav>
    </div>

    <div class="main-content">
        <h2 style="color: var(--primary-dark); margin-bottom: 15px;">
            <i class="fa-solid <?= ($edit_data['id_obat'] != '') ? 'fa-pen-to-square' : 'fa-plus'; ?>"></i>
            <?= ($edit_data['id_obat'] != '') ? 'Edit Data Obat' : 'Tambah Data Obat'; ?>
        </h2>
        
        <div class="table-container">
            <form action="obat.php" method="POST">
                <input type="hidden" name="id_obat" value="<?= $edit_data['id_obat']; ?>">
                
                <div class="form-group">
                    <label>Nama Obat</label>
                    <input type="text" name="nama_obat" value="<?= $edit_data['nama_obat']; ?>" placeholder="Contoh: Paracetamol 500mg" required>
                </div>
                <div class="form-group">
                    <label>Satuan</label>
                    <select name="satuan" required>
                        <option value="">-- Pilih Satuan --</option>
                        <option value="Tablet" <?= ($edit_data['satuan'] == 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                        <option value="Kaplet" <?= ($edit_data['satuan'] == 'Kaplet') ? 'selected' : ''; ?>>Kaplet</option>
                        <option value="Kapsul" <?= ($edit_data['satuan'] == 'Kapsul') ? 'selected' : ''; ?>>Kapsul</option>
                        <option value="Botol" <?= ($edit_data['satuan'] == 'Botol') ? 'selected' : ''; ?>>Botol</option>
                        <option value="Salep" <?= ($edit_data['satuan'] == 'Salep') ? 'selected' : ''; ?>>Salep</option>
                        <option value="Ampul" <?= ($edit_data['satuan'] == 'Ampul') ? 'selected' : ''; ?>>Ampul</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stok</label>
                    <input type="number" name="stok" value="<?= $edit_data['stok']; ?>" placeholder="Contoh: 100" min="0" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Obat
                </button>
                <?php if ($edit_data['id_obat'] != '') : ?>
                    <a href="obat.php" class="btn" style="background:#e0e0e0; color:#333; margin-left:8px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fa-solid fa-list-ul"></i> Daftar Obat</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM obat ORDER BY id_obat DESC");
                    if ($query && mysqli_num_rows($query) > 0) {
                        while ($o = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= $o['nama_obat']; ?></strong></td>
                        <td><?= $o['satuan']; ?></td>
                        <td><?= $o['stok']; ?></td>
                        <td>
                            <a href="obat.php?edit=<?= $o['id_obat']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="obat.php?hapus=<?= $o['id_obat']; ?>" class="btn btn-delete" onclick="return confirm('Hapus data obat ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; color:#888;'>Belum ada data obat.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>