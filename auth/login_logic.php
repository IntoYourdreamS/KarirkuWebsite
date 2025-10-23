<?php
require_once __DIR__ . '/../config/database.php';

function loginUser($pdo, $username, $password)
{
    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE email = ? OR nama_lengkap = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        return [
            'success' => true,
            'user_id' => $user['id_pengguna'],
            'user_name' => $user['nama_lengkap'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Email/Nama atau password salah!'
        ];
    }
}
