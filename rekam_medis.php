<?php
// Tampilkan error jika terjadi masalah pada script/database
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Fitur Simpan Data Rekam Medis
if (isset($_POST['simpan'])) {
    $id_pasien       = $_POST['id_pasien'];
    $id_dokter       = $_POST['id_dokter'];
    $id_obat         = $_POST['id_obat'];
    $tanggal_periksa = $_POST['tanggal_periksa'];
    $keluhan         = $_POST['keluhan'];
    $diagnosa        = $_POST['diagnosa'];
    $tindakan        = $_POST['tindakan'];
    $resep           = $_POST['resep'];

    // Menyebutkan nama kolom secara eksplisit (tanpa mengisi manual id_rekam auto-increment)
    $query = "INSERT INTO rekam_medis (id_pasien, id_dokter, id_obat, tanggal_periksa, keluhan, diagnosa, tindakan, resep) 
              VALUES ('$id_pasien', '$id_dokter', '$id_obat', '$tanggal_periksa', '$keluhan', '$diagnosa', '$tindakan', '$resep')";
              
    if (mysqli_query($koneksi, $query)) {
        header("Location: rekam_medis.php");
        exit();
    } else {
        die("Gagal Menyimpan Data Rekam Medis: " . mysqli_error($koneksi));
    }
}

// Fitur Hapus Data Rekam Medis
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query_delete = "DELETE FROM rekam_medis WHERE id_rekam='$id'";
    if (mysqli_query($koneksi, $query_delete)) {
        header("Location: rekam_medis.php");
        exit();
    } else {
        die("Gagal Menghapus Data: " . mysqli_error($koneksi));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekam Medis</title>
    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Sidebar Navigasi -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-notes-medical"></i>
            <h2>Rekam Medis</h2>
        </div>
        <nav>
            <a href="index.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="pasien.php"><i class="fa-solid fa-user-injured"></i> Data Pasien</a>
            <a href="dokter.php"><i class="fa-solid fa-user-doctor"></i> Data Dokter</a>
            <a href="obat.php"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <a href="rekam_medis.php" class="active"><i class="fa-solid fa-file-medical"></i> Rekam Medis</a>
        </nav>
    </div>

    <!-- Konten Utama -->
    <div class="main-content">
        <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fa-solid fa-square-plus"></i> Catat Pemeriksaan Rekam Medis</h2>
        
        <div class="table-container">
            <form action="rekam_medis.php" method="POST">
                
                <div class="form-group">
                    <label>Pasien</label>
                    <select name="id_pasien" required>
                        <option value="">-- Pilih Pasien --</option>
                        <?php
                        $pasien = mysqli_query($koneksi, "SELECT * FROM pasien ORDER BY nama_pasien ASC");
                        while ($p = mysqli_fetch_assoc($pasien)) {
                            echo "<option value='".$p['id_pasien']."'>".$p['no_rm']." - ".$p['nama_pasien']."</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Dokter</label>
                    <select name="id_dokter" required>
                        <option value="">-- Pilih Dokter --</option>
                        <?php
                        $dokter = mysqli_query($koneksi, "SELECT * FROM dokter ORDER BY nama_dokter ASC");
                        while ($d = mysqli_fetch_assoc($dokter)) {
                            echo "<option value='".$d['id_dokter']."'>".$d['nama_dokter']." (".$d['spesialisasi'].")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Obat</label>
                    <select name="id_obat" required>
                        <option value="">-- Pilih Obat --</option>
                        <?php
                        $obat = mysqli_query($koneksi, "SELECT * FROM obat ORDER BY nama_obat ASC");
                        while ($o = mysqli_fetch_assoc($obat)) {
                            echo "<option value='".$o['id_obat']."'>".$o['nama_obat']." (Stok: ".$o['stok'].")</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Periksa</label>
                    <input type="date" name="tanggal_periksa" value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Keluhan</label>
                    <textarea name="keluhan" rows="2" placeholder="Masukkan keluhan utama pasien" required></textarea>
                </div>

                <div class="form-group">
                    <label>Diagnosa</label>
                    <textarea name="diagnosa" rows="2" placeholder="Hasil diagnosa medis" required></textarea>
                </div>

                <div class="form-group">
                    <label>Tindakan</label>
                    <textarea name="tindakan" rows="2" placeholder="Tindakan pemeriksaan yang diberikan" required></textarea>
                </div>

                <div class="form-group">
                    <label>Resep / Aturan Pakai</label>
                    <input type="text" name="resep" placeholder="Contoh: 3x1 sehari sesudah makan" required>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Rekam Medis
                </button>
            </form>
        </div>

        <h2 style="color: var(--primary-dark); margin-bottom: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pemeriksaan Pasien</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Periksa</th>
                        <th>No RM</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Keluhan</th>
                        <th>Diagnosa</th>
                        <th>Obat & Resep</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = "SELECT rm.*, p.no_rm, p.nama_pasien, d.nama_dokter, o.nama_obat 
                            FROM rekam_medis rm
                            JOIN pasien p ON rm.id_pasien = p.id_pasien
                            JOIN dokter d ON rm.id_dokter = d.id_dokter
                            JOIN obat o ON rm.id_obat = o.id_obat
                            ORDER BY rm.id_rekam DESC";
                    $query = mysqli_query($koneksi, $sql);

                    if (mysqli_num_rows($query) > 0) {
                        while ($data = mysqli_fetch_assoc($query)) {
                    ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= date('d-m-Y', strtotime($data['tanggal_periksa'])); ?></td>
                        <td><strong><?= $data['no_rm']; ?></strong></td>
                        <td><?= $data['nama_pasien']; ?></td>
                        <td><?= $data['nama_dokter']; ?></td>
                        <td><?= $data['keluhan']; ?></td>
                        <td><?= $data['diagnosa']; ?></td>
                        <td><?= $data['nama_obat']; ?><br><small style="color:#666;">(<?= $data['resep']; ?>)</small></td>
                        <td>
                            <a href="rekam_medis.php?hapus=<?= $data['id_rekam']; ?>" class="btn btn-delete" onclick="return confirm('Hapus data rekam medis ini?')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align:center; color:#888;'>Belum ada data rekam medis terdaftar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>