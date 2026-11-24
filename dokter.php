<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Simpan / Update Dokter
if (isset($_POST['simpan'])) {
    $id_dokter   = $_POST['id_dokter'];
    $nama_dokter = $_POST['nama_dokter'];
    $spesialisasi= $_POST['spesialisasi'];

    if ($id_dokter == "") {
        $query = "INSERT INTO dokter (nama_dokter, spesialisasi) VALUES ('$nama_dokter', '$spesialisasi')";
    } else {
        $query = "UPDATE dokter SET nama_dokter='$nama_dokter', spesialisasi='$spesialisasi' WHERE id_dokter='$id_dokter'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: dokter.php");
        exit();
    } else {
        die("Gagal menyimpan data dokter: " . mysqli_error($koneksi));
    }
}

// Hapus Dokter
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (mysqli_query($koneksi, "DELETE FROM dokter WHERE id_dokter='$id'")) {
        header("Location: dokter.php");
        exit();
    } else {
        die("Gagal menghapus data dokter: " . mysqli_error($koneksi));
    }
}

// Ambil Data untuk Edit
$edit_data = ['id_dokter'=>'', 'nama_dokter'=>'', 'spesialisasi'=>''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM dokter WHERE id_dokter='$id'");
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
    <title>Data Dokter</title>
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
            <a href="dokter.php" class="active"><i class="fa-solid fa-user-doctor"></i> Data Dokter</a>
            <a href="obat.php"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <a href="rekam_medis.php"><i class="fa-solid fa-file-medical"></i> Rekam Medis</a>
        </nav>
    </div>

    <div class="main-content">
        <h2 style="color: var(--primary-dark); margin-bottom: 15px;">
            <i class="fa-solid <?= ($edit_data['id_dokter'] != '') ? 'fa-pen-to-square' : 'fa-user-plus'; ?>"></i>
            <?= ($edit_data['id_dokter'] != '') ? 'Edit Data Dokter' : 'Tambah Data Dokter'; ?>
        </h2>
        
        <div class="table-container">
            <form action="dokter.php" method="POST">
                <input type="hidden" name="id_dokter" value="<?= $edit_data['id_dokter']; ?>">
                
                <div class="form-group">
                    <label>Nama Dokter</label>
                    <input type="text" name="nama_dokter" value="<?= $edit_data['nama_dokter']; ?>" placeholder="Contoh: dr. Herman Supriadi" required>
                </div>
                <div class="form-group">
                    <label>Spesialisasi</label>
                    <input type="text" name="spesialisasi" value="<?= $edit_data['spesialisasi']; ?>" placeholder="Contoh: Dokter Umum / Spesialis Anak" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Dokter
                </button>
                <?php if ($edit_data['id_dokter'] != '') : ?>
                    <a href="dokter.php" class="btn" style="background:#e0e0e0; color:#333; margin-left:8px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fa-solid fa-list-ul"></i> Daftar Dokter</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Dokter</th>
                        <th>Spesialisasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY id_dokter DESC");
                    if ($query && mysqli_num_rows($query) > 0) {
                        while ($d = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= $d['nama_dokter']; ?></strong></td>
                        <td><?= $d['spesialisasi']; ?></td>
                        <td>
                            <a href="dokter.php?edit=<?= $d['id_dokter']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="dokter.php?hapus=<?= $d['id_dokter']; ?>" class="btn btn-delete" onclick="return confirm('Hapus data dokter ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; color:#888;'>Belum ada data dokter.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>