<div class="mb-8 text-center">
    <a href="<?= e(url('/')) ?>" class="inline-flex items-center gap-2.5 text-[1.7rem] font-extrabold tracking-tight">
        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-900 text-white"><?= icon('book', 'h-6 w-6') ?></span>
        <span>Pageon</span>
    </a>
    <p class="mt-3 text-[0.95rem] text-gray-500">Masuk untuk meminjam dan melacak buku Anda</p>
</div>

<div class="card card-pad shadow-sm">

    <?php $flashError = Session::getFlash('error'); ?>
    <?php if ($flashError): ?>
        <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm leading-relaxed text-red-800">
            <?= e((string) $flashError) ?>
        </div>
    <?php endif; ?>

    <?php $fieldErrors = Session::getFlash('errors'); ?>
    <?php if (is_array($fieldErrors) && $fieldErrors !== []): ?>
        <div role="alert" class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
            <ul class="list-disc space-y-1 pl-5">
                <?php foreach ($fieldErrors as $msg): ?>
                    <li><?= e((string) $msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= e(url('/login')) ?>" class="space-y-5" novalidate>
        <?= csrf_field() ?>

        <div>
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= old('email') ?>"
                required
                autocomplete="email"
                autofocus
                class="form-input"
                placeholder="nama@email.com"
            >
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between">
                <label for="password" class="text-sm font-semibold text-gray-700">Password</label>
                <a href="<?= e(url('/forgot-password')) ?>" class="rounded text-xs font-semibold text-gray-500 transition hover:text-gray-900 hover:underline">Lupa password?</a>
            </div>
            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
                class="form-input"
                placeholder="Masukkan password"
            >
        </div>

        <button
            type="submit"
            class="btn btn-primary w-full !py-3.5 !text-[0.95rem]"
        >
            Masuk
        </button>

    </form>

    <p class="mt-6 rounded-xl bg-gray-50 px-4 py-3 text-center text-xs leading-relaxed text-gray-400">
        Demo admin — email: <span class="font-mono font-semibold text-gray-500">admin@pageon.com</span> · password: <span class="font-mono font-semibold text-gray-500">password</span>
    </p>

</div>

<p class="mt-6 text-center text-sm text-gray-500">
    Belum punya akun?
    <a href="<?= e(url('/register')) ?>" class="font-bold text-gray-900 hover:underline">Daftar gratis</a>
</p>
