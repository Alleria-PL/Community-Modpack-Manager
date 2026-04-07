<?php
$url = 'http://100.115.4.16:8000/login/access-token';

// Data to be sent in the POST request
$postData = [
    'grant_type' => '',
    'username' => 'admin',
    'password' => '',
    'scope' => '',
    'client_id' => '',
    'client_secret' => ''
];

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded'
]);

// Execute cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Decode JSON response
$responseData = json_decode($response, true);

// Extract access_token
if (isset($responseData['access_token'])) {
    $authToken = $responseData['access_token'];
} else {
    echo "Failed to retrieve access token.";
}


$apiUrl = 'http://100.115.4.16:8000/maintenance/6';

function getMaintenanceStatus($apiUrl, $authToken) {
    $ch = curl_init("$apiUrl");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        "Authorization: Bearer $authToken"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$statusData = getMaintenanceStatus($apiUrl, $authToken);
$isActive = $statusData['maintenance']['active'] ?? false;
$statusColor = $isActive ? '#2196F3' : '#D3D3D3';
$statusText = $isActive ? 'MAINTENANCE ACTIVE' : 'MAINTENANCE PAUSED';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'enable') {
        $endpoint = "$apiUrl/resume";
    } elseif ($action === 'disable') {
        $endpoint = "$apiUrl/pause";
    } else {
        die('Invalid action.');
    }
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        "Authorization: Bearer $authToken"
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo json_encode(getMaintenanceStatus($apiUrl, $authToken));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Control</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Inter', system-ui, sans-serif; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #09090b; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#4f46e5,#7c3aed); border-radius:999px; }

        /* Ambient blobs */
        body::before {
            content: '';
            position: fixed; top: -10%; left: -10%;
            width: 50vw; height: 50vw;
            background: radial-gradient(circle, rgba(99,102,241,0.07) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }
        body::after {
            content: '';
            position: fixed; bottom: -10%; right: -10%;
            width: 40vw; height: 40vw;
            background: radial-gradient(circle, rgba(168,85,247,0.05) 0%, transparent 70%);
            pointer-events: none; z-index: 0;
        }

        /* Glass card */
        .glass-card {
            background: rgba(24, 24, 27, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Fade-in */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeUp 0.6s ease-out both; }

        /* Status box */
        .status-box-active {
            background: rgba(37,99,235,0.15);
            border-color: rgba(99,102,241,0.4);
            color: #818cf8;
        }
        .status-box-inactive {
            background: rgba(39,39,42,0.8);
            border-color: rgba(63,63,70,0.8);
            color: #71717a;
        }

        /* Popup */
        #popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    <script>
        $(document).ready(function() {
            $(".container, img, footer").animate({ opacity: 1 }, 800);
            $("button").click(function(e) {
                e.preventDefault();
                var action = $(this).val();
                $.post("", { action: action }, function(response) {
                    let data = JSON.parse(response);
                    const isActive = data.maintenance.active;
                    const box = $(".status-box");
                    box.text(isActive ? "MAINTENANCE ACTIVE" : "MAINTENANCE PAUSED");
                    box.removeClass("status-box-active status-box-inactive");
                    box.addClass(isActive ? "status-box-active" : "status-box-inactive");
                    $("#popup").text("Action successful!").fadeIn().delay(2000).fadeOut();
                });
            });
        });
    </script>
</head>
<body class="bg-zinc-950 text-zinc-200 min-h-screen flex flex-col justify-center items-center relative overflow-hidden">

    <div class="z-10 w-full max-w-sm px-4 fade-in">

        <div class="flex justify-center mb-8">
            <img src="https://alleria.pl/image/logo.png" alt="Logo" class="h-14 drop-shadow-2xl" style="filter: drop-shadow(0 0 12px rgba(99,102,241,0.4));">
        </div>

        <div class="glass-card border border-zinc-800 rounded-2xl shadow-2xl shadow-black/40 p-8 text-center relative overflow-hidden container" style="opacity:0;">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-indigo-600/8 rounded-full blur-3xl pointer-events-none"></div>

            <h2 class="text-xl font-extrabold text-white mb-6 tracking-tight">Maintenance Control</h2>

            <?php
            $boxClass = $isActive ? 'status-box-active' : 'status-box-inactive';
            ?>
            <div class="status-box <?= $boxClass ?> border rounded-xl px-4 py-3 mb-8 text-sm font-mono font-bold uppercase tracking-widest transition-all duration-300">
                <?= $statusText ?>
            </div>

            <form class="flex gap-3">
                <button type="button" name="action" value="enable" class="flex-1 flex items-center justify-center bg-zinc-800 hover:bg-indigo-600 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-zinc-700 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Enable
                </button>
                <button type="button" name="action" value="disable" class="flex-1 flex items-center justify-center bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-zinc-700 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Disable
                </button>
            </form>
        </div>

        <footer class="mt-8 text-center text-xs text-zinc-600 font-mono" style="opacity:0;">
            &copy; 2025 <strong class="text-zinc-500">Alleria</strong> | All Rights Reserved | Built by <a href="https://twitter.com/henas_pl" target="_blank" class="text-zinc-500 hover:text-indigo-400 transition-colors"><b>@henas_pl</b></a>
        </footer>

    </div>

    <div id="popup" class="z-50 bg-zinc-900 border border-indigo-500/40 text-indigo-300 text-sm font-medium px-5 py-3 rounded-xl shadow-xl shadow-black/50">
    </div>

</body>
</html>