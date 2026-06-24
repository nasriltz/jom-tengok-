<?php
session_start();
include 'koneksi.php';

// 1. PROTEKSI UTAMA: Kunci dulu status Admin-nya. 
// Jika bukan admin, baru lempar ke login.php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 2. PENGECEKAN ID: Jika status sudah pasti admin, baru cek ID bukunya
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Amankan ID menjadi format angka murni

    // Ambil nama file gambar secara aman dengan Prepared Statement
    $stmt_cek = $conn->prepare("SELECT gambar FROM books WHERE id = ?");
    $stmt_cek->bind_param("i", $id);
    $stmt_cek->execute();
    $result = $stmt_cek->get_result();

    if ($result->num_rows > 0) {
        $cek = $result->fetch_assoc();
        
        // Hapus file fisik gambar di folder img/ jika filenya ada
        if (!empty($cek['gambar']) && file_exists("img/" . $cek['gambar'])) {
            unlink("img/" . $cek['gambar']);
        }
        
        // Hapus data dari database secara aman
        $stmt_hapus = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt_hapus->bind_param("i", $id);
        $stmt_hapus->execute();
        $stmt_hapus->close();
    }
    $stmt_cek->close();
}

// 3. KEMBALI KE DASHBOARD ADMIN SECARA AMAN
header("Location: dashboard_admin.php");
exit; // Menghentikan baris program agar session tidak bocor/hilang di jalan
?>