SET NAMES utf8mb4;
CREATE TABLE admins(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(50) NOT NULL UNIQUE,email VARCHAR(100),password VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE packages(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(50) NOT NULL,tagline VARCHAR(255),price INT NOT NULL,max_hours INT NOT NULL,features TEXT,sort_order INT DEFAULT 0,is_active TINYINT(1) DEFAULT 1) ENGINE=InnoDB;
CREATE TABLE units(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) NOT NULL,status ENUM('available','booked','maintenance','inactive') DEFAULT 'available',notes TEXT) ENGINE=InnoDB;
CREATE TABLE bookings(id INT AUTO_INCREMENT PRIMARY KEY,customer_name VARCHAR(100) NOT NULL,whatsapp VARCHAR(20) NOT NULL,event_date DATE NOT NULL,package_id INT NOT NULL,unit_id INT NULL,event_type VARCHAR(50),start_time TIME NOT NULL,end_time TIME NOT NULL,location VARCHAR(255),notes TEXT,status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
KEY(event_date,status),FOREIGN KEY(package_id) REFERENCES packages(id) ON DELETE RESTRICT,FOREIGN KEY(unit_id) REFERENCES units(id) ON DELETE SET NULL) ENGINE=InnoDB;
CREATE TABLE testimonials(id INT AUTO_INCREMENT PRIMARY KEY,image VARCHAR(120) NOT NULL,customer_name VARCHAR(100) NOT NULL,message TEXT,show_home TINYINT(1) DEFAULT 1,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE portfolio(id INT AUTO_INCREMENT PRIMARY KEY,image VARCHAR(120) NOT NULL,title VARCHAR(120) NOT NULL,category ENUM('Prewedding','Siraman','Akad','Resepsi','Wedding') DEFAULT 'Wedding',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
INSERT INTO packages(name,tagline,price,max_hours,features,sort_order) VALUES
('Bloom','Before the vows, there are stories worth remembering',200000,2,'Prewedding – Siraman\n2 Story Editing Real-Time',1),
('Cherish','For the moment you say ''I do''',300000,4,'Akad Only\n5 Story Editing Real-Time',2),
('Lumiere','From ''I do'' to the celebration of forever',400000,8,'Akad + Resepsi\n10+ Story Editing Real-Time',3);
INSERT INTO units(name,status) VALUES('Unit 1 – iPhone 13','available');
INSERT INTO admins(username,email,password) VALUES('admin','admin@example.com','$2y$12$YWOBtae4TjR/BxGLtRhcjOHt1xk6LlAtqy/gtrdH6JOqPF2hM2BO2');
