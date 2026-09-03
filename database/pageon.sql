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
    status     ENUM('active','suspended') NOT NULL DEFAULT 'active',
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
    copy_id     INT  NULL,
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

-- ── Book copies ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS book_copies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    barcode VARCHAR(30) NOT NULL,
    `condition` ENUM('baik','rusak','hilang') NOT NULL DEFAULT 'baik',
    status ENUM('available','borrowed','lost') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_copies_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_copies_barcode (barcode),
    INDEX idx_copies_book (book_id),
    INDEX idx_copies_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Reservations ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    status ENUM('waiting','ready','cancelled','fulfilled') NOT NULL DEFAULT 'waiting',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_res_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_res_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_res_book (book_id, status),
    INDEX idx_res_user (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Reviews ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rev_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_rev_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_rev_user_book (user_id, book_id),
    INDEX idx_rev_book (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Wishlists ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wish_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_wish_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_wish_user_book (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Notifications ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_notif_user (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Announcements ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_by INT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ann_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
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
