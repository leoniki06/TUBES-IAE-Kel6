<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Member - BookHouse</title>

    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/member-dashboard.css') ?>?v=<?= time() ?>">

    <style>
        .t-cover, .reco-img {
            object-fit: cover;
        }
        .reco-img{
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }
    </style>
</head>
<body>

<?php
// helper kecil: resolve cover (support cover_url / cover filename / url)
function resolveCover($book): string {
    $coverRaw = $book['cover_url'] ?? ($book['cover'] ?? '');
    if (!empty($coverRaw)) {
        if (preg_match('/^https?:\/\//', $coverRaw)) return $coverRaw;
        if (strpos($coverRaw, 'assets/') === 0) return base_url($coverRaw);
        return base_url('assets/img/books/' . ltrim($coverRaw, '/'));
    }
    return base_url('assets/img/books/laskar-pelangi.jpg'); // fallback local
}

function formatTanggal($dateStr): string {
    if (!$dateStr) return '-';
    return date('d M', strtotime($dateStr));
}
?>

<div class="app-container">

    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon">
                <i data-feather="book"></i>
            </div>
            <div class="logo-text">BookHouse</div>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= base_url('member/dashboard') ?>" class="nav-item active">
                <i data-feather="grid"></i> <span>Dashboard</span>
            </a>

            <a href="<?= base_url('member/books') ?>" class="nav-item">
                <i data-feather="search"></i> <span>Cari Buku</span>
            </a>

            <a href="<?= base_url('member/borrowed') ?>" class="nav-item">
                <i data-feather="book-open"></i> <span>Buku Dipinjam</span>
            </a>

            <a href="<?= base_url('member/return') ?>" class="nav-item">
                <i data-feather="corner-down-left"></i> <span>Pengembalian Buku</span>
            </a>

            <a href="<?= base_url('member/history') ?>" class="nav-item">
                <i data-feather="clock"></i> <span>Riwayat Transaksi</span>
            </a>
        </nav>

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
            <a href="<?= base_url('logout') ?>" class="btn-logout"><i data-feather="log-out"></i></a>
        </div>
    </aside>

    <main class="main-content">

        <header class="top-header">
            <div class="date-display">
                <i data-feather="calendar"></i>
                <span><?= date('l, d F Y') ?></span>
            </div>

            <form action="<?= base_url('member/books') ?>" method="get" class="search-bar">
                <button type="submit" class="search-btn"><i data-feather="search"></i></button>
                <input type="text" name="keyword" placeholder="Cari judul buku, penulis, atau kategori...">
            </form>

            <div class="header-actions">
                <button class="icon-btn"><i data-feather="bell"></i><span class="dot"></span></button>
            </div>
        </header>

        <section class="dashboard-hero">
            <div class="hero-text">
                <h1>Halo, <?= esc(session('user.name') ?? 'Member') ?>! 👋</h1>
                <p>Selamat datang kembali di portal perpustakaan.</p>
            </div>

            <?php if (!empty($summary['nearest_due'])): ?>
                <div class="alert-wrapper">
                    <div class="alert-box warning">
                        <i data-feather="alert-circle"></i>
                        <span><strong>Perhatian:</strong> Ada pinjaman yang jatuh tempo pada <?= esc(date('d M Y', strtotime($summary['nearest_due']))) ?>.</span>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <section class="stats-grid">
            <a href="<?= base_url('member/borrowed') ?>" class="stat-card blue">
                <div class="stat-icon"><i data-feather="book-open"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Buku Dipinjam</span>
                    <h3 class="stat-value"><?= (int)($summary['active_borrowed'] ?? 0) ?> Buku</h3>
                    <small class="stat-note">Aktif</small>
                </div>
            </a>

            <a href="<?= base_url('member/borrowed') ?>" class="stat-card orange">
                <div class="stat-icon"><i data-feather="clock"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Jatuh Tempo</span>
                    <h3 class="stat-value">
                        <?= !empty($summary['nearest_due']) ? esc(date('d M', strtotime($summary['nearest_due']))) : '-' ?>
                    </h3>
                    <small class="stat-note">Terdekat</small>
                </div>
            </a>

            <a href="<?= base_url('member/history') ?>" class="stat-card pink">
                <div class="stat-icon"><i data-feather="alert-triangle"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Denda Aktif</span>
                    <h3 class="stat-value">Rp <?= number_format((int)($summary['total_fine'] ?? 0), 0, ',', '.') ?></h3>
                    <small class="stat-note">Total</small>
                </div>
            </a>

            <a href="<?= base_url('member/history') ?>" class="stat-card purple">
                <div class="stat-icon"><i data-feather="archive"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Transaksi</span>
                    <h3 class="stat-value"><?= (int)($summary['total_transactions'] ?? 0) ?></h3>
                    <small class="stat-note">Riwayat</small>
                </div>
            </a>
        </section>

        <div class="split-layout">

            <div class="main-column">
                <div class="section-head">
                    <h3>Buku Sedang Dipinjam</h3>
                    <a href="<?= base_url('member/borrowed') ?>" class="link-view">Lihat Semua</a>
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
                        <?php if (!empty($borrowed)): ?>
                            <?php foreach ($borrowed as $t): ?>
                                <?php
                                    $isLate = (!empty($t['due_date']) && strtotime(date('Y-m-d')) > strtotime($t['due_date']));
                                ?>
                                <tr>
                                    <td>
                                        <div class="book-flex">
                                            <img src="<?= esc(resolveCover($t)) ?>" class="t-cover" alt="cover">
                                            <div>
                                                <div class="t-title"><?= esc($t['title'] ?? '-') ?></div>
                                                <div class="t-sub"><?= esc($t['author'] ?? '-') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= esc(formatTanggal($t['borrow_date'] ?? null)) ?></td>
                                    <td><?= esc(formatTanggal($t['due_date'] ?? null)) ?></td>
                                    <td>
                                        <?php if ($isLate): ?>
                                            <span class="badge late">🔴 Terlambat</span>
                                        <?php else: ?>
                                            <span class="badge active">🟢 Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('member/return') ?>" class="btn-sm btn-primary">Kembalikan</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:20px; color:#64748B;">
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
                    <h3>Aktivitas Terakhir</h3>
                </div>

                <div class="history-list">
                    <?php if (!empty($recent)): ?>
                        <?php foreach ($recent as $r): ?>
                            <?php
                                $isReturn = !empty($r['return_date']) || (($r['status'] ?? '') === 'returned');
                                $icon = $isReturn ? 'check' : 'arrow-up-right';
                                $cls  = $isReturn ? 'return' : 'borrow';
                            ?>
                            <div class="h-item">
                                <div class="h-icon <?= esc($cls) ?>"><i data-feather="<?= esc($icon) ?>"></i></div>
                                <div class="h-info">
                                    <div class="h-title"><?= esc($r['title'] ?? '-') ?></div>
                                    <div class="h-date">
                                        <?= $isReturn ? 'Dikembalikan' : 'Dipinjam' ?>
                                        • <?= esc(date('d M Y', strtotime($r['updated_at'] ?? $r['created_at'] ?? date('Y-m-d')))) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:12px; color:#64748B;">
                            Belum ada aktivitas.
                        </div>
                    <?php endif; ?>

                    <a href="<?= base_url('member/history') ?>" class="btn-block-outline">Lihat Semua Riwayat</a>
                </div>
            </div>
        </div>

        <section class="content-section">
    <div class="section-head">
        <h3>Rekomendasi Pilihan</h3>
        <a href="<?= base_url('member/books') ?>" class="link-view">Lihat Katalog</a>
    </div>

    <div class="reco-grid">
        <?php if (!empty($reco)): ?>
            <?php foreach ($reco as $b): ?>
                <?php
                    $title  = $b['title'] ?? 'Buku';
                    $author = $b['author'] ?? '-';
                    $id     = $b['id'] ?? 0;

                    // kalau kamu sudah pakai cover_url di katalog, bisa pakai yang sama.
                    // untuk aman, fallback avatar:
                    $cover = $b['cover'] ?? '';
                    if (!$cover) {
                        $cover = "https://ui-avatars.com/api/?name=" . urlencode($title) . "&background=3DB2FF&color=fff&size=512";
                    } else if (!preg_match('#^https?://#i', $cover)) {
                        $cover = base_url('assets/img/books/' . ltrim($cover, '/'));
                    }
                ?>

                <a href="<?= base_url('member/books/detail/' . $id) ?>" class="reco-card">
                    <div class="reco-img" style="background-image: url('<?= esc($cover) ?>')"></div>
                    <div class="reco-content">
                        <h4><?= esc($title) ?></h4>
                        <span><?= esc($author) ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="padding: 10px 0; color:#64748B;">
                Belum ada rekomendasi.
            </div>
        <?php endif; ?>
    </div>
</section>
    </main>
</div>
<script>feather.replace();</script>
</body>
</html>
