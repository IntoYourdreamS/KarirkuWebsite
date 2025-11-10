<?php
// auth-process.php
session_start();
require_once __DIR__ . '/../config/supabase.php';

if ($_POST['action'] == 'register') {
    // Proses registrasi
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validasi data
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header('Location: register.php');
        exit;
    }
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Format email tidak valid!";
        header('Location: register.php');
        exit;
    }
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password harus minimal 6 karakter!";
        header('Location: register.php');
        exit;
    }
    
    // Cek apakah email sudah ada
    $result = supabaseQuery('pengguna', [
        'select' => 'id_pengguna',
        'email' => 'eq.' . $email
    ]);
    
    if ($result['success'] && count($result['data']) > 0) {
        $_SESSION['error'] = "Email sudah terdaftar!";
        header('Location: register.php');
        exit;
    }
    
    // Insert user baru ke Supabase tabel 'pengguna'
    $newUser = supabaseInsert('pengguna', [
        'nama_lengkap' => $username,
        'email' => $email,
        'password' => $password,
        'dibuat_pada' => date('Y-m-d H:i:s'),
        'email_verified' => false
    ]);
    
    if ($newUser['success']) {
        // Set session login
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $username;
        $_SESSION['user_id'] = $newUser['data'][0]['id_pengguna'];
        $_SESSION['email'] = $email;
        
        // Redirect ke index.php di root folder
        header('Location: ' . dirname($_SERVER['PHP_SELF'], 2) . '/index.php');
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        header('Location: register.php');
        exit;
    }
    
} elseif ($_POST['action'] == 'login') {
    // Proses login
    $email = trim($_POST['username']); // Input dari form bernama 'username' tapi isinya email
    $password = $_POST['password'];
    
    // Validasi
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email dan password harus diisi!";
        header('Location: login.php');
        exit;
    }
    
    // Debug: Log data yang dicari
    error_log("Mencari user dengan email: " . $email);
    error_log("Password yang diinput: " . $password);
    
    // Cek user di Supabase dengan email
    $result = supabaseQuery('pengguna', [
        'select' => '*',
        'email' => 'eq.' . $email
    ]);
    
    // Debug: Log hasil query
    error_log("Query result: " . print_r($result, true));
    
    if ($result['success'] && count($result['data']) > 0) {
        $user = $result['data'][0];
        
        // Debug: Log password dari database
        error_log("Password dari database: " . $user['password']);
        
        // Bandingkan password
        if ($password === $user['password']) {
            // Password benar - Update diperbarui_pada
            supabaseUpdate('pengguna', [
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ], 'id_pengguna', $user['id_pengguna']);
            
            // Set session login
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $user['nama_lengkap'];
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect ke index.php di root folder
            header('Location: ' . dirname($_SERVER['PHP_SELF'], 2) . '/index.php');
            exit;
        } else {
            $_SESSION['error'] = "Password salah! (Email ditemukan tapi password tidak cocok)";
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
        header('Location: login.php');
        exit;
    }
}

// Jika aksi tidak dikenali
$_SESSION['error'] = "Aksi tidak valid!";
header('Location: login.php');
exit;
?>