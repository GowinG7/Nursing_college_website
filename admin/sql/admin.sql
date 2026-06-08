-- Run this AFTER database.sql
USE bpkmch_nursing;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username = admin , password = admin123
INSERT INTO admins (username, password, full_name, email)
VALUES ('admin', '$2y$10$e0NRSj3Yx1Y0sZJj9XaQ8O1mB5oQqWZxPjQ8K0gQjvN1B0eJ2sQK6', 'Super Admin', 'admin@bpkmch.edu.np')
ON DUPLICATE KEY UPDATE username=username;
