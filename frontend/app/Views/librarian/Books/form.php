<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
$isEdit = ($mode ?? '') === 'edit';
$id = $book['id'] ?? null;

// old input priority
$val = function (string $key, $fallback = '') use ($old, $book) {
    if (isset($old[$key])) return $old[$key];
    return $book[$key] ?? $fallback;
};
?>

<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= $isEdit ? 'Edit Book' : 'Add Book' ?></h1>
            <p class="page-sub"><?= $isEdit ? 'Perbarui data buku.' : 'Tambahkan buku baru ke katalog.' ?></p>
        </div>

        <a class="btn btn-ghost" href="<?= base_url('books') ?>">Back</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <b>Periksa input kamu:</b>
            <ul style="margin:8px 0 0 18px;">
                <?php foreach ($errors as $k => $v): ?>
                    <li><?= esc($v) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card card-pad">
        <form method="post" action="<?= $isEdit ? base_url('books/' . $id) : base_url('books') ?>" id="bookForm" novalidate>
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="grid2">
                <div class="form-group">
                    <label>Title *</label>
                    <input class="input" type="text" name="title" value="<?= esc($val('title')) ?>" required>
                    <div class="help error-text" data-err="title"></div>
                </div>

                <div class="form-group">
                    <label>Author *</label>
                    <input class="input" type="text" name="author" value="<?= esc($val('author')) ?>" required>
                    <div class="help error-text" data-err="author"></div>
                </div>

                <div class="form-group">
                    <label>Category *</label>
                    <input class="input" type="text" name="category" value="<?= esc($val('category')) ?>" required>
                    <div class="help error-text" data-err="category"></div>
                </div>

                <div class="form-group">
                    <label>Stock *</label>
                    <input class="input" type="number" name="stock" min="0" value="<?= esc($val('stock', 1)) ?>" required>
                    <div class="help error-text" data-err="stock"></div>
                </div>

                <div class="form-group">
                    <label>ISBN</label>
                    <input class="input" type="text" name="isbn" value="<?= esc($val('isbn')) ?>" placeholder="optional">
                    <div class="help error-text" data-err="isbn"></div>
                </div>

                <div class="form-group">
                    <label>Publisher</label>
                    <input class="input" type="text" name="publisher" value="<?= esc($val('publisher')) ?>" placeholder="optional">
                </div>

                <div class="form-group">
                    <label>Year</label>
                    <input class="input" type="number" name="year" min="0" value="<?= esc($val('year')) ?>" placeholder="optional">
                    <div class="help error-text" data-err="year"></div>
                </div>
            </div>

            <div class="form-group" style="margin-top:12px;">
                <label>Description</label>
                <textarea class="input" name="description" rows="4" placeholder="optional"><?= esc($val('description')) ?></textarea>
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Save Changes' : 'Create Book' ?></button>
                <?php if ($isEdit): ?>
                    <a class="btn btn-ghost" href="<?= base_url('books/' . $id) ?>">View Detail</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('assets/js/books-form.js') ?>"></script>
<?= $this->endSection() ?>