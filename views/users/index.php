<div class="page-header"><div><h1 class="page-title">Kelola User (<?= (int) ($totalCount ?? 0) ?>)</h1><p class="page-subtitle">Ubah role, suspend, reset password.</p></div></div>
<div class="table-shell"><div class="table-scroll"><table class="table-base">
<thead><tr class="bg-gray-50 border-b text-left text-gray-500"><th class="px-5 lg:px-6 py-4 font-medium">User</th><th class="px-5 lg:px-6 py-4 font-medium">Role</th><th class="px-5 lg:px-6 py-4 font-medium">Status</th><th class="px-5 lg:px-6 py-4 font-medium">Aktif Pinjam</th><th class="px-5 lg:px-6 py-4 text-right font-medium">Aksi</th></tr></thead>
<tbody>
<?php foreach (($users ?? []) as $u): ?>
<tr class="border-b border-gray-50">
<td class="px-5 lg:px-6 py-4"><p class="font-medium"><?= e($u['name']) ?></p><p class="text-xs text-gray-500"><?= e($u['email']) ?></p></td>
<td class="px-5 lg:px-6 py-4"><form method="POST" action="<?= e(url('/users/' . $u['id'] . '/role')) ?>" class="flex gap-2"><?= csrf_field() ?>
<select name="role" class="rounded-lg border text-xs px-2 py-1.5"><option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>user</option><option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin</option></select>
<button class="text-xs underline">Simpan</button></form></td>
<td class="px-5 lg:px-6 py-4"><span class="rounded-full px-2.5 py-1 text-xs <?= ($u['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= e($u['status'] ?? 'active') ?></span></td>
<td class="px-5 lg:px-6 py-4"><?= (int) ($u['active_loans'] ?? 0) ?></td>
<td class="px-5 lg:px-6 py-4 text-right">
<div class="flex justify-end gap-2">
<form method="POST" action="<?= e(url('/users/' . $u['id'] . '/status')) ?>"><?= csrf_field() ?><button class="text-xs underline"><?= ($u['status'] ?? 'active') === 'active' ? 'Suspend' : 'Aktifkan' ?></button></form>
<form method="POST" action="<?= e(url('/users/' . $u['id'] . '/reset-password')) ?>" onsubmit="return confirm('Reset password user ini?')"><?= csrf_field() ?><button class="text-xs text-amber-600 underline">Reset PW</button></form>
</div>
</td></tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?= pagination_links(url('/users'), [], $currentPage ?? 1, $totalPages ?? 1) ?>
