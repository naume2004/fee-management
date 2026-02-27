<?php
include '../db.php';
include 'auth.php';

// Data for Payment Dashboard
$stats_sql = "SELECT 
    fee_type, 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as collected,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as pending,
    COUNT(id) as total_records
    FROM fees 
    GROUP BY fee_type";
$stats_result = mysqli_query($conn, $stats_sql);

// Overall Collection Data
$overall_sql = "SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as total_pending
    FROM fees";
$overall_result = mysqli_query($conn, $overall_sql);
$overall = mysqli_fetch_assoc($overall_result);

// Latest Payments
$latest_payments_sql = "SELECT f.*, s.name as student_name 
    FROM fees f 
    JOIN students s ON f.student_id = s.student_id 
    WHERE f.status = 'Paid' 
    ORDER BY f.payment_date DESC 
    LIMIT 10";
$latest_payments_result = mysqli_query($conn, $latest_payments_sql);

include 'header.php';
?>

<div class="admin-dashboard-header" style="margin-bottom: 3rem;">
    <h1>School Payment Data Dashboard</h1>
    <p style="color: var(--text-muted);">Real-time financial analytics and payment tracking for all school students.</p>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Collections (Paid)</h3>
        <div class="value" style="color: var(--success);">$<?php echo number_format($overall['total_paid'] ?? 0, 2); ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Total revenue received</p>
    </div>
    <div class="stat-card">
        <h3>Outstanding Balance</h3>
        <div class="value" style="color: var(--danger);">$<?php echo number_format($overall['total_pending'] ?? 0, 2); ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Total fees awaiting payment</p>
    </div>
    <div class="stat-card">
        <h3>Recovery Rate</h3>
        <div class="value">
            <?php 
                $total = ($overall['total_paid'] ?? 0) + ($overall['total_pending'] ?? 0);
                echo $total > 0 ? round(($overall['total_paid'] / $total) * 100, 1) : 0;
            ?>%
        </div>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Collection efficiency</p>
    </div>
</div>

<div class="admin-panels-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 2rem;">
    <div class="left-panel">
        <div class="table-section">
            <h2>Collection by Fee Type</h2>
            <p style="margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.9rem;">Distribution of payments across different school fee categories.</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Fee Category</th>
                        <th>Collected</th>
                        <th>Pending</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($stats_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['fee_type']); ?></strong></td>
                            <td style="color: var(--success);">$<?php echo number_format($row['collected'], 2); ?></td>
                            <td style="color: var(--danger);">$<?php echo number_format($row['pending'], 2); ?></td>
                            <td>$<?php echo number_format($row['collected'] + $row['pending'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="right-panel">
        <div class="table-section">
            <h2>Recent Payment Activities</h2>
            <p style="margin-bottom: 1.5rem; color: var(--text-muted); font-size: 0.9rem;">The latest successful transactions processed by the school system.</p>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($latest_payments_result) > 0): ?>
                        <?php while($pay = mysqli_fetch_assoc($latest_payments_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pay['student_name']); ?></td>
                                <td>$<?php echo number_format($pay['amount'], 2); ?></td>
                                <td><span class="badge badge-success"><?php echo $pay['payment_method']; ?></span></td>
                                <td style="font-size: 0.85rem;"><?php echo date('M j, H:i', strtotime($pay['payment_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; padding: 2rem;">No payment activities recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="data-visualization-preview" style="margin-top: 4rem; background: white; padding: 3rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); text-align: center;">
    <h2>Monthly Collection Trend</h2>
    <div style="height: 200px; background: #f8fafc; border: 2px dashed var(--border); border-radius: 1rem; margin-top: 2rem; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
        [ Interactive Chart Visualization Placeholder: Revenue Over Time ]
    </div>
</div>

<?php include 'footer.php'; ?>
