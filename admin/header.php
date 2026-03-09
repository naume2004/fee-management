<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$current_page = basename($_SERVER['PHP_SELF']);
$page_title   = isset($page_title) ? $page_title . ' — FeeFlow Admin' : 'Admin Dashboard — FeeFlow';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="../index.html" class="logo">🏫 FeeFlow</a>

            <?php if (!empty($_SESSION['staff_name'])): ?>
                <span class="nav-staff-badge">
                    👤 <?php echo htmlspecialchars($_SESSION['staff_name']); ?>
                </span>
            <?php endif; ?>

            <ul class="nav-links">
                <li><a href="admin.php"              class="<?php echo $current_page==='admin.php'              ? 'nav-active':'' ?>">Dashboard</a></li>
                <li><a href="index.php"              class="<?php echo $current_page==='index.php'              ? 'nav-active':'' ?>">🔥 Students DB</a></li>
                <li><a href="students_list.php"      class="<?php echo $current_page==='students_list.php'      ? 'nav-active':'' ?>">Manage Students</a></li>
                <li><a href="payments_dashboard.php" class="<?php echo $current_page==='payments_dashboard.php' ? 'nav-active':'' ?>">Payments</a></li>
                <li><a href="payment_analytics.php"  class="<?php echo $current_page==='payment_analytics.php'  ? 'nav-active':'' ?>">Analytics</a></li>
                <li><a href="view_tuition.php"       class="<?php echo $current_page==='view_tuition.php'       ? 'nav-active':'' ?>">Tuition</a></li>
                <li><a href="send_reminders.php"     class="<?php echo $current_page==='send_reminders.php'     ? 'nav-active':'' ?>" style="color:#f59e0b; font-weight:600;">📧 Reminders</a></li>
                <li><a href="../student/dashboard.php" style="color:#64748b;">Student View</a></li>
                <li><a href="logout.php" style="color:#ef4444; font-weight:600;">🚪 Logout</a></li>
            </ul>
        </div>
    </nav>

    <style>
        .nav-staff-badge {
            font-size: 0.82rem; font-weight: 600; color: var(--text-muted);
            background: #f1f5f9; border: 1px solid var(--border);
            padding: 0.3rem 0.75rem; border-radius: 1rem;
            margin-right: auto; margin-left: 1rem; white-space: nowrap;
        }
        .nav-active {
            color: var(--primary) !important;
            font-weight: 700 !important;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 2px;
        }
    </style>

    <main class="container main-content">
