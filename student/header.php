<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' — FeeFlow' : 'Student Dashboard — FeeFlow'; ?></title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="../index.html" class="logo">FeeFlow</a>
            <ul class="nav-links">
                <li><a href="../index.html">Home</a></li>
                <li><a href="dashboard.php">My Dashboard</a></li>
                <li><a href="payment_gateway.php">Pay Online</a></li>
                <li><a href="payment_plans.php">Installments</a></li>
                <li><a href="payment_history.php">History</a></li>
                <li><a href="payment_reminders.php" style="color: #ef4444; font-weight: 600;">🔔 Alerts</a></li>
                <li><a href="logout.php" style="color: #ef4444; font-weight: 600;">🚪 Logout</a></li>
            </ul>
        </div>
    </nav>
    <main class="container main-content">
