<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

$step = 1; // Step 1: Input Username
$username_input = '';

// Proses Step 1: Cek Username
if (isset($_POST['cek_username'])) {
    $username = $_POST['username'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $step = 2; // Lanjut ke Step 2 (Input Password Baru)
        $_SESSION['reset_username'] = $username; // Simpan username di session sementara
    } else {
        echo "<script>alert('Username tidak ditemukan!');</script>";
    }
}

// Proses Step 2: Update Password Baru
if (isset($_POST['update_password'])) {
    if (isset($_SESSION['reset_username'])) {
        $username = $_SESSION['reset_username'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        // Validasi minimal 8 karakter di sisi PHP
        if (strlen($new_pass) < 8) {
            $step = 2;
            echo "<script>alert('Password gagal disimpan! Password harus minimal 8 karakter.');</script>";
        } else {
            if ($new_pass === $confirm_pass) {
                // Hash password baru agar aman
                $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $stmt->bind_param("ss", $hashed_password, $username);

                if ($stmt->execute()) {
                    unset($_SESSION['reset_username']); // Hapus session sementara
                    echo "<script>
                            alert('Password berhasil diperbarui! Silakan login kembali.');
                            window.location.href='login.php';
                          </script>";
                    exit;
                } else {
                    echo "<script>alert('Gagal memperbarui password.');</script>";
                }
            } else {
                $step = 2; // Tetap di step 2 karena password tidak cocok
                echo "<script>alert('Konfirmasi password tidak cocok!');</script>";
            }
        }
    } else {
        echo "<script>alert('Sesi habis, silakan ulang dari awal.'); window.location.href='reset.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - UT Bookstore</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=1.9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Perbaikan Warna Kolom Input Teks agar Putih Bersih */
        input.form-control, 
        input.form-control:focus,
        input.form-control:active {
            background-color: #0f172a !important;
            color: #ffffff !important; /* Teks ketikan jadi putih */
            border: 1px solid #334155 !important;
            border-radius: 10px !important; 
            padding: 12px 45px 12px 40px !important; 
            transition: all 0.3s ease;
        }

        /* Warna Placeholder (Teks petunjuk sebelum diketik) dibuat abu-abu terang agar kelihatan */
        input.form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        /* Mengatasi Autofill Browser */
        input.form-control:-webkit-autofill,
        input.form-control:-webkit-autofill:hover, 
        input.form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important; 
            -webkit-box-shadow: 0 0 0px 1000px #0f172a inset !important; 
        }

        input.form-control:focus {
            border-color: #3b82f6 !important; 
            box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25) !important;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0f172a; 
        }
        
        .custom-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        /* Icon Mata untuk lihat password */
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            color: #94a3b8;
        }
        .toggle-password:hover {
            color: #3b82f6;
        }

        /* Indikator Real-time Minimal 8 Karakter */
        .length-indicator {
            font-size: 12px;
            margin-top: 6px;
            display: block;
        }
        .text-invalid { color: #f87171 !important; } /* Merah terang */
        .text-valid { color: #4ade80 !important; }   /* Hijau terang */
    </style>
</head>
<body class="auth-wrapper login-wrapper"> 

    <div class="container d-flex justify-content-center align-items-center">
        <div class="custom-card">
            
            <div class="text-center mb-4">
                <i class="fas fa-user-shield fa-3x mb-3" style="color: #3b82f6;"></i>
                <h2 class="fw-bold h3 text-white">Reset Password</h2>
                <p class="text-white-50 small">Pulihkan akses akun Anda</p>
            </div>

            <?php if ($step === 1): ?>
                <form method="POST" action="">
                    <div class="mb-4">
                        <label class="form-label small text-secondary fw-medium">Username Anda</label>
                        <div class="position-relative">
                            <i class="fas fa-user position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username akun" required>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="cek_username" class="btn btn-primary py-2 fw-bold rounded-3">
                            Lanjutkan <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                        <a href="login.php" class="btn btn-outline-secondary py-2 fw-bold rounded-3 text-white text-decoration-none mt-1">
                            Kembali ke Login
                        </a>
                    </div>
                </form>

            <?php elseif ($step === 2): ?>
                <form method="POST" action="">
                    <p class="text-info small mb-3 text-center">Akun ditemukan: <strong><?= htmlspecialchars($_SESSION['reset_username']) ?></strong></p>
                    
                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-medium">Password Baru</label>
                        <div class="position-relative">
                            <i class="fas fa-lock position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Masukkan password baru" minlength="8" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePass('new_password', this)"></i>
                        </div>
                        <span id="char-note" class="length-indicator text-invalid">
                            <i class="fas fa-times-circle"></i> Password harus minimal 8 karakter.
                        </span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-secondary fw-medium">Konfirmasi Password Baru</label>
                        <div class="position-relative">
                            <i class="fas fa-check-double position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password baru" minlength="8" required>
                            <i class="fas fa-eye toggle-password" onclick="togglePass('confirm_password', this)"></i>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="update_password" class="btn btn-success py-2 fw-bold rounded-3">
                            Simpan Password Baru
                        </button>
                        <a href="reset.php" class="btn btn-outline-secondary py-2 fw-bold rounded-3 text-white text-decoration-none mt-1">
                            Batal
                        </a>
                    </div>
                </form>
            <?php endif; ?>
            
        </div>
    </div>

    <script>
        // Fungsi Mata Password
        function togglePass(inputId, iconElement) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                iconElement.classList.remove("fa-eye");
                iconElement.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                iconElement.classList.remove("fa-eye-slash");
                iconElement.classList.add("fa-eye");
            }
        }

        // Validasi real-time 8 karakter
        const passwordInput = document.getElementById('new_password');
        const charNote = document.getElementById('char-note');

        if(passwordInput) {
            passwordInput.addEventListener('input', function() {
                if (passwordInput.value.length >= 8) {
                    charNote.classList.remove('text-invalid');
                    charNote.classList.add('text-valid');
                    charNote.innerHTML = '<i class="fas fa-check-circle"></i> Panjang password memenuhi syarat.';
                } else {
                    charNote.classList.remove('text-valid');
                    charNote.classList.add('text-invalid');
                    charNote.innerHTML = '<i class="fas fa-times-circle"></i> Password harus minimal 8 karakter.';
                }
            });
        }
    </script>

</body>
</html>