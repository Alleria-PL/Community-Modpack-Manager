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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Debug Page</title>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Courier New', monospace; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #09090b; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#4f46e5,#7c3aed); border-radius:999px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg,#6366f1,#8b5cf6); }

        body::before {
            content: '';
            position: fixed; top:-15%; left:-5%;
            width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 min-h-screen p-6 lg:p-10">
<div class="max-w-3xl mx-auto relative z-10">

    <div class="mb-8">
        <div class="flex items-center mb-2">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-xl mr-3 shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Debug Utility</h1>
        </div>
        <p class="text-zinc-500 text-sm font-mono ml-12 uppercase tracking-widest">Klocki Time Management</p>
    </div>

    <div class="bg-zinc-900/60 backdrop-blur-sm border border-zinc-800 rounded-2xl p-6 mb-6 shadow-xl">
        <h2 class="text-sm font-semibold text-zinc-400 uppercase tracking-widest mb-4">Actions</h2>
        <form method="POST" class="flex flex-wrap gap-3">
            <button type="submit" name="list_files" class="flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-indigo-600/20 hover:shadow-indigo-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                [DEBUG] List all files in download directory
            </button>
            <button type="submit" name="list_chunks" class="flex items-center px-4 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-sm font-semibold rounded-xl transition-all border border-zinc-700 hover:border-zinc-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                [DEBUG] List all chunks (failed or live)
            </button>
            <button type="submit" name="checksum" class="flex items-center px-4 py-2.5 bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 hover:text-amber-300 text-sm font-semibold rounded-xl transition-all border border-amber-600/30 hover:border-amber-500/50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                [DEBUG] SHA 256 CheckSum of KT-latest.zip
            </button>
            <button type="submit" name="checksum2" class="flex items-center px-4 py-2.5 bg-amber-600/20 hover:bg-amber-600/30 text-amber-400 hover:text-amber-300 text-sm font-semibold rounded-xl transition-all border border-amber-600/30 hover:border-amber-500/50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                [DEBUG] SHA 256 CheckSum of KT-new.zip
            </button>
        </form>
    </div>

    <div class="bg-zinc-900/60 backdrop-blur-sm border border-zinc-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="flex items-center px-5 py-3 bg-zinc-900 border-b border-zinc-800">
            <div class="flex space-x-1.5 mr-4">
                <div class="w-3 h-3 rounded-full bg-red-500/70"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/70"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/70"></div>
            </div>
            <h2 class="text-xs font-semibold text-zinc-500 mono uppercase tracking-wider">Output</h2>
        </div>
        <pre class="mono text-sm text-zinc-300 p-5 overflow-x-auto min-h-24 leading-relaxed">
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
