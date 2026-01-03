<?php $u = session()->get('user'); ?>
<div class="panel">
    <div style="font-weight:800;font-size:14px;margin-bottom:10px">Selected Book</div>

    <div style="background:rgba(255,255,255,.10);border-radius:16px;padding:12px;border:1px solid rgba(255,255,255,.12)">
        <div style="height:160px;border-radius:14px;background:rgba(255,255,255,.14);display:grid;place-items:center;font-weight:800">
            Cover
        </div>

        <div style="margin-top:12px;font-weight:800">Company of One</div>
        <div style="opacity:.8;font-size:12px;margin-top:4px">Paul Jarvis</div>

        <div style="margin-top:12px;font-size:12px;opacity:.9;line-height:1.5">
            Panel ini nanti kita isi dari API saat user klik buku (detail).
        </div>

        <a href="<?= site_url('/books') ?>" style="display:block;margin-top:14px;text-align:center;padding:10px 12px;border-radius:14px;background:#2563eb;color:#fff;font-weight:800">
            Browse Books
        </a>
    </div>

    <div style="margin-top:14px;font-size:12px;opacity:.85">
        Logged in as <b><?= esc($u['email'] ?? '-') ?></b>
    </div>
</div>