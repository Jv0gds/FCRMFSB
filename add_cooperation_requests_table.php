<?php
 include 'db.php';
 
 try {
     $pdo->exec("
         CREATE TABLE IF NOT EXISTS cooperation_requests (
             id INT AUTO_INCREMENT PRIMARY KEY,
             lead_id INT NOT NULL,
             user_id INT NOT NULL,
             owner_id INT NOT NULL,
             reason TEXT NOT NULL,
             status VARCHAR(255) NOT NULL DEFAULT 'pending',
             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             FOREIGN KEY (lead_id) REFERENCES leads(id),
             FOREIGN KEY (user_id) REFERENCES users(id),
             FOREIGN KEY (owner_id) REFERENCES users(id)
         )
     ");
 
     $pdo->exec("
         CREATE TABLE IF NOT EXISTS notifications (
             id INT AUTO_INCREMENT PRIMARY KEY,
             user_id INT NOT NULL,
             message TEXT NOT NULL,
             is_read BOOLEAN NOT NULL DEFAULT 0,
             type VARCHAR(255),
             related_id INT,
             created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
             FOREIGN KEY (user_id) REFERENCES users(id)
         )
     ");
 
     echo "Tables 'cooperation_requests' and 'notifications' created successfully.";
 } catch (PDOException $e) {
     die("Table creation failed: " . $e->getMessage());
 }
