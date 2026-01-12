<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo-icon">
            <i data-feather="book"></i>
        </div>
        <div class="logo-text">BookHouse</div>
    </div>

    <?php
        $current = current_url();
        function nav_active($needle, $current) {
            return (strpos($current, $needle) !== false) ? 'active' : '';
        }
    ?>

    <nav class="sidebar-nav">
        <a href="<?= base_url('member/dashboard') ?>" class="nav-item <?= nav_active('/member/dashboard', $current) ?>">
            <i data-feather="grid"></i> <span>Dashboard</span>
        </a>

        <a href="<?= base_url('member/books') ?>" class="nav-item <?= nav_active('/member/books', $current) ?>">
            <i data-feather="search"></i> <span>Cari Buku</span>
        </a>

        <a href="<?= base_url('member/borrowed') ?>" class="nav-item <?= nav_active('/member/borrowed', $current) ?>">
            <i data-feather="book-open"></i> <span>Buku Dipinjam</span>
        </a>

        <a href="<?= base_url('member/return') ?>" class="nav-item <?= nav_active('/member/return', $current) ?>">
            <i data-feather="corner-down-left"></i> <span>Pengembalian Buku</span>
        </a>

        <a href="<?= base_url('member/history') ?>" class="nav-item <?= nav_active('/member/history', $current) ?>">
            <i data-feather="clock"></i> <span>Riwayat Transaksi</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode(session('user.name') ?? 'Member') ?>&background=3DB2FF&color=fff" alt="User">
            </div>
            <div class="user-details">
                <span class="u-name"><?= session('user.name') ?? 'Member' ?></span>
                <span class="u-role">Member Aktif</span>
            </div>
        </div>
        <a href="<?= base_url('logout') ?>" class="btn-logout"><i data-feather="log-out"></i></a>
    </div>
</aside>
