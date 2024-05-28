CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payments (
    payment_id CHAR(36) PRIMARY KEY UNIQUE,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) NOT NULL,
    description TEXT,
    returnUrl TEXT,
    webhookUrl TEXT,
    webhook_response TEXT,
    create_response TEXT,
    url TEXT,
    id CHAR(36),
    status VARCHAR(50),
    payment_date DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);