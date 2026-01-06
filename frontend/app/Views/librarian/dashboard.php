<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
// =====================
// DATA INPUT (AMAN)
// =====================
$kpi      = $kpi ?? [];
$tx       = $tx ?? [];
$txRecent = $txRecent ?? [];

// Books (optional)
$booksList = $booksList ?? [];
$booksMeta = $booksMeta ?? [];

// Members pager khusus dashboard
$membersPager = $membersPager ?? ['items' => [], 'total' => 0];
$membersItems = $membersPager['items'] ?? [];

// =====================
// URL
// =====================
$uDash    = site_url('librarian/dashboard');
$uBooks   = site_url('librarian/books');
$uMembers = site_url('librarian/members');
$uTx      = site_url('librarian/transactions');

// =====================
// KPI TOTAL (lebih valid dari API)
// =====================
$booksTotal   = (int)($kpi['books_total'] ?? ($booksMeta['total'] ?? count($booksList)));
$membersTotal = (int)($kpi['members_total'] ?? ((int)($membersPager['total'] ?? 0)));

// snapshot active/inactive dari KPI
$activeSnap   = (int)($kpi['active_snap'] ?? 0);
$inactiveSnap = (int)($kpi['inactive_snap'] ?? 0);

// stock snapshot dari KPI
$stockAvailSnap    = (int)($kpi['stock_avail'] ?? 0);
$stockBorrowedSnap = (int)($kpi['stock_borrowed'] ?? 0);

// Today
$borrowToday  = (int)($tx['borrow_today'] ?? 0);
$returnToday  = (int)($tx['return_today'] ?? 0);
$overdueTotal = (int)($kpi['overdue_total'] ?? 0);
$txToday      = (int)($kpi['tx_today'] ?? ($borrowToday + $returnToday));

// badge status transaction
$badgeTone = function ($raw) {
    $s = strtolower((string)$raw);
    if ($s === 'overdue') return 'danger';
    return 'primary';
};
?>

