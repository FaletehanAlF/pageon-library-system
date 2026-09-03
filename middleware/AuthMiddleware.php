<?php

declare(strict_types=1);

final class AuthMiddleware
{
    public static function check(): void
    {
        if (!isAuth()) {
            Session::flash('error', 'Silakan login terlebih dahulu.');
            redirect('/login');
        }
    }

    public static function admin(): void
    {
        self::check();
        if (!isAdmin()) {
            Session::flash('error', 'Anda tidak memiliki akses.');
            redirect('/');
        }
    }

    public static function guest(): void
    {
        if (isAuth()) {
            redirect('/');
        }
    }
}
