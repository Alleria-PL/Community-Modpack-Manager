<?php
// Ensure errors are displayed for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="bg-zinc-950 text-zinc-200 min-h-screen relative bg-grid-overlay">

    <!-- Ambient blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
        <div class="animate-blob absolute top-0 right-0 w-96 h-96 bg-amber-900/10 rounded-full blur-3xl"></div>
        <div class="animate-blob animation-delay-4000 absolute bottom-0 left-0 w-80 h-80 bg-indigo-900/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 max-w-3xl mx-auto px-4 py-12">

        <!-- Header -->
        <div class="mb-10 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-1">
                <div class="bg-amber-600/20 p-2.5 rounded-xl border border-amber-500/30">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Debug Utility</h1>
            </div>
            <p class="text-sm text-zinc-500 ml-14">Internal diagnostics &amp; file inspection tools</p>
        </div>

        <!-- Action buttons card -->
        <div class="glass-card rounded-2xl p-6 mb-8 animate-fade-in-up delay-100">
            <h2 class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-4">Actions</h2>
            <form method="POST" class="flex flex-wrap gap-3">
                <button type="submit" name="list_files"
                    class="group flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg transition-all btn-glow-indigo hover-lift border border-indigo-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    [DEBUG] List all files in download directory
                </button>

                <button type="submit" name="list_chunks"
                    class="group flex items-center gap-2 px-4 py-2.5 bg-zinc-700 hover:bg-zinc-600 text-zinc-200 hover:text-white text-sm font-semibold rounded-lg transition-all hover-lift border border-zinc-600 hover:border-zinc-500">
                    <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    [DEBUG] List all chunks (failed or live)
                </button>

                <button type="submit" name="checksum"
                    class="group flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-lg transition-all hover-lift border border-amber-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    [DEBUG] SHA 256 CheckSum of KT-latest.zip
                </button>

                <button type="submit" name="checksum2"
                    class="group flex items-center gap-2 px-4 py-2.5 bg-amber-700 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-all hover-lift border border-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    [DEBUG] SHA 256 CheckSum of KT-new.zip
                </button>
            </form>
        </div>

        <!-- Output card -->
        <div class="glass-card rounded-2xl p-6 animate-fade-in-up delay-200">
            <h2 class="text-xs font-semibold text-zinc-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Output
            </h2>
            <pre class="code-block">
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['list_files'])) {
        $files = glob(__DIR__ . '/../dl/*');
        foreach ($files as $file) {
            if (is_dir($file) && basename($file) === 'chunks') {
                continue; // Skip the chunks folder
            }
            $size = filesize($file);
            echo str_pad(basename($file), 50) . formatSize($size) . "\n";
        }
    }

    if (isset($_POST['list_chunks'])) {
        $chunksPath = __DIR__ . '/../dl/chunks';
        if (is_dir($chunksPath)) {
            $chunkFiles = glob($chunksPath . '/*');
            foreach ($chunkFiles as $chunk) {
                $size = filesize($chunk);
                echo str_pad(basename($chunk), 50) . formatSize($size) . "\n";
            }
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checksum'])) {
        $file = '../dl/KT-latest.zip';

        if (file_exists($file)) {
            $checksum = hash_file('sha256', $file);
            echo "<p>SHA256 Checksum: <strong>$checksum</strong></p>";
        } else {
            echo "<p style='color:red;'>File not found!</p>";
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checksum2'])) {
        $file = '../dl/KT-new.zip';

        if (file_exists($file)) {
            $checksum = hash_file('sha256', $file);
            echo "<p>SHA256 Checksum: <strong>$checksum</strong></p>";
        } else {
            echo "<p style='color:red;'>File not found!</p>";
        }
    }
}
?>
            </pre>
        </div>

    </div>
</body>
</html>
