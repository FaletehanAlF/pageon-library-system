<?php

declare(strict_types=1);

final class ReservationController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $model = new Reservation();
        $this->viewWithLayout('reservations/index', 'layouts/main', [
            'title' => 'Reservasi Saya - Pageon',
            'page' => 'reservations',
            'reservations' => $model->getUserReservations((int) Session::get('user_id')),
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
        if ($bookModel->find($bookId) === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }
        $uid = (int) Session::get('user_id');
        $model = new Reservation();
        $borrowing = new Borrowing();
        if ($borrowing->hasActiveBorrowing($uid, $bookId)) {
            Session::flash('error', 'Anda sedang meminjam buku ini.');
            redirect("/books/{$bookId}");
        }
        if ($model->hasActive($uid, $bookId)) {
            Session::flash('error', 'Anda sudah dalam antrean buku ini.');
            redirect("/books/{$bookId}");
        }
        $model->create(['user_id' => $uid, 'book_id' => $bookId, 'status' => 'waiting']);
        Session::flash('success', 'Reservasi berhasil. Anda akan dinotifikasi saat tersedia.');
        redirect("/books/{$bookId}");
    }

    public function cancel(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $model = new Reservation();
        $r = $model->find((int) $id);
        if ($r === null) {
            Session::flash('error', 'Reservasi tidak ditemukan.');
            redirect('/reservations');
        }
        if (!isAdmin() && (int) $r['user_id'] !== (int) Session::get('user_id')) {
            abort(403);
        }
        $model->update((int) $id, ['status' => 'cancelled']);
        Session::flash('success', 'Reservasi dibatalkan.');
        redirect('/reservations');
    }

    public function manage(): void
    {
        $this->requireAdmin();
        $db = Database::getInstance();
        $rows = $db->query("
            SELECT reservations.*, users.name AS user_name, books.title AS book_title
            FROM reservations
            INNER JOIN users ON users.id = reservations.user_id
            INNER JOIN books ON books.id = reservations.book_id
            ORDER BY reservations.created_at DESC LIMIT 200
        ")->fetchAll();
        $this->viewWithLayout('reservations/manage', 'layouts/main', [
            'title' => 'Kelola Reservasi - Pageon',
            'page' => 'reservations-manage',
            'reservations' => $rows,
        ]);
    }
}
