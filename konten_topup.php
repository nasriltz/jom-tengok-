<?php
// Ambil ID user dari session
$user_id = $_SESSION['id'];

// Proses jika tombol Top Up ditekan
if (isset($_POST['proses_topup'])) {
    $nominal = intval($_POST['nominal']);
    $metode = isset($_POST['metode_topup']) ? $_POST['metode_topup'] : '';

    if ($nominal >= 10000) {
        if (!empty($metode)) {
            // Query untuk menambahkan saldo saat ini dengan nominal baru
            $query_topup = "UPDATE users SET saldo = saldo + ? WHERE id = ?";
            $stmt_topup = $conn->prepare($query_topup);
            $stmt_topup->bind_param("ii", $nominal, $user_id);
            
            if ($stmt_topup->execute()) {
                echo "<script>
                        alert('Top Up Berhasil! Saldo sebesar Rp " . number_format($nominal, 0, ',', '.') . " via " . strtoupper($metode) . " telah ditambahkan.');
                        window.location='dashboard_user.php?page=dashboard';
                      </script>";
                exit;
            } else {
                echo "<script>alert('Gagal memproses Top Up.');</script>";
            }
        } else {
            echo "<script>alert('Silakan pilih metode pembayaran terlebih dahulu.');</script>";
        }
    } else {
        echo "<script>alert('Minimal Top Up adalah Rp 10.000');</script>";
    }
}

// Ambil data saldo user saat ini untuk ditampilkan
$query_saldo = mysqli_query($conn, "SELECT saldo FROM users WHERE id = '$user_id'");
$data_saldo = mysqli_fetch_assoc($query_saldo);
$saldo_sekarang = $data_saldo['saldo'] ?? 0;
?>

<style>
    .premium-card {
        background: linear-gradient(145deg, #1e293b, #0f172a);
        border: 1px solid #334155;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }
    .balance-glow {
        background: linear-gradient(135deg, #1e293b, #1e3a8a);
        border: 1px solid #3b82f6;
        position: relative;
        overflow: hidden;
    }
    .balance-glow::after {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(59,130,246,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    .btn-quick {
        background: #1e293b;
        border: 1px solid #475569;
        color: #cbd5e1;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .btn-quick:hover {
        background: #334155;
        border-color: #3b82f6;
        color: #fff;
        transform: translateY(-2px);
    }
    /* Custom Payment Method Radio Buttons */
    .pay-method-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
    }
    .pay-option {
        position: relative;
    }
    .pay-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0; height: 0;
    }
    .pay-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px;
        background: #0f172a;
        border: 2px solid #334155;
        border-radius: 14px;
        cursor: pointer;
        color: #94a3b8;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .pay-box i {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }
    .pay-option input[type="radio"]:checked + .pay-box {
        border-color: #3b82f6;
        background: rgba(59, 130, 246, 0.1);
        color: #60a5fa;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
    }
    .pay-box:hover {
        border-color: #475569;
        color: #fff;
    }
</style>

<div class="p-4">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h2 class="text-white fw-bold h4 m-0">Dompet Digital</h2>
            <p class="text-secondary small mt-1 mb-0">Isi saldo instan untuk kenyamanan transaksi berbelanja buku.</p>
        </div>
        <div class="badge bg-primary px-3 py-2 rounded-pill" style="font-size: 0.8rem; background: rgba(59, 130, 246, 0.2) !important; color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.4);">
            <i class="fas fa-shield-alt me-1"></i> Secured Payment
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card premium-card balance-glow p-4 text-center text-white h-100 d-flex flex-column justify-content-center">
                <div class="text-primary mb-3">
                    <span class="p-3 rounded-circle d-inline-block" style="background: rgba(59, 130, 246, 0.15);">
                        <i class="fas fa-wallet fa-2x text-primary"></i>
                    </span>
                </div>
                <h4 class="h6 text-secondary fw-medium uppercase tracking-wider m-0">SALDO AKTIF UT-PAY</h4>
                <h2 class="fw-bold text-success mt-2 mb-0 h2 text-gradient" style="text-shadow: 0 0 20px rgba(34, 197, 94, 0.2);">
                    Rp <?= number_format($saldo_sekarang, 0, ',', '.') ?>
                </h2>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card premium-card p-4 text-white">
                <form method="POST" id="formTopup">
                    
                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-bold text-uppercase">1. Masukkan Nominal</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-dark border-secondary text-secondary fw-bold" style="border-radius: 12px 0 0 12px;">Rp</span>
                            <input type="number" id="inputNominal" name="nominal" class="form-control bg-dark border-secondary text-white" placeholder="0" min="10000" style="border-radius: 0 12px 12px 0; font-weight: 600;" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-bold text-uppercase">Atau Pilih Instan</label>
                        <div class="row g-2">
                            <div class="col-4 col-sm-3"><button type="button" class="btn btn-quick w-100" onclick="setNominal(20000)">20K</button></div>
                            <div class="col-4 col-sm-3"><button type="button" class="btn btn-quick w-100" onclick="setNominal(50000)">50K</button></div>
                            <div class="col-4 col-sm-3"><button type="button" class="btn btn-quick w-100" onclick="setNominal(100000)">100K</button></div>
                            <div class="col-4 col-sm-3"><button type="button" class="btn btn-quick w-100" onclick="setNominal(200000)">200K</button></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small fw-bold text-uppercase mb-3">2. Metode Pembayaran</label>
                        <div class="pay-method-grid">
                            
                            <label class="pay-option">
                                <input type="radio" name="metode_topup" value="Dana" required>
                                <div class="pay-box">
                                    <i class="fas fa-mobile-alt text-info"></i>
                                    <span>DANA</span>
                                </div>
                            </label>

                            <label class="pay-option">
                                <input type="radio" name="metode_topup" value="Ovo">
                                <div class="pay-box">
                                    <i class="fas fa-coins text-warning"></i>
                                    <span>OVO</span>
                                </div>
                            </label>

                            <label class="pay-option">
                                <input type="radio" name="metode_topup" value="Gopay">
                                <div class="pay-box">
                                    <i class="fas fa-wallet text-success"></i>
                                    <span>GOPAY</span>
                                </div>
                            </label>

                            <label class="pay-option">
                                <input type="radio" name="metode_topup" value="Transfer Bank">
                                <div class="pay-box">
                                    <i class="fas fa-university text-danger"></i>
                                    <span>VA BANK</span>
                                </div>
                            </label>

                        </div>
                    </div>

                    <button type="submit" name="proses_topup" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow mt-2" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); border: none; font-size: 1.05rem; letter-spacing: 0.5px; border-radius: 12px !important;">
                        <i class="fas fa-bolt me-2"></i> Konfirmasi Isi Saldo
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setNominal(nilai) {
    document.getElementById('inputNominal').value = nilai;
}
</script>