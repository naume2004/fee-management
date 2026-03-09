-- ============================================================
--  FeeFlow — Primary School Seed Data  (Nursery → Primary 7)
--  Run AFTER migrate_schema.php
--  All amounts are annual totals (3 terms per academic year)
-- ============================================================

-- Clear existing data
DELETE FROM payment_deadlines;
DELETE FROM queue;
DELETE FROM appointments;
DELETE FROM fees;
DELETE FROM students;

-- ── Students ──────────────────────────────────────────────
-- Password hash = 'password123'  ($2y$10$N9qo8uLO...)
INSERT INTO students
  (student_id, name, email, password,
   gender, class_name, student_status, academic_year, duration, course)
VALUES
-- Nursery
('S101','Aisha Nakato',      'aisha.nakato@school.edu',      '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Nursery',  'Active',   '2025/2026','3 Terms','Early Childhood'),
('S102','Brian Ssekandi',    'brian.ssekandi@school.edu',    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Nursery',  'Active',   '2025/2026','3 Terms','Early Childhood'),
-- Primary 1
('S103','Christine Namukasa','christine.namukasa@school.edu','$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 1','Active',   '2025/2026','3 Terms','Lower Primary'),
('S104','Denis Kizito',      'denis.kizito@school.edu',      '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 1','Active',   '2025/2026','3 Terms','Lower Primary'),
-- Primary 2
('S105','Esther Atim',       'esther.atim@school.edu',       '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 2','Active',   '2025/2026','3 Terms','Lower Primary'),
('S106','Fred Mugisha',      'fred.mugisha@school.edu',      '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 2','Suspended','2025/2026','3 Terms','Lower Primary'),
-- Primary 3
('S107','Grace Auma',        'grace.auma@school.edu',        '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 3','Active',   '2025/2026','3 Terms','Middle Primary'),
('S108','Henry Okello',      'henry.okello@school.edu',      '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 3','Active',   '2025/2026','3 Terms','Middle Primary'),
-- Primary 4
('S109','Irene Nambi',       'irene.nambi@school.edu',       '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 4','Active',   '2025/2026','3 Terms','Middle Primary'),
('S110','John Wasswa',       'john.wasswa@school.edu',       '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 4','Inactive', '2025/2026','3 Terms','Middle Primary'),
-- Primary 5
('S111','Kato Mulindwa',     'kato.mulindwa@school.edu',     '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 5','Active',   '2025/2026','3 Terms','Upper Primary'),
('S112','Lydia Nakaziba',    'lydia.nakaziba@school.edu',    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 5','Active',   '2025/2026','3 Terms','Upper Primary'),
-- Primary 6
('S113','Moses Byamukama',   'moses.byamukama@school.edu',   '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 6','Active',   '2025/2026','3 Terms','Upper Primary'),
('S114','Norah Kiconco',     'norah.kiconco@school.edu',     '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 6','Active',   '2025/2026','3 Terms','Upper Primary'),
-- Primary 7
('S115','Patrick Tumusiime', 'patrick.tumusiime@school.edu', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Male',  'Primary 7','Active',   '2025/2026','3 Terms','Upper Primary'),
('S116','Queen Namusisi',    'queen.namusisi@school.edu',    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86.bzfVIJ8a', 'Female','Primary 7','Active',   '2025/2026','3 Terms','Upper Primary');


-- ── Fees (annual, 3 fee types each) ──────────────────────────────────────────
-- Nursery — total ~$1,350 per student
INSERT INTO fees (student_id, amount, fee_type, status, payment_method, payment_date, created_at) VALUES
('S101', 800.00,'Tuition Fee',     'Paid',   'Mobile Money', DATE_SUB(NOW(),INTERVAL 20 DAY), DATE_SUB(NOW(),INTERVAL 30 DAY)),
('S101', 150.00,'Development Fee', 'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 20 DAY), DATE_SUB(NOW(),INTERVAL 30 DAY)),
('S101', 300.00,'School Meals',    'Pending', NULL, NULL,     NOW()),
('S101', 100.00,'Sports Fee',      'Paid',   'Bank Transfer',DATE_SUB(NOW(),INTERVAL 18 DAY), DATE_SUB(NOW(),INTERVAL 28 DAY)),

('S102', 800.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S102', 150.00,'Development Fee', 'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 10 DAY), DATE_SUB(NOW(),INTERVAL 15 DAY)),
('S102', 300.00,'School Meals',    'Pending', NULL, NULL,     NOW()),
('S102', 100.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),

-- Primary 1 — total ~$725
('S103', 550.00,'Tuition Fee',     'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 25 DAY), DATE_SUB(NOW(),INTERVAL 35 DAY)),
('S103', 100.00,'Development Fee', 'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 25 DAY), DATE_SUB(NOW(),INTERVAL 35 DAY)),
('S103',  75.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),

('S104', 550.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S104', 100.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S104',  75.00,'Sports Fee',      'Paid',   'Mobile Money', DATE_SUB(NOW(),INTERVAL 8 DAY), DATE_SUB(NOW(),INTERVAL 12 DAY)),

-- Primary 2 — total ~$755
('S105', 580.00,'Tuition Fee',     'Paid',   'Bank Transfer',DATE_SUB(NOW(),INTERVAL 22 DAY), DATE_SUB(NOW(),INTERVAL 30 DAY)),
('S105', 100.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S105',  75.00,'Sports Fee',      'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 22 DAY), DATE_SUB(NOW(),INTERVAL 30 DAY)),

('S106', 580.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S106', 100.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S106',  75.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),

-- Primary 3 — total ~$825
('S107', 620.00,'Tuition Fee',     'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 15 DAY), DATE_SUB(NOW(),INTERVAL 22 DAY)),
('S107', 120.00,'Development Fee', 'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 15 DAY), DATE_SUB(NOW(),INTERVAL 22 DAY)),
('S107',  85.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),

('S108', 620.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S108', 120.00,'Development Fee', 'Paid',   'Mobile Money', DATE_SUB(NOW(),INTERVAL 5 DAY), DATE_SUB(NOW(),INTERVAL 10 DAY)),
('S108',  85.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),

-- Primary 4 — total ~$865
('S109', 660.00,'Tuition Fee',     'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 12 DAY), DATE_SUB(NOW(),INTERVAL 20 DAY)),
('S109', 120.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S109',  85.00,'Sports Fee',      'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 12 DAY), DATE_SUB(NOW(),INTERVAL 20 DAY)),

('S110', 660.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S110', 120.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S110',  85.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),

-- Primary 5 — total ~$1,010
('S111', 720.00,'Tuition Fee',     'Paid',   'Bank Transfer',DATE_SUB(NOW(),INTERVAL 18 DAY), DATE_SUB(NOW(),INTERVAL 25 DAY)),
('S111', 140.00,'Development Fee', 'Paid',   'Bank Transfer',DATE_SUB(NOW(),INTERVAL 18 DAY), DATE_SUB(NOW(),INTERVAL 25 DAY)),
('S111',  95.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),
('S111',  55.00,'Library Fee',     'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 16 DAY), DATE_SUB(NOW(),INTERVAL 20 DAY)),

('S112', 720.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S112', 140.00,'Development Fee', 'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 7 DAY), DATE_SUB(NOW(),INTERVAL 12 DAY)),
('S112',  95.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),
('S112',  55.00,'Library Fee',     'Pending', NULL, NULL,     NOW()),

-- Primary 6 — total ~$1,070
('S113', 760.00,'Tuition Fee',     'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 30 DAY), DATE_SUB(NOW(),INTERVAL 38 DAY)),
('S113', 150.00,'Development Fee', 'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 30 DAY), DATE_SUB(NOW(),INTERVAL 38 DAY)),
('S113', 100.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),
('S113',  60.00,'Library Fee',     'Paid',   'Mobile Money', DATE_SUB(NOW(),INTERVAL 28 DAY), DATE_SUB(NOW(),INTERVAL 35 DAY)),

('S114', 760.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S114', 150.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S114', 100.00,'Sports Fee',      'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 3 DAY), DATE_SUB(NOW(),INTERVAL 8 DAY)),
('S114',  60.00,'Library Fee',     'Pending', NULL, NULL,     NOW()),

-- Primary 7 — total ~$1,330
('S115', 950.00,'Tuition Fee',     'Paid',   'Bank Transfer',DATE_SUB(NOW(),INTERVAL 14 DAY), DATE_SUB(NOW(),INTERVAL 20 DAY)),
('S115', 180.00,'Development Fee', 'Paid',   'Bank Transfer',DATE_SUB(NOW(),INTERVAL 14 DAY), DATE_SUB(NOW(),INTERVAL 20 DAY)),
('S115', 120.00,'Sports Fee',      'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 14 DAY), DATE_SUB(NOW(),INTERVAL 20 DAY)),
('S115',  80.00,'Library Fee',     'Pending', NULL, NULL,     NOW()),

('S116', 950.00,'Tuition Fee',     'Pending', NULL, NULL,     NOW()),
('S116', 180.00,'Development Fee', 'Pending', NULL, NULL,     NOW()),
('S116', 120.00,'Sports Fee',      'Pending', NULL, NULL,     NOW()),
('S116',  80.00,'Library Fee',     'Paid',   'Card',         DATE_SUB(NOW(),INTERVAL 6 DAY), DATE_SUB(NOW(),INTERVAL 10 DAY));


-- ── Payment Deadlines (for pending fees by student_id VARCHAR) ────────────────
INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S101', id, DATE_ADD(CURDATE(), INTERVAL 10 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S101' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S102', id, DATE_SUB(CURDATE(), INTERVAL 5 DAY), TRUE, 2, DATE_SUB(NOW(),INTERVAL 2 DAY), NOW()
FROM fees WHERE student_id='S102' AND status='Pending' ORDER BY id ASC LIMIT 3;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S103', id, DATE_ADD(CURDATE(), INTERVAL 7 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S103' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S104', id, DATE_SUB(CURDATE(), INTERVAL 3 DAY), TRUE, 1, DATE_SUB(NOW(),INTERVAL 1 DAY), NOW()
FROM fees WHERE student_id='S104' AND status='Pending' ORDER BY id ASC LIMIT 2;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S105', id, DATE_ADD(CURDATE(), INTERVAL 14 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S105' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S106', id, DATE_SUB(CURDATE(), INTERVAL 12 DAY), TRUE, 3, DATE_SUB(NOW(),INTERVAL 4 DAY), NOW()
FROM fees WHERE student_id='S106' AND status='Pending' ORDER BY id ASC LIMIT 3;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S107', id, DATE_ADD(CURDATE(), INTERVAL 5 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S107' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S108', id, DATE_SUB(CURDATE(), INTERVAL 8 DAY), TRUE, 2, DATE_SUB(NOW(),INTERVAL 3 DAY), NOW()
FROM fees WHERE student_id='S108' AND status='Pending' ORDER BY id ASC LIMIT 2;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S109', id, DATE_ADD(CURDATE(), INTERVAL 21 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S109' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S110', id, DATE_SUB(CURDATE(), INTERVAL 15 DAY), TRUE, 3, DATE_SUB(NOW(),INTERVAL 5 DAY), NOW()
FROM fees WHERE student_id='S110' AND status='Pending' ORDER BY id ASC LIMIT 3;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S111', id, DATE_ADD(CURDATE(), INTERVAL 3 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S111' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S112', id, DATE_SUB(CURDATE(), INTERVAL 7 DAY), TRUE, 2, DATE_SUB(NOW(),INTERVAL 2 DAY), NOW()
FROM fees WHERE student_id='S112' AND status='Pending' ORDER BY id ASC LIMIT 3;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S113', id, DATE_ADD(CURDATE(), INTERVAL 10 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S113' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S114', id, DATE_SUB(CURDATE(), INTERVAL 4 DAY), TRUE, 1, DATE_SUB(NOW(),INTERVAL 1 DAY), NOW()
FROM fees WHERE student_id='S114' AND status='Pending' ORDER BY id ASC LIMIT 3;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S115', id, DATE_ADD(CURDATE(), INTERVAL 30 DAY), FALSE, 0, NULL, NOW()
FROM fees WHERE student_id='S115' AND status='Pending' LIMIT 1;

INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count, last_reminder_date, created_at)
SELECT 'S116', id, DATE_SUB(CURDATE(), INTERVAL 6 DAY), TRUE, 2, DATE_SUB(NOW(),INTERVAL 2 DAY), NOW()
FROM fees WHERE student_id='S116' AND status='Pending' ORDER BY id ASC LIMIT 3;


-- ── Queue ─────────────────────────────────────────────────
INSERT INTO queue (token_number, student_id, status, counter_number, created_at) VALUES
(301,'S102','Serving',  1,   NOW()),
(302,'S106','Waiting',  NULL, DATE_SUB(NOW(),INTERVAL 8  MINUTE)),
(303,'S110','Waiting',  NULL, DATE_SUB(NOW(),INTERVAL 5  MINUTE)),
(304,'S114','Waiting',  NULL, DATE_SUB(NOW(),INTERVAL 3  MINUTE)),
(305,'S116','Waiting',  NULL, DATE_SUB(NOW(),INTERVAL 1  MINUTE));

-- ── Appointments ──────────────────────────────────────────
INSERT INTO appointments (student_id, appointment_date, appointment_time, purpose, status, created_at) VALUES
('S101', DATE_ADD(CURDATE(),INTERVAL 2 DAY), '09:00:00','School Meals payment arrangement','Scheduled',NOW()),
('S102', DATE_ADD(CURDATE(),INTERVAL 1 DAY), '10:30:00','Fee balance discussion',          'Scheduled',NOW()),
('S104', DATE_ADD(CURDATE(),INTERVAL 3 DAY), '14:00:00','Tuition fee payment plan',        'Scheduled',NOW()),
('S106', DATE_SUB(CURDATE(),INTERVAL 2 DAY), '11:00:00','Suspension fee clearance',        'Completed',NOW()),
('S108', DATE_ADD(CURDATE(),INTERVAL 5 DAY), '09:30:00','Tuition installment plan',        'Scheduled',NOW()),
('S110', DATE_ADD(CURDATE(),INTERVAL 4 DAY), '15:30:00','Re-enrollment discussion',        'Scheduled',NOW()),
('S112', DATE_ADD(CURDATE(),INTERVAL 2 DAY), '13:00:00','Library & sports fee clearance',  'Scheduled',NOW()),
('S114', DATE_SUB(CURDATE(),INTERVAL 1 DAY), '10:00:00','Tuition fee reminder follow-up',  'Completed',NOW()),
('S116', DATE_ADD(CURDATE(),INTERVAL 6 DAY), '11:30:00','P7 fees payment schedule',        'Scheduled',NOW());
