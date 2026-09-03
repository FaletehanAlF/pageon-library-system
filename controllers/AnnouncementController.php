<?php

declare(strict_types=1);

final class AnnouncementController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $model = new Announcement();
        $this->viewWithLayout('announcements/index', 'layouts/main', [
            'title' => 'Pengumuman - Pageon',
            'page' => 'announcements',
            'items' => $model->all(),
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $title = sanitize_string($_POST['title'] ?? '', 150);
        $message = mb_substr(trim((string) ($_POST['message'] ?? '')), 0, 2000);
        $_POST['title'] = $title;
        $errors = $this->validate(['title' => 'required|max:150']);
        if ($message === '') {
            $errors['message'] = 'Isi pengumuman wajib diisi.';
        }
        if ($errors !== []) {
            Session::flash('errors', $errors);
            redirect('/announcements');
        }
        $model = new Announcement();
        $model->create(['title' => $title, 'message' => $message, 'is_active' => 1, 'created_by' => (int) Session::get('user_id')]);

        // Broadcast as notifications to all users
        $userModel = new User();
        $notif = new Notification();
        $notif->broadcast($userModel->allIds(), $title, mb_substr($message, 0, 300), url('/'));

        Session::flash('success', 'Pengumuman dipublikasi ke semua user.');
        redirect('/announcements');
    }

    public function toggle(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $model = new Announcement();
        $a = $model->find((int) $id);
        if ($a === null) {
            redirect('/announcements');
        }
        $model->update((int) $id, ['is_active' => ((int) $a['is_active'] === 1 ? 0 : 1)]);
        redirect('/announcements');
    }

    public function destroy(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        (new Announcement())->delete((int) $id);
        Session::flash('success', 'Pengumuman dihapus.');
        redirect('/announcements');
    }
}
