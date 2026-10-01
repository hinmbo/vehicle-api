CREATE DATABASE IF NOT EXISTS vehicle_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehicle_db;

DROP TABLE IF EXISTS vehicles;

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_name VARCHAR(255) NOT NULL,
    type_id INT NOT NULL,
    vehicle_type VARCHAR(100) NOT NULL,
    doors INT NOT NULL,
    transmission ENUM('manual', 'automatic') NOT NULL,
    fuel ENUM('petrol', 'diesel', 'hybrid', 'electric') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_price (price),
    INDEX idx_type_id (type_id),
    INDEX idx_transmission (transmission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO vehicles (model_name, type_id, vehicle_type, doors, transmission, fuel, price) VALUES
('Fiat Panda', 2, 'hatchback', 4, 'manual', 'petrol', 90.00),
('Toyota Yaris', 1, 'hatchback', 5, 'automatic', 'hybrid', 150.00),
('BMW X5', 3, 'suv', 5, 'automatic', 'diesel', 250.00),
('Tesla Model 3', 1, 'sedan', 4, 'automatic', 'electric', 200.00),
('Volkswagen Golf', 1, 'hatchback', 5, 'manual', 'petrol', 120.00),
('Nissan Qashqai', 3, 'suv', 5, 'manual', 'diesel', 180.00),
('Hyundai Tucson', 3, 'suv', 5, 'automatic', 'hybrid', 210.00),
('Peugeot 208', 2, 'hatchback', 5, 'automatic', 'petrol', 110.00),
('Audi A4', 1, 'sedan', 4, 'automatic', 'diesel', 230.00),
('Porsche Taycan', 4, 'sports', 4, 'automatic', 'electric', 450.00);