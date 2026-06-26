<?php
// Pastikan session sudah aman, karena di-include dari dashboard_user.php yang punya session
$user_id = $_SESSION['id'];

// 1. Kotak 1: Total Koleksi Buku di Toko (Global)
$query_buku = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
$total_buku = mysqli_fetch_assoc($query_buku)['total'];

// 7. Mengambil 3 Buku Terpopuler (Berdasarkan jumlah pesanan terbanyak)
$query_populer = mysqli_query($conn, "SELECT b.*, COUNT(o.book_id) as total_terjual 
                                      FROM books b 
                                      JOIN orders o ON b.id = o.book_id 
                                      GROUP BY b.id 
                                      ORDER BY total_terjual DESC 
                                      LIMIT 3");

// 2. Kotak 2: Total Pesanan SAYA (Spesifik User)
$query_pesanan = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE user_id = '$user_id'");
$total_pesanan = mysqli_fetch_assoc($query_pesanan)['total'];

// 3. Kotak 3: Saldo Digital User (Spesifik User dari tabel users)
$query_user = mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'");
$data_user = mysqli_fetch_assoc($query_user);
$saldo_user = $data_user['saldo'] ?? 0;

// 4. Kotak 4: Total Pengeluaran / Belanjaan Saya
$query_pengeluaran = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM orders WHERE user_id = '$user_id'");
$total_pengeluaran = mysqli_fetch_assoc($query_pengeluaran)['total'] ?? 0;

// 5. Mengambil Buku Terbaru yang Ditambahkan oleh Admin
$query_terbaru = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 1");
$data_terbaru = mysqli_fetch_assoc($query_terbaru);

// Logika pengecekan file gambar buku terbaru
$gambar_nama = $data_terbaru['gambar'] ?? '';
$gambar_path = "img/" . $gambar_nama;

if (empty($gambar_nama) || !file_exists($gambar_path)) {
    $gambar_display = "https://via.placeholder.com/150x200?text=No+Cover";
} else {
    $gambar_display = $gambar_path;
}

// 6. Data Aktivitas/Pesanan Terakhir (LIMIT 5 agar seimbang dengan layout kiri)
$query_activity = mysqli_query($conn, "SELECT orders.id, orders.metode_pembayaran, books.judul 
                                       FROM orders 
                                       JOIN books ON orders.book_id = books.id 
                                       WHERE orders.user_id = '$user_id' 
                                       ORDER BY orders.id DESC LIMIT 5");

// Array penampung data untuk pop-up kustom
$list_buku_modal = [];
if($data_terbaru) { 
    $list_buku_modal[$data_terbaru['id']] = $data_terbaru; 
}
?>

<style>
/* Custom utility jika Bootstrap kamu belum mendukung mt-10 secara default */
.mt-10 {
    margin-top: 2.5rem !important; /* setara dengan kustomisasi spacing atas */
}

/* ==================== POPUP UTAMA ==================== */
.katalog-popup-overlay {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(4px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99999 !important;
}
.katalog-popup-box {
    background-color: #111a2e;
    border: 2px solid #00a8e8;
    border-radius: 16px;
    width: 90%;
    max-width: 750px;
    padding: 30px;
    position: relative;
    box-shadow: 0 0 25px rgba(0, 168, 232, 0.25);
    display: flex;
    gap: 30px;
}
.katalog-popup-close {
    position: absolute;
    top: 15px; right: 20px;
    background: none; border: none;
    color: #fff; font-size: 1.6rem;
    cursor: pointer; opacity: 0.6;
    transition: opacity 0.2s;
}
.katalog-popup-close:hover { opacity: 1; }

.popup-left { flex: 0 0 210px; text-align: center; }
.popup-left img {
    width: 100%; max-height: 290px; object-fit: cover;
    border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.4);
}
.popup-right {
    flex: 1; display: flex; flex-direction: column;
    justify-content: space-between; color: #fff; text-align: left;
}
.popup-title {
    font-size: 1.8rem; font-weight: 800; color: #00a8e8;
    margin-bottom: 4px; line-height: 1.2;
}
.popup-author { color: #94a3b8; font-size: 0.95rem; margin-bottom: 12px; }

.popup-meta-wrapper { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 15px; }
.popup-tag {
    background: rgba(0, 168, 232, 0.1); color: #00a8e8;
    border: 1px solid rgba(0, 168, 232, 0.2);
    padding: 4px 10px; border-radius: 6px;
    font-size: 0.75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.popup-tag-success {
    background: rgba(0, 230, 118, 0.1); color: #00e676;
    border: 1px solid rgba(0, 230, 118, 0.2);
}
.popup-divider { border-top: 1px solid rgba(148, 163, 184, 0.15); margin: 15px 0; }

.popup-bottom-row { display: flex; align-items: center; justify-content: space-between; margin-top: auto; }
.popup-price { font-size: 1.5rem; font-weight: bold; color: #00e676; }
.popup-btn-action {
    background-color: #00a8e8; color: #fff !important; font-weight: bold;
    border: none; padding: 10px 24px; border-radius: 8px;
    text-decoration: none; transition: all 0.2s ease;
}
.popup-btn-action:hover { background-color: #007cc0; box-shadow: 0 0 10px rgba(0, 168, 232, 0.4); }

/* ==================== SEKSI AKTIVITAS BARU ==================== */
.timeline-item-premium {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border-radius: 8px;
    padding: 10px 10px 10px 15px;
}
.timeline-item-premium:hover {
    background-color: rgba(255, 255, 255, 0.03);
    transform: translateX(4px);
}

.pulse-indicator {
    position: absolute;
    left: -11px; top: 4px;
    width: 20px; height: 20px;
    background-color: #10b981;
    z-index: 2;
}
.pulse-indicator::after {
    content: '';
    position: absolute;
    width: 100%; height: 100%;
    top: 0; left: 0;
    background: inherit;
    border-radius: inherit;
    animation: timelinePulse 2s infinite ease-out;
    opacity: 0.6;
    z-index: -1;
}

@keyframes timelinePulse {
    0% { transform: scale(1); opacity: 0.6; }
    100% { transform: scale(1.8); opacity: 0; }
}

.civ-timeline-premium {
    border-left: 2px solid rgba(16, 185, 129, 0.25);
    position: relative;
}

@media (max-width: 576px) {
    .katalog-popup-box { flex-direction: column; padding: 20px; gap: 15px; max-height: 85vh; overflow-y: auto; }
    .popup-left { flex: 0 0 auto; width: 140px; margin: 0 auto; }
    .popup-bottom-row { flex-direction: column; gap: 15px; align-items: center; text-align: center; }
}
</style>

<div class="p-3">
    <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
        <div class="d-flex align-items-center gap-3">
            <!-- <button class="burger-btn" id="burgerToggleDashboard" style="background: #1e293b; color: #3b82f6; border: 1px solid #334155; padding: 10px 14px; border-radius: 8px; cursor: pointer; box-shadow: 0 0 10px rgba(59, 130, 246, 0.1); transition: background 0.2s;">
                <i class="fas fa-bars"></i>
            </button> -->
            <div>
                <h1 class="text-white fw-bold m-0 h3">Dashboard</h1>
                <p class="text-secondary small mt-1 mb-0">Selamat datang di <strong class="text-primary">UT Bookstore</strong></p>
            </div>
        </div>
        <div class="badge border border-primary px-3 py-2 rounded-pill text-white" style="background-color: #1e293b; white-space: nowrap;">
            <i class="fas fa-user text-primary me-1"></i> Client Mode
        </div>
    </div>
    
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-2 mt-18">
        <div class="col">
            <a href="dashboard_user.php?page=katalog" class="text-decoration-none">
            <div class="card hover-stat border-secondary p-3 h-100 d-flex flex-row align-items-center gap-3" style="background-color: #1e293b; border-radius: 12px;">
                <div class="p-3 rounded-3 text-primary" style="background-color: rgba(59, 130, 246, 0.15);">
                    <i class="fas fa-book fa-2x"></i>
                </div>
                <div>
                    <h3 class="text-white fw-bold m-0 h4"><?= $total_buku ?></h3>
                    <p class="text-secondary small m-0" style="font-size: 0.75rem;">Total Buku Tersedia</p>
                </div>
            </div>
            </a>
        </div>

        <div class="col">
            <a href="dashboard_user.php?page=pemesanan" class="text-decoration-none">
            <div class="card hover-stat border-secondary p-3 h-100 d-flex flex-row align-items-center gap-3" style="background-color: #1e293b; border-radius: 12px;">
                <div class="p-3 rounded-3 text-success" style="background-color: rgba(16, 185, 129, 0.15);">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <div>
                    <h3 class="text-white fw-bold m-0 h4"><?= $total_pesanan ?></h3>
                    <p class="text-secondary small m-0" style="font-size: 0.75rem;">Total Pesanan Saya</p>
                </div>
            </div>
            </a>
        </div>

        <div class="col">
            <a href="dashboard_user.php?page=topup" class="text-decoration-none">
            <div class="card hover-stat border-secondary p-3 h-100 d-flex flex-row align-items-center gap-3" style="background-color: #1e293b; border-radius: 12px;">
                <div class="p-3 rounded-3 text-warning" style="background-color: rgba(245, 158, 11, 0.15);">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <div>
                    <h3 class="text-white fw-bold m-0 h5">Rp <?= number_format($saldo_user, 0, ',', '.') ?></h3>
                    <p class="text-secondary small m-0" style="font-size: 0.75rem;">Saldo Digital</p>
                </div>
            </div>
            </a>
        </div>

        <div class="col">
            <a href="#" class="text-decoration-none">
            <div class="card hover-stat border-secondary p-3 h-100 d-flex flex-row align-items-center gap-3" style="background-color: #1e293b; border-radius: 12px;">
                <div class="p-3 rounded-3 text-info" style="background-color: rgba(139, 92, 246, 0.15);">
                    <i class="fas fa-hand-holding-usd fa-2x"></i>
                </div>
                <div>
                    <h3 class="text-white fw-bold m-0 h5">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                    <p class="text-secondary small m-0" style="font-size: 0.75rem;">Total Pengeluaran</p>
                </div>
            </div>
            </a>
        </div>
    </div>

    <div class="row g-3 mt-0">
        <div class="col-12 col-lg-8 d-flex flex-column gap-3">
            
            <div class="card border-secondary p-3" style="background-color: #1e293b; border-radius: 12px;">
                <h3 class="text-white h6 mb-3">
                    <i class="fas fa-star text-danger me-1"></i> Buku Baru Ditambahkan
                </h3>
                <?php if($data_terbaru): ?>
                    <div class="card border-primary p-3 d-flex flex-row align-items-center gap-3" style="background-color: #111a2e; border-radius: 12px;">
                        <img src="<?= $gambar_display ?>" alt="Cover" class="rounded shadow" style="width: 70px; height: 95px; object-fit: cover; cursor: pointer;" onclick="bukaKatalogPopup(<?= $data_terbaru['id'] ?>)">
                        <div>
                            <h4 class="text-white h6 m-0 fw-bold" style="cursor: pointer;" onclick="bukaKatalogPopup(<?= $data_terbaru['id'] ?>)">
                                <?= htmlspecialchars($data_terbaru['judul']) ?>
                            </h4>
                            <p class="text-secondary small my-1">Penulis: <?= htmlspecialchars($data_terbaru['penulis']) ?></p>
                            <p class="text-success fw-bold m-0 small">Rp <?= number_format($data_terbaru['harga'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-secondary small m-0">Belum ada buku baru.</p>
                <?php endif; ?>
            </div>

            <div class="card border-secondary p-3" style="background-color: #1e293b; border-radius: 12px;">
                <h3 class="text-white h6 mb-3">
                    <i class="fas fa-fire text-warning me-1"></i> Buku Terpopuler Web Ini
                </h3>
                <div class="d-flex flex-column gap-2">
                    <?php if(mysqli_num_rows($query_populer) > 0): ?>
                        <?php while($pop = mysqli_fetch_assoc($query_populer)): 
                            $list_buku_modal[$pop['id']] = $pop;

                            $pop_img_path = "img/" . $pop['gambar'];
                            if (empty($pop['gambar']) || !file_exists($pop_img_path)) {
                                $pop_img_display = "https://via.placeholder.com/150x200?text=No+Cover";
                            } else {
                                $pop_img_display = $pop_img_path;
                            }
                        ?>
                            <div class="card border-secondary p-3 d-flex flex-row align-items-center gap-3" style="background-color: #111a2e; border-radius: 12px;">
                                <img src="<?= $pop_img_display ?>" alt="Cover" class="rounded shadow" style="width: 70px; height: 95px; object-fit: cover; cursor: pointer;" onclick="bukaKatalogPopup(<?= $pop['id'] ?>)">
                                <div class="w-100 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="text-white h6 m-0 fw-bold" style="cursor: pointer;" onclick="bukaKatalogPopup(<?= $pop['id'] ?>)">
                                            <?= htmlspecialchars($pop['judul']) ?>
                                        </h4>
                                        <p class="text-secondary small my-1">Penulis: <?= htmlspecialchars($pop['penulis'] ?? 'Anonim') ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-shopping-cart me-1"></i> <?= $pop['total_terjual'] ?> Terjual
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-secondary small m-0">Belum ada data penjualan buku.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-secondary p-3 h-100" style="background-color: #1e293b; border-radius: 12px;">
                <h3 class="text-white h6 mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-history text-info me-1"></i> Aktivitas Saya</span>
                    <span class="badge bg-dark text-secondary font-monospace" style="font-size: 0.65rem;">Live Tracker</span>
                </h3>
                
                <div class="ps-2 pt-2">
                    <ul class="list-unstyled m-0 civ-timeline-premium">
                        <?php if(mysqli_num_rows($query_activity) > 0): 
                            $counter = 0;
                            while($act = mysqli_fetch_assoc($query_activity)): 
                                $counter++;
                                
                                if($counter == 1) { 
                                    $time_label = "Baru saja"; 
                                    $pulse_class = "pulse-indicator"; 
                                } elseif($counter == 2) { 
                                    $time_label = "2 jam yang lalu"; 
                                    $pulse_class = ""; 
                                } elseif($counter == 3) { 
                                    $time_label = "Kemarin"; 
                                    $pulse_class = ""; 
                                } else { 
                                    $time_label = "Beberapa hari lalu"; 
                                    $pulse_class = ""; 
                                }
                            ?>
                                <li class="position-relative timeline-item-premium mb-3">
                                    <div class="position-absolute d-flex align-items-center justify-content-center rounded-circle text-white shadow <?= $pulse_class ?>" 
                                         style="left: -11px; top: 4px; width: 20px; height: 20px; background-color: #10b981; font-size: 0.6rem;">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    
                                    <div class="m-0 row">
                                        <p class="text-white small m-0" style="font-size: 0.85rem; line-height: 1.4;">
                                            Membeli buku <strong>"<?= htmlspecialchars($act['judul']) ?>"</strong>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-white font-monospace text-opacity-75" style="font-size: 0.65rem;">
                                                ID: #ORD-<?= $act['id'] ?>
                                            </small>
                                            <span class="badge rounded px-2 py-0.5" style="font-size: 0.65rem; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                                <i class="far fa-clock me-1"></i><?= $time_label ?>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="position-relative timeline-item-premium">
                                <div class="position-absolute d-flex align-items-center justify-content-center rounded-circle text-white" 
                                     style="left: -11px; top: 4px; width: 20px; height: 20px; background-color: #3b82f6; font-size: 0.6rem;">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <p class="text-white small m-0" style="font-size: 0.85rem;">
                                    Selamat bergabung di UT Bookstore!
                                </p>
                                <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Akun Anda terverifikasi aktif</small>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php foreach($list_buku_modal as $id_buku => $buku_detail): 
    $img_modal_path = "img/" . $buku_detail['gambar'];
    if (empty($buku_detail['gambar']) || !file_exists($img_modal_path)) {
        $img_modal_display = "https://via.placeholder.com/150x200?text=No+Cover";
    } else {
        $img_modal_display = $img_modal_path;
    }
?>
<div class="katalog-popup-overlay" id="katalogPop<?= $id_buku ?>" onclick="tutupKatalogPopup(<?= $id_buku ?>)">
    <div class="katalog-popup-box" onclick="event.stopPropagation()">
        <button class="katalog-popup-close" onclick="tutupKatalogPopup(<?= $id_buku ?>)">&times;</button>
        
        <div class="popup-left">
            <img src="<?= $img_modal_display ?>" alt="Cover Buku">
        </div>
        
        <div class="popup-right">
            <div>
                <div class="popup-title"><?= htmlspecialchars($buku_detail['judul']) ?></div>
                <div class="popup-author">Penulis: <?= htmlspecialchars($buku_detail['penulis'] ?? 'Tidak Diketahui') ?></div>
                
                <div class="popup-meta-wrapper">
                    <span class="popup-tag popup-tag-success"><i class="fas fa-check-circle me-1"></i> Ready Stock</span>
                    <span class="popup-tag"><i class="fas fa-bookmark me-1"></i> UT Book Item</span>
                    <span class="popup-tag"><i class="fas fa-shield-alt me-1"></i> Verified</span>
                </div>
                <div class="popup-divider"></div>
            </div>
            
            <div class="popup-bottom-row">
                <div class="popup-price">Rp <?= number_format($buku_detail['harga'], 0, ',', '.') ?></div>
                <a href="dashboard_user.php?page=katalog&buka_id=<?= $id_buku ?>" class="popup-btn-action">Lihat Buku</a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function bukaKatalogPopup(id) {
    var pop = document.getElementById('katalogPop' + id);
    if(pop) { pop.style.display = 'flex'; }
}
function tutupKatalogPopup(id) {
    var pop = document.getElementById('katalogPop' + id);
    if(pop) { pop.style.display = 'none'; }
}

// SCRIPT KHUSUS UNTUK BURGER BUTTON DI DASHBOARD INI
const burgerToggleDashboard = document.getElementById('burgerToggleDashboard');
if(burgerToggleDashboard) {
    burgerToggleDashboard.addEventListener('click', (e) => {
        e.stopPropagation();
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth > 992) {
            document.body.classList.toggle('sidebar-hidden');
        } else {
            if(sidebar) sidebar.classList.toggle('active');
        }
    });
}
</script>