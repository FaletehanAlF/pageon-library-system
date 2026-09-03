<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">Profil Saya</h1>
    <p class="mt-1 text-gray-500">Kelola data akun dan keamanan.</p>
</div>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-gray-200 bg-white p-6">
        <div class="flex items-center gap-4">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-900 text-xl font-bold text-white"><?= e(strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
            <div>
                <h2 class="font-bold"><?= e($user['name']) ?></h2>
                <p class="text-sm text-gray-500"><?= e($user['email']) ?></p>
                <span class="mt-1 inline-block rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium capitalize"><?= e($user['role']) ?></span>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 text-center">
            <div class="rounded-xl bg-gray-50 p-3"><p class="text-xl font-bold"><?= (int) $stats['active'] ?></p><p class="text-xs text-gray-500">Aktif</p></div>
            <div class="rounded-xl bg-gray-50 p-3"><p class="text-xl font-bold"><?= (int) $stats['total'] ?></p><p class="text-xs text-gray-500">Total Pinjam</p></div>
            <div class="rounded-xl bg-red-50 p-3"><p class="text-xl font-bold text-red-600"><?= (int) $stats['overdue'] ?></p><p class="text-xs text-gray-500">Terlambat</p></div>
            <div class="rounded-xl bg-amber-50 p-3"><p class="text-sm font-bold"><?= e(format_rupiah((int) $stats['fine'])) ?></p><p class="text-xs text-gray-500">Denda</p></div>
        </div>
        <a href="<?= e(url('/my-borrowings')) ?>" class="mt-4 block text-center text-sm font-medium text-gray-900 underline">Lihat peminjaman saya</a>
        <a href="<?= e(url('/profile/card')) ?>" class="mt-2 block rounded-xl border border-gray-200 px-4 py-2.5 text-center text-sm font-medium hover:bg-gray-50">Cetak Kartu Anggota</a>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6">
            <h3 class="font-semibold mb-4">Edit Profil</h3>
            <form method="POST" action="<?= e(url('/profile')) ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="<?= e($user['name']) ?>" required maxlength="100" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-900 focus:bg-white">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Email (tidak bisa diubah)</label>
                    <input type="email" value="<?= e($user['email']) ?>" disabled class="w-full rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-400">
                </div>
                <button class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-medium text-white hover:bg-gray-800">Simpan</button>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6">
            <h3 class="font-semibold mb-4">Ganti Password</h3>
            <form method="POST" action="<?= e(url('/profile/password')) ?>" class="space-y-4">
                <?= csrf_field() ?>
                <div>
                    <label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-900 focus:bg-white">
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="new_password" class="mb-1.5 block text-sm font-medium text-gray-700">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" required autocomplete="new-password" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-900 focus:bg-white">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-gray-900 focus:bg-white">
                    </div>
                </div>
                <button class="rounded-xl border border-gray-200 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">Ubah Password</button>
            </form>
        </div>
    </div>
</div>
