<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - <?= esc($book['title']) ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/member-books.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/book_detail.css') ?>?v=<?= time() ?>">

    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<div class="app-container">

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
                <a href="<?= base_url('member/books') ?>" class="nav-item active">
                    <i data-feather="search"></i> <span>Cari Buku</span>
                </a>
            </nav>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="date-display">
                <i data-feather="calendar"></i>
                <span><?= date('l, d F Y') ?></span>
            </div>
            <div class="header-actions">
                <button class="icon-btn">
                    <i data-feather="bell"></i><span class="dot"></span>
                </button>
            </div>
        </header>

        <a href="<?= base_url('member/books') ?>" class="back-link">
            <i data-feather="arrow-left" style="width:18px"></i> Kembali ke Katalog
        </a>

        <section class="detail-wrapper">
            <div class="detail-cover">
                <?php
                    $coverUrl = !empty($book['cover']) ? $book['cover'] : base_url('assets/img/no-book.png');
                ?>
                <img src="<?= $coverUrl ?>" alt="<?= esc($book['title']) ?>">
            </div>

            <div class="detail-info">
                <h2><?= esc($book['title']) ?></h2>
                <p class="author">Penulis: <?= esc($book['author']) ?></p>

                <div class="info-grid">
                    <div class="info-item">
                        <strong>Kategori</strong>
                        <span><?= esc($book['genre'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Penerbit</strong>
                        <span><?= esc($book['publisher'] ?? '-') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Tahun</strong>
                        <span><?= esc($book['year'] ?? '-') ?></span>
                    </div>

                    <div class="info-item">
                        <strong>Stok Tersedia</strong>
                        <span style="color: <?= ($book['stock_available'] > 0) ? '#166534' : '#DC2626' ?>">
                            <?= esc($book['stock_available']) ?> Eks
                        </span>
                    </div>
                </div>

                <div class="description">
                    <strong>Sinopsis:</strong><br>
                    <?= nl2br(esc($book['description'] ?? 'Belum ada deskripsi.')) ?>
                </div>

                <?php if ($book['stock_available'] > 0): ?>
                    <form action="<?= base_url('member/borrow/confirm') ?>" method="post">
                        <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                        <button type="submit" class="btn-action">
                            Pinjam Buku Sekarang
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn-action btn-disabled">
                        Stok Habis
                    </button>
                <?php endif; ?>
            </div>
        </section>

    </main>
</div>

<script>feather.replace()</script>
</body>
</html>
