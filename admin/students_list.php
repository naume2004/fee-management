<?php
include '../db.php';
include 'auth.php';

// Fetch all students and their fee summary, including tuition details
$sql = "SELECT s.*, 
    (SELECT SUM(amount) FROM fees WHERE student_id = s.student_id AND status = 'Pending') as pending_fees,
    (SELECT SUM(amount) FROM fees WHERE student_id = s.student_id AND status = 'Paid') as paid_fees,
    (SELECT SUM(amount) FROM fees WHERE student_id = s.student_id AND fee_type = 'Tuition Fee' AND status = 'Paid') as tuition_paid,
    (SELECT SUM(amount) FROM fees WHERE student_id = s.student_id AND fee_type = 'Tuition Fee' AND status = 'Pending') as tuition_pending
    FROM students s";
$result = mysqli_query($conn, $sql);

include 'header.php';
?>

<div class="admin-dashboard-header" style="margin-bottom: 3rem;">
    <h1>School Students Registry</h1>
    <p style="color: var(--text-muted);">View and manage payment data for all students enrolled in the school.</p>
</div>

<div class="table-section">
    <table class="data-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Course</th>
                <th>Tuition Paid</th>
                <th>Total Paid</th>
                <th>Total Pending</th>
                <th>Financial Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['student_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td style="color: var(--success); font-weight: 600;">$<?php echo number_format($row['tuition_paid'] ?? 0, 2); ?></td>
                    <td style="color: var(--success); font-weight: 600;">$<?php echo number_format($row['paid_fees'] ?? 0, 2); ?></td>
                    <td style="color: var(--danger); font-weight: 600;">$<?php echo number_format($row['pending_fees'] ?? 0, 2); ?></td>
                    <td>
                        <?php if (($row['pending_fees'] ?? 0) <= 0): ?>
                            <span class="badge badge-success">Fully Cleared</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Outstanding Bal</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="../student/dashboard.php?sim_id=<?php echo $row['student_id']; ?>" class="btn secondary-btn" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; color: var(--primary);">View Dashboard</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="summary-card" style="margin-top: 3rem; background: #fffbeb; border-radius: 1rem; padding: 2rem; border-left: 5px solid #f59e0b;">
    <p style="color: #92400e;">
        <strong>System Admin Note:</strong> The "View Dashboard" link currently simulates the student's personal portal view for the selected Student ID. Use this to verify student-facing payment data.
    </p>
</div>

<?php include 'footer.php'; ?>
