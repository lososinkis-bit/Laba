<?php
class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        $this->pdo = new PDO("mysql:host=localhost;dbname=architectural_bureau;charset=utf8mb4", "root", "");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->pdo;
    }
}
?>