-- Database: bengkel_queue (Sistem Booking Antrean Servis)
-- Import file ini lewat phpMyAdmin (menu Import) atau: mysql -u root < schema.sql

CREATE DATABASE IF NOT EXISTS bengkel_queue;
USE bengkel_queue;

-- hapus tabel lama dulu kalau sudah ada
DROP TABLE IF EXISTS transaksi_pembayaran;
DROP TABLE IF EXISTS booking_antrean;
DROP TABLE IF EXISTS mekanik;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------------------
-- 1. users — akun untuk autentikasi 3 role
-- ---------------------------------------------------------------------
CREATE TABLE users (
    id_user   INT          NOT NULL AUTO_INCREMENT,
    nama      VARCHAR(100) NOT NULL,
    email     VARCHAR(150) NOT NULL,
    password  VARCHAR(255) NOT NULL,                       -- disimpan ter-hash (password_hash)
    role      ENUM('Pemilik', 'Kepala Mekanik', 'Kasir') NOT NULL DEFAULT 'Pemilik',
    no_telp   VARCHAR(20)  NULL,
    created_at TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. mekanik — daftar montir yang tersedia di bengkel
-- ---------------------------------------------------------------------
CREATE TABLE mekanik (
    id_mekanik          INT          NOT NULL AUTO_INCREMENT,
    nama_mekanik        VARCHAR(100) NOT NULL,
    status_ketersediaan ENUM('Tersedia', 'Sibuk') NOT NULL DEFAULT 'Tersedia',
    PRIMARY KEY (id_mekanik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. booking_antrean — inti pelacakan alur antrean (State Machine)
--    status: Antrean Menunggu -> Sedang Dikerjakan -> Selesai
-- ---------------------------------------------------------------------
CREATE TABLE booking_antrean (
    id_booking  INT          NOT NULL AUTO_INCREMENT,
    id_user     INT          NOT NULL,                     -- FK pemilik kendaraan
    id_mekanik  INT          NULL,                         -- FK, diisi saat di-assign
    nomor_plat  VARCHAR(20)  NOT NULL,
    jenis_motor VARCHAR(100) NOT NULL,
    keluhan     TEXT         NOT NULL,
    tanggal_jam DATETIME     NOT NULL,
    status      ENUM('Antrean Menunggu', 'Sedang Dikerjakan', 'Selesai')
                             NOT NULL DEFAULT 'Antrean Menunggu',
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_booking),
    KEY idx_status (status),
    CONSTRAINT fk_booking_user
        FOREIGN KEY (id_user)   REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_booking_mekanik
        FOREIGN KEY (id_mekanik) REFERENCES mekanik(id_mekanik)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. transaksi_pembayaran — nota keuangan (1 booking : 1 transaksi)
-- ---------------------------------------------------------------------
CREATE TABLE transaksi_pembayaran (
    id_transaksi     INT           NOT NULL AUTO_INCREMENT,
    id_booking       INT           NOT NULL,
    total_biaya      DECIMAL(12,2) NOT NULL,
    waktu_pembayaran TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_transaksi),
    UNIQUE KEY uq_transaksi_booking (id_booking),          -- jaminan relasi 1:1
    CONSTRAINT fk_transaksi_booking
        FOREIGN KEY (id_booking) REFERENCES booking_antrean(id_booking)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  DATA AWAL (SEED) — akun demo & mekanik
--  Password semua akun demo: "password123"
--  (hash di bawah dihasilkan oleh password_hash('password123', PASSWORD_DEFAULT))
-- =====================================================================
INSERT INTO users (nama, email, password, role, no_telp) VALUES
('Budi Pemilik',  'pemilik@mail.com', '$2y$10$TuAQ6y9cd5VvktWDRxNzReOCiCFBbRPquWXSUL5m9xGwrG6yxg792', 'Pemilik',        '081200000001'),
('Andi Kepala',   'kepala@mail.com',  '$2y$10$TuAQ6y9cd5VvktWDRxNzReOCiCFBbRPquWXSUL5m9xGwrG6yxg792', 'Kepala Mekanik', '081200000002'),
('Sari Kasir',    'kasir@mail.com',   '$2y$10$TuAQ6y9cd5VvktWDRxNzReOCiCFBbRPquWXSUL5m9xGwrG6yxg792', 'Kasir',          '081200000003');

INSERT INTO mekanik (nama_mekanik, status_ketersediaan) VALUES
('Joko Susilo',   'Tersedia'),
('Rian Hidayat',  'Tersedia'),
('Dewi Lestari',  'Tersedia');

-- Hash di atas valid untuk password "password123".
-- Jika ingin meng-generate hash baru: php -r "echo password_hash('xxx', PASSWORD_DEFAULT);"
