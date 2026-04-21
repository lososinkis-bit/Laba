<?php
// config.php
session_start();
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/TableModule.php';
require_once __DIR__ . '/core/DesignTable.php';

$designTable = new DesignTable();
?>