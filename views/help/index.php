<div class="page-header">
    <div>
        <h1 class="page-title">Bantuan — Cara Pakai Pageon</h1>
        <p class="page-subtitle">Panduan singkat dengan bahasa sederhana. Baru pertama kali? Baca <strong class="text-gray-700">Pertanyaan Umum</strong> di bawah dulu.</p>
    </div>
</div>

<div class="card card-pad">
    <h2 class="font-bold tracking-tight">Pertanyaan Umum</h2>
    <p class="mt-1.5 text-sm text-gray-500">Klik pertanyaan untuk melihat jawabannya.</p>
    <div class="mt-5 space-y-2.5 text-[0.9rem] leading-relaxed text-gray-600">
        <?php
        $faqs = [
            ['Apakah saya harus daftar dulu?', 'Ya. Tekan <strong>Daftar</strong>, isi nama + email + password. Setelah itu Anda bisa langsung pinjam buku.'],
            ['Bagaimana cara meminjam buku?', 'Buka <strong>Katalog Buku</strong> → klik bukunya untuk melihat detail → tekan <strong>Pinjam Sekarang — Gratis</strong> dan konfirmasi. Buku langsung tercatat atas nama Anda.'],
            ['Buku yang saya mau stoknya habis, bagaimana?', 'Tekan <strong>Reservasi</strong> untuk ikut antrean. Kalau bukunya kembali, Anda otomatis diprioritaskan dan dapat notifikasi.'],
            ['Kapan harus mengembalikan?', 'Lihat tanggalnya di <strong>Pinjaman Saya</strong>. Butuh waktu lebih lama? Tekan <strong>Perpanjang</strong> <em>sebelum</em> tanggalnya lewat.'],
            ['Apa yang terjadi kalau telat mengembalikan?', 'Kena <strong>denda yang naik setiap hari</strong> (hari ke-1 murah, hari berikutnya makin mahal — besarnya lihat di halaman <strong>Denda</strong>). Selama masih ada tagihan, Anda belum bisa pinjam lagi. <strong>Segera bayar ke petugas</strong> agar tidak makin besar.'],
            ['Bagaimana kalau bukunya rusak / hilang?', 'Petugas mencatat kondisinya saat Anda mengembalikan. Buku <strong>rusak</strong> kena denda kerusakan, buku <strong>hilang</strong> kena ganti rugi — Anda langsung dapat <strong>notifikasi tagihan</strong> dan wajib segera membayar.'],
            ['Di mana saya lihat buku yang sudah dikembalikan?', 'Buka <strong>Pinjaman Saya</strong> lalu pilih tab <strong>Riwayat</strong> — ada ringkasan tepat waktu / telat dan status denda tiap pinjaman (lunas atau belum).'],
            ['Lupa password?', 'Di halaman Masuk, klik <strong>Lupa password?</strong> lalu ikuti langkahnya. Atau minta bantuan admin untuk me-reset password Anda.'],
        ];
        foreach ($faqs as $i => [$q, $a]):
        ?>
        <details class="group rounded-2xl bg-gray-50/80 ring-1 ring-gray-100" <?= $i === 0 ? 'open' : '' ?>>
            <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-4 font-semibold text-gray-900 sm:px-5 sm:py-4 [&::-webkit-details-marker]:hidden">
                <span><?= e($q) ?></span>
                <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </summary>
            <p class="px-4 pb-4 sm:px-5 sm:pb-5"><?= $a ?></p>
        </details>
        <?php endforeach; ?>
    </div>
</div>

<h2 class="text-lg font-bold tracking-tight">Fungsi Tiap Halaman</h2>

<div class="grid gap-4 sm:gap-5 lg:grid-cols-2">
    <?php
    $guides = [
        ['Dashboard', 'Ringkasan angka (total buku, dipinjam, terlambat), grafik 14 hari, buku terbaru & terpopuler, pengumuman, dan peringatan stok menipis. Mulai hari dari sini.', '/'],
        ['Buku', 'Katalog + cari (judul/penulis/penerbit), filter kategori & stok, urutkan. Klik buku untuk detail, pinjam langsung, reservasi (jika habis), ulas, atau wishlist.', '/books'],
        ['Pinjaman Saya', 'Buku yang sedang Anda pinjam: batas kembali, denda berjalan, tombol Perpanjang (maks sesuai aturan) dan Kembalikan, plus tombol Struk untuk bukti print. Tab Riwayat berisi buku yang sudah dikembalikan.', '/my-borrowings'],
        ['Denda', 'Tagihan denda (telat / rusak / hilang). Lunasi SEGERA ke petugas — denda telat naik tiap hari. Selama ada tagihan, Anda belum bisa pinjam lagi.', '/fines'],
        ['Reservasi', 'Antrean saat buku habis. Anda otomatis diprioritaskan saat stok kembali dan dapat notifikasi. Bisa dibatalkan kapan saja.', '/reservations'],
        ['Wishlist', 'Simpan buku incaran. Buka lagi nanti tanpa harus mencari ulang.', '/wishlist'],
        ['Notifikasi', 'Info reservasi siap + pengumuman admin. Bell di atas ikut menghitung yang belum dibaca.', '/notifications'],
        ['Profil', 'Ubah nama, ganti password, lihat statistik pinjam & denda, cetak Kartu Anggota.', '/profile'],
    ];
    foreach ($guides as [$t, $d, $href]):
    ?>
        <a href="<?= e(url($href)) ?>" class="card card-pad-sm block transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="font-bold tracking-tight"><?= e($t) ?> <span class="font-normal text-gray-400">&rarr;</span></p>
            <p class="mt-2 text-[0.9rem] leading-relaxed text-gray-600"><?= e($d) ?></p>
        </a>
    <?php endforeach; ?>
</div>

<?php if (isAdmin()): ?>
<h2 class="text-lg font-bold tracking-tight">Khusus Admin</h2>
<div class="grid gap-4 sm:gap-5 lg:grid-cols-2">
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
        <a href="<?= e(url($href)) ?>" class="card card-pad-sm block !border-amber-200 !bg-amber-50/50 transition hover:shadow-md">
            <p class="font-bold tracking-tight"><?= e($t) ?> <span class="font-normal text-gray-400">&rarr;</span></p>
            <p class="mt-2 text-[0.9rem] leading-relaxed text-gray-600"><?= e($d) ?></p>
        </a>
    <?php endforeach; ?>
</div>
<p class="rounded-2xl bg-gray-100/70 px-5 py-4 text-sm leading-relaxed text-gray-500">Catatan owner: pendaftaran admin hanya lewat URL portal rahasia + kode invite (maks 2). Jangan sebarkan URL itu.</p>
<?php endif; ?>

<div class="card card-pad">
    <h2 class="font-bold tracking-tight">Alur standar user (4 langkah)</h2>
    <ol class="mt-3 list-decimal space-y-2 pl-5 text-[0.9rem] leading-relaxed text-gray-600">
        <li><strong>Cari buku</strong> di halaman Katalog Buku → buka Detail.</li>
        <li><strong>Pinjam langsung</strong> dengan menekan <strong>Pinjam Sekarang</strong> pada halaman detail buku.</li>
        <li><strong>Pantau</strong> tanggal kembali di Pinjaman Saya. Jika stok habis, pakai <strong>Reservasi</strong>.</li>
        <li><strong>Kembalikan</strong> tepat waktu agar tidak kena denda. Butuh waktu? pakai Perpanjang sebelum jatuh tempo. Cek tagihan di halaman Denda.</li>
    </ol>
</div>
