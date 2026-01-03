<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= esc($title ?? 'Library') ?></title>

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
            --shadow: 0 12px 30px rgba(15, 23, 42, .08);
            --radius: 18px;
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

        .shell {
            max-width: 1280px;
            margin: 24px auto;
            padding: 16px;
            border-radius: 26px;
            background: rgba(255, 255, 255, .55);
            border: 1px solid rgba(229, 231, 235, .8)
        }

        .app {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 16px;
            background: #f7f9fc;
            border: 1px solid rgba(229, 231, 235, .9);
            border-radius: 26px;
            padding: 16px
        }

        .sidebar {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 14px;
            box-shadow: var(--shadow)
        }

        .content {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 16px
        }

        .main {
            background: transparent
        }

        .panel {
            background: #0b1b3a;
            border-radius: 22px;
            color: #fff;
            padding: 18px;
            box-shadow: var(--shadow)
        }

        .topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px
        }

        .search {
            flex: 1
        }

        .search input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--border);
            outline: none
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff
        }

        a {
            text-decoration: none;
            color: inherit
        }

        .nav {
            margin-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            color: var(--muted);
            border: 1px solid transparent
        }

        .nav a.active {
            background: rgba(37, 99, 235, .10);
            color: var(--primary);
            border-color: rgba(37, 99, 235, .18)
        }

        .nav a:hover {
            background: #f8fafc
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .logo {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: rgba(37, 99, 235, .12);
            display: grid;
            place-items: center;
            color: var(--primary);
            font-weight: 800
        }

        .card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 14px;
            box-shadow: var(--shadow)
        }

        .muted {
            color: var(--muted)
        }

        .toast {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 14px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-size: 13px
        }

        @media (max-width: 1024px) {
            .content {
                grid-template-columns: 1fr
            }

            .panel {
                display: none
            }
        }
    </style>
</head>

<body>

    <div class="shell">
        <div class="app">
            <?= $this->include('layouts/sidebar') ?>

            <div class="content">
                <div class="main">
                    <?= $this->include('layouts/topbar') ?>

                    <?php if (!empty($message)): ?>
                        <div class="toast"><?= esc($message) ?></div>
                    <?php endif; ?>

                    <?= $this->renderSection('content') ?>
                </div>

                <?= $this->include('layouts/rightpanel') ?>
            </div>
        </div>
    </div>

</body>

</html>