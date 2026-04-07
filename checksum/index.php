<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Checksum</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-zinc-950 text-zinc-200 min-h-screen flex flex-col items-center justify-center relative bg-grid-overlay">

    <!-- Ambient blob -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
        <div class="animate-blob absolute top-1/3 left-1/2 -translate-x-1/2 w-96 h-96 bg-amber-900/15 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4 animate-fade-in-up">

        <!-- Card -->
        <div class="glass-card rounded-2xl p-8 text-center shadow-2xl">

            <!-- Icon + title -->
            <div class="flex items-center justify-center gap-3 mb-6">
                <div class="bg-amber-600/20 p-3 rounded-xl border border-amber-500/30">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white tracking-tight">File Checksum</h1>
            </div>

            <p class="text-sm text-zinc-500 mb-6">Generate a SHA-256 checksum for <span class="font-mono text-zinc-300">KT-latest.zip</span></p>

            <form method="post">
                <button type="submit" name="checksum"
                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-all btn-glow-amber hover-lift border border-amber-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Generate Checksum
                </button>
            </form>

            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checksum'])) {
                $file = '../dl/KT-latest.zip';
                if (file_exists($file)) {
                    $checksum = hash_file('sha256', $file);
                    echo '<div class="mt-6 text-left">';
                    echo '<p class="text-xs text-zinc-500 uppercase tracking-widest font-semibold mb-2">SHA-256 Checksum</p>';
                    echo '<div class="code-block break-all">' . htmlspecialchars($checksum) . '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="mt-6 flex items-center gap-2 bg-red-900/20 border border-red-500/30 rounded-xl px-4 py-3 text-red-400 text-sm">';
                    echo '<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    echo 'File not found!';
                    echo '</div>';
                }
            }
            ?>
        </div>

    </div>
</body>
</html>