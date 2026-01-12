<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Buku - BookHouse</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/member-books.css') ?>?v=<?= time() ?>">
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        .book-cover {
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
        }
    </style>
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
                            $title  = $b['title'] ?? 'Buku';
                            $author = $b['author'] ?? '-';

                            // ===== COVER RESOLVER (LOKAL + URL) =====
                            $coverRaw = $b['cover'] ?? ($b['cover_url'] ?? '');
                            $fallbackLocal = base_url('assets/img/books/laskar-pelangi.jpg');
                            $fallbackAvatar = "https://ui-avatars.com/api/?name=" . urlencode($title) . "&background=3DB2FF&color=fff&size=512";

                            if (!empty($coverRaw)) {
                                // URL eksternal
                                if (preg_match('/^https?:\/\//', $coverRaw)) {
                                    $cover = $coverRaw;
                                }
                                // Sudah path assets/...
                                else if (strpos($coverRaw, 'assets/') === 0) {
                                    $cover = base_url($coverRaw);
                                }
                                // Cuma nama file -> arahkan ke folder public/assets/img/books/
                                else {
                                    $cover = base_url('assets/img/books/' . ltrim($coverRaw, '/'));
                                }
                            } else {
                                $cover = $fallbackLocal;
                            }
                            ?>

                            <div class="book-card">
                                <div class="book-cover js-book-cover"
                                    style="background-image:url('<?= esc($cover) ?>')"
                                    data-fallback="<?= esc($fallbackLocal) ?>"
                                    data-avatar="<?= esc($fallbackAvatar) ?>">
                                </div>

                                <div class="book-info">
                                    <h4><?= esc($title) ?></h4>
                                    <span><?= esc($author) ?></span>

                                    <div class="book-meta">
                                        <small><?= esc($b['genre'] ?? 'Umum') ?></small>
                                    </div>

                                    <a href="<?= base_url('member/books/detail/' . ($b['id'] ?? 0)) ?>" class="btn-detail">
                                        Lihat Detail
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

        // cek load background-image, kalau error swap ke fallback lokal, lalu avatar
        document.querySelectorAll('.js-book-cover').forEach(function(el) {
            const bg = el.style.backgroundImage;
            const url = bg.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
            const fallback = el.getAttribute('data-fallback');
            const avatar = el.getAttribute('data-avatar');

            const img = new Image();
            img.onerror = function() {
                const img2 = new Image();
                img2.onerror = function() {
                    el.style.backgroundImage = `url('${avatar}')`;
                };
                img2.onload = function() {
                    el.style.backgroundImage = `url('${fallback}')`;
                };
                img2.src = fallback;
            };
            img.src = url;
        });
    </script>
</body>

</html>
