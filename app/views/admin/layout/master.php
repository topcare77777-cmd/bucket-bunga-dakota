<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:,">
    <link rel="stylesheet" href="/css/admin/reset.css">
    <link rel="stylesheet" href="/css/admin/variables.css">
    <link rel="stylesheet" href="/css/admin/layout.css">
    <title>Admin Panel - Bucket Bunga Dakota</title>
</head>
<body>
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main>
        <?php include __DIR__ . '/navbar.php'; ?>
        <div class="content">
            <?php echo $content ?? ''; ?>
        </div>
        <?php include __DIR__ . '/footer.php'; ?>
    </main>
    <script src="/js/admin/layout.js" type="module"></script>
</body>
</html>