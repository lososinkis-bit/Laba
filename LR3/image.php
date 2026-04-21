<?php
// image.php
require_once 'config.php';

if (!is_auth()) {
    header("Location: login.php");
    exit;
}

$file = $_GET['file'] ?? '';
if (empty($file)) die("Файл не указан");

$file = basename($file);
$path = __DIR__ . '/uploads/' . $file;

if (!file_exists($path)) die("Файл не найден");

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);
finfo_close($finfo);

header("Content-Type: $mime");
header("Content-Length: " . filesize($path));
readfile($path);
exit;
?>