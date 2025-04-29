-- Create the database
CREATE DATABASE IF NOT EXISTS property_db;
USE property_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('user', 'admin', 'government') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create properties table with all required fields
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    short_description TEXT,
    price DECIMAL(15, 2) NOT NULL,
    area DECIMAL(10, 2) NOT NULL,
    location VARCHAR(255) NOT NULL,
    map_link VARCHAR(255),
    listing_type ENUM('sale', 'rent') NOT NULL,
    property_type ENUM('apartment', 'house', 'villa', 'land', 'commercial') NOT NULL,
    owner_name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(100) NOT NULL,
    road_access BOOLEAN DEFAULT FALSE,
    utilities TEXT,
    nearby_landmarks TEXT,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verified_by VARCHAR(100),
    title_deed VARCHAR(255),
    encumbrance VARCHAR(255),
    tax_receipt VARCHAR(255),
    property_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    buyer_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Modify properties table to ensure buyer_id is properly set up
ALTER TABLE properties MODIFY COLUMN buyer_id INT;
ALTER TABLE properties DROP FOREIGN KEY IF EXISTS fk_buyer_id;
ALTER TABLE properties ADD CONSTRAINT fk_buyer_id FOREIGN KEY (buyer_id) REFERENCES users(id);

-- Update the status and buyer_id columns
ALTER TABLE properties MODIFY COLUMN status ENUM('available', 'sold') NOT NULL DEFAULT 'available';

-- Create purchases table if not exists
CREATE TABLE IF NOT EXISTS purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    property_id INT NOT NULL,
    buyer_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    purchase_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id),
    FOREIGN KEY (buyer_id) REFERENCES users(id)
); 