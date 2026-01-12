<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/librarian-books.css') ?>">

<?php
$errors = session()->getFlashdata('errors') ?? [];

// pagination meta
$current = (int) ($meta['current_page'] ?? 1);
$last    = (int) ($meta['last_page'] ?? 1);

// query string
$searchVal = (string) ($search ?? '');
$qs = $searchVal !== '' ? '&search=' . urlencode($searchVal) : '';

// flash
$flashSuccess = session()->getFlashdata('success');
$flashError   = session()->getFlashdata('error');

// items
$totalItems = (int)($totalItems ?? ($meta['total'] ?? count($books ?? [])));

// genres list
$genres = [
    'Fiksi',
    'Non-Fiksi',
    'Novel',
    'Sastra',
    'Pendidikan',
    'Ilmu Pengetahuan',
    'Teknologi & Komputer',
    'Sejarah',
    'Biografi',
    'Agama',
    'Ekonomi & Bisnis',
    'Psikologi',
    'Kesehatan',
    'Hukum & Politik',
    'Anak & Remaja',
];
?>

<div class="bx-page">

    <div class="bx-titlebar">
        <div>
            <h1 class="bx-title">Books</h1>
            <p class="bx-sub">Kelola katalog buku: tambah, edit, hapus, dan lihat info.</p>
        </div>

        <button class="bx-btn primary" type="button" data-open="modal-add">
            <span class="bx-plus">+</span> Add New Book
        </button>
    </div>

    <!-- Toasts -->
    <div class="bx-toasts" aria-live="polite" aria-atomic="true">
        <?php if ($flashSuccess): ?>
            <div class="bx-toast success js-toast" role="status" data-autohide="3500">
                <div class="bx-toast-title">Success</div>
                <div class="bx-toast-msg"><?= esc($flashSuccess) ?></div>
                <button type="button" class="bx-toast-close" data-toast-close aria-label="Close">✕</button>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="bx-toast error js-toast" role="alert" data-autohide="4500">
                <div class="bx-toast-title">Error</div>
                <div class="bx-toast-msg"><?= esc($flashError) ?></div>
                <button type="button" class="bx-toast-close" data-toast-close aria-label="Close">✕</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="bx-card">
        <!-- Toolbar -->
        <div class="bx-toolbar">
            <div class="bx-left">
                <form id="booksSearchForm" class="bx-search" method="get" action="<?= base_url('librarian/books') ?>">
                    <span class="bx-ic" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M10.5 19a8.5 8.5 0 1 1 0-17 8.5 8.5 0 0 1 0 17Z" stroke="currentColor" stroke-width="2" />
                            <path d="M16.8 16.8 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                    <input
                        id="booksSearchInput"
                        name="search"
                        placeholder="Search title / author / isbn / publisher / genre..."
                        value="<?= esc($searchVal) ?>">
                </form>

                <?php if ($searchVal !== ''): ?>
                    <a class="bx-btn" href="<?= base_url('librarian/books') ?>">Reset</a>
                <?php endif; ?>
            </div>

            <div class="bx-right">
                <div class="bx-pill"><?= $totalItems ?> items</div>
            </div>
        </div>

        <!-- Table -->
        <div class="bx-tablewrap">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th style="width:160px;">Genre</th>
                        <th>Book</th>
                        <th style="width:160px;">Author</th>
                        <th style="width:170px;">Publisher</th>
                        <th style="width:110px;">Year</th>
                        <th style="width:140px;">Stock</th>
                        <th style="width:120px; text-align:right;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="7" style="padding:0; border:none; background:transparent; box-shadow:none;">
                                <div class="bx-empty">
                                    <div class="t">Tidak ada data</div>
                                    <div class="d">Coba cari kata lain atau tambah buku baru.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $b): ?>
                            <?php
                            $id        = (int)($b['id'] ?? 0);
                            $title     = (string)($b['title'] ?? '-');
                            $author    = (string)($b['author'] ?? '-');
                            $publisher = (string)($b['publisher'] ?? '-');
                            $genre     = (string)($b['genre'] ?? '-');
                            $year      = (int)($b['year'] ?? 0);
                            $isbn      = (string)($b['isbn'] ?? '');

                            $total     = (int)($b['stock_total'] ?? 0);
                            $avail     = (int)($b['stock_available'] ?? 0);

                            // ✅ cover dari Laravel (Book model appends cover_url)
                            $coverUrl  = (string)($b['cover_url'] ?? '');

                            // low stock: <=20% atau avail=0
                            $isLow = $avail <= 0 || ($total > 0 && ($avail / max(1, $total)) <= 0.2);

                            $firstChar = '-';
                            $tTrim = trim($title);
                            if ($tTrim !== '' && $tTrim !== '-') {
                                $firstChar = strtoupper(mb_substr($tTrim, 0, 1));
                            }
                            ?>
                            <tr>
                                <td><span class="bx-genre"><?= esc($genre ?: '-') ?></span></td>

                                <td>
                                    <div class="bx-bookcell">
                                        <?php if ($coverUrl !== ''): ?>
                                            <img class="bx-coverthumb" src="<?= esc($coverUrl) ?>" alt="Cover">
                                        <?php else: ?>
                                            <div class="bx-avatar"><?= esc($firstChar) ?></div>
                                        <?php endif; ?>

                                        <div style="min-width:0;">
                                            <div class="bx-booktitle"><?= esc($title) ?></div>
                                            <div class="bx-booksub"><span class="bx-strong">ISBN:</span> <?= esc($isbn ?: '-') ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td><?= esc($author) ?></td>
                                <td><?= esc($publisher ?: '-') ?></td>
                                <td><?= esc($year ?: '-') ?></td>

                                <td>
                                    <span class="bx-stock <?= $isLow ? 'low' : '' ?>">
                                        <?= esc($avail) ?>/<?= esc($total) ?>
                                    </span>
                                </td>

                                <td style="text-align:right;">
                                    <div class="bx-actions">
                                        <button
                                            type="button"
                                            class="bx-dotbtn"
                                            data-open="modal-edit"
                                            data-id="<?= esc($id) ?>"
                                            data-isbn="<?= esc($isbn) ?>"
                                            data-title="<?= esc($title) ?>"
                                            data-author="<?= esc($author) ?>"
                                            data-publisher="<?= esc($publisher) ?>"
                                            data-genre="<?= esc($genre) ?>"
                                            data-year="<?= esc($year) ?>"
                                            data-stock_total="<?= esc($total) ?>"
                                            data-stock_available="<?= esc($avail) ?>"
                                            data-cover_url="<?= esc($coverUrl) ?>"
                                            title="Edit">
                                            <span class="dots">⋯</span>
                                        </button>

                                        <form method="post"
                                            action="<?= base_url('librarian/books/' . $id . '/delete') ?>"
                                            onsubmit="return confirm('Yakin hapus buku ini?');"
                                            style="display:inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="bx-dotbtn" style="color:var(--c-danger);" title="Delete">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($last > 1): ?>
            <?php
            $start = max(1, $current - 2);
            $end   = min($last, $current + 2);
            ?>
            <div class="bx-paging">
                <a class="bx-pagebtn <?= $current <= 1 ? 'disabled' : '' ?>"
                    href="<?= $current <= 1 ? '#' : base_url('librarian/books?page=' . ($current - 1) . $qs) ?>">
                    Previous
                </a>

                <div class="bx-pages">
                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <a class="bx-p <?= $p === $current ? 'active' : '' ?>"
                            href="<?= base_url('librarian/books?page=' . $p . $qs) ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <a class="bx-pagebtn <?= $current >= $last ? 'disabled' : '' ?>"
                    href="<?= $current >= $last ? '#' : base_url('librarian/books?page=' . ($current + 1) . $qs) ?>">
                    Next
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Add -->
<div class="m-overlay" id="modal-add">
    <div class="m" role="dialog" aria-modal="true">
        <div class="m-head">
            <div class="m-title">Add New Book</div>
            <button class="m-close" type="button" data-close>✕</button>
        </div>

        <!-- ✅ enctype wajib untuk upload cover -->
        <form method="post" id="addForm" action="<?= base_url('librarian/books') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="m-body">
                <?php if (!empty($errors)): ?>
                    <ul class="errlist">
                        <?php foreach ($errors as $e): ?>
                            <li><?= esc($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <div class="m-grid">
                    <div class="frow">
                        <label>ISBN</label>
                        <input name="isbn" value="<?= esc(old('isbn')) ?>" placeholder="ex: 978602032...">
                    </div>

                    <div class="frow">
                        <label>Year</label>
                        <input name="year" value="<?= esc(old('year')) ?>" placeholder="ex: 2021">
                    </div>

                    <div class="frow" style="grid-column:1/-1;">
                        <label>Title</label>
                        <input name="title" value="<?= esc(old('title')) ?>" placeholder="Judul buku">
                    </div>

                    <div class="frow">
                        <label>Author</label>
                        <input name="author" value="<?= esc(old('author')) ?>" placeholder="Nama penulis">
                    </div>

                    <div class="frow">
                        <label>Publisher (optional)</label>
                        <input name="publisher" value="<?= esc(old('publisher')) ?>" placeholder="Penerbit">
                    </div>

                    <div class="frow">
                        <label>Genre</label>
                        <select name="genre" required>
                            <option value="">-- Pilih Genre --</option>
                            <?php foreach ($genres as $g): ?>
                                <option value="<?= esc($g) ?>" <?= old('genre') === $g ? 'selected' : '' ?>>
                                    <?= esc($g) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="frow">
                        <label>Stock Total</label>
                        <input type="number" min="0" name="stock_total" value="<?= esc(old('stock_total') ?? '0') ?>">
                        <div class="fhelp err" data-err="add_stock"></div>
                    </div>

                    <div class="frow">
                        <label>Stock Available</label>
                        <input type="number" min="0" name="stock_available" value="<?= esc(old('stock_available') ?? '0') ?>">
                    </div>

                    <!-- ✅ Cover -->
                    <div class="frow" style="grid-column:1/-1;">
                        <label>Cover (optional)</label>
                        <input type="file" name="cover" id="a_cover" accept=".jpg,.jpeg,.png,.webp">
                        <div class="fhelp">Max 2MB • JPG/JPEG/PNG/WEBP</div>
                    </div>
                </div>
            </div>

            <div class="m-foot">
                <button type="button" class="bx-btn" data-close>Cancel</button>
                <button type="submit" class="bx-btn primary js-submit">Save Book</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="m-overlay" id="modal-edit">
    <div class="m" role="dialog" aria-modal="true">
        <div class="m-head">
            <div class="m-title">Edit Book</div>
            <button class="m-close" type="button" data-close>✕</button>
        </div>

        <!-- ✅ enctype wajib untuk upload cover -->
        <form method="post" id="editForm" action="#" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="m-body">
                <div class="m-grid">
                    <div class="frow">
                        <label>ISBN</label>
                        <input name="isbn" id="e_isbn">
                    </div>

                    <div class="frow">
                        <label>Year</label>
                        <input name="year" id="e_year">
                    </div>

                    <div class="frow" style="grid-column:1/-1;">
                        <label>Title</label>
                        <input name="title" id="e_title">
                    </div>

                    <div class="frow">
                        <label>Author</label>
                        <input name="author" id="e_author">
                    </div>

                    <div class="frow">
                        <label>Publisher (optional)</label>
                        <input name="publisher" id="e_publisher">
                    </div>

                    <div class="frow">
                        <label>Genre</label>
                        <select name="genre" id="e_genre" required>
                            <option value="">-- Pilih Genre --</option>
                            <?php foreach ($genres as $g): ?>
                                <option value="<?= esc($g) ?>"><?= esc($g) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="frow">
                        <label>Stock Total</label>
                        <input type="number" min="0" name="stock_total" id="e_stock_total">
                        <div class="fhelp err" data-err="edit_stock"></div>
                    </div>

                    <div class="frow">
                        <label>Stock Available</label>
                        <input type="number" min="0" name="stock_available" id="e_stock_available">
                    </div>

                    <!-- ✅ Cover edit -->
                    <div class="frow" style="grid-column:1/-1;">
                        <label>Cover (optional)</label>

                        <div class="bx-coverrow">
                            <img id="e_cover_preview" class="bx-coverpreview" src="" alt="Cover preview" style="display:none;">
                            <div class="bx-covermeta" id="e_cover_meta">Tidak ada cover</div>
                        </div>

                        <input type="file" name="cover" id="e_cover" accept=".jpg,.jpeg,.png,.webp">

                        <label class="bx-check">
                            <input type="checkbox" name="remove_cover" value="1" id="e_remove_cover">
                            Hapus cover
                        </label>

                        <div class="fhelp">Upload file baru untuk mengganti cover.</div>
                    </div>
                </div>
            </div>

            <div class="m-foot">
                <button type="button" class="bx-btn" data-close>Cancel</button>
                <button type="submit" class="bx-btn primary js-submit">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.BOOKS_PAGE = {
        baseBooksUrl: "<?= base_url('librarian/books') ?>",
        openAddOnError: <?= session()->getFlashdata('open_add_modal') ? 'true' : 'false' ?>
    };
</script>

<script src="<?= base_url('assets/js/books.js') ?>" defer></script>
<script src="<?= base_url('assets/js/books-form.js') ?>" defer></script>

<?= $this->endSection() ?>
