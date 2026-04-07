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
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-zinc-950 text-zinc-200 min-h-screen flex flex-col justify-center items-center relative overflow-hidden bg-grid-overlay">

    <!-- Ambient background blobs -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
        <div class="animate-blob absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-900/25 rounded-full blur-3xl"></div>
        <div class="animate-blob animation-delay-4000 absolute bottom-1/4 right-1/4 w-80 h-80 bg-purple-900/15 rounded-full blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">

        <!-- Logo -->
        <div class="flex justify-center mb-8 animate-fade-in-up" id="logo-wrap">
            <img src="https://alleria.pl/image/logo.png" alt="Logo" class="h-16 drop-shadow-2xl opacity-0" id="logo">
        </div>

        <!-- Card -->
        <div class="container glass-card rounded-2xl shadow-2xl p-8 text-center opacity-0 animate-fade-in-up delay-100">

            <h2 class="text-2xl font-bold text-white mb-6 tracking-tight">Change Maintenance Status</h2>

            <!-- Status badge -->
            <div class="status-box flex items-center justify-center gap-3 mb-8 py-3 px-5 rounded-xl border transition-all duration-500 <?= $isActive ? 'status-active' : 'status-paused' ?>">
                <span class="relative flex h-3 w-3 flex-shrink-0">
                    <?php if ($isActive): ?>
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-60"></span>
                    <?php endif; ?>
                    <span class="relative inline-flex h-3 w-3 rounded-full <?= $isActive ? 'bg-indigo-500 glow-indigo' : 'bg-zinc-600' ?> transition-all duration-500 status-dot"></span>
                </span>
                <span class="font-mono font-bold text-base tracking-widest <?= $isActive ? 'text-indigo-300' : 'text-zinc-400' ?> transition-colors duration-500 status-label"><?= $statusText ?></span>
            </div>

            <!-- Action buttons -->
            <form>
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" name="action" value="enable"
                        class="group flex items-center justify-center gap-2 bg-zinc-800 hover:bg-indigo-600 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-zinc-700 hover:border-indigo-500 btn-glow-indigo">
                        <svg class="w-4 h-4 text-indigo-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Enable
                    </button>
                    <button type="button" name="action" value="disable"
                        class="group flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-zinc-700 hover:border-zinc-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Disable
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <footer class="mt-8 text-center text-xs text-zinc-600 font-mono opacity-0 animate-fade-in-up delay-300" id="footer">
            &copy; 2025 <strong class="text-zinc-500">Alleria</strong> | All Rights Reserved |
            Built by <a href="https://twitter.com/henas_pl" target="_blank" class="text-zinc-500 hover:text-indigo-400 transition-colors"><b>@henas_pl</b></a>
        </footer>
    </div>

    <!-- Toast popup -->
    <div id="popup"
        class="hidden fixed bottom-6 right-6 flex items-center gap-3 bg-zinc-900 border border-zinc-700 shadow-2xl rounded-xl px-5 py-3 z-50">
        <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm font-medium text-white">Action successful!</p>
    </div>

    <script>
        $(document).ready(function () {
            // Fade in elements
            $(".container, #footer").animate({ opacity: 1 }, 800);
            $("#logo").animate({ opacity: 1 }, 1000);

            $("button").click(function (e) {
                e.preventDefault();
                var action = $(this).val();
                var $btn = $(this);
                $btn.prop("disabled", true);

                $.post("", { action: action }, function (response) {
                    var data = JSON.parse(response);
                    var isActive = data.maintenance.active;
                    var $box = $(".status-box");
                    var $dot = $(".status-dot");
                    var $label = $(".status-label");

                    $label.text(isActive ? "MAINTENANCE ACTIVE" : "MAINTENANCE PAUSED");

                    if (isActive) {
                        $box.removeClass("status-paused").addClass("status-active");
                        $dot.removeClass("bg-zinc-600").addClass("bg-indigo-500 glow-indigo");
                        $label.removeClass("text-zinc-400").addClass("text-indigo-300");
                    } else {
                        $box.removeClass("status-active").addClass("status-paused");
                        $dot.removeClass("bg-indigo-500 glow-indigo").addClass("bg-zinc-600");
                        $label.removeClass("text-indigo-300").addClass("text-zinc-400");
                    }

                    $("#popup").fadeIn(300).delay(2500).fadeOut(400);
                }).always(function () {
                    $btn.prop("disabled", false);
                });
            });
        });
    </script>
</body>
</html>