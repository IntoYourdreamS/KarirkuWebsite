<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../auth/login_logic.php';

class LoginTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("
            CREATE TABLE pengguna (
                id_pengguna INTEGER PRIMARY KEY AUTOINCREMENT,
                nama_lengkap TEXT,
                email TEXT,
                no_hp TEXT,
                password_hash TEXT,
                role TEXT
            )
        ");

        $hashed = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO pengguna (nama_lengkap, email, no_hp, password_hash, role)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(['Test User', 'test@example.com', '0812345678', $hashed, 'pencaker']);
    }

    public function testLoginSuccess(): void
    {
        $result = loginUser($this->pdo, 'test@example.com', 'password123');
        $this->assertTrue($result['success']);
        $this->assertSame('Test User', $result['user_name']);
    }

    public function testLoginFailWrongPassword(): void
    {
        $result = loginUser($this->pdo, 'test@example.com', 'wrongpass');
        $this->assertFalse($result['success']);
        $this->assertSame('Email/Nama atau password salah!', $result['error']);
    }

    public function testLoginFailUserNotFound(): void
    {
        $result = loginUser($this->pdo, 'notfound@example.com', 'password123');
        $this->assertFalse($result['success']);
    }
}
