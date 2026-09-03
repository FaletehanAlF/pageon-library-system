<?php

declare(strict_types=1);

final class LogController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();

        $action = sanitize_string($_GET['action'] ?? '', 50);
        if ($action !== '' && !preg_match('/^[a-z_]+$/', $action)) {
            $action = '';
        }
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $model = new ActivityLog();
        $result = $model->paginate($action, $page);

        $this->viewWithLayout('logs/index', 'layouts/main', [
            'title' => 'Log Aktivitas - Pageon',
            'page' => 'logs',
            'logs' => $result['data'],
            'total' => $result['total'],
            'currentPage' => $page,
            'totalPages' => max(1, (int) ceil($result['total'] / 20)),
            'action' => $action,
            'actions' => $model->distinctActions(),
        ]);
    }
}
