<?php
// Logika PHP na samym początku
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'upload';
$currentYear = date('Y');

// Funkcje pomocnicze dla Archiwum
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function listFiles($title, $folder) {
    $iconFile = '<svg class="w-5 h-5 text-indigo-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
    $iconFolder = '<svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>';

    echo "<div class='mb-8 bg-zinc-800 rounded-xl border border-zinc-700 shadow-lg overflow-hidden'>";
    echo "<div class='bg-zinc-900/50 p-4 border-b border-zinc-700 flex items-center'>";
    echo "$iconFolder <h3 class='text-lg font-bold text-white'>$title</h3>";
    echo "</div>";
    echo "<ul class='divide-y divide-zinc-700/50'>";

    if (is_dir($folder) || file_exists($folder)) {
        $files = @scandir($folder); 
        if ($files && count(array_diff($files, ['.', '..'])) > 0) {
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;
                $filepath = rtrim($folder, '/') . '/' . $file;
                if (is_file($filepath)) {
                    $size = formatBytes(filesize($filepath));
                    $url = rtrim($folder, '/') . '/' . rawurlencode($file);
                    echo "<li class='hover:bg-zinc-700/50 transition-colors duration-200'>";
                    echo "<a href=\"$url\" download class='flex items-center justify-between p-4 group'>";
                    echo "<div class='flex items-center text-zinc-300 group-hover:text-white transition-colors'>";
                    echo "$iconFile <span class='font-medium'>$file</span>";
                    echo "</div>";
                    echo "<span class='text-xs font-mono text-zinc-500 bg-zinc-900 px-2 py-1 rounded border border-zinc-700'>$size</span>";
                    echo "</a></li>";
                }
            }
        } else {
            echo "<li class='p-4 text-zinc-500 italic text-center'>Brak plików w folderze.</li>";
        }
    } else {
        echo "<li class='p-4 text-red-400 italic text-center'>Folder nie istnieje: $folder</li>";
    }
    echo "</ul></div>";
}
?>
<!DOCTYPE html>
<html lang="pl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KT - Management [Alleria]</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    </style>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-J0WCNB0WDW"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-J0WCNB0WDW');
    </script>
