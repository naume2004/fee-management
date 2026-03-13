<?php
/**
 * FeeFlow — Skip the Line Feature Database Schema
 * Run this to add online payment and standing order tables
 * Browse to: http://localhost/fee-management/migrate_skip_line.php
 */
include 'db.php';

$changes = [];
$errors = [];

function safe_create_table(mysqli $conn, string $sql, string $desc): void {
    global $changes, $errors;
    if (mysqli_query($conn, $sql)) {
        $changes[] = "✓ $desc";
    } else {
        $errno = mysqli_errno($conn);
        if ($errno === 1050) { // Table already exists
            $changes[] = "— $desc (already exists — skipped)";
        } else {
            $errors[] = "✗ $desc: " . mysqli_error($conn);
        }
    }
}

// Online Payments Table
$online_payments_sql = "CREATE TABLE IF NOT EXISTS online_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    amount_ugx DECIMAL(12,2) NOT NULL,
    amount_usd DECIMAL(10,2) NOT NULL,
    payment_type VARCHAR(20) NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    transaction_ref VARCHAR(100) DEFAULT NULL,
    sender_name VARCHAR(100) DEFAULT NULL,
    proof_file VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Pending',
    verified_by INT DEFAULT NULL,
    verified_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student (student_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
)";
safe_create_table($conn, $online_payments_sql, "online_payments table");

// Standing Orders / Auto-Pay Table
$standing_orders_sql = "CREATE TABLE IF NOT EXISTS standing_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    schedule ENUM('weekly', 'monthly', 'termly') DEFAULT 'monthly',
    amount_ugx DECIMAL(12,2) NOT NULL,
    start_date DATE NOT NULL,
    next_deduction_date DATE NOT NULL,
    status ENUM('Active', 'Paused', 'Cancelled') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_student (student_id),
    INDEX idx_status (status),
    INDEX idx_next_deduction (next_deduction_date)
)";
safe_create_table($conn, $standing_orders_sql, "standing_orders (Auto-Pay) table");

// Payment Notifications Table
$payment_notifications_sql = "CREATE TABLE IF NOT EXISTS payment_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    payment_id INT DEFAULT NULL,
    notification_type ENUM('sms', 'email', 'whatsapp') NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Sent', 'Failed', 'Pending') DEFAULT 'Sent',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student (student_id),
    INDEX idx_status (status)
)";
safe_create_table($conn, $payment_notifications_sql, "payment_notifications table");

// Update appointments table to add notes column if not exists
$alter_appointments = "ALTER TABLE appointments ADD COLUMN IF NOT EXISTS notes TEXT DEFAULT NULL";
try {
    mysqli_query($conn, $alter_appointments);
    $changes[] = "✓ appointments.notes column added";
} catch (Exception $e) {
    $changes[] = "— appointments.notes (already exists or error)";
}

// Add sample online payment for testing (if none exists)
$check_payments = "SELECT COUNT(*) as cnt FROM online_payments";
$check_result = mysqli_query($conn, $check_payments);
$count = mysqli_fetch_assoc($check_result);

if ($count['cnt'] == 0) {
    $sample_payments = [
        "INSERT INTO online_payments (student_id, amount_ugx, amount_usd, payment_type, payment_method, phone, status, created_at) 
         VALUES ('S101', 3040000, 800, 'mobile', 'Mobile Money (MTN)', '+256772123456', 'Completed', DATE_SUB(NOW(), INTERVAL 20 DAY))",
        "INSERT INTO online_payments (student_id, amount_ugx, amount_usd, payment_type, payment_method, phone, status, created_at) 
         VALUES ('S101', 570000, 150, 'card', 'Credit/Debit Card', '+256772123456', 'Completed', DATE_SUB(NOW(), INTERVAL 20 DAY))",
        "INSERT INTO online_payments (student_id, amount_ugx, amount_usd, payment_type, payment_method, phone, status, created_at) 
         VALUES ('S102', 1140000, 300, 'ussd', 'USSD Payment (MTN)', '+256772234567', 'Completed', DATE_SUB(NOW(), INTERVAL 10 DAY))",
        "INSERT INTO online_payments (student_id, amount_ugx, amount_usd, payment_type, payment_method, transaction_ref, status, created_at) 
         VALUES ('S103', 2090000, 550, 'bank', 'Bank Transfer', 'FTN123456789', 'Completed', DATE_SUB(NOW(), INTERVAL 25 DAY))",
        "INSERT INTO online_payments (student_id, amount_ugx, amount_usd, payment_type, payment_method, phone, status, created_at) 
         VALUES ('S105', 2204000, 580, 'mobile', 'Mobile Money (Airtel)', '+256702345678', 'Pending', NOW())",
    ];
    
    foreach ($sample_payments as $sql) {
        mysqli_query($conn, $sql);
    }
    $changes[] = "✓ Sample online payments added";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skip the Line — Database Migration</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background: #f8fafc; }
        .migration-card {
            max-width: 700px; margin: 4rem auto; padding: 2.5rem;
            background: white; border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        h1 { color: var(--primary); margin-bottom: 0.5rem; }
        .log-item { padding: 0.5rem 0; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .error-log { background: #fef2f2; border-left: 4px solid var(--danger); padding: 1rem; border-radius: 0.5rem; margin-top: 1.5rem; }
        .success-box { background: #f0fdf4; border-left: 4px solid var(--success); padding: 1.5rem; border-radius: 0.5rem; margin-top: 1.5rem; }
        .feature-list { background: #eff6ff; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem; }
        .feature-list h3 { color: #1e40af; margin-top: 0; }
        .feature-list ul { color: #1e3a8a; padding-left: 1.25rem; margin-bottom: 0; }
        .feature-list li { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
<div class="migration-card">
    <h1>⏭️ Skip the Line — Database Migration</h1>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">
        Adding new tables to support online payments, auto-pay, and digital receipts.
    </p>

    <h3 style="margin-bottom: 1rem; font-size: 0.95rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">Migration Log</h3>
    <?php foreach ($changes as $line): ?>
        <div class="log-item"><?php echo $line; ?></div>
    <?php endforeach; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-log">
            <strong style="color: var(--danger);">Errors encountered:</strong>
            <?php foreach ($errors as $e): ?>
                <p style="margin: 0.5rem 0 0; font-size: 0.9rem;"><?php echo $e; ?></p>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="success-box">
            <strong style="color: var(--success);">✅ Migration completed successfully!</strong>
        </div>
    <?php endif; ?>

    <div class="feature-list">
        <h3>✨ New Features Added</h3>
        <ul>
            <li><strong>Skip the Line Portal</strong> — Student landing page for online payments</li>
            <li><strong>Enhanced Payment Methods</strong> — Mobile Money, Cards, USSD, QR Code, Bank Transfer, Auto-Pay</li>
            <li><strong>Digital Receipts</strong> — Instant payment confirmation with downloadable receipts</li>
            <li><strong>Online Appointments</strong> — Book time slots for in-person payments</li>
            <li><strong>Auto-Pay (Standing Orders)</strong> — Set up automatic monthly deductions</li>
            <li><strong>Admin Dashboard</strong> — Monitor and verify online payments</li>
        </ul>
    </div>

    <div style="margin-top: 2rem; text-align: center;">
        <a href="online_payments.php" class="btn primary-btn" style="margin-right: 1rem;">💳 View Online Payments</a>
        <a href="index.php" class="btn" style="background: #f1f5f9; color: var(--text-main);">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
