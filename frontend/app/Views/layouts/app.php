<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= esc($title ?? 'BookHouse') ?></title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- IMPORTANT: pastikan file ini ada di public/assets/css/app.css -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>

<body>
    <div class="app-shell">

        <?= $this->include('partials/sidebar') ?>

        <main class="app-main">

            <!-- TOPBAR -->
            <header class="tb">
                <div class="tb-left">
                    <div class="tb-breadcrumb">
                        <span><?= esc($crumbParent ?? 'Librarian') ?></span>
                        <span class="tb-sep">/</span>
                        <span><?= esc($crumbPage ?? 'Dashboard') ?></span>
                    </div>

                    <div>
                        <div class="tb-title"><?= esc($tbTitle ?? ($title ?? 'BookHouse')) ?></div>
                        <div class="tb-desc"><?= esc($tbDesc ?? 'Kelola data perpustakaan dengan rapi dan cepat.') ?></div>
                    </div>
                </div>

                <div class="tb-right">
                    <div class="tb-status">
                        <span class="tb-dot"></span>
                        <span>System Online</span>
                    </div>

                    <div class="tb-user">
                        <div class="tb-avatar" aria-hidden="true"></div>
                        <div>
                            <div class="tb-username"><?= esc($userName ?? 'Librarian') ?></div>
                            <div class="tb-userrole"><?= esc($userRole ?? 'Staff') ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="app-content">
                <?= $this->renderSection('content') ?>
            </div>

        </main>
    </div>

    <!-- JS kecil untuk dropdown menu di Books -->
    <script>
        // toggle menu "..." per row
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-bx-toggle]');
            const allMenus = document.querySelectorAll('.bx-menu.open');

            // klik tombol dots -> toggle menu row itu
            if (btn) {
                const id = btn.getAttribute('data-bx-toggle');
                const menu = document.querySelector('[data-bx-menu="' + id + '"]');

                // tutup menu lain dulu
                allMenus.forEach(m => {
                    if (m !== menu) m.classList.remove('open');
                });

                if (menu) menu.classList.toggle('open');
                e.preventDefault();
                return;
            }

            // klik di luar -> tutup semua
            allMenus.forEach(m => m.classList.remove('open'));
        });
    </script>
</body>

</html>