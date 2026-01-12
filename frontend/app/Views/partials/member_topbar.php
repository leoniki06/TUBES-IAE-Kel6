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
