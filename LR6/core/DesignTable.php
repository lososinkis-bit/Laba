<?php
// core/DesignTable.php
require_once __DIR__ . '/TableModule.php';

class DesignTable extends TableModule {
    protected $tableName = 'design';
    private $uploadDir = 'uploads/';
    
    public function __construct() {
        parent::__construct();
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    public function getAllWithCategories() {
        $sql = "SELECT d.*, c.name as category_name 
                FROM design d 
                LEFT JOIN categories c ON d.id_brand = c.id 
                ORDER BY d.id DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = "Название проекта обязательно";
        } elseif (strlen($data['name']) < 3) {
            $errors[] = "Название должно быть не менее 3 символов";
        }
        
        if (empty($data['id_brand']) || $data['id_brand'] <= 0) {
            $errors[] = "Выберите категорию";
        }
        
        if (empty($data['cost']) || !is_numeric($data['cost']) || $data['cost'] <= 0) {
            $errors[] = "Стоимость должна быть положительным числом";
        }
        
        return $errors;
    }
    
    private function uploadFile($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'no_img.png';
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedTypes)) {
            throw new Exception("Разрешены только изображения (JPEG, PNG, GIF)");
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $ext;
        $destination = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        return 'no_img.png';
    }
    
    private function deleteFile($filename) {
        if ($filename && $filename != 'no_img.png') {
            $filepath = $this->uploadDir . $filename;
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
    }
    
    public function insert($data) {
        $img_path = 'no_img.png';
        
        if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] === UPLOAD_ERR_OK) {
            try {
                $img_path = $this->uploadFile($_FILES['img_path']);
            } catch (Exception $e) {
                throw new Exception("Ошибка загрузки файла: " . $e->getMessage());
            }
        }
        
        $sql = "INSERT INTO design (img_path, name, id_brand, description, cost) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$img_path, $data['name'], $data['id_brand'], $data['description'] ?? '', $data['cost']]);
    }
    
    public function update($id, $data) {
        $record = $this->getById($id);
        if (!$record) {
            throw new Exception("Запись не найдена");
        }
        
        $img_path = $record['img_path'];
        
        if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] === UPLOAD_ERR_OK) {
            try {
                $new_file = $this->uploadFile($_FILES['img_path']);
                if ($new_file != 'no_img.png') {
                    $this->deleteFile($record['img_path']);
                    $img_path = $new_file;
                }
            } catch (Exception $e) {
                throw new Exception("Ошибка загрузки файла: " . $e->getMessage());
            }
        }
        
        $sql = "UPDATE design SET img_path = ?, name = ?, id_brand = ?, description = ?, cost = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$img_path, $data['name'], $data['id_brand'], $data['description'] ?? '', $data['cost'], $id]);
    }
    
    public function delete($id) {
        $record = $this->getById($id);
        if ($record) {
            $this->deleteFile($record['img_path']);
        }
        $stmt = $this->pdo->prepare("DELETE FROM design WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>