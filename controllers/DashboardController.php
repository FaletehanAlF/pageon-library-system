<?php

declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $bookModel = new Book();
        $borrowingModel = new Borrowing();

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
        ]);
    }
}
