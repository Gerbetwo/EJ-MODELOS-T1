-- No necesitamos crear la base de datos ni el usuario aquí, 
-- Docker lo hará por nosotros mediante las variables de entorno.
-- Solo nos enfocamos en la estructura.

USE `db-modelos-udenar`;

-- Tabla de Clientes
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    city VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Pedidos
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_client INT NOT NULL,
    product VARCHAR(150) NOT NULL,
    stock INT NOT NULL DEFAULT 1,
    order_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Definición de la llave foránea
    CONSTRAINT fk_cliente
        FOREIGN KEY (id_client) 
        REFERENCES clientes(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de prueba (Opcional, pero útil para verificar que funciona)
INSERT INTO clients (name, email, telephone, city) VALUES 
('Juan Perez', 'juan@example.com', '3001234567', 'Pasto'),
('Maria Lopez', 'maria@example.com', '3109876543', 'Cali');

INSERT INTO orders (id_client, product, stock, order_date) VALUES 
(1, 'Laptop Dell', 1, '2026-03-17'),
(2, 'Monitor LG', 2, '2026-03-16');