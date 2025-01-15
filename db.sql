CREATE DATABASE syncgo;

USE syncgo;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    place VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    destinations TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
