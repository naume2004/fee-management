<?php
$host = "localhost";
$user = "root";
$pass = "";

$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS student_fees_db";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

mysqli_select_db($conn, "student_fees_db");

// Table for Students
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    course VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

// Table for Fees
$sql = "CREATE TABLE IF NOT EXISTS fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    fee_type VARCHAR(50) NOT NULL,
    status ENUM('Pending', 'Paid', 'Processing') DEFAULT 'Pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_date TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
)";
mysqli_query($conn, $sql);

// Table for Appointments
$sql = "CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    purpose VARCHAR(255) NOT NULL,
    status ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
)";
mysqli_query($conn, $sql);

// Table for Live Queue
$sql = "CREATE TABLE IF NOT EXISTS queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_number INT NOT NULL,
    student_id VARCHAR(20) DEFAULT NULL,
    status ENUM('Waiting', 'Serving', 'Done') DEFAULT 'Waiting',
    counter_number INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

// Table for Payment Plans & Deadlines
$sql = "CREATE TABLE IF NOT EXISTS payment_deadlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    fee_id INT NOT NULL,
    due_date DATE NOT NULL,
    reminder_sent BOOLEAN DEFAULT FALSE,
    reminder_count INT DEFAULT 0,
    last_reminder_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (fee_id) REFERENCES fees(id)
)";
mysqli_query($conn, $sql);

// Insert Sample Data if empty
$check_students = mysqli_query($conn, "SELECT id FROM students LIMIT 1");
if (mysqli_num_rows($check_students) == 0) {
    $hashed_pass = password_hash('password123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO students (student_id, name, email, password, course) VALUES 
        ('S101', 'Sarah', 'sarah@example.com', '$hashed_pass', 'Computer Science'),
        ('S102', 'Jackie', 'jackie@example.com', '$hashed_pass', 'Business Administration'),
        ('S103', 'Winnie', 'winnie@example.com', '$hashed_pass', 'Engineering')");
    
    mysqli_query($conn, "INSERT INTO fees (student_id, amount, fee_type, status) VALUES 
        ('S101', 1200.00, 'Tuition Fee', 'Pending'),
        ('S101', 150.00, 'Library Fee', 'Paid'),
        ('S102', 1100.00, 'Tuition Fee', 'Pending'),
        ('S103', 1300.00, 'Tuition Fee', 'Pending'),
        ('S103', 200.00, 'Lab Fee', 'Pending')");
        
    mysqli_query($conn, "INSERT INTO queue (token_number, student_id, status) VALUES 
        (101, 'S101', 'Serving'),
        (102, 'S102', 'Waiting'),
        (103, 'S103', 'Waiting')");
        
    echo "Sample data inserted successfully<br>";
}

echo "All tables created successfully!";
mysqli_close($conn);
?>
