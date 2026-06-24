<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'koneksi.php';

$user_id = $_SESSION['id'];

// Query mengambil data pemesanan (ditambahkan kolom o.status agar dinamis)
$query_orders = "SELECT o.*, b.judul, b.penulis, b.gambar 
                 FROM orders o 
                 JOIN books b ON o.book_id = b.id 
                 WHERE o.user_id = ? 
                 ORDER BY o.id DESC";

$stmt_orders = $conn->prepare($query_orders);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
?>

<div class="container p-0">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold m-0 text-white" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                <span class="text-primary"><i class="fas fa-receipt me-2"></i></span>Riwayat Belanja
            </h2>
            <p class="text-secondary m-0 small mt-1">Pantau terus status pengiriman buku dan invoice pembelianmu disini.</p>
        </div>
        <div style="background: rgba(59, 130, 246, 0.1); border: 1px dashed rgba(59, 130, 246, 0.3); padding: 8px 16px; border-radius: 10px;">
            <span class="small text-primary fw-medium"><i class="fas fa-box-open me-2"></i>Total Transaksi: <strong><?= $result_orders->num_rows ?></strong></span>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 25px;">
        <?php if ($result_orders->num_rows > 0): ?>
            <?php while ($order = $result_orders->fetch_assoc()): ?>
                
                <div class="luxury-order-card">
                    <div class="card-top-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-shopping-bag text-primary small"></i>
                            <span class="invoice-num">INV/<?= date("Ymd", strtotime($order['created_at'] ?? 'now')) ?>/UT/<?= $order['id'] ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-payment"><i class="fas fa-wallet me-1"></i> <?= htmlspecialchars($order['metode_pembayaran']) ?></span>
                        </div>
                    </div>

                    <div class="card-main-grid">
                        
                        <div class="prod-details">
                            <img src="img/<?= htmlspecialchars($order['gambar']) ?>" alt="Cover" class="prod-img">
                            <div>
                                <h4 class="prod-title"><?= htmlspecialchars($order['judul']) ?></h4>
                                <p class="prod-author">Oleh <?= htmlspecialchars($order['penulis']) ?></p>
                                <div class="prod-price-tag">
                                    <span class="text-secondary small">Total Bayar:</span>
                                    <span class="price-num">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Segmen 2: Alur Logistik / Delivery Tracker (Dinamis sinkron dengan database) -->
                        <div class="delivery-status-box">
                            <div class="timeline-stepper">
                                <!-- Step 1: Terbayar -->
                                <div class="step active">
                                    <div class="step-dot"><i class="fas fa-check text-white" style="font-size: 8px;"></i></div>
                                    <span class="step-label">Lunas</span>
                                </div>
                                
                                <div class="step-line active"></div>

                                <!-- Step 2 & 3: Proses/Kirim Dinamis Berdasarkan Tipe & Status -->
                                <?php if ($order['tipe_pengiriman'] == 'Antar ke Rumah'): ?>
                                    <!-- JIKA ANTAR KE RUMAH -->
                                    <div class="step active">
                                        <div class="step-dot delivery-orange"><i class="fas fa-truck text-white" style="font-size: 8px;"></i></div>
                                        <span class="step-label text-warning">Kurir Jalan</span>
                                    </div>
                                    
                                    <div class="step-line <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'active' : '' ?>"></div>
                                    
                                    <div class="step <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'active' : 'pending' ?>">
                                        <div class="step-dot <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'delivery-green' : '' ?>">
                                            <i class="fas fa-home <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'text-white' : '' ?>" style="font-size: 8px;"></i>
                                        </div>
                                        <span class="step-label">Diterima</span>
                                    </div>
                                <?php else: ?>
                                    <!-- JIKA AMBIL DI TOKO -->
                                    <div class="step active">
                                        <div class="step-dot delivery-orange"><i class="fas fa-store text-white" style="font-size: 8px;"></i></div>
                                        <span class="step-label text-warning">Siap Ambil</span>
                                    </div>
                                    
                                    <div class="step-line <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'active' : '' ?>"></div>
                                    
                                    <div class="step <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'active' : 'pending' ?>">
                                        <div class="step-dot <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'delivery-green' : '' ?>">
                                            <i class="fas fa-user-check <?= (isset($order['status']) && $order['status'] == 'Selesai') ? 'text-white' : '' ?>" style="font-size: 8px;"></i>
                                        </div>
                                        <span class="step-label">Selesai</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Keterangan Alamat / Lokasi Pengambilan Kotak Meta-Box -->
                            <div class="delivery-meta-box">
                                <?php if (isset($order['status']) && $order['status'] == 'Selesai'): ?>
                                    <div class="address-text" style="color: #10b981 !important;">
                                        <i class="fas fa-check-double text-success mt-1"></i>
                                        <div>
                                            <strong class="text-success d-block mb-1" style="font-size: 0.75rem;">Pesanan Berhasil Diselesaikan:</strong>
                                            Terima kasih telah berbelanja di UT Bookstore! Buku telah sukses diserahterimakan.
                                        </div>
                                    </div>
                                <?php elseif ($order['tipe_pengiriman'] == 'Antar ke Rumah'): ?>
                                    <div class="address-text">
                                        <i class="fas fa-map-pin text-danger mt-1"></i>
                                        <div>
                                            <strong class="text-white d-block mb-1" style="font-size: 0.75rem;">Dikirim ke Alamat Rumah:</strong>
                                            <?= htmlspecialchars($order['alamat_pengiriman']) ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="address-text pickup-mode">
                                        <i class="fas fa-map-marked-alt text-success mt-1"></i>
                                        <div>
                                            <strong class="text-white d-block mb-1" style="font-size: 0.75rem;">Lokasi Pengambilan:</strong>
                                            Loket Utama UT Bookstore (Bawa Bukti Invoice di Atas).
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state-card">
                <div class="empty-icon-circle">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4 class="text-white fw-bold m-0 mt-3" style="font-size: 1.1rem;">Belum Ada Riwayat Pembelian</h4>
                <p class="text-secondary small m-0 mt-1">Semua transaksi buku digital maupun fisik kamu akan terekam otomatis di halaman ini.</p>
                <a href="dashboard_user.php?page=katalog" class="btn-shop-now">
                    Mulai Jelajahi Katalog <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .luxury-order-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .luxury-order-card:hover {
        transform: translateY(-3px);
        border-color: #3b82f6;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.08);
    }
    
    .card-top-header {
        background: #151f32;
        padding: 12px 20px;
        border-bottom: 1px solid #334155;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .invoice-num {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.8rem;
        font-weight: bold;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }
    .badge-payment {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.25);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .card-main-grid {
        display: grid;
        grid-template-columns: 1.2fr 1.5fr;
        gap: 20px;
        padding: 20px;
    }
    @media (max-width: 768px) {
        .card-main-grid { grid-template-columns: 1fr; }
    }

    .prod-details {
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }
    .prod-img {
        width: 75px;
        height: 105px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #475569;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .prod-title {
        color: white;
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 4px 0;
        line-height: 1.3;
    }
    .prod-author {
        color: #64748b;
        font-size: 0.8rem;
        font-style: italic;
        margin: 0 0 12px 0;
    }
    .prod-price-tag {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .price-num {
        color: #10b981;
        font-weight: 800;
        font-size: 1.15rem;
    }

    /* TRACKER STEPPER TIMELINE */
    .delivery-status-box {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 15px;
    }
    .timeline-stepper {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding-left: 5px;
    }
    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        min-width: 60px;
    }
    .step-dot {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #334155;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .step.active .step-dot { background: #3b82f6; box-shadow: 0 0 8px #3b82f6; }
    .step-dot.delivery-orange { background: #f97316 !important; box-shadow: 0 0 8px #f97316 !important; }
    .step-dot.delivery-green { background: #10b981 !important; box-shadow: 0 0 8px #10b981 !important; }
    
    .step-label {
        font-size: 0.7rem;
        margin-top: 6px;
        font-weight: 600;
        color: #64748b;
        white-space: nowrap;
    }
    .step.active .step-label { color: #f8fafc; }
    
    .step-line {
        flex-grow: 0.15;
        height: 2px;
        background: #334155;
        margin-bottom: 16px; /* Balance label gap */
    }
    .step-line.active { background: #3b82f6; }

    /* INNER METABOX INFO */
    .delivery-meta-box {
        background: #0f172a;
        border: 1px solid #334155;
        padding: 12px 14px;
        border-radius: 12px;
    }
    .address-text {
        display: flex;
        gap: 10px;
        font-size: 0.78rem;
        color: #94a3b8;
        line-height: 1.4;
    }
    .address-text.pickup-mode { color: #cbd5e1; }

    /* EMPTY STATE */
    .empty-state-card {
        text-align: center;
        padding: 4.5rem 2rem;
        background: #1e293b;
        border-radius: 16px;
        border: 1px dashed #334155;
    }
    .empty-icon-circle {
        width: 65px;
        height: 65px;
        background: rgba(71, 85, 105, 0.2);
        color: #475569;
        font-size: 1.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .btn-shop-now {
        display: inline-block;
        margin-top: 20px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        transition: 0.2s;
    }
    .btn-shop-now:hover {
        background: #2563eb;
        color: white;
        transform: scale(1.02);
    }
</style>