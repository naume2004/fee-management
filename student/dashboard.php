<?php
include '../db.php';
include 'header.php';

// Support simulation of various students via GET or default to S101
$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student details
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    echo "<div class='container' style='padding: 5rem; text-align: center;'><h2>Student not found.</h2><a href='../index.html'>Go Home</a></div>";
    include 'footer.php';
    exit();
}

// Fetch fee summary for Data Payment Dashboard
$summary_sql = "SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as total_pending
    FROM fees WHERE student_id = '$student_id'";
$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);

// Fetch all fees for history
$fees_sql = "SELECT * FROM fees WHERE student_id = '$student_id' ORDER BY created_at DESC";
$fees_result = mysqli_query($conn, $fees_sql);

// Fetch fee breakdown by type
$breakdown_sql = "SELECT fee_type, amount, status FROM fees WHERE student_id = '$student_id'";
$breakdown_result = mysqli_query($conn, $breakdown_sql);

// Fetch overdue payments
$overdue_sql = "SELECT COUNT(f.id) as overdue_count, SUM(f.amount) as overdue_amount
    FROM fees f
    LEFT JOIN payment_deadlines pd ON f.id = pd.fee_id
    WHERE f.student_id = '$student_id' AND f.status = 'Pending'
    AND pd.due_date < CURDATE()";
$overdue_result = mysqli_query($conn, $overdue_sql);
$overdue = mysqli_fetch_assoc($overdue_result);
?>

<?php if (($overdue['overdue_count'] ?? 0) > 0): ?>
    <div style="background: #fee2e2; border-left: 5px solid var(--danger); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h3 style="margin: 0 0 0.5rem; color: var(--danger);">⚠️ Payment Reminder</h3>
                <p style="margin: 0; color: #7f1d1d; font-size: 0.95rem;">You have <strong><?php echo $overdue['overdue_count']; ?> overdue payment(s)</strong> totaling <strong>$<?php echo number_format($overdue['overdue_amount'], 2); ?></strong></p>
                <p style="margin: 0.5rem 0 0; color: #7f1d1d; font-size: 0.85rem;">Please pay as soon as possible to avoid late fees and academic holds.</p>
            </div>
            <a href="payment_reminders.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" class="btn primary-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; white-space: nowrap;">View Details</a>
        </div>
    </div>
<?php endif; ?>

<div class="welcome-section" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1>Student Financial Dashboard</h1>
        <p style="color: var(--text-muted);">Name: <strong><?php echo htmlspecialchars($student['name']); ?></strong> | ID: <?php echo htmlspecialchars($student['student_id']); ?></p>
    </div>
    <div style="text-align: right;">
        <span class="badge badge-success"><?php echo htmlspecialchars($student['course']); ?></span>
    </div>
</div>

<!-- Quick Access Menu -->
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 1rem; margin-bottom: 3rem; color: white;">
    <h2 style="margin-top: 0; margin-bottom: 1.5rem; color: white;">Quick Access</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <!-- Pay Online -->
        <a href="payment_gateway.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">💳</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Pay Online</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Make payments now</p>
        </a>
        
        <!-- Installments -->
        <a href="payment_plans.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📅</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Installments</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">View payment plans</p>
        </a>
        
        <!-- Payment History -->
        <a href="payment_history.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📊</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">History</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Payment records</p>
        </a>
        
        <!-- Reminders -->
        <a href="payment_reminders.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">⚠️</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Reminders</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Pending notifications</p>
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Paid</h3>
        <div class="value" style="color: var(--success);">$<?php echo number_format($summary['total_paid'] ?? 0, 2); ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Completed transactions</p>
    </div>
    <div class="stat-card">
        <h3>Financial Clearance</h3>
        <div class="value">
            <?php 
                $total = ($summary['total_paid'] ?? 0) + ($summary['total_pending'] ?? 0);
                echo ($total > 0 && ($summary['total_pending'] ?? 0) <= 0) ? 'CLEARED' : 'PENDING';
            ?>
        </div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">School status</p>
    </div>
</div>

