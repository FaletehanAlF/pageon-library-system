<?php
declare(strict_types=1);
define('BASE_PATH', __DIR__);
require BASE_PATH . '/core/Database.php';
$pdo = Database::getInstance();
$pdo->exec('USE pageon_db');

function hasColumn(PDO $pdo, string $table, string $col): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?');
    $stmt->execute([$table, $col]);
    return (int) $stmt->fetchColumn() > 0;
}
function hasTable(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

// users.status
if (!hasColumn($pdo, 'users', 'status')) {
    $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('active','suspended') NOT NULL DEFAULT 'active' AFTER role");
    echo "ADDED users.status\n";
} else {
    echo "EXISTS users.status\n";
}

// borrowings.copy_id
if (!hasColumn($pdo, 'borrowings', 'copy_id')) {
    $pdo->exec('ALTER TABLE borrowings ADD COLUMN copy_id INT NULL AFTER book_id');
    echo "ADDED borrowings.copy_id\n";
} else {
    echo "EXISTS borrowings.copy_id\n";
}

// book_copies
if (!hasTable($pdo, 'book_copies')) {
    $pdo->exec("CREATE TABLE book_copies (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "CREATED book_copies\n";
} else {
    echo "EXISTS book_copies\n";
}

// reservations
if (!hasTable($pdo, 'reservations')) {
    $pdo->exec("CREATE TABLE reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        book_id INT NOT NULL,
        status ENUM('waiting','ready','cancelled','fulfilled') NOT NULL DEFAULT 'waiting',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_res_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_res_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
        INDEX idx_res_book (book_id, status),
        INDEX idx_res_user (user_id, status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "CREATED reservations\n";
} else {
    echo "EXISTS reservations\n";
}

// reviews
if (!hasTable($pdo, 'reviews')) {
    $pdo->exec("CREATE TABLE reviews (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "CREATED reviews\n";
} else {
    echo "EXISTS reviews\n";
}

// wishlists
if (!hasTable($pdo, 'wishlists')) {
    $pdo->exec("CREATE TABLE wishlists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        book_id INT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_wish_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_wish_book FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE ON UPDATE CASCADE,
        UNIQUE KEY uq_wish_user_book (user_id, book_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "CREATED wishlists\n";
} else {
    echo "EXISTS wishlists\n";
}

// notifications
if (!hasTable($pdo, 'notifications')) {
    $pdo->exec("CREATE TABLE notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(150) NOT NULL,
        message TEXT NOT NULL,
        link VARCHAR(255) NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        INDEX idx_notif_user (user_id, is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "CREATED notifications\n";
} else {
    echo "EXISTS notifications\n";
}

// announcements
if (!hasTable($pdo, 'announcements')) {
    $pdo->exec("CREATE TABLE announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(150) NOT NULL,
        message TEXT NOT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_by INT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_ann_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "CREATED announcements\n";
} else {
    echo "EXISTS announcements\n";
}

// Backfill copies for existing books without copies
$stmt = $pdo->query('SELECT id, stock FROM books');
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $b) {
    $c = $pdo->prepare('SELECT COUNT(*) FROM book_copies WHERE book_id = ?');
    $c->execute([$b['id']]);
    $have = (int) $c->fetchColumn();
    $need = max(0, (int) $b['stock'] - $have);
    $ins = $pdo->prepare("INSERT INTO book_copies (book_id, barcode, `condition`, status) VALUES (?, ?, 'baik', 'available')");
    for ($i = 0; $i < $need; $i++) {
        $barcode = 'BK' . $b['id'] . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        try {
            $ins->execute([$b['id'], $barcode]);
        } catch (PDOException) {
            // retry once on collision
            $barcode = 'BK' . $b['id'] . '-' . time() . '-' . strtoupper(bin2hex(random_bytes(4)));
            $ins->execute([$b['id'], $barcode]);
        }
    }
    if ($need > 0) {
        echo "Backfilled {$need} copies for book {$b['id']}\n";
    }
}

echo "Migrate fase2 done\n";
