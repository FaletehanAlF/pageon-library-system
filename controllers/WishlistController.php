<?php

declare(strict_types=1);

final class WishlistController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $model = new Wishlist();
        $this->viewWithLayout('wishlist/index', 'layouts/main', [
            'title' => 'Wishlist - Pageon',
            'page' => 'wishlist',
            'items' => $model->getUserWishlist((int) Session::get('user_id')),
        ]);
    }

    public function toggle(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        $bookId = filter_var($this->input('book_id', 0), FILTER_VALIDATE_INT);
        if ($bookId === false || $bookId <= 0) {
            Session::flash('error', 'Buku tidak valid.');
            redirect('/books');
        }
        $model = new Wishlist();
        $added = $model->toggle((int) Session::get('user_id'), $bookId);
        Session::flash('success', $added ? 'Ditambahkan ke wishlist.' : 'Dihapus dari wishlist.');
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        if ($ref !== '' && str_contains($ref, '/pageon')) {
            header('Location: ' . $ref);
            exit;
        }
        redirect("/books/{$bookId}");
    }
}
