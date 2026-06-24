<?php
// Cek dulu biar gak double session_start yang bikin error/ancur
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // PENGALIHAN BERDASARKAN ROLE & NAMA FILE YANG BENAR
            if ($row['role'] == 'admin') {
                echo "<script>window.location.href='dashboard_admin.php';</script>";
            } else {
                echo "<script>window.location.href='dashboard_user.php';</script>";
            }
            exit;
        }
    }
    echo "<script>alert('Username atau Password salah!'); window.location.href='login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - UT Bookeepstore</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=1.9">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Perbaikan menyeluruh warna teks di dalam input */
        input.form-control,
        input.form-control:focus,
        input.form-control:active {
            background-color: #0f172a !important;
            color: #ffffff !important; /* Ketikan teks wajib putih bersih */
            border: 1px solid #334155 !important;
            border-radius: 10px; 
            padding: 12px 45px 12px 40px !important; 
            transition: all 0.3s ease;
        }

        /* Perbaikan warna teks placeholder biar terlihat jelas */
        input.form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        /* Mengatasi Autofill Browser agar latar/teks tidak rusak */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover, 
        .form-control:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff !important; 
            -webkit-box-shadow: 0 0 0px 1000px #0f172a inset !important; 
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-control:focus {
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

        /* Memaksa warna link teks bawah card agar cerah dan kontras */
        .link-terang {
            color: #94a3b8 !important;
            transition: color 0.2s;
        }
        .link-terang:hover {
            color: #3b82f6 !important;
        }
    </style>
</head>
<body class="auth-wrapper login-wrapper"> 

    <div class="container d-flex justify-content-center align-items-center">
        <div class="custom-card animate__animated animate__fadeIn">
            
            <div class="text-center mb-4">
                <i class="fas fa-book-open fa-3x mb-3" style="color: #2563eb;"></i>
                <h2 class="fw-bold h3" style="color: #2563eb;">
                    Book<span class="text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Store</span>
                </h2>
                <p class="text-white-50 small m-0">Sistem Pemesanan Buku</p>
            </div>

            <form method="POST" action="">
                
                <div class="mb-3">
                    <label class="form-label small text-secondary fw-medium">Username</label>
                    <div class="position-relative">
                        <i class="fas fa-user position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small text-secondary fw-medium">Password</label>
                    <div class="position-relative">
                        <i class="fas fa-lock position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                        <input type="password" name="password" id="passInput" class="form-control" placeholder="Masukkan password" required>
                        <i class="fas fa-eye position-absolute text-secondary" id="togglePass" 
                           style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 5;"></i>
                    </div>
                </div>

                <div class="mb-4 text-end">
                    <a href="reset.php" class="link-terang small text-decoration-none">Lupa Password?</a>
                </div>

                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary py-2 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-sign-in-alt"></i> Masuk
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <p class="small text-white-50 m-0">
                    Belum punya akun? <a href="register.php" class="text-primary text-decoration-none fw-bold">Daftar di sini</a>
                </p>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const togglePass = document.querySelector('#togglePass');
        const passwordField = document.querySelector('#passInput');

        togglePass.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>