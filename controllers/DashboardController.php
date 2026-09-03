<?php

declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        // Pengingat jatuh tempo ≤ 2 hari (anti-duplikat, aman diabaikan bila gagal)
        ensure_due_notifications((int) Session::get('user_id', 0));
        // Pengingat tagihan denda belum lunas (anti-duplikat)
        ensure_unpaid_fine_reminder((int) Session::get('user_id', 0));
        // Auto-migrasi: lengkapi setting baru di database lama (aman, tak menimpa)
        try {
            (new Setting())->ensureDefaults();
        } catch (Throwable) {
            // abaikan
        }

        $bookModel = new Book();
        $borrowingModel = new Borrowing();
        $announcementModel = new Announcement();

        $chart = $borrowingModel->getDailyStats(14);

        // Checklist pemula (khusus user biasa, hilang otomatis jika semua centang)
        $firstSteps = [];
        $showTips = false;
        if (!isAdmin() && !Session::get('hide_tips')) {
            $uid = (int) Session::get('user_id', 0);
            $totalPinjam = $borrowingModel->count(['user_id' => $uid]);
            $history = $borrowingModel->getHistoryByUser($uid);
            $sudahKembali = count($history);
            $tepatWaktu = 0;
            foreach ($history as $h) {
                $retTs = strtotime((string) ($h['return_date'] ?? $h['due_date'])) ?: 0;
                $dueTs = strtotime((string) $h['due_date']) ?: 0;
                if ((int) floor(($retTs - $dueTs) / 86400) <= 0) {
                    $tepatWaktu++;
                }
            }
            $firstSteps = [
                ['label' => 'Pinjam buku pertama Anda', 'href' => '/books', 'cta' => 'Cari buku →', 'done' => $totalPinjam > 0],
                ['label' => 'Kembalikan buku pertama', 'href' => '/my-borrowings', 'cta' => 'Lihat pinjaman →', 'done' => $sudahKembali > 0],
                ['label' => 'Kembalikan tepat waktu (tanpa denda)', 'href' => '/riwayat', 'cta' => 'Lihat riwayat →', 'done' => $tepatWaktu > 0],
            ];
            $showTips = !($totalPinjam > 0 && $sudahKembali > 0 && $tepatWaktu > 0);
        }

        $this->viewWithLayout('dashboard/index', 'layouts/main', [
            'title' => 'Dashboard - Pageon',
            'page' => 'dashboard',
            'totalBooks' => $bookModel->getTotalBooks(),
            'totalBorrowed' => $borrowingModel->getTotalBorrowed(),
            'totalReturned' => $borrowingModel->getTotalReturned(),
            'totalOverdue' => $borrowingModel->getTotalOverdue(),
            'latestBooks' => $bookModel->getLatestBooks(5),
            'popularBooks' => $bookModel->getPopular(5),
            'overdueBorrowings' => $borrowingModel->getOverdueBorrowings(),
            'chartLabels' => $chart['labels'],
            'chartData' => $chart['data'],
            'lowStock' => $bookModel->getAllWithCategory([], 'books.stock ASC'),
            'announcements' => $announcementModel->active(3),
            'firstSteps' => $firstSteps,
            'showTips' => $showTips,
        ]);
    }

    /** Sembunyikan checklist pemula (pilihan user, tersimpan di sesi). */
    public function hideTips(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        Session::set('hide_tips', 1);
        redirect('/');
    }
}
