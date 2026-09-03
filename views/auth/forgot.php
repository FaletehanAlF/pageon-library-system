<div class="flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <a href="<?= e(url('/')) ?>" class="inline-flex items-center gap-2 text-3xl font-bold tracking-tight"><?= icon('book', 'h-9 w-9') ?><span>Pageon</span></a>
            <p class="mt-2 text-gray-500">Lupa password? Masukkan email akun Anda.</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">

            <?php $flashSuccess = Session::getFlash('success'); ?>
            <?php if ($flashSuccess): ?>
                <div role="alert" class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    <?= e((string) $flashSuccess) ?>
                </div>
            <?php endif; ?>

            <?php $flashError = Session::getFlash('error'); ?>
            <?php if ($flashError): ?>
                <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <?= e((string) $flashError) ?>
                </div>
            <?php endif; ?>

            <?php $fieldErrors = Session::getFlash('errors'); ?>
            <?php if (is_array($fieldErrors) && $fieldErrors !== []): ?>
                <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach ($fieldErrors as $msg): ?>
                            <li><?= e((string) $msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= e(url('/forgot-password')) ?>" class="space-y-5" novalidate>
                <?= csrf_field() ?>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= old('email') ?>"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                        placeholder="nama@email.com"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition"
                >
                    Buat Kode Reset
                </button>
            </form>

            <p class="mt-4 text-xs leading-relaxed text-gray-400">
                Kode berlaku 30 menit dan hanya bisa dipakai sekali. Tanpa layanan email, kode ditampilkan sekali di halaman berikutnya (mode demo) — atau minta ke petugas/admin.
            </p>

        </div>

        <p class="mt-6 text-center text-sm text-gray-500">
            Ingat password?
            <a href="<?= e(url('/login')) ?>" class="font-medium text-gray-900 hover:underline">Masuk</a>
            ·
            <a href="<?= e(url('/reset-password')) ?>" class="font-medium text-gray-900 hover:underline">Punya kode? Reset di sini</a>
        </p>

    </div>
</div>
