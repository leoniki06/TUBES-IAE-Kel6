<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BookHouse' ?></title>

    <script src="https://unpkg.com/feather-icons"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/member-dashboard.css') ?>">

    <?= $this->renderSection('css') ?>
</head>
<body>

<div class="app-container">

    <?php
        $role = session('user.role') ?? session('role') ?? 'member';
        $isMember = (strtolower($role) === 'member');
    ?>

    <?php if ($isMember): ?>
        <?= $this->include('partials/member_sidebar') ?>
    <?php else: ?>
        <?= $this->include('partials/sidebar') ?>
    <?php endif; ?>

    <main class="main-content">
        <?php if ($isMember): ?>
            <?= $this->include('partials/member_topbar') ?>
        <?php else: ?>
            <?= $this->include('partials/topbar') ?>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

</div>

<script>
    feather.replace();
</script>

<script>
    /**
     * ✅ TOKEN SYNC ONLY (TIDAK ADA REDIRECT PAKSA)
     * - Simpan token dari session flashdata jika ada
     * - Kalau localStorage kosong dan session ada -> isi
     * - Tidak melakukan redirect otomatis ke "/"
     */
    <?php if (session()->getFlashdata('token_baru')): ?>
        localStorage.setItem('token', '<?= session()->getFlashdata('token_baru') ?>');
    <?php endif; ?>

    <?php if (session()->get('token')): ?>
        if (!localStorage.getItem('token')) {
            localStorage.setItem('token', '<?= session()->get('token') ?>');
        }
    <?php endif; ?>
</script>

</body>
</html>
