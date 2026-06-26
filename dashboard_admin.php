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
        body { background-color: #0f172a !important; color: #f8fafc !important; font-family: sans-serif; overflow-x: hidden; }

        /* SIDEBAR DEFAULT STYLES */
        .sidebar {
            padding: 25px 15px !important; 
            display: flex; flex-direction: column; height: 100vh;
            position: fixed; left: 0; top: 0; width: 250px;
            background-color: #1e293b; z-index: 1040;
            transition: left 0.3s ease;
        }

        .sidebar h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 0px; padding-left: 10px; }

        .nav-menu { list-style: none !important; padding-left: 0 !important; margin-top: 15px !important; }
        .nav-menu li { margin-bottom: 12px; width: 100%; }
        .nav-menu li a {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 15px; color: #94a3b8;
            text-decoration: none; border-radius: 8px;
            transition: all 0.3s; font-weight: 500;
        }
        .nav-menu li a:hover, .nav-menu li a.active { background-color: #3b82f6 !important; color: white !important; }

        /* MAIN CONTENT AREA */
        .main-content { margin-left: 270px; padding: 20px; transition: margin-left 0.3s ease; }

        /* LOGIK TOGGLE SIDEBAR (KHUSUS LAYAR PC/LAPTOP) */
        body.sidebar-hidden .sidebar { left: -250px; }
        body.sidebar-hidden .main-content { margin-left: 20px; }

        /* STAT CARDS */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 10px; margin-bottom: 25px; }
        .stat-card { background: #1e293b; padding: 20px; border-radius: 12px; border: 1px solid #334155; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-color: #3b82f6; }

        /* PEMISAH */
        .divider-line { border-bottom: 1px solid #334155; margin-bottom: 25px; margin-top: -10px; }

        /* TABLE */
        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        .custom-table tbody tr { background: #1e293b; transition: all 0.3s ease;}
        .custom-table td { padding: 15px; color: white; border: none; vertical-align: middle; }
        .cover-img { width: 60px; height: 80px; object-fit: fill; border-radius: 8px; border: 2px solid #3b82f6; }

        /* TABLE SORTING & IMAGE PREVIEW STYLES */
        .hover-sort { cursor: pointer; user-select: none; transition: color 0.2s ease; }
        .hover-sort:hover { color: #3b82f6 !important; }
        .hover-sort i { opacity: 0.5; transition: opacity 0.2s; }
        .hover-sort:hover i { opacity: 1; }

        .hover-zoom { cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-zoom:hover { transform: scale(1.1); box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4); }

        /* MODAL PREVIEW GAMBAR */
        .image-modal {
            display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.9); backdrop-filter: blur(5px);
            justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease;
        }
        .image-modal.show { display: flex; opacity: 1; }
        .image-modal-content {
            position: relative; max-width: 90%; max-height: 90%; text-align: center;
            animation: zoomIn 0.3s ease;
        }
        .image-modal-content img {
            max-width: 100%; max-height: 75vh; border-radius: 12px; border: 3px solid #3b82f6;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5); object-fit: contain;
        }
        .image-modal-title { color: white; margin-top: 15px; font-size: 1.2rem; font-weight: bold; }
        .image-modal-close {
            position: absolute; top: -15px; right: -15px; background: #ef4444; color: white;
            border: none; border-radius: 50%; width: 35px; height: 35px; font-size: 1.2rem;
            cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.2s;
        }
        .image-modal-close:hover { transform: scale(1.1); }
        @keyframes zoomIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        /* BURGER BUTTON */
        .burger-btn {
            display: block; background: #1e293b; color: #3b82f6; border: 1px solid #334155; padding: 10px 14px;
            border-radius: 8px; cursor: pointer; font-size: 1.1rem; box-shadow: 0 0 10px rgba(59, 130, 246, 0.1);
            transition: background 0.2s;
        }
        .burger-btn:hover { background: #243147; }
        .close-sidebar-btn { display: none; background: none; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer; }

        /* CUSTOM SEARCH INPUT */
        .admin-search-input {
            background-color: #1e293b !important; border: 1px solid #334155 !important;
            color: #f8fafc !important; border-radius: 8px; padding-left: 38px !important; transition: all 0.3s;
        }
        .admin-search-input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important; outline: none; }
        .admin-search-input::placeholder { color: #64748b !important; }

        /* RESPONSIVE LAYOUT */
        @media (max-width: 992px) {
            .sidebar { left: -250px; } .sidebar.active { left: 0; } 
            .main-content { margin-left: 0 !important; padding: 15px; } body.sidebar-hidden .main-content { margin-left: 0; }
            .close-sidebar-btn { display: block; } .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 576px) { .stat-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2><i class="fas fa-book-bookmark text-primary"></i> UT Bookstore</h2>
            <button id="closeSidebar" class="close-sidebar-btn"><i class="fas fa-times"></i></button>
        </div>
        <hr style="background:#94a3b8; width:100%; margin: 15px 0 10px 0; border: 0.5px solid #334155;">
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

    <div class="main-content">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <button class="burger-btn" id="burgerToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin: 0;">
                        <?= $page == 'pesanan_masuk' ? 'Kelola Transaksi' : 'Dashboard Admin' ?>
                    </h2>
                    <p style="color: #94a3b8; margin: 0; font-size: 0.9rem;">Selamat datang di <strong>UT Bookstore</strong></p>
                </div>
            </div>
            <div style="background: #1e293b; padding: 6px 16px; border-radius: 20px; border: 1px solid #334155; color: #3b82f6; font-weight: 600; font-size: 0.9rem; box-shadow: 0 0 10px rgba(59, 130, 246, 0.15); white-space: nowrap;">
                <i class="fas fa-user-shield me-1"></i> Admin Mode
            </div>
        </header>

        <div class="stat-grid">
            <a href="#" class="text-decoration-none">
                <div class="stat-card">
                    <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total Koleksi</h3>
                    <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #fff;"><?= $count_buku ?> Buku</p>
                </div>
            </a>
            <a href="#" class="text-decoration-none">
                <div class="stat-card">
                    <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total Pesanan</h3>
                    <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #fff;"><?= $count_order ?> Order</p>
                </div>
            </a>
            <a href="#" class="text-decoration-none">
                <div class="stat-card">
                    <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total User</h3>
                    <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #fff;"><?= $count_user ?> Orang</p>
                </div>
            </a>
            <a href="#" class="text-decoration-none">
                <div class="stat-card">
                    <h3 style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 5px;">Total Omzet</h3>
                    <p style="font-size: 1.4rem; font-weight: bold; margin: 0; color: #10b981;">Rp <?= number_format($sum_pendapatan ?? 0, 0, ',', '.') ?></p>
                </div>
            </a>
        </div>

        <div class="divider-line"></div>

        <div class="container-content">
            <?php if ($page == 'pesanan_masuk'): ?>
                <?php include 'kelola_pemesanan.php'; ?>
            <?php else: ?>
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
                    <h2 class="h5 text-white m-0 fw-bold"><i class="fas fa-boxes text-secondary me-2"></i>Daftar Inventaris Buku</h2>
                    
                    <div class="position-relative" style="width: 100%; max-width: 300px;">
                        <i class="fas fa-search position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" id="searchInputAdmin" class="form-control admin-search-input" placeholder="Cari judul atau penulis..." autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="custom-table" id="adminBookTable">
                        <thead>
                            <tr style="color: #94a3b8; font-size: 0.85rem; text-transform: uppercase;">
                                <th>Sampul</th>
                                <th onclick="sortTable(1)" class="hover-sort" title="Klik untuk mengurutkan">Informasi Buku <i class="fas fa-sort ms-1"></i></th>
                                <th onclick="sortTable(2)" class="hover-sort" title="Klik untuk mengurutkan">Harga <i class="fas fa-sort ms-1"></i></th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $buku->fetch_assoc()): ?>
                            <tr class="book-row">
                                <td>
                                    <img src="img/<?= $row['gambar'] ?>" class="cover-img hover-zoom" 
                                         title="Klik untuk lihat ukuran penuh" 
                                         onclick="openPreviewModal('img/<?= $row['gambar'] ?>', '<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>')">
                                </td>
                                <td>
                                    <div class="fw-bold text-white book-title-author"><?= $row['judul'] ?></div>
                                    <div class="small text-secondary mt-1 book-title-author"><?= $row['penulis'] ?></div>
                                </td>
                                <td class="text-success fw-bold">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <a href="edit_buku.php?id=<?= $row['id'] ?>" class="text-primary me-3 fs-5"><i class="fas fa-edit"></i></a>
                                    <a href="hapus_buku.php?id=<?= $row['id'] ?>" class="text-danger fs-5" onclick="return confirm('Hapus buku ini dari inventaris?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <tr id="noResultRow" style="display: none;">
                                <td colspan="4" class="text-center py-5" style="color: #94a3b8;">
                                    <i class="fas fa-search-minus fa-2x mb-2 d-block"></i>
                                    Data buku tidak ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="imagePreviewModal" class="image-modal" onclick="closePreviewModal(event)">
        <div class="image-modal-content" onclick="event.stopPropagation()">
            <button class="image-modal-close" onclick="closePreviewModal(event)"><i class="fas fa-times"></i></button>
            <img id="previewImageSrc" src="" alt="Preview Sampul">
            <div id="previewImageTitle" class="image-modal-title"></div>
        </div>
    </div>

    <script>
        // --- 1. LOGIKA PREVIEW SAMPUL ---
        const imageModal = document.getElementById('imagePreviewModal');
        const previewImageSrc = document.getElementById('previewImageSrc');
        const previewImageTitle = document.getElementById('previewImageTitle');

        function openPreviewModal(imgSrc, title) {
            previewImageSrc.src = imgSrc;
            previewImageTitle.textContent = title;
            
            // Menampilkan modal dengan sedikit delay agar animasi smooth jalan
            imageModal.style.display = 'flex';
            setTimeout(() => { imageModal.classList.add('show'); }, 10);
            
            // Mengunci scroll website di background
            document.body.style.overflow = 'hidden'; 
        }

        function closePreviewModal(e) {
            if (e) e.preventDefault();
            imageModal.classList.remove('show');
            
            // Tunggu animasi selesai lalu sembunyikan sepenuhnya
            setTimeout(() => {
                imageModal.style.display = 'none';
                previewImageSrc.src = '';
                document.body.style.overflow = 'auto'; // Kembalikan scroll
            }, 300);
        }

        // --- 2. LOGIKA SORTING (PENGURUTAN) TABEL ---
        function sortTable(columnIndex) {
            const table = document.getElementById("adminBookTable");
            const tbody = table.querySelector("tbody");
            let rows = Array.from(tbody.querySelectorAll(".book-row"));
            const header = table.querySelectorAll("th")[columnIndex];
            let direction = header.getAttribute("data-dir") || "asc";
            const isNumeric = (columnIndex === 2); 

            rows.sort((rowA, rowB) => {
                let valA = rowA.cells[columnIndex].innerText.toLowerCase();
                let valB = rowB.cells[columnIndex].innerText.toLowerCase();

                if (isNumeric) {
                    valA = parseInt(valA.replace(/[^0-9]/g, '')) || 0;
                    valB = parseInt(valB.replace(/[^0-9]/g, '')) || 0;
                }

                if (valA < valB) return direction === "asc" ? -1 : 1;
                if (valA > valB) return direction === "asc" ? 1 : -1;
                return 0;
            });

            rows.forEach(row => tbody.insertBefore(row, document.getElementById('noResultRow')));
            header.setAttribute("data-dir", direction === "asc" ? "desc" : "asc");
        }


        // --- 3. LOGIKA LIVE SEARCH TABEL INVENTARIS ---
        const searchInputAdmin = document.getElementById('searchInputAdmin');
        
        if (searchInputAdmin) {
            searchInputAdmin.addEventListener('input', function() {
                const keyword = this.value.toLowerCase(); 
                const rows = document.querySelectorAll('.book-row'); 
                let visibleCount = 0;

                rows.forEach(row => {
                    const infoBuku = row.cells[1].textContent.toLowerCase();
                    
                    if (infoBuku.includes(keyword)) {
                        row.style.display = ''; 
                        visibleCount++;
                    } else {
                        row.style.display = 'none'; 
                    }
                });

                const noResultRow = document.getElementById('noResultRow');
                if (visibleCount === 0 && rows.length > 0) {
                    noResultRow.style.display = '';
                } else {
                    noResultRow.style.display = 'none';
                }
            });
        }


        // --- 4. LOGIKA BURGER MENU SIDEBAR ---
        const burgerToggle = document.getElementById('burgerToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.querySelector('.sidebar');

        burgerToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            if (window.innerWidth > 992) {
                document.body.classList.toggle('sidebar-hidden');
            } else {
                sidebar.classList.toggle('active');
            }
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(e.target) && !burgerToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>