<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Simpan / Update Pasien
if (isset($_POST['simpan'])) {
    $id_pasien     = $_POST['id_pasien'];
    $no_rm         = $_POST['no_rm'];
    $nama_pasien   = $_POST['nama_pasien'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat        = $_POST['alamat'];

    if ($id_pasien == "") {
        $query = "INSERT INTO pasien (no_rm, nama_pasien, jenis_kelamin, tanggal_lahir, alamat) 
                  VALUES ('$no_rm', '$nama_pasien', '$jenis_kelamin', '$tanggal_lahir', '$alamat')";
    } else {
        $query = "UPDATE pasien SET no_rm='$no_rm', nama_pasien='$nama_pasien', jenis_kelamin='$jenis_kelamin', tanggal_lahir='$tanggal_lahir', alamat='$alamat' WHERE id_pasien='$id_pasien'";
    }
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: pasien.php");
        exit();
    } else {
        die("Gagal menyimpan data pasien: " . mysqli_error($koneksi));
    }
}

// Hapus Pasien
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (mysqli_query($koneksi, "DELETE FROM pasien WHERE id_pasien='$id'")) {
        header("Location: pasien.php");
        exit();
    } else {
        die("Gagal menghapus data pasien: " . mysqli_error($koneksi));
    }
}

// Ambil Data untuk Edit
$edit_data = ['id_pasien'=>'', 'no_rm'=>'', 'nama_pasien'=>'', 'jenis_kelamin'=>'', 'tanggal_lahir'=>'', 'alamat'=>''];
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM pasien WHERE id_pasien='$id'");
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
    <title>Data Pasien</title>
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
            <a href="pasien.php" class="active"><i class="fa-solid fa-user-injured"></i> Data Pasien</a>
            <a href="dokter.php"><i class="fa-solid fa-user-doctor"></i> Data Dokter</a>
            <a href="obat.php"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <a href="rekam_medis.php"><i class="fa-solid fa-file-medical"></i> Rekam Medis</a>
        </nav>
    </div>

    <div class="main-content">
        <h2 style="color: var(--primary-dark); margin-bottom: 15px;">
            <i class="fa-solid <?= ($edit_data['id_pasien'] != '') ? 'fa-pen-to-square' : 'fa-user-plus'; ?>"></i>
            <?= ($edit_data['id_pasien'] != '') ? 'Edit Data Pasien' : 'Tambah Data Pasien'; ?>
        </h2>
        
        <div class="table-container">
            <form action="pasien.php" method="POST">
                <input type="hidden" name="id_pasien" value="<?= $edit_data['id_pasien']; ?>">
                
                <div class="form-group">
                    <label>No. Rekam Medis (No RM)</label>
                    <input type="text" name="no_rm" value="<?= $edit_data['no_rm']; ?>" placeholder="Contoh: RM-004" required>
                </div>
                <div class="form-group">
                    <label>Nama Pasien</label>
                    <input type="text" name="nama_pasien" value="<?= $edit_data['nama_pasien']; ?>" placeholder="Masukkan nama lengkap pasien" required>
                </div>
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= ($edit_data['jenis_kelamin'] == 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="P" <?= ($edit_data['jenis_kelamin'] == 'P') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= $edit_data['tanggal_lahir']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat" rows="2" placeholder="Masukkan alamat pasien" required><?= $edit_data['alamat']; ?></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pasien
                </button>
                <?php if ($edit_data['id_pasien'] != '') : ?>
                    <a href="pasien.php" class="btn" style="background:#e0e0e0; color:#333; margin-left:8px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fa-solid fa-list-ul"></i> Daftar Pasien</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>JK</th>
                        <th>Tgl Lahir</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $query = mysqli_query($koneksi, "SELECT * FROM pasien ORDER BY id_pasien DESC");
                    if ($query && mysqli_num_rows($query) > 0) {
                        while ($p = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= $p['no_rm']; ?></strong></td>
                        <td><?= $p['nama_pasien']; ?></td>
                        <td><?= $p['jenis_kelamin']; ?></td>
                        <td><?= date('d-m-Y', strtotime($p['tanggal_lahir'])); ?></td>
                        <td><?= $p['alamat']; ?></td>
                        <td>
                            <a href="pasien.php?edit=<?= $p['id_pasien']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="pasien.php?hapus=<?= $p['id_pasien']; ?>" class="btn btn-delete" onclick="return confirm('Hapus data pasien ini?')"><i class="fa-solid fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; color:#888;'>Belum ada data pasien.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>