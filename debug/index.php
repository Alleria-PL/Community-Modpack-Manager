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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Debug Page</title>
</head>
<body>
<div class="container mt-5">
    <h1>Debug Utility</h1>
    <div class="mt-4">
        <form method="POST">
            <button type="submit" name="list_files" class="btn btn-primary mb-3">[DEBUG] List all files in download directory</button>
            <button type="submit" name="list_chunks" class="btn btn-secondary mb-3">[DEBUG] List all chunks (failed or live)</button>
            <button type="submit" name="checksum" class="btn btn-warning mb-3">[DEBUG] SHA 256 CheckSum of KT-latest.zip</button>
            <button type="submit" name="checksum2" class="btn btn-warning mb-3">[DEBUG] SHA 256 CheckSum of KT-new.zip</button>
        </form>
    </div>

    <div class="mt-4">
        <h2>Output:</h2>
        <pre class="border p-3">
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



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>