</head>
<body class="bg-zinc-950 text-zinc-200 h-full flex flex-col overflow-hidden">

    <header class="bg-zinc-900 border-b border-zinc-800 shadow-md z-20 flex-shrink-0">
        <div class="w-full flex flex-wrap items-center justify-between px-6 py-3">
            
            <div class="flex items-center mr-8 flex-shrink-0">
                <div class="bg-indigo-600 p-2 rounded-lg mr-3 shadow-lg shadow-indigo-500/20">
                    <img src="http://alleria.pl/image/favicon.png" alt="Alleria Logo" class="w-6 h-6 object-contain">
                </div>
                <div>
                    <h1 class="font-bold text-xl text-white tracking-tight whitespace-nowrap">Klocki Time</h1>
                    <p class="text-xs text-zinc-400 font-mono uppercase tracking-widest hidden sm:block">Management Panel</p>
                </div>
            </div>

            <nav class="flex-1 flex items-center justify-end flex-wrap gap-2">
                <?php
                $navItems = [
                    ['id' => 'docs', 'label' => 'Docs', 'url' => 'https://kt-docs.alleria.pl', 'ext' => true, 'color' => 'rose'],
                    ['id' => 'upload', 'label' => 'Client Upload', 'url' => '?tab=upload', 'ext' => false, 'color' => 'indigo'],
                    ['id' => 'maintenance', 'label' => 'Maintenance', 'url' => '?tab=maintenance', 'ext' => false, 'color' => 'indigo'],
                    ['id' => 'panel', 'label' => 'Server Panel', 'url' => '?tab=panel', 'ext' => false, 'color' => 'indigo'],
                    ['id' => 'versions', 'label' => 'Modpack Versions', 'url' => 'https://www.technicpack.net/modpack/edit/2000598/versions', 'ext' => true, 'color' => 'emerald'],
                    ['id' => 'status', 'label' => 'Status usług', 'url' => 'https://monitor.alleria.pl/status/klocki-time', 'ext' => true, 'color' => 'emerald'],
                    ['id' => 'stats', 'label' => 'Server Stats', 'url' => 'https://grafana.alleria.pl', 'ext' => true, 'color' => 'emerald'],
                    ['id' => 'mapa', 'label' => 'Mapa', 'url' => '?tab=mapa', 'ext' => false, 'color' => 'indigo'],
                    ['id' => 'archive', 'label' => 'Archiwum', 'url' => '?tab=archive', 'ext' => false, 'color' => 'amber'],
                ];

                foreach ($navItems as $item) {
                    $isActive = ($tab == $item['id']);
                    // Usunąłem width: full/auto, teraz przyciski mają naturalną wielkość
                    $baseClass = "flex items-center px-3 py-2 text-sm font-semibold rounded-lg transition-all duration-300 whitespace-nowrap border border-transparent";
                    
                    if ($isActive) {
                        $class = "$baseClass bg-{$item['color']}-600 text-white shadow-lg shadow-{$item['color']}-500/20";
                    } else {
                        if ($item['ext']) {
                            $class = "$baseClass bg-zinc-800 text-{$item['color']}-400 hover:bg-{$item['color']}-900/30 hover:text-{$item['color']}-200 border-zinc-700";
                        } else {
                            $class = "$baseClass hover:bg-zinc-800 text-zinc-400 hover:text-white";
                        }
                    }

                    $icon = $item['ext'] ? '<svg class="w-3 h-3 ml-2 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>' : '';
                    $target = $item['ext'] ? 'target="_blank"' : '';
                    echo "<a href='{$item['url']}' $target class='$class'>{$item['label']} $icon</a>";
                }
                ?>
            </nav>
        </div>
    </header>

    <main class="flex-grow relative w-full overflow-hidden bg-zinc-950">
        <?php if ($tab == 'upload') { ?>
            <iframe src="https://kt-management.alleria.pl/modpack.html" class="w-full h-full border-none"></iframe>
        
        <?php } elseif ($tab == 'maintenance') { ?>
            <div class="relative w-full h-full">
                <div id="loading-overlay" class="absolute inset-0 bg-zinc-900 flex flex-col items-center justify-center z-10 transition-opacity duration-500">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500 mb-4"></div>
                    <p class="text-indigo-400 font-mono animate-pulse">Retrieving access token...</p>
                </div>
                <iframe src="https://kt-management.alleria.pl/maintenance/" 
                        class="w-full h-full border-none opacity-0 transition-opacity duration-500"
                        onload="document.getElementById('loading-overlay').style.opacity='0'; setTimeout(()=>{document.getElementById('loading-overlay').style.display='none'; this.classList.remove('opacity-0')}, 500);">
                </iframe>
            </div>

        <?php } elseif ($tab == 'panel') { ?>
            <iframe src="https://panel.alleria.pl/server/4a0a24c9" class="w-full h-full border-none"></iframe>
            
        <?php } elseif ($tab == 'mapa') { ?>
            <iframe src="https://kt-mapa.alleria.pl" class="w-full h-full border-none"></iframe>

        <?php } elseif ($tab == 'archive') { ?>
            <div class="h-full overflow-y-auto p-6 lg:p-10 bg-zinc-950">
                <div class="max-w-5xl mx-auto">
                    <div class="mb-8 border-b border-zinc-800 pb-4">
                        <h2 class="text-3xl font-bold text-white flex items-center">
                            <svg class="w-8 h-8 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            Archiwum Plików
                        </h2>
                    </div>
                    <?php
                        listFiles("Klocki Time 2 (ATM 9)", "/archive/KT2-ATM9/");
                        listFiles("Klocki Time 3 (ATM 10)", "/archive/KT3-ATM10/");
                    ?>
                </div>
            </div>
        <?php } ?>
    </main>

    <footer class="bg-zinc-900 border-t border-zinc-800 py-3 flex-shrink-0 z-20">
        <div class="text-center text-xs font-mono text-zinc-500">
            &copy; 2025 - <?php echo $currentYear; ?> <a target="_blank" href="https://alleria.pl" class="text-zinc-300 hover:text-white transition-colors">Alleria</a> | All Rights Reserved | 
            Built by <a target="_blank" href="https://x.com/henas_pl" class="text-zinc-300 hover:text-white transition-colors">@henas_pl</a>
             </div>
    </footer>

</body>
</html>