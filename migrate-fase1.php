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

$alters = [
    ['books', 'publisher', "ALTER TABLE books ADD COLUMN publisher VARCHAR(100) NULL AFTER author"],
    ['books', 'year', "ALTER TABLE books ADD COLUMN year INT NULL AFTER publisher"],
    ['books', 'pages', "ALTER TABLE books ADD COLUMN pages INT NULL AFTER year"],
    ['books', 'language', "ALTER TABLE books ADD COLUMN language VARCHAR(30) NULL DEFAULT 'Indonesia' AFTER pages"],
    ['books', 'rack', "ALTER TABLE books ADD COLUMN rack VARCHAR(20) NULL AFTER language"],
    ['borrowings', 'renew_count', "ALTER TABLE borrowings ADD COLUMN renew_count INT NOT NULL DEFAULT 0 AFTER status"],
];

foreach ($alters as [$table, $col, $sql]) {
    if (hasColumn($pdo, $table, $col)) {
        echo "EXISTS {$table}.{$col}\n";
        continue;
    }
    $pdo->exec($sql);
    echo "ADDED {$table}.{$col}\n";
}

$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL,
    `value` TEXT NOT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_key (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
echo "settings table OK\n";

$defaults = ['library_name' => 'Pageon', 'loan_days' => '7', 'fine_per_day' => '1000', 'max_loans' => '3', 'max_renew' => '1'];
$stmt = $pdo->prepare('INSERT IGNORE INTO settings (`key`, `value`) VALUES (?, ?)');
foreach ($defaults as $k => $v) {
    $stmt->execute([$k, $v]);
}
echo "settings seed OK\n";
echo "Migrate fase1 done\n";
