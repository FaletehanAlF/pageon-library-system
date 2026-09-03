<div class="mb-8"><h1 class="text-2xl font-bold">Pengaturan</h1><p class="text-gray-500 mt-1">Aturan peminjaman global. Berlaku langsung untuk pinjaman baru & perhitungan denda.</p></div>
<div class="max-w-xl rounded-2xl border bg-white p-8">
<form method="POST" action="<?= e(url('/settings')) ?>" class="space-y-4"><?= csrf_field() ?>
<div><label class="text-sm font-medium">Nama Perpustakaan</label><input type="text" name="library_name" value="<?= e($settings['library_name'] ?? 'Pageon') ?>" maxlength="100" class="mt-1 w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"></div>
<div class="grid grid-cols-2 gap-4">
<div><label class="text-sm font-medium">Lama Pinjam (hari)</label><input type="number" name="loan_days" value="<?= (int) ($settings['loan_days'] ?? 7) ?>" min="1" max="60" class="mt-1 w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"></div>
<div><label class="text-sm font-medium">Denda / hari (Rp)</label><input type="number" name="fine_per_day" value="<?= (int) ($settings['fine_per_day'] ?? 1000) ?>" min="0" max="100000" class="mt-1 w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"></div>
<div><label class="text-sm font-medium">Max Pinjam Aktif</label><input type="number" name="max_loans" value="<?= (int) ($settings['max_loans'] ?? 3) ?>" min="1" max="20" class="mt-1 w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"></div>
<div><label class="text-sm font-medium">Max Perpanjang</label><input type="number" name="max_renew" value="<?= (int) ($settings['max_renew'] ?? 1) ?>" min="0" max="5" class="mt-1 w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"></div>
<div class="col-span-2"><label class="text-sm font-medium">Ganti Rugi Buku Hilang (Rp)</label><input type="number" name="lost_book_fee" value="<?= (int) ($settings['lost_book_fee'] ?? 50000) ?>" min="0" max="1000000" step="500" class="mt-1 w-full rounded-xl border bg-gray-50 px-4 py-3 text-sm"><p class="mt-1 text-xs text-gray-400">Ditagihkan otomatis saat pengembalian dengan kondisi “hilang”.</p></div>
</div>
<button class="rounded-xl bg-gray-900 px-6 py-3 text-sm text-white">Simpan</button>
</form>
</div>