<div class="dashboard-sections" style="display: grid; grid-template-columns: 1fr 2fr; gap: 3rem; margin-top: 2rem;">
    <aside class="financial-breakdown">
        <div class="table-section" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h2 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Fee Breakdown</h2>
            <ul style="list-style: none;">
                <?php while($item = mysqli_fetch_assoc($breakdown_result)): ?>
                    <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                        <div>
                            <p style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($item['fee_type']); ?></p>
                            <span class="badge <?php echo $item['status'] == 'Paid' ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.65rem;">
                                <?php echo $item['status']; ?>
                            </span>
                        </div>
                        <p style="font-weight: 700;">$<?php echo number_format($item['amount'], 2); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </aside>

    <div class="payment-history">
        <div class="admin-dashboard-header" style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; align-items: center;">
            <button class="btn" style="background: white; border: 1px solid var(--border); color: var(--text-main); font-size: 0.8rem; padding: 0.4rem 0.75rem; display: flex; align-items: center; gap: 0.4rem;">🖨️ Print</button>
            <button class="btn" style="background: #800000; color: white; font-size: 0.8rem; padding: 0.4rem 0.75rem; display: flex; align-items: center; gap: 0.4rem;">+ Add Fee</button>
            <button class="btn" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.8rem; padding: 0.4rem 0.75rem; display: flex; align-items: center; gap: 0.4rem;">📥 Export</button>
        </div>

        <div class="filter-section" style="background: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 1.5rem; display: flex; gap: 0.75rem; align-items: center;">
            <select style="flex: 1; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.3rem; color: var(--text-muted); font-size: 0.9rem;">
                <option>All Status</option>
                <option>Paid</option>
                <option>Partial</option>
                <option>Pending</option>
            </select>
            <button class="btn" style="background: #800000; color: white; padding: 0.5rem 1.25rem; display: flex; align-items: center; gap: 0.4rem; font-weight: 600; font-size: 0.9rem;">
                🔍 Filter
            </button>
        </div>

        <div class="table-section" style="background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <tr>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Remaining</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Status</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Due Date</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">PIN</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($fees_result, 0); 
                    if (mysqli_num_rows($fees_result) > 0): 
                        while($fee = mysqli_fetch_assoc($fees_result)): 
                            $mock_pin = str_pad(($fee['id'] * 123456) % 999999, 6, '0', STR_PAD_LEFT);
                            $remaining = $fee['status'] == 'Paid' ? 0.00 : $fee['amount'];
                    ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem; font-weight: 500; font-size: 0.9rem;"><?php echo number_format($remaining, 2); ?></td>
                            <td style="padding: 1rem;">
                                <span class="badge" style="
                                    padding: 0.35rem 0.7rem; 
                                    border-radius: 0.4rem; 
                                    font-weight: 700; 
                                    font-size: 0.7rem;
                                    <?php if($fee['status'] == 'Paid'): ?>
                                        background: #15803d; color: white;
                                    <?php elseif($fee['status'] == 'Pending'): ?>
                                        background: #991b1b; color: white;
                                    <?php else: ?>
                                        background: #eab308; color: white;
                                    <?php endif; ?>
                                ">
                                    <?php echo $fee['status']; ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: #475569; font-size: 0.85rem;">Dec 18, 2025</td>
                            <td style="padding: 1rem; color: #475569; font-size: 0.85rem;"><?php echo $mock_pin; ?></td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 4px;">
                                    <button title="View" style="background: #38bdf8; border: none; padding: 4px 6px; border-radius: 3px; cursor: pointer; color: white; font-size: 0.75rem;">👁️</button>
                                    <button title="Edit" style="background: #fbbf24; border: none; padding: 4px 6px; border-radius: 3px; cursor: pointer; color: white; font-size: 0.75rem;">📝</button>
                                    <?php if($fee['status'] != 'Paid'): ?>
                                        <button title="Pay" style="background: #10b981; border: none; padding: 4px 6px; border-radius: 3px; cursor: pointer; color: white; font-size: 0.75rem;">💳</button>
                                    <?php endif; ?>
                                    <button title="Print" style="background: #64748b; border: none; padding: 4px 6px; border-radius: 3px; cursor: pointer; color: white; font-size: 0.75rem;">🖨️</button>
                                    <?php if($fee['status'] == 'Paid'): ?>
                                        <button title="Delete" style="background: #ef4444; border: none; padding: 4px 6px; border-radius: 3px; cursor: pointer; color: white; font-size: 0.75rem;">🗑️</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
