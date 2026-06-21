<?php
// Proses logout (keluar).
require_once __DIR__ . '/_bootstrap.php';

$user = new User();
$user->logout();

header("Location: ../login.php");
exit;
