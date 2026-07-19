CREATE TABLE IF NOT EXISTS early_access_subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  product VARCHAR(80) NOT NULL DEFAULT 'Beyond Health',
  source VARCHAR(120) DEFAULT 'landing_page',
  status ENUM('active','unsubscribed','bounced') NOT NULL DEFAULT 'active',
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default login: admin@beyondimagination.co.technology / ChangeMeNow123!
-- Change this immediately after install.
INSERT IGNORE INTO admin_users (email, password_hash) VALUES
('admin@beyondimagination.co.technology', '$2y$10$RzE5Sxl4tcIu8CS3rduzUejOpzO8daal23rNIxgZbeqZyM1cCEyUu');
