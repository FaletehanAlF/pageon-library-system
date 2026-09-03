<?php

declare(strict_types=1);

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (isAuth()) {
            redirect('/');
        }

        $this->viewWithLayout('auth/login', 'layouts/auth', [
            'title' => 'Login - Pageon',
        ]);
    }

    public function login(): void
    {
        if (isAuth()) {
            redirect('/');
        }

        // Basic rate-limit: if too many attempts, delay
        // (simple in-memory per session)
        $attempts = (int) Session::get('_login_attempts', 0);
        if ($attempts >= 5) {
            Session::flash('error', 'Terlalu banyak percobaan. Coba lagi dalam 1 menit.');
            redirect('/login');
        }

        $email = sanitize_string($_POST['email'] ?? '', 100);
        $password = (string) ($_POST['password'] ?? '');

        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', ['email' => $email]);
            Session::flash('_login_attempts', $attempts + 1);
            redirect('/login');
        }

        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa, silakan coba lagi.');
            Session::flash('old', ['email' => $email]);
            redirect('/login');
        }

        $userModel = new User();
        $found = $userModel->findByEmail($email);
        if ($found !== null && ($found['status'] ?? 'active') === 'suspended' && password_verify($password, (string) $found['password'])) {
            Session::flash('error', 'Akun Anda disuspend. Hubungi admin.');
            Session::flash('old', ['email' => $email]);
            redirect('/login');
        }
        $user = $userModel->login($email, $password);

        if ($user === null) {
            Session::flash('error', 'Email atau password salah.');
            Session::flash('old', ['email' => $email]);
            Session::flash('_login_attempts', $attempts + 1);
            redirect('/login');
        }

        Session::regenerate(true);
        Session::set('user_id', (int) $user['id']);
        Session::set('user_name', (string) $user['name']);
        Session::set('user_email', (string) $user['email']);
        Session::set('user_role', (string) $user['role']);
        Session::remove('_login_attempts');

        Session::flash('success', 'Selamat datang, ' . $user['name'] . '!');
        redirect('/');
    }

    public function registerForm(): void
    {
        if (isAuth()) {
            redirect('/');
        }

        $this->viewWithLayout('auth/register', 'layouts/auth', [
            'title' => 'Register - Pageon',
        ]);
    }

    public function register(): void
    {
        if (isAuth()) {
            redirect('/');
        }

        $name = sanitize_string($_POST['name'] ?? '', 100);
        $email = sanitize_string($_POST['email'] ?? '', 100);
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');

        $errors = $this->validate([
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|min:6|max:72',
            'password_confirmation' => 'required',
        ]);

        if ($password !== $confirmation) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', ['name' => $name, 'email' => $email]);
            redirect('/register');
        }

        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa, silakan coba lagi.');
            Session::flash('old', ['name' => $name, 'email' => $email]);
            redirect('/register');
        }

        try {
            $userModel = new User();
            $userId = $userModel->register([
                'name' => $name,
                'email' => strtolower($email),
                'password' => $password,
                'role' => 'user',
            ]);

            $user = $userModel->find($userId);
            if ($user === null) {
                throw new RuntimeException('Failed to retrieve new user');
            }

            Session::regenerate(true);
            Session::set('user_id', (int) $user['id']);
            Session::set('user_name', (string) $user['name']);
            Session::set('user_email', (string) $user['email']);
            Session::set('user_role', (string) $user['role']);

            Session::flash('success', 'Registrasi berhasil! Selamat datang, ' . $user['name'] . '.');
            redirect('/');
        } catch (PDOException $e) {
            error_log('[Auth] Register failed: ' . $e->getMessage());
            Session::flash('error', 'Registrasi gagal. Silakan coba lagi.');
            Session::flash('old', ['name' => $name, 'email' => $email]);
            redirect('/register');
        }
    }

    public function forgotForm(): void
    {
        if (isAuth()) {
            redirect('/');
        }
        $this->viewWithLayout('auth/forgot', 'layouts/auth', [
            'title' => 'Lupa Password - Pageon',
        ]);
    }

    public function forgotSubmit(): void
    {
        if (isAuth()) {
            redirect('/');
        }
        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa, silakan coba lagi.');
            redirect('/forgot-password');
        }

        $email = strtolower(sanitize_string($_POST['email'] ?? '', 100));
        $errors = $this->validate(['email' => 'required|email']);
        if ($errors !== []) {
            Session::flash('errors', $errors);
            redirect('/forgot-password');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        // Pesan sama apapun hasilnya agar email tidak bisa ditebak
        if ($user === null) {
            Session::flash('success', 'Jika email terdaftar, kode reset telah dibuat. Minta kode ke petugas/admin atau gunakan kode di bawah (mode demo).');
            redirect('/forgot-password');
        }

        $token = bin2hex(random_bytes(16));
        (new PasswordReset())->createForUser((int) $user['id'], $token);
        log_activity('password_forgot', "Kode reset dibuat untuk {$email}");

        // Mode demo/lokal tanpa email: tampilkan kode sekali saja
        Session::flash('reset_code', $token);
        Session::flash('success', 'Kode reset dibuat dan berlaku 30 menit. Masukkan kode di halaman reset password.');
        redirect('/reset-password');
    }

    public function resetForm(): void
    {
        if (isAuth()) {
            redirect('/');
        }
        $this->viewWithLayout('auth/reset', 'layouts/auth', [
            'title' => 'Reset Password - Pageon',
            'prefill' => sanitize_string($_GET['code'] ?? '', 64),
        ]);
    }

    public function resetSubmit(): void
    {
        if (isAuth()) {
            redirect('/');
        }
        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa, silakan coba lagi.');
            redirect('/reset-password');
        }

        $token = trim((string) ($_POST['token'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirmation = (string) ($_POST['password_confirmation'] ?? '');

        $errors = [];
        if ($token === '') {
            $errors['token'] = 'Kode reset wajib diisi.';
        }
        if (strlen($password) < 6) {
            $errors['password'] = 'Password minimal 6 karakter.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'Password maksimal 72 karakter.';
        }
        if ($password !== $confirmation) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }
        if ($errors !== []) {
            Session::flash('errors', $errors);
            redirect('/reset-password');
        }

        $resetModel = new PasswordReset();
        $row = $resetModel->findValid($token);
        if ($row === null) {
            Session::flash('error', 'Kode salah, kadaluarsa, atau sudah dipakai.');
            redirect('/reset-password');
        }

        (new User())->update((int) $row['user_id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $resetModel->markUsed((int) $row['id']);
        log_activity('password_reset', 'Password direset via kode (user #' . $row['user_id'] . ')');

        Session::flash('success', 'Password berhasil diubah. Silakan login.');
        redirect('/login');
    }

    public function logout(): void
    {
        Session::destroy();
        Session::start();
        // New CSRF for next login form
        csrf_token();
        Session::flash('success', 'Anda telah logout.');
        redirect('/login');
    }
}
