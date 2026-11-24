<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Menghitung total data
$q_pasien = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pasien");
$jml_pasien = ($q_pasien) ? mysqli_fetch_assoc($q_pasien)['total'] : 0;

$q_dokter = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM dokter");
$jml_dokter = ($q_dokter) ? mysqli_fetch_assoc($q_dokter)['total'] : 0;

$q_obat = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM obat");
$jml_obat = ($q_obat) ? mysqli_fetch_assoc($q_obat)['total'] : 0;

$q_rekam = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM rekam_medis");
$jml_rekam = ($q_rekam) ? mysqli_fetch_assoc($q_rekam)['total'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Rekam Medis</title>
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
            <a href="index.php" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="pasien.php"><i class="fa-solid fa-user-injured"></i> Data Pasien</a>
            <a href="dokter.php"><i class="fa-solid fa-user-doctor"></i> Data Dokter</a>
            <a href="obat.php"><i class="fa-solid fa-pills"></i> Data Obat</a>
            <a href="rekam_medis.php"><i class="fa-solid fa-file-medical"></i> Rekam Medis</a>
        </nav>
    </div>

    <!-- Konten Utama -->
    <div class="main-content">
        
        <!-- Banner Informasi -->
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Selamat Datang di Sistem Rekam Medis</h1>
                <p><i class="fa-solid fa-school"></i> SMK Negeri 3 Tuban — Konsentrasi Keahlian RPL</p>
            </div>
            <div class="welcome-icon">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
        </div>
        
        <!-- Kartu Statistik dengan Ikon -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-info">
                    <h3>Total Pasien</h3>
                    <p><?= $jml_pasien; ?></p>
                </div>
                <div class="card-icon icon-pasien">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Dokter</h3>
                    <p><?= $jml_dokter; ?></p>
                </div>
                <div class="card-icon icon-dokter">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Obat</h3>
                    <p><?= $jml_obat; ?></p>
                </div>
                <div class="card-icon icon-obat">
                    <i class="fa-solid fa-capsules"></i>
                </div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Rekam Medis</h3>
                    <p><?= $jml_rekam; ?></p>
                </div>
                <div class="card-icon icon-rekam">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        <!-- Tabel Aktivitas Rekam Medis Terbaru -->
        <h3 class="section-title"><i class="fa-solid fa-clock-rotate-left"></i> Rekam Medis Terbaru</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tgl Periksa</th>
                        <th>No. RM</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Diagnosa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = "SELECT rm.*, p.no_rm, p.nama_pasien, d.nama_dokter 
                            FROM rekam_medis rm
                            JOIN pasien p ON rm.id_pasien = p.id_pasien
                            JOIN dokter d ON rm.id_dokter = d.id_dokter
                            ORDER BY rm.id_rekam DESC LIMIT 5";
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
                        <td><?= $data['diagnosa']; ?></td>
                        <td><span class="badge"><i class="fa-solid fa-check"></i> Selesai</span></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; color:#888;'>Belum ada aktivitas rekam medis.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>