<div class="mb-8">
    <h1 class="text-2xl font-bold tracking-tight">Bantuan — Cara Pakai Pageon</h1>
    <p class="mt-1 text-gray-500">Panduan singkat dengan bahasa sederhana. Baru pertama kali? Baca <strong>Pertanyaan Umum</strong> di bawah dulu.</p>
</div>

<div class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6">
    <h2 class="font-semibold">Pertanyaan Umum</h2>
    <p class="mt-1 text-sm text-gray-500">Klik pertanyaan untuk melihat jawabannya.</p>
    <div class="mt-3 space-y-2 text-sm text-gray-600">
        <?php
        $faqs = [
            ['Apakah saya harus daftar dulu?', 'Ya. Tekan <strong>Daftar</strong>, isi nama + email + password. Setelah itu Anda bisa langsung pinjam buku.'],
            ['Bagaimana cara meminjam buku?', 'Buka <strong>Buku</strong> → klik bukunya → tekan <strong>Pinjam Sekarang</strong>. Mau pinjam banyak? Tekan <strong>+ Keranjang</strong> di tiap buku, lalu buka <strong>Keranjang</strong> dan tekan <strong>Pinjam Semua Sekaligus</strong>.'],
            ['Buku yang saya mau stoknya habis, bagaimana?', 'Tekan <strong>Reservasi</strong> untuk ikut antrean. Kalau bukunya kembali, Anda otomatis diprioritaskan dan dapat notifikasi.'],
            ['Kapan harus mengembalikan?', 'Lihat tanggalnya di <strong>Pinjaman Saya</strong>. Butuh waktu lebih lama? Tekan <strong>Perpanjang</strong> <em>sebelum</em> tanggalnya lewat.'],
            ['Apa yang terjadi kalau telat mengembalikan?', 'Kena <strong>denda yang naik setiap hari</strong> (hari ke-1 murah, hari berikutnya makin mahal — besarnya lihat di halaman <strong>Denda</strong>). Selama masih ada tagihan, Anda belum bisa pinjam lagi. <strong>Segera bayar ke petugas</strong> agar tidak makin besar.'],
            ['Bagaimana kalau bukunya rusak / hilang?', 'Petugas mencatat kondisinya saat Anda mengembalikan. Buku <strong>rusak</strong> kena denda kerusakan, buku <strong>hilang</strong> kena ganti rugi — Anda langsung dapat <strong>notifikasi tagihan</strong> dan wajib segera membayar.'],
            ['Di mana saya lihat buku yang sudah dikembalikan?', 'Buka <strong>Pinjaman Saya</strong> lalu pilih tab <strong>Riwayat</strong> — ada ringkasan tepat waktu / telat dan status denda tiap pinjaman (lunas atau belum).'],
            ['Lupa password?', 'Di halaman Masuk, klik <strong>Lupa password?</strong> lalu ikuti langkahnya. Atau minta bantuan admin untuk me-reset password Anda.'],
        ];
        foreach ($faqs as $i => [$q, $a]):
        ?>
        <details class="group rounded-xl bg-gray-50" <?= $i === 0 ? 'open' : '' ?>>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 font-medium text-gray-900 [&::-webkit-details-marker]:hidden">
                <span><?= e($q) ?></span>
                <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="px-4 pb-4"><?= $a ?></p>
        </details>
        <?php endforeach; ?>
    </div>
</div>

<h2 class="mb-4 text-lg font-semibold">Fungsi Tiap Halaman</h2>

<div class="grid gap-4 lg:grid-cols-2">
    <?php
    $guides = [
        ['Dashboard', 'Ringkasan angka (total buku, dipinjam, terlambat), grafik 14 hari, buku terbaru & terpopuler, pengumuman, dan peringatan stok menipis. Mulai hari dari sini.', '/'],
        ['Buku', 'Katalog + cari (judul/penulis/penerbit), filter kategori & stok, urutkan. Klik buku untuk detail, pinjam langsung / + keranjang, reservasi (jika habis), ulas, atau wishlist.', '/books'],
        ['Keranjang', 'Kumpulkan beberapa buku dulu, lalu “Pinjam Semua Sekaligus”. Dicek otomatis: batas maks pinjam, denda lunas, stok & antrean reservasi.', '/cart'],
        ['Pinjaman Saya', 'Buku yang sedang Anda pinjam: batas kembali, denda berjalan, tombol Perpanjang (maks sesuai aturan) dan Kembalikan, plus tombol Struk untuk bukti print. Tab Riwayat berisi buku yang sudah dikembalikan.', '/my-borrowings'],
        ['Denda', 'Tagihan denda (telat / rusak / hilang). Lunasi SEGERA ke petugas — denda telat naik tiap hari. Selama ada tagihan, Anda belum bisa pinjam lagi.', '/fines'],
        ['Reservasi', 'Antrean saat buku habis. Anda otomatis diprioritaskan saat stok kembali dan dapat notifikasi. Bisa dibatalkan kapan saja.', '/reservations'],
        ['Wishlist', 'Simpan buku incaran. Buka lagi nanti tanpa harus mencari ulang.', '/wishlist'],
        ['Notifikasi', 'Info reservasi siap + pengumuman admin. Bell di atas ikut menghitung yang belum dibaca.', '/notifications'],
        ['Profil', 'Ubah nama, ganti password, lihat statistik pinjam & denda, cetak Kartu Anggota.', '/profile'],
    ];
    foreach ($guides as [$t, $d, $href]):
    ?>
        <a href="<?= e(url($href)) ?>" class="block rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-gray-300 hover:shadow-sm">
            <p class="font-semibold"><?= e($t) ?> <span class="font-normal text-gray-400">&rarr;</span></p>
            <p class="mt-1 text-sm text-gray-600"><?= e($d) ?></p>
        </a>
    <?php endforeach; ?>
