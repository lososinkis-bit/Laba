CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    birth_date DATE,
    address TEXT,
    gender ENUM('male', 'female', 'other') DEFAULT 'other',
    interests TEXT,
    vk_profile VARCHAR(255),
    blood_type ENUM('A', 'B', 'AB', 'O') DEFAULT NULL,
    rh_factor ENUM('+', '-') DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;