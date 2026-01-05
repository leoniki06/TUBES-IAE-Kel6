<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
$items   = $items ?? [];
$filters = $filters ?? ['search' => '', 'status' => '', 'from' => '', 'to' => '', 'dummy' => 0];
$flash   = $flash ?? [];

$baseTx = site_url('librarian/transactions');

$fmt = function ($v, $withTime = true) {
    if (!$v) return '—';
    $t = strtotime((string)$v);
    if ($t === false) return esc((string)$v);
    return $withTime ? date('d M Y H:i', $t) : date('d M Y', $t);
};

$money = function (int $n) {
    if ($n <= 0) return 'Rp 0';
    return 'Rp ' . number_format($n, 0, ',', '.');
};

$tone = function (string $s) {
    $s = strtolower($s);
    return match ($s) {
        'overdue'  => 'red',
        'borrowed' => 'blue',
        'returned' => 'green', // ✅ beda dari borrowed biar kebaca
        default    => 'blue',
    };
};

$canReturn = function (array $row) {
    $s = strtolower((string)($row['status'] ?? ''));
    return in_array($s, ['borrowed', 'overdue'], true);
};

$isDummy = (int)($filters['dummy'] ?? 0) === 1;
?>

<div class="bx-page tx-page">

    <div class="bx-titlebar tx-titlebar">
        <div>
            <h1 class="bx-title">Transactions</h1>
            <p class="bx-sub">
                Status otomatis: <b>Overdue</b> jika melewati due date. Denda flat <b>Rp 10.000</b>.
            </p>
        </div>

        <div class="tx-title-actions">
            <a class="tx-btn tx-btn-ghost" href="<?= esc($baseTx . ($isDummy ? '?dummy1' : '')) ?>">Reset</a>
            <a class="tx-btn tx-btn-soft" href="<?= esc($baseTx . '?' . http_build_query(array_filter([
                                                    'status' => 'overdue',
                                                    'dummy1' => $isDummy ? 1 : null,
                                                ], fn($v) => $v !== null && $v !== ''))) ?>">
                Overdue
            </a>
        </div>
    </div>

    <?php if (!empty($flash['success'])): ?>
        <div class="alert success js-flash">
            <?= esc($flash['success']) ?>
            <button type="button" class="alert-close" aria-label="Close">×</button>
        </div>
    <?php endif; ?>

    <?php if (!empty($flash['error'])): ?>
        <div class="alert error js-flash">
            <?= esc($flash['error']) ?>
            <button type="button" class="alert-close" aria-label="Close">×</button>
        </div>
    <?php endif; ?>

    <div class="bx-card">
        <form class="bx-toolbar" method="get" action="<?= esc($baseTx) ?>">
            <input type="hidden" name="page" value="1">
            <?php if ($isDummy): ?>
                <input type="hidden" name="dummy1" value="1">
            <?php endif; ?>

            <div class="bx-left">
                <div class="bx-search">
                    <span class="bx-ic">⌕</span>
                    <input type="text" name="search" value="<?= esc($filters['search'] ?? '') ?>"
                        placeholder="Cari ID / nama member / judul buku">
                </div>

                <div class="bx-pill">
                    Status:
                    <select name="status" class="tx-select">
                        <?php
                        $opts = [
                            '' => 'All',
                            'borrowed' => 'Borrowed',
                            'returned' => 'Returned',
                            'overdue' => 'Overdue',
                        ];
                        $cur = (string)($filters['status'] ?? '');
                        foreach ($opts as $k => $lbl):
                        ?>
                            <option value="<?= esc($k) ?>" <?= $cur === $k ? 'selected' : '' ?>><?= esc($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="bx-pill">
                    From:
                    <input type="date" name="from" value="<?= esc($filters['from'] ?? '') ?>" class="tx-date">
                </div>

                <div class="bx-pill">
                    To:
                    <input type="date" name="to" value="<?= esc($filters['to'] ?? '') ?>" class="tx-date">
                </div>
            </div>

            <div class="bx-right">
                <button class="tx-btn tx-btn-primary" type="submit">Apply</button>
            </div>
        </form>

        <div class="bx-tablewrap tx-wrap">
            <div class="tx-head">
                <div>
                    <div class="tx-h1">Transactions List</div>
                    <div class="tx-h2"><?= count($items) ?> data</div>
                </div>
            </div>

            <table class="bx-table tx-table" id="txTable"
                data-return-base="<?= esc(site_url('librarian/transactions')) ?>"
                data-is-dummy="<?= $isDummy ? '1' : '0' ?>">
                <thead>
                    <tr>
                        <th>Borrowed</th>
                        <th>Member</th>
                        <th>Book</th>
                        <th>Due</th>
                        <th>Returned</th>
                        <th>Status</th>
                        <th>Fine</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="bx-empty">
                                    <div class="t">Belum ada transaksi</div>
                                    <div class="d">Data peminjaman akan muncul saat member meminjam buku.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $row): ?>
                            <?php if (!is_array($row)) continue; ?>
                            <?php
                            $id = (int)($row['id'] ?? 0);
                            $memberName  = $row['member']['name'] ?? '-';
                            $memberEmail = $row['member']['email'] ?? '';
                            $bookTitle   = $row['book']['title'] ?? '-';

                            $borrowedAt = $row['borrowed_at'] ?? null;
                            $dueAt      = $row['due_at'] ?? null;
                            $returnedAt = $row['returned_at'] ?? null;

                            $statusVal = (string)($row['status'] ?? 'borrowed');
                            $badgeTone = $tone($statusVal);

                            $fine = (int)($row['fine'] ?? 0);
                            $can = $canReturn($row);
                            ?>
                            <tr class="tx-row"
                                data-id="<?= (int)$id ?>"
                                data-member="<?= esc($memberName) ?>"
                                data-email="<?= esc($memberEmail ?: '—') ?>"
                                data-book="<?= esc($bookTitle) ?>"
                                data-borrowed="<?= esc($fmt($borrowedAt)) ?>"
                                data-due="<?= esc($fmt($dueAt, false)) ?>"
                                data-returned="<?= esc($fmt($returnedAt)) ?>"
                                data-status="<?= esc(ucfirst($statusVal)) ?>"
                                data-fine="<?= esc($money($fine)) ?>">
                                <td class="tx-td-strong"><?= $fmt($borrowedAt) ?></td>

                                <td>
                                    <div class="tx-person">
                                        <div class="tx-ava"><?= esc(strtoupper(substr($memberName, 0, 1))) ?></div>
                                        <div class="tx-pmeta">
                                            <div class="tx-name"><?= esc($memberName) ?></div>
                                            <div class="tx-mail"><?= esc($memberEmail ?: '—') ?></div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="tx-book"><?= esc($bookTitle) ?></div>
                                    <div class="tx-id">ID: #<?= esc($id) ?></div>
                                </td>

                                <td><?= $fmt($dueAt, false) ?></td>
                                <td><?= $fmt($returnedAt) ?></td>

                                <td>
                                    <span class="badge <?= esc($badgeTone) ?>"><?= esc(ucfirst($statusVal)) ?></span>
                                </td>

                                <td>
                                    <span class="tx-fine <?= $fine > 0 ? 'is-on' : '' ?>"><?= esc($money($fine)) ?></span>
                                </td>

                                <td style="text-align:right;">
                                    <?php if ($can): ?>
                                        <button type="button"
                                            class="tx-btn tx-btn-return js-return-btn"
                                            data-id="<?= (int)$id ?>"
                                            data-member="<?= esc($memberName) ?>"
                                            data-book="<?= esc($bookTitle) ?>">
                                            Confirm Returned
                                        </button>
                                    <?php else: ?>
                                        <span class="mx-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Hover popover -->
            <div class="tx-pop" id="txPop" aria-hidden="true">
                <div class="tx-pop-title" id="txPopTitle">Transaction</div>
                <div class="tx-pop-grid">
                    <div class="k">Member</div>
                    <div class="v" id="txPopMember">—</div>
                    <div class="k">Book</div>
                    <div class="v" id="txPopBook">—</div>
                    <div class="k">Borrowed</div>
                    <div class="v" id="txPopBorrowed">—</div>
                    <div class="k">Due</div>
                    <div class="v" id="txPopDue">—</div>
                    <div class="k">Returned</div>
                    <div class="v" id="txPopReturned">—</div>
                    <div class="k">Status</div>
                    <div class="v" id="txPopStatus">—</div>
                    <div class="k">Fine</div>
                    <div class="v" id="txPopFine">—</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL Confirm Return -->
<div class="m-overlay" id="mReturn" aria-hidden="true">
    <div class="m" role="dialog" aria-modal="true" aria-label="Confirm return">
        <div class="m-head">
            <div class="m-title">Confirm Book Returned</div>
            <button class="m-close" type="button" data-close="mReturn">×</button>
        </div>

        <div class="m-body">
            <div class="bx-empty" style="border-style:solid; border-color: rgba(17,24,39,.08); background: rgba(246,247,251,.7);">
                <div class="t" id="mReturnTitle">Konfirmasi pengembalian</div>
                <div class="d" id="mReturnDesc">Pastikan buku sudah diterima sebelum konfirmasi.</div>
            </div>

            <form id="mReturnForm" method="post" action="" style="margin-top:12px;">
                <?= csrf_field() ?>
            </form>
        </div>

        <div class="m-foot">
            <button class="tx-btn tx-btn-ghost" type="button" data-close="mReturn">Cancel</button>
            <button class="tx-btn tx-btn-primary" type="submit" form="mReturnForm">Yes, Confirm</button>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/librarian/transactions.js') ?>"></script>

<?= $this->endSection() ?>