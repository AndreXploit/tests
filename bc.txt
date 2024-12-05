<?php
// Simple PHP File Manager

// Current directory
$currentDir = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();
$parentDir = dirname($currentDir);

// Change directory
if (isset($_GET['change_dir'])) {
    $currentDir = realpath($_GET['change_dir']);
}

// Delete a file
if (isset($_GET['delete'])) {
    $fileToDelete = realpath($currentDir . DIRECTORY_SEPARATOR . $_GET['delete']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
    }
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}

// Upload file
if (isset($_FILES['file'])) {
    $uploadedFile = $_FILES['file'];
    move_uploaded_file($uploadedFile['tmp_name'], $currentDir . DIRECTORY_SEPARATOR . $uploadedFile['name']);
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}

// Display file contents
if (isset($_GET['view'])) {
    $fileToView = realpath($currentDir . DIRECTORY_SEPARATOR . $_GET['view']);
    if (file_exists($fileToView)) {
        echo "<pre>" . htmlspecialchars(file_get_contents($fileToView)) . "</pre>";
        exit;
    }
}

// HTML layout
echo "<h2>PHP File Manager</h2>";
echo "<p>Current Directory: $currentDir</p>";

// Navigation
echo "<a href='?dir=" . urlencode($parentDir) . "'>Go to Parent Directory</a>";
echo "<hr>";

// Upload form
echo "<form method='POST' enctype='multipart/form-data'>";
echo "Upload File: <input type='file' name='file'>";
echo "<button type='submit'>Upload</button>";
echo "</form>";
echo "<hr>";

// File and directory listing
$files = scandir($currentDir);
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Name</th><th>Type</th><th>Actions</th></tr>";
foreach ($files as $file) {
    if ($file === "." || $file === "..") continue;
    $filePath = $currentDir . DIRECTORY_SEPARATOR . $file;
    $isDir = is_dir($filePath) ? "Directory" : "File";
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td>$isDir</td>";
    echo "<td>";
    if ($isDir === "Directory") {
        echo "<a href='?dir=" . urlencode($filePath) . "'>Open</a>";
    } else {
        echo "<a href='?view=" . urlencode($file) . "&dir=" . urlencode($currentDir) . "'>View</a> | ";
        echo "<a href='?delete=" . urlencode($file) . "&dir=" . urlencode($currentDir) . "' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
    }
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
?>
