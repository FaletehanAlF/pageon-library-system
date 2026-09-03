<?php

declare(strict_types=1);

final class ProfileController extends Controller
{
    public function show(): void
    {
        $this->requireAuth();
        $user = currentUser();
        if ($user === null) {
            redirect('/login');
        }

        $borrowingModel = new Borrowing();
        $uid = (int) $user['id'];
        $stats = [
            'active' => $borrowingModel->countActiveByUser($uid),
            'overdue' => $borrowingModel->countOverdueByUser($uid),
            'fine' => $borrowingModel->totalFineByUser($uid, setting_int('fine_per_day', 1000)),
            'total' => $borrowingModel->count(['user_id' => $uid]),
        ];

        $this->viewWithLayout('profile/show', 'layouts/main', [
            'title' => 'Profil Saya - Pageon',
            'page' => 'profile',
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    public function card(): void
    {
        $this->requireAuth();
        $user = currentUser();
        if ($user === null) {
            redirect('/login');
        }
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT created_at FROM users WHERE id = ?');
        $stmt->execute([(int) $user['id']]);
        $since = (string) ($stmt->fetchColumn() ?: date('Y-m-d'));

        $this->viewWithLayout('profile/card', 'layouts/print', [
            'title' => 'Kartu Anggota - Pageon',
            'page' => 'profile',
            'user' => $user,
            'since' => $since,
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $name = sanitize_string($_POST['name'] ?? '', 100);
        $_POST['name'] = $name;

        $errors = $this->validate(['name' => 'required|min:3|max:100']);
        if ($errors !== []) {
            Session::flash('errors', $errors);
            redirect('/profile');
        }

        $uid = (int) Session::get('user_id');
        $userModel = new User();
        $userModel->update($uid, ['name' => $name]);
        Session::set('user_name', $name);
        Session::flash('success', 'Profil berhasil diperbarui.');
        redirect('/profile');
    }

    public function updatePassword(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirmation'] ?? '');

        $errors = [];
        if ($current === '') {
            $errors['current_password'] = 'Password saat ini wajib diisi.';
        }
        if (strlen($new) < 6) {
            $errors['new_password'] = 'Password baru minimal 6 karakter.';
        } elseif (strlen($new) > 72) {
            $errors['new_password'] = 'Password baru maksimal 72 karakter.';
        }
        if ($new !== $confirm) {
            $errors['new_password_confirmation'] = 'Konfirmasi tidak cocok.';
        }

        if ($errors !== []) {
            Session::flash('errors', $errors);
            redirect('/profile');
        }

        $uid = (int) Session::get('user_id');
        $userModel = new User();
        $user = $userModel->find($uid);
        if ($user === null || !password_verify($current, (string) $user['password'])) {
            Session::flash('error', 'Password saat ini salah.');
            redirect('/profile');
        }

        $userModel->update($uid, ['password' => password_hash($new, PASSWORD_DEFAULT)]);
        Session::flash('success', 'Password berhasil diubah. Silakan login ulang jika diminta.');
        redirect('/profile');
    }
}
