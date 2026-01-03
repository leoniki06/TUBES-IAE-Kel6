<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
// ===== Data dari controller (amanin default) =====
$books  = $books ?? [];
$meta   = $meta ?? ['current_page' => 1, 'last_page' => 1, 'total' => count($books)];
$search = $search ?? (string) (service('request')->getGet('search') ?? '');

$current = (int)($meta['current_page'] ?? 1);
$last    = (int)($meta['last_page'] ?? 1);
$total   = (int)($meta['total'] ?? count($books));

// ===== Query params (konsep "toolbar seperti referensi") =====
$req = service('request');

$perPage  = (int)($req->getGet('perPage') ?? 10);
$perPage  = in_array($perPage, [10, 20, 50], true) ? $perPage : 10;

$category = (string)($req->getGet('category') ?? 'all');
$sort     = (string)($req->getGet('sort') ?? 'new'); // optional (new/old/stock)

// helper: build query string preserve state
$qsBuild = function (array $extra = []) use ($search, $perPage, $category, $sort, $current) {
    $q = [
        'search'   => $search ?: null,
        'perPage'  => $perPage ?: null,
        'category' => ($category && $category !== 'all') ? $category : null,
        'sort'     => $sort ?: null,
        'page'     => $current ?: 1,
    ];
    foreach ($extra as $k => $v) $q[$k] = $v;

    // bersihin null
    $q = array_filter($q, fn($v) => $v !== null && $v !== '');
    return $q ? ('?' . http_build_query($q)) : '';
};

// helper avatar initial
$initial = function ($title) {
    $t = trim((string)$title);
    if ($t === '') return 'B';
    return strtoupper(mb_substr($t, 0, 1));
};

// (opsional) daftar kategori untuk filter UI.
// kalau controller sudah kirim $categories, pakai itu.
// kalau belum, fallback ambil dari data $books.
$categories = $categories ?? [];
if (empty($categories)) {
    $tmp = [];
    foreach ($books as $b) {
        $c = (string)($b['category'] ?? '');
        if ($c !== '') $tmp[$c] = true;
    }
    $categories = array_keys($tmp);
    sort($categories);
}
?>

