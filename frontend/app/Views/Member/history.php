<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - BookHouse</title>

    <script src="https://unpkg.com/feather-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Pakai CSS dashboard kamu biar konsisten -->
    <link rel="stylesheet" href="<?= base_url('assets/css/member-dashboard.css') ?>?v=<?= time() ?>">

    <style>
        .card { background:#fff; border-radius:16px; padding:18px; box-shadow:0 10px 30px rgba(0,0,0,.06); }
        .table { width:100%; border-collapse:collapse; }
        .table th, .table td { padding:12px 10px; border-bottom:1px solid #eef2f7; text-align:left; vertical-align:middle; }
        .badge { padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px; display:inline-block; }
        .b-active { background:#e8f9ee; color:#15803d; }
        .b-return { background:#eef2ff; color:#4338ca; }
        .b-late { background:#fee2e2; color:#b91c1c; }
        .muted { color:#6b7280; font-size:13px; }
        .book-flex { display:flex; gap:10px; align-items:center; }
        .cover { width:42px; height:56px; border-radius:10px; background:#e5e7eb; object-fit:cover; }
        .page-title { margin:0; font-size:24px; font-weight:800; }
        .page-sub { margin:6px 0 0; }
    </style>
</head>
<body>

<div class="app-container">

    <!-- SIDEBAR (samakan dengan yang lain) -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon"><i data-feather="book"></i></div>
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
            <a href="<?= base_url('member/return') ?>" class="nav-item">
                <i data-feather="corner-down-left"></i> <span>Pengembalian Buku</span>
            </a>
            <a href="<?= base_url('member/history') ?>" class="nav-item active">
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
                <h1 class="page-title">Riwayat Transaksi</h1>
                <p class="page-sub muted">Berikut daftar peminjaman & pengembalian buku kamu.</p>
            </div>
        </section>

        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $r): ?>
                        <?php
                            $title  = $r['book_title'] ?? 'Buku';
                            $author = $r['book_author'] ?? '-';

                            $status = strtolower((string)($r['status'] ?? 'borrowed'));

                            $badgeClass = 'b-active';
                            $badgeText  = 'Aktif';

                            if ($status === 'returned') {
                                $badgeClass = 'b-return';
                                $badgeText  = 'Dikembalikan';
                            } elseif ($status === 'late') {
                                $badgeClass = 'b-late';
                                $badgeText  = 'Terlambat';
                            }

                            $fine = (int)($r['fine_amount'] ?? 0);

                            // cover optional (kalau kamu sudah punya cover lokal/URL)
                            $cover = $r['book_cover'] ?? '';
                            $coverUrl = '';
                            if ($cover) {
                                if (preg_match('#^https?://#i', $cover)) {
                                    $coverUrl = $cover;
                                } else {
                                    $coverUrl = base_url('assets/img/books/' . ltrim($cover, '/'));
                                }
                            } else {
                                $coverUrl = "https://ui-avatars.com/api/?name=" . urlencode($title) . "&background=3DB2FF&color=fff&size=128";
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="book-flex">
                                    <img class="cover" src="<?= esc($coverUrl) ?>" alt="<?= esc($title) ?>">
                                    <div>
                                        <div style="font-weight:800;"><?= esc($title) ?></div>
                                        <div class="muted"><?= esc($author) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= esc($r['borrow_date'] ?? '-') ?></td>
                            <td><?= esc($r['due_date'] ?? '-') ?></td>
                            <td><?= esc($r['return_date'] ?? '-') ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                            <td><?= $fine > 0 ? 'Rp ' . number_format($fine, 0, ',', '.') : 'Rp 0' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="muted">Belum ada riwayat transaksi.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>

<script>feather.replace()</script>
</body>
</html>
