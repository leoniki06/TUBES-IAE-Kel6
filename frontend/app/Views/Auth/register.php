<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= esc($title ?? 'Register') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #eef2f7;
            --card: #fff;
            --txt: #0f172a;
            --muted: #64748b;
            --primary: #2563eb;
            --border: #e5e7eb;
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: Inter, system-ui;
            background: var(--bg);
            color: var(--txt)
        }

        .wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px
        }

        .card {
            width: min(460px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
            padding: 22px
        }

        h1 {
            font-size: 18px;
            margin: 6px 0 2px
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 13px
        }

        label {
            display: block;
            font-size: 12px;
            color: var(--muted);
            margin: 14px 0 6px
        }

        input,
        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            outline: none;
            background: #fff
        }

        .err {
            color: #b91c1c;
            font-size: 12px;
            margin-top: 6px
        }

        .btn {
            margin-top: 16px;
            width: 100%;
            padding: 11px 12px;
            border: 0;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            cursor: pointer
        }

        .row {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            font-size: 13px
        }

        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600
        }

        .alert {
            margin: 12px 0;
            padding: 10px 12px;
            border-radius: 12px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <h1>Register</h1>
            <p>Buat akun untuk akses portal member / librarian</p>

            <?php if (!empty($message)): ?>
                <div class="alert"><?= esc($message) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('/auth/register') ?>">
                <?= csrf_field() ?>

                <label>Nama</label>
                <input name="name" value="<?= esc(old('name')) ?>" placeholder="Nama lengkap">
                <?php if (!empty($errors['name'])): ?><div class="err"><?= esc($errors['name']) ?></div><?php endif; ?>

                <label>Email</label>
                <input type="email" name="email" value="<?= esc(old('email')) ?>" placeholder="nama@email.com">
                <?php if (!empty($errors['email'])): ?><div class="err"><?= esc($errors['email']) ?></div><?php endif; ?>

                <label>Password</label>
                <input type="password" name="password" placeholder="minimal 6 karakter">
                <?php if (!empty($errors['password'])): ?><div class="err"><?= esc($errors['password']) ?></div><?php endif; ?>

                <label>Role</label>
                <select name="role">
                    <option value="member" <?= old('role') === 'member' ? 'selected' : ''; ?>>Member</option>
                    <option value="librarian" <?= old('role') === 'librarian' ? 'selected' : ''; ?>>Librarian</option>
                </select>

                <button class="btn" type="submit">Buat Akun</button>

                <div class="row">
                    <span>Sudah punya akun?</span>
                    <a href="<?= site_url('/auth/login') ?>">Login</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>