<div class="bx-page">

    <!-- Title bar (mirip referensi: judul kiri, tombol utama kanan) -->
    <div class="bx-titlebar">
        <div>
            <h1 class="bx-title">Books</h1>
            <p class="bx-sub">Kelola katalog buku: tambah, edit, hapus, dan lihat detail.</p>
        </div>

        <a class="btn btn-secondary" href="<?= base_url('librarian/books/create') ?>">
            + Add New Book
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert error"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <!-- Surface Card -->
    <div class="card bx-card">

        <!-- Toolbar: Search + Showing + Filter + Export + Total -->
        <div class="bx-toolbar">

            <form class="bx-left" method="get" action="<?= base_url('librarian/books') ?>" style="gap:12px;">
                <!-- Search -->
                <div class="bx-search" role="search" style="min-width:min(520px, 92vw);">
                    <span class="bx-ic">⌕</span>
                    <input
                        type="text"
                        name="search"
                        placeholder="Search title / author..."
                        value="<?= esc($search) ?>"
                        autocomplete="off">
                </div>

                <!-- Showing / perPage (ref: dropdown kecil) -->
                <label class="inline" style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:12px; font-weight:900; color:var(--muted);">Showing</span>
                    <select class="bx-select" name="perPage" onchange="this.form.submit()">
                        <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $perPage === 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </label>

                <!-- Filter category (ref: tombol filter) -->
                <select class="bx-select" name="category" onchange="this.form.submit()" title="Filter category">
                    <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>All Categories</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= esc($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= esc($c) ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Sort (optional, tetep clean) -->
                <select class="bx-select" name="sort" onchange="this.form.submit()" title="Sort">
                    <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Newest</option>
                    <option value="old" <?= $sort === 'old' ? 'selected' : '' ?>>Oldest</option>
                    <option value="stock" <?= $sort === 'stock' ? 'selected' : '' ?>>Lowest Stock</option>
                </select>

                <!-- Action buttons -->
                <button class="btn btn-ghost" type="submit">Search</button>

                <?php if ($search || $category !== 'all' || $perPage !== 10 || $sort !== 'new'): ?>
                    <a class="btn btn-ghost" href="<?= base_url('librarian/books') ?>">Reset</a>
                <?php endif; ?>

                <!-- Export (dummy link dulu) -->
                <a class="btn btn-ghost" href="#" onclick="return false;" title="Export (soon)">Export</a>
            </form>

            <div class="bx-right">
                <div class="bx-pill">Total: <?= esc($total) ?></div>
            </div>
        </div>

        <!-- Table -->
        <div class="bx-tablewrap">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th style="width:90px;">ID</th>
                        <th>Book</th>
                        <th style="width:220px;">Author</th>
                        <th style="width:170px;">Category</th>
                        <th style="width:120px;">Stock</th>
                        <th style="width:90px; text-align:right;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="6">
                                <div class="bx-empty">
                                    <div class="t">Tidak ada data</div>
                                    <div class="d">Coba tambah buku baru atau ubah keyword pencarian.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $b): ?>
                            <?php
                            $id = (int)($b['id'] ?? 0);
                            $title = (string)($b['title'] ?? '-');
                            $author = (string)($b['author'] ?? '-');
                            $cat = (string)($b['category'] ?? '-');
                            $stock = (int)($b['stock'] ?? 0);

                            $low = $stock <= 2;
                            $rowKey = 'row-' . $id;
                            ?>
                            <tr>
                                <td class="bx-id">#<?= esc($id) ?></td>

                                <td>
                                    <div class="bx-bookcell">
                                        <div class="bx-avatar" aria-hidden="true"><?= esc($initial($title)) ?></div>
                                        <div style="min-width:0;">
                                            <div class="bx-booktitle" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                <?= esc($title) ?>
                                            </div>
                                            <div class="bx-booksub">
                                                ID: <span class="bx-id">#<?= esc($id) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:900; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <?= esc($author) ?>
                                    </div>
                                    <div class="bx-booksub">Author</div>
                                </td>

                                <td>
                                    <span class="bx-tag"><?= esc($cat) ?></span>
                                </td>

                                <td>
                                    <span class="bx-stock <?= $low ? 'low' : '' ?>"><?= esc($stock) ?></span>
                                </td>

                                <td style="text-align:right;">
                                    <div class="bx-actions">
                                        <button
                                            class="bx-dotbtn"
                                            type="button"
                                            data-bx-toggle="<?= esc($rowKey) ?>"
                                            aria-label="Open menu">
                                            <span class="dots">⋯</span>
                                        </button>

                                        <div class="bx-menu" data-bx-menu="<?= esc($rowKey) ?>">
                                            <a href="<?= base_url('librarian/books/' . $id) ?>">👁 Detail</a>
                                            <a href="<?= base_url('librarian/books/' . $id . '/edit') ?>">✏ Edit</a>

                                            <form
                                                method="post"
                                                action="<?= base_url('librarian/books/' . $id . '/delete') ?>"
                                                onsubmit="return confirm('Yakin hapus buku ini?');">
                                                <?= csrf_field() ?>
                                                <button class="danger" type="submit">🗑 Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (ref: kiri Previous, tengah nomor, kanan Next) -->
        <div class="bx-paging">
            <a
                class="bx-pagebtn <?= $current <= 1 ? 'disabled' : '' ?>"
                href="<?= $current <= 1 ? '#' : base_url('librarian/books' . $qsBuild(['page' => $current - 1])) ?>">
                Previous
            </a>

            <div class="bx-pages">
                <?php for ($p = max(1, $current - 2); $p <= min($last, $current + 2); $p++): ?>
                    <a
                        class="bx-p <?= $p === $current ? 'active' : '' ?>"
                        href="<?= base_url('librarian/books' . $qsBuild(['page' => $p])) ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>
            </div>

            <a
                class="bx-pagebtn <?= $current >= $last ? 'disabled' : '' ?>"
                href="<?= $current >= $last ? '#' : base_url('librarian/books' . $qsBuild(['page' => $current + 1])) ?>">
                Next
            </a>
        </div>

    </div>
</div>

<!-- Dropdown menu logic (klik “⋯” seperti referensi) -->
<script>
    (function() {
        const closeAll = () => {
            document.querySelectorAll('.bx-menu.open').forEach(m => m.classList.remove('open'));
        };

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-bx-toggle]');
            const insideMenu = e.target.closest('[data-bx-menu]');

            if (btn) {
                const key = btn.getAttribute('data-bx-toggle');
                const menu = document.querySelector('[data-bx-menu="' + key + '"]');
                if (!menu) return;

                const isOpen = menu.classList.contains('open');
                closeAll();
                if (!isOpen) menu.classList.add('open');
                e.preventDefault();
                return;
            }

            // klik di luar menu => tutup
            if (!insideMenu) closeAll();
        });

        // ESC to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAll();
        });
    })();
</script>

<?= $this->endSection() ?>