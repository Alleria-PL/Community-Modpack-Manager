<?php
// --- BACKEND LOGIC ---
$url = 'http://s.alleria.pl:8000/login/access-token';

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
    // In production, handle this more gracefully
    die('cURL error: ' . curl_error($ch));
}

curl_close($ch);

$responseData = json_decode($response, true);
$authToken = $responseData['access_token'] ?? null;

if (!$authToken) {
    die("Failed to retrieve access token.");
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

// Handle AJAX Request (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'enable') {
        $endpoint = "$apiUrl/resume";
    } elseif ($action === 'disable') {
        $endpoint = "$apiUrl/pause";
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        "Authorization: Bearer $authToken"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    // Return new status
    echo json_encode(getMaintenanceStatus($apiUrl, $authToken));
    exit;
}

// Initial Page Load Data
$statusData = getMaintenanceStatus($apiUrl, $authToken);
$isActive = $statusData['maintenance']['active'] ?? false;
$currentYear = date('Y');
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Control</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        /* Custom animation for pulse glow */
        @keyframes subtle-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .animate-subtle-pulse {
            animation: subtle-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
    <script>
        // Store initial state from PHP
        let isMaintenanceActive = <?php echo $isActive ? 'true' : 'false'; ?>;

        function updateUI(active) {
            isMaintenanceActive = active;
            const statusLabel = document.getElementById('statusLabel');
            const statusDot = document.getElementById('statusDot');
            const statusContainer = document.getElementById('statusContainer');
            
            if (active) {
                // Active State Styling
                statusLabel.innerText = "MAINTENANCE ACTIVE";
                statusLabel.classList.remove('text-zinc-500');
                statusLabel.classList.add('text-indigo-400');
                
                statusDot.classList.remove('bg-zinc-600');
                statusDot.classList.add('bg-indigo-500', 'animate-pulse', 'shadow-[0_0_10px_rgba(99,102,241,0.6)]');
                
                statusContainer.classList.add('border-indigo-500/30', 'bg-indigo-500/5');
                statusContainer.classList.remove('border-zinc-700', 'bg-zinc-800/50');
            } else {
                // Inactive State Styling
                statusLabel.innerText = "MAINTENANCE PAUSED";
                statusLabel.classList.add('text-zinc-500');
                statusLabel.classList.remove('text-indigo-400');
                
                statusDot.classList.add('bg-zinc-600');
                statusDot.classList.remove('bg-indigo-500', 'animate-pulse', 'shadow-[0_0_10px_rgba(99,102,241,0.6)]');
                
                statusContainer.classList.remove('border-indigo-500/30', 'bg-indigo-500/5');
                statusContainer.classList.add('border-zinc-700', 'bg-zinc-800/50');
            }
        }

        async function sendAction(action) {
            const btn = document.getElementById(`btn-${action}`);
            const originalText = btn.innerHTML;
            
            // Set Loading State
            document.querySelectorAll('button').forEach(b => b.disabled = true);
            btn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            
            try {
                const formData = new FormData();
                formData.append('action', action);

                const response = await fetch("", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) throw new Error("Network response was not ok");

                const data = await response.json();
                
                // Update UI based on server response
                if (data.maintenance) {
                    updateUI(data.maintenance.active);
                    showToast("Status updated successfully!", "success");
                }
            } catch (error) {
                console.error("Error:", error);
                showToast("Failed to update status.", "error");
            } finally {
                // Reset Buttons
                btn.innerHTML = originalText;
                document.querySelectorAll('button').forEach(b => b.disabled = false);
            }
        }

        function showToast(message, type = "success") {
            const toast = document.getElementById('toast');
            const toastText = document.getElementById('toastText');
            const toastIcon = document.getElementById('toastIcon');

            toastText.innerText = message;
            
            if (type === 'success') {
                toastIcon.innerHTML = `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            } else {
                toastIcon.innerHTML = `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
            }

            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        // Initialize UI on load
        document.addEventListener('DOMContentLoaded', () => {
            updateUI(isMaintenanceActive);
        });
    </script>
</head>
<body class="bg-zinc-950 text-zinc-200 h-full flex flex-col justify-center items-center min-h-screen relative overflow-hidden">

    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-900/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-900/10 rounded-full blur-3xl"></div>
    </div>

    <div class="z-10 w-full max-w-md px-4">
        
        <div class="flex justify-center mb-8">
             <img src="https://alleria.pl/image/logo-clr.png" alt="Alleria Logo" class="h-16 drop-shadow-2xl">
        </div>

        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl shadow-2xl p-8 text-center relative overflow-hidden backdrop-blur-sm">
            
            <h2 class="text-2xl font-bold text-white mb-6 tracking-tight">Maintenance Control</h2>

            <div id="statusContainer" class="flex items-center justify-center space-x-3 mb-8 py-3 px-4 rounded-lg border border-zinc-700 bg-zinc-800/50 transition-all duration-300">
                <div id="statusDot" class="w-3 h-3 rounded-full bg-zinc-600 transition-all duration-300"></div>
                <span id="statusLabel" class="font-mono font-bold text-lg text-zinc-500 transition-colors duration-300">LOADING...</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <button id="btn-enable" onclick="sendAction('enable')" class="group flex items-center justify-center bg-zinc-800 hover:bg-indigo-600 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-zinc-700 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-500/20">
                    <svg class="w-5 h-5 mr-2 text-indigo-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Enable
                </button>
                
                <button id="btn-disable" onclick="sendAction('disable')" class="group flex items-center justify-center bg-zinc-800 hover:bg-zinc-700 text-zinc-400 hover:text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-zinc-700 hover:border-zinc-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Disable
                </button>
            </div>
        </div>

        <footer class="mt-8 text-center text-xs text-zinc-600 font-mono">
            &copy; Alleria 2025 - <?php echo $currentYear; ?> | Built by <a href="https://x.com/henas_pl" target="_blank" class="text-zinc-500 hover:text-indigo-400 transition-colors">@henas_pl</a>
        </footer>
    </div>

    <div id="toast" class="fixed bottom-6 right-6 flex items-center bg-zinc-900 border border-zinc-700 shadow-2xl rounded-lg px-4 py-3 transform translate-y-20 opacity-0 transition-all duration-500 z-50">
        <div id="toastIcon" class="mr-3"></div>
        <p id="toastText" class="text-sm font-medium text-white"></p>
    </div>

</body>
</html>