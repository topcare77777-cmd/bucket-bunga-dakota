<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Admin - Bucket Bunga Dakota</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans flex items-center justify-center min-h-screen p-4 antialiased">
    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 w-full max-w-sm sm:max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Dakota Admin</h1>
            <p class="text-xs text-slate-500 mt-1">Masukkan kredensial pemilik web untuk masuk</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 text-xs p-3 rounded-lg mb-4 border border-red-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Username</label>
                <input type="text" name="username" required placeholder="admin" autocomplete="username"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none transition text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Password</label>
                <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-rose-500 outline-none transition text-sm">
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 active:bg-slate-950 text-white font-medium py-3 rounded-lg shadow transition text-sm touch-manipulation">
                Masuk ke Panel Admin
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="/" class="text-xs text-slate-400 hover:text-slate-600">← Kembali ke Toko Utama</a>
        </div>
    </div>
</body>
</html>