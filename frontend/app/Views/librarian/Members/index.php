<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
$q = (string)($q ?? '');
$members = $members ?? ['items' => [], 'total' => 0, 'per_page' => 10, 'current_page' => 1, 'last_page' => 1, 'from' => 0, 'to' => 0];

$items   = $members['items'] ?? [];
$total   = (int)($members['total'] ?? 0);
$perPage = (int)($members['per_page'] ?? 10);
$current = (int)($members['current_page'] ?? 1);
$last    = (int)($members['last_page'] ?? 1);
$from    = (int)($members['from'] ?? 0);
$to      = (int)($members['to'] ?? 0);

$activeCount = 0;
$inactiveCount = 0;
foreach ($items as $it) {
    if (!empty($it['is_active'])) $activeCount++;
    else $inactiveCount++;
}

function pageUrl($page, $q)
{
    return base_url('librarian/members') . '?search=' . urlencode((string)$q) . '&page=' . (int)$page;
}

// range pagination (max 7 buttons)
$maxBtns = 7;
$half = (int) floor($maxBtns / 2);
$start = max(1, $current - $half);
$end = min($last, $start + $maxBtns - 1);
$start = max(1, $end - $maxBtns + 1);
?>

<div class="bx-page">

    <div class="bx-titlebar">
        <div>
            <h1 class="bx-title">Members</h1>
            <p class="bx-sub">Manajemen member yang terdaftar: cari cepat dan nonaktifkan akun jika diperlukan.</p>
        </div>

        <!-- ✅ DIHAPUS: tombol tambah member -->
        <div class="bx-pill" title="Mode manajemen">
            Management Only
        </div>
    </div>

    <?php if (!empty($flash['msg'])): ?>
        <div class="alert <?= esc($flash['type'] ?? 'success') ?> js-flash">
            <?= esc($flash['msg']) ?>
            <button class="alert-close" type="button" onclick="this.closest('.js-flash').classList.add('is-hiding')">×</button>
        </div>
    <?php endif; ?>

    <div class="mx-shell">
        <!-- LEFT -->
        <div class="bx-card">

            <div class="bx-toolbar">
                <form class="bx-left" method="get" action="<?= base_url('librarian/members') ?>">
                    <div class="bx-search" style="min-width: min(560px, 92vw);">
                        <span class="bx-ic">🔎</span>
                        <input name="search" value="<?= esc($q) ?>" placeholder="Cari nama / email / phone..." />
                    </div>
                    <button class="bx-btn" type="submit">Cari</button>
                    <a class="bx-btn" href="<?= base_url('librarian/members') ?>">Reset</a>
                </form>

                <div class="bx-right">
                    <div class="bx-pill">
                        <?= $total > 0 ? "Menampilkan {$from}–{$to} dari {$total}" : "Total: 0" ?>
                    </div>
                </div>
            </div>

            <div class="bx-tablewrap">
                <table class="bx-table bx-members">
                    <thead>
                        <tr>
                            <th style="width:90px;">ID</th>
                            <th>Member</th>
                            <th style="width:140px;">Status</th>
                            <th class="ta-right" style="width:200px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="4">
                                    <div class="bx-empty">
                                        <div class="t">Belum ada member.</div>
                                        <div class="d">Data akan muncul otomatis saat user mendaftar di website.</div>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($items as $m): ?>
                                <?php
                                $isActive = !empty($m['is_active']);
                                $statusTxt = $isActive ? 'Aktif' : 'Nonaktif';
                                $pillClass = $isActive ? 'on' : 'off';

                                $name = (string)($m['name'] ?? '-');
                                $email = (string)($m['email'] ?? '-');
                                $phone = (string)($m['phone'] ?? '');
                                $initial = strtoupper(substr($name, 0, 1));
                                ?>
                                <tr>
                                    <td class="bx-id">#<?= esc($m['id']) ?></td>

                                    <td>
                                        <div class="bx-bookcell">
                                            <div class="bx-avatar"><?= esc($initial) ?></div>
                                            <div style="min-width:0">
                                                <div class="bx-booktitle"><?= esc($name) ?></div>
                                                <div class="bx-booksub">
                                                    <?= esc($email) ?>
                                                    <?= $phone !== '' ? ' • ' . esc($phone) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="mstat <?= $pillClass ?>"><?= $statusTxt ?></span>
                                    </td>

                                    <td class="ta-right">
                                        <div class="bx-actions">
                                            <?php if ($isActive): ?>
                                                <!-- ✅ Aksi satu-satunya: Nonaktifkan -->
                                                <form method="post"
                                                    action="<?= base_url('librarian/members/' . $m['id']) ?>"
                                                    onsubmit="return confirm('Nonaktifkan member ini?')"
                                                    style="display:inline;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <?= csrf_field() ?>
                                                    <button class="bx-btn danger" type="submit">Nonaktifkan</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="bx-pill" title="Akun sudah nonaktif">Sudah nonaktif</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- pagination -->
            <div class="bx-paging">
                <a class="bx-pagebtn <?= $current <= 1 ? 'disabled' : '' ?>"
                    href="<?= $current <= 1 ? 'javascript:void(0)' : pageUrl($current - 1, $q) ?>">
                    ‹ Prev
                </a>

                <div class="bx-pages">
                    <?php if ($start > 1): ?>
                        <a class="bx-p" href="<?= pageUrl(1, $q) ?>">1</a>
                        <?php if ($start > 2): ?><span class="bx-ellipsis">…</span><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <?php if ($p === $current): ?>
                            <span class="bx-p active"><?= $p ?></span>
                        <?php else: ?>
                            <a class="bx-p" href="<?= pageUrl($p, $q) ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($end < $last): ?>
                        <?php if ($end < $last - 1): ?><span class="bx-ellipsis">…</span><?php endif; ?>
                        <a class="bx-p" href="<?= pageUrl($last, $q) ?>"><?= $last ?></a>
                    <?php endif; ?>
                </div>

                <a class="bx-pagebtn <?= $current >= $last ? 'disabled' : '' ?>"
                    href="<?= $current >= $last ? 'javascript:void(0)' : pageUrl($current + 1, $q) ?>">
                    Next ›
                </a>
            </div>
        </div>

        <!-- RIGHT -->
        <aside class="mx-side">
            <div class="mx-card">
                <div class="mx-panelhead">
                    <div>
                        <div class="mx-panelttl">Members Snapshot</div>
                        <div class="mx-panelsub">Ringkasan dari halaman ini</div>
                    </div>
                    <span class="pill">Live</span>
                </div>

                <div class="mx-kpis">
                    <div class="mx-kpi">
                        <div class="l">Total</div>
                        <div class="v"><?= $total ?></div>
                    </div>
                    <div class="mx-kpi">
                        <div class="l">Page</div>
                        <div class="v"><?= $current ?>/<?= $last ?></div>
                    </div>
                    <div class="mx-kpi">
                        <div class="l">Aktif (halaman)</div>
                        <div class="v"><?= $activeCount ?></div>
                    </div>
                    <div class="mx-kpi">
                        <div class="l">Nonaktif (halaman)</div>
                        <div class="v"><?= $inactiveCount ?></div>
                    </div>
                </div>

                <div class="mx-help" style="margin-top:12px">
                    Tip: gunakan pencarian untuk menemukan akun cepat, lalu nonaktifkan jika melanggar aturan.
                </div>
            </div>
        </aside>
    </div>
</div>

<?= $this->endSection() ?>