<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';
$method = isset($_GET['method']) ? $_GET['method'] : 'mobile';

$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

$pending_sql = "SELECT * FROM fees WHERE student_id = '$student_id' AND status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_sql);

$total_pending = 0;
$pending_fees = [];
while($fee = mysqli_fetch_assoc($pending_result)) {
    $total_pending += $fee['amount'];
    $pending_fees[] = $fee;
}

$method_names = [
    'mobile' => 'Mobile Money',
    'card' => 'Credit/Debit Card',
    'ussd' => 'USSD Payment',
    'qr' => 'QR Code Payment',
    'bank' => 'Bank Transfer',
    'autopay' => 'Auto-Pay (Standing Order)'
];

$method_icons = [
    'mobile' => '📱',
    'card' => '💳',
    'ussd' => '☎️',
    'qr' => '📲',
    'bank' => '🏦',
    'autopay' => '🔄'
];

include 'header.php';
?>

<style>
    .method-selector {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 2rem;
    }
    .method-btn {
        padding: 0.75rem 1.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 50px;
        background: white;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        color: var(--text-main);
        font-weight: 500;
    }
    .method-btn:hover, .method-btn.active {
        border-color: #10b981;
        background: #ecfdf5;
        color: #065f46;
    }
    .payment-form-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .ussd-steps {
        background: #f0fdf4;
        border: 1px solid #10b981;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }
    .ussd-step {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px dashed #10b981;
    }
    .ussd-step:last-child {
        border-bottom: none;
    }
    .step-number {
        width: 30px;
        height: 30px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        flex-shrink: 0;
    }
    .qr-display {
        text-align: center;
        padding: 2rem;
    }
    .qr-code {
        width: 200px;
        height: 200px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        margin: 1rem auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
    }
    .autopay-info {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin: 1.5rem 0;
    }
    .autopay-info h4 {
        margin: 0 0 1rem;
        color: #1e40af;
    }
    .autopay-info ul {
        margin: 0;
        padding-left: 1.5rem;
        color: #1e3a8a;
    }
    .autopay-info li {
        margin-bottom: 0.5rem;
    }
    .standing-order-form {
        border: 2px dashed #3b82f6;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }
    .countdown-box {
        background: #fef3c7;
        border-radius: 0.75rem;
        padding: 1rem;
        text-align: center;
        margin-top: 1rem;
    }
    .countdown-timer {
        font-size: 2rem;
        font-weight: 800;
        color: #92400e;
    }
</style>

<div class="skip-pay-header" style="text-align: center; margin-bottom: 2rem;">
    <h1>⏭️ Pay Online - Skip the Line</h1>
    <p style="color: var(--text-muted);">Complete your payment securely. No queue, no waiting.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem;">
    <aside class="payment-summary" style="background: white; padding: 1.5rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); height: fit-content;">
        <h3 style="margin-bottom: 1rem;">📋 Payment Summary</h3>
        
        <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <p style="color: var(--text-muted); font-size: 0.85rem;">Student: <strong><?php echo htmlspecialchars($student['name']); ?></strong></p>
            <p style="color: var(--text-muted); font-size: 0.85rem;">ID: <strong><?php echo htmlspecialchars($student['student_id']); ?></strong></p>
            <p style="color: var(--text-muted); font-size: 0.85rem;">Class: <strong><?php echo htmlspecialchars($student['class_name']); ?></strong></p>
        </div>
        
        <h4 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">Outstanding Fees:</h4>
        <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem;">
            <?php foreach($pending_fees as $fee): ?>
                <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9rem; border-bottom: 1px dashed #e5e7eb;">
                    <span><?php echo htmlspecialchars($fee['fee_type']); ?></span>
                    <strong style="color: var(--danger);">UGX <?php echo number_format($fee['amount'] * 3800, 0); ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <div style="background: #fef3c7; border-radius: 0.5rem; padding: 1rem;">
            <p style="font-size: 0.8rem; color: #92400e; margin: 0;"><strong>Total Due:</strong></p>
            <p style="font-size: 1.75rem; color: var(--danger); font-weight: 900; margin: 0.5rem 0 0;">UGX <?php echo number_format($total_pending * 3800, 0); ?></p>
        </div>
        
        <a href="skip_line.php?sim_id=<?php echo $student_id; ?>" class="btn secondary-btn" style="width: 100%; margin-top: 1rem; text-align: center;">← Change Method</a>
    </aside>

    <div class="payment-form-area">
        <div class="method-selector">
            <a href="?sim_id=<?php echo $student_id; ?>&method=mobile" class="method-btn <?php echo $method === 'mobile' ? 'active' : ''; ?>">📱 Mobile Money</a>
            <a href="?sim_id=<?php echo $student_id; ?>&method=card" class="method-btn <?php echo $method === 'card' ? 'active' : ''; ?>">💳 Card</a>
            <a href="?sim_id=<?php echo $student_id; ?>&method=ussd" class="method-btn <?php echo $method === 'ussd' ? 'active' : ''; ?>">☎️ USSD</a>
            <a href="?sim_id=<?php echo $student_id; ?>&method=qr" class="method-btn <?php echo $method === 'qr' ? 'active' : ''; ?>">📲 QR Code</a>
            <a href="?sim_id=<?php echo $student_id; ?>&method=bank" class="method-btn <?php echo $method === 'bank' ? 'active' : ''; ?>">🏦 Bank</a>
            <a href="?sim_id=<?php echo $student_id; ?>&method=autopay" class="method-btn <?php echo $method === 'autopay' ? 'active' : ''; ?>">🔄 Auto-Pay</a>
        </div>

        <div class="payment-form-card">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                <div style="font-size: 2.5rem;"><?php echo $method_icons[$method]; ?></div>
                <div>
                    <h2 style="margin: 0;"><?php echo $method_names[$method]; ?></h2>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Secure payment via <?php echo $method_names[$method]; ?></p>
                </div>
            </div>

            <?php if ($method === 'mobile'): ?>
                <form action="process_online_payment.php" method="POST">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    <input type="hidden" name="payment_type" value="mobile">
                    <input type="hidden" name="amount" value="<?php echo $total_pending * 3800; ?>">
                    
                    <div class="form-group">
                        <label>Phone Number (Registered with Mobile Money)</label>
                        <input type="tel" name="phone" placeholder="+256 7xx xxx xxx" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mobile Money Provider</label>
                        <select name="provider" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                            <option value="">Select Provider</option>
                            <option value="MTN Mobile Money">MTN Mobile Money</option>
                            <option value="Airtel Money">Airtel Money</option>
                        </select>
                    </div>
                    
                    <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 1rem; margin: 1.5rem 0; font-size: 0.9rem; color: #065f46;">
                        <strong>💡 Payment Process:</strong><br>
                        1. Click Pay Now<br>
                        2. You will receive a USSD prompt on your phone<br>
                        3. Confirm the amount and PIN<br>
                        4. Payment confirmed instantly
                    </div>
                    
                    <button type="submit" class="btn primary-btn" style="width: 100%; background: #10b981; font-size: 1.1rem; padding: 1rem;" <?php echo $total_pending <= 0 ? 'disabled' : ''; ?>>
                        💰 Pay UGX <?php echo number_format($total_pending * 3800, 0); ?> via Mobile Money
                    </button>
                </form>

            <?php elseif ($method === 'card'): ?>
                <form action="process_online_payment.php" method="POST">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    <input type="hidden" name="payment_type" value="card">
                    <input type="hidden" name="amount" value="<?php echo $total_pending * 3800; ?>">
                    
                    <div class="form-group">
                        <label>Card Number</label>
                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Expiry Date</label>
                            <input type="text" name="expiry" placeholder="MM/YY" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" name="cvv" placeholder="123" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Cardholder Name</label>
                        <input type="text" name="card_name" placeholder="<?php echo htmlspecialchars($student['name']); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 1rem; margin: 1.5rem 0; font-size: 0.9rem; color: #065f46;">
                        <strong>🔒 Secure Payment:</strong><br>
                        Your card details are encrypted and secure. Payment is processed securely.
                    </div>
                    
                    <button type="submit" class="btn primary-btn" style="width: 100%; background: #10b981; font-size: 1.1rem; padding: 1rem;" <?php echo $total_pending <= 0 ? 'disabled' : ''; ?>>
                        💳 Pay UGX <?php echo number_format($total_pending * 3800, 0); ?>
                    </button>
                </form>

            <?php elseif ($method === 'ussd'): ?>
                <div class="ussd-steps">
                    <h4 style="margin: 0 0 1rem; color: #065f46;">📱 How to Pay via USSD</h4>
                    <div class="ussd-step">
                        <span class="step-number">1</span>
                        <div>
                            <strong>Dial *XXX#</strong> on your MTN or Airtel line
                        </div>
                    </div>
                    <div class="ussd-step">
                        <span class="step-number">2</span>
                        <div>
                            Select <strong>Pay Bills</strong> → <strong>School Fees</strong>
                        </div>
                    </div>
                    <div class="ussd-step">
                        <span class="step-number">3</span>
                        <div>
                            Enter Amount: <strong>UGX <?php echo number_format($total_pending * 3800, 0); ?></strong>
                        </div>
                    </div>
                    <div class="ussd-step">
                        <span class="step-number">4</span>
                        <div>
                            Enter Reference: <strong><?php echo htmlspecialchars($student_id); ?></strong>
                        </div>
                    </div>
                    <div class="ussd-step">
                        <span class="step-number">5</span>
                        <div>
                            Confirm with <strong>PIN</strong> and wait for confirmation SMS
                        </div>
                    </div>
                </div>
                
                <form action="process_online_payment.php" method="POST">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    <input type="hidden" name="payment_type" value="ussd">
                    <input type="hidden" name="amount" value="<?php echo $total_pending * 3800; ?>">
                    
                    <div class="form-group">
                        <label>Phone Number Used for USSD</label>
                        <input type="tel" name="phone" placeholder="+256 7xx xxx xxx" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Service Provider</label>
                        <select name="provider" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                            <option value="">Select Provider</option>
                            <option value="MTN">MTN</option>
                            <option value="Airtel">Airtel</option>
                        </select>
                    </div>
                    
                    <div class="countdown-box">
                        <p style="margin: 0; color: #92400e; font-size: 0.9rem;">Complete payment within:</p>
                        <div class="countdown-timer">10:00</div>
                    </div>
                    
                    <button type="submit" class="btn primary-btn" style="width: 100%; background: #10b981; font-size: 1.1rem; padding: 1rem; margin-top: 1rem;" <?php echo $total_pending <= 0 ? 'disabled' : ''; ?>>
                        ✓ I've Completed the Payment
                    </button>
                </form>

            <?php elseif ($method === 'qr'): ?>
                <div class="qr-display">
                    <h3 style="margin-bottom: 1rem;">Scan to Pay</h3>
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">Scan this QR code with your banking app to pay</p>
                    
                    <div class="qr-code">
                        ⌕
                    </div>
                    
                    <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 1rem; margin: 1.5rem 0; text-align: left;">
                        <p style="margin: 0 0 0.5rem;"><strong>Amount:</strong> UGX <?php echo number_format($total_pending * 3800, 0); ?></p>
                        <p style="margin: 0 0 0.5rem;"><strong>Reference:</strong> <?php echo htmlspecialchars($student_id); ?></p>
                        <p style="margin: 0;"><strong>Account:</strong> First National Bank - 1234567890</p>
                    </div>
                    
                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                        💡 Open your bank's mobile app, select QR Pay, and scan the code above
                    </div>
                    
                    <form action="process_online_payment.php" method="POST">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                        <input type="hidden" name="payment_type" value="qr">
                        <input type="hidden" name="amount" value="<?php echo $total_pending * 3800; ?>">
                        
                        <div class="form-group">
                            <label>Phone Number (for confirmation)</label>
                            <input type="tel" name="phone" placeholder="+256 7xx xxx xxx" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                        </div>
                        
                        <button type="submit" class="btn primary-btn" style="width: 100%; background: #10b981; font-size: 1.1rem; padding: 1rem;">
                            ✓ I've Completed the Payment
                        </button>
                    </form>
                </div>

            <?php elseif ($method === 'bank'): ?>
                <div style="background: #f8fafc; border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem;">🏦 Bank Transfer Details</h4>
                    <p style="margin: 0.5rem 0;"><strong>Bank Name:</strong> First National Bank Uganda</p>
                    <p style="margin: 0.5rem 0;"><strong>Account Name:</strong> [School Name] Fees Account</p>
                    <p style="margin: 0.5rem 0;"><strong>Account Number:</strong> 1234567890</p>
                    <p style="margin: 0.5rem 0;"><strong>Branch:</strong> Main Branch</p>
                    <p style="margin: 0.5rem 0; color: var(--danger);"><strong>Amount:</strong> UGX <?php echo number_format($total_pending * 3800, 0); ?></p>
                    <p style="margin: 0.5rem 0; color: #059669;"><strong>Reference:</strong> <?php echo htmlspecialchars($student_id); ?></p>
                </div>
                
                <div style="background: #fef3c7; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
                    <strong>⚠️ Important:</strong> Use your Student ID (<?php echo htmlspecialchars($student_id); ?>) as the payment reference. This ensures your payment is credited to your account correctly.
                </div>
                
                <form action="process_online_payment.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    <input type="hidden" name="payment_type" value="bank">
                    <input type="hidden" name="amount" value="<?php echo $total_pending * 3800; ?>">
                    
                    <div class="form-group">
                        <label>Transfer Reference/Transaction ID</label>
                        <input type="text" name="transaction_ref" placeholder="Enter your bank transaction reference" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Sender Account Name</label>
                        <input type="text" name="sender_name" placeholder="Name on bank account" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Proof of Payment (Optional)</label>
                        <input type="file" name="proof" accept="image/*,.pdf" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;">
                    </div>
                    
                    <button type="submit" class="btn primary-btn" style="width: 100%; background: #10b981; font-size: 1.1rem; padding: 1rem;" <?php echo $total_pending <= 0 ? 'disabled' : ''; ?>>
                        ✓ Submit Payment Details
                    </button>
                </form>

            <?php elseif ($method === 'autopay'): ?>
                <div class="autopay-info">
                    <h4>🔄 Set Up Automatic Payments</h4>
                    <p>Never miss a payment again! Set up a standing order to automatically pay your fees on a scheduled date each month.</p>
                    <ul>
                        <li>Automatic monthly deductions</li>
                        <li>No need to remember payment dates</li>
                        <li>Instant confirmation via SMS</li>
                        <li>Cancel or modify anytime</li>
                    </ul>
                </div>
                
                <form action="process_online_payment.php" method="POST">
                    <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>">
                    <input type="hidden" name="payment_type" value="autopay">
                    <input type="hidden" name="amount" value="<?php echo $total_pending * 3800; ?>">
                    
                    <div class="form-group">
                        <label>Bank Account Number</label>
                        <input type="text" name="account_number" placeholder="Your bank account number" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Bank Name</label>
                        <select name="bank_name" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                            <option value="">Select Bank</option>
                            <option value="DFCU Bank">DFCU Bank</option>
                            <option value="Stanbic Bank">Stanbic Bank</option>
                            <option value="Centenary Bank">Centenary Bank</option>
                            <option value="Bank of Baroda">Bank of Baroda</option>
                            <option value="Housing Finance">Housing Finance</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Schedule</label>
                        <select name="schedule" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                            <option value="monthly">Monthly</option>
                            <option value="termly">Termly</option>
                            <option value="weekly">Weekly</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" placeholder="+256 7xx xxx xxx" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem;" required>
                    </div>
                    
                    <button type="submit" class="btn primary-btn" style="width: 100%; background: #3b82f6; font-size: 1.1rem; padding: 1rem;">
                        🔄 Set Up Auto-Pay
                    </button>
                </form>
            <?php endif; ?>
            
            <?php if ($total_pending <= 0): ?>
                <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 1.5rem; margin-top: 1.5rem; text-align: center;">
                    <div style="font-size: 3rem;">✅</div>
                    <h3 style="color: #065f46; margin: 1rem 0 0.5rem;">All Fees Paid!</h3>
                    <p style="color: #065f46; margin: 0;">You have no outstanding fees. Thank you!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    let timeLeft = 600;
    const timerElement = document.querySelector('.countdown-timer');
    if (timerElement) {
        const countdown = setInterval(function() {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            if (timeLeft <= 0) {
                clearInterval(countdown);
                timerElement.textContent = 'Expired';
            }
        }, 1000);
    }
</script>

<?php include 'footer.php'; ?>
