<?php
include '../db.php';
include 'auth.php';

$status_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reminders'])) {
    $days_overdue = intval($_POST['days_overdue'] ?? 0);
    
    $update_sql = "UPDATE payment_deadlines pd
        SET reminder_sent = TRUE, reminder_count = reminder_count + 1, last_reminder_date = NOW()
        WHERE pd.due_date < DATE_SUB(CURDATE(), INTERVAL $days_overdue DAY)
        AND (SELECT status FROM fees f WHERE f.id = pd.fee_id) = 'Pending'";
    
    if (mysqli_query($conn, $update_sql)) {
        $affected_rows = mysqli_affected_rows($conn);
        $status_message = "✓ Payment reminders sent to $affected_rows overdue student(s)";
    } else {
        $status_message = "✗ Error sending reminders: " . mysqli_error($conn);
    }
}

$overdue_sql = "SELECT s.student_id, s.name, s.email, COUNT(f.id) as pending_fees, SUM(f.amount) as total_due, MAX(pd.due_date) as oldest_due
    FROM students s
    LEFT JOIN fees f ON s.student_id = f.student_id AND f.status = 'Pending'
    LEFT JOIN payment_deadlines pd ON f.id = pd.fee_id
    WHERE pd.due_date < CURDATE()
    GROUP BY s.student_id, s.name, s.email
    ORDER BY pd.due_date ASC";
$overdue_result = mysqli_query($conn, $overdue_sql);

$summary_sql = "SELECT 
    COUNT(DISTINCT pd.student_id) as students_overdue,
    COUNT(f.id) as total_overdue_fees,
    SUM(f.amount) as total_overdue_amount
    FROM payment_deadlines pd
    LEFT JOIN fees f ON f.id = pd.fee_id
    WHERE pd.due_date < CURDATE() AND f.status = 'Pending'";
$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);

include 'header.php';
?>

<div class="admin-dashboard-header" style="margin-bottom: 3rem;">
    <h1>Payment Reminders Management</h1>
    <p style="color: var(--text-muted);">Send reminders to students with overdue payments and track reminder history.</p>
</div>

<?php if ($status_message): ?>
    <div style="background: <?php echo strpos($status_message, '✓') === 0 ? '#dcfce7' : '#fee2e2'; ?>; border-left: 5px solid <?php echo strpos($status_message, '✓') === 0 ? 'var(--success)' : 'var(--danger)'; ?>; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        <p style="margin: 0; color: <?php echo strpos($status_message, '✓') === 0 ? '#166534' : '#7f1d1d'; ?>"><?php echo htmlspecialchars($status_message); ?></p>
    </div>
<?php endif; ?>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Students Overdue</h3>
        <div class="value" style="color: var(--danger);"><?php echo $summary['students_overdue'] ?? 0; ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">With unpaid fees</p>
    </div>
    <div class="stat-card">
        <h3>Overdue Fees Count</h3>
        <div class="value" style="color: var(--warning);"><?php echo $summary['total_overdue_fees'] ?? 0; ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Total pending items</p>
    </div>
    <div class="stat-card">
        <h3>Total Overdue Amount</h3>
        <div class="value" style="color: var(--danger);">$<?php echo number_format($summary['total_overdue_amount'] ?? 0, 2); ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Awaiting payment</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 3rem;">
    <!-- Send Reminders Form -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); height: fit-content;">
        <h2 style="margin-top: 0;">Send Batch Reminders</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Send reminder notifications to students with overdue payments.</p>
        
        <form method="POST">
            <div class="form-group">
                <label>Select Overdue Period:</label>
                <select name="days_overdue" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    <option value="0">All overdue (0+ days)</option>
                    <option value="7">7+ days overdue</option>
                    <option value="14">14+ days overdue</option>
                    <option value="30">30+ days overdue (seriously overdue)</option>
                </select>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">Only students meeting this criteria will be reminded.</p>
            </div>
            
            <button type="submit" name="send_reminders" class="btn primary-btn" style="width: 100%;">📧 Send Reminders Now</button>
        </form>
        
        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
            <h3 style="font-size: 1rem; margin-bottom: 1rem;">Reminder Methods:</h3>
            <ul style="list-style: none; color: var(--text-muted); font-size: 0.9rem;">
                <li style="margin-bottom: 0.5rem;">📧 Email notifications</li>
                <li style="margin-bottom: 0.5rem;">📱 SMS alerts (optional)</li>
                <li style="margin-bottom: 0.5rem;">🔔 Dashboard notifications</li>
                <li>📋 Payment history records</li>
            </ul>
        </div>
    </div>

    <!-- Overdue Students List -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Overdue Students List</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Students with payments overdue.</p>
        
        <?php if (mysqli_num_rows($overdue_result) > 0): ?>
            <div style="max-height: 500px; overflow-y: auto;">
                <?php while ($student = mysqli_fetch_assoc($overdue_result)): ?>
                    <div style="border: 1px solid var(--border); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($student['name']); ?></p>
                                <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars($student['student_id']); ?></p>
                                <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">📧 <?php echo htmlspecialchars($student['email']); ?></p>
                            </div>
                            <div style="text-align: right;">
                                <p style="margin: 0; color: var(--danger); font-weight: 600;">$<?php echo number_format($student['total_due'], 2); ?></p>
                                <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;"><?php echo $student['pending_fees']; ?> fee(s)</p>
                            </div>
                        </div>
                        
                        <a href="../student/dashboard.php?sim_id=<?php echo htmlspecialchars($student['student_id']); ?>" class="btn secondary-btn" style="width: 100%; margin-top: 0.75rem; padding: 0.5rem; font-size: 0.85rem; text-align: center; display: block; color: var(--primary); border: 1px solid var(--primary);">View Student</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="background: #f0fdf4; padding: 2rem; border-radius: 0.5rem; text-align: center;">
                <p style="margin: 0; color: var(--success);">✓ No overdue students!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
