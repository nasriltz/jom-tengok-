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
        overflow: hidden; /* Memastikan tidak ada luapan dari card */
    }

    /* Memaksa tabel mengikuti lebar 100% card tanpa overflow */
    .table-manage {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        color: #f8fafc !important;
        table-layout: fixed; /* Membagi kolom secara proporsional dan disiplin */
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

    .table-manage td {
        padding: 18px 12px;
        color: #f8fafc !important;
        border-bottom: 1px solid rgba(51, 65, 85, 0.3);
        vertical-align: middle;
        background: transparent !important;
    }

    /* Custom lebar kolom agar proporsional di layar */
    .col-invoice { width: 18%; }
    .col-pelanggan { width: 16%; }
    .col-buku { width: 22%; }
    .col-logistik { width: 16%; }
    .col-bayar { width: 12%; }
    .col-status { width: 10%; }
    .col-aksi { width: 10%; }

    .txt-invoice {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: #cbd5e1 !important;
    }

    .txt-username {
        font-weight: 600;
        color: #ffffff !important;
        font-size: 0.95rem;
    }

    /* Trigger klik */
    .user-click-trigger {
        cursor: pointer;
        color: #ffffff;
        border-bottom: 1px dashed #64748b;
        transition: all 0.2s;
    }
    .user-click-trigger:hover {
        color: #3b82f6 !important;
        border-bottom-color: #3b82f6;
    }

    /* Custom CSS Popup Window Dark Mode */
    .custom-dark-popup {
        display: none;
        position: absolute;
        background: #1e293b;
        border: 1px solid #3b82f6;
        color: #f8fafc;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        z-index: 9999;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        font-weight: 500;
    }

    .txt-book-title {
        font-weight: 600;
        color: #f1f5f9 !important;
        font-size: 0.9rem;
    }

    .badge-pro {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-pro-lunas {
        background: rgba(59, 130, 246, 0.12) !important;
        color: #3b82f6 !important;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .badge-pro-selesai {
        background: rgba(16, 185, 129, 0.12) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .btn-action-done {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white !important;
        border: none;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        white-space: nowrap;
    }

    .btn-action-disabled {
        background: rgba(255, 255, 255, 0.05);
        color: #475569 !important;
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 0.8rem;
        white-space: nowrap;
    }
</style>

<div id="emailPopup" class="custom-dark-popup"></div>

<div class="pro-management-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h5 text-white m-0 fw-bold"><i class="fas fa-stream text-primary me-2"></i>Antrean Logistik Masuk</h2>
        </div>
        <span class="badge bg-secondary px-3 py-2" style="font-size:0.75rem; border-radius:8px;">
            Total: <?= $result_all_orders->num_rows ?> Transaksi
        </span>
    </div>
    
    <table class="table table-manage m-0">
        <thead>
            <tr>
                <th class="col-invoice">Invoice ID</th>
                <th class="col-pelanggan">Pelanggan</th>
                <th class="col-buku">Detail Buku</th>
                <th class="col-logistik">Opsi Logistik</th>
                <th class="col-bayar">Total Bayar</th>
                <th class="col-status">Status</th>
                <th class="col-aksi text-center">Aksi Konfirmasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_all_orders->num_rows > 0): ?>
                <?php while ($row = $result_all_orders->fetch_assoc()): ?>
                    <tr>
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
                                <img src="img/<?= htmlspecialchars($row['gambar']) ?>" style="width: 32px; height: 44px; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 8px rgba(0,0,0,0.3); flex-shrink: 0;">
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
                                <span class="badge-pro badge-pro-selesai">
                                    Selesai
                                </span>
                            <?php else: ?>
                                <span class="badge-pro badge-pro-lunas">
                                    Diproses
                                </span>
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
                                <button class="btn-action-disabled" disabled>
                                    Closed
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
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

<script>
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
</script>