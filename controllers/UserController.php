<?php

declare(strict_types=1);

final class UserController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $model = new User();
        $result = $model->paginateWithStats($page, 15);
        $totalPages = max(1, (int) ceil($result['total'] / 15));
        $this->viewWithLayout('users/index', 'layouts/main', [
            'title' => 'Kelola User - Pageon',
            'page' => 'users',
            'users' => $result['data'],
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $result['total'],
        ]);
    }

    public function updateRole(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $role = $_POST['role'] ?? '';
        if (!in_array($role, ['admin', 'user'], true)) {
            Session::flash('error', 'Role tidak valid.');
            redirect('/users');
        }
        if ((int) $id === (int) Session::get('user_id') && $role !== 'admin') {
            Session::flash('error', 'Tidak bisa menurunkan role sendiri.');
            redirect('/users');
        }
        (new User())->update((int) $id, ['role' => $role]);
        Session::flash('success', 'Role diperbarui.');
        redirect('/users');
    }

    public function toggleStatus(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        if ((int) $id === (int) Session::get('user_id')) {
            Session::flash('error', 'Tidak bisa suspend diri sendiri.');
            redirect('/users');
        }
        $model = new User();
        $u = $model->find((int) $id);
        if ($u === null) {
            redirect('/users');
        }
        $new = (($u['status'] ?? 'active') === 'active') ? 'suspended' : 'active';
        $model->update((int) $id, ['status' => $new]);
        Session::flash('success', $new === 'suspended' ? 'User disuspend.' : 'User diaktifkan.');
        redirect('/users');
    }

    public function resetPassword(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $new = bin2hex(random_bytes(4));
        (new User())->update((int) $id, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
        Session::flash('success', "Password baru: {$new} — catat dan berikan ke user.");
        redirect('/users');
    }
}
