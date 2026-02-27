<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Fetch all transactions
$transactions_sql = "SELECT * FROM fees WHERE student_id = '$student_id' ORDER BY created_at DESC";
$transactions_result = mysqli_query($conn, $transactions_sql);

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Payment History & Receipts</h1>
    <p style="color: var(--text-muted);">View your complete payment history and download receipts.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 300px; gap: 3rem;">
    <div class="payment-list">
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h2 style="margin-bottom: 1.5rem;">Transaction Records</h2>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($trans = mysqli_fetch_assoc($transactions_result)): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($trans['created_at'])); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($trans['fee_type']); ?></strong>
                                <?php if($trans['payment_method']): ?>
                                    <br><small style="color: var(--text-muted);">via <?php echo htmlspecialchars($trans['payment_method']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><strong>$<?php echo number_format($trans['amount'], 2); ?></strong></td>
                            <td>
                                <span class="badge <?php echo $trans['status'] == 'Paid' ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $trans['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($trans['status'] == 'Paid'): ?>
                                    <a href="#receipt" style="color: var(--primary); font-size: 0.85rem; text-decoration: none;" onclick="alert('Receipt for <?php echo htmlspecialchars($trans['fee_type']); ?> - $<?php echo number_format($trans['amount'], 2); ?>')">View Receipt</a>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <aside class="payment-filters" style="height: fit-content;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem;">Filter Transactions</h3>
            
            <div class="form-group">
                <label>Status</label>
                <select style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    <option>All</option>
                    <option>Paid</option>
                    <option>Pending</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date Range</label>
                <select style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    <option>All Time</option>
                    <option>Last 30 Days</option>
                    <option>Last 90 Days</option>
                    <option>This Year</option>
                </select>
            </div>
            
            <button class="btn primary-btn" style="width: 100%;">Apply Filter</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 1rem;">Export Options</h3>
            
            <button class="btn secondary-btn" style="width: 100%; margin-bottom: 0.75rem; color: var(--text-main); border: 1px solid var(--border);">📥 Download PDF</button>
            <button class="btn secondary-btn" style="width: 100%; margin-bottom: 0.75rem; color: var(--text-main); border: 1px solid var(--border);">📊 Export Excel</button>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">📧 Email Receipts</button>
        </div>
    </aside>
</div>

<?php include 'footer.php'; ?>
