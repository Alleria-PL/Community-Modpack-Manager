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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #121212;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .status-box {
            background-color: <?= $statusColor ?>;
            padding: 15px;
            border-radius: 5px;
            font-size: 18px;
            margin: 20px auto;
            width: 50%;
            font-weight: bold;
        }
        .container {
            margin: auto;
            width: 50%;
            padding: 20px;
            background: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2);
            opacity: 0;
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            background: #ff9800;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s;
        }
        button:hover {
            background: #4b0082;
        }
        img {
            width: 150px;
            margin-bottom: 20px;
            opacity: 0;
        }
        footer {
            margin-top: 20px;
            opacity: 0;
        }
        a {
            color: #ff9800;
            text-decoration: none;
            transition: color 0.3s;
        }
        a:hover {
            color: purple;
        }
        #popup {
            display: none;
            background: #333;
            color: white;
            padding: 15px;
            border-radius: 5px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
    <script>
        $(document).ready(function() {
            $(".container, img, footer").animate({ opacity: 1 }, 1000);
            $("button").click(function(e) {
                e.preventDefault();
                var action = $(this).val();
                $.post("", { action: action }, function(response) {
                    let data = JSON.parse(response);
                    $(".status-box").text(data.maintenance.active ? "MAINTENANCE ACTIVE" : "MAINTENANCE PAUSED")
                        .css("background-color", data.maintenance.active ? "#2196F3" : "#D3D3D3");
                    $("#popup").text("Action successful!").fadeIn().delay(2000).fadeOut();
                });
            });
        });
    </script>
</head>
<body>
    <img src="https://alleria.pl/image/logo.png" alt="Logo">
    <div class="container">
        <div class="status-box"> <?= $statusText ?> </div>
        <h2>Change Maintenance Status</h2>
        <form>
            <button type="button" name="action" value="enable">Enable</button>
            <button type="button" name="action" value="disable">Disable</button>
        </form>


    </div>
    <div id="popup"></div>
    <footer>
        © 2025 <strong>Alleria</strong> | All Rights Reserved | Built by <a href="https://twitter.com/henas_pl" target="_blank"><b>@henas_pl</b></a>
    </footer>
</body>
</html>