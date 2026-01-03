<?php
$path = service('uri')->getPath();

$is = function (string $needle) use ($path) {
    return str_contains($path, $needle);
};

$activeDashboard = $is('librarian/dashboard') || $path === 'dashboard' || $path === '';
$activeBooks     = $is('librarian/books') || $is('books');
$activeMembers   = $is('members');
$activeTx        = $is('librarian/transactions') || $is('transactions');

$role = 'Librarian';
?>

<aside class="sb2">
    <div class="sb2-top">
        <div class="sb2-brand">
            <div class="sb2-logo" aria-hidden="true"></div>
            <div>
                <div class="sb2-name">BookHouse</div>
                <div class="sb2-role"><?= esc($role) ?> Panel</div>
            </div>
        </div>

        <div class="sb2-section">MAIN</div>

        <nav class="sb2-nav">
            <a class="sb2-item <?= $activeDashboard ? 'is-active' : '' ?>" href="<?= base_url('librarian/dashboard') ?>">
                <span class="sb2-ic" aria-hidden="true">⌂</span>
                <span class="sb2-label">Dashboard</span>
                <?php if ($activeDashboard): ?><span class="sb2-badge">Live</span><?php endif; ?>
            </a>

            <a class="sb2-item <?= $activeBooks ? 'is-active' : '' ?>" href="<?= base_url('librarian/books') ?>">
                <span class="sb2-ic" aria-hidden="true">📚</span>
                <span class="sb2-label">Books</span>
            </a>

            <a class="sb2-item <?= $activeMembers ? 'is-active' : '' ?>" href="<?= base_url('members') ?>">
                <span class="sb2-ic" aria-hidden="true">👤</span>
                <span class="sb2-label">Members</span>
            </a>

            <a class="sb2-item <?= $activeTx ? 'is-active' : '' ?>" href="<?= base_url('librarian/transactions') ?>">
                <span class="sb2-ic" aria-hidden="true">⇄</span>
                <span class="sb2-label">Transactions</span>
            </a>
        </nav>
    </div>

    <div class="sb2-bottom">
        <div class="sb2-focus">
            <div class="sb2-focus-title">Today Focus</div>
            <div class="sb2-focus-text">Prioritaskan <b>overdue</b> agar denda tercatat rapi.</div>
            <div class="sb2-focus-actions">
                <a class="sb2-pillbtn" href="<?= base_url('librarian/transactions') ?>?status=overdue">View Overdue</a>
                <a class="sb2-pillbtn ghost" href="<?= base_url('librarian/books') ?>?sort=new">New Books</a>
            </div>
        </div>

        <a class="sb2-logout" href="<?= base_url('auth/logout') ?>">
            <span class="sb2-ic" aria-hidden="true">⟲</span>
            <span class="sb2-label">Logout</span>
        </a>

        <div class="sb2-footnote">BookHouse • v1</div>
    </div>
</aside>