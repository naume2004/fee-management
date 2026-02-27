<?php
/**
 * Comprehensive Test Database Seeder for FeeFlow
 * Populates the database with realistic test data for various scenarios
 */

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_fees_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Ensure staff table exists and clear data
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) DEFAULT NULL
)");

echo "<h2>Clearing existing test data...</h2>";
mysqli_query($conn, "DELETE FROM payment_deadlines");
mysqli_query($conn, "DELETE FROM queue");
mysqli_query($conn, "DELETE FROM appointments");
mysqli_query($conn, "DELETE FROM fees");
mysqli_query($conn, "DELETE FROM students");
mysqli_query($conn, "DELETE FROM staff");

$hashed_pass = password_hash('password123', PASSWORD_DEFAULT);

// ============= COMPREHENSIVE STUDENT DATA =============
$students_data = [
    ('S101', 'Sarah Johnson', 'sarah.johnson@school.edu', '$hashed_pass', 'Computer Science', 'Year 2'),
    ('S102', 'Jackie Wilson', 'jackie.wilson@school.edu', '$hashed_pass', 'Business Administration', 'Year 3'),
    ('S103', 'Winnie Omondi', 'winnie.omondi@school.edu', '$hashed_pass', 'Engineering', 'Year 1'),
    ('S104', 'David Kipchoge', 'david.kipchoge@school.edu', '$hashed_pass', 'Computer Science', 'Year 3'),
    ('S105', 'Emily Chen', 'emily.chen@school.edu', '$hashed_pass', 'Mathematics', 'Year 2'),
    ('S106', 'Michael Brown', 'michael.brown@school.edu', '$hashed_pass', 'Physics', 'Year 1'),
    ('S107', 'Lisa Anderson', 'lisa.anderson@school.edu', '$hashed_pass', 'Commerce', 'Year 4'),
    ('S108', 'James Mwangi', 'james.mwangi@school.edu', '$hashed_pass', 'Engineering', 'Year 2'),
    ('S109', 'Catherine Kipkemboi', 'catherine.kipkemboi@school.edu', '$hashed_pass', 'Law', 'Year 3'),
    ('S110', 'Peter Kariuki', 'peter.kariuki@school.edu', '$hashed_pass', 'Medicine', 'Year 4'),
    ('S111', 'Rachel Mutua', 'rachel.mutua@school.edu', '$hashed_pass', 'Nursing', 'Year 2'),
    ('S112', 'Thomas Kiplagat', 'thomas.kiplagat@school.edu', '$hashed_pass', 'Agriculture', 'Year 1'),
    ('S113', 'Jennifer Njoroge', 'jennifer.njoroge@school.edu', '$hashed_pass', 'Education', 'Year 3'),
    ('S114', 'Daniel Kipketer', 'daniel.kipketer@school.edu', '$hashed_pass', 'Computer Science', 'Year 1'),
    ('S115', 'Victoria Kimani', 'victoria.kimani@school.edu', '$hashed_pass', 'Psychology', 'Year 2'),
];

echo "<h3>Inserting Students...</h3>";
foreach ($students_data as $student) {
    list($sid, $name, $email, $pass, $course, $year) = $student;
    $sql = "INSERT INTO students (student_id, name, email, password, course) VALUES 
            ('$sid', '$name', '$email', '$pass', '$course - $year')";
    if (mysqli_query($conn, $sql)) {
        echo "✓ Student inserted: $sid - $name<br>";
    }
}

// insert a sample staff user for admin login
$admin_password_hashed = password_hash('admin123', PASSWORD_DEFAULT);
mysqli_query($conn, "INSERT INTO staff (username, password, name) VALUES ('admin', '$admin_password_hashed', 'Admin User')");
echo "✓ Staff account inserted: admin<br>";

// ============= COMPREHENSIVE FEES DATA =============
echo "<h3>Inserting Fees with Various Statuses...</h3>";

