<?php

declare(strict_types=1);

final class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAdmin();
        $model = new Setting();
        $this->viewWithLayout('settings/index', 'layouts/main', [
            'title' => 'Pengaturan - Pageon',
            'page' => 'settings',
            'settings' => $model->all(),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->verifyCsrf();
        $model = new Setting();
        $loanDays = max(1, min(60, (int) ($_POST['loan_days'] ?? 7)));
        $fine = max(0, min(100000, (int) ($_POST['fine_per_day'] ?? 1000)));
        $maxLoans = max(1, min(20, (int) ($_POST['max_loans'] ?? 3)));
        $maxRenew = max(0, min(5, (int) ($_POST['max_renew'] ?? 1)));
        $library = sanitize_string($_POST['library_name'] ?? 'Pageon', 100);
        if ($library === '') {
            $library = 'Pageon';
        }
        $model->set('loan_days', $loanDays);
        $model->set('fine_per_day', $fine);
        $model->set('max_loans', $maxLoans);
        $model->set('max_renew', $maxRenew);
        $model->set('library_name', $library);
        Session::flash('success', 'Pengaturan disimpan.');
        redirect('/settings');
    }
}