</div>

<?php if (isAdmin()): ?>
<h2 class="mt-10 mb-4 text-lg font-semibold">Khusus Admin (2 orang)</h2>
<div class="grid gap-4 lg:grid-cols-2">
    <?php
    $adminGuides = [
        ['Kelola Peminjaman', 'Lihat semua pinjaman user, kembalikan atas nama user (pilih kondisi baik/rusak/hilang), pantau denda & keterlambatan.', '/borrowings'],
        ['Kelola Reservasi', 'Lihat antrean semua buku. Antrean otomatis jadi "ready" saat buku dikembalikan.', '/admin/reservations'],
        ['Tambah Buku', 'Via halaman Buku → + Tambah Buku. Isi judul/penulis/kategori/stok, upload cover, isi rak agar mudah ditemukan.', '/books/create'],
        ['Kategori', 'Tambah/ubah/hapus kategori. Kategori yang masih dipakai buku tidak bisa dihapus.', '/categories'],
        ['Laporan', 'Filter periode + status, print untuk arsip, export CSV untuk Excel.', '/reports'],
        ['Kas Denda', 'Lihat semua tagihan. Tandai Lunas saat user membayar, atau Bebaskan bila kebijakan. Total lunas vs belum lunas terpantau.', '/fines'],
        ['Kelola User', 'Ubah role (kuota admin maks 2), suspend/aktifkan, reset password user yang lupa.', '/users'],
        ['Pengumuman', 'Tulis info (libur, aturan baru). Otomatis masuk notifikasi semua user + dashboard.', '/announcements'],
        ['Log Aktivitas', 'Audit siapa berbuat apa (pinjam, kembali, bayar denda, ubah setting). Filter per aksi.', '/logs'],
        ['Pengaturan', 'Ubah nama perpustakaan, lama pinjam, denda/hari + kenaikannya, denda rusak, maks pinjam & perpanjang, dan ganti rugi buku hilang tanpa edit kode.', '/settings'],
    ];
    foreach ($adminGuides as [$t, $d, $href]):
    ?>
        <a href="<?= e(url($href)) ?>" class="block rounded-2xl border border-amber-200 bg-amber-50/50 p-5 transition hover:shadow-sm">
            <p class="font-semibold"><?= e($t) ?> <span class="font-normal text-gray-400">&rarr;</span></p>
            <p class="mt-1 text-sm text-gray-600"><?= e($d) ?></p>
        </a>
    <?php endforeach; ?>
</div>
<p class="mt-4 text-sm text-gray-500">Catatan owner: pendaftaran admin hanya lewat URL portal rahasia + kode invite (maks 2). Jangan sebarkan URL itu.</p>
<?php endif; ?>

<div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6">
    <h2 class="font-semibold">Alur standar user (4 langkah)</h2>
    <ol class="mt-2 list-decimal list-inside space-y-1 text-sm text-gray-600">
        <li><strong>Cari buku</strong> di halaman Buku → buka Detail.</li>
        <li><strong>Pinjam langsung</strong> atau <strong>+ Keranjang</strong> untuk pinjam beberapa sekaligus → buka Keranjang lalu tekan pinjam.</li>
        <li><strong>Pantau</strong> tanggal kembali di Pinjaman Saya. Jika stok habis, pakai <strong>Reservasi</strong>.</li>
        <li><strong>Kembalikan</strong> tepat waktu agar tidak kena denda. Butuh waktu? pakai Perpanjang sebelum jatuh tempo. Cek tagihan di halaman Denda.</li>
    </ol>
</div>
