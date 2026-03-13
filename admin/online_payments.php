<?php
include 'auth.php';
include 'header.php';

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$where_clause = "1=1";
if ($filter === 'today') {
    $where_clause = "DATE(created_at) = CURDATE()";
} elseif ($filter === 'pending') {
    $where_clause = "status = 'Pending'";
} elseif ($filter === 'completed') {
    $where_clause = "status = 'Completed'";
}

$payments_sql = "SELECT op.*, s.name, s.class_name 
    FROM online_payments op 
    LEFT JOIN students s ON op.student_id = s.student_id 
    WHERE $where_clause 
    ORDER BY op.created_at DESC 
    LIMIT 50";
$payments_result = mysqli_query($conn, $payments_sql);

$stats_sql = "SELECT 
    COUNT(*) as total_count,
    SUM(CASE WHEN status = 'Completed' THEN amount_ugx ELSE 0 END) as total_completed,
    SUM(CASE WHEN status = 'Pending' THEN amount_ugx ELSE 0 END) as total_pending,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_count
    FROM online_payments";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>

<style>
    .stat-box {
        background: white;
        padding: 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 50px;
        background: white;
        text-decoration: none;
        color: var(--text-main);
        font-size: 0.9rem;
        transition: all 0.3s;
    }
    .filter-tab:hover, .filter-tab.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
    .payment-method-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .payment-method-badge.mobile { background: #ecfdf5; color: #065f46; }
    .payment-method-badge.card { background: #eff6ff; color: #1e40af; }
    .payment-method-badge.bank { background: #fef3c7; color: #92400e; }
    .payment-method-badge.ussd { background: #f3e8ff; color: #6b21a8; }
    .payment-method-badge.autopay { background: #f0fdf4; color: #047857; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1>💳 Online Payments</h1>
        <p style="color: var(--text-muted); margin: 0;">Monitor and manage Skip-the-Line digital payments</p>
    </div>
    <a href="send_reminders.php" class="btn secondary-btn">📧 Send Reminders</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-box">
        <div style="font-size: 0.85rem; color: var(--text-muted);">Today's Payments</div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?php echo $stats['today_count']; ?></div>
    </div>
    <div class="stat-box">
        <div style="font-size: 0.85rem; color: var(--text-muted);">Total Transactions</div>
        <div style="font-size: 2rem; font-weight: 700;"><?php echo number_format($stats['total_count']); ?></div>
    </div>
    <div class="stat-box">
        <div style="font-size: 0.85rem; color: var(--text-muted);">Completed (UGX)</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;"><?php echo number_format(($stats['total_completed'] ?? 0), 0); ?></div>
    </div>
    <div class="stat-box">
        <div style="font-size: 0.85rem; color: var(--text-muted);">Pending (UGX)</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;"><?php echo number_format(($stats['total_pending'] ?? 0), 0); ?></div>
    </div>
</div>

<div class="filter-tabs">
    <a href="?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">All Payments</a>
    <a href="?filter=today" class="filter-tab <?php echo $filter === 'today' ? 'active' : ''; ?>">Today</a>
    <a href="?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending Verification</a>
    <a href="?filter=completed" class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">Completed</a>
</div>

<div class="table-section">
    <table class="data-table">
        <thead>
            <tr>
                <th>Receipt #</th>
                <th>Student</th>
                <th>Class</th>
                <th>Amount (UGX)</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($payments_result) > 0): ?>
                <?php while($payment = mysqli_fetch_assoc($payments_result)): 
                    $method_class = '';
                    if (strpos($payment['payment_type'], 'mobile') !== false) $method_class = 'mobile';
                    elseif (strpos($payment['payment_type'], 'card') !== false) $method_class = 'card';
                    elseif (strpos($payment['payment_type'], 'bank') !== false) $method_class = 'bank';
                    elseif (strpos($payment['payment_type'], 'ussd') !== false) $method_class = 'ussd';
                    elseif (strpos($payment['payment_type'], 'autopay') !== false) $method_class = 'autopay';
                ?>
                    <tr>
                        <td><strong><?php echo 'RCP-' . str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($payment['name'] ?? 'Unknown'); ?>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($payment['student_id']); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($payment['class_name'] ?? '-'); ?></td>
                        <td><strong><?php echo number_format($payment['amount_ugx'], 0); ?></strong></td>
                        <td>
                            <span class="payment-method-badge <?php echo $method_class; ?>">
                                <?php echo htmlspecialchars(ucfirst($payment['payment_type'])); ?>
                            </span>
                            <?php if ($payment['phone']): ?>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($payment['phone']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $payment['status'] === 'Completed' ? 'success' : 'warning'; ?>">
                                <?php echo $payment['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($payment['created_at'])); ?>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('h:i A', strtotime($payment['created_at'])); ?></div>
                        </td>
                        <td>
                            <a href="view_payment.php?id=<?php echo $payment['id']; ?>" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">View</a>
                            <?php if ($payment['status'] === 'Pending'): ?>
                                <a href="verify_payment.php?id=<?php echo $payment['id']; ?>&action=verify" class="btn primary-btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Verify this payment?');">Verify</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        No payments found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
