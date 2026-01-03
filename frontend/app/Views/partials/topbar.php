<?php
$path = service('uri')->getPath();

$page = 'Dashboard';
$desc = 'Overview aktivitas hari ini';

if (str_contains($path, 'books')) {
    $page = 'Books';
    $desc = 'Kelola katalog, kategori, dan ketersediaan';
}
if (str_contains($path, 'members')) {
    $page = 'Members';
    $desc = 'Kelola data member dan status';
}
if (str_contains($path, 'transactions')) {
    $page = 'Transactions';
    $desc = 'Borrow, return, overdue, dan denda';
}

$role = 'Librarian';
?>

<header class="tb">
    <div class="tb-left">
        <div class="tb-breadcrumb">
            <span class="tb-crumb">BookHouse</span>
            <span class="tb-sep">•</span>
            <span class="tb-crumb"><?= esc($role) ?></span>
        </div>

        <div class="tb-titlewrap">
            <div class="tb-title"><?= esc($page) ?></div>
            <div class="tb-desc"><?= esc($desc) ?></div>
        </div>
    </div>

    <div class="tb-right">
        <div class="tb-status">
            <span class="tb-dot"></span>
            System OK
        </div>

        <div class="tb-actions">
            <a class="btn btn-primary" href="<?= base_url('librarian/transactions') ?>">Borrow/Return</a>
            <a class="btn btn-secondary" href="<?= base_url('librarian/books/create') ?>">+ Add Book</a>
        </div>

        <div class="tb-user">
            <div class="tb-avatar" aria-hidden="true"></div>
            <div class="tb-usertext">
                <div class="tb-username">Librarian</div>
                <div class="tb-userrole">Staff</div>
            </div>
        </div>
    </div>
</header>