-- Pageon — Library Management System
-- Run:  php setup-db.php   or   mysql -u root pageon_db < database/pageon.sql

CREATE DATABASE IF NOT EXISTS pageon_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pageon_db;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Users ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Categories ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Books ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS books (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
    author      VARCHAR(100) NOT NULL,
    publisher   VARCHAR(100) NULL,
    year        INT          NULL,
    pages       INT          NULL,
    language    VARCHAR(30)  NULL DEFAULT 'Indonesia',
    rack        VARCHAR(20)  NULL,
    isbn        VARCHAR(20)  NULL,
    category_id INT          NULL,
    stock       INT          NOT NULL DEFAULT 1 CHECK (stock >= 0),
    description TEXT         NULL,
    cover       VARCHAR(255) NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_books_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_books_category (category_id),
    INDEX idx_books_title (title),
    INDEX idx_books_author (author)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Borrowings ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS borrowings (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT  NOT NULL,
    book_id     INT  NOT NULL,
    borrow_date DATE NOT NULL,
    due_date    DATE NOT NULL,
    return_date DATE NULL,
    status      ENUM('borrowed', 'returned', 'overdue') NOT NULL DEFAULT 'borrowed',
    renew_count INT  NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_borrowings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_borrowings_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_borrowings_user (user_id),
    INDEX idx_borrowings_book (book_id),
    INDEX idx_borrowings_status (status),
    INDEX idx_borrowings_due (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Settings ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL,
    `value` TEXT NOT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES
('library_name', 'Pageon'),
('loan_days', '7'),
('fine_per_day', '1000'),
('max_loans', '3'),
('max_renew', '1')
ON DUPLICATE KEY UPDATE `value` = `value`;

SET FOREIGN_KEY_CHECKS = 1;

-- ── Seed: admin (password: "password") ─────────────────────
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@pageon.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ── Seed: categories ────────────────────────────────────────
INSERT INTO categories (name) VALUES
('Fiksi'), ('Non-Fiksi'), ('Sains'), ('Teknologi'), ('Sejarah'), ('Novel'), ('Pendidikan'), ('Agama')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ── Seed: books ─────────────────────────────────────────────
INSERT INTO books (id, title, author, isbn, category_id, stock, description) VALUES
(1, 'Laskar Pelangi',   'Andrea Hirata',      '978-602-0013-18-7', 6, 5, 'Novel tentang perjuangan anak-anak di Belitong.'),
(2, 'Bumi',             'Tere Liye',          '978-602-0803-02-1', 6, 3, 'Petualangan Raib di dunia paralel.'),
(3, 'Sapiens',          'Yuval Noah Harari',  '978-0-06-231609-7', 2, 4, 'Sejarah singkat umat manusia.'),
(4, 'Clean Code',       'Robert C. Martin',   '978-0-13-235088-4', 4, 2, 'Panduan menulis kode yang bersih.'),
(5, 'Tenggelamnya Kapal Van der Wijck', 'Hamka', '978-602-0803-10-6', 1, 3, 'Novel klasik Indonesia.')
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    author = VALUES(author),
    stock = VALUES(stock);
