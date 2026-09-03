<div class="overflow-hidden rounded-2xl border-2 border-gray-900 bg-white">
    <div class="bg-gray-900 px-6 py-4 text-white">
        <p class="font-bold">📚 <?= e(setting('library_name', 'Pageon')) ?></p>
        <p class="text-xs opacity-70">Kartu Anggota Perpustakaan</p>
    </div>
    <div class="flex items-center gap-5 p-6">
        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-900 text-2xl font-bold text-white"><?= e(strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
        <div>
            <p class="text-lg font-bold"><?= e($user['name']) ?></p>
            <p class="text-sm text-gray-500"><?= e($user['email']) ?></p>
            <p class="mt-1 font-mono text-sm">ID: PG-<?= str_pad((string) $user['id'], 5, '0', STR_PAD_LEFT) ?> · Sejak <?= e(format_date($since)) ?></p>
        </div>
    </div>
    <div class="border-t px-6 py-3 text-xs text-gray-400">Kartu ini sebagai identitas saat meminjam di meja petugas.</div>
</div>
