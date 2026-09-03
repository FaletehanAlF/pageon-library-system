<?php

declare(strict_types=1);

final class ReportController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $from = sanitize_string($_GET['from'] ?? date('Y-m-01'), 10);
        $to = sanitize_string($_GET['to'] ?? date('Y-m-d'), 10);
        $status = in_array($_GET['status'] ?? '', ['borrowed', 'returned', ''], true) ? ($_GET['status'] ?? '') : '';

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $from = date('Y-m-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $to = date('Y-m-d');
        }

        $db = Database::getInstance();
        $sql = "
            SELECT borrowings.*, users.name AS user_name, books.title AS book_title
            FROM borrowings
            INNER JOIN users ON users.id = borrowings.user_id
            INNER JOIN books ON books.id = borrowings.book_id
            WHERE borrowings.borrow_date BETWEEN ? AND ?
        ";
        $params = [$from, $to];
        if ($status !== '') {
            $sql .= ' AND borrowings.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY borrowings.borrow_date DESC, borrowings.id DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $finePerDay = setting_int('fine_per_day', 1000);
        $totalFine = 0;
        foreach ($rows as &$r) {
            $r['fine'] = $r['status'] === 'borrowed' ? calc_fine((string) $r['due_date'], $finePerDay) : 0;
            $totalFine += (int) $r['fine'];
        }
        unset($r);

        // Export CSV
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="laporan-' . $from . '-' . $to . '.csv"');
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Tanggal Pinjam', 'Jatuh Tempo', 'Kembali', 'Peminjam', 'Buku', 'Status', 'Denda']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['id'], $r['borrow_date'], $r['due_date'], $r['return_date'] ?? '-',
                    $r['user_name'], $r['book_title'], $r['status'], $r['fine'],
                ]);
            }
            fclose($out);
            exit;
        }

        $this->viewWithLayout('reports/index', 'layouts/main', [
            'title' => 'Laporan - Pageon',
            'page' => 'reports',
            'rows' => $rows,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'totalFine' => $totalFine,
        ]);
    }
}
