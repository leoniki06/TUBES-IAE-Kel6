<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= esc($book['title'] ?? 'Book Detail') ?></h1>
            <p class="page-sub">Detail informasi buku.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a class="btn btn-ghost" href="<?= base_url('books') ?>">Back</a>
            <a class="btn btn-secondary" href="<?= base_url('books/' . ($book['id'] ?? 0) . '/edit') ?>">Edit</a>

            <form class="inline" method="post" action="<?= base_url('books/' . ($book['id'] ?? 0) . '/delete') ?>"
                onsubmit="return confirm('Yakin hapus buku ini?');">
                <?= csrf_field() ?>
                <button class="btn btn-ghost btn-danger" type="submit">Delete</button>
            </form>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <div class="card card-pad">
        <div class="grid2">
            <div class="field">
                <div class="f-label">Author</div>
                <div class="f-value"><?= esc($book['author'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="f-label">Category</div>
                <div class="f-value"><?= esc($book['category'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="f-label">ISBN</div>
                <div class="f-value"><?= esc($book['isbn'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="f-label">Publisher</div>
                <div class="f-value"><?= esc($book['publisher'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="f-label">Year</div>
                <div class="f-value"><?= esc($book['year'] ?? '-') ?></div>
            </div>
            <div class="field">
                <div class="f-label">Stock</div>
                <div class="f-value"><?= esc($book['stock'] ?? 0) ?></div>
            </div>
        </div>

        <div class="field" style="margin-top:16px;">
            <div class="f-label">Description</div>
            <div class="f-value" style="white-space:pre-wrap;"><?= esc($book['description'] ?? '-') ?></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>