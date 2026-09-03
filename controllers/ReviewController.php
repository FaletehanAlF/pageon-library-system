<?php

declare(strict_types=1);

final class ReviewController extends Controller
{
    public function store(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $bookId = filter_var($this->input('book_id', 0), FILTER_VALIDATE_INT);
        $rating = filter_var($this->input('rating', 0), FILTER_VALIDATE_INT);
        $comment = mb_substr(trim((string) $this->input('comment', '')), 0, 1000);
        if ($bookId === false || $bookId <= 0) {
            Session::flash('error', 'Buku tidak valid.');
            redirect('/books');
        }
        if ($rating === false || $rating < 1 || $rating > 5) {
            Session::flash('error', 'Rating harus 1-5.');
            redirect("/books/{$bookId}");
        }
        $bookModel = new Book();
        if ($bookModel->find($bookId) === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }
        $model = new Review();
        $model->upsert((int) Session::get('user_id'), $bookId, $rating, $comment);
        Session::flash('success', 'Ulasan tersimpan.');
        redirect("/books/{$bookId}");
    }

    public function destroy(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $model = new Review();
        $r = $model->find((int) $id);
        if ($r === null) {
            Session::flash('error', 'Ulasan tidak ditemukan.');
            redirect('/books');
        }
        if (!isAdmin() && (int) $r['user_id'] !== (int) Session::get('user_id')) {
            abort(403);
        }
        $model->delete((int) $id);
        Session::flash('success', 'Ulasan dihapus.');
        redirect("/books/" . (int) $r['book_id']);
    }
}
