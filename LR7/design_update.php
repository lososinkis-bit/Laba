<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: design_list.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    header('Location: design_list.php');
    exit;
}

$errors = $designTable->validate($_POST);

if (!empty($errors)) {
    session_start();
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = $_POST;
    header("Location: design_edit.php?id=$id");
    exit;
}

try {
    $designTable->update($id, $_POST);
    header('Location: design_list.php?updated=1');
} catch (Exception $e) {
    session_start();
    $_SESSION['form_errors'] = [$e->getMessage()];
    $_SESSION['form_old'] = $_POST;
    header("Location: design_edit.php?id=$id");
}
exit;
?>