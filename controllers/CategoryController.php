<?php

declare(strict_types=1);

final class CategoryController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $categoryModel = new Category();

        $this->viewWithLayout('categories/index', 'layouts/main', [
            'title' => 'Kategori - Pageon',
            'page' => 'categories',
            'categories' => $categoryModel->getWithBookCount(),
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        $name = sanitize_string($_POST['name'] ?? '', 100);
        $_POST['name'] = $name;

        $errors = $this->validate([
            'name' => 'required|max:100|unique:categories,name',
        ]);

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('error', $errors['name'] ?? 'Gagal menambahkan kategori.');
            redirect('/categories');
        }

        try {
            $categoryModel = new Category();
            $categoryModel->create(['name' => $name]);
            Session::flash('success', 'Kategori berhasil ditambahkan.');
        } catch (PDOException $e) {
            error_log('[Category] Create failed: ' . $e->getMessage());
            Session::flash('error', 'Gagal menambahkan kategori.');
        }

        redirect('/categories');
    }

    public function update(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $categoryModel = new Category();
        if (!$categoryModel->exists((int) $id)) {
            Session::flash('error', 'Kategori tidak ditemukan.');
            redirect('/categories');
        }

        $name = sanitize_string($_POST['name'] ?? '', 100);
        $_POST['name'] = $name;

        $errors = $this->validate([
            'name' => "required|max:100|unique:categories,name,{$id}",
        ]);

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('error', $errors['name'] ?? 'Gagal memperbarui kategori.');
            redirect('/categories');
        }

        try {
            $categoryModel->update((int) $id, ['name' => $name]);
            Session::flash('success', 'Kategori berhasil diperbarui.');
        } catch (PDOException $e) {
            error_log("[Category] Update {$id} failed: " . $e->getMessage());
            Session::flash('error', 'Gagal memperbarui kategori.');
        }

        redirect('/categories');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();

        if (!ctype_digit($id)) {
            abort(404);
        }

        $categoryModel = new Category();
        if (!$categoryModel->exists((int) $id)) {
            Session::flash('error', 'Kategori tidak ditemukan.');
            redirect('/categories');
        }

        if (!$categoryModel->isDeletable((int) $id)) {
            Session::flash('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.');
            redirect('/categories');
        }

        try {
            $categoryModel->delete((int) $id);
            Session::flash('success', 'Kategori berhasil dihapus.');
        } catch (PDOException $e) {
            error_log("[Category] Delete {$id} failed: " . $e->getMessage());
            Session::flash('error', 'Gagal menghapus kategori.');
        }

        redirect('/categories');
    }
}
