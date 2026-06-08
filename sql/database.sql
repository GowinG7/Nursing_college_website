-- BPKMCH Nursing College Database
-- Import this file in phpMyAdmin or run: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS bpkmch_nursing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bpkmch_nursing;

-- Contact messages from the contact form (AJAX)
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Notices / News
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    posted_on DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Programs / Courses
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    seats INT DEFAULT 0,
    description TEXT NOT NULL
) ENGINE=InnoDB;

-- Faculty
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    designation VARCHAR(150) NOT NULL,
    qualification VARCHAR(200) NOT NULL,
    email VARCHAR(150) DEFAULT NULL
) ENGINE=InnoDB;

-- Sample seed data
INSERT INTO notices (title, body, posted_on) VALUES
('Admissions Open 2082 B.S.', 'Applications are open for BSc Nursing and PCL Nursing programs for the academic year 2082 B.S. Last date to apply: Shrawan 30.', CURDATE()),
('Orientation Program', 'Freshers orientation will be held in the college auditorium at 10:00 AM.', CURDATE()),
('Scholarship Notice', 'Government scholarship forms are now available at the admin office for eligible students.', CURDATE());

INSERT INTO programs (name, duration, seats, description) VALUES
('B.Sc. Nursing', '4 Years', 40, 'A comprehensive undergraduate program affiliated with Tribhuvan University focused on advanced clinical skills, leadership, and community health nursing.'),
('PCL Nursing', '3 Years', 60, 'Proficiency Certificate Level program by CTEVT preparing students for staff nurse roles with strong fundamentals in patient care.'),
('BN (Post Basic)', '2 Years', 20, 'Bachelor in Nursing for registered nurses seeking specialization in critical care, oncology, and public health.'),
('MN (Masters in Nursing)', '2 Years', 15, 'Advanced research-driven program training nurse educators, administrators and specialists.');

INSERT INTO faculty (full_name, designation, qualification, email) VALUES
('Dr. Sushila Sharma', 'Principal', 'PhD in Nursing, MN (Medical-Surgical)', 'principal@bpkmch.edu.np'),
('Mrs. Anita Karki', 'Vice Principal', 'MN (Community Health Nursing)', 'anita.karki@bpkmch.edu.np'),
('Mr. Ramesh Adhikari', 'Senior Lecturer', 'MN (Pediatric Nursing)', 'ramesh.a@bpkmch.edu.np'),
('Mrs. Sunita Poudel', 'Lecturer', 'MN (Oncology Nursing)', 'sunita.p@bpkmch.edu.np'),
('Dr. Bikash Thapa', 'Faculty - Medical', 'MD (Oncology)', 'bikash.t@bpkmch.edu.np'),
('Ms. Rojina Gurung', 'Clinical Instructor', 'BN, ICU Specialist', 'rojina.g@bpkmch.edu.np');
