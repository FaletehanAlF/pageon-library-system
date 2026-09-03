<div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight">Log Aktivitas</h1>
        <p class="mt-1 text-gray-500">Audit admin — siapa melakukan apa. Total: <span class="font-semibold text-gray-700"><?= (int) ($total ?? 0) ?></span>.</p>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4">
    <form method="GET" action="<?= e(url('/logs')) ?>" class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <select name="action" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm outline-none focus:border-gray-900" aria-label="Filter aksi">
            <option value="">Semua aksi</option>
            <?php foreach (($actions ?? []) as $a): ?>
                <option value="<?= e($a) ?>" <?= ($action ?? '') === $a ? 'selected' : '' ?>><?= e($a) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">Filter</button>
        <?php if (!empty($action)): ?>
            <a href="<?= e(url('/logs')) ?>" class="rounded-xl border px-5 py-2.5 text-sm text-gray-600 hover:bg-gray-50">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b bg-gray-50 text-left text-gray-500">
                <th class="px-6 py-3 font-medium">Waktu</th>
                <th class="px-6 py-3 font-medium">User</th>
                <th class="px-6 py-3 font-medium">Aksi</th>
                <th class="px-6 py-3 font-medium">Detail</th>
                <th class="px-6 py-3 font-medium">IP</th>
            </tr></thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada log.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-gray-500 whitespace-nowrap" title="<?= e($l['created_at']) ?>"><?= e(time_ago($l['created_at'])) ?></td>
                            <td class="px-6 py-3"><?= e($l['user_name'] ?? ('#' . ($l['user_id'] ?? '-'))) ?></td>
                            <td class="px-6 py-3"><span class="rounded-full bg-gray-100 px-2.5 py-1 font-mono text-xs"><?= e($l['action']) ?></span></td>
                            <td class="px-6 py-3 text-gray-600 max-w-md truncate" title="<?= e($l['detail'] ?? '') ?>"><?= e($l['detail'] ?? '-') ?></td>
                            <td class="px-6 py-3 font-mono text-xs text-gray-400"><?= e($l['ip'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= pagination_links(url('/logs'), ['action' => $action ?? ''], $currentPage ?? 1, $totalPages ?? 1) ?>
