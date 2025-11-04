<?php
session_start();
require __DIR__ . '/../config/supabase.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi!';
    } else {
        // Cari user by email
        $result = supabaseQuery($client, 'pengguna', [
            'select' => '*',
            'email' => 'eq.' . $email
        ]);

        if ($result['success'] && !empty($result['data'])) {
            $user = $result['data'][0];

            // Verifikasi password PLAIN TEXT
            if ($password === $user['password_hash']) {
                // Set session
                $_SESSION['user_id'] = $user['id_pengguna'];
                $_SESSION['user_name'] = $user['nama_lengkap'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                // Redirect ke halaman utama
                header('Location: ../index.php');
                exit;
            } else {
                $error = 'Password salah!';

                // DEBUG: Tampilkan info password jika needed
                if (isset($_GET['debug'])) {
                    echo "<!-- DEBUG: Password input: " . $password . " -->";
                    echo "<!-- DEBUG: Password in DB: " . $user['password_hash'] . " -->";
                }
            }
        } else {
            $error = 'Email tidak ditemukan!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login - KaririKu</title>
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
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-container">
                    <img src="../assets/img/logo.png" alt="KaririKu Logo">
                </div>
                <h2>Masuk ke Akun</h2>
                <p>Selamat datang kembali di KaririKu</p>
            </div>

            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Masukkan email Anda"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Masukkan password Anda" required>
                    </div>

                    <div style="text-align: right; margin-bottom: 20px;">
                        <a href="forgot-password.php" style="color: #001f66; text-decoration: none; font-size: 14px;">
                            <i class="fas fa-question-circle me-1"></i>Lupa Password?
                        </a>
                    </div>

                    <button type="submit" class="btn-auth">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk
                    </button>
                </form>

                <div class="divider">
                    <span>Atau masuk dengan</span>
                </div>

                <button class="btn-google" type="button">
                    <i class="fab fa-google"></i>
                    Google
                </button>

                <div class="auth-footer">
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>