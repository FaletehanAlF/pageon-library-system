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
    // Connect without selecting DB first
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!$noDrop) {
        $pdo->exec('DROP DATABASE IF EXISTS pageon_db');
    }

    $pdo->exec($sql);
    echo "Database setup completed successfully!\n";

    $pdo2 = new PDO('mysql:host=localhost;dbname=pageon_db;charset=utf8mb4', 'root', '');
    $tables = $pdo2->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo 'Tables: ' . implode(', ', $tables) . "\n";

    foreach (['users', 'books', 'categories', 'borrowings'] as $t) {
        $count = $pdo2->query("SELECT COUNT(*) FROM `{$t}`")->fetchColumn();
        echo ucfirst($t) . ": {$count}\n";
    }
} catch (PDOException $e) {
    fwrite(STDERR, 'Database error: ' . $e->getMessage() . "\n");
    exit(1);
}