<div class="dashv4 dashv4-fit">

    <!-- Header -->
    <div class="dashv4-head">
        <div class="dashv4-greet">
            <div class="dashv4-title">Good Morning, Librarian <span class="wave">👋</span></div>
            <div class="dashv4-sub">Ringkas aktivitas hari ini, cek overdue, dan pantau koleksi — cepat & rapi.</div>
        </div>

        <div class="dashv4-actions">
            <a class="iconbtn" href="<?= $uDash ?>" title="Refresh">
                <i class="fa-solid fa-rotate-right"></i>
            </a>
            <a class="btnPrimary" href="<?= $uTx ?>" title="Open Transactions">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Transactions
            </a>
        </div>
    </div>

    <!-- KPI 4 cards -->
    <div class="kpi4v4">
        <a class="kpiColorCard green kpiWhiteHover" href="<?= $uBooks ?>">
            <div class="kpiColorCard-top">
                <div class="kpiColorCard-label">TOTAL BOOKS</div>
                <div class="kpiColorCard-ic"><i class="fa-solid fa-book"></i></div>
            </div>
            <div class="kpiColorCard-value"><?= number_format($booksTotal) ?></div>
            <div class="kpiColorCard-foot">
                <span>Stock snapshot: <?= $stockAvailSnap ?> available</span>
                <span class="dotsep">•</span>
                <span><?= $stockBorrowedSnap ?> borrowed</span>
            </div>
        </a>

        <a class="kpiColorCard blue kpiWhiteHover" href="<?= $uMembers ?>">
            <div class="kpiColorCard-top">
                <div class="kpiColorCard-label">TOTAL MEMBERS</div>
                <div class="kpiColorCard-ic"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="kpiColorCard-value"><?= number_format($membersTotal) ?></div>
            <div class="kpiColorCard-foot">
                <span>Active snapshot: <?= $activeSnap ?></span>
                <span class="dotsep">•</span>
                <span>Inactive snapshot: <?= $inactiveSnap ?></span>
            </div>
        </a>

        <a class="kpiColorCard purple kpiWhiteHover" href="<?= $uTx ?>">
            <div class="kpiColorCard-top">
                <div class="kpiColorCard-label">TODAY ACTIVITY</div>
                <div class="kpiColorCard-ic"><i class="fa-solid fa-right-left"></i></div>
            </div>
            <div class="kpiColorCard-value"><?= number_format($txToday) ?></div>
            <div class="kpiColorCard-foot">
                <span>Borrow: <?= $borrowToday ?></span>
                <span class="dotsep">•</span>
                <span>Return: <?= $returnToday ?></span>
            </div>
        </a>

        <a class="kpiColorCard orange kpiWhiteHover" href="<?= $uTx ?>?status=overdue">
            <div class="kpiColorCard-top">
                <div class="kpiColorCard-label">OVERDUE</div>
                <div class="kpiColorCard-ic"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
            <div class="kpiColorCard-value"><?= number_format($overdueTotal) ?></div>
            <div class="kpiColorCard-foot">
                <?= $overdueTotal > 0 ? 'Need follow-up' : 'All clear — nice!' ?>
            </div>
        </a>
    </div>

    <!-- Main grid -->
    <div class="dashv4-grid dashv4-gridFit">

        <!-- LEFT: Transactions -->
        <section class="cardv4 cardv4-trans">
            <div class="cardv4-head">
                <div>
                    <div class="cardv4-title">Transactions</div>
                    <div class="cardv4-sub">Latest borrow/return — quick scan without noise</div>
                </div>
                <a class="cardv4-link" href="<?= $uTx ?>">See all</a>
            </div>

            <div class="chipbar">
                <span class="chip is-on">All</span>
                <span class="chip">Borrow</span>
                <span class="chip">Return</span>
                <span class="chip danger">Overdue</span>
            </div>

            <div class="tablev4">
                <div class="tablev4-head">
                    <div>MEMBER</div>
                    <div>BOOK</div>
                    <div>TYPE</div>
                    <div>STATUS</div>
                    <div class="ta-right">TIME</div>
                </div>

                <div class="tablev4-body">
                    <?php if (empty($txRecent)): ?>
                        <div class="emptyv4">
                            <div class="emptyv4-title">No transactions yet</div>
                            <div class="emptyv4-desc">Begitu ada aktivitas, list terbaru akan muncul di sini.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($txRecent as $r): ?>
                            <?php $tone = $badgeTone($r['status'] ?? ''); ?>
                            <a class="rowv4" href="<?= $uTx ?>" title="Open Transactions">
                                <div class="rowv4-main">
                                    <div class="rowv4-strong"><?= esc($r['member'] ?? '—') ?></div>
                                    <div class="rowv4-muted"><?= esc($r['book'] ?? '—') ?></div>
                                </div>
                                <div class="rowv4-cell"><?= esc($r['type'] ?? 'Borrow') ?></div>
                                <div class="rowv4-cell">
                                    <span class="pillx <?= $tone ?>"><?= esc($r['status'] ?? '-') ?></span>
                                </div>
                                <div class="rowv4-cell ta-right"><?= esc($r['time'] ?? '—') ?></div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- RIGHT -->
        <aside class="dashv4-right dashv4-rightFit">

            <!-- BOOKS OVERVIEW -->
            <section class="cardv4 ovCard cardv4-ov cardv4-ovBooks">
                <div class="cardv4-head">
                    <div>
                        <div class="cardv4-title">Books Overview</div>
                        <div class="cardv4-sub">Jumlah buku terdaftar + snapshot stok dari list saat ini</div>
                    </div>
                    <a class="cardv4-link" href="<?= $uBooks ?>">Open</a>
                </div>

                <div class="ovHero ovHero-blue">
                    <div class="ovNum"><?= number_format($booksTotal) ?></div>
                    <div class="ovLbl">Total books in system</div>
                    <div class="ovMeta">
                        Stock snapshot (page):
                        <b><?= $stockAvailSnap ?></b> available •
                        <b><?= $stockBorrowedSnap ?></b> borrowed
                    </div>
                </div>

                <div class="ovActions">
                    <a class="ovBtn primary" href="<?= $uBooks ?>">
                        <i class="fa-solid fa-books"></i>
                        View Books
                        <span class="ovGo">→</span>
                    </a>
                </div>
            </section>

            <!-- MEMBERS OVERVIEW -->
            <section class="cardv4 ovCard cardv4-ov cardv4-ovMembers">
                <div class="cardv4-head">
                    <div>
                        <div class="cardv4-title">Members Overview</div>
                        <div class="cardv4-sub">Jumlah member terdaftar + snapshot status active dari list saat ini</div>
                    </div>
                    <a class="cardv4-link" href="<?= $uMembers ?>">Browse</a>
                </div>

                <div class="ovHero ovHero-cream">
                    <div class="ovNum"><?= number_format($membersTotal) ?></div>
                    <div class="ovLbl">Total members registered</div>
                    <div class="ovMeta">
                        Status snapshot (page):
                        <b><?= $activeSnap ?></b> active •
                        <b><?= $inactiveSnap ?></b> inactive
                    </div>
                </div>

                <div class="ovActions">
                    <a class="ovBtn soft" href="<?= $uMembers ?>">
                        <i class="fa-solid fa-users-gear"></i>
                        Manage Members
                        <span class="ovGo">→</span>
                    </a>
                </div>
            </section>

        </aside>
    </div>
</div>

<?= $this->endSection() ?>
