<?php
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: design_list.php');
    exit;
}

try {
    $designTable->delete($id);
    header('Location: design_list.php?deleted=1');
} catch (Exception $e) {
    header('Location: design_list.php?error=1');
}
exit;
?>