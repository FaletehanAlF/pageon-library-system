<?php

declare(strict_types=1);

/**
 * Setup / reset database for Pageon.
 *
 * Usage:
 *   php setup-db.php            — clean reset (drops & recreates)
 *   php setup-db.php --no-drop  — safe mode, creates if not exists
 */

$noDrop = in_array('--no-drop', $argv ?? [], true);

$sqlFile = __DIR__ . '/database/pageon.sql';
if (!file_exists($sqlFile)) {
    fwrite(STDERR, "SQL file not found: {$sqlFile}\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);

try {
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!$noDrop) {
        $pdo->exec('DROP DATABASE IF EXISTS pageon_db');
    }

    $pdo->exec($sql);
    echo "Database setup completed successfully!\n";

    $pdo2 = new PDO('mysql:host=localhost;dbname=pageon_db;charset=utf8mb4', 'root', '');
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Backfill book_copies from stock (fresh install has none)
    $books = $pdo2->query('SELECT id, stock FROM books')->fetchAll(PDO::FETCH_ASSOC);
    $ins = $pdo2->prepare("INSERT IGNORE INTO book_copies (book_id, barcode, `condition`, status) VALUES (?, ?, 'baik', 'available')");
    $copied = 0;
    foreach ($books as $b) {
        $c = $pdo2->prepare('SELECT COUNT(*) FROM book_copies WHERE book_id = ?');
        $c->execute([$b['id']]);
        $have = (int) $c->fetchColumn();
        $need = max(0, (int) $b['stock'] - $have);
        for ($i = 0; $i < $need; $i++) {
            $barcode = 'BK' . $b['id'] . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $ins->execute([$b['id'], $barcode]);
            $copied++;
        }
    }
    echo "Backfilled {$copied} book copies\n";

    $tables = $pdo2->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables: ' . implode(', ', $tables) . "\n";

    foreach (['users', 'books', 'categories', 'borrowings', 'book_copies', 'reservations', 'reviews', 'wishlists', 'notifications', 'announcements', 'settings'] as $t) {
        try {
            $count = $pdo2->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
            echo ucfirst($t) . ": {$count}\n";
        } catch (PDOException) {
            echo ucfirst($t) . ": (missing)\n";
        }
    }
} catch (PDOException $e) {
    fwrite(STDERR, 'Database error: ' . $e->getMessage() . "\n");
    exit(1);
}
