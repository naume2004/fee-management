<?php
include '../db.php';
include 'auth.php';

// students with tuition paid
$sql = "SELECT s.student_id, s.name, s.course,
    SUM(f.amount) as tuition_paid,
    MAX(f.payment_date) as last_payment
    FROM fees f
    JOIN students s ON f.student_id = s.student_id
    WHERE f.fee_type = 'Tuition Fee' AND f.status = 'Paid'
    GROUP BY s.student_id, s.name, s.course
    ORDER BY tuition_paid DESC";
$result = mysqli_query($conn, $sql);

include 'header.php';
?>

<div class="admin-dashboard-header" style="margin-bottom: 3rem;">
    <h1>Tuition Payments</h1>
    <p style="color: var(--text-muted);">List of students who have completed tuition payments.</p>
</div>

<div class="table-section">
    <table class="data-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Tuition Amount</th>
                <th>Last Payment</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['student_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['course']); ?></td>
                        <td style="color: var(--success); font-weight:600;">$<?php echo number_format($row['tuition_paid'], 2); ?></td>
                        <td style="font-size:0.85rem; color: var(--text-muted);"><?php echo date('M j, Y', strtotime($row['last_payment'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center; padding:2rem;">No tuition payments recorded yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
