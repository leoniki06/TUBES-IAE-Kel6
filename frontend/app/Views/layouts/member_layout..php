<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'BookHouse' ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/member-base.css') ?>">
    <?= $this->renderSection('css') ?>

    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>

<div class="app-container">

    <?= $this->include('layouts/partials/sidebar') ?>

    <main class="main-content">
        <?= $this->include('layouts/partials/topbar') ?>
        <?= $this->renderSection('content') ?>
    </main>

</div>

<script>feather.replace()</script>
</body>
</html>
