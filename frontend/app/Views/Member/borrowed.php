<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Buku Dipinjam') ?> - BookHouse</title>

    <script src="https://unpkg.com/feather-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- WAJIB: pastikan CSS borrowed ke-load -->
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

                <a href="<?= base_url('member/borrowed') ?>" class="nav-item active">
                    <i data-feather="book-open"></i> <span>Buku Dipinjam</span>
                </a>

                <a href="<?= base_url('member/return') ?>" class="nav-item">
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
            <h2 style="margin-bottom:6px;">Buku Sedang Dipinjam 📚</h2>
            <p style="margin-top:0;color:#64748B;">Kelola koleksi yang sedang kamu baca dan pantau tanggal jatuh temponya.</p>

            <div class="alert-wrapper" style="margin-top:14px;">
                <div class="alert-box warning">
                    <i data-feather="alert-circle"></i>
                    <span><strong>Penting:</strong> Harap kembalikan buku sebelum pukul 16:00 pada tanggal jatuh tempo.</span>
                </div>
            </div>

            <!-- SUMMARY CARDS -->
            <div class="stats-grid" style="margin-top:16px;">
                <div class="stat-card blue" style="cursor:default;">
                    <div class="stat-icon"><i data-feather="book-open"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Sedang Dipinjam</span>
                        <h3 class="stat-value"><?= (int)($borrowCount ?? 0) ?> Buku</h3>
                        <small class="stat-note">Limit: 3 Buku</small>
                    </div>
                </div>

                <div class="stat-card orange" style="cursor:default;">
                    <div class="stat-icon"><i data-feather="clock"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Jatuh Tempo</span>
                        <h3 class="stat-value"><?= esc($nearestDue ?? '-') ?></h3>
                        <small class="stat-note">Terdekat</small>
                    </div>
                </div>

                <div class="stat-card pink" style="cursor:default;">
                    <div class="stat-icon"><i data-feather="alert-triangle"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Status Denda</span>
                        <h3 class="stat-value">Rp <?= number_format((int)($fineTotal ?? 0), 0, ',', '.') ?></h3>
                        <small class="stat-note"><?= ((int)($fineTotal ?? 0) > 0) ? 'Ada denda' : 'Tidak ada denda' ?></small>
                    </div>
                </div>

                <div class="stat-card purple" style="cursor:default;">
                    <div class="stat-icon"><i data-feather="check-circle"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Kepatuhan</span>
                        <h3 class="stat-value"><?= ((int)($fineTotal ?? 0) > 0) ? '80%' : '100%' ?></h3>
                        <small class="stat-note"><?= ((int)($fineTotal ?? 0) > 0) ? 'Perlu ditingkatkan' : 'Sangat Baik' ?></small>
                    </div>
                </div>
            </div>

            <div class="split-layout" style="margin-top:16px;">

                <div class="main-column">
                    <div class="section-head">
                        <h3>Daftar Pinjaman Aktif</h3>
                    </div>

                    <div class="table-card">
                        <table class="clean-table">
                            <thead>
                            <tr>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($active)): ?>
                                <?php foreach ($active as $t): ?>
                                    <?php
                                        $title  = $t['title'] ?? 'Buku';
                                        $author = $t['author'] ?? '-';
                                        $borrowDate = !empty($t['borrow_date']) ? date('d M', strtotime($t['borrow_date'])) : '-';
                                        $dueDate    = !empty($t['due_date']) ? date('d M', strtotime($t['due_date'])) : '-';
                                        $status     = $t['status'] ?? 'active';

                                        // cover fallback
                                        $cover = $t['cover'] ?? '';
                                        if (!$cover) {
                                            $cover = "https://ui-avatars.com/api/?name=" . urlencode($title) . "&background=3DB2FF&color=fff&size=256";
                                        } else if (!preg_match('#^https?://#i', $cover)) {
                                            $cover = base_url('assets/img/books/' . ltrim($cover, '/'));
                                        }

                                        $badgeClass = ($status === 'late') ? 'late' : 'active';
                                        $badgeText  = ($status === 'late') ? '🔴 Terlambat' : '🟢 Aktif';
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
                                        <td><?= esc($dueDate) ?></td>
                                        <td><span class="badge <?= esc($badgeClass) ?>"><?= esc($badgeText) ?></span></td>
                                        <td>
                                            <!-- nanti tombol ini kita sambungkan ke proses return -->
                                            <a href="<?= base_url('member/return') ?>" class="btn-sm btn-primary">Kembalikan</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;color:#64748B;padding:18px;">
                                        Belum ada buku yang sedang dipinjam.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="side-column">
                    <div class="section-head">
                        <h3>Info Penting</h3>
                    </div>

                    <div class="history-list">
                        <div class="h-item">
                            <div class="h-icon return"><i data-feather="info"></i></div>
                            <div class="h-info">
                                <div class="h-title">Perpanjangan</div>
                                <div class="h-date">Fitur perpanjang tersedia 2 hari sebelum jatuh tempo.</div>
                            </div>
                        </div>

                        <div class="h-item">
                            <div class="h-icon borrow"><i data-feather="shield"></i></div>
                            <div class="h-info">
                                <div class="h-title">Keamanan</div>
                                <div class="h-date">Jaga buku tetap bersih dan tidak rusak.</div>
                            </div>
                        </div>

                        <a href="<?= base_url('member/history') ?>" class="btn-block-outline">Lihat Semua Riwayat</a>
                    </div>
                </div>

            </div>
        </section>

    </main>
</div>

<script>feather.replace()</script>
</body>
</html>
