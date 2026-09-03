<?php

declare(strict_types=1);

final class FineController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $model = new FinePayment();
        $userId = (int) Session::get('user_id');

        if (isAdmin()) {
            $status = $_GET['status'] ?? '';
            if (!in_array($status, ['unpaid', 'paid', 'waived'], true)) {
                $status = '';
            }
            $page = max(1, (int) ($_GET['page'] ?? 1));
            $result = $model->paginateAll($status, $page);
            $totals = $model->totals();
            $this->viewWithLayout('fines/index', 'layouts/main', [
                'title' => 'Kas Denda - Pageon',
                'page' => 'fines',
                'fines' => $result['data'],
                'total' => $result['total'],
                'currentPage' => $page,
                'totalPages' => max(1, (int) ceil($result['total'] / 15)),
                'status' => $status,
                'totals' => $totals,
            ]);
            return;
        }

        $this->viewWithLayout('fines/index', 'layouts/main', [
            'title' => 'Denda Saya - Pageon',
            'page' => 'fines',
            'fines' => $model->unpaidByUser($userId),
            'myTotal' => $model->unpaidTotalByUser($userId),
        ]);
    }

    public function pay(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $model = new FinePayment();
        if ($model->markPaid((int) $id)) {
            log_activity('fine_paid', "Denda #{$id} ditandai lunas");
            Session::flash('success', 'Denda ditandai lunas.');
        } else {
            Session::flash('error', 'Denda tidak ditemukan atau sudah diproses.');
        }
        redirect('/fines');
    }

    public function waive(string $id): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        if (!ctype_digit($id)) {
            abort(404);
        }
        $model = new FinePayment();
        if ($model->markWaived((int) $id)) {
            log_activity('fine_waived', "Denda #{$id} dibebaskan");
            Session::flash('success', 'Denda dibebaskan.');
        } else {
            Session::flash('error', 'Denda tidak ditemukan atau sudah diproses.');
        }
        redirect('/fines');
    }
}
