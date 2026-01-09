<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Buku - BookHouse</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/member-books.css') ?>?v=<?= time() ?>">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

    <div class="app-container">

        <aside class="sidebar">
            <div>
                <div class="sidebar-header">
                    <div class="logo-icon">
                        <i data-feather="book-open"></i>
                    </div>
                    <div class="logo-text">BookHouse</div>
                </div>

                <nav class="sidebar-nav">
                    <a href="<?= base_url('member/dashboard') ?>" class="nav-item">
                        <i data-feather="grid"></i> <span>Dashboard</span>
                    </a>

                    <a href="<?= base_url('member/books') ?>" class="nav-item active">
                        <i data-feather="search"></i> <span>Cari Buku</span>
                    </a>

                    <a href="<?= base_url('member/borrowed') ?>" class="nav-item">
                        <i data-feather="layers"></i> <span>Buku Dipinjam</span>
                    </a>

                    <a href="<?= base_url('member/return') ?>" class="nav-item">
                        <i data-feather="corner-down-left"></i> <span>Pengembalian</span>
                    </a>

                    <a href="<?= base_url('member/history') ?>" class="nav-item">
                        <i data-feather="clock"></i> <span>Riwayat</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session('user.name') ?? 'Member') ?>&background=3DB2FF&color=fff"
                            alt="User">
                    </div>
                    <div class="user-details">
                        <span class="u-name"><?= esc(session('user.name') ?? 'Guest') ?></span>
                        <span class="u-role">Member Aktif</span>
                    </div>
                </div>
                <a href="<?= base_url('logout') ?>" class="btn-logout" title="Logout">
                    <i data-feather="log-out"></i>
                </a>
            </div>
        </aside>

        <main class="main-content">

            <header class="top-header">
                <div class="date-display">
                    <i data-feather="calendar"></i>
                    <span><?= date('l, d F Y') ?></span>
                </div>

                <form action="" method="get" class="search-bar">
                    <button type="submit" class="search-btn"><i data-feather="search"></i></button>
                    <input type="text" name="keyword" value="<?= esc($keyword ?? '') ?>"
                        placeholder="Cari judul buku, penulis, atau genre favoritmu..." autocomplete="off">
                </form>

                <div class="header-actions">
                    <button class="icon-btn">
                        <i data-feather="bell"></i>
                        <span class="dot"></span>
                    </button>
                </div>
            </header>

            <section class="content-section">
                <h3>Katalog Buku</h3>

                <div class="book-grid">
                    <?php if (!empty($books)): ?>
                        <?php foreach ($books as $b): ?>

                            <?php
                            // Logic Cover (gunakan placeholder jika kosong)
                            $cover = !empty($b['cover']) ? $b['cover'] : base_url('assets/img/no-book.png');
                            // Jika URL eksternal tidak valid, bisa pakai placeholder online
                            if (empty($b['cover'])) {
                                $cover = 'https://via.placeholder.com/300x450?text=No+Cover';
                            }
                            ?>

                            <div class="book-card">
                                <div class="book-cover" style="background-image:url('<?= esc($cover) ?>')"></div>

                                <div class="book-info">
                                    <h4><?= esc($b['title']) ?></h4>
                                    <span><?= esc($b['author']) ?></span>

                                    <div class="book-meta">
                                        <small><?= esc($b['genre'] ?? 'Umum') ?></small>
                                    </div>

                                    <a href="<?= base_url('member/books/detail/' . $b['id']) ?>" class="btn-detail">
                                        Lihat Detail
                                    </a>
                                    </a>
                                </div>
                            </div>

                        <?php endforeach ?>
                    <?php else: ?>
                        <div class="empty-text">
                            <i data-feather="frown" style="width: 48px; height: 48px; margin-bottom: 10px;"></i>
                            <p>Ups, buku yang kamu cari tidak ditemukan.</p>
                        </div>
                    <?php endif ?>
                </div>
            </section>

        </main>
    </div>

    <script>
        feather.replace();
    </script>
</body>

</html>
