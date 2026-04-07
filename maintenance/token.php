<?php
// Uptime Kuma REST API URL
$baseUrl = 'http://100.115.4.16:8000';
$loginUrl = "$baseUrl/auth/login";
$maintenanceUrl = "$baseUrl/maintenance/6";

// Login credentials
$username = 'admin';
$password = '';

// Function to get authentication token
function getAuthToken($loginUrl, $username, $password) {
    $ch = curl_init($loginUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'username' => $username,
        'password' => $password
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $data = json_decode($response, true);

    if ($httpCode === 200 && isset($data['token'])) {
        return $data['token'];
    } else {
        die('Failed to get auth token. Response: ' . json_encode($data));
    }
}

// Get new token
$authToken = getAuthToken($loginUrl, $username, $password);

// Function to get maintenance status
function getMaintenanceStatus($maintenanceUrl, $authToken) {
    $ch = curl_init($maintenanceUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        "Authorization: Bearer $authToken"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Fetch current maintenance status
$statusData = getMaintenanceStatus($maintenanceUrl, $authToken);
$isActive = $statusData['maintenance']['active'] ?? false;
$statusColor = $isActive ? '#2196F3' : '#D3D3D3';
$statusText = $isActive ? 'MAINTENANCE ACTIVE' : 'MAINTENANCE PAUSED';

// Handle enabling/disabling maintenance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    if ($action === 'enable') {
        $endpoint = "$maintenanceUrl/resume";
    } elseif ($action === 'disable') {
        $endpoint = "$maintenanceUrl/pause";
    } else {
        die('Invalid action.');
    }

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        "Authorization: Bearer $authToken"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo json_encode(getMaintenanceStatus($maintenanceUrl, $authToken));
    exit;
}
?>
