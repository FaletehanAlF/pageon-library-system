<div class="flex min-h-screen items-center justify-center p-4">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <p class="text-3xl font-bold tracking-tight">Portal Admin</p>
            <p class="mt-2 text-sm text-gray-500">Halaman khusus pengelola. Jangan sebarkan URL ini ke siapa pun.</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-white p-8 shadow-sm">

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

            <form method="POST" action="<?= e($portalAction) ?>" class="space-y-5" novalidate>
                <?= csrf_field() ?>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email admin</label>
                    <input type="email" id="email" name="email" value="<?= old('email') ?>" required autocomplete="email" autofocus
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-900 focus:bg-white"
                        placeholder="admin@...">
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-900 focus:bg-white"
                        placeholder="Password admin">
                </div>

                <button type="submit" class="w-full rounded-xl bg-gray-900 px-4 py-3 text-sm font-medium text-white hover:bg-gray-800 transition">
                    Masuk sebagai Admin
                </button>
            </form>

        </div>

        <p class="mt-6 text-center text-sm text-gray-500">
            Belum punya akun admin kedua?
            <a href="<?= e($portalRegister) ?>" class="font-medium text-gray-900 hover:underline">Daftar dengan kode invite</a>
        </p>

    </div>
</div>
