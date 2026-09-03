<div class="mb-8 text-center">
    <a href="<?= e(url('/')) ?>" class="inline-flex items-center gap-2.5 text-[1.7rem] font-extrabold tracking-tight">
        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-900 text-white"><?= icon('book', 'h-6 w-6') ?></span>
        <span>Pageon</span>
    </a>
    <p class="mt-3 text-[0.95rem] text-gray-500">Lupa password? Masukkan email akun Anda.</p>
</div>

<div class="card card-pad shadow-sm">

    <?php $flashSuccess = Session::getFlash('success'); ?>
    <?php if ($flashSuccess): ?>
        <div role="alert" class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm leading-relaxed text-green-800">
            <?= e((string) $flashSuccess) ?>
        </div>
    <?php endif; ?>

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

    <form method="POST" action="<?= e(url('/forgot-password')) ?>" class="space-y-5" novalidate>
        <?= csrf_field() ?>

        <div>
            <label for="email" class="form-label">Email akun</label>
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

        <button
            type="submit"
            class="btn btn-primary w-full !py-3.5 !text-[0.95rem]"
        >
            Buat Kode Reset
        </button>
    </form>

    <p class="mt-5 rounded-xl bg-gray-50 px-4 py-3 text-xs leading-relaxed text-gray-500">
        Kode berlaku 30 menit dan hanya bisa dipakai sekali. Tanpa layanan email, kode ditampilkan sekali di halaman berikutnya (mode demo) — atau minta ke petugas/admin.
    </p>

</div>

<p class="mt-6 text-center text-sm text-gray-500">
    Ingat password?
    <a href="<?= e(url('/login')) ?>" class="font-bold text-gray-900 hover:underline">Masuk</a>
    <span class="mx-1.5 text-gray-300">·</span>
    <a href="<?= e(url('/reset-password')) ?>" class="font-bold text-gray-900 hover:underline">Punya kode? Reset di sini</a>
</p>
