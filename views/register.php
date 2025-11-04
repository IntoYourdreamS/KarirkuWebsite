<?php
session_start();
require __DIR__ . '/../config/supabase.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';

    // Validasi
    if (empty($nama_lengkap) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field wajib diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek apakah email sudah terdaftar
        $checkEmail = supabaseQuery($client, 'pengguna', [
            'select' => 'email',
            'email' => 'eq.' . $email
        ]);

        if ($checkEmail['success'] && !empty($checkEmail['data'])) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Data untuk insert - SESUAI STRUCTURE DATABASE
            $userData = [
                'nama_lengkap' => $nama_lengkap,
                'email' => $email,
                'password_hash' => $password_hash,
                'no_hp' => $no_hp,
                'role' => 'user' // default role
            ];

            $result = supabaseInsert($client, 'pengguna', $userData);

            if ($result['success']) {
                $success = 'Registrasi berhasil! Silakan login.';
                // Reset form
                $_POST = [];
            } else {
                $error = 'Terjadi kesalahan saat registrasi: ' . ($result['error'] ?? 'Unknown error');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Daftar - KaririKu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="../assets/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Inter:wght@700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/auth.css" rel="stylesheet">

    <style>
        
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-container">
                    <img src="../assets/img/logo.png" alt="KaririKu Logo">
                </div>
                <h2>Buat Akun Baru</h2>
                <p>Bergabung dengan KaririKu hari ini</p>
            </div>

            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                            placeholder="Masukkan nama lengkap Anda"
                            value="<?= htmlspecialchars($_POST['nama_lengkap'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Masukkan email Anda"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="no_hp">Nomor Handphone</label>
                        <input type="tel" class="form-control" id="no_hp" name="no_hp"
                            placeholder="Masukkan nomor handphone"
                            value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Buat password minimal 6 karakter" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            placeholder="Ulangi password Anda" required>
                    </div>

                    <div class="form-group">
                        <label class="form-check" style="display: flex; align-items: flex-start; gap: 10px;">
                            <input type="checkbox" required style="margin-top: 3px;">
                            <span style="font-size: 14px; color: #666;">
                                Saya menyetujui <a href="#" style="color: #001f66;">Syarat & Ketentuan</a> dan <a href="#" style="color: #001f66;">Kebijakan Privasi</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn-auth">
                        <i class="fas fa-user-plus me-2"></i>Daftar
                    </button>
                </form>

                <div class="divider">
                    <span>Atau daftar dengan</span>
                </div>

                <button class="btn-google" type="button">
                    <i class="fab fa-google"></i>
                    Google
                </button>

                <div class="auth-footer">
                    <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Validasi konfirmasi password
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Password tidak cocok');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            password.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
        });
    </script>
</body>

</html>