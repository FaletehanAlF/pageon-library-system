<?php

declare(strict_types=1);

final class BookController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $bookModel = new Book();
        $categoryModel = new Category();

        $filters = [
            'q' => sanitize_string($this->input('q', ''), 100),
            'category_id' => (int) ($this->input('category_id', 0)),
            'availability' => in_array($this->input('availability', ''), ['available', 'empty'], true) ? $this->input('availability', '') : '',
            'sort' => in_array($this->input('sort', ''), ['newest', 'oldest', 'title_asc', 'title_desc'], true) ? $this->input('sort', '') : 'newest',
        ];
        $page = max(1, (int) ($this->input('page', 1)));
        $perPage = 12;

        $result = $bookModel->paginate($filters, $page, $perPage);
        $totalPages = (int) ceil($result['total'] / $perPage);
        if ($totalPages < 1) {
            $totalPages = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
            $result = $bookModel->paginate($filters, $page, $perPage);
        }

        $this->viewWithLayout('books/index', 'layouts/main', [
            'title' => 'Daftar Buku - Pageon',
            'page' => 'books',
            'books' => $result['data'],
            'keyword' => $filters['q'],
            'filters' => $filters,
            'categories' => $categoryModel->getAll(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $result['total'],
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();

        $categoryModel = new Category();

        $this->viewWithLayout('books/create', 'layouts/main', [
            'title' => 'Tambah Buku - Pageon',
            'page' => 'books',
            'categories' => $categoryModel->getAll(),
        ]);
    }

    /** @return array{data: array<string,mixed>, errors: array<string,string>} */
    private function collectBookInput(): array
    {
        $title = sanitize_string($_POST['title'] ?? '', 255);
        $author = sanitize_string($_POST['author'] ?? '', 100);
        $publisher = sanitize_string($_POST['publisher'] ?? '', 100);
        $yearRaw = sanitize_string($_POST['year'] ?? '', 4);
        $pagesRaw = sanitize_string($_POST['pages'] ?? '', 6);
        $language = sanitize_string($_POST['language'] ?? 'Indonesia', 30);
        $rack = sanitize_string($_POST['rack'] ?? '', 20);
        $isbn = sanitize_string($_POST['isbn'] ?? '', 20);
        $categoryId = sanitize_string($_POST['category_id'] ?? '', 10);
        $stock = sanitize_string($_POST['stock'] ?? '', 10);
        $description = trim((string) ($_POST['description'] ?? ''));

        $_POST['title'] = $title;
        $_POST['author'] = $author;
        $_POST['category_id'] = $categoryId;
        $_POST['stock'] = $stock;

        $errors = $this->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:100',
            'category_id' => 'required|integer',
            'stock' => 'required|integer|min:0',
        ]);

        if ($yearRaw !== '' && (!ctype_digit($yearRaw) || (int) $yearRaw < 1000 || (int) $yearRaw > (int) date('Y') + 1)) {
            $errors['year'] = 'Tahun tidak valid.';
        }
        if ($pagesRaw !== '' && (!ctype_digit($pagesRaw) || (int) $pagesRaw < 1 || (int) $pagesRaw > 10000)) {
            $errors['pages'] = 'Jumlah halaman tidak valid.';
        }

        if ($errors === [] && $categoryId !== '') {
            $catModel = new Category();
            if (!$catModel->exists((int) $categoryId)) {
                $errors['category_id'] = 'Kategori tidak valid.';
            }
        }

        if ($stock !== '' && filter_var($stock, FILTER_VALIDATE_INT) !== false && (int) $stock < 0) {
            $errors['stock'] = 'Stok tidak boleh negatif.';
        }

        return [
            'data' => [
                'title' => $title,
                'author' => $author,
                'publisher' => $publisher !== '' ? $publisher : null,
                'year' => $yearRaw !== '' ? (int) $yearRaw : null,
                'pages' => $pagesRaw !== '' ? (int) $pagesRaw : null,
                'language' => $language !== '' ? $language : 'Indonesia',
                'rack' => $rack !== '' ? $rack : null,
                'isbn' => $isbn !== '' ? $isbn : null,
                'category_id' => $categoryId !== '' ? (int) $categoryId : null,
                'stock' => $stock !== '' ? (int) $stock : 0,
                'description' => $description !== '' ? mb_substr($description, 0, 2000) : null,
            ],
            'errors' => $errors,
        ];
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $collected = $this->collectBookInput();
        $data = $collected['data'];
        $errors = $collected['errors'];

        // Cover upload
        $upload = handle_cover_upload($_FILES['cover'] ?? [], null);
        if ($upload['error'] !== null) {
            $errors['cover'] = $upload['error'];
        } else {
            $data['cover'] = $upload['filename'];
        }

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', array_merge($data, ['category_id' => (string) ($data['category_id'] ?? ''), 'stock' => (string) $data['stock'], 'year' => isset($data['year']) && $data['year'] !== null ? (string) $data['year'] : '', 'pages' => isset($data['pages']) && $data['pages'] !== null ? (string) $data['pages'] : '']));
            redirect('/books/create');
        }

        try {
            $bookModel = new Book();
            $this->db->beginTransaction();
            $bookId = $bookModel->create($data);
            $copyModel = new BookCopy();
            $copyModel->createCopies($bookId, max(0, (int) $data['stock']));
            $this->db->commit();

            Session::flash('success', 'Buku berhasil ditambahkan.');
            redirect('/books');
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('[Book] Create failed: ' . $e->getMessage());
            if (!empty($data['cover'])) {
                delete_cover($data['cover']);
            }
            Session::flash('error', 'Gagal menambahkan buku.');
            redirect('/books/create');
        }
    }

    public function show(string $id): void
    {
        $this->requireAuth();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $bookModel = new Book();
        $book = $bookModel->getWithCategory((int) $id);

        if ($book === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }

        $hasBorrowed = false;
        $inWishlist = false;
        $userReview = null;
        $queueCount = 0;
        $hasReservation = false;
        $uid = (int) Session::get('user_id', 0);
        if (isAuth() && $uid > 0) {
            $borrowingModel = new Borrowing();
            $hasBorrowed = $borrowingModel->hasActiveBorrowing($uid, (int) $id);
            $wishlistModel = new Wishlist();
            $inWishlist = $wishlistModel->has($uid, (int) $id);
            $reviewModel = new Review();
            $userReview = $reviewModel->getUserReview($uid, (int) $id);
            $resModel = new Reservation();
            $queueCount = $resModel->countWaiting((int) $id);
            $hasReservation = $resModel->hasActive($uid, (int) $id);
        }

        $reviewModel = new Review();
        $rating = $reviewModel->avgRating((int) $id);
        $reviews = $reviewModel->getByBook((int) $id);
        $copyModel = new BookCopy();
        $copies = isAdmin() ? $copyModel->getByBook((int) $id) : [];
        $related = (new Book())->getAllWithCategory(['books.category_id' => $book['category_id'] ?? 0], 'books.created_at DESC');
        $related = array_values(array_filter($related, static fn($r) => (int) $r['id'] !== (int) $id));
        $related = array_slice($related, 0, 4);

        $this->viewWithLayout('books/show', 'layouts/main', [
            'title' => $book['title'] . ' - Pageon',
            'page' => 'books',
            'book' => $book,
            'hasBorrowed' => $hasBorrowed,
            'inWishlist' => $inWishlist,
            'userReview' => $userReview,
            'rating' => $rating,
            'reviews' => $reviews,
            'copies' => $copies,
            'related' => $related,
            'queueCount' => $queueCount,
            'hasReservation' => $hasReservation,
            'loanDays' => max(1, setting_int('loan_days', 7)),
            'finePerDay' => setting_int('fine_per_day', 1000),
            'fineIncrement' => setting_int('fine_increment', 0),
        ]);
    }

    public function edit(string $id): void
    {
        $this->requireAdmin();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $bookModel = new Book();
        $book = $bookModel->getWithCategory((int) $id);

        if ($book === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }

        $categoryModel = new Category();

        $this->viewWithLayout('books/edit', 'layouts/main', [
            'title' => 'Edit ' . $book['title'] . ' - Pageon',
            'page' => 'books',
            'book' => $book,
            'categories' => $categoryModel->getAll(),
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $bookModel = new Book();
        $existing = $bookModel->find((int) $id);
        if ($existing === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }

        $collected = $this->collectBookInput();
        $data = $collected['data'];
        $errors = $collected['errors'];

        $removeCover = isset($_POST['remove_cover']) && $_POST['remove_cover'] === '1';
        $upload = handle_cover_upload($_FILES['cover'] ?? [], $existing['cover'] ?? null);
        if ($upload['error'] !== null) {
            $errors['cover'] = $upload['error'];
            $data['cover'] = $existing['cover'] ?? null;
        } else {
            $data['cover'] = $upload['filename'];
            if ($removeCover && empty($_FILES['cover']['name'] ?? '')) {
                delete_cover($existing['cover'] ?? null);
                $data['cover'] = null;
            }
        }

        if ($errors !== []) {
            Session::flash('errors', $errors);
            redirect("/books/{$id}/edit");
        }

        try {
            $this->db->beginTransaction();
            $bookModel->update((int) $id, $data);
            $copyModel = new BookCopy();
            $copyModel->syncStock((int) $id, (int) $data['stock']);
            $this->db->commit();

            Session::flash('success', 'Buku berhasil diperbarui.');
            redirect("/books/{$id}");
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("[Book] Update {$id} failed: " . $e->getMessage());
            Session::flash('error', 'Gagal memperbarui buku.');
            redirect("/books/{$id}/edit");
        }
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $bookModel = new Book();
        $book = $bookModel->find((int) $id);

        if ($book === null) {
            Session::flash('error', 'Buku tidak ditemukan.');
            redirect('/books');
        }

        if ($bookModel->hasActiveBorrowings((int) $id)) {
            Session::flash('error', 'Buku tidak dapat dihapus karena masih ada peminjaman aktif.');
            redirect("/books/{$id}");
        }

        try {
            $bookModel->delete((int) $id);
            delete_cover($book['cover'] ?? null);
            Session::flash('success', 'Buku berhasil dihapus.');
        } catch (PDOException $e) {
            error_log("[Book] Delete {$id} failed: " . $e->getMessage());
            Session::flash('error', 'Gagal menghapus buku.');
        }

        redirect('/books');
    }
}
