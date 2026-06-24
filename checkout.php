<?php
session_start();
include 'koneksi.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit;
}

$id_buku = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
$stmt->bind_param("i", $id_buku);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();

if (!$buku) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='dashboard_user.php';</script>";
    exit;
}

if (isset($_POST['bayar'])) {
    $user_id = $_SESSION['id'];
    $metode = $_POST['metode_pembayaran'];
    $total = $buku['harga'];

    $ins = $conn->prepare("INSERT INTO orders (user_id, book_id, total_harga, metode_pembayaran) VALUES (?, ?, ?, ?)");
    $ins->bind_param("iiis", $user_id, $id_buku, $total, $metode);
    if($ins->execute()) {
        echo "<script>alert('Pesanan Berhasil Dibuat!'); window.location='dashboard_user.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - UT Bookstore</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=1.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #0f172a !important;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .checkout-card {
            background-color: #1e293b;
            width: 100%;
            max-width: 500px;
            padding: 2.6rem;
            border-radius: 20px;
            border: 1px solid #3b82f6;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5), 0 0 20px rgba(59, 130, 246, 0.1);
        }

        .checkout-card h2 {
            color: #3b82f6;
            font-size: 1.8rem;
        }

        .book-detail {
            display: flex;
            gap: 20px;
            background: #0f172a;
            padding: 15px;
            border-radius: 14px;
            border: 1px solid #334155;
        }

        .book-detail img {
            width: 90px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        /* Setel warna dropdown select agar teks pilihan tetap putih di semua browser */
        .form-select, .form-select option {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
            border-radius: 8px;
            padding: 12px;
        }

        .form-select:focus {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
        }

        .btn-confirm {
            width: 100%;
            padding: 14px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            font-size: 1rem;
            display: block;
            text-align: center;
            text-decoration: none;
        }

        .btn-confirm:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .btn-back {
            display: block;
            text-align: center;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .btn-back:hover {
            color: white;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="checkout-card">
        <h2 class="text-center fw-bold mb-4"><i class="fas fa-shopping-cart me-2"></i>Konfirmasi Pesanan</h2>
        
        <div class="book-detail mb-4">
            <img src="img/<?= $buku['gambar'] ?>" alt="Cover">
            <div class="book-info">
                <h3 class="h6 fw-bold text-white m-0"><?= htmlspecialchars($buku['judul']) ?></h3>
                <p class="small text-secondary my-1"><?= htmlspecialchars($buku['penulis']) ?></p>
                <div class="text-success fw-bold fs-5 mt-2">Rp <?= number_format($buku['harga'],0,',','.') ?></div>
            </div>
        </div>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label small text-secondary fw-medium mb-2">
                    <i class="fas fa-wallet me-1"></i> Pilih Metode Pembayaran:
                </label>
                <select name="metode_pembayaran" class="form-select" required>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet (Gopay/OVO/Dana)</option>
                    <option value="COD">Bayar di Tempat (COD)</option>
                </select>
            </div>
            
            <button type="submit" name="bayar" class="btn-confirm mb-3">
                Konfirmasi & Bayar Sekarang
            </button>
            
            <a href="dashboard_user.php" class="btn-back">
                <i class="fas fa-arrow-left me-1"></i> Batal & Kembali
            </a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>