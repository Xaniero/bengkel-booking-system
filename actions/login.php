<?php
// Proses login (dari form login).
require_once __DIR__ . '/_bootstrap.php';

$user = new User();

$email    = $_POST['email'];
$password = $_POST['password'];

if ($user->login($email, $password)) {
    $role = $user->current()['role'];
    if ($role == 'Pemilik') {
        header("Location: ../pemilik/dashboard.php");
    } else if ($role == 'Kepala Mekanik') {
        header("Location: ../mekanik/dashboard.php");
    } else {
        header("Location: ../kasir/dashboard.php");
    }
    exit;
} else {
    setPesan('error', 'Email atau password salah.');
    kembali();
}
