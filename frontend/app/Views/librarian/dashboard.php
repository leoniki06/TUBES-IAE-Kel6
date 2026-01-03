<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
// ===== Dummy data UI (nanti ganti REST API) =====
$kpi = [
    'books' => 1240,
    'members' => 318,
    'tx_today' => 27,
    'overdue' => 9,
];

// transactions summary
$tx = [
    'borrow' => 18,
    'return' => 9,
    'processing' => 7,
    'done' => 16,
    'overdue' => 9,
];

$txRecent = [
    ['id' => 101, 'member' => 'Natan', 'book' => 'Atomic Habits', 'type' => 'Borrow', 'status' => 'Processing', 'time' => '09:12'],
    ['id' => 102, 'member' => 'Alya', 'book' => "Don’t Make Me Think", 'type' => 'Return', 'status' => 'Done', 'time' => '08:40'],
    ['id' => 103, 'member' => 'Raka', 'book' => 'The Power of Habit', 'type' => 'Borrow', 'status' => 'Overdue', 'time' => 'Yesterday'],
];

// books snapshot
$books = [
    'new_this_week' => 8,
    'available' => 972,
    'borrowed' => 268,
    'categories' => [
        ['name' => 'UI/UX', 'count' => 210],
        ['name' => 'Business', 'count' => 180],
        ['name' => 'Programming', 'count' => 165],
        ['name' => 'Self Dev', 'count' => 120],
    ],
    'most_borrowed' => [
        ['title' => 'Atomic Habits', 'meta' => 'Borrowed 42x'],
        ['title' => 'Ikigai', 'meta' => 'Borrowed 31x'],
        ['title' => "Don’t Make Me Think", 'meta' => 'Borrowed 27x'],
    ],
];

// members snapshot
$members = [
    'new' => 5,
    'active_today' => 22,
    'with_fines' => 9,
    'top_active' => [
        ['name' => 'Natan', 'meta' => '4 borrows this month'],
        ['name' => 'Alya', 'meta' => '3 returns this month'],
        ['name' => 'Raka', 'meta' => '2 borrows • 1 overdue'],
    ],
];

// helper untuk progress bar (maks 100)
$pct = function (int $part, int $total) {
    if ($total <= 0) return 0;
    $v = (int) round(($part / $total) * 100);
    return max(0, min(100, $v));
};

$txTotalToday = $tx['borrow'] + $tx['return'];
$borrowPct = $pct($tx['borrow'], max(1, $txTotalToday));
$returnPct = 100 - $borrowPct;

$bookTotal = $books['available'] + $books['borrowed'];
$availPct = $pct($books['available'], max(1, $bookTotal));
$borrowedPct = 100 - $availPct;

$memberRiskPct = $pct($members['with_fines'], max(1, $kpi['members']));
?>

<div class="dashx">
    <div class="dashx-head">
        <div>
            <div class="dashx-title">Dashboard</div>
            <div class="dashx-sub">Gambaran data utama dari transaksi, buku, dan member.</div>
        </div>
    </div>

    <!-- KPI (PENTING: pertahankan seperti screenshot kamu) -->
    <div class="kpi-grid keep-kpi">
        <a class="kpi kpi-link" href="<?= base_url('librarian/books') ?>" title="Lihat semua buku">
            <div class="label">Total Books</div>
            <div class="value red"><?= number_format($kpi['books']) ?></div>
            <div class="kpi-foot">
                <span class="hint">Koleksi terdaftar</span>
                <span class="pill">+<?= (int)$books['new_this_week'] ?> minggu ini</span>
            </div>
        </a>

        <a class="kpi kpi-link" href="<?= base_url('members') ?>" title="Lihat semua member">
            <div class="label">Members</div>
            <div class="value blue"><?= number_format($kpi['members']) ?></div>
            <div class="kpi-foot">
                <span class="hint">Member aktif</span>
                <span class="pill">+<?= (int)$members['new'] ?> baru</span>
            </div>
        </a>

        <a class="kpi kpi-link" href="<?= base_url('librarian/transactions') ?>" title="Lihat transaksi">
            <div class="label">Transactions</div>
            <div class="value red"><?= number_format($kpi['tx_today']) ?></div>
            <div class="kpi-foot">
                <span class="hint">Hari ini</span>
                <span class="pill">Borrow <?= (int)$tx['borrow'] ?></span>
            </div>
        </a>

        <a class="kpi kpi-link kpi-soft" href="<?= base_url('librarian/transactions') ?>?status=overdue" title="Lihat overdue">
            <div class="label">Overdue</div>
            <div class="value red"><?= number_format($kpi['overdue']) ?></div>
            <div class="kpi-foot">
                <span class="hint">Perlu follow-up</span>
                <span class="pill pill-red">urgent</span>
            </div>
        </a>
    </div>

    <!-- MAIN PANELS (3 domain: Transactions, Books, Members) -->
    <div class="dashx-panels">

        <!-- Transactions panel -->
        <section class="card card-pad panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Transactions Snapshot</div>
                    <div class="panel-sub">Ringkas status hari ini</div>
                </div>
                <a class="link" href="<?= base_url('librarian/transactions') ?>">Open</a>
            </div>

            <!-- Split bar Borrow vs Return -->
            <div class="splitbar" title="Borrow vs Return">
                <div class="split a" style="width:<?= $borrowPct ?>%"></div>
                <div class="split b" style="width:<?= $returnPct ?>%"></div>
            </div>

            <div class="splitmeta">
                <div class="m">
                    <span class="dot dot-red"></span> Borrow
                    <b><?= (int)$tx['borrow'] ?></b>
                </div>
                <div class="m">
                    <span class="dot dot-blue"></span> Return
                    <b><?= (int)$tx['return'] ?></b>
                </div>
                <div class="m">
                    <span class="badge red"><?= (int)$tx['overdue'] ?> overdue</span>
                </div>
            </div>

            <!-- Mini recent list (ringkas, bukan table besar) -->
            <div class="mini-list">
                <?php foreach ($txRecent as $r): ?>
                    <?php
                    $s = strtolower($r['status']);
                    $tone = ($s === 'overdue') ? 'red' : 'blue';
                    ?>
                    <a class="mini-row"
                        href="<?= base_url('librarian/transactions/' . $r['id']) ?>"
                        title="Detail transaksi #<?= esc($r['id']) ?>">
                        <div class="mini-left">
                            <div class="mini-title">#<?= esc($r['id']) ?> • <?= esc($r['member']) ?></div>
                            <div class="mini-meta"><?= esc($r['type']) ?> • <?= esc($r['book']) ?> • <?= esc($r['time']) ?></div>
                        </div>
                        <span class="badge <?= esc($tone) ?>"><?= esc($r['status']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="panel-foot">
                <div class="footnote">Klik item untuk masuk ke detail transaksi.</div>
            </div>
        </section>

        <!-- Books panel -->
        <section class="card card-pad panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Books Snapshot</div>
                    <div class="panel-sub">Ketersediaan & tren katalog</div>
                </div>
                <a class="link" href="<?= base_url('librarian/books') ?>">Browse</a>
            </div>

            <!-- Availability bar -->
            <div class="meter">
                <div class="meter-label">
                    <span>Availability</span>
                    <span class="muted"><?= (int)$books['available'] ?> available • <?= (int)$books['borrowed'] ?> borrowed</span>
                </div>
                <div class="meterbar">
                    <span class="fill blue" style="width:<?= $availPct ?>%"></span>
                </div>
            </div>

            <!-- Top categories chips -->
            <div class="chips">
                <?php foreach ($books['categories'] as $c): ?>
                    <a class="chip ghost" href="<?= base_url('librarian/books') ?>?category=<?= urlencode($c['name']) ?>" title="Filter kategori">
                        <?= esc($c['name']) ?> <b><?= (int)$c['count'] ?></b>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Most borrowed mini list -->
            <div class="subsection">
                <div class="subhead">
                    <div class="subttl">Most Borrowed</div>
                    <div class="submeta">minggu ini</div>
                </div>
                <div class="mini-list">
    <?php foreach ($books['most_borrowed'] as $b): ?>
        <a class="mini-row"
           href="<?= base_url('librarian/books') ?>?search=<?= urlencode($b['title']) ?>"
           title="Cari <?= esc($b['title']) ?> di daftar buku">
            <div class="mini-left">
                <div class="mini-title"><?= esc($b['title']) ?></div>
                <div class="mini-meta"><?= esc($b['meta']) ?></div>
            </div>
            <span class="chev">›</span>
        </a>
    <?php endforeach; ?>
</div>
            </div>

            <div class="panel-foot">
                <div class="footnote">Klik kategori untuk filter buku.</div>
            </div>
        </section>

        <!-- Members panel -->
        <section class="card card-pad panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Members Snapshot</div>
                    <div class="panel-sub">Aktivitas & risiko denda</div>
                </div>
                <a class="link" href="<?= base_url('members') ?>">Open</a>
            </div>

            <!-- Risk meter -->
            <div class="meter">
                <div class="meter-label">
                    <span>Fine Risk</span>
                    <span class="muted"><?= (int)$members['with_fines'] ?> member berisiko</span>
                </div>
                <div class="meterbar">
                    <span class="fill red" style="width:<?= max(10, $memberRiskPct) ?>%"></span>
                </div>
            </div>

            <!-- Small stats row -->
            <div class="stats3">
                <div class="s">
                    <div class="s-lbl">New</div>
                    <div class="s-val blue"><?= (int)$members['new'] ?></div>
                </div>
                <div class="s">
                    <div class="s-lbl">Active Today</div>
                    <div class="s-val"><?= (int)$members['active_today'] ?></div>
                </div>
                <div class="s">
                    <div class="s-lbl">With Fines</div>
                    <div class="s-val red"><?= (int)$members['with_fines'] ?></div>
                </div>
            </div>

            <!-- Top active -->
            <div class="subsection">
                <div class="subhead">
                    <div class="subttl">Top Active Members</div>
                    <div class="submeta">ringkas</div>
                </div>

                <div class="mini-list">
                    <?php foreach ($members['top_active'] as $m): ?>
                        <a class="mini-row" href="<?= base_url('members') ?>" title="Lihat member list">
                            <div class="mini-left">
                                <div class="mini-title"><?= esc($m['name']) ?></div>
                                <div class="mini-meta"><?= esc($m['meta']) ?></div>
                            </div>
                            <span class="chev">›</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="panel-foot">
                <div class="footnote">Klik untuk menuju daftar member.</div>
            </div>
        </section>

    </div>
</div>

<?= $this->endSection() ?>