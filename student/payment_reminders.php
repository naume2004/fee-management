<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

$overdue_sql = "SELECT f.*, pd.due_date, pd.reminder_count, pd.last_reminder_date
    FROM fees f
    LEFT JOIN payment_deadlines pd ON f.id = pd.fee_id
    WHERE f.student_id = '$student_id' AND f.status = 'Pending'
    AND (pd.due_date IS NULL OR pd.due_date < CURDATE())
    ORDER BY pd.due_date ASC";
$overdue_result = mysqli_query($conn, $overdue_sql);

$upcoming_sql = "SELECT f.*, pd.due_date
    FROM fees f
    LEFT JOIN payment_deadlines pd ON f.id = pd.fee_id
    WHERE f.student_id = '$student_id' AND f.status = 'Pending'
    AND (pd.due_date IS NULL OR pd.due_date >= CURDATE())
    ORDER BY pd.due_date ASC";
$upcoming_result = mysqli_query($conn, $upcoming_sql);

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Payment Reminders</h1>
    <p style="color: var(--text-muted);">Track overdue payments and upcoming deadlines.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
    <!-- Overdue Payments -->
    <div>
        <h2 style="margin-bottom: 1.5rem; color: var(--danger);">⚠️ Overdue Payments</h2>
        
        <?php if (mysqli_num_rows($overdue_result) > 0): ?>
            <?php while ($fee = mysqli_fetch_assoc($overdue_result)): ?>
                <div style="background: #fee2e2; border-left: 5px solid var(--danger); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="margin: 0; color: var(--danger);"><?php echo htmlspecialchars($fee['fee_type']); ?></h3>
                            <p style="margin: 0.5rem 0 0; color: #7f1d1d; font-size: 0.9rem;">Due: <?php echo $fee['due_date'] ? date('M j, Y', strtotime($fee['due_date'])) : 'Not set'; ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0; font-size: 1.5rem; font-weight: 900; color: var(--danger);">$<?php echo number_format($fee['amount'], 2); ?></p>
                            <p style="margin: 0.5rem 0 0; color: #7f1d1d; font-size: 0.85rem;">
                                <?php 
                                $days_overdue = (int)((strtotime(date('Y-m-d')) - strtotime($fee['due_date'])) / (60 * 60 * 24));
                                echo $days_overdue . ' day' . ($days_overdue != 1 ? 's' : '') . ' overdue';
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($fee['reminder_count'] > 0): ?>
                        <p style="margin: 0.5rem 0; color: #7f1d1d; font-size: 0.85rem;">📧 <?php echo $fee['reminder_count']; ?> reminder(s) sent - Last: <?php echo date('M j, Y', strtotime($fee['last_reminder_date'])); ?></p>
                    <?php endif; ?>
                    
                    <a href="payment_gateway.php?sim_id=<?php echo $student_id; ?>" class="btn primary-btn" style="width: 100%; margin-top: 1rem; text-align: center; display: block;">Pay Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="background: white; padding: 2rem; border-radius: 0.5rem; text-align: center; color: var(--text-muted);">
                <p style="margin: 0;">✓ No overdue payments!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Upcoming Deadlines -->
    <div>
        <h2 style="margin-bottom: 1.5rem; color: var(--warning);">📅 Upcoming Deadlines</h2>
        
        <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
            <?php while ($fee = mysqli_fetch_assoc($upcoming_result)): ?>
                <div style="background: #fffbeb; border-left: 5px solid var(--warning); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="margin: 0; color: #b45309;"><?php echo htmlspecialchars($fee['fee_type']); ?></h3>
                            <p style="margin: 0.5rem 0 0; color: #92400e; font-size: 0.9rem;">Due: <?php echo $fee['due_date'] ? date('M j, Y', strtotime($fee['due_date'])) : 'Not set'; ?></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="margin: 0; font-size: 1.5rem; font-weight: 900; color: var(--warning);">$<?php echo number_format($fee['amount'], 2); ?></p>
                            <p style="margin: 0.5rem 0 0; color: #92400e; font-size: 0.85rem;">
                                <?php 
                                $days_left = (int)((strtotime($fee['due_date']) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                                echo $days_left . ' day' . ($days_left != 1 ? 's' : '') . ' remaining';
                                ?>
                            </p>
                        </div>
                    </div>
                    
                    <a href="payment_gateway.php?sim_id=<?php echo $student_id; ?>" class="btn secondary-btn" style="width: 100%; margin-top: 1rem; text-align: center; display: block; color: var(--primary); border: 1px solid var(--primary);">Pay Before Deadline</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="background: white; padding: 2rem; border-radius: 0.5rem; text-align: center; color: var(--text-muted);">
                <p style="margin: 0;">✓ No upcoming deadlines!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
