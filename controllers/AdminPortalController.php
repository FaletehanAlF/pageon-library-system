<?php

declare(strict_types=1);

/**
 * Portal login/daftar KHUSUS admin lewat URL rahasia.
 *
 * Route-nya TIDAK ada di routes/web.php — melainkan di
 * routes/admin-portal.php yang di-generate lokal dan masuk .gitignore.
 * Tanpa file itu, halaman ini tidak ada (404).
 */
final class AdminPortalController extends Controller
{
    private function secret(): ?array
    {
        return admin_secret();
    }

    private function portalUrl(string $sub = ''): string
    {
        $url = admin_portal_url($sub);
        if ($url === null) {
            abort(404);
        }

        return $url;
    }

    public function loginForm(): void
    {
        if ($this->secret() === null) {
            abort(404);
        }
        if (isAuth() && isAdmin()) {
            redirect('/');
        }

        $this->viewWithLayout('auth/admin-login', 'layouts/auth', [
            'title' => 'Portal Admin - Pageon',
            'portalAction' => $this->portalUrl(),
            'portalRegister' => $this->portalUrl('/register'),
        ]);
    }

    public function login(): void
    {
        if ($this->secret() === null) {
            abort(404);
        }
        if (isAuth() && isAdmin()) {
            redirect('/');
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
            redirect($this->portalPath());
        }
        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa, silakan coba lagi.');
            Session::flash('old', ['email' => $email]);
            redirect($this->portalPath());
        }

        $userModel = new User();
        $found = $userModel->findByEmail($email);

        // Sengaja pesan generik agar tidak membocorkan mana yang salah / siapa admin.
        $portalPath = $this->portalPath();
        $fail = static function () use ($email, $portalPath): never {
            Session::flash('error', 'Email atau password salah.');
            Session::flash('old', ['email' => $email]);
            redirect($portalPath);
        };

        if ($found === null || !password_verify($password, (string) $found['password'])) {
            $fail();
        }
        if (($found['role'] ?? 'user') !== 'admin') {
            $fail();
        }
        if (($found['status'] ?? 'active') === 'suspended') {
            Session::flash('error', 'Akun disuspend. Hubungi owner.');
            redirect($this->portalPath());
        }

        Session::regenerate(true);
        Session::set('user_id', (int) $found['id']);
        Session::set('user_name', (string) $found['name']);
        Session::set('user_email', (string) $found['email']);
        Session::set('user_role', 'admin');

        Session::flash('success', 'Selamat datang di portal admin.');
        redirect('/');
    }

    public function registerForm(): void
    {
        if ($this->secret() === null) {
            abort(404);
        }
        if (isAuth() && isAdmin()) {
            redirect('/');
        }

        $this->viewWithLayout('auth/admin-register', 'layouts/auth', [
            'title' => 'Daftar Admin - Pageon',
            'portalAction' => $this->portalUrl('/register'),
            'portalLogin' => $this->portalUrl(),
        ]);
    }

    public function register(): void
    {
        if ($this->secret() === null) {
            abort(404);
        }
        if (isAuth() && isAdmin()) {
            redirect('/');
        }

        $secret = $this->secret();
        $maxAdmins = (int) ($secret['max_admins'] ?? 2);

        $name = sanitize_string($_POST['name'] ?? '', 100);
        $email = sanitize_string($_POST['email'] ?? '', 100);
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['password_confirmation'] ?? '');
        $invite = trim((string) ($_POST['invite_code'] ?? ''));

        $errors = $this->validate([
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|min:8|max:72',
            'password_confirmation' => 'required',
        ]);
        if ($password !== $confirm) {
            $errors['password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }
        if (!hash_equals((string) ($secret['invite_code'] ?? ''), $invite)) {
            $errors['invite_code'] = 'Kode invite salah.';
        }

        $userModel = new User();
        if ($errors === [] && $userModel->countAdmins() >= $maxAdmins) {
            $errors['email'] = "Kuota admin penuh (maks {$maxAdmins} orang).";
        }

        if ($errors !== []) {
            Session::flash('errors', $errors);
            Session::flash('old', ['name' => $name, 'email' => $email]);
            redirect($this->portalPath('/register'));
        }
        if (!verify_csrf()) {
            Session::flash('error', 'Sesi kadaluarsa, silakan coba lagi.');
            Session::flash('old', ['name' => $name, 'email' => $email]);
            redirect($this->portalPath('/register'));
        }

        try {
            $id = $userModel->register([
                'name' => $name,
                'email' => strtolower($email),
                'password' => $password,
                'role' => 'admin',
            ]);
            $user = $userModel->find($id);
            if ($user === null) {
                throw new RuntimeException('Gagal membaca user baru');
            }
            Session::regenerate(true);
            Session::set('user_id', (int) $user['id']);
            Session::set('user_name', (string) $user['name']);
            Session::set('user_email', (string) $user['email']);
            Session::set('user_role', 'admin');
            Session::flash('success', 'Akun admin dibuat. Jaga kerahasiaan URL ini.');
            redirect('/');
        } catch (PDOException $e) {
            error_log('[AdminPortal] register failed: ' . $e->getMessage());
            Session::flash('error', 'Pendaftaran gagal. Coba lagi.');
            Session::flash('old', ['name' => $name, 'email' => $email]);
            redirect($this->portalPath('/register'));
        }
    }

    /** Path portal (tanpa base) untuk redirect internal. */
    private function portalPath(string $sub = ''): string
    {
        $secret = $this->secret();
        if ($secret === null) {
            abort(404);
        }

        return '/' . trim((string) $secret['slug'], '/') . $sub;
    }
}
