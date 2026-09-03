<div class="mb-8 text-center">
    <a href="<?= e(url('/')) ?>" class="inline-flex items-center gap-2.5 text-[1.7rem] font-extrabold tracking-tight">
        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-900 text-white"><?= icon('book', 'h-6 w-6') ?></span>
        <span>Pageon</span>
    </a>
    <p class="mt-3 text-[0.95rem] text-gray-500">Buat akun gratis, langsung bisa pinjam buku</p>
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

    <form method="POST" action="<?= e(url('/register')) ?>" class="space-y-5" novalidate>
        <?= csrf_field() ?>

        <div>
            <label for="name" class="form-label">Nama Lengkap</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?= old('name') ?>"
                required
                autocomplete="name"
                autofocus
                maxlength="100"
                class="form-input"
                placeholder="Contoh: Budi Santoso"
            >
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= old('email') ?>"
                required
                autocomplete="email"
                maxlength="100"
                class="form-input"
                placeholder="nama@email.com"
            >
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="form-input"
                    placeholder="Min. 6 karakter"
                >
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Konfirmasi</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="form-input"
                    placeholder="Ulangi password"
                >
            </div>
        </div>

        <button
            type="submit"
            class="btn btn-primary w-full !py-3.5 !text-[0.95rem]"
        >
            Daftar Sekarang
        </button>

    </form>

</div>

<p class="mt-6 text-center text-sm text-gray-500">
    Sudah punya akun?
    <a href="<?= e(url('/login')) ?>" class="font-bold text-gray-900 hover:underline">Masuk</a>
</p>
