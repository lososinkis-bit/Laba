<?php
require_once __DIR__ . '/TableModule.php';

class DesignTable extends TableModule {
    protected $tableName = 'design';
    private $uploadDir = 'uploads/';
    
    public function __construct() {
        parent::__construct();
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir);
        }
    }
    
    public function getAllWithCategories() {
        $stmt = $this->pdo->query("SELECT d.*, c.name as category_name 
                                   FROM design d 
                                   LEFT JOIN categories c ON d.id_brand = c.id 
                                   ORDER BY d.id DESC");
        return $stmt->fetchAll();
    }
    
    public function validate($data) {
        $errors = [];
        if (empty($data['name'])) $errors[] = "Введите название";
        if (empty($data['id_brand']) || $data['id_brand'] <= 0) $errors[] = "Выберите категорию";
        if (empty($data['cost']) || $data['cost'] <= 0) $errors[] = "Стоимость должна быть больше 0";
        return $errors;
    }
    
    private function uploadFile($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) return 'no_img.png';
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $this->uploadDir . $filename);
        return $filename;
    }
    
    private function deleteFile($filename) {
        if ($filename && $filename != 'no_img.png') {
            $filepath = $this->uploadDir . $filename;
            if (file_exists($filepath)) unlink($filepath);
        }
    }
    
    public function insert($data) {
        $img = 'no_img.png';
        if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] === UPLOAD_ERR_OK) {
            $img = $this->uploadFile($_FILES['img_path']);
        }
        $stmt = $this->pdo->prepare("INSERT INTO design (img_path, name, id_brand, description, cost) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$img, $data['name'], $data['id_brand'], $data['description'] ?? '', $data['cost']]);
    }
    
    public function update($id, $data) {
        $old = $this->getById($id);
        $img = $old['img_path'];
        
        if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] === UPLOAD_ERR_OK) {
            $this->deleteFile($old['img_path']);
            $img = $this->uploadFile($_FILES['img_path']);
        }
        
        $stmt = $this->pdo->prepare("UPDATE design SET img_path=?, name=?, id_brand=?, description=?, cost=? WHERE id=?");
        return $stmt->execute([$img, $data['name'], $data['id_brand'], $data['description'] ?? '', $data['cost'], $id]);
    }
    
    public function delete($id) {
        $old = $this->getById($id);
        if ($old) $this->deleteFile($old['img_path']);
        $stmt = $this->pdo->prepare("DELETE FROM design WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>