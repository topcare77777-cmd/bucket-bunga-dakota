<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Admin CSS Foundation -->
    <link rel="stylesheet" href="/css/admin/reset.css">
    <link rel="stylesheet" href="/css/admin/variables.css">
    <link rel="stylesheet" href="/css/admin/layout.css">
    <link rel="stylesheet" href="/css/admin/sidebar.css">
    <link rel="stylesheet" href="/css/admin/navbar.css">
    <link rel="stylesheet" href="/css/admin/footer.css">
    <link rel="stylesheet" href="/css/admin/component.css">
    <link rel="stylesheet" href="/css/admin/responsive.css">
    
    <title>Admin Panel - Bucket Bunga Dakota</title>
</head>
<body data-theme="light">
    <div class="app-layout">
        <?php 
            if (file_exists(__DIR__ . '/sidebar.php')) {
                include __DIR__ . '/sidebar.php';
            }
        ?>
        <main class="main-content">
            <?php 
                if (file_exists(__DIR__ . '/navbar.php')) {
                    include __DIR__ . '/navbar.php';
                }
            ?>
            <div class="content-body">
                <?php echo $content ?? ''; ?>
            </div>
            <?php 
                if (file_exists(__DIR__ . '/footer.php')) {
                    include __DIR__ . '/footer.php';
                }
            ?>
        </main>
    </div>
    
    <script src="/js/admin/layout.js" type="module"></script>
</body>
</html>