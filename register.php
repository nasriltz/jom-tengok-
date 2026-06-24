<?php
include 'koneksi.php';

// Proses register ditaruh paling atas agar tidak ada kebocoran output HTML sebelum redirect
if (isset($_POST['register'])) {
    $user = htmlspecialchars($_POST['username']);
    $raw_pass = $_POST['password']; 

    // Validasi: Username minimal 5, Password minimal 8
    if (strlen($user) < 5) {
        echo "<script>alert('Gagal! Username minimal harus 5 karakter.'); window.history.back();</script>";
        exit;
    } elseif (strlen($raw_pass) < 8) {
        echo "<script>alert('Gagal! Password minimal harus 8 karakter.'); window.history.back();</script>";
        exit;
    } else {
        $pass = password_hash($raw_pass, PASSWORD_DEFAULT);
        $role = 'client'; 

        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $pass, $role);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registrasi sukses! Silakan login.'); window.location='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal! Username mungkin sudah terdaftar.');</script>";
        }
        $stmt->close();
    }
}

// Panggil header setelah proses logika selesai
include 'header.php'; 
?>

<style>
    /* 1. SAMAIN PERSIS HACK AUTOFILL DARI FILE LOGIN KAMU */
    .form-control:-webkit-autofill,
    .form-control:-webkit-autofill:hover, 
    .form-control:-webkit-autofill:focus {
        -webkit-text-fill-color: #ffffff !important; 
        -webkit-box-shadow: 0 0 0px 1000px #0f172a inset !important; 
        transition: background-color 5000s ease-in-out 0s;
    }

    /* 2. STYLE BASE FORM CONTROL DI SINI */
    input.form-control,
    input.form-control:focus,
    input.form-control:active {
        background-color: #0f172a !important;
        color: #ffffff !important; /* Ketikan teks wajib putih bersih */
        border: 1px solid #334155 !important;
        border-radius: 10px; 
        padding: 12px 45px 12px 40px !important; /* Spacing kanan ditambah untuk icon mata */
        transition: all 0.3s ease;
    }

    /* Perbaikan warna teks placeholder biar terlihat jelas */
    input.form-control::placeholder {
        color: #94a3b8 !important;
        opacity: 1 !important;
    }

    /* 3. STYLE PAS DIKLIK / FOCUS */
    input.form-control:focus {
        border-color: #3b82f6 !important; 
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25) !important;
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
</style>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="custom-card">
        
        <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-3x mb-3" style="color: #2563eb;"></i>
            <h2 class="fw-bold h3 text-white">
                Daftar <span class="text-primary">Akun</span>
            </h2>
            <p class="text-white-50 small">Bergabung dengan UT Bookstore</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small text-secondary fw-medium">Username (Min 5 Karakter)</label>
                <div class="position-relative">
                    <i class="fas fa-user position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                    <input type="text" name="username" class="form-control" placeholder="Buat username" minlength="5" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small text-secondary fw-medium">Password (Min 8 Karakter)</label>
                <div class="position-relative">
                    <i class="fas fa-lock position-absolute text-secondary" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 5;"></i>
                    <input type="password" name="password" id="passInput" class="form-control" placeholder="Buat password" minlength="8" required>
                    <i class="fas fa-eye position-absolute text-secondary" id="togglePass" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 5;"></i>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" name="register" class="btn btn-primary py-2.5 fw-bold rounded-3 shadow-sm">
                    Daftar Sekarang
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="small text-white-50 m-0">
                Sudah punya akun? <a href="login.php" class="text-primary text-decoration-none fw-bold ms-1">Login</a>
            </p>
        </div>
        
    </div>
</div>

<script>
    // Fitur intip password tetap aman
    const togglePass = document.querySelector('#togglePass');
    const passwordField = document.querySelector('#passInput');

    togglePass.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
</script>

<?php
include 'footer.php'; 
?>