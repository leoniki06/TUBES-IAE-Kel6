<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - BookHouse</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/member-books.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/book_detail.css') ?>?v=<?= time() ?>">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

    <div class="app-container">

        <!-- SIDEBAR -->
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
                    <button class="icon-btn">
                        <i data-feather="bell"></i>
                        <span class="dot"></span>
                    </button>
                </div>
            </header>

            <?php
            // DATA BOOK dari controller biasanya: $book
            $title  = $book['title'] ?? 'Buku';
            $author = $book['author'] ?? '-';
            $genre  = $book['genre'] ?? ($book['category'] ?? 'Umum');
            $publisher = $book['publisher'] ?? '-';
            $year   = $book['year'] ?? ($book['published_year'] ?? '-');
            $stock  = (int) ($book['stock_available'] ?? ($book['stock'] ?? 0));
            $desc   = $book['description'] ?? ($book['synopsis'] ?? 'Belum ada deskripsi.');

            // ===== COVER RESOLVER DETAIL =====
            $coverRaw = $book['cover'] ?? ($book['cover_url'] ?? '');

            // AUTO-MAP judul -> nama file lokal (public/assets/img/books/)
            // contoh: "Ayat-Ayat Cinta" -> "ayat-ayat-cinta.jpg"
            $slug = strtolower($title);
            $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
            $slug = trim($slug, '-');

            $localJpg = base_url('assets/img/books/' . $slug . '.jpg');
            $localPng = base_url('assets/img/books/' . $slug . '.png');
            $fallbackAvatar = "https://ui-avatars.com/api/?name=" . urlencode($title) . "&background=3DB2FF&color=fff&size=512";

            if (!empty($coverRaw)) {
                if (preg_match('/^https?:\/\//', $coverRaw)) {
                    $cover = $coverRaw;
                } else if (strpos($coverRaw, 'assets/') === 0) {
                    $cover = base_url($coverRaw);
                } else {
                    $cover = base_url('assets/img/books/' . ltrim($coverRaw, '/'));
                }
            } else {
                // default pakai slug (jpg dulu), nanti dicek JS kalau 404, coba png, lalu avatar
                $cover = $localJpg;
            }
            ?>

            <a href="<?= base_url('member/books') ?>" class="back-link">
                <i data-feather="arrow-left"></i> Kembali ke Katalog
            </a>

            <div class="detail-wrapper">

                <div class="detail-cover">
                    <img id="detailCover"
                         src="<?= esc($cover) ?>"
                         data-jpg="<?= esc($localJpg) ?>"
                         data-png="<?= esc($localPng) ?>"
                         data-avatar="<?= esc($fallbackAvatar) ?>"
                         alt="<?= esc($title) ?>">
                </div>

                <div class="detail-info">
                    <h2><?= esc($title) ?></h2>
                    <div class="author">Penulis: <?= esc($author) ?></div>

                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Kategori</strong>
                            <span><?= esc($genre) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Penerbit</strong>
                            <span><?= esc($publisher) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Tahun</strong>
                            <span><?= esc($year) ?></span>
                        </div>
                        <div class="info-item">
                            <strong>Stok</strong>
                            <span style="color: <?= $stock > 0 ? '#16A34A' : '#DC2626' ?>;">
                                <?= $stock ?> Eks
                            </span>
                        </div>
                    </div>

                    <div class="description">
                        <strong>Sinopsis:</strong>
                        <?= esc($desc) ?>
                    </div>

                    <?php if ($stock > 0): ?>
                        <form action="<?= base_url('member/books/borrow/' . (int)($book['id'] ?? 0)) ?>" method="post" style="display:inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-action">
                                <i data-feather="shopping-bag"></i> Pinjam Buku Sekarang
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn-action btn-disabled" disabled>
                            Stok Habis
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>

    <script>
        feather.replace();

        // FIX cover detail:
        // 1) coba src dari data / jpg
        // 2) kalau error -> coba png
        // 3) kalau error -> avatar
        const img = document.getElementById('detailCover');
        img.addEventListener('error', function handler() {
            const jpg = img.getAttribute('data-jpg');
            const png = img.getAttribute('data-png');
            const avatar = img.getAttribute('data-avatar');

            if (img.src !== png) {
                img.src = png;
                return;
            }
            img.src = avatar;
            img.removeEventListener('error', handler);
        });
    </script>

</body>
</html>
