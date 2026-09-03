<?php

declare(strict_types=1);

final class HelpController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $this->viewWithLayout('help/index', 'layouts/main', [
            'title' => 'Bantuan - Pageon',
            'page' => 'bantuan',
        ]);
    }
}
