<?php
include '../db.php';
include 'auth.php';

// Comprehensive Payment Analytics
$stats_sql = "SELECT 
    fee_type, 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as collected,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as pending,
    COUNT(CASE WHEN status = 'Paid' THEN 1 END) as paid_count,
    COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count,
    COUNT(id) as total_records
    FROM fees 
    GROUP BY fee_type";
$stats_result = mysqli_query($conn, $stats_sql);

// Overall Collection Summary
$overall_sql = "SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as total_pending,
    COUNT(CASE WHEN status = 'Paid' THEN 1 END) as paid_transactions,
    COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_transactions
    FROM fees";
$overall_result = mysqli_query($conn, $overall_sql);
$overall = mysqli_fetch_assoc($overall_result);

// Payment Method Distribution
$method_sql = "SELECT payment_method, COUNT(*) as count, SUM(amount) as total 
    FROM fees 
    WHERE payment_method IS NOT NULL 
    GROUP BY payment_method";
$method_result = mysqli_query($conn, $method_sql);

// Student Payment Status
$student_sql = "SELECT 
    s.student_id, 
    s.name, 
    s.course,
    SUM(CASE WHEN f.status = 'Paid' THEN f.amount ELSE 0 END) as paid,
    SUM(CASE WHEN f.status = 'Pending' THEN f.amount ELSE 0 END) as pending
    FROM students s 
    LEFT JOIN fees f ON s.student_id = f.student_id
    GROUP BY s.student_id
    ORDER BY pending DESC
    LIMIT 10";
$student_result = mysqli_query($conn, $student_sql);

include 'header.php';
?>

<div class="admin-dashboard-header" style="margin-bottom: 3rem;">
    <h1>Advanced Payment Analytics</h1>
    <p style="color: var(--text-muted);">Comprehensive insights into university payment collections and trends.</p>
</div>

<!-- KPI Cards -->
<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Collections</h3>
        <div class="value" style="color: var(--success);">$<?php echo number_format($overall['total_paid'] ?? 0, 2); ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.85rem;"><?php echo number_format($overall['paid_transactions'] ?? 0); ?> transactions</p>
    </div>
    
    <div class="stat-card">
        <h3>Outstanding Balance</h3>
        <div class="value" style="color: var(--danger);">$<?php echo number_format($overall['total_pending'] ?? 0, 2); ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.85rem;"><?php echo number_format($overall['pending_transactions'] ?? 0); ?> pending</p>
    </div>
    
    <div class="stat-card">
        <h3>Recovery Rate</h3>
        <div class="value">
            <?php 
                $total = ($overall['total_paid'] ?? 0) + ($overall['total_pending'] ?? 0);
                echo $total > 0 ? round(($overall['total_paid'] / $total) * 100, 1) : 0;
            ?>%
        </div>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.85rem;">Overall collection rate</p>
    </div>

    <div class="stat-card">
        <h3>Total Ledger</h3>
        <div class="value" style="color: var(--primary);">$<?php echo number_format(($overall['total_paid'] ?? 0) + ($overall['total_pending'] ?? 0), 2); ?></div>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.85rem;">All fees tracked</p>
    </div>
</div>

<!-- Analytics Sections -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-top: 3rem; margin-bottom: 3rem;">
    <!-- Collection by Fee Type -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">Collection Breakdown by Fee Type</h2>
        
        <table class="data-table" style="font-size: 0.9rem;">
            <thead>
                <tr>
                    <th>Fee Type</th>
                    <th>Collected</th>
                    <th>Pending</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($stats_result)): 
                    $total = $row['collected'] + $row['pending'];
                    $rate = $total > 0 ? round(($row['collected'] / $total) * 100, 1) : 0;
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['fee_type']); ?></strong></td>
                        <td style="color: var(--success);">$<?php echo number_format($row['collected'], 2); ?></td>
                        <td style="color: var(--danger);">$<?php echo number_format($row['pending'], 2); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 60px; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                    <div style="width: <?php echo $rate; ?>%; height: 100%; background: var(--success);"></div>
                                </div>
                                <span style="font-weight: 600; font-size: 0.85rem;"><?php echo $rate; ?>%</span>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Payment Method Distribution -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">Payment Methods Used</h2>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php 
            $total_via_methods = 0;
            $method_rows = [];
            mysqli_data_seek($method_result, 0);
            while($row = mysqli_fetch_assoc($method_result)) {
                $total_via_methods += $row['total'];
                $method_rows[] = $row;
            }
            
            foreach($method_rows as $row):
                $percentage = $total_via_methods > 0 ? round(($row['total'] / $total_via_methods) * 100, 1) : 0;
            ?>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($row['payment_method'] ?? 'Not Specified'); ?></p>
                        <p style="margin: 0; color: var(--text-muted);"><?php echo $row['count']; ?> payments</p>
                    </div>
                    <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                        <div style="width: <?php echo $percentage; ?>%; height: 100%; background: var(--primary); transition: width 0.3s;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.25rem;">
                        <span style="font-size: 0.8rem; color: var(--text-muted);">$<?php echo number_format($row['total'], 2); ?></span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $percentage; ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Top Students with Outstanding Balances -->
