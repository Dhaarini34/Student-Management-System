CREATE DATABASE IF NOT EXISTS mentoring_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mentoring_system;

DROP VIEW IF EXISTS student_risk_view;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS counseling_sessions;
DROP TABLE IF EXISTS marks;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS mentor_assignment;
DROP TABLE IF EXISTS mentors;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','mentor','student') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role (role)
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_number VARCHAR(50) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    semester INT NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_students_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mentors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    specialization VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_mentors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE mentor_assignment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mentor_id INT NOT NULL,
    student_id INT NOT NULL,
    assigned_at DATETIME NOT NULL,
    UNIQUE KEY uniq_student_assignment (student_id),
    INDEX idx_ma_mentor (mentor_id),
    CONSTRAINT fk_ma_mentor FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE CASCADE,
    CONSTRAINT fk_ma_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    recorded_at DATETIME NOT NULL,
    INDEX idx_att_student (student_id),
    CONSTRAINT fk_att_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    recorded_at DATETIME NOT NULL,
    INDEX idx_marks_student (student_id),
    CONSTRAINT fk_marks_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE counseling_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mentor_id INT NOT NULL,
    student_id INT NOT NULL,
    session_date DATE NOT NULL,
    notes TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_cs_student (student_id),
    INDEX idx_cs_mentor (mentor_id),
    CONSTRAINT fk_cs_mentor FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE CASCADE,
    CONSTRAINT fk_cs_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_audit_user (user_id),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (full_name, email, password, role) VALUES
('System Administrator', 'admin@college.edu', '$2y$12$f7cMssXdITAUYWDRMfHBzOw2HeFQjlI6kFcX4V2.PO84FVdrv51XK', 'admin'),
('Dr. Maya Mentor', 'mentor1@college.edu', '$2y$12$4GtuJmVQbk1jJD7sA1w03eOWK8DfBhgtSwYiDlbih9W3OJIB5xL0i', 'mentor'),
('Prof. Deni Mentor', 'mentor2@college.edu', '$2y$12$4GtuJmVQbk1jJD7sA1w03eOWK8DfBhgtSwYiDlbih9W3OJIB5xL0i', 'mentor'),
('Ari Student', 'student1@college.edu', '$2y$12$6GmeunMMXyevAKIo4zC/w.9XHP1xDqd863QRrlQPkhS6HQNuOE/kK', 'student'),
('Bela Student', 'student2@college.edu', '$2y$12$6GmeunMMXyevAKIo4zC/w.9XHP1xDqd863QRrlQPkhS6HQNuOE/kK', 'student');

INSERT INTO mentors (user_id, name, specialization, phone, created_at) VALUES
(2, 'Dr. Maya Mentor', 'Academic Counseling', '0812340001', NOW()),
(3, 'Prof. Deni Mentor', 'Career Guidance', '0812340002', NOW());

INSERT INTO students (user_id, student_number, department, semester, created_at) VALUES
(4, 'STU2024001', 'Computer Science', 4, NOW()),
(5, 'STU2024002', 'Information Systems', 2, NOW());

INSERT INTO mentor_assignment (mentor_id, student_id, assigned_at) VALUES
(1, 1, NOW()),
(2, 2, NOW());

INSERT INTO attendance (student_id, percentage, recorded_at) VALUES
(1, 92.5, NOW() - INTERVAL 10 DAY),
(1, 88.0, NOW() - INTERVAL 2 DAY),
(2, 72.0, NOW() - INTERVAL 5 DAY);

INSERT INTO marks (student_id, subject, score, recorded_at) VALUES
(1, 'Algorithms', 81, NOW() - INTERVAL 8 DAY),
(1, 'Databases', 76, NOW() - INTERVAL 2 DAY),
(2, 'Programming', 48, NOW() - INTERVAL 3 DAY);

INSERT INTO counseling_sessions (mentor_id, student_id, session_date, notes, created_at) VALUES
(1, 1, CURDATE() - INTERVAL 12 DAY, 'Discussed exam strategy and attendance consistency.', NOW()),
(2, 2, CURDATE() - INTERVAL 40 DAY, 'Reviewed low performance and recovery plan.', NOW());

CREATE VIEW student_risk_view AS
SELECT
    s.id AS student_id,
    u.full_name AS student_name,
    COALESCE(ROUND((SELECT AVG(a.percentage) FROM attendance a WHERE a.student_id = s.id), 2), 0) AS attendance_percent,
    COALESCE(ROUND((SELECT AVG(m.score) FROM marks m WHERE m.student_id = s.id), 2), 0) AS average_mark,
    (SELECT MAX(cs.session_date) FROM counseling_sessions cs WHERE cs.student_id = s.id) AS last_counseling,
    CASE
        WHEN COALESCE((SELECT AVG(a.percentage) FROM attendance a WHERE a.student_id = s.id), 0) < 75
             OR COALESCE((SELECT AVG(m.score) FROM marks m WHERE m.student_id = s.id), 0) < 50
             OR DATEDIFF(CURDATE(), COALESCE((SELECT MAX(cs.session_date) FROM counseling_sessions cs WHERE cs.student_id = s.id), '1970-01-01')) > 30
            THEN 'HIGH'
        WHEN COALESCE((SELECT AVG(a.percentage) FROM attendance a WHERE a.student_id = s.id), 0) BETWEEN 75 AND 85
            THEN 'MEDIUM'
        ELSE 'SAFE'
    END AS risk_status
FROM students s
JOIN users u ON u.id = s.user_id;
