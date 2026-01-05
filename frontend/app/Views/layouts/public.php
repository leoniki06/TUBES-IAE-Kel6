<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'BookHouse') ?></title>

  <!-- Bootstrap (kalau masih dipakai modal js dll) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" referrerpolicy="no-referrer" />

  <!-- Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- App CSS (tetap pakai token kamu) -->
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>

<body class="sp-body">
  <?= $this->renderSection('content') ?>

  <!-- Bootstrap JS (kalau modal login/register pakai bootstrap) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
