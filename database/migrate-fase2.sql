-- Fase 2 migration notes (run via migrate-fase2.php)
USE pageon_db;

-- users: status
-- ALTER TABLE users ADD COLUMN status ENUM('active','suspended') NOT NULL DEFAULT 'active' AFTER role;

-- borrowings: copy link
-- ALTER TABLE borrowings ADD COLUMN copy_id INT NULL AFTER book_id;

-- book_copies
-- CREATE TABLE book_copies (...);

-- reservations, reviews, wishlists, notifications, announcements
-- See migrate-fase2.php for idempotent DDL.
