<?php
include '../db.php';

$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

$payment_sql = "SELECT * FROM online_payments WHERE id = $payment_id AND student_id = '$student_id'";
$payment_result = mysqli_query($conn, $payment_sql);
$payment = mysqli_fetch_assoc($payment_result);

if (!$payment) {
    echo "<div style='padding: 3rem; text-align: center;'><h2>Receipt not found</h2><a href='dashboard.php'>Go to Dashboard</a></div>";
    exit();
}

$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

$fees_sql = "SELECT * FROM fees WHERE student_id = '$student_id' AND status = 'Paid' AND payment_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
$fees_result = mysqli_query($conn, $fees_sql);
$fees_paid = [];
while($fee = mysqli_fetch_assoc($fees_result)) {
    $fees_paid[] = $fee;
}

include 'header.php';
?>

<style>
    .receipt-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .receipt-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 2.5rem;
        text-align: center;
    }
    .receipt-success-icon {
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 1rem;
    }
    .receipt-body {
        padding: 2rem;
    }
    .receipt-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px dashed #e5e7eb;
    }
    .receipt-section:last-child {
        border-bottom: none;
    }
    .receipt-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .receipt-value {
        font-size: 1.1rem;
        font-weight: 600;
    }
    .amount-display {
        font-size: 2.5rem;
        font-weight: 800;
        color: #10b981;
    }
    .fee-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .fee-item:last-child {
        border-bottom: none;
    }
    .receipt-footer {
        background: #f9fafb;
        padding: 1.5rem 2rem;
        text-align: center;
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    @media print {
        body * { visibility: hidden; }
        .receipt-container, .receipt-container * { visibility: visible; }
        .receipt-container { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none; }
    }
</style>

<div style="padding: 2rem 0;">
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-success-icon">✅</div>
            <h1 style="margin: 0; font-size: 2rem;">Payment Successful!</h1>
            <p style="margin: 0.5rem 0 0; opacity: 0.9;">Thank you for your payment. Your transaction has been processed.</p>
        </div>
        
        <div class="receipt-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="receipt-section">
                    <div class="receipt-label">Receipt Number</div>
                    <div class="receipt-value"><?php echo 'RCP-' . str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?></div>
                </div>
                <div class="receipt-section">
                    <div class="receipt-label">Date & Time</div>
                    <div class="receipt-value"><?php echo date('M d, Y h:i A', strtotime($payment['created_at'])); ?></div>
                </div>
            </div>
            
            <div class="receipt-section">
                <div class="receipt-label">Student Information</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem;">
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Name</div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($student['name']); ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Student ID</div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($student['student_id']); ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Class</div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($student['class_name']); ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Payment Method</div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($payment['payment_method']); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="receipt-section">
                <div class="receipt-label">Amount Paid</div>
                <div class="amount-display">UGX <?php echo number_format($payment['amount_ugx'], 0); ?></div>
                <div style="font-size: 0.9rem; color: var(--text-muted);">USD <?php echo number_format($payment['amount_usd'], 2); ?></div>
            </div>
            
            <div class="receipt-section">
                <div class="receipt-label">Payment For (Fees Paid)</div>
                <div>
                    <?php 
                    $fee_types = [];
                    foreach($fees_paid as $fee) {
                        $fee_types[] = $fee['fee_type'];
                    }
                    if (empty($fee_types)) {
                        echo '<div class="fee-item"><span>All Pending Fees</span></div>';
                    } else {
                        foreach(array_unique($fee_types) as $type): ?>
                            <div class="fee-item">
                                <span><?php echo htmlspecialchars($type); ?></span>
                                <span>Paid</span>
                            </div>
                        <?php endforeach;
                    }
                    ?>
                </div>
            </div>
            
            <?php if ($payment['payment_type'] === 'bank' && $payment['transaction_ref']): ?>
            <div class="receipt-section">
                <div class="receipt-label">Transaction Reference</div>
                <div class="receipt-value"><?php echo htmlspecialchars($payment['transaction_ref']); ?></div>
            </div>
            <?php endif; ?>
            
            <div style="background: #f0fdf4; border-radius: 0.75rem; padding: 1.5rem; margin-top: 1.5rem;">
                <h4 style="margin: 0 0 1rem; color: #065f46;">📧 What's Next?</h4>
                <ul style="margin: 0; padding-left: 1.25rem; color: #065f46;">
                    <li>A confirmation email has been sent to your registered email</li>
                    <li>Your school's finance office will verify the payment within 24 hours</li>
                    <li>You can download this receipt for your records</li>
                    <li>For any queries, contact the school with this receipt number</li>
                </ul>
            </div>
        </div>
        
        <div class="receipt-footer">
            <p style="margin: 0 0 1rem;">This is an official receipt for the payment made. Please keep this for your records.</p>
            <p style="margin: 0;">School Finance Office | Email: fees@school.edu | Phone: +256 XXX XXXX</p>
        </div>
        
        <div class="no-print" style="padding: 1.5rem 2rem; background: #f9fafb; display: flex; gap: 1rem; justify-content: center; border-top: 1px solid #e5e7eb;">
            <button onclick="window.print()" class="btn primary-btn">🖨️ Print Receipt</button>
            <a href="dashboard.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" class="btn secondary-btn">← Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
