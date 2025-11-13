<?php
session_start();
require_once __DIR__ . 'supabase.php';

if ($_POST['action'] == 'register') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header('Location: ../views/register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Format email tidak valid!";
        header('Location: ../views/register.php');
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = "Password harus minimal 6 karakter!";
        header('Location: ../views/register.php');
        exit;
    }

    $result = supabaseQuery('pengguna', [
        'select' => 'id_pengguna',
        'email' => 'eq.' . $email
    ]);
    
    if ($result['success'] && count($result['data']) > 0) {
        $_SESSION['error'] = "Email sudah terdaftar!";
        header('Location: ../views/register.php');
        exit;
    }

    $newUser = supabaseInsert('pengguna', [
        'nama_lengkap' => $username,
        'email' => $email,
        'password' => $password,
        'dibuat_pada' => date('Y-m-d H:i:s'),
        'email_verified' => false
    ]);
    
    if ($newUser['success']) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $username;
        $_SESSION['user_id'] = $newUser['data'][0]['id_pengguna'];
        $_SESSION['email'] = $email;

        header('Location: ' . dirname($_SERVER['PHP_SELF'], 2) . '/index.php');
        exit;
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        header('Location: ../views/register.php');
        exit;
    }
    
} elseif ($_POST['action'] == 'login') {
    $email = trim($_POST['username']); // Input dari form bernama 'username' tapi isinya email
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email dan password harus diisi!";
        header('Location: ../views/login.php');
        exit;
    }

    error_log("Mencari user dengan email: " . $email);
    error_log("Password yang diinput: " . $password);

    $result = supabaseQuery('pengguna', [
        'select' => '*',
        'email' => 'eq.' . $email
    ]);

    error_log("Query result: " . print_r($result, true));
    
    if ($result['success'] && count($result['data']) > 0) {
        $user = $result['data'][0];

        error_log("Password dari database: " . $user['password']);

        if ($password === $user['password']) {
            supabaseUpdate('pengguna', [
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ], 'id_pengguna', $user['id_pengguna']);

            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $user['nama_lengkap'];
            $_SESSION['user_id'] = $user['id_pengguna'];
            $_SESSION['email'] = $user['email'];

            header('Location: ' . dirname($_SERVER['PHP_SELF'], 2) . '/index.php');
            exit;
        } else {
            $_SESSION['error'] = "Password salah! (Email ditemukan tapi password tidak cocok)";
            header('Location: ../views/login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
        header('Location: ../views/login.php');
        exit;
    }
}

$_SESSION['error'] = "Aksi tidak valid!";
header('Location: ../views/login.php');
exit;
?>