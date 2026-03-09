<?php
/**
 * FeeFlow — Database Schema Migration
 * Run this once to add new columns: gender, class_name, student_status,
 * academic_year, duration to students; email to staff.
 * Browse to: http://localhost/fee-management/migrate_schema.php
 */
include 'db.php';

$changes = [];
$errors  = [];

function safe_alter(mysqli $conn, string $sql, string $desc): void {
    global $changes, $errors;
    if (mysqli_query($conn, $sql)) {
        $changes[] = "✓  $desc";
    } else {
        $errno = mysqli_errno($conn);
        if ($errno === 1060) { // Duplicate column
            $changes[] = "—  $desc <em>(column already exists — skipped)</em>";
        } else {
            $errors[] = "✗  $desc: " . htmlspecialchars(mysqli_error($conn));
        }
    }
}

// ── Students table ───────────────────────────────────────────────────────────
safe_alter($conn,
    "ALTER TABLE students ADD COLUMN gender VARCHAR(10) NOT NULL DEFAULT 'Male'",
    "students.gender");

safe_alter($conn,
    "ALTER TABLE students ADD COLUMN class_name VARCHAR(50) NOT NULL DEFAULT 'Primary 1'",
    "students.class_name");

safe_alter($conn,
    "ALTER TABLE students ADD COLUMN student_status VARCHAR(20) NOT NULL DEFAULT 'Active'",
    "students.student_status");

safe_alter($conn,
    "ALTER TABLE students ADD COLUMN academic_year VARCHAR(20) NOT NULL DEFAULT '2025/2026'",
    "students.academic_year");

safe_alter($conn,
    "ALTER TABLE students ADD COLUMN duration VARCHAR(50) NOT NULL DEFAULT '3 Terms'",
    "students.duration");

// ── Staff table ──────────────────────────────────────────────────────────────
$staff_create_sql = "CREATE TABLE IF NOT EXISTS staff (
    id        INT          AUTO_INCREMENT PRIMARY KEY,
    username  VARCHAR(50)  UNIQUE NOT NULL,
    email     VARCHAR(100) UNIQUE DEFAULT NULL,
    password  VARCHAR(255) NOT NULL,
    name      VARCHAR(100) NOT NULL,
    role      VARCHAR(50)  NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $staff_create_sql)) {
    $changes[] = "✓  staff table ready";
} else {
    $errors[] = "✗  staff table: " . htmlspecialchars(mysqli_error($conn));
}

safe_alter($conn,
    "ALTER TABLE staff ADD COLUMN email VARCHAR(100) UNIQUE DEFAULT NULL",
    "staff.email");

// ── Default admin account ────────────────────────────────────────────────────
$check = mysqli_query($conn, "SELECT id FROM staff LIMIT 1");
if ($check && mysqli_num_rows($check) === 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn,
        "INSERT INTO staff (username, email, password, name, role) VALUES (?, ?, ?, ?, ?)");
    $email = 'admin@school.edu';
    $name  = 'School Administrator';
    $role  = 'admin';
    mysqli_stmt_bind_param($stmt, 'sssss', $u='admin', $email, $hash, $name, $role);
    if (mysqli_stmt_execute($stmt)) {
        $changes[] = "✓  Default admin created — <strong>admin / admin@school.edu / admin123</strong>";
    } else {
        $errors[] = "✗  Default admin: " . htmlspecialchars(mysqli_error($conn));
    }
    mysqli_stmt_close($stmt);
} else {
    $changes[] = "—  Admin account already exists — skipped";
}

// Update existing admin's email if it's null
mysqli_query($conn, "UPDATE staff SET email = 'admin@school.edu' WHERE email IS NULL AND username = 'admin'");
if (mysqli_affected_rows($conn) > 0) {
    $changes[] = "✓  Updated admin email to admin@school.edu";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schema Migration — FeeFlow</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        .next-steps { background: #eff6ff; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem; }
        .next-steps ol { padding-left: 1.2rem; color: #1e40af; font-size: 0.9rem; }
        .next-steps li { margin-bottom: 0.5rem; }
        code { background: #f1f5f9; padding: 0.15rem 0.4rem; border-radius: 0.25rem; font-size: 0.85rem; color: var(--primary); }
    </style>
</head>
<body>
<div class="migration-card">
    <h1>🔧 FeeFlow Schema Migration</h1>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">
        Adding new columns to support classes, gender, status, academic year and staff email login.
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

    <div class="next-steps">
        <strong style="color: #1e40af;">Next Steps:</strong>
        <ol>
            <li>Run <code>school_students.sql</code> in phpMyAdmin to seed the new student data.</li>
            <li>Log in to the Admin panel at <a href="admin/login.php" style="color: var(--primary);">admin/login.php</a></li>
            <li>Use <strong>admin@school.edu</strong> (email) or <strong>admin</strong> (username) with password <strong>admin123</strong></li>
            <li>Visit <a href="admin/students_list.php" style="color: var(--primary);">Manage Students</a> to see the new table.</li>
        </ol>
    </div>

    <div style="margin-top: 2rem; text-align: center;">
        <a href="admin/login.php" class="btn primary-btn" style="margin-right: 1rem;">Go to Admin Login</a>
        <a href="index.html" class="btn" style="background: #f1f5f9; color: var(--text-main);">Home</a>
    </div>
</div>
</body>
</html>
