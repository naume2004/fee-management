<?php
// make sure session is available to show staff info (auth include normally started it)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FeeFlow</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="../index.html" class="logo">FeeFlow Admin</a>
            <?php if (!empty(
                
                
                
                
                
                
                
                
                $_SESSION['staff_name'])): ?>
                <span style="margin-left:auto; color:#fff; font-weight:600;">Welcome, <?php echo htmlspecialchars($_SESSION['staff_name']); ?></span>
            <?php endif; ?>
            <ul class="nav-links">
                <li><a href="../index.html">Home Site</a></li>
                <li><a href="admin.php">Admin Panel</a></li>
                <li><a href="payments_dashboard.php">Payments Data</a></li>
                <li><a href="payment_analytics.php">Analytics</a></li>
                <li><a href="students_list.php">Manage Students</a></li>
                <li><a href="view_tuition.php">Tuition Payments</a></li>
                <li><a href="send_reminders.php" style="color: #f59e0b; font-weight: 600;">📧 Send Reminders</a></li>
                <li><a href="../student/dashboard.php">Student View</a></li>
                <li><a href="logout.php" style="color: #ef4444; font-weight: 600;">🚪 Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="container main-content">
