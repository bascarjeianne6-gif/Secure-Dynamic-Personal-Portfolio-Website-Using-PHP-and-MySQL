-- Secure Dynamic Portfolio - Jeianne Bascar
-- Import in phpMyAdmin or: mysql -u root -p < database.sql
CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio_db;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS login_attempts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(45) NOT NULL,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (ip, attempted_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
  skey VARCHAR(60) PRIMARY KEY,
  svalue TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skills (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  level TINYINT UNSIGNED NOT NULL DEFAULT 50,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  category VARCHAR(60) NOT NULL,
  description TEXT NOT NULL,
  tags VARCHAR(255) NOT NULL DEFAULT '',
  image VARCHAR(255) NOT NULL DEFAULT '',
  image_alt VARCHAR(255) NOT NULL DEFAULT '',
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS resume_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  section ENUM('Education','Experience','Training & certifications') NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  ip VARCHAR(45) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (ip, created_at)
) ENGINE=InnoDB;

-- ---------- Seed data (from your static pages) ----------
INSERT IGNORE INTO settings (skey, svalue) VALUES
('site_name','Jeianne Bascar'),
('initials','JB'),
('meta_description','Personal portfolio of Jeianne Bascar, a BSINT student.'),
('typing_words','BSINT Student'),
('home_intro','Being a Bachelor of Science in Information Technology (BSINT) student at Negros Oriental State University (NORSU) – Bayawan-Santa Catalina Campus has been a meaningful and challenging journey. As a student, I have experienced different opportunities to learn, improve my skills, and discover my abilities. This course has taught me that Information Technology is not only about computers, programming, and technology but also about creativity, problem-solving, patience, and continuous learning.'),
('portrait','images/bascar.jpg'),
('about_sub','As a student, I enjoy discovering new things, improving my problem-solving abilities, and challenging myself through different activities and projects.'),
('about_background','I am passionate about learning technology, exploring new ideas, and developing my skills in programming, networking, web development, and multimedia. Throughout my academic journey, I continue to challenge myself, gain new experiences, and improve my abilities.'),
('about_education','BSINT student at Negros Oriental State University – Bayawan-Santa Catalina Campus has taught me the importance of patience, dedication, and continuous learning. Throughout my studies, I have gained knowledge and experiences in programming, networking, web development, and other areas of Information Technology.'),
('about_goals','Graduate with a degree in Information Technology.\nImprove my programming and technical skills.\nGain more knowledge and practical experience.\nBecome confident, responsible, and independent.'),
('about_interests','Playing Sports\nExploring new things'),
('skills_sub','I am continuously developing my technical and personal skills through learning and hands-on activities.'),
('projects_sub','A selection of school and personal work. Click an image to see it larger.'),
('contact_email','Jeianne.Bascar@example.com'),
('contact_location','Bayawan City, Negros Oriental, Philippines');

INSERT INTO skills (name, level, sort_order) VALUES
('HTML5',92,1),('CSS3 / Flexbox / Grid',88,2),('JavaScript',78,3),('Responsive design',90,4),
('UI/UX design',85,5),('Figma',90,6),('Branding & logo design',80,7),('Git & GitHub',72,8),
('Photoshop / Illustrator',76,9),('Accessibility basics',68,10);

INSERT INTO projects (title, category, description, tags, image, image_alt, sort_order) VALUES
('Cisco Packet Tracer','Cisco','The network topology illustrates a computer network composed of five different LAN (Local Area Network) sections, each represented by a different color: orange, pink, gray, cyan, and purple. Each section contains switches and desktop computers connected through network cables to establish communication between devices.','Cisco Packet Tracer','images/Cisco.png','Cisco Packet Tracer network topology',1),
('Animation','Animation','The project, titled “The Girl Crossing the Road,” is an interactive mini-game that demonstrates road safety and basic user interaction. It features a girl standing beside a pedestrian crossing, with moving vehicles on the road. The player can control the girl''s movement using the available buttons, such as PLAY, STOP, MOVE LEFT, MOVE RIGHT, and RESET.','Animation','images/animation.png','The Girl Crossing the Road animation with PLAY, STOP, MOVE LEFT, MOVE RIGHT and RESET buttons',2),
('Seminar','Seminar','The Seminar Journal is designed to document the knowledge, experiences, and learnings gained from the seminar titled “Mobile Android and Web Application Development,” held on October 24–25, 2026.','Seminar','images/seminar.png','Seminar journal preview',3);

INSERT INTO resume_items (section, title, description, sort_order) VALUES
('Education','BS Information Technology','Negros Oriental State University (NORSU) – Bayawan-Santa Catalina Campus – 2024',1),
('Education','Senior High School, CSS Strand','Your High School — 2022 to 2024, with honors',2),
('Experience','Cisco Networking and Configuration','2026 to present. I gained hands-on experience in Cisco Networking by creating network topologies using Cisco Packet Tracer. I learned how to connect different network devices, such as routers, switches, computers, servers, and wireless access points.',1),
('Experience','Network setup and Troubleshooting','Present – 2026. I gained experience in setting up computer networks by connecting different devices, such as computers, routers, and switches. I learned how to establish proper network connections, assign IP addresses, and check network configurations.',2),
('Training & certifications','Cisco Academy Certificate','Present – 2026',1);
