<div class="page-header">
    <div>
        <h1 class="page-title">Pengaturan</h1>
        <p class="page-subtitle">Aturan peminjaman global. Berlaku langsung untuk pinjaman baru &amp; perhitungan denda.</p>
    </div>
</div>

<div class="max-w-2xl">
    <form method="POST" action="<?= e(url('/settings')) ?>" class="card card-pad space-y-6"><?= csrf_field() ?>

        <div>
            <label for="library_name" class="form-label">Nama Perpustakaan</label>
            <input type="text" id="library_name" name="library_name" value="<?= e($settings['library_name'] ?? 'Pageon') ?>" maxlength="100" class="form-input">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="loan_days" class="form-label">Lama Pinjam (hari)</label>
                <input type="number" id="loan_days" name="loan_days" value="<?= (int) ($settings['loan_days'] ?? 7) ?>" min="1" max="60" class="form-input">
            </div>
            <div>
                <label for="fine_per_day" class="form-label">Denda / hari (Rp)</label>
                <input type="number" id="fine_per_day" name="fine_per_day" value="<?= (int) ($settings['fine_per_day'] ?? 1000) ?>" min="0" max="100000" class="form-input">
            </div>
            <div>
                <label for="fine_increment" class="form-label">Kenaikan denda / hari (Rp)</label>
                <input type="number" id="fine_increment" name="fine_increment" value="<?= (int) ($settings['fine_increment'] ?? 500) ?>" min="0" max="50000" step="100" class="form-input">
                <p class="form-hint">Denda naik tiap hari telat. 0 = datar. Misal 1000 + naik 500: telat 3 hari = Rp4.500.</p>
            </div>
            <div>
                <label for="max_loans" class="form-label">Maks Pinjam Aktif</label>
                <input type="number" id="max_loans" name="max_loans" value="<?= (int) ($settings['max_loans'] ?? 3) ?>" min="1" max="20" class="form-input">
            </div>
            <div>
                <label for="max_renew" class="form-label">Maks Perpanjang</label>
                <input type="number" id="max_renew" name="max_renew" value="<?= (int) ($settings['max_renew'] ?? 1) ?>" min="0" max="5" class="form-input">
            </div>
            <div>
                <label for="lost_book_fee" class="form-label">Ganti Rugi Buku Hilang (Rp)</label>
                <input type="number" id="lost_book_fee" name="lost_book_fee" value="<?= (int) ($settings['lost_book_fee'] ?? 50000) ?>" min="0" max="1000000" step="500" class="form-input">
                <p class="form-hint">Ditagihkan otomatis saat pengembalian dengan kondisi “hilang”.</p>
            </div>
            <div class="sm:col-span-2">
                <label for="damage_fee" class="form-label">Denda Buku Rusak (Rp)</label>
                <input type="number" id="damage_fee" name="damage_fee" value="<?= (int) ($settings['damage_fee'] ?? 20000) ?>" min="0" max="1000000" step="500" class="form-input">
                <p class="form-hint">Ditagihkan otomatis + notifikasi ke user saat pengembalian dengan kondisi “rusak”.</p>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <button class="btn btn-primary sm:min-w-[180px]">Simpan Pengaturan</button>
        </div>
    </form>
</div>
