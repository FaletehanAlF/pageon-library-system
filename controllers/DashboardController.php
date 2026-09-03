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
        ]);
    }
}
