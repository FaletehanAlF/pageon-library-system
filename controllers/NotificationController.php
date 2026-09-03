<?php

declare(strict_types=1);

final class NotificationController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $model = new Notification();
        $uid = (int) Session::get('user_id');
        $this->viewWithLayout('notifications/index', 'layouts/main', [
            'title' => 'Notifikasi - Pageon',
            'page' => 'notifications',
            'items' => $model->getUser($uid, 30),
        ]);
    }

    public function read(string $id): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $model = new Notification();
        $model->markRead((int) Session::get('user_id'), (int) $id);
        $n = $model->find((int) $id);
        if ($n !== null && !empty($n['link'])) {
            redirect(str_replace(url(''), '', (string) $n['link']) ?: '/');
        }
        redirect('/notifications');
    }

    public function readAll(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();
        (new Notification())->markAllRead((int) Session::get('user_id'));
        Session::flash('success', 'Semua notifikasi ditandai dibaca.');
        redirect('/notifications');
    }

    public function unreadCount(): void
    {
        $this->requireAuth();
        $this->json(['unread' => (new Notification())->unreadCount((int) Session::get('user_id'))]);
    }
}
