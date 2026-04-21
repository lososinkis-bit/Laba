<?php
// design_delete.php - ТОЛЬКО POST запросы
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: design.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if (!$id) {
    header('Location: design.php');
    exit;
}

try {
    $designTable->delete($id);
    header('Location: design.php?deleted=1');
} catch (Exception $e) {
    header('Location: design.php?error=1');
}
exit;
?>