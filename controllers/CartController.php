<?php

declare(strict_types=1);

final class CartController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $bookModel = new Book();
        $items = [];
        foreach (cart_items() as $bookId) {
            $book = $bookModel->getWithCategory($bookId);
            if ($book !== null) {
                $items[] = $book;
            }
        }

        $this->viewWithLayout('cart/index', 'layouts/main', [
            'title' => 'Keranjang Pinjam - Pageon',
            'page' => 'cart',
            'items' => $items,
            'maxLoans' => setting_int('max_loans', 3),
            'loanDays' => max(1, setting_int('loan_days', 7)),
            'finePerDay' => setting_int('fine_per_day', 1000),
        ]);
    }

    public function add(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $bookId = filter_var($this->input('book_id', 0), FILTER_VALIDATE_INT);
        if ($bookId === false || $bookId <= 0) {
            Session::flash('error', 'Buku tidak valid.');
            redirect('/books');
        }
        $book = (new Book())->find($bookId);
        if ($book === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }
        if (cart_has($bookId)) {
            Session::flash('error', 'Buku sudah ada di keranjang.');
            redirect("/books/{$bookId}");
        }
        cart_add($bookId);
        Session::flash('success', 'Ditambahkan ke keranjang.');
        redirect('/cart');
    }

    public function remove(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $bookId = filter_var($this->input('book_id', 0), FILTER_VALIDATE_INT);
        if ($bookId !== false && $bookId > 0) {
            cart_remove($bookId);
        }
        redirect('/cart');
    }

    public function clear(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        cart_clear();
        redirect('/cart');
    }

    public function checkout(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $userId = (int) Session::get('user_id');
        $bookIds = cart_items();
        if ($bookIds === []) {
            Session::flash('error', 'Keranjang masih kosong.');
            redirect('/cart');
        }

        $bookModel = new Book();
        $borrowingModel = new Borrowing();
        $resModel = new Reservation();
        $maxLoans = setting_int('max_loans', 3);
        $loanDays = max(1, setting_int('loan_days', 7));

        // Validasi global dulu (sebelum transaksi)
        if ($borrowingModel->countOverdueByUser($userId) > 0) {
            Session::flash('error', 'Anda memiliki peminjaman terlambat. Kembalikan dulu.');
            redirect('/my-borrowings');
        }
        if ((new FinePayment())->unpaidTotalByUser($userId) > 0) {
            Session::flash('error', 'Anda memiliki denda belum lunas. Lunasi dulu di halaman Denda.');
            redirect('/fines');
        }

        $active = $borrowingModel->countActiveByUser($userId);
        if ($active + count($bookIds) > $maxLoans) {
            Session::flash('error', "Batas {$maxLoans} buku aktif. Anda sudah meminjam {$active}, keranjang berisi " . count($bookIds) . ". Kurangi isi keranjang.");
            redirect('/cart');
        }

        // Validasi per buku
        $books = [];
        foreach ($bookIds as $bookId) {
            $book = $bookModel->find($bookId);
            if ($book === null) {
                cart_remove($bookId);
                Session::flash('error', 'Ada buku yang sudah tidak tersedia, keranjang diperbarui. Coba lagi.');
                redirect('/cart');
            }
            if ((int) $book['stock'] <= 0) {
                Session::flash('error', "Stok \"{$book['title']}\" habis. Keluarkan dari keranjang atau reservasi.");
                redirect('/cart');
            }
            if ($borrowingModel->hasActiveBorrowing($userId, $bookId)) {
                Session::flash('error', "Anda sudah meminjam \"{$book['title']}\".");
                redirect('/cart');
            }
            $first = $resModel->firstWaiting($bookId);
            if ($first !== null && (int) $first['user_id'] !== $userId) {
                Session::flash('error', "\"{$book['title']}\" sedang dalam antrean reservasi.");
                redirect('/cart');
            }
            $books[$bookId] = $book;
        }

        try {
            $this->db->beginTransaction();
            $borrowDate = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime("+{$loanDays} days"));
            $copyModel = new BookCopy();
            $count = 0;

            foreach ($books as $bookId => $book) {
                $copy = $copyModel->takeAvailable($bookId);
                $copyId = $copy['id'] ?? null;

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
                if (!$bookModel->decrementStock($bookId)) {
                    throw new RuntimeException("Stok \"{$book['title']}\" habis saat transaksi.");
                }

                $first = $resModel->firstWaiting($bookId);
                if ($first !== null && (int) $first['user_id'] === $userId) {
                    $resModel->update((int) $first['id'], ['status' => 'fulfilled']);
                }
                $count++;
            }

            $this->db->commit();
            cart_clear();

            log_activity('checkout', "Pinjam {$count} buku via keranjang");
            Session::flash('success', "{$count} buku berhasil dipinjam. Kembalikan sebelum " . format_date($dueDate) . '.');
            redirect('/my-borrowings');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Cart] Checkout failed: ' . $e->getMessage());
            Session::flash('error', $e instanceof RuntimeException ? $e->getMessage() : 'Checkout gagal. Silakan coba lagi.');
            redirect('/cart');
        }
    }
}
