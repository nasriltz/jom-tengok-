<?php
session_start();
include 'koneksi.php';

// PROTEKSI KETAT: Mencegah session bocor yang bikin auto-logout
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $judul = htmlspecialchars($_POST['judul']);
    $penulis = htmlspecialchars($_POST['penulis']);
    $kategori = htmlspecialchars($_POST['kategori']); // MENANGKAP KATEGORI
    $harga = intval($_POST['harga']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    
    // Upload Gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $gambar_baru = time() . "_" . $gambar;
    
    if (move_uploaded_file($tmp, "img/" . $gambar_baru)) {
        // Query disesuaikan dengan menambahkan kolom kategori
        $stmt = $conn->prepare("INSERT INTO books (judul, penulis, kategori, harga, gambar, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $judul, $penulis, $kategori, $harga, $gambar_baru, $deskripsi);
        $stmt->execute();
        $stmt->close();
        
        header("Location: dashboard_admin.php");
        exit;
    } else {
        echo "<script>alert('Gagal upload gambar!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - UT Bookstore Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .form-card {
            background-color: #1e293b;
            border: 1px solid rgba(59, 130, 246, 0.2); 
            border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 25px rgba(59, 130, 246, 0.05) !important;
        }
        .form-control, .form-select {
            background-color: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
        }
        .form-control::placeholder {
            color: #475569;
        }
        .form-label {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        /* Kotak Preview Gambar Padat & Rapi */
        .preview-box {
            background-color: #0f172a;
            border: 2px dashed #334155;
            border-radius: 12px;
            width: 110px;
            height: 145px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #475569;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: inset 0 4px 10px rgba(0,0,0,0.3);
        }
        .preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        .form-control::file-selector-button {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 8px;
            padding: 6px 14px;
            transition: all 0.2s ease;
        }
        .form-control::file-selector-button:hover {
            background-color: #3b82f6;
            color: white;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
        }
        .btn-link-custom {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-link-custom:hover {
            color: #f8fafc;
        }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                
                <div class="d-flex align-items-center gap-3 mb-4 px-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 42px; height: 42px; background-color: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);">
                        <i class="fas fa-plus-circle fs-5"></i>
                    </div>
                    <div>
                        <h2 class="h4 fw-bold text-white m-0">Tambah Buku Baru</h2>
                        <p class="text-secondary small m-0">Management System / Books / Create</p>
                    </div>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="form-card p-4 p-md-5">
                    <div class="row g-4">
                        
                        <div class="col-12 col-md-6 pe-md-4 border-end border-secondary border-opacity-25 d-flex flex-column gap-3">
                            <div>
                                <label class="form-label"><i class="fas fa-bookmark text-primary me-2"></i>Judul Buku</label>
                                <input type="text" name="judul" class="form-control" placeholder="Masukkan judul buku..." required>
                            </div>
                            
                            <div>
                                <label class="form-label"><i class="fas fa-user-edit text-primary me-2"></i>Penulis / Creator</label>
                                <input type="text" name="penulis" class="form-control" placeholder="Nama penulis..." required>
                            </div>

                            <div>
                                <label class="form-label"><i class="fas fa-th-list text-primary me-2"></i>Kategori / Genre</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    <option value="Fiksi">Fiksi</option>
                                    <option value="Non-Fiksi">Non-Fiksi</option>
                                    <option value="Teknologi">Teknologi</option>
                                    <option value="Sains">Sains</option>
                                    <option value="Komik">Komik</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="form-label"><i class="fas fa-tags text-primary me-2"></i>Harga Buku (Rp)</label>
                                <input type="number" name="harga" class="form-control" placeholder="Contoh: 85000" required>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6 ps-md-4 d-flex flex-column gap-3">
                            <div>
                                <label class="form-label"><i class="fas fa-image text-primary me-2"></i>Upload Gambar Sampul</label>
                                <input type="file" name="gambar" id="imageInput" class="form-control" accept="image/*" required>
                            </div>

                            <div class="d-flex align-items-center justify-content-start gap-3 p-3 rounded-4 w-100" style="background-color: rgba(15, 23, 42, 0.4); border: 1px solid #334155;">
                                <div class="preview-box flex-shrink-0" id="previewContainer">
                                    <i class="fas fa-file-image fa-lg mb-1" id="previewIcon"></i>
                                    <span class="text-center px-1" id="previewText" style="font-size: 0.65rem;">No Image</span>
                                    <img id="imagePreview" src="#" alt="Preview Sampul">
                                </div>
                                <div>
                                    <span class="form-label d-block mb-1" style="font-size: 0.75rem; color: #3b82f6;"><i class="fas fa-eye me-1"></i> Live Cover Preview</span>
                                    <p class="text-secondary small m-0" style="font-size: 0.7rem; line-height: 1.4;">Sampul yang kamu pilih otomatis dirender di kotak sebelah kiri secara real-time sebelum disimpan.</p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-grow-1">
                                <label class="form-label"><i class="fas fa-align-left text-primary me-2"></i>Deskripsi & Sinopsis</label>
                                <textarea name="deskripsi" class="form-control flex-grow-1" placeholder="Tuliskan sinopsis atau deskripsi singkat mengenai buku ini..." style="min-height: 120px; resize: none;"></textarea>
                            </div>
                        </div>
                        
                    </div>

                    <div class="mt-5 pt-3 border-top border-secondary border-opacity-25 d-flex flex-column align-items-center gap-3">
                        <button type="submit" name="simpan" class="btn btn-gradient w-100 fw-bold py-3 rounded-3 fs-6">
                            <i class="fas fa-save me-2"></i>Simpan Buku ke Sistem
                        </button>
                        
                        <a href="dashboard_admin.php" class="btn-link-custom small d-flex align-items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Batal & Kembali ke Panel Admin
                        </a>
                    </div>
                    
                </form>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.getElementById('imagePreview');
                    const iconElement = document.getElementById('previewIcon');
                    const textElement = document.getElementById('previewText');
                    const container = document.getElementById('previewContainer');
                    
                    imgElement.src = e.target.result;
                    imgElement.style.display = 'block';
                    
                    iconElement.style.display = 'none';
                    textElement.style.display = 'none';
                    container.style.borderStyle = 'solid';
                    container.style.borderColor = '#3b82f6';
                    container.style.boxShadow = '0 0 15px rgba(59, 130, 246, 0.3)';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>