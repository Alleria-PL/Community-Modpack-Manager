<?php
// Define directories
$baseDir = __DIR__ . '/dl/';
$chunksDir = $baseDir . 'chunks/';
if (!is_dir($baseDir)) mkdir($baseDir, 0755, true);
if (!is_dir($chunksDir)) mkdir($chunksDir, 0755, true);

// Clear chunks directory if requested
if (isset($_GET['clear_chunks'])) {
    array_map('unlink', glob("$chunksDir*"));
    echo json_encode(['success' => true]);
    exit;
}

// Handle POST request for chunked uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileId = $_POST['fileId'] ?? null;
    $chunkIndex = $_POST['chunkIndex'] ?? null;
    $totalChunks = $_POST['totalChunks'] ?? null;

    if (!$fileId || $chunkIndex === null || !$totalChunks) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
        exit;
    }

    $chunk = $_FILES['chunk'] ?? null;
    if (!$chunk || $chunk['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Failed to upload chunk.']);
        exit;
    }

    $chunkPath = $chunksDir . $fileId . '.part' . $chunkIndex;
    if (!move_uploaded_file($chunk['tmp_name'], $chunkPath)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save chunk.']);
        exit;
    }

    if ($chunkIndex == $totalChunks - 1) {
        $finalPath = $baseDir . $fileId;
        $outFile = fopen($finalPath, 'wb');
        for ($i = 0; $i < $totalChunks; $i++) {
            $partPath = $chunksDir . $fileId . '.part' . $i;
            $inFile = fopen($partPath, 'rb');
            while ($buffer = fread($inFile, 8192)) {
                fwrite($outFile, $buffer);
            }
            fclose($inFile);
            unlink($partPath);
        }
        fclose($outFile);

        // Rename to KT-new.zip after upload completion
        rename($finalPath, $baseDir . 'KT-new.zip');
    }

    echo json_encode(['success' => true]);
    exit;
}

// Handle make live version request
if (isset($_GET['make_live'])) {
    $newFile = $baseDir . 'KT-new.zip';
    $latestFile = $baseDir . 'KT-latest.zip';

    if (!file_exists($newFile)) {
        echo json_encode(['success' => false, 'message' => 'There is no uploaded file to replace the current version.']);
        exit;
    }

    if (file_exists($latestFile)) {
        unlink($latestFile);
    }

    rename($newFile, $latestFile);
    echo json_encode(['success' => true]);
    exit;
}

// Fetch modification date of KT-latest.zip
if (isset($_GET['get_upload_date'])) {
    $latestFile = $baseDir . 'KT-latest.zip';

    if (file_exists($latestFile)) {
        $modTime = date("Y-m-d H:i:s", filemtime($latestFile));
        echo json_encode(['success' => true, 'date' => $modTime]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No modpack file found.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
?>
