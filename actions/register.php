<?php
// Proses daftar akun baru (dari form register).
require_once __DIR__ . '/_bootstrap.php';

$user = new User();

$nama     = $_POST['nama'];
$email    = $_POST['email'];
$password = $_POST['password'];
$no_telp  = isset($_POST['no_telp']) ? $_POST['no_telp'] : null;

if ($nama == '' || $email == '' || $password == '') {
    setPesan('error', 'Semua data wajib diisi.');
    kembali();
}
if (strlen($password) < 6) {
    setPesan('error', 'Password minimal 6 karakter.');
    kembali();
}

if ($user->register($nama, $email, $password)) {
    setPesan('success', 'Registrasi berhasil! Silakan login.');
} else {
    setPesan('error', 'Email sudah terdaftar.');
}
kembali();
