<?php

declare(strict_types=1);

final class BorrowingController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $borrowingModel = new Borrowing();
        $finePerDay = setting_int('fine_per_day', 1000);

        $borrowings = isAdmin()
            ? $borrowingModel->getAllWithDetails()
            : $borrowingModel->getUserBorrowings((int) Session::get('user_id'));

        // Enrich with fine + renew info
        $maxRenew = setting_int('max_renew', 1);
        foreach ($borrowings as &$b) {
            $b['fine'] = $b['status'] === 'borrowed' ? calc_fine((string) $b['due_date'], $finePerDay) : 0;
            $b['days_overdue'] = $b['status'] === 'borrowed' ? days_overdue((string) $b['due_date']) : 0;
            $b['can_renew'] = $b['status'] === 'borrowed'
                && (int) ($b['renew_count'] ?? 0) < $maxRenew
                && days_overdue((string) $b['due_date']) === 0;
        }
        unset($b);

        $this->viewWithLayout('borrowings/index', 'layouts/main', [
            'title' => isAdmin() ? 'Kelola Peminjaman - Pageon' : 'Peminjaman Saya - Pageon',
            'page' => 'borrowings',
            'borrowings' => $borrowings,
            'finePerDay' => $finePerDay,
            'maxLoans' => setting_int('max_loans', 3),
        ]);
    }

    public function myBorrowings(): void
    {
        $this->requireAuth();

        $borrowingModel = new Borrowing();
        $finePerDay = setting_int('fine_per_day', 1000);
        $maxRenew = setting_int('max_renew', 1);
        $borrowings = $borrowingModel->getUserBorrowings((int) Session::get('user_id'));

        foreach ($borrowings as &$b) {
            $b['fine'] = $b['status'] === 'borrowed' ? calc_fine((string) $b['due_date'], $finePerDay) : 0;
            $b['days_overdue'] = $b['status'] === 'borrowed' ? days_overdue((string) $b['due_date']) : 0;
            $b['can_renew'] = $b['status'] === 'borrowed'
                && (int) ($b['renew_count'] ?? 0) < $maxRenew
                && days_overdue((string) $b['due_date']) === 0;
        }
        unset($b);

        $this->viewWithLayout('borrowings/index', 'layouts/main', [
            'title' => 'Peminjaman Saya - Pageon',
            'page' => 'my-borrowings',
            'borrowings' => $borrowings,
            'finePerDay' => $finePerDay,
            'maxLoans' => setting_int('max_loans', 3),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $bookId = filter_var($this->input('book_id', 0), FILTER_VALIDATE_INT);

        if ($bookId === false || $bookId <= 0) {
            Session::flash('error', 'Buku tidak valid.');
            redirect('/books');
        }

        $bookModel = new Book();
        $book = $bookModel->find($bookId);

        if ($book === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }

        if ((int) $book['stock'] <= 0) {
            Session::flash('error', 'Buku tidak tersedia untuk dipinjam.');
            redirect("/books/{$bookId}");
        }

        $userId = (int) Session::get('user_id');
        $borrowingModel = new Borrowing();
        $maxLoans = setting_int('max_loans', 3);
        $loanDays = max(1, setting_int('loan_days', 7));

        if ($borrowingModel->countActiveByUser($userId) >= $maxLoans) {
            Session::flash('error', "Batas maksimal {$maxLoans} buku aktif. Kembalikan dulu sebelum meminjam lagi.");
            redirect("/books/{$bookId}");
        }

        if ($borrowingModel->countOverdueByUser($userId) > 0) {
            Session::flash('error', 'Anda memiliki peminjaman terlambat. Kembalikan dulu sebelum meminjam lagi.');
            redirect('/my-borrowings');
        }

        if ($borrowingModel->hasActiveBorrowing($userId, $bookId)) {
            Session::flash('error', 'Anda sudah meminjam buku ini dan belum mengembalikannya.');
            redirect("/books/{$bookId}");
        }

        // Reservation priority: if queue exists and user is not first, block
        $resModel = new Reservation();
        $first = $resModel->firstWaiting($bookId);
        if ($first !== null && (int) $first['user_id'] !== $userId) {
            Session::flash('error', 'Buku sedang dalam antrean reservasi. Silakan reservasi dulu.');
            redirect("/books/{$bookId}");
        }

        try {
            $this->db->beginTransaction();

            $borrowDate = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime("+{$loanDays} days"));

            $copyModel = new BookCopy();
            $copy = $copyModel->takeAvailable($bookId);
            $copyId = $copy['id'] ?? null;
            if ($copyId === null && (int) $book['stock'] <= 0) {
                throw new RuntimeException('Stok habis saat transaksi.');
            }

            $borrowingModel->create([
                'user_id' => $userId,
                'book_id' => $bookId,
                'copy_id' => $copyId,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'status' => 'borrowed',
                'renew_count' => 0,
            ]);

            if ($copyId !== null) {
                $copyModel->markBorrowed((int) $copyId);
            }
            $decremented = $bookModel->decrementStock($bookId);
            if (!$decremented) {
                throw new RuntimeException('Stok habis saat transaksi.');
            }

            // If user had reservation, mark fulfilled
            if ($first !== null && (int) $first['user_id'] === $userId) {
                $resModel->update((int) $first['id'], ['status' => 'fulfilled']);
            }

            $this->db->commit();

            Session::flash('success', 'Buku berhasil dipinjam. Harap kembalikan sebelum ' . format_date($dueDate) . '.');
            redirect('/my-borrowings');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Borrowing] Store failed: ' . $e->getMessage());
            Session::flash('error', $e instanceof RuntimeException ? $e->getMessage() : 'Gagal meminjam buku. Silakan coba lagi.');
            redirect("/books/{$bookId}");
        }
    }

    public function renew(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $borrowingModel = new Borrowing();
        $borrowing = $borrowingModel->find((int) $id);
        if ($borrowing === null) {
            Session::flash('error', 'Data peminjaman tidak ditemukan.');
            redirect('/my-borrowings');
        }

        $uid = (int) Session::get('user_id');
        if (!isAdmin() && (int) $borrowing['user_id'] !== $uid) {
            abort(403);
        }
        if ($borrowing['status'] !== 'borrowed') {
            Session::flash('error', 'Hanya peminjaman aktif yang bisa diperpanjang.');
            redirect('/my-borrowings');
        }
        if (days_overdue((string) $borrowing['due_date']) > 0) {
            Session::flash('error', 'Peminjaman terlambat tidak bisa diperpanjang. Kembalikan dulu.');
            redirect('/my-borrowings');
        }

        $maxRenew = setting_int('max_renew', 1);
        if ((int) ($borrowing['renew_count'] ?? 0) >= $maxRenew) {
            Session::flash('error', "Maksimal perpanjangan {$maxRenew}x.");
            redirect('/my-borrowings');
        }

        $loanDays = max(1, setting_int('loan_days', 7));
        $newDue = date('Y-m-d', strtotime((string) $borrowing['due_date'] . " +{$loanDays} days"));

        if ($borrowingModel->renew((int) $id, $newDue)) {
            Session::flash('success', 'Diperpanjang sampai ' . format_date($newDue) . '.');
        } else {
            Session::flash('error', 'Gagal memperpanjang.');
        }
        redirect('/my-borrowings');
    }

    public function receipt(string $id): void
    {
        $this->requireAuth();
        if (!ctype_digit($id)) {
            abort(404);
        }

        $borrowingModel = new Borrowing();
        $rows = $borrowingModel->getAllWithDetails(['borrowings.id' => (int) $id]);
        $b = $rows[0] ?? null;
        if ($b === null) {
            Session::flash('error', 'Data peminjaman tidak ditemukan.');
            redirect('/my-borrowings');
        }
        if (!isAdmin() && (int) $b['user_id'] !== (int) Session::get('user_id')) {
            abort(403);
        }

        $b['fine'] = $b['status'] === 'borrowed' ? calc_fine((string) $b['due_date']) : 0;

        $this->viewWithLayout('borrowings/receipt', 'layouts/print', [
            'title' => 'Struk #' . $b['id'] . ' - Pageon',
            'page' => 'my-borrowings',
            'b' => $b,
        ]);
    }

    public function returnBook(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $borrowingId = (int) $id;
        $borrowingModel = new Borrowing();
        $borrowing = $borrowingModel->find($borrowingId);

        if ($borrowing === null) {
            Session::flash('error', 'Data peminjaman tidak ditemukan.');
            redirect(isAdmin() ? '/borrowings' : '/my-borrowings');
        }

        $currentUserId = (int) Session::get('user_id');
        if (!isAdmin() && (int) $borrowing['user_id'] !== $currentUserId) {
            abort(403);
        }

        if ($borrowing['status'] !== 'borrowed') {
            Session::flash('error', 'Buku ini sudah dikembalikan.');
            redirect(isAdmin() ? '/borrowings' : '/my-borrowings');
        }

        $fine = calc_fine((string) $borrowing['due_date']);

        try {
            $this->db->beginTransaction();

            $returned = $borrowingModel->returnBook($borrowingId);
            if (!$returned) {
                throw new RuntimeException('Gagal mengembalikan buku.');
            }

            $bookModel = new Book();
            $bookModel->incrementStock((int) $borrowing['book_id']);

            if (!empty($borrowing['copy_id'])) {
                $copyModel = new BookCopy();
                $copyModel->markAvailable((int) $borrowing['copy_id']);
            }

            // Promote next reservation to ready + notify
            $resModel = new Reservation();
            $next = $resModel->firstWaiting((int) $borrowing['book_id']);
            if ($next !== null) {
                $resModel->update((int) $next['id'], ['status' => 'ready']);
                $notif = new Notification();
                $bookTitle = $bookModel->find((int) $borrowing['book_id'])['title'] ?? 'Buku';
                $notif->notify((int) $next['user_id'], 'Reservasi siap', "Buku \"{$bookTitle}\" sudah tersedia. Segera pinjam.", url('/books/' . $borrowing['book_id']));
            }

            $this->db->commit();

            if ($fine > 0) {
                Session::flash('success', 'Buku dikembalikan dengan denda ' . format_rupiah($fine) . '.');
            } else {
                Session::flash('success', 'Buku berhasil dikembalikan.');
            }
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("[Borrowing] Return {$id} failed: " . $e->getMessage());
            Session::flash('error', $e instanceof RuntimeException ? $e->getMessage() : 'Gagal mengembalikan buku.');
        }

        $redirect = isAdmin() ? '/borrowings' : '/my-borrowings';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer !== '' && str_contains($referer, '/my-borrowings')) {
            $redirect = '/my-borrowings';
        } elseif ($referer !== '' && str_contains($referer, '/borrowings')) {
            $redirect = '/borrowings';
        }

        redirect($redirect);
    }
}
