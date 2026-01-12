<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pengembalian Buku') ?> - BookHouse</title>

    <script src="https://unpkg.com/feather-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/member-dashboard.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/member-books.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/member-borrowed.css') ?>?v=<?= time() ?>">
</head>
<body>

<div class="app-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="logo-icon"><i data-feather="book-open"></i></div>
                <div class="logo-text">BookHouse</div>
            </div>

            <nav class="sidebar-nav">
                <a href="<?= base_url('member/dashboard') ?>" class="nav-item">
                    <i data-feather="grid"></i> <span>Dashboard</span>
                </a>
                <a href="<?= base_url('member/books') ?>" class="nav-item">
                    <i data-feather="search"></i> <span>Cari Buku</span>
                </a>
                <a href="<?= base_url('member/borrowed') ?>" class="nav-item">
                    <i data-feather="book-open"></i> <span>Buku Dipinjam</span>
                </a>
                <a href="<?= base_url('member/return') ?>" class="nav-item active">
                    <i data-feather="corner-down-left"></i> <span>Pengembalian Buku</span>
                </a>
                <a href="<?= base_url('member/history') ?>" class="nav-item">
                    <i data-feather="clock"></i> <span>Riwayat Transaksi</span>
                </a>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session('user.name') ?? 'Member') ?>&background=3DB2FF&color=fff" alt="User">
                </div>
                <div class="user-details">
                    <span class="u-name"><?= esc(session('user.name') ?? 'Member') ?></span>
                    <span class="u-role">Member Aktif</span>
                </div>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn-logout" title="Logout">
                <i data-feather="log-out"></i>
            </a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <header class="top-header">
            <div class="date-display">
                <i data-feather="calendar"></i>
                <span><?= date('l, d F Y') ?></span>
            </div>

            <form action="<?= base_url('member/books') ?>" method="get" class="search-bar">
                <button type="submit" class="search-btn"><i data-feather="search"></i></button>
                <input type="text" name="keyword" placeholder="Cari judul buku, penulis, atau kategori..." autocomplete="off">
            </form>

            <div class="header-actions">
                <button class="icon-btn"><i data-feather="bell"></i><span class="dot"></span></button>
            </div>
        </header>

        <section class="content-section">
            <h2 style="margin-bottom:6px;">Pengembalian Buku ↩️</h2>
            <p style="margin-top:0;color:#64748B;">Silakan pilih buku yang ingin kamu kembalikan ke perpustakaan.</p>

            <!-- FLASH -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert-wrapper" style="margin-top:14px;">
                    <div class="alert-box" style="background:#ECFDF5;border:1px solid #BBF7D0;color:#166534;">
                        <i data-feather="check-circle"></i>
                        <span><?= esc(session()->getFlashdata('success')) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-wrapper" style="margin-top:14px;">
                    <div class="alert-box" style="background:#FEF2F2;border:1px solid #FECACA;color:#991B1B;">
                        <i data-feather="alert-triangle"></i>
                        <span><?= esc(session()->getFlashdata('error')) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="alert-wrapper" style="margin-top:14px;">
                <div class="alert-box warning">
                    <i data-feather="info"></i>
                    <span><strong>Info:</strong> Pastikan fisik buku dalam keadaan baik saat dikembalikan.</span>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="stats-grid" style="margin-top:16px;">
                <div class="stat-card blue" style="cursor:default;">
                    <div class="stat-icon"><i data-feather="book"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Buku di Tangan</span>
                        <h3 class="stat-value"><?= (int)($count ?? 0) ?> Buku</h3>
                    </div>
                </div>

                <div class="stat-card orange" style="cursor:default;">
                    <div class="stat-icon"><i data-feather="calendar"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Batas Kembali</span>
                        <h3 class="stat-value"><?= esc($nearestDue ?? '-') ?></h3>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="table-card" style="margin-top:16px;">
                <table class="clean-table">
                    <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Aksi Pengembalian</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php if (!empty($active)): ?>
                        <?php foreach ($active as $t): ?>
                            <?php
                                $title  = $t['title'] ?? 'Buku';
                                $author = $t['author'] ?? '-';
                                $borrowDate = !empty($t['borrow_date']) ? date('d M Y', strtotime($t['borrow_date'])) : '-';

                                $cover = $t['cover'] ?? '';
                                if (!$cover) {
                                    $cover = "https://ui-avatars.com/api/?name=" . urlencode($title) . "&background=3DB2FF&color=fff&size=256";
                                } else if (!preg_match('#^https?://#i', $cover)) {
                                    $cover = base_url('assets/img/books/' . ltrim($cover, '/'));
                                }

                                $trxId = (int) ($t['id'] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <div class="book-flex">
                                        <img src="<?= esc($cover) ?>" class="t-cover" alt="<?= esc($title) ?>">
                                        <div>
                                            <div class="t-title"><?= esc($title) ?></div>
                                            <div class="t-sub"><?= esc($author) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><?= esc($borrowDate) ?></td>
                                <td>
                                    <form action="<?= base_url('member/return-process/' . $trxId) ?>" method="post" onsubmit="return confirm('Yakin ingin mengembalikan buku ini?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-sm btn-primary">Proses Kembalikan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align:center;color:#64748B;padding:18px;">
                                Tidak ada buku yang bisa dikembalikan.
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </section>

    </main>
</div>

<script>feather.replace()</script>
</body>
</html>
