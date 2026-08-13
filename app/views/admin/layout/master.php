<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/admin/reset.css">
    <link rel="stylesheet" href="/css/admin/variables.css">
    <link rel="stylesheet" href="/css/admin/layout.css">
    <title>Admin Panel - Bucket Bunga Dakota</title>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main>
        <?php include 'navbar.php'; ?>
        <div class="content">
            <?php echo $content ?? ''; ?>
        </div>
        <?php include 'footer.php'; ?>
    </main>
    <script src="/js/admin/layout.js" type="module"></script>
</body>
</html>