<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

// Proteksi: Hanya Admin yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// LOGIKA PROSES KONFIRMASI DARI ADMIN
if (isset($_POST['konfirmasi_selesai'])) {
    $order_id = intval($_POST['order_id']);
    
    // Update status pesanan menjadi Selesai
    $stmt_update = $conn->prepare("UPDATE orders SET status = 'Selesai' WHERE id = ?");
    $stmt_update->bind_param("i", $order_id);
    
    if ($stmt_update->execute()) {
        echo "<script>alert('Pesanan #$order_id berhasil diselesaikan!'); window.location='dashboard_admin.php?page=pesanan_masuk';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui status!');</script>";
    }
}

// Ambil semua data orderan dari database gabung dengan info buku dan nama pembeli
$query_all_orders = "SELECT o.*, b.judul, b.gambar, u.username 
                     FROM orders o 
                     JOIN books b ON o.book_id = b.id 
                     JOIN users u ON o.user_id = u.id 
                     ORDER BY o.id DESC";
$result_all_orders = $conn->query($query_all_orders);
?>

<style>
    .pro-management-card {
        background: #111827;
        border: 1px solid rgba(51, 65, 85, 0.5);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        position: relative;
        overflow: hidden; 
    }

    .table-manage {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        color: #f8fafc !important;
        table-layout: fixed; 
    }

    .table-manage thead th {
        color: #94a3b8 !important;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        padding: 16px 12px;
        border-bottom: 1px solid rgba(51, 65, 85, 0.5);
        background: transparent !important;
    }

    .table-manage tbody tr { transition: all 0.3s ease; }
    
    .table-manage td {
        padding: 18px 12px;
        color: #f8fafc !important;
        border-bottom: 1px solid rgba(51, 65, 85, 0.3);
        vertical-align: middle;
        background: transparent !important;
    }

    .col-invoice { width: 18%; }
    .col-pelanggan { width: 16%; }
    .col-buku { width: 22%; }
    .col-logistik { width: 16%; }
    .col-bayar { width: 12%; }
    .col-status { width: 10%; }
    .col-aksi { width: 10%; }

    .txt-invoice { font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; font-weight: 700; color: #cbd5e1 !important; }
    .txt-username { font-weight: 600; color: #ffffff !important; font-size: 0.95rem; }

    .user-click-trigger { cursor: pointer; color: #ffffff; border-bottom: 1px dashed #64748b; transition: all 0.2s; }
    .user-click-trigger:hover { color: #3b82f6 !important; border-bottom-color: #3b82f6; }

    .custom-dark-popup {
        display: none; position: absolute; background: #1e293b; border: 1px solid #3b82f6; color: #f8fafc;
        padding: 10px 16px; border-radius: 8px; font-size: 0.85rem; z-index: 9999; box-shadow: 0 10px 25px rgba(0,0,0,0.5); font-weight: 500;
    }

    .txt-book-title { font-weight: 600; color: #f1f5f9 !important; font-size: 0.9rem; }

    .badge-pro { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; }
    .badge-pro-lunas { background: rgba(59, 130, 246, 0.12) !important; color: #3b82f6 !important; border: 1px solid rgba(59, 130, 246, 0.25); }
    .badge-pro-selesai { background: rgba(16, 185, 129, 0.12) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.25); }

    .btn-action-done { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white !important; border: none; padding: 8px 12px; border-radius: 10px; font-size: 0.8rem; font-weight: 600; transition: all 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); white-space: nowrap; }
    .btn-action-disabled { background: rgba(255, 255, 255, 0.05); color: #475569 !important; border: 1px solid rgba(255, 255, 255, 0.05); padding: 8px 12px; border-radius: 10px; font-size: 0.8rem; white-space: nowrap; }

    /* --- GAYA TAMBAHAN UNTUK SEARCH, SORT & MODAL GAMBAR --- */
    .admin-search-input { background-color: #1e293b !important; border: 1px solid #334155 !important; color: #f8fafc !important; border-radius: 8px; padding-left: 38px !important; transition: all 0.3s; }
    .admin-search-input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important; outline: none; }
    .admin-search-input::placeholder { color: #64748b !important; }

    .hover-sort { cursor: pointer; user-select: none; transition: color 0.2s ease; }
    .hover-sort:hover { color: #3b82f6 !important; }
    .hover-sort i { opacity: 0.5; transition: opacity 0.2s; }
    .hover-sort:hover i { opacity: 1; }

    .hover-zoom { cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .hover-zoom:hover { transform: scale(1.1); box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4); z-index: 10; position: relative; }

    /* MODAL PREVIEW GAMBAR */
    .order-image-modal { display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.9); backdrop-filter: blur(5px); justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s ease; }
    .order-image-modal.show { display: flex; opacity: 1; }
    .order-image-modal-content { position: relative; max-width: 90%; max-height: 90%; text-align: center; animation: zoomIn 0.3s ease; }
    .order-image-modal-content img { max-width: 100%; max-height: 75vh; border-radius: 12px; border: 3px solid #3b82f6; box-shadow: 0 10px 25px rgba(0,0,0,0.5); object-fit: contain; }
    .order-image-modal-title { color: white; margin-top: 15px; font-size: 1.2rem; font-weight: bold; }
    .order-image-modal-close { position: absolute; top: -15px; right: -15px; background: #ef4444; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; font-size: 1.2rem; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.2s; }
    .order-image-modal-close:hover { transform: scale(1.1); }
</style>

<div id="emailPopup" class="custom-dark-popup"></div>

<div class="pro-management-card">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="h5 text-white m-0 fw-bold"><i class="fas fa-stream text-primary me-2"></i>Antrean Logistik Masuk</h2>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="position-relative" style="width: 280px;">
                <i class="fas fa-search position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                <input type="text" id="searchOrderInput" class="form-control admin-search-input" placeholder="Cari Pelanggan, Buku, atau Invoice..." autocomplete="off">
            </div>
            <span class="badge bg-secondary px-3 py-2" style="font-size:0.75rem; border-radius:8px;">
                Total: <?= $result_all_orders->num_rows ?> Transaksi
            </span>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-manage m-0" id="orderManageTable">
            <thead>
                <tr>
                    <th class="col-invoice hover-sort" onclick="sortOrderTable(0)" title="Klik untuk mengurutkan">Invoice ID <i class="fas fa-sort ms-1"></i></th>
                    <th class="col-pelanggan hover-sort" onclick="sortOrderTable(1)" title="Klik untuk mengurutkan">Pelanggan <i class="fas fa-sort ms-1"></i></th>
                    <th class="col-buku hover-sort" onclick="sortOrderTable(2)" title="Klik untuk mengurutkan">Detail Buku <i class="fas fa-sort ms-1"></i></th>
                    <th class="col-logistik hover-sort" onclick="sortOrderTable(3)" title="Klik untuk mengurutkan">Opsi Logistik <i class="fas fa-sort ms-1"></i></th>
                    <th class="col-bayar hover-sort" onclick="sortOrderTable(4)" title="Klik untuk mengurutkan">Total Bayar <i class="fas fa-sort ms-1"></i></th>
                    <th class="col-status hover-sort" onclick="sortOrderTable(5)" title="Klik untuk mengurutkan">Status <i class="fas fa-sort ms-1"></i></th>
                    <th class="col-aksi text-center">Aksi Konfirmasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_all_orders->num_rows > 0): ?>
                    <?php while ($row = $result_all_orders->fetch_assoc()): ?>
                        <tr class="order-row">
                            <td class="txt-invoice text-truncate" title="INV/<?= date("Ymd", strtotime($row['created_at'] ?? 'now')) ?>/UT/<?= $row['id'] ?>">
                                INV/<?= date("Ymd", strtotime($row['created_at'] ?? 'now')) ?>/UT/<?= $row['id'] ?>
                            </td>
                            
                            <td class="txt-username">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="far fa-user text-secondary flex-shrink-0" style="font-size: 0.8rem;"></i>
                                    <span class="d-inline-block text-truncate user-click-trigger" 
                                          onclick="showEmailPopup(event, '<?= htmlspecialchars($row['username']) ?>')">
                                        <?= htmlspecialchars($row['username']) ?>
                                    </span>
                                </div>
                            </td>
                            
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="img/<?= htmlspecialchars($row['gambar']) ?>" 
                                         class="hover-zoom" 
                                         style="width: 32px; height: 44px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 8px rgba(0,0,0,0.3); flex-shrink: 0;"
                                         title="Klik untuk lihat sampul"
                                         onclick="openOrderPreviewModal('img/<?= htmlspecialchars($row['gambar']) ?>', '<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>')">
                                         
                                    <span class="txt-book-title d-inline-block text-truncate" title="<?= htmlspecialchars($row['judul']) ?>">
                                        <?= htmlspecialchars($row['judul']) ?>
                                    </span>
                                </div>
                            </td>
                            
                            <td>
                                <?php if ($row['tipe_pengiriman'] == 'Antar ke Rumah'): ?>
                                    <span class="text-warning fw-semibold d-block small text-truncate"><i class="fas fa-truck me-1"></i> Kirim Rumah</span>
                                    <div class="mt-0.5 text-truncate" style="font-size: 0.75rem; color: #64748b;" title="<?= htmlspecialchars($row['alamat_pengiriman']) ?>">
                                        <?= htmlspecialchars($row['alamat_pengiriman']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-success fw-semibold small text-truncate"><i class="fas fa-store me-1"></i> Ambil Mandiri</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="fw-bold text-truncate" style="color: #10b981;">
                                Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                            </td>
                            
                            <td>
                                <?php if (($row['status'] ?? 'Lunas') == 'Selesai'): ?>
                                    <span class="badge-pro badge-pro-selesai">Selesai</span>
                                <?php else: ?>
                                    <span class="badge-pro badge-pro-lunas">Diproses</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-center">
                                <?php if (($row['status'] ?? 'Lunas') == 'Lunas'): ?>
                                    <form method="POST" onsubmit="return confirm('Konfirmasi bahwa buku sudah diambil/diterima pelanggan?');" class="m-0">
                                        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="konfirmasi_selesai" class="btn-action-done">
                                            Set Selesai
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-action-disabled" disabled>Closed</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    
                    <tr id="noOrderResultRow" style="display: none;">
                        <td colspan="7" class="text-center py-5 text-secondary" style="color: #64748b !important;">
                            <i class="fas fa-search-minus d-block mb-2 fs-3" style="color: #334155;"></i>
                            Data transaksi tidak ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-secondary" style="color: #64748b !important;">
                            <i class="fas fa-inbox d-block mb-2 fs-3" style="color: #334155;"></i>
                            Belum ada antrean transaksi logistik yang masuk.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="orderImagePreviewModal" class="order-image-modal" onclick="closeOrderPreviewModal(event)">
    <div class="order-image-modal-content" onclick="event.stopPropagation()">
        <button class="order-image-modal-close" onclick="closeOrderPreviewModal(event)"><i class="fas fa-times"></i></button>
        <img id="orderPreviewImageSrc" src="" alt="Preview Sampul">
        <div id="orderPreviewImageTitle" class="order-image-modal-title"></div>
    </div>
</div>

<script>
    // --- 1. POPUP TOOLTIP EMAIL/USERNAME ---
    function showEmailPopup(event, fullEmail) {
        event.stopPropagation();
        var popup = document.getElementById('emailPopup');
        popup.innerHTML = "📧 " + fullEmail;
        popup.style.left = (event.pageX - 40) + "px";
        popup.style.top = (event.pageY - 50) + "px";
        popup.style.display = "block";
    }

    document.addEventListener('click', function(e) {
        var popup = document.getElementById('emailPopup');
        if (popup && e.target !== popup) {
            popup.style.display = 'none';
        }
    });

    // --- 2. LOGIKA PREVIEW SAMPUL ---
    const orderImageModal = document.getElementById('orderImagePreviewModal');
    const orderPreviewImageSrc = document.getElementById('orderPreviewImageSrc');
    const orderPreviewImageTitle = document.getElementById('orderPreviewImageTitle');

    function openOrderPreviewModal(imgSrc, title) {
        orderPreviewImageSrc.src = imgSrc;
        orderPreviewImageTitle.textContent = title;
        orderImageModal.style.display = 'flex';
        setTimeout(() => { orderImageModal.classList.add('show'); }, 10);
    }

    function closeOrderPreviewModal(e) {
        if (e) e.preventDefault();
        orderImageModal.classList.remove('show');
        setTimeout(() => {
            orderImageModal.style.display = 'none';
            orderPreviewImageSrc.src = '';
        }, 300);
    }

    // --- 3. LOGIKA LIVE SEARCH TABEL PEMESANAN ---
    const searchOrderInput = document.getElementById('searchOrderInput');
    
    if (searchOrderInput) {
        searchOrderInput.addEventListener('input', function() {
            const keyword = this.value.toLowerCase(); 
            const rows = document.querySelectorAll('.order-row'); 
            let visibleCount = 0;

            rows.forEach(row => {
                // Menggabungkan teks Invoice, Pelanggan, Detail Buku, Logistik & Status
                const rowText = row.innerText.toLowerCase();
                
                if (rowText.includes(keyword)) {
                    row.style.display = ''; 
                    visibleCount++;
                } else {
                    row.style.display = 'none'; 
                }
            });

            const noOrderResultRow = document.getElementById('noOrderResultRow');
            if (visibleCount === 0 && rows.length > 0) {
                noOrderResultRow.style.display = '';
            } else {
                noOrderResultRow.style.display = 'none';
            }
        });
    }

    // --- 4. LOGIKA SORTING (PENGURUTAN) TABEL ---
    function sortOrderTable(columnIndex) {
        const table = document.getElementById("orderManageTable");
        const tbody = table.querySelector("tbody");
        // Ambil baris data saja (mengabaikan pesan error)
        let rows = Array.from(tbody.querySelectorAll(".order-row")); 
        
        if (rows.length === 0) return; // Mencegah error jika tabel kosong

        const header = table.querySelectorAll("th")[columnIndex];
        let direction = header.getAttribute("data-dir") || "asc";
        
        // Kolom indeks 4 adalah "Total Bayar" (Harga dalam Rupiah)
        const isNumeric = (columnIndex === 4); 

        rows.sort((rowA, rowB) => {
            let valA = rowA.cells[columnIndex].innerText.toLowerCase().trim();
            let valB = rowB.cells[columnIndex].innerText.toLowerCase().trim();

            if (isNumeric) {
                // Menghapus string 'Rp', spasi, dan titik untuk dikonversi menjadi angka
                valA = parseInt(valA.replace(/[^0-9]/g, '')) || 0;
                valB = parseInt(valB.replace(/[^0-9]/g, '')) || 0;
            }

            if (valA < valB) return direction === "asc" ? -1 : 1;
            if (valA > valB) return direction === "asc" ? 1 : -1;
            return 0;
        });

        // Menyusun ulang baris secara DOM
        rows.forEach(row => tbody.insertBefore(row, document.getElementById('noOrderResultRow')));
        
        // Membalikkan status arah sort (Asc/Desc)
        header.setAttribute("data-dir", direction === "asc" ? "desc" : "asc");
    }
</script>