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
        $lostFee = max(0, min(1000000, (int) ($_POST['lost_book_fee'] ?? 50000)));
        $fineInc = max(0, min(50000, (int) ($_POST['fine_increment'] ?? 0)));
        $damageFee = max(0, min(1000000, (int) ($_POST['damage_fee'] ?? 20000)));
        $library = sanitize_string($_POST['library_name'] ?? 'Pageon', 100);
        if ($library === '') {
            $library = 'Pageon';
        }
        $model->set('loan_days', $loanDays);
        $model->set('fine_per_day', $fine);
        $model->set('fine_increment', $fineInc);
        $model->set('damage_fee', $damageFee);
        $model->set('max_loans', $maxLoans);
        $model->set('max_renew', $maxRenew);
        $model->set('lost_book_fee', $lostFee);
        $model->set('library_name', $library);
        log_activity('settings_update', "loan_days={$loanDays}, fine={$fine}, fine_inc={$fineInc}, damage_fee={$damageFee}, max_loans={$maxLoans}, max_renew={$maxRenew}, lost_fee={$lostFee}");
        Session::flash('success', 'Pengaturan disimpan.');
        redirect('/settings');
    }
}
