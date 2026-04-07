<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Checksum</title>
</head>
<body>

    <form method="post">
        <button type="submit" name="checksum">Generate Checksum</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checksum'])) {
        $file = '../dl/KT-latest.zip';

        if (file_exists($file)) {
            $checksum = hash_file('sha256', $file);
            echo "<p>SHA256 Checksum: <strong>$checksum</strong></p>";
        } else {
            echo "<p style='color:red;'>File not found!</p>";
        }
    }
    ?>

</body>
</html>