<div class="rounded-2xl border border-gray-200 bg-white p-8">
    <div class="flex items-start justify-between">
        <div>
            <p class="flex items-center gap-2 text-xl font-bold"><?= icon('book', 'h-6 w-6') ?><span><?= e(setting('library_name', 'Pageon')) ?></span></p>
            <p class="text-sm text-gray-500">Bukti Peminjaman Buku</p>
        </div>
        <div class="text-right">
            <p class="font-mono font-bold">No. #<?= (int) $b['id'] ?></p>
            <p class="text-xs text-gray-500"><?= e(format_date((string) $b['borrow_date'])) ?></p>
        </div>
    </div>

    <dl class="mt-6 space-y-2 text-sm">
        <div class="flex justify-between border-b border-dashed pb-2"><dt class="text-gray-500">Peminjam</dt><dd class="font-medium"><?= e($b['user_name']) ?></dd></div>
        <div class="flex justify-between border-b border-dashed pb-2"><dt class="text-gray-500">Buku</dt><dd class="font-medium"><?= e($b['book_title']) ?> — <?= e($b['book_author']) ?></dd></div>
        <div class="flex justify-between border-b border-dashed pb-2"><dt class="text-gray-500">Tanggal pinjam</dt><dd><?= e(format_date((string) $b['borrow_date'])) ?></dd></div>
        <div class="flex justify-between border-b border-dashed pb-2"><dt class="text-gray-500">Batas kembali</dt><dd class="font-semibold"><?= e(format_date((string) $b['due_date'])) ?></dd></div>
        <div class="flex justify-between border-b border-dashed pb-2"><dt class="text-gray-500">Dikembalikan</dt><dd><?= !empty($b['return_date']) ? e(format_date((string) $b['return_date'])) : '— (masih dipinjam)' ?></dd></div>
        <div class="flex justify-between border-b border-dashed pb-2"><dt class="text-gray-500">Status</dt><dd><?= e($b['status']) ?></dd></div>
        <div class="flex justify-between"><dt class="text-gray-500">Denda berjalan</dt><dd class="font-bold"><?= (int) ($b['fine'] ?? 0) > 0 ? e(format_rupiah((int) $b['fine'])) : 'Rp0' ?></dd></div>
    </dl>

    <p class="mt-6 text-xs text-gray-400">Harap kembalikan sebelum batas waktu agar tidak kena denda mulai <?= e(format_rupiah(setting_int('fine_per_day', 1000))) ?>/hari<?php if (setting_int('fine_increment', 0) > 0): ?> (naik <?= e(format_rupiah(setting_int('fine_increment', 0))) ?> setiap harinya)<?php endif; ?>. Tunjukkan struk ini ke petugas.</p>
</div>