<div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 3rem;">
    <h2 style="margin-bottom: 1.5rem;">Top Students with Outstanding Balances</h2>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Paid</th>
                <th>Outstanding</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($student_result)): 
                $total_owed = $row['pending'];
            ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['student_id']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['course']); ?></td>
                    <td style="color: var(--success);">$<?php echo number_format($row['paid'] ?? 0, 2); ?></td>
                    <td style="color: var(--danger); font-weight: 600;">$<?php echo number_format($total_owed ?? 0, 2); ?></td>
                    <td>
                        <?php if ($total_owed > 5000): ?>
                            <span class="badge" style="background: #fee2e2; color: #991b1b;">Critical</span>
                        <?php elseif ($total_owed > 2000): ?>
                            <span class="badge badge-warning">High</span>
                        <?php elseif ($total_owed > 0): ?>
                            <span class="badge" style="background: #fef3c7; color: #92400e;">Moderate</span>
                        <?php else: ?>
                            <span class="badge badge-success">Cleared</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Financial Summary Reports -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
    <!-- Performance Metrics -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">Key Performance Metrics</h2>
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="padding: 1rem; background: #f0fdf4; border-radius: 0.5rem; border-left: 4px solid var(--success);">
                <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Average Payment per Transaction</p>
                <p style="margin: 0.5rem 0 0; font-size: 1.5rem; color: var(--success); font-weight: 900;">
                    $<?php echo $overall['paid_transactions'] > 0 ? number_format($overall['total_paid'] / $overall['paid_transactions'], 2) : '0.00'; ?>
                </p>
            </div>

            <div style="padding: 1rem; background: #fef2f2; border-radius: 0.5rem; border-left: 4px solid var(--danger);">
                <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Average Outstanding per Student</p>
                <p style="margin: 0.5rem 0 0; font-size: 1.5rem; color: var(--danger); font-weight: 900;">
                    $<?php 
                    $student_count_sql = "SELECT COUNT(DISTINCT student_id) as count FROM students";
                    $student_count_result = mysqli_query($conn, $student_count_sql);
                    $student_count = mysqli_fetch_assoc($student_count_result)['count'] ?? 1;
                    echo number_format(($overall['total_pending'] ?? 0) / $student_count, 2);
                    ?>
                </p>
            </div>

            <div style="padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border-left: 4px solid var(--primary);">
                <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Students with Perfect Payment</p>
                <p style="margin: 0.5rem 0 0; font-size: 1.5rem; color: var(--primary); font-weight: 900;">
                    <?php 
                    $cleared_sql = "SELECT COUNT(DISTINCT s.student_id) as count FROM students s 
                        WHERE NOT EXISTS (SELECT 1 FROM fees f WHERE f.student_id = s.student_id AND f.status = 'Pending')";
                    $cleared_result = mysqli_query($conn, $cleared_sql);
                    $cleared = mysqli_fetch_assoc($cleared_result)['count'] ?? 0;
                    echo $cleared;
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Export & Actions -->
    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <h2 style="margin-bottom: 1.5rem;">Generate Reports</h2>
        
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <button class="btn primary-btn" style="width: 100%; text-align: center;">📊 Export All Data (CSV)</button>
            <button class="btn secondary-btn" style="width: 100%; text-align: center; color: var(--text-main); border: 1px solid var(--border);">📄 PDF Report</button>
            <button class="btn secondary-btn" style="width: 100%; text-align: center; color: var(--text-main); border: 1px solid var(--border);">📈 Excel Analytics</button>
            <button class="btn secondary-btn" style="width: 100%; text-align: center; color: var(--text-main); border: 1px solid var(--border);">📧 Email Summary</button>
        </div>

        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
            <h3 style="margin-bottom: 1rem; font-size: 0.95rem;">Data Summary</h3>
            <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.5rem;">
                <p style="margin: 0;"><strong>Last Updated:</strong> Just now</p>
                <p style="margin: 0;"><strong>Date Range:</strong> All time</p>
                <p style="margin: 0;"><strong>Total Records:</strong> 
                    <?php 
                    $total_records_sql = "SELECT COUNT(*) as count FROM fees";
                    $total_records = mysqli_fetch_assoc(mysqli_query($conn, $total_records_sql))['count'] ?? 0;
                    echo $total_records;
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
