<div class="page-header">
    <div>
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-subtitle">Kelola data akun dan keamanan Anda.</p>
    </div>
</div>

<div class="grid items-start gap-6 lg:grid-cols-3 lg:gap-8">
    <div class="card card-pad">
        <div class="flex items-center gap-4">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gray-900 text-xl font-extrabold text-white"><?= e(strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
            <div class="min-w-0">
                <h2 class="truncate font-bold leading-snug"><?= e($user['name']) ?></h2>
                <p class="truncate text-sm text-gray-500"><?= e($user['email']) ?></p>
                <span class="badge mt-2 bg-gray-100 capitalize text-gray-600"><?= e($user['role']) ?></span>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 text-center">
            <div class="rounded-2xl bg-gray-50 p-4"><p class="text-2xl font-extrabold tracking-tight"><?= (int) $stats['active'] ?></p><p class="mt-0.5 text-xs font-medium text-gray-500">Aktif</p></div>
            <div class="rounded-2xl bg-gray-50 p-4"><p class="text-2xl font-extrabold tracking-tight"><?= (int) $stats['total'] ?></p><p class="mt-0.5 text-xs font-medium text-gray-500">Total Pinjam</p></div>
            <div class="rounded-2xl bg-red-50 p-4"><p class="text-2xl font-extrabold tracking-tight text-red-600"><?= (int) $stats['overdue'] ?></p><p class="mt-0.5 text-xs font-medium text-gray-500">Terlambat</p></div>
            <div class="rounded-2xl bg-amber-50 p-4"><p class="truncate text-lg font-extrabold tracking-tight"><?= e(format_rupiah((int) $stats['fine'])) ?></p><p class="mt-0.5 text-xs font-medium text-gray-500">Denda</p></div>
        </div>
        <a href="<?= e(url('/my-borrowings')) ?>" class="mt-5 block rounded-xl bg-gray-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800">Lihat peminjaman saya</a>
        <a href="<?= e(url('/profile/card')) ?>" class="btn btn-secondary mt-2.5 w-full">Cetak Kartu Anggota</a>
    </div>

    <div class="space-y-6 lg:col-span-2">
        <div class="card card-pad">
            <h3 class="font-bold tracking-tight">Edit Profil</h3>
            <p class="mt-1 text-sm text-gray-500">Nama tampil di struk, kartu anggota, dan daftar admin.</p>
            <form method="POST" action="<?= e(url('/profile')) ?>" class="mt-5 space-y-5">
                <?= csrf_field() ?>
                <div>
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="<?= e($user['name']) ?>" required maxlength="100" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email (tidak bisa diubah)</label>
                    <input type="email" value="<?= e($user['email']) ?>" disabled class="form-input !border-gray-100 !bg-gray-50 !text-gray-400">
                </div>
                <button class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>

        <div class="card card-pad">
            <h3 class="font-bold tracking-tight">Ganti Password</h3>
            <p class="mt-1 text-sm text-gray-500">Gunakan password yang kuat dan berbeda dari akun lain.</p>
            <form method="POST" action="<?= e(url('/profile/password')) ?>" class="mt-5 space-y-5">
                <?= csrf_field() ?>
                <div>
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="form-input">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" required autocomplete="new-password" class="form-input" placeholder="Min. 6 karakter">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password" class="form-input" placeholder="Ulangi password baru">
                    </div>
                </div>
                <button class="btn btn-secondary">Ubah Password</button>
            </form>
        </div>
    </div>
</div>
