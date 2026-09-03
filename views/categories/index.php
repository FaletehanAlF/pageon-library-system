<div class="page-header">
    <div>
        <h1 class="page-title">Kategori</h1>
        <p class="page-subtitle">Kelola kategori buku.</p>
    </div>
    <button type="button" onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 transition">
        + Tambah Kategori
    </button>
</div>

<?php if (empty($categories)): ?>
    <div class="empty-state">
        <p class="text-gray-500">Belum ada kategori.</p>
        <button type="button" onclick="document.getElementById('add-category-modal').classList.remove('hidden')" class="mt-4 text-sm font-medium text-gray-900 underline">Tambah kategori pertama</button>
    </div>
<?php else: ?>
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($categories as $cat): ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="font-semibold truncate"><?= e($cat['name']) ?></h3>
                        <p class="mt-1 text-sm text-gray-500"><?= (int) $cat['book_count'] ?> buku</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button"
                            data-cat-id="<?= (int) $cat['id'] ?>"
                            data-cat-name="<?= e($cat['name']) ?>"
                            onclick="openEditCategory(this)"
                            class="text-sm text-gray-500 hover:text-gray-900">Edit</button>
                        <span class="text-gray-200">·</span>
                        <form method="POST" action="<?= e(url('/categories/' . $cat['id'] . '/delete')) ?>" onsubmit="return confirm('Yakin ingin menghapus kategori &quot;<?= e(addslashes($cat['name'])) ?>&quot;?')" class="inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Add Category Modal -->
<div id="add-category-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" role="dialog" aria-modal="true" aria-labelledby="add-cat-title">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 id="add-cat-title" class="text-lg font-semibold mb-4">Tambah Kategori</h3>
        <form method="POST" action="<?= e(url('/categories')) ?>" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="category-name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input type="text" id="category-name" name="name" required maxlength="100"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10"
                    placeholder="Masukkan nama kategori">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-category-modal').classList.add('hidden')" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="edit-category-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4" role="dialog" aria-modal="true" aria-labelledby="edit-cat-title">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <h3 id="edit-cat-title" class="text-lg font-semibold mb-4">Edit Kategori</h3>
        <form id="edit-category-form" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="edit-category-name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input type="text" id="edit-category-name" name="name" required maxlength="100"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('edit-category-modal').classList.add('hidden')" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50">Batal</button>
                <button type="submit" class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCategory(el) {
    const id = el.getAttribute('data-cat-id');
    const name = el.getAttribute('data-cat-name');
    const form = document.getElementById('edit-category-form');
    form.action = <?= json_encode(url('/categories/')) ?> + '/' + encodeURIComponent(id) + '/update';
    document.getElementById('edit-category-name').value = name;
    document.getElementById('edit-category-modal').classList.remove('hidden');
}
</script>
