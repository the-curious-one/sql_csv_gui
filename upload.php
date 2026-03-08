<?php

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    require("csvdb.php");

/* CONFIG */
$uploadDir = 'uploads';
$maxFileSize = 10 * 1024 * 1024; // 10MB

/* CREATE DIRECTORY IF NOT EXISTS */
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* HANDLE UPLOAD */
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    $file = $_FILES['file'];

    /* CHECK FOR ERRORS */
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload failed with error code: ' . $file['error'];
    }

    /* CHECK SIZE */
    elseif ($file['size'] > $maxFileSize) {
        $message = 'File is too large.';
    }

    else {

        /* SAFE FILE NAME */
        //$originalName = basename($file['name']);

        // remove dangerous characters
        //$safeName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $originalName);
        $safeName = "list.csv";

        /* PREVENT OVERWRITE */
        $targetPath = $uploadDir . '/' . $safeName;

        /*
        $counter = 1;

        while (file_exists($targetPath)) {

            $pathInfo = pathinfo($safeName);

            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

            $targetPath = $uploadDir . '/' .
                $pathInfo['filename'] . '_' . $counter . $extension;

            $counter++;
        }
            */

        $allowed = ['csv'];

        $ext = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $message = 'File type not allowed.';
            echo $message;
            exit;
        }
        else {
            /* MOVE FILE */
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {

                $relativePath = $uploadDir .'/' . basename($targetPath);

                $message = "Upload successful:<br><a href=\"$relativePath\" target=\"_blank\">$relativePath</a>";

            } else {
                $message = 'Failed to move uploaded file.';
            }
        }

        

    }

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
    <style>
        body {
            font-family: Arial;
            padding: 40px;
        }
        .box {
            border: 1px solid #ccc;
            padding: 20px;
            width: 400px;
        }
        .message {
            margin-top: 20px;
            color: green;
        }
    </style>
</head>
<body>

<div class="box">

    <h2>Upload File</h2><hr>

    <p><a href="/csv_query">Back to main</a></p>

    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required><br><br>
        <button type="submit">Upload</button>
    </form>

    <?php if ($message): ?>
        <div class="message">
            <?= $message ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>