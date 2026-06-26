<?php
session_start();
include 'koneksi.php';

// Proteksi: Hanya Client yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit;
}

// ===================================================
// LOGIKA PEMROSESAN PEMBAYARAN VIA POP-UP MODAL
// ===================================================
if (isset($_POST['bayar_via_modal'])) {
    $user_id = $_SESSION['id'];
    $id_buku = intval($_POST['book_id']);
    $metode = $_POST['metode_pembayaran'];
    $tipe_pengiriman = $_POST['tipe_pengiriman'];
    $alamat_pengiriman = isset($_POST['alamat_pengiriman']) ? trim($_POST['alamat_pengiriman']) : null;
    
    // Tentukan biaya antar menjadi Rp 15.000 jika diantar
    $biaya_antar = ($tipe_pengiriman == 'Antar ke Rumah') ? 15000 : 0;
    
    // 1. Ambil data harga buku dan saldo user saat ini
    $stmt_buku = $conn->prepare("SELECT harga FROM books WHERE id = ?");
    $stmt_buku->bind_param("i", $id_buku);
    $stmt_buku->execute();
    $buku = $stmt_buku->get_result()->fetch_assoc();
    
    $stmt_user = $conn->prepare("SELECT saldo FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user = $stmt_user->get_result()->fetch_assoc();
    
    if ($buku && $user) {
        $harga_buku = $buku['harga'];
        // Total yang harus dibayar adalah harga buku ditambah biaya antar
        $total_bayar = $harga_buku + $biaya_antar;
        
        // 2. Cek apakah saldo cukup
        if ($user['saldo'] >= $total_bayar) {
            
            // 3. Gunakan transaksi untuk keamanan data
            $conn->begin_transaction();
            try {
                // A. Insert ke tabel orders dengan kolom baru
                $ins = $conn->prepare("INSERT INTO orders (user_id, book_id, total_harga, metode_pembayaran, tipe_pengiriman, alamat_pengiriman, biaya_antar) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $ins->bind_param("iiisssi", $user_id, $id_buku, $total_bayar, $metode, $tipe_pengiriman, $alamat_pengiriman, $biaya_antar);
                $ins->execute();
                
                // B. Kurangi saldo user berdasarkan total_bayar di tabel users
                $upd = $conn->prepare("UPDATE users SET saldo = saldo - ? WHERE id = ?");
                $upd->bind_param("ii", $total_bayar, $user_id);
                $upd->execute();
                
                $conn->commit();
                echo "<script>alert('Pembayaran Berhasil! Saldo telah dipotong.'); window.location='dashboard_user.php?page=katalog';</script>";
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>alert('Terjadi kesalahan sistem!');</script>";
            }
        } else {
            echo "<script>alert('Maaf, saldo Anda tidak mencukupi untuk total transaksi ini!');</script>";
        }
    } else {
        echo "<script>alert('Data tidak ditemukan!');</script>";
    }
}

// Menentukan halaman aktif
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Fitur Pencarian & Filter Genre
$keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';

// Bangun Query Dinamis Berdasarkan Pencarian dan Kategori (Server Side tetap aktif untuk validasi/refresh)
if (!empty($genre_filter) && !empty($keyword)) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE (judul LIKE ? OR penulis LIKE ?) AND kategori = ? ORDER BY id DESC");
    $search_param = "%$keyword%";
    $stmt->bind_param("sss", $search_param, $search_param, $genre_filter);
} elseif (!empty($genre_filter)) {
    $stmt = $conn->prepare("SELECT * FROM books WHERE kategori = ? ORDER BY id DESC");
    $stmt->bind_param("s", $genre_filter);
} else {
    $stmt = $conn->prepare("SELECT * FROM books WHERE judul LIKE ? OR penulis LIKE ? ORDER BY id DESC");
    $search_param = "%$keyword%";
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$buku = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UT Bookstore - Client Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=1.7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
    <style>
        body {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            overflow-x: hidden;
        }

        /* --- STYLING SIDEBAR & RESPONSIVE BURGER MENU --- */
        .sidebar {
            padding: 25px 15px !important; 
            display: flex; flex-direction: column; height: 100vh;
            position: fixed; left: 0; top: 0; width: 250px;
            background-color: #1e293b; z-index: 1040; transition: left 0.3s ease;
        }
        .sidebar h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0; padding-left: 10px; }

        .nav-menu { list-style: none !important; padding-left: 0 !important; margin-left: 0 !important; margin-top: 15px !important; }
        .nav-menu li { margin-bottom: 12px; width: 100%; }
        .nav-menu li a {
            display: flex; align-items: center; gap: 12px; padding: 12px 15px;
            color: #94a3b8; text-decoration: none; border-radius: 8px;
            transition: all 0.3s; font-weight: 500;
        }
        .nav-menu li a:hover, .nav-menu li a.active { background-color: #3b82f6 !important; color: white !important; }

        .main-content { margin-left: 270px; padding: 20px; transition: margin-left 0.3s ease; }

        body.sidebar-hidden .sidebar { left: -250px; }
        body.sidebar-hidden .main-content { margin-left: 20px; }

        .burger-btn {
            display: block; background: #1e293b; color: #3b82f6;
            border: 1px solid #334155; padding: 10px 14px;
            border-radius: 8px; cursor: pointer; font-size: 1.1rem;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.1); transition: background 0.2s;
        }
        .burger-btn:hover { background: #243147; }
        .close-sidebar-btn { display: none; background: none; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer; }

        @media (max-width: 992px) {
            .sidebar { left: -250px; }
            .sidebar.active { left: 0; }
            .main-content { margin-left: 0 !important; padding: 15px; }
            body.sidebar-hidden .main-content { margin-left: 0; }
            .close-sidebar-btn { display: block; }
            .header-search { width: 100%; margin-bottom: 15px; }
            .header-search form input { width: 100% !important; max-width: 100%; }
            header { flex-direction: column; align-items: flex-start !important; }
        }

        /* Dropdown Kategori */
        .dropdown-kategori-btn {
            background-color: #1e293b !important; color: #f8fafc !important;
            border: 1px solid #334155 !important; padding: 10px 16px;
            border-radius: 8px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s ease;
        }
        .dropdown-kategori-btn:hover, .dropdown-kategori-btn:focus { border-color: #3b82f6 !important; background-color: #243249 !important; color: white !important; }
        .dropdown-menu-dark-custom { background-color: #1e293b !important; border: 1px solid #334155 !important; border-radius: 8px; padding: 6px; }
        .dropdown-menu-dark-custom .dropdown-item { color: #cbd5e1 !important; padding: 10px 16px; border-radius: 6px; transition: all 0.2s; }
        .dropdown-menu-dark-custom .dropdown-item:hover, .dropdown-menu-dark-custom .dropdown-item.active { background-color: #3b82f6 !important; color: white !important; }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); justify-content: center; align-items: center; }
        .modal-content {
            background-color: #1e293b !important; color: #f8fafc !important; margin: 0 auto !important; 
            padding: 35px; border: 1px solid #3b82f6; width: 90% !important; max-width: 950px !important; 
            border-radius: 20px; display: flex !important; flex-direction: row !important; gap: 35px;
            position: relative; box-shadow: 0 0 30px rgba(59, 130, 246, 0.2); animation: slideUp 0.4s ease;
        }
        .modal-checkout-content { max-width: 520px !important; flex-direction: column !important; gap: 15px; padding: 2.5rem; max-height: 85vh; overflow-y: auto; padding-right: 2rem; scrollbar-width: thin; scrollbar-color: #3b82f6 transparent; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
        .close-modal { position: absolute; right: 20px; top: 15px; color: #94a3b8; font-size: 30px; cursor: pointer; transition: 0.3s; z-index: 10; }
        .close-modal:hover { color: #ef4444; }

        .modal-left { flex: 1 !important; text-align: center; display: flex !important; align-items: center; justify-content: center; }
        .modal-left img { width: 100%; max-width: 260px; border-radius: 12px; border: 3px solid #334155; box-shadow: 0 10px 20px rgba(0,0,0,0.5); object-fit: cover; }
        .modal-right { flex: 2 !important; color: white !important; display: flex !important; flex-direction: column !important; }
        .modal-right h2 { color: #3b82f6; margin-top: 10px; margin-bottom: 5px; font-size: 1.9rem; }
        .modal-right .author { color: #94a3b8; margin-bottom: 15px; font-style: italic; }
        .modal-right .desc { line-height: 1.6; color: #cbd5e1; max-height: 180px; overflow-y: auto; padding-right: 15px; margin-bottom: 20px; scrollbar-width: thin; scrollbar-color: #3b82f6 transparent; }

        .book-card-img-wrapper { width: 100%; aspect-ratio: 3 / 4; overflow: hidden; border-top-left-radius: 8px; border-top-right-radius: 8px; background-color: #1e293b; display: flex; align-items: center; justify-content: center; }
        .book-card-img-wrapper img { width: 100%; height: 100%; object-fit: fill; }
        .book-checkout-detail { display: flex; gap: 20px; background: #0f172a; padding: 15px; border-radius: 14px; border: 1px solid #334155; align-items: center; }
        .book-checkout-detail img { width: 85px; height: 120px; object-fit: cover; border-radius: 8px; }

        .form-select, .form-select option, .form-control { background-color: #0f172a !important; color: #f8fafc !important; border: 1px solid #334155 !important; border-radius: 8px; padding: 12px; }
        .form-control:focus { border-color: #3b82f6 !important; box-shadow: none; }
        .form-control::placeholder { color: #64748b !important; }

        .card, .stat-card { transition: transform 0.3s ease !important; }
        .card:hover, .stat-card:hover { transform: translateY(-5px) !important; }
        .book-card { display: flex; flex-direction: column; height: 100%; background-color: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow: hidden; transition: transform 0.3s; cursor: pointer; }
        .book-info { display: flex; flex-direction: column; flex-grow: 1; padding: 15px; }
        .book-info .price { margin-top: auto; margin-bottom: 10px; display: block; color: #3b82f6; font-weight: bold; font-size: 1.1rem; }

        .btn-beli {
            display: block; text-align: center; background: linear-gradient(135deg, #3b82f6, #2563eb) !important; 
            color: white !important; text-decoration: none; padding: 10px 15px; border-radius: 8px;
            font-size: 0.95rem; font-weight: 600; margin-top: 5px; transition: all 0.3s ease; cursor: pointer;
        }
        .btn-beli:hover { background: linear-gradient(135deg, #2563eb, #1d4ed8) !important; transform: translateY(-3px); box-shadow: 0 6px 15px rgba(59, 130, 246, 0.5); }
    </style>
</head>
<body>

<div class="sidebar">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h2><i class="fas fa-book-open text-primary"></i> UT BookStore</h2>
        <button id="closeSidebar" class="close-sidebar-btn"><i class="fas fa-times"></i></button>
    </div>
    <hr style="height:1px; border:none; background:#94a3b8; width:100%; margin: 15px 0 10px 0;">

    <ul class="nav-menu">
        <li>
            <a href="dashboard_user.php?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-heart"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="dashboard_user.php?page=katalog" class="<?= $page == 'katalog' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Katalog Buku
            </a>
        </li>
        <li>
            <a href="dashboard_user.php?page=pemesanan" class="<?= $page == 'pemesanan' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Pemesanan
            </a>
        </li>
        <li>
            <a href="dashboard_user.php?page=topup" class="<?= $page == 'topup' ? 'active' : '' ?>">
                <i class="fas fa-wallet"></i> Top Up Saldo
            </a>
        </li>
    </ul>

    <div style="margin-top: auto; padding: 5px 0;">
        <a href="logout.php" class="btn-logout" style="display: block; text-align: center; text-decoration: none; background: #ef4444; color: white; padding: 10px; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    
    <div class="d-flex align-items-center mb-4">
        <button class="burger-btn me-3" id="burgerToggle">
            <i class="fas fa-bars"></i>
        </button>
        <?php if ($page == 'dashboard'): ?>
            <h4 class="m-0 text-white fw-bold d-none d-md-block">UT BookStore</h4>
        <?php endif; ?>
        <?php if ($page == 'katalog'): ?>
            <h4 class="m-0 text-white fw-bold d-none d-md-block">Katalog Buku</h4>
        <?php endif; ?>
        <?php if ($page == 'pemesanan'): ?>
            <h4 class="m-0 text-white fw-bold d-none d-md-block">Pemesanan</h4>
        <?php endif; ?>
        <?php if ($page == 'topup'): ?>
            <h4 class="m-0 text-white fw-bold d-none d-md-block">Saldo Anda</h4>
        <?php endif; ?>
    </div>

    <?php if ($page == 'dashboard'): ?>
        <div class="container-fluid p-0">
            <?php include 'dashboard.php'; ?>
        </div>
    <?php elseif ($page == 'pemesanan'): ?>
        <div class="container-fluid p-0">
            <?php include 'konten_pemesanan.php'; ?>
        </div>
    <?php elseif ($page == 'topup'): ?>
        <div class="container-fluid p-0">
            <?php include 'konten_topup.php'; ?>
        </div>
    <?php else: ?>
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
            <div class="header-search">
                <form method="GET" style="display: flex; align-items: center; background: #1e293b; border-radius: 8px; padding: 5px 15px; border: 1px solid #334155;">
                    <input type="hidden" name="page" value="katalog">
                    <input type="hidden" name="genre" value="<?= htmlspecialchars($genre_filter) ?>">
                    <i class="fas fa-search" style="color: #94a3b8;"></i>
                    
                    <input type="text" name="cari" id="searchInput" placeholder="Cari judul atau penulis..."
                           value="<?= htmlspecialchars($keyword) ?>" autocomplete="off"
                           style="background: transparent; border: none; color: white; padding: 10px; outline: none; width: 300px;">
                    <button type="submit" style="display: none;">Cari</button>
                </form>
            </div>
            <div class="user-info" style="display: flex; align-items: center;">
                <span style="margin-right: 15px; color: #94a3b8;">Halo, <strong><?= $_SESSION['username'] ?></strong></span>
                <i class="fas fa-bell" style="margin-right: 15px; cursor: pointer; color: #94a3b8;"></i>
                <div class="cart-badge" style="position: relative; display: inline-block; color: #94a3b8;">
                    <i class="fas fa-shopping-bag fa-lg" style="cursor: pointer;"></i>
                    <span style="position: absolute; top: -8px; right: -8px; background: #ef4444; font-size: 0.7rem; padding: 2px 6px; border-radius: 50%; color: white;">0</span>
                </div>
            </div>
        </header>

        <section>
            <div class="row align-items-end mb-4 g-3">
                <div class="col-12 col-md-auto me-auto">
                    <div class="dropdown">
                        <button class="btn dropdown-kategori-btn dropdown-toggle d-flex align-items-center gap-2" type="button" id="dropdownGenre" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 190px; justify-content: space-between;">
                            <span>
                                <i class="fas fa-sliders text-primary me-2" style="font-size: 0.85rem;"></i>
                                <?= empty($genre_filter) ? 'Semua Kategori' : 'Kategori: ' . htmlspecialchars($genre_filter) ?>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark-custom" aria-labelledby="dropdownGenre" style="min-width: 190px;">
                            <li>
                                <a class="dropdown-item <?= empty($genre_filter) ? 'active' : '' ?>" href="dashboard_user.php?page=katalog&cari=<?= urlencode($keyword) ?>">
                                    <i class="fas fa-th-large me-2 small opacity-50"></i> Semua Kategori
                                </a>
                            </li>
                            <li><hr class="dropdown-divider" style="border-color: #334155;"></li>
                            <?php
                            $list_genre = ['Fiksi', 'Non-Fiksi', 'Novel', 'Komik', 'Teknologi'];
                            foreach ($list_genre as $g):
                            ?>
                                <li>
                                    <a class="dropdown-item <?= ($genre_filter === $g) ? 'active' : '' ?>" 
                                       href="dashboard_user.php?page=katalog&genre=<?= urlencode($g) ?>&cari=<?= urlencode($keyword) ?>">
                                        <?= $g ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="book-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px;">
                <?php if($buku->num_rows > 0): ?>
                    <?php while($row = $buku->fetch_assoc()): ?>
                    
                    <div class="book-card card-item-buku"
                         data-id="<?= $row['id'] ?>"
                         data-judul="<?= htmlspecialchars($row['judul']) ?>"
                         data-penulis="<?= htmlspecialchars($row['penulis']) ?>"
                         data-deskripsi="<?= htmlspecialchars($row['deskripsi']) ?>"
                         data-gambar="img/<?= $row['gambar'] ?>"
                         data-harga="Rp <?= number_format($row['harga'], 0, ',', '.') ?>"
                         data-hargasingle="<?= $row['harga'] ?>">
                        
                        <div class="book-card-img-wrapper" style="position: relative;">
                            <img src="img/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['judul']) ?>">
                            <span style="position: absolute; bottom: 8px; left: 8px; background: rgba(59, 130, 246, 0.85); padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; color: white; font-weight: 600;">
                                <?= htmlspecialchars($row['kategori']) ?>
                            </span>
                            <div style="position: absolute; top: 10px; right: 10px; background: rgba(15, 23, 42, 0.8); padding: 5px 10px; border-radius: 5px; font-size: 0.8rem; color: white;">
                                <i class="fas fa-star" style="color: #f59e0b;"></i> 4.5
                            </div>
                        </div>
                        
                        <div class="book-info">
                            <h4 style="color: white; margin-top: 0; margin-bottom: 5px; font-size: 1.05rem; font-weight: 600;" class="text-truncate"><?= htmlspecialchars($row['judul']) ?></h4>
                            <p class="author text-truncate" style="color: #94a3b8; font-size: 0.85rem; margin-top: 0; margin-bottom: 15px; font-style: italic;"><?= htmlspecialchars($row['penulis']) ?></p>
                            
                            <span class="price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                            
                            <button class="btn-beli btn-action-beli w-100 border-0 mt-2">
                                <i class="fas fa-cart-plus"></i> Beli Sekarang
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>

                    <div id="noResultsMsg" style="display: none; grid-column: 1 / -1; text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="fas fa-search-minus fa-3x" style="margin-bottom: 1rem;"></i>
                        <p>Buku dengan judul atau penulis tersebut tidak ditemukan.</p>
                    </div>

                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="fas fa-search fa-3x" style="margin-bottom: 1rem;"></i>
                        <p>Buku pada kategori/pencarian ini tidak ditemukan.</p>
                        <a href="dashboard_user.php?page=katalog" style="color: #3b82f6; text-decoration: none;">Lihat semua buku</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<div id="bookModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeMyModal('bookModal')">&times;</span>
        <div class="modal-left">
            <img id="modalImg" src="" alt="Cover">
        </div>
        <div class="modal-right">
            <h2 id="modalTitle"></h2>
            <p id="modalAuthor" class="author"></p>
            <div class="desc">
                <p id="modalDesc"></p>
            </div>
            <hr style="border: 0.5px solid #334155; margin: 20px 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
                <h3 id="modalPrice" style="color: #10b981; margin: 0;"></h3>
                <button id="btnCheckoutModal" class="btn-beli border-0" style="padding: 10px 25px; margin-top: 0;">Lanjut Checkout</button>
            </div>
        </div>
    </div>
</div>

<div id="checkoutModal" class="modal">
    <div class="modal-content modal-checkout-content">
        <span class="close-modal" onclick="closeMyModal('checkoutModal')">&times;</span>
        
        <h2 class="text-center fw-bold mb-3" style="color: #3b82f6; font-size: 1.7rem;"><i class="fas fa-shopping-cart me-2"></i>Konfirmasi Pesanan</h2>
        
        <div class="book-checkout-detail mb-3">
            <img id="chkImg" src="" alt="Cover">
            <div class="book-info">
                <h3 id="chkTitle" class="h6 fw-bold text-white m-0"></h3>
                <p id="chkAuthor" class="small text-secondary my-1"></p>
                <div id="chkPrice" class="text-success fw-bold fs-5 mt-2"></div>
            </div>
        </div>

        <form method="POST">
            <input type="hidden" id="chkBookId" name="book_id" value="">
            
            <div class="mb-3">
                <label class="form-label small text-secondary fw-medium mb-1">
                    <i class="fas fa-truck me-1"></i> Metode Pengiriman:
                </label>
                <select name="tipe_pengiriman" id="tipePengiriman" class="form-select" onchange="hitungOtomatisBiaya()" required>
                    <option value="Ambil di Toko">Ambil di Toko (Gratis Ongkir)</option>
                    <option value="Antar ke Rumah">Antar ke Rumah (+Rp 15.000)</option>
                </select>
            </div>

            <div class="mb-3" id="boxAlamat" style="display: none;">
                <label class="form-label small text-secondary fw-medium mb-1">
                    <i class="fas fa-map-marker-alt me-1"></i> Alamat Lengkap Pengiriman:
                </label>
                <textarea name="alamat_pengiriman" id="alamatPengiriman" class="form-control" rows="2" placeholder="Masukkan nama jalan, nomor rumah, RT/RW, dan kecamatan..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label small text-secondary fw-medium mb-1">
                    <i class="fas fa-wallet me-1"></i> Pilih Metode Pembayaran:
                </label>
                <select name="metode_pembayaran" class="form-select" required>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet (Gopay/OVO/Dana)</option>
                    <option value="COD">Bayar di Tempat (COD)</option>
                </select>
            </div>

            <div class="p-3 mb-3 rounded" style="background-color: #0f172a; border: 1px dashed #334155;">
                <div class="d-flex justify-content-between small text-secondary mb-1">
                    <span>Harga Buku:</span>
                    <span id="summaryHargaBuku">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between small text-secondary mb-1">
                    <span>Biaya Antar:</span>
                    <span id="summaryBiayaAntar">Rp 0</span>
                </div>
                <hr style="border-top: 1px solid #334155; margin: 8px 0;">
                <div class="d-flex justify-content-between fw-bold text-white">
                    <span>Total Pembayaran:</span>
                    <span id="summaryTotalBayar" class="text-info">Rp 0</span>
                </div>
            </div>
            
            <button type="submit" name="bayar_via_modal" class="btn-beli w-100 py-3 mb-2 border-0" style="font-size: 1rem;">
                Konfirmasi & Bayar Sekarang
            </button>
            
            <button type="button" onclick="closeMyModal('checkoutModal')" class="btn bg-transparent w-100 text-secondary border-0 small py-1" style="font-size: 0.9rem;">
                <i class="fas fa-arrow-left me-1"></i> Batal & Kembali
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// --- LOGIKA REAL-TIME SEARCH PADA HALAMAN KATALOG ---
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        const bookCards = document.querySelectorAll('.card-item-buku');
        let foundCount = 0;
        
        bookCards.forEach(card => {
            // Mengambil judul dan penulis dari atribut data
            const judul = card.getAttribute('data-judul').toLowerCase();
            const penulis = card.getAttribute('data-penulis').toLowerCase();
            
            // Cocokkan keyword dengan judul atau penulis
            if (judul.includes(keyword) || penulis.includes(keyword)) {
                card.style.display = 'flex'; // Menampilkan card
                foundCount++;
            } else {
                card.style.display = 'none'; // Menyembunyikan card
            }
        });

        // Logika menampilkan atau menyembunyikan pesan "Tidak Ditemukan"
        const noResultsMsg = document.getElementById('noResultsMsg');
        if (noResultsMsg) {
            if (foundCount === 0 && bookCards.length > 0) {
                noResultsMsg.style.display = 'block';
            } else {
                noResultsMsg.style.display = 'none';
            }
        }
    });
}

// --- LOGIKA BURGER MENU & RESPONSIVE SIDEBAR ---
const burgerToggle = document.getElementById('burgerToggle');
const closeSidebar = document.getElementById('closeSidebar');
const sidebar = document.querySelector('.sidebar');

if(burgerToggle) {
    burgerToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        if (window.innerWidth > 992) {
            document.body.classList.toggle('sidebar-hidden');
        } else {
            sidebar.classList.toggle('active');
        }
    });
}

if(closeSidebar) {
    closeSidebar.addEventListener('click', () => {
        sidebar.classList.remove('active');
    });
}

document.addEventListener('click', (e) => {
    if (window.innerWidth <= 992) {
        if (!sidebar.contains(e.target) && burgerToggle && !burgerToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});


// --- LOGIKA MODAL BUKU & CHECKOUT ---
let activeBook = {};
let currentHargaRaw = 0; 

function lockScroll() { document.body.style.overflow = 'hidden'; }

function unlockScroll() {
    let bModal = document.getElementById('bookModal');
    let cModal = document.getElementById('checkoutModal');
    
    if (bModal.style.display !== 'flex' && cModal.style.display !== 'flex') {
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('click', function(e) {
    const card = e.target.closest('.card-item-buku');
    if (card) {
        const id = card.getAttribute('data-id');
        const judul = card.getAttribute('data-judul');
        const penulis = card.getAttribute('data-penulis');
        const deskripsi = card.getAttribute('data-deskripsi');
        const gambar = card.getAttribute('data-gambar');
        const harga = card.getAttribute('data-harga');
        const hargaRaw = parseInt(card.getAttribute('data-hargasingle'));

        if (e.target.closest('.btn-action-beli')) {
            e.stopPropagation();
            openCheckoutModal(id, judul, penulis, gambar, harga, hargaRaw);
        } else {
            openBookDetail(id, judul, penulis, deskripsi, gambar, harga, hargaRaw);
        }
    }
});

function openBookDetail(idBuku, judul, penulis, deskripsi, gambar, harga, hargaRaw) {
    activeBook = { id: idBuku, title: judul, author: penulis, img: gambar, price: harga, priceRaw: hargaRaw };
    
    document.getElementById('modalTitle').innerText = judul;
    document.getElementById('modalAuthor').innerText = 'Penulis: ' + penulis;
    document.getElementById('modalDesc').innerText = deskripsi;
    document.getElementById('modalImg').src = gambar;
    document.getElementById('modalPrice').innerText = harga;
    
    document.getElementById('btnCheckoutModal').onclick = function() {
        document.getElementById('bookModal').style.display = 'none';
        openCheckoutModal(activeBook.id, activeBook.title, activeBook.author, activeBook.img, activeBook.price, activeBook.priceRaw);
    };
    
    document.getElementById('bookModal').style.display = 'flex'; 
    lockScroll();
}

function openCheckoutModal(idBuku, judul, penulis, gambar, harga, hargaRaw) {
    currentHargaRaw = hargaRaw;

    document.getElementById('chkBookId').value = idBuku;
    document.getElementById('chkTitle').innerText = judul;
    document.getElementById('chkAuthor').innerText = 'Penulis: ' + penulis;
    document.getElementById('chkImg').src = gambar;
    document.getElementById('chkPrice').innerText = harga;
    
    document.getElementById('tipePengiriman').value = 'Ambil di Toko';
    document.getElementById('boxAlamat').style.display = 'none';
    document.getElementById('alamatPengiriman').required = false;
    document.getElementById('alamatPengiriman').value = '';

    hitungOtomatisBiaya();

    document.getElementById('checkoutModal').style.display = 'flex'; 
    lockScroll();
}

function hitungOtomatisBiaya() {
    let tipe = document.getElementById('tipePengiriman').value;
    let boxAlamat = document.getElementById('boxAlamat');
    let inputAlamat = document.getElementById('alamatPengiriman');
    
    let ongkir = 0;
    if (tipe === 'Antar ke Rumah') {
        boxAlamat.style.display = 'block';
        inputAlamat.required = true;
        ongkir = 15000; 
    } else {
        boxAlamat.style.display = 'none';
        inputAlamat.required = false;
        ongkir = 0;
    }

    let totalSemua = currentHargaRaw + ongkir;

    document.getElementById('summaryHargaBuku').innerText = 'Rp ' + currentHargaRaw.toLocaleString('id-ID');
    document.getElementById('summaryBiayaAntar').innerText = 'Rp ' + ongkir.toLocaleString('id-ID');
    document.getElementById('summaryTotalBayar').innerText = 'Rp ' + totalSemua.toLocaleString('id-ID');
}

function closeMyModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    unlockScroll();
}

window.onclick = function(event) {
    let bModal = document.getElementById('bookModal');
    let cModal = document.getElementById('checkoutModal');
    if (event.target == bModal) { closeMyModal('bookModal'); }
    if (event.target == cModal) { closeMyModal('checkoutModal'); }
}

window.addEventListener('DOMContentLoaded', (event) => {
    const urlParams = new URLSearchParams(window.location.search);
    const bukaId = urlParams.get('buka_id');
    if (bukaId) {
        const bukuCard = document.querySelector(`.card-item-buku[data-id="${bukaId}"]`);
        if (bukuCard) { 
            bukuCard.click(); 
        }
    }
});
</script>

</body>
</html>