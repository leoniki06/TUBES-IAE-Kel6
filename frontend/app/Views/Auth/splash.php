<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>

<header class="sp-top">
  <div class="sp-top-inner">
    <a class="sp-brand" href="<?= site_url('/') ?>">
      <span class="sp-mark">
        <img
          src="<?= base_url('assets/Logo.png') ?>"
          alt="BookHouse Logo"
          class="sp-mark-img"
          onerror="this.style.display='none'; this.parentElement.classList.add('is-fallback');"
        >
        <span class="sp-mark-fallback">BH</span>
      </span>

      <span class="sp-brand-txt">
        <span class="sp-brand-name">BookHouse</span>
        <span class="sp-brand-sub">Perpustakaan Digital</span>
      </span>
    </a>

    <!-- <nav class="sp-nav">
      <a class="sp-navlink is-active" href="#">Overview</a>
      <a class="sp-navlink" href="#">Features</a>
      <a class="sp-navlink" href="#">Workflow</a>
      <a class="sp-navlink" href="#">Contact</a>
    </nav> -->

    <div class="sp-actions">
      <button class="sp-btn sp-btn-ghost" type="button" data-bs-toggle="modal" data-bs-target="#modalLogin">
        Sign in
      </button>
      <button class="sp-btn sp-btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalRegister">
        Sign up
      </button>
    </div>
  </div>
</header>

<main class="sp-stage">
  <section class="sp-hero">
    <div class="sp-hero-grid">
      <!-- LEFT -->
      <div class="sp-left">
        <span class="sp-pill">
          <i class="fa-solid fa-book-open"></i>
          Portal Perpustakaan Digital
        </span>

        <h1 class="sp-h1">
          Online Library,
          <span class="sp-em sp-em-blue">lebih</span>
          <span class="sp-em sp-em-amber">rapi</span>
          dan cepat.
        </h1>

        <p class="sp-desc">
          Cari buku, pantau due date, dan kelola transaksi dengan UI yang bersih.
          Sistem otomatis menandai overdue dan menghitung denda sesuai aturan.
        </p>

        <div class="sp-feats">
          <div class="sp-feat">
            <span class="sp-feat-ic">
              <i class="fa-solid fa-layer-group"></i>
            </span>
            <div class="sp-feat-txt">
              <b>Catalog</b>
              <span>Pencarian cepat + pagination</span>
            </div>
          </div>

          <div class="sp-feat">
            <span class="sp-feat-ic">
              <i class="fa-solid fa-arrow-right-arrow-left"></i>
            </span>
            <div class="sp-feat-txt">
              <b>Transactions</b>
              <span>Borrow/Return + status</span>
            </div>
          </div>

          <div class="sp-feat">
            <span class="sp-feat-ic">
              <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <div class="sp-feat-txt">
              <b>Due &amp; Fine</b>
              <span>Overdue otomatis + denda</span>
            </div>
          </div>
        </div>

        <div class="sp-cta">
          <button class="sp-btn sp-btn-primary sp-btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#modalLogin">
            Masuk Sekarang <i class="fa-solid fa-arrow-right"></i>
          </button>
          <button class="sp-btn sp-btn-soft sp-btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#modalRegister">
            Buat Akun
          </button>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="sp-right">
        <div class="sp-bubbles">
          <span class="sp-bub sp-bub-blue"></span>
          <span class="sp-bub sp-bub-amber"></span>
          <span class="sp-bub sp-bub-pink"></span>
        </div>

        <div class="sp-cards">
          <div class="sp-mini sp-mini-primary">
            <div class="sp-mini-k">STATUS</div>
            <div class="sp-mini-row">
              <b>On Time</b>
              <span>Due: 12 Jan • Fine: Rp0</span>
            </div>
          </div>

          <div class="sp-mini sp-mini-danger">
            <div class="sp-mini-k">OVERDUE</div>
            <div class="sp-mini-row">
              <b>Rp10.000</b>
              <span>Lewat due date</span>
            </div>
          </div>

          <div class="sp-mini sp-mini-soft">
            <div class="sp-mini-k">RETURNED</div>
            <div class="sp-mini-row">
              <b class="sp-ok">Confirmed</b>
              <span>Transaksi selesai</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- =========================
     MODAL LOGIN
========================= -->
<div class="modal fade" id="modalLogin" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content sp-modal">
      <div class="sp-modal-head">
        <div class="sp-modal-title">Login</div>
        <button class="sp-x" type="button" data-bs-dismiss="modal" aria-label="Close">×</button>
      </div>

      <form method="post" action="<?= site_url('auth/login') ?>" class="sp-modal-body">
        <?= csrf_field() ?>
        <label class="sp-label">Email</label>
        <input class="sp-input" name="email" type="email" placeholder="admin@library.com" required>

        <label class="sp-label">Password</label>
        <input class="sp-input" name="password" type="password" placeholder="••••••••" required>

        <button class="sp-btn sp-btn-primary sp-btn-block" type="submit">Login</button>

        <div class="sp-minihelp">
          Belum punya akun? <button class="sp-link" type="button" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalRegister">Daftar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- =========================
     MODAL REGISTER (tanpa role)
========================= -->
<div class="modal fade" id="modalRegister" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content sp-modal">
      <div class="sp-modal-head">
        <div class="sp-modal-title">Register</div>
        <button class="sp-x" type="button" data-bs-dismiss="modal" aria-label="Close">×</button>
      </div>

      <form method="post" action="<?= site_url('auth/register') ?>" class="sp-modal-body">
        <?= csrf_field() ?>

        <label class="sp-label">Nama</label>
        <input class="sp-input" name="name" type="text" placeholder="Nama lengkap" required>

        <label class="sp-label">Email</label>
        <input class="sp-input" name="email" type="email" placeholder="nama@email.com" required>

        <label class="sp-label">Password</label>
        <input class="sp-input" name="password" type="password" placeholder="Minimal 8 karakter" required>

        <!-- role sengaja tidak ada.
             kalau mau, kamu bisa kirim hidden ini -->
        <input type="hidden" name="role" value="member">

        <button class="sp-btn sp-btn-primary sp-btn-block" type="submit">Buat Akun</button>

        <div class="sp-minihelp">
          Sudah punya akun? <button class="sp-link" type="button" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#modalLogin">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
