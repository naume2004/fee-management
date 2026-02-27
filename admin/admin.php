<?php
include '../db.php';
include 'auth.php';

// Fetch statistics
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM students) as total_students,
    (SELECT SUM(amount) FROM fees WHERE status = 'Paid') as total_collected,
    (SELECT SUM(amount) FROM fees WHERE status = 'Pending') as total_pending,
    (SELECT COUNT(*) FROM fees WHERE status = 'Pending') as pending_count";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

include 'header.php';
?>

<div class="admin-dashboard-header" style="margin-bottom: 3rem;">
    <h1>Finance Office Dashboard</h1>
    <p style="color: var(--text-muted);">Quick overview of student fee payments and statistics.</p>
</div>

<!-- Key Metrics -->
<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Students</h3>
        <div class="value"><?php echo $stats['total_students']; ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Registered students</p>
    </div>
    
    <div class="stat-card">
        <h3>Collected Fees</h3>
        <div class="value" style="color: var(--success);">$<?php echo number_format($stats['total_collected'] ?? 0, 2); ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Total paid payments</p>
    </div>
    
    <div class="stat-card">
        <h3>Pending Payments</h3>
        <div class="value" style="color: var(--danger);"><?php echo $stats['pending_count']; ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Awaiting payment</p>
    </div>
    
    <div class="stat-card">
        <h3>Outstanding Amount</h3>
        <div class="value" style="color: var(--warning);">$<?php echo number_format($stats['total_pending'] ?? 0, 2); ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Total pending fees</p>
    </div>
</div>

<!-- Quick Links Section -->
<div style="margin-top: 3rem; background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 2rem;">Quick Actions</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="payments_dashboard.php" class="btn primary-btn" style="padding: 1.5rem; text-align: center; text-decoration: none;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">💳</div>
            <div>View Payments</div>
        </a>
        <a href="payment_analytics.php" class="btn primary-btn" style="padding: 1.5rem; text-align: center; text-decoration: none;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📊</div>
            <div>Analytics</div>
        </a>
        <a href="students_list.php" class="btn primary-btn" style="padding: 1.5rem; text-align: center; text-decoration: none;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">👥</div>
            <div>Manage Students</div>
        </a>
        <a href="send_reminders.php" class="btn primary-btn" style="padding: 1.5rem; text-align: center; text-decoration: none;">
            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">📧</div>
            <div>Send Reminders</div>
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
