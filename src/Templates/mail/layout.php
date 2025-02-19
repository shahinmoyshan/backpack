<!DOCTYPE html>
<html lang="<?= env('lang', 'en') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= _e($title ?? '') ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-height: 100vh;
            padding: 20px;
        }

        main {
            width: 600px;
            max-width: 100%;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        header {
            background-color: #1d4ed8;
            color: #fff;
            padding: 25px 30px;
        }

        header h2 {
            font-size: 1.35rem;
            margin: 0;
            font-weight: 400;
        }

        section {
            padding: 20px 30px;
            color: #333;
        }

        .content h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .content p {
            font-size: 1rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .table-container {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
            font-weight: 600;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer a,
        .content a {
            color: #1d4ed8;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <main>
        <!-- Header of of the mail, default is subject -->
        <?php if (isset($heading)): ?>
            <header>
                <h2><?= $heading ?></h2>
            </header>
        <?php endif ?>
        <!-- Main content -->
        <section class="content">
            <?= $content ?>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <!-- Copyright text and Link to our website -->
            <p><?= __('%s our website. all rights reserved', '&copy; ' . date('Y')) ?></p>
            <p>
                <a href="<?= _e(url('/')) ?>"><?= _e(__('visit our website')) ?></a>
                <span style="margin: 0 5px;opacity: 0.75;">|</span>
                <a href="<?= _e(url('/privacy-policy')) ?>"><?= _e(__('privacy policy')) ?></a>
            </p>
        </footer>
    </main>
</body>

</html>