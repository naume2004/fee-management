-- School Student Database Setup SQL
-- This script populates the FeeFlow system with realistic student data

-- Clear existing data (optional - comment out if you want to keep existing records)
DELETE FROM payment_deadlines;
DELETE FROM queue;
DELETE FROM appointments;
DELETE FROM fees;
DELETE FROM students;

-- Insert Students
INSERT INTO students (student_id, name, email, password, course) VALUES 
('S101', 'Sarah Johnson', 'sarah.johnson@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Computer Science'),
('S102', 'Jackie Smith', 'jackie.smith@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Business Administration'),
('S103', 'Winnie Davis', 'winnie.davis@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Engineering'),
('S104', 'Michael Brown', 'michael.brown@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Medicine'),
('S105', 'Emily Wilson', 'emily.wilson@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Law'),
('S106', 'David Miller', 'david.miller@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Architecture'),
('S107', 'Jessica Taylor', 'jessica.taylor@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Arts & Design'),
('S108', 'James Anderson', 'james.anderson@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Education'),
('S109', 'Lisa Moore', 'lisa.moore@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Nursing'),
('S110', 'Christopher Garcia', 'christopher.garcia@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Finance'),
('S111', 'Amanda Martinez', 'amanda.martinez@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Psychology'),
('S112', 'Ryan Thompson', 'ryan.thompson@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Physics'),
('S113', 'Sophia Lee', 'sophia.lee@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Chemistry'),
('S114', 'Daniel White', 'daniel.white@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Biology'),
('S115', 'Olivia Harris', 'olivia.harris@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Mathematics');

-- Insert Fee Records
-- Tuition Fees
INSERT INTO fees (student_id, amount, fee_type, status, payment_method, payment_date, created_at) VALUES 
('S101', 1200.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S102', 1100.00, 'Tuition Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY)),
('S103', 1300.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S104', 1500.00, 'Tuition Fee', 'Paid', 'Mobile Money', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
('S105', 1200.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S106', 1350.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S107', 1100.00, 'Tuition Fee', 'Paid', 'Bank Transfer', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY)),
('S108', 1200.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S109', 1400.00, 'Tuition Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
('S110', 1250.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S111', 1200.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S112', 1300.00, 'Tuition Fee', 'Paid', 'Mobile Money', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY)),
('S113', 1200.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S114', 1350.00, 'Tuition Fee', 'Pending', NULL, NULL, NOW()),
('S115', 1100.00, 'Tuition Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY));

-- Library Fees
INSERT INTO fees (student_id, amount, fee_type, status, payment_method, payment_date, created_at) VALUES 
('S101', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
('S102', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
('S103', 200.00, 'Lab Fee', 'Pending', NULL, NULL, NOW()),
('S104', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY)),
('S105', 150.00, 'Library Fee', 'Pending', NULL, NULL, NOW()),
('S106', 250.00, 'Lab Fee', 'Pending', NULL, NULL, NOW()),
('S107', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY)),
('S108', 150.00, 'Library Fee', 'Pending', NULL, NULL, NOW()),
('S109', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY)),
('S110', 200.00, 'Lab Fee', 'Pending', NULL, NULL, NOW()),
('S111', 150.00, 'Library Fee', 'Pending', NULL, NULL, NOW()),
('S112', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY)),
('S113', 200.00, 'Lab Fee', 'Pending', NULL, NULL, NOW()),
('S114', 150.00, 'Library Fee', 'Pending', NULL, NULL, NOW()),
('S115', 150.00, 'Library Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Student Activity Fees
INSERT INTO fees (student_id, amount, fee_type, status, payment_method, payment_date, created_at) VALUES 
('S101', 75.00, 'Student Activity Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY)),
('S102', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S103', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S104', 75.00, 'Student Activity Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 27 DAY)),
('S105', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S106', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S107', 75.00, 'Student Activity Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 33 DAY)),
('S108', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S109', 75.00, 'Student Activity Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 23 DAY)),
('S110', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S111', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S112', 75.00, 'Student Activity Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY)),
('S113', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S114', 75.00, 'Student Activity Fee', 'Pending', NULL, NULL, NOW()),
('S115', 75.00, 'Student Activity Fee', 'Paid', 'Card', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY));

-- Insert Payment Deadlines
INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at) VALUES 
(1, 1, DATE_SUB(CURDATE(), INTERVAL 10 DAY), TRUE, 2, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()),
(1, 16, DATE_SUB(CURDATE(), INTERVAL 15 DAY), TRUE, 1, DATE_SUB(NOW(), INTERVAL 8 DAY), NOW()),
(3, 4, DATE_SUB(CURDATE(), INTERVAL 5 DAY), TRUE, 1, DATE_SUB(NOW(), INTERVAL 2 DAY), NOW()),
(3, 19, DATE_SUB(CURDATE(), INTERVAL 8 DAY), TRUE, 2, DATE_SUB(NOW(), INTERVAL 4 DAY), NOW()),
(5, 7, DATE_SUB(CURDATE(), INTERVAL 3 DAY), TRUE, 1, NOW(), NOW()),
(6, 10, DATE_SUB(CURDATE(), INTERVAL 12 DAY), TRUE, 3, DATE_SUB(NOW(), INTERVAL 6 DAY), NOW()),
(8, 13, CURDATE(), FALSE, 0, NULL, NOW()),
(10, 19, DATE_SUB(CURDATE(), INTERVAL 7 DAY), TRUE, 2, DATE_SUB(NOW(), INTERVAL 4 DAY), NOW()),
(11, 22, CURDATE(), FALSE, 0, NULL, NOW()),
(14, 25, DATE_SUB(CURDATE(), INTERVAL 2 DAY), TRUE, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), NOW());

-- Insert Appointments
INSERT INTO appointments (student_id, appointment_date, appointment_time, purpose, status, created_at) VALUES 
('S101', DATE_ADD(CURDATE(), INTERVAL 2 DAY), '10:00:00', 'Payment arrangement', 'Scheduled', NOW()),
('S102', DATE_ADD(CURDATE(), INTERVAL 5 DAY), '14:30:00', 'Fee inquiry', 'Scheduled', NOW()),
('S103', DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', 'Payment plan discussion', 'Scheduled', NOW()),
('S104', DATE_SUB(CURDATE(), INTERVAL 2 DAY), '11:00:00', 'Payment confirmation', 'Completed', NOW()),
('S105', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '15:00:00', 'Fee clearance', 'Scheduled', NOW()),
('S106', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '13:00:00', 'Payment arrangement', 'Scheduled', NOW()),
('S107', DATE_SUB(CURDATE(), INTERVAL 5 DAY), '10:30:00', 'Fee payment', 'Completed', NOW()),
('S108', DATE_ADD(CURDATE(), INTERVAL 4 DAY), '11:30:00', 'Installment plan', 'Scheduled', NOW()),
('S109', DATE_SUB(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'Fee clearance confirmation', 'Completed', NOW()),
('S110', DATE_ADD(CURDATE(), INTERVAL 6 DAY), '10:00:00', 'Payment discussion', 'Scheduled', NOW());

-- Insert Queue Data
INSERT INTO queue (token_number, student_id, status, counter_number, created_at) VALUES 
(201, 'S103', 'Serving', 1, NOW()),
(202, 'S105', 'Waiting', NULL, DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(203, 'S108', 'Waiting', NULL, DATE_SUB(NOW(), INTERVAL 3 MINUTE)),
(204, 'S110', 'Waiting', NULL, DATE_SUB(NOW(), INTERVAL 1 MINUTE)),
(205, NULL, 'Waiting', NULL, DATE_SUB(NOW(), INTERVAL 30 SECOND)),
(206, NULL, 'Waiting', NULL, DATE_SUB(NOW(), INTERVAL 15 SECOND));
