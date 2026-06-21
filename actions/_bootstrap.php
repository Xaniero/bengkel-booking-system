<?php
// Dipanggil di awal setiap file action (supaya require & session tidak ditulis berulang).

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Booking.php';
require_once __DIR__ . '/../classes/Transaksi.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// simpan pesan notifikasi ('success' / 'error' / 'warning')
function setPesan($tipe, $pesan)
{
    $_SESSION['tipe']  = $tipe;
    $_SESSION['pesan'] = $pesan;
}

// kembali ke halaman sebelumnya
function kembali()
{
    $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.html';
    header("Location: " . $url);
    exit;
}
