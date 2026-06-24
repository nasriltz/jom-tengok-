<?php
session_start();
include 'koneksi.php';

// Proteksi: Hanya admin yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Menentukan halaman aktif
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Ambil data untuk Tabel
$buku = $conn->query("SELECT * FROM books ORDER BY id DESC");

// Hitung statistik
$count_buku = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];
$count_order = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$sum_pendapatan = $conn->query("SELECT SUM(total_harga) as total FROM orders")->fetch_assoc()['total'];
$count_user = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UT Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #0f172a !important; color: #f8fafc !important; font-family: sans-serif; }

        .sidebar {
            padding: 25px 15px !important; 
            display: flex; flex-direction: column; height: 100vh;
            position: fixed; left: 0; top: 0; width: 250px;
            background-color: #1e293b; z-index: 100;
        }

        .sidebar h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 5px; padding-left: 10px; }

        .nav-menu { list-style: none !important; padding-left: 0 !important; margin-top: 15px !important; }
        .nav-menu li { margin-bottom: 12px; width: 100%; }
        .nav-menu li a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 15px; color: #94a3b8;
            text-decoration: none; border-radius: 8px;
            transition: all 0.3s; font-weight: 500;
        }
        .nav-menu li a:hover, .nav-menu li a.active { background-color: #3b82f6 !important; color: white !important; }

        /* Stat Cards: Grid 4 Kolom */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 10px; margin-bottom: 25px; }
        .stat-card { background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-color: #3b82f6; }

        /* Garis Pemisah */
        .divider-line { border-bottom: 1px solid #334155; margin-bottom: 25px; margin-top: -10px; }

        /* Table */
        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .custom-table tbody tr { background: #1e293b; }
        .custom-table td { padding: 15px; color: white; border: none; }
        .cover-img { width: 60px; height: 80px; object-fit: fill; border-radius: 8px; border: 2px solid #3b82f6; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fas fa-book-bookmark text-primary"></i> UT Bookstore</h2>
        <hr style="background:#94a3b8; width:100%; margin: 10px 0; border: 0.5px solid #334155;">
        <ul class="nav-menu">
            <li><a href="dashboard_admin.php" class="<?= $page == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="tambah_buku.php"><i class="fas fa-plus-circle"></i> Tambah Buku</a></li>
            <li><a href="dashboard_admin.php?page=pesanan_masuk" class="<?= $page == 'pesanan_masuk' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Pesanan Masuk</a></li>
        </ul>
        <div style="margin-top: auto;">
            <a href="logout.php" style="display: block; text-align: center; background: #ef4444; color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content" style="margin-left: 270px; padding: 20px;">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin: 0;">
                    <?= $page == 'pesanan_masuk' ? 'Kelola Transaksi' : 'Dashboard Admin' ?>
                </h2>
                <p style="color: #94a3b8; margin: 0; font-size: 0.9rem;">Selamat datang di <strong>UT Bookstore</strong></p>
            </div>
            <div style="background: #1e293b; padding: 6px 16px; border-radius: 20px; border: 1px solid #334155; color: #3b82f6; font-weight: 600; font-size: 0.9rem; box-shadow: 0 0 10px rgba(59, 130, 246, 0.15);">
                <i class="fas fa-user-shield me-1"></i> Admin Mode
            </div>
        </header>

        <div class="stat-grid">
            <div class="stat-card">
                <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total Koleksi</h3>
                <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #fff;"><?= $count_buku ?> Buku</p>
            </div>
            <div class="stat-card">
                <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total Pesanan</h3>
                <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #fff;"><?= $count_order ?> Order</p>
            </div>
            <div class="stat-card">
                <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total Omzet</h3>
                <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #10b981;">Rp <?= number_format($sum_pendapatan ?? 0, 0, ',', '.') ?></p>
            </div>
            <div class="stat-card">
                <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total User</h3>
                <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #fff;"><?= $count_user ?> Orang</p>
            </div>
        </div>

        <div class="divider-line"></div>

        <div class="container-content">
            <?php if ($page == 'pesanan_masuk'): ?>
                <?php include 'kelola_pemesanan.php'; ?>
            <?php else: ?>
                <h2 class="h5 text-white mb-3 fw-bold"><i class="fas fa-boxes text-secondary me-2"></i>Daftar Inventaris Buku</h2>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr style="color: #94a3b8; font-size: 0.85rem; text-transform: uppercase;">
                                <th>Sampul</th><th>Informasi Buku</th><th>Harga</th><th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $buku->fetch_assoc()): ?>
                            <tr>
                                <td><img src="img/<?= $row['gambar'] ?>" class="cover-img"></td>
                                <td><div class="fw-bold text-white"><?= $row['judul'] ?></div><div class="small text-secondary mt-1"><?= $row['penulis'] ?></div></td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="edit_buku.php?id=<?= $row['id'] ?>" class="text-primary me-3 fs-5"><i class="fas fa-edit"></i></a>
                                    <a href="hapus_buku.php?id=<?= $row['id'] ?>" class="text-danger fs-5" onclick="return confirm('Hapus buku ini dari inventaris?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>