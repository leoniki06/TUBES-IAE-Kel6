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

    <link rel="stylesheet" href="<?= base_url('assets/css/member-dashboard.css') ?>">
</head>
<body>

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
                    <span class="u-name"><?= session('user.name') ?? 'Tery' ?></span>
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
                <h1>Halo, <?= session('user.name') ?? 'Tery' ?>! 👋</h1>
                <p>Selamat datang kembali di portal perpustakaan.</p>
            </div>

            <div class="alert-wrapper">
                <div class="alert-box warning">
                    <i data-feather="alert-circle"></i>
                    <span><strong>Perhatian:</strong> Buku "Clean Code" akan jatuh tempo 2 hari lagi.</span>
                </div>
            </div>
        </section>

        <section class="stats-grid">
            <a href="<?= base_url('member/borrowed') ?>" class="stat-card blue">
                <div class="stat-icon"><i data-feather="book-open"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Buku Dipinjam</span>
                    <h3 class="stat-value">2 Buku</h3>
                    <small class="stat-note">Sedang dibaca</small>
                </div>
            </a>
            <a href="<?= base_url('member/borrowed') ?>" class="stat-card orange">
                <div class="stat-icon"><i data-feather="clock"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Jatuh Tempo</span>
                    <h3 class="stat-value">20 Jan</h3>
                    <small class="stat-note">Terdekat</small>
                </div>
            </a>
            <a href="<?= base_url('member/transactions') ?>" class="stat-card pink">
                <div class="stat-icon"><i data-feather="alert-triangle"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Denda Aktif</span>
                    <h3 class="stat-value">Rp 0</h3>
                    <small class="stat-note">Aman terkendali</small>
                </div>
            </a>
            <a href="<?= base_url('member/history') ?>" class="stat-card purple">
                <div class="stat-icon"><i data-feather="archive"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Transaksi</span>
                    <h3 class="stat-value">14</h3>
                    <small class="stat-note">Buku telah dibaca</small>
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
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="book-flex">
                                        <img src="https://m.media-amazon.com/images/I/41xShlnTZTL._SX376_BO1,204,203,200_.jpg" class="t-cover">
                                        <div>
                                            <div class="t-title">Clean Code</div>
                                            <div class="t-sub">Robert C. Martin</div>
                                        </div>
                                    </div>
                                </td>
                                <td>13 Jan</td>
                                <td>20 Jan</td>
                                <td><span class="badge active">🟢 Aktif</span></td>
                                <td>
                                    <a href="<?= base_url('member/return/1') ?>" class="btn-sm btn-primary">Kembalikan</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="book-flex">
                                        <img src="https://m.media-amazon.com/images/I/51k+3k0k0iL._SX377_BO1,204,203,200_.jpg" class="t-cover">
                                        <div>
                                            <div class="t-title">Refactoring</div>
                                            <div class="t-sub">Martin Fowler</div>
                                        </div>
                                    </div>
                                </td>
                                <td>01 Jan</td>
                                <td>08 Jan</td>
                                <td><span class="badge late">🔴 Terlambat</span></td>
                                <td>
                                    <a href="<?= base_url('member/pay-fine/2') ?>" class="btn-sm btn-outline-danger">Bayar Denda</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="side-column">
                <div class="section-head">
                    <h3>Aktivitas Terakhir</h3>
                </div>
                <div class="history-list">
                    <div class="h-item">
                        <div class="h-icon return"><i data-feather="check"></i></div>
                        <div class="h-info">
                            <div class="h-title">Atomic Habits</div>
                            <div class="h-date">Dikembalikan • Kemarin</div>
                        </div>
                    </div>
                    <div class="h-item">
                        <div class="h-icon borrow"><i data-feather="arrow-up-right"></i></div>
                        <div class="h-info">
                            <div class="h-title">Design Patterns</div>
                            <div class="h-date">Dipinjam • 12 Jan 2026</div>
                        </div>
                    </div>
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
                <a href="<?= base_url('book/detail/1') ?>" class="reco-card">
                    <div class="reco-img" style="background-image: url('https://m.media-amazon.com/images/I/81wgcld4wxL._AC_UF1000,1000_QL80_.jpg')"></div>
                    <div class="reco-content">
                        <h4>Atomic Habits</h4>
                        <span>James Clear</span>
                    </div>
                </a>
                <a href="<?= base_url('book/detail/2') ?>" class="reco-card">
                    <div class="reco-img" style="background-image: url('https://m.media-amazon.com/images/I/51W1sBPO7tL._SX380_BO1,204,203,200_.jpg')"></div>
                    <div class="reco-content">
                        <h4>The Pragmatic Programmer</h4>
                        <span>Andrew Hunt</span>
                    </div>
                </a>
                <a href="<?= base_url('book/detail/3') ?>" class="reco-card">
                    <div class="reco-img" style="background-image: url('https://m.media-amazon.com/images/I/51E2055ZGUL._AC_UF1000,1000_QL80_.jpg')"></div>
                    <div class="reco-content">
                        <h4>Design Patterns</h4>
                        <span>Erich Gamma</span>
                    </div>
                </a>
                <a href="<?= base_url('book/detail/4') ?>" class="reco-card">
                    <div class="reco-img" style="background-image: url('https://m.media-amazon.com/images/I/713jIoMO3UL._AC_UF1000,1000_QL80_.jpg')"></div>
                    <div class="reco-content">
                        <h4>Sapiens</h4>
                        <span>Yuval Noah Harari</span>
                    </div>
                </a>
                 <a href="<?= base_url('book/detail/5') ?>" class="reco-card">
                    <div class="reco-img" style="background-image: url('https://m.media-amazon.com/images/I/81q6-h7-yXL._AC_UF1000,1000_QL80_.jpg')"></div>
                    <div class="reco-content">
                        <h4>Company of One</h4>
                        <span>Paul Jarvis</span>
                    </div>
                </a>
            </div>
        </section>

    </main>
</div>

<script>feather.replace()</script>
</body>
</html>