$fees_data = [
    // Student S101 - Sarah (Mixed status)
    ('S101', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S101', 200.00, 'Library Fee', 'Paid', 'Credit Card', '2026-01-15 10:30:00'),
    ('S101', 150.00, 'Lab Fee', 'Paid', 'Mobile Money', '2026-01-20 14:20:00'),
    ('S101', 100.00, 'Exam Fee', 'Processing', NULL, NULL),
    
    // Student S102 - Jackie (All Paid)
    ('S102', 1500.00, 'Tuition Fee', 'Paid', 'Bank Transfer', '2026-01-10 09:15:00'),
    ('S102', 200.00, 'Library Fee', 'Paid', 'Debit Card', '2026-01-12 11:45:00'),
    ('S102', 150.00, 'Lab Fee', 'Paid', 'Mobile Money', '2026-01-18 16:30:00'),
    
    // Student S103 - Winnie (Mostly Pending)
    ('S103', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S103', 200.00, 'Library Fee', 'Pending', NULL, NULL),
    ('S103', 150.00, 'Lab Fee', 'Pending', NULL, NULL),
    ('S103', 100.00, 'Exam Fee', 'Processing', NULL, NULL),
    
    // Student S104 - David (Mixed)
    ('S104', 1500.00, 'Tuition Fee', 'Paid', 'Bank Transfer', '2025-12-20 08:00:00'),
    ('S104', 200.00, 'Library Fee', 'Processing', NULL, NULL),
    ('S104', 150.00, 'Lab Fee', 'Pending', NULL, NULL),
    
    // Student S105 - Emily
    ('S105', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S105', 200.00, 'Library Fee', 'Paid', 'Mobile Money', '2026-01-25 13:10:00'),
    
    // Student S106 - Michael
    ('S106', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S106', 200.00, 'Library Fee', 'Pending', NULL, NULL),
    ('S106', 100.00, 'Exam Fee', 'Paid', 'Credit Card', '2026-02-01 10:00:00'),
    
    // Student S107 - Lisa (All Paid)
    ('S107', 1500.00, 'Tuition Fee', 'Paid', 'Bank Transfer', '2025-12-15 07:30:00'),
    ('S107', 200.00, 'Library Fee', 'Paid', 'Debit Card', '2025-12-16 09:45:00'),
    ('S107', 150.00, 'Lab Fee', 'Paid', 'Mobile Money', '2025-12-18 15:20:00'),
    ('S107', 100.00, 'Exam Fee', 'Paid', 'Credit Card', '2025-12-22 11:00:00'),
    
    // Student S108 - James
    ('S108', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S108', 200.00, 'Library Fee', 'Processing', NULL, NULL),
    
    // Student S109 - Catherine (Mixed)
    ('S109', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S109', 200.00, 'Library Fee', 'Pending', NULL, NULL),
    ('S109', 150.00, 'Lab Fee', 'Paid', 'Bank Transfer', '2026-01-28 14:30:00'),
    
    // Student S110 - Peter
    ('S110', 1500.00, 'Tuition Fee', 'Paid', 'Bank Transfer', '2025-12-10 06:00:00'),
    ('S110', 200.00, 'Library Fee', 'Paid', 'Debit Card', '2025-12-12 10:30:00'),
    ('S110', 300.00, 'Medical Kit Fee', 'Pending', NULL, NULL),
    
    // Student S111 - Rachel
    ('S111', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S111', 250.00, 'Uniform Fee', 'Pending', NULL, NULL),
    
    // Student S112 - Thomas
    ('S112', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S112', 150.00, 'Lab Fee', 'Pending', NULL, NULL),
    
    // Student S113 - Jennifer
    ('S113', 1500.00, 'Tuition Fee', 'Paid', 'Mobile Money', '2026-01-08 12:15:00'),
    ('S113', 200.00, 'Library Fee', 'Paid', 'Mobile Money', '2026-01-10 15:45:00'),
    ('S113', 150.00, 'Lab Fee', 'Pending', NULL, NULL),
    
    // Student S114 - Daniel
    ('S114', 1500.00, 'Tuition Fee', 'Pending', NULL, NULL),
    ('S114', 200.00, 'Library Fee', 'Pending', NULL, NULL),
    
    // Student S115 - Victoria
    ('S115', 1500.00, 'Tuition Fee', 'Paid', 'Credit Card', '2026-02-01 09:00:00'),
    ('S115', 200.00, 'Library Fee', 'Processing', NULL, NULL),
];

foreach ($fees_data as $fee) {
    list($sid, $amount, $type, $status, $method, $date) = $fee;
    $payment_date = $date ? "'$date'" : "NULL";
    $payment_method = $method ? "'$method'" : "NULL";
    
    $sql = "INSERT INTO fees (student_id, amount, fee_type, status, payment_method, payment_date) 
            VALUES ('$sid', $amount, '$type', '$status', $payment_method, $payment_date)";
    mysqli_query($conn, $sql);
}
echo "✓ " . count($fees_data) . " fees inserted<br>";

// ============= APPOINTMENTS DATA =============
echo "<h3>Inserting Appointments...</h3>";

$appointments_data = [
    ('S101', '2026-02-28', '10:00:00', 'Discuss payment plan options', 'Scheduled'),
    ('S102', '2026-02-27', '14:30:00', 'Fee payment verification', 'Scheduled'),
    ('S103', '2026-03-01', '09:00:00', 'Financial aid discussion', 'Scheduled'),
    ('S104', '2026-02-26', '11:15:00', 'Clarify pending charges', 'Scheduled'),
    ('S105', '2026-03-02', '15:00:00', 'Payment plan setup', 'Scheduled'),
    ('S106', '2026-02-25', '13:45:00', 'Discuss fee waiver program', 'Completed'),
    ('S107', '2026-02-20', '10:30:00', 'Fee receipt query', 'Completed'),
    ('S108', '2026-03-05', '09:30:00', 'General fee inquiry', 'Scheduled'),
    ('S109', '2026-02-28', '16:00:00', 'Payment deadline extension', 'Scheduled'),
    ('S110', '2026-02-24', '12:00:00', 'Payment confirmation', 'Completed'),
    ('S111', '2026-03-03', '14:00:00', 'Fee payment plan', 'Scheduled'),
];

foreach ($appointments_data as $appointment) {
    list($sid, $date, $time, $purpose, $status) = $appointment;
    $sql = "INSERT INTO appointments (student_id, appointment_date, appointment_time, purpose, status) 
            VALUES ('$sid', '$date', '$time', '$purpose', '$status')";
    mysqli_query($conn, $sql);
}
echo "✓ " . count($appointments_data) . " appointments inserted<br>";

// ============= LIVE QUEUE DATA =============
echo "<h3>Inserting Queue Data...</h3>";

$queue_data = [
    (101, 'S101', 'Serving', 1),
    (102, 'S103', 'Waiting', NULL),
    (103, 'S108', 'Waiting', NULL),
    (104, 'S110', 'Done', 1),
    (105, 'S105', 'Waiting', NULL),
    (106, 'S109', 'Serving', 2),
    (107, 'S112', 'Waiting', NULL),
];

foreach ($queue_data as $queue) {
    list($token, $sid, $status, $counter) = $queue;
    $counter_val = $counter ? $counter : 'NULL';
    $sql = "INSERT INTO queue (token_number, student_id, status, counter_number) 
            VALUES ($token, '$sid', '$status', $counter_val)";
    mysqli_query($conn, $sql);
}
echo "✓ " . count($queue_data) . " queue entries inserted<br>";

// ============= PAYMENT DEADLINES DATA =============
echo "<h3>Inserting Payment Deadlines...</h3>";

// Get fee IDs for linking
$fees_result = mysqli_query($conn, "SELECT id, student_id FROM fees WHERE status IN ('Pending', 'Processing')");
$deadline_counter = 0;

while ($fee = mysqli_fetch_assoc($fees_result)) {
    $fee_id = $fee['id'];
    $student_id = $fee['student_id'];
    
    // Set different due dates
    $days_ahead = rand(3, 45);
    $due_date = date('Y-m-d', strtotime("+$days_ahead days"));
    
    $sql = "INSERT INTO payment_deadlines (student_id, fee_id, due_date, reminder_sent, reminder_count) 
            VALUES ('$student_id', $fee_id, '$due_date', " . (rand(0, 1)) . ", " . rand(0, 3) . ")";
    
    if (mysqli_query($conn, $sql)) {
        $deadline_counter++;
    }
}
echo "✓ $deadline_counter payment deadlines inserted<br>";

// ============= SUMMARY =============
echo "<hr>";
echo "<h2>✅ Test Database Seeded Successfully!</h2>";

$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students"))['count'];
$fee_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM fees"))['count'];
$appointment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM appointments"))['count'];
$queue_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM queue"))['count'];
$deadline_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM payment_deadlines"))['count'];

echo "<table style='border: 1px solid #ccc; border-collapse: collapse; margin: 20px 0;'>";
echo "<tr style='background: #f0f0f0;'><th style='border: 1px solid #ccc; padding: 10px;'>Data Type</th><th style='border: 1px solid #ccc; padding: 10px;'>Count</th></tr>";
echo "<tr><td style='border: 1px solid #ccc; padding: 10px;'>Students</td><td style='border: 1px solid #ccc; padding: 10px;'>$student_count</td></tr>";
echo "<tr><td style='border: 1px solid #ccc; padding: 10px;'>Fee Records</td><td style='border: 1px solid #ccc; padding: 10px;'>$fee_count</td></tr>";
echo "<tr><td style='border: 1px solid #ccc; padding: 10px;'>Appointments</td><td style='border: 1px solid #ccc; padding: 10px;'>$appointment_count</td></tr>";
echo "<tr><td style='border: 1px solid #ccc; padding: 10px;'>Queue Entries</td><td style='border: 1px solid #ccc; padding: 10px;'>$queue_count</td></tr>";
echo "<tr><td style='border: 1px solid #ccc; padding: 10px;'>Payment Deadlines</td><td style='border: 1px solid #ccc; padding: 10px;'>$deadline_count</td></tr>";
echo "</table>";

echo "<h3>Test Login Credentials:</h3>";
echo "<p><strong>All students use password:</strong> <code>password123</code></p>";
echo "<p><strong>Sample staff login:</strong> <code>admin</code> / <code>admin123</code></p>";
echo "<p><strong>Sample students to test:</strong></p>";
echo "<ul>";
echo "<li>S101 - Sarah Johnson (Mixed fee status)</li>";
echo "<li>S102 - Jackie Wilson (All fees paid)</li>";
echo "<li>S103 - Winnie Omondi (All fees pending)</li>";
echo "<li>S107 - Lisa Anderson (All fees paid)</li>";
echo "</ul>";

echo "<p><a href='student/login.php' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Go to Student Portal</a>";
echo " <a href='admin/admin.php' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px;'>Go to Admin Panel</a></p>";

mysqli_close($conn);
?>
