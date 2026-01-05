<?php
$uri  = service('uri');
$path = trim($uri->getPath(), '/');

$uDash    = site_url('librarian/dashboard');
$uBooks   = site_url('librarian/books');
$uMembers = site_url('librarian/members');
$uTx      = site_url('librarian/transactions');
$uOverdue = $uTx . '?' . http_build_query(['status' => 'overdue']);

$isActive = function (string $route) use ($path): bool {
    $route = trim($route, '/');
    return ($path === $route) || str_starts_with($path, $route . '/');
};

$logoSrc = base_url('assets/img/logo-white.png');
?>

<aside class="sb3">
    <div class="sb3-top">
        <a class="sb3-logoWrap" href="<?= $uDash ?>" aria-label="BookHouse">
            <img class="sb3-logoImg" src="<?= $logoSrc ?>" alt="BookHouse"
                onerror="this.style.display='none'; this.parentElement.classList.add('is-fallback');" />
            <div class="sb3-logoFallback">
                <i class="fa-solid fa-book-open"></i>
            </div>
        </a>

        <div class="sb3-section">
            <i class="fa-solid fa-layer-group"></i>&nbsp; MAIN
        </div>

        <nav class="sb3-nav">
            <a class="sb3-item <?= $isActive('librarian/dashboard') ? 'is-active' : '' ?>" href="<?= $uDash ?>">
                <span class="sb3-ic"><i class="fa-solid fa-house"></i></span>
                <span class="sb3-label">Dashboard</span>
                <span class="sb3-live">Live</span>
            </a>

            <a class="sb3-item <?= $isActive('librarian/books') ? 'is-active' : '' ?>" href="<?= $uBooks ?>">
                <span class="sb3-ic"><i class="fa-solid fa-book"></i></span>
                <span class="sb3-label">Books</span>
            </a>

            <a class="sb3-item <?= $isActive('librarian/members') ? 'is-active' : '' ?>" href="<?= $uMembers ?>">
                <span class="sb3-ic"><i class="fa-solid fa-users"></i></span>
                <span class="sb3-label">Members</span>
            </a>

            <a class="sb3-item <?= $isActive('librarian/transactions') ? 'is-active' : '' ?>" href="<?= $uTx ?>">
                <span class="sb3-ic"><i class="fa-solid fa-right-left"></i></span>
                <span class="sb3-label">Transactions</span>
            </a>
        </nav>
    </div>

    <div class="sb3-bottom">
        <div class="sb3-focus">
            <div class="sb3-focus-head">
                <div>
                    <div class="sb3-focus-title">Today Focus</div>
                    <div class="sb3-focus-sub">Rapikan overdue biar denda terdata.</div>
                </div>
                <span class="sb3-focus-dot" aria-hidden="true"></span>
            </div>

            <div class="sb3-focus-actions">
                <a class="sb3-pill" href="<?= $uOverdue ?>">View Overdue</a>
                <a class="sb3-pill ghost" href="<?= $uBooks ?>">New Books</a>
            </div>
        </div>

        <a class="sb3-logout" href="<?= site_url('auth/logout') ?>">
            <span class="sb3-ic"><i class="fa-solid fa-right-from-bracket"></i></span>
            Logout
        </a>

        <div class="sb3-footnote">BookHouse • v1</div>
    </div>
</aside>
