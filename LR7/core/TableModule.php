<?php
require_once __DIR__ . '/Database.php';

abstract class TableModule {
    protected $pdo;
    protected $tableName;
    
    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    abstract public function validate($data);
    abstract public function insert($data);
    abstract public function update($id, $data);
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>