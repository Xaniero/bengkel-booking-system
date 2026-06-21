<?php
// Class untuk daftar akun, login, logout, dan cek hak akses (role).

require_once __DIR__ . '/Database.php';

class User extends Database
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($nama, $email, $password, $role = 'Pemilik', $no_telp = null)
    {
        if ($this->findByEmail($email) != null) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("INSERT INTO users (nama, email, password, role, no_telp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $email, $hash, $role, $no_telp);
        return $stmt->execute();
    }

    public function login($email, $password)
    {
        $user = $this->findByEmail($email);
        if ($user == null) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id_user' => $user['id_user'],
                'nama'    => $user['nama'],
                'email'   => $user['email'],
                'role'    => $user['role']
            ];
            return true;
        }
        return false;
    }

    public function logout()
    {
        session_destroy();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public function hasRole($role)
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] == $role;
    }

    public function current()
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    public function currentId()
    {
        return isset($_SESSION['user']) ? $_SESSION['user']['id_user'] : null;
    }

    public function requireRole($role)
    {
        if (!$this->hasRole($role)) {
            header("Location: ../login.php");
            exit;
        }
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $hasil = $stmt->get_result();
        return $hasil->fetch_assoc();
    }
}
