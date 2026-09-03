-- Fase 1 migration — dijalankan via php migrate-fase1.php (kompatibel MySQL lama)
-- Jangan jalankan langsung via mysql client karena ADD COLUMN IF NOT EXISTS
-- tidak didukung MySQL < 8.0. Lihat migrate-fase1.php untuk logika idempotent.
USE pageon_db;

ALTER TABLE books ADD COLUMN publisher VARCHAR(100) NULL AFTER author;
ALTER TABLE books ADD COLUMN year INT NULL AFTER publisher;
ALTER TABLE books ADD COLUMN pages INT NULL AFTER year;
ALTER TABLE books ADD COLUMN language VARCHAR(30) NULL DEFAULT 'Indonesia' AFTER pages;
ALTER TABLE books ADD COLUMN rack VARCHAR(20) NULL AFTER language;

ALTER TABLE borrowings ADD COLUMN renew_count INT NOT NULL DEFAULT 0 AFTER status;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL,
    `value` TEXT NOT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO settings (`key`, `value`) VALUES
('library_name', 'Pageon'),
('loan_days', '7'),
('fine_per_day', '1000'),
('max_loans', '3'),
('max_renew', '1');
