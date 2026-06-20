<?php
// Class untuk koneksi ke database. Class lain mewarisi (extends) class ini.

class Database
{
    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_error) {
            die("Koneksi database gagal: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8mb4");
    }
}
