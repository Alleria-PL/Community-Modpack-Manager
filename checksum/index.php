<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Checksum</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', system-ui, sans-serif; }
        .mono { font-family: 'JetBrains Mono', 'Courier New', monospace; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #09090b; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#4f46e5,#7c3aed); border-radius:999px; }

        body::before {
            content: '';
            position: fixed; top:-15%; right:-10%;
            width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(99,102,241,0.06) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }

        /* Glass card */
        .glass-card {
            background: rgba(24, 24, 27, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeUp 0.5s ease-out both; }
    </style>
</head>
<body class="bg-zinc-950 text-zinc-200 min-h-screen flex items-center justify-center p-6">

<div class="w-full max-w-md relative z-10 fade-in">

    <div class="flex items-center mb-6">
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-xl mr-3 shadow-lg shadow-indigo-500/30">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">File Checksum</h1>
    </div>

    <div class="glass-card border border-zinc-800 rounded-2xl p-6 shadow-2xl shadow-black/40">
        <p class="text-zinc-500 text-sm mb-5">Generate SHA-256 checksum for <span class="text-zinc-300 font-mono">KT-latest.zip</span>.</p>

        <form method="post">
            <button type="submit" name="checksum" class="w-full flex items-center justify-center bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold py-3 px-5 rounded-xl transition-all shadow-lg shadow-indigo-600/25 hover:shadow-indigo-500/35">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Generate Checksum
            </button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checksum'])) {
            $file = '../dl/KT-latest.zip';

            if (file_exists($file)) {
                $checksum = hash_file('sha256', $file);
                echo "<div class='mt-5 bg-zinc-950/80 border border-zinc-800 rounded-xl p-4'>
                    <p class='text-xs text-zinc-500 font-semibold uppercase tracking-wider mb-2'>SHA-256 Checksum</p>
                    <p class='text-emerald-400 font-mono text-xs break-all leading-relaxed mono'>$checksum</p>
                </div>";
            } else {
                echo "<div class='mt-5 bg-red-900/20 border border-red-800/50 rounded-xl p-4'>
                    <p class='text-red-400 text-sm'>File not found!</p>
                </div>";
            }
        }
        ?>
    </div>

</div>

</body>
</html>