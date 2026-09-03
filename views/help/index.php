<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">Bantuan — Fungsi Tiap Halaman</h1>
    <p class="mt-1 text-gray-500">Panduan singkat agar tidak bingung memakai Pageon.</p>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    <?php
    $guides = [
        ['Dashboard', 'Ringkasan angka (total buku, dipinjam, terlambat), grafik 14 hari, buku terbaru & terpopuler, pengumuman, dan peringatan stok menipis. Mulai hari dari sini.', '/'],
        ['Buku', 'Katalog + cari (judul/penulis/penerbit), filter kategori & stok, urutkan. Klik buku untuk detail, pinjam, reservasi (jika habis), ulas, atau wishlist.', '/books'],
        ['Peminjaman Saya', 'Daftar pinjaman Anda: batas kembali, denda berjalan, tombol Perpanjang (maks sesuai aturan) dan Kembalikan, plus tombol Struk untuk bukti print.', '/my-borrowings'],
        ['Reservasi', 'Antrean saat buku habis. Anda otomatis diprioritaskan saat stok kembali dan dapat notifikasi. Bisa dibatalkan kapan saja.', '/reservations'],
        ['Wishlist', 'Simpan buku incaran. Buka lagi nanti tanpa harus mencari ulang.', '/wishlist'],
        ['Notifikasi', 'Info reservasi siap + pengumuman admin. Bell di atas ikut menghitung yang belum dibaca.', '/notifications'],
        ['Profil', 'Ubah nama, ganti password, lihat statistik pinjam & denda, cetak Kartu Anggota.', '/profile'],
    ];
    foreach ($guides as [$t, $d, $href]):
    ?>
        <a href="<?= e(url($href)) ?>" class="block rounded-2xl border border-gray-200 bg-white p-6 hover:shadow-sm transition">
            <p class="font-semibold"><?= e($t) ?></p>
            <p class="mt-1 text-sm text-gray-600"><?= e($d) ?></p>
            <p class="mt-2 text-sm font-medium text-gray-900">Buka &rarr;</p>
        </a>
    <?php endforeach; ?>
</div>

<?php if (isAdmin()): ?>
<h2 class="mt-10 mb-4 text-lg font-semibold">Khusus Admin (2 orang)</h2>
<div class="grid gap-4 lg:grid-cols-2">
    <?php
    $adminGuides = [
        ['Kelola Peminjaman', 'Lihat semua pinjaman user, kembalikan atas nama user, pantau denda & keterlambatan.', '/borrowings'],
        ['Kelola Reservasi', 'Lihat antrean semua buku. Antrean otomatis jadi "ready" saat buku dikembalikan.', '/admin/reservations'],
        ['Tambah Buku', 'Via halaman Buku → + Tambah Buku. Isi judul/penulis/kategori/stok, upload cover, isi rak agar mudah ditemukan.', '/books/create'],
        ['Kategori', 'Tambah/ubah/hapus kategori. Kategori yang masih dipakai buku tidak bisa dihapus.', '/categories'],
        ['Laporan', 'Filter periode + status, print untuk arsip, export CSV untuk Excel.', '/reports'],
        ['Kelola User', 'Ubah role (kuota admin maks 2), suspend/aktifkan, reset password user yang lupa.', '/users'],
        ['Pengumuman', 'Tulis info (libur, aturan baru). Otomatis masuk notifikasi semua user + dashboard.', '/announcements'],
        ['Pengaturan', 'Ubah lama pinjam, denda/hari, maks pinjam & perpanjang tanpa edit kode.', '/settings'],
    ];
    foreach ($adminGuides as [$t, $d, $href]):
    ?>
        <a href="<?= e(url($href)) ?>" class="block rounded-2xl border border-amber-200 bg-amber-50/50 p-6 hover:shadow-sm transition">
            <p class="font-semibold"><?= e($t) ?></p>
            <p class="mt-1 text-sm text-gray-600"><?= e($d) ?></p>
            <p class="mt-2 text-sm font-medium text-gray-900">Buka &rarr;</p>
        </a>
    <?php endforeach; ?>
</div>
<p class="mt-4 text-sm text-gray-500">Catatan owner: pendaftaran admin hanya lewat URL portal rahasia + kode invite (maks 2). Jangan sebarkan URL itu.</p>
<?php endif; ?>

<div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6">
    <h2 class="font-semibold">Alur standar user (3 langkah)</h2>
    <ol class="mt-2 list-decimal list-inside space-y-1 text-sm text-gray-600">
        <li><strong>Cari buku</strong> di halaman Buku → buka Detail.</li>
        <li><strong>Pinjam</strong> (atau Reservasi jika stok habis) → cek tanggal kembali di Peminjaman Saya.</li>
        <li><strong>Kembalikan</strong> tepat waktu agar tidak kena denda. Butuh waktu? pakai Perpanjang sebelum jatuh tempo.</li>
    </ol>
</div>
