<?php
session_start();
include 'koneksi.php';

// Proteksi halaman admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// 1. AMBIL DATA LAMA
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM books WHERE id = $id");
    $data = $result->fetch_assoc();

    if (!$data) {
        echo "<script>alert('Data tidak ditemukan!'); window.location='dashboard_admin.php';</script>";
        exit;
    }
}

// 2. PROSES UPDATE SAAT TOMBOL DIKLIK
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $judul = htmlspecialchars($_POST['judul']);
    $penulis = htmlspecialchars($_POST['penulis']);
    $kategori = htmlspecialchars($_POST['kategori']); // MENANGKAP KATEGORI
    $harga = $_POST['harga'];
    $gambar_lama = $_POST['gambar_lama'];

    // Cek apakah user upload gambar baru
    if ($_FILES['gambar']['name'] != "") {
        $nama_file = $_FILES['gambar']['name'];
        $tmp_file = $_FILES['gambar']['tmp_name'];
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_baru = time() . "_" . $nama_file; 

        if (move_uploaded_file($tmp_file, "img/" . $nama_baru)) {
            $gambar_final = $nama_baru;
            if (file_exists("img/" . $gambar_lama)) {
                unlink("img/" . $gambar_lama);
            }
        }
    } else {
        $gambar_final = $gambar_lama; 
    }

    // Query disesuaikan dengan menambahkan kolom kategori
    $sql = "UPDATE books SET judul=?, penulis=?, kategori=?, harga=?, gambar=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $judul, $penulis, $kategori, $harga, $gambar_final, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Buku berhasil diperbarui!'); window.location='dashboard_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - UT Bookstore</title>
    <link rel="stylesheet" href="style.css?v=1.9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-container { background: #1e293b; padding: 30px; border-radius: 15px; border: 1px solid #334155; max-width: 600px; margin: 2rem auto; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; color: #94a3b8; margin-bottom: 8px; }
        .form-control, .form-select { width: 100%; padding: 12px; background: #0f172a; border: 1px solid #334155; color: white; border-radius: 8px; box-sizing: border-box; }
        .form-control:focus, .form-select:focus { outline: none; border-color: #3b82f6; }
        .current-img { width: 100px; border-radius: 8px; margin-top: 5px; margin-bottom: 10px; border: 2px solid #3b82f6; display: block; }
        
        /* Layout tombol berdampingan, bebas tabrakan css luar */
        .edit-btn-group { 
            display: grid !important; 
            grid-template-columns: 1fr 1fr !important; 
            gap: 15px !important; 
            width: 100% !important; 
            margin-top: 25px !important; 
            box-sizing: border-box !important;
        }
    </style>
</head>
<body style="background: #0f172a; color: white; font-family: sans-serif;">

    <div class="form-container">
        <h2 style="margin-bottom: 1.5rem; color: #3b82f6;"><i class="fas fa-edit"></i> Edit Data Buku</h2>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $data['id'] ?>">
            <input type="hidden" name="gambar_lama" value="<?= $data['gambar'] ?>">

            <div class="form-group">
                <label>Judul Buku</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($data['penulis']) ?>" required>
            </div>

            <div class="form-group">
                <label>Kategori / Genre</label>
                <select name="kategori" class="form-select" required>
                    <option value="Fiksi" <?= $data['kategori'] == 'Fiksi' ? 'selected' : '' ?>>Fiksi</option>
                    <option value="Non-Fiksi" <?= $data['kategori'] == 'Non-Fiksi' ? 'selected' : '' ?>>Non-Fiksi</option>
                    <option value="Teknologi" <?= $data['kategori'] == 'Teknologi' ? 'selected' : '' ?>>Teknologi</option>
                    <option value="Sains" <?= $data['kategori'] == 'Sains' ? 'selected' : '' ?>>Sains</option>
                    <option value="Komik" <?= $data['kategori'] == 'Komik' ? 'selected' : '' ?>>Komik</option>
                    <option value="Novel" <?= $data['kategori'] == 'Novel' ? 'selected' : '' ?>>Novel</option>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
            </div>

            <div class="form-group">
                <label>Sampul Saat Ini</label>
                <img src="img/<?= $data['gambar'] ?>" class="current-img">
                
                <label style="margin-top: 15px;">Ganti Sampul Buku</label>
                <input type="file" name="gambar" class="form-control">
            </div>

            <div class="edit-btn-group">
                <button type="submit" name="update" style="background: #3b82f6 !important; border: none !important; color: white !important; cursor: pointer !important; width: 100% !important; min-width: 100% !important; max-width: 100% !important; padding: 12px 0 !important; border-radius: 8px !important; font-weight: bold !important; font-size: 14px !important; display: block !important; box-sizing: border-box !important;">
                    Simpan Perubahan
                </button>
                <a href="dashboard_admin.php" style="background: #334155 !important; color: white !important; text-decoration: none !important; text-align: center !important; width: 100% !important; min-width: 100% !important; max-width: 100% !important; padding: 12px 0 !important; border-radius: 8px !important; font-weight: bold !important; font-size: 14px !important; display: block !important; box-sizing: border-box !important; line-height: 20px !important;">
                    Batal
                </a>
            </div>
        </form>
    </div>

</body>
</html>