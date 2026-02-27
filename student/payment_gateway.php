<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student and pending fees
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

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Secure Payment Gateway</h1>
    <p style="color: var(--text-muted);">Choose your preferred payment method to settle your school fees.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 3rem;">
    <aside class="payment-summary" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); height: fit-content;">
        <h2 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Payment Summary</h2>
        
        <div style="margin-bottom: 2rem;">
            <p style="color: var(--text-muted); font-size: 0.85rem;">Student: <strong><?php echo htmlspecialchars($student['name']); ?></strong></p>
            <p style="color: var(--text-muted); font-size: 0.85rem;">ID: <strong><?php echo htmlspecialchars($student['student_id']); ?></strong></p>
        </div>
        
        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem;">Outstanding Fees:</h3>
            <ul style="list-style: none;">
                <?php foreach($pending_fees as $fee): ?>
                    <li style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9rem;">
                        <span><?php echo htmlspecialchars($fee['fee_type']); ?></span>
                        <strong style="color: var(--danger);">$<?php echo number_format($fee['amount'], 2); ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div style="background: #fef3c7; border-radius: 0.5rem; padding: 1rem; margin-bottom: 2rem; border-left: 4px solid #f59e0b;">
            <p style="font-size: 0.8rem; color: #92400e; margin: 0;">
                <strong>Total Due:</strong>
            </p>
            <p style="font-size: 1.5rem; color: var(--danger); font-weight: 900; margin: 0.5rem 0 0;">$<?php echo number_format($total_pending, 2); ?></p>
        </div>
        
        <button class="btn secondary-btn" style="width: 100%; color: var(--primary);">← Back to Dashboard</button>
    </aside>

    <div class="payment-methods">
        <h2 style="margin-bottom: 2rem;">Select Payment Method</h2>
        
        <!-- Credit Card Payment -->
        <div class="payment-method-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; border: 2px solid var(--border); cursor: pointer; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.15)';" onmouseout="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; margin-right: 1rem;">💳</div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem;">Credit/Debit Card</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Visa, Mastercard, American Express</p>
                </div>
            </div>
            <form style="margin-top: 1rem;">
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" placeholder="1234 5678 9012 3456" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="text" placeholder="MM/YY" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" placeholder="123" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                    </div>
                </div>
                <button type="submit" class="btn primary-btn" style="width: 100%; margin-top: 1rem;">Pay $<?php echo number_format($total_pending, 2); ?></button>
            </form>
        </div>

        <!-- Mobile Money Payment -->
        <div class="payment-method-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; border: 2px solid var(--border); cursor: pointer; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.15)';" onmouseout="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; margin-right: 1rem;">📱</div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem;">Mobile Money</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">M-Pesa, Airtel Money, MTN Mobile Money</p>
                </div>
            </div>
            <form style="margin-top: 1rem;">
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" placeholder="+254712345678" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                </div>
                <div class="form-group">
                    <label>Provider</label>
                    <select style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                        <option>Select Provider</option>
                        <option>M-Pesa (Kenya)</option>
                        <option>Airtel Money</option>
                        <option>MTN Mobile Money</option>
                    </select>
                </div>
                <button type="submit" class="btn primary-btn" style="width: 100%; margin-top: 1rem;">Pay $<?php echo number_format($total_pending, 2); ?></button>
            </form>
        </div>

        <!-- Bank Transfer Payment -->
        <div class="payment-method-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; border: 2px solid var(--border); cursor: pointer; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.15)';" onmouseout="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; margin-right: 1rem;">🏦</div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem;">Bank Transfer</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Direct bank account transfer</p>
                </div>
            </div>
            <div style="margin-top: 1rem; background: #f8fafc; padding: 1rem; border-radius: 0.5rem;">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0 0 0.5rem;">Transfer to:</p>
                <p style="margin: 0.25rem 0;"><strong>Bank Name:</strong> First National Bank</p>
                <p style="margin: 0.25rem 0;"><strong>Account:</strong> 1234567890</p>
                <p style="margin: 0.25rem 0;"><strong>Reference:</strong> <?php echo $student_id; ?></p>
                <p style="margin: 0.25rem 0; color: var(--danger);"><strong>Amount:</strong> $<?php echo number_format($total_pending, 2); ?></p>
            </div>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 1rem;">Please include your student ID as reference for proper allocation.</p>
        </div>

        <!-- Pay by Installment -->
        <div class="payment-method-card" style="background: white; padding: 2rem; border-radius: 1rem; border: 2px solid var(--border); cursor: pointer; transition: all 0.3s;" onmouseover="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.15)';" onmouseout="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; margin-right: 1rem;">📅</div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem;">Installment Plan</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Flexible payment over multiple months</p>
                </div>
            </div>
            <div style="margin-top: 1rem; background: #f8fafc; padding: 1rem; border-radius: 0.5rem;">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0 0 0.75rem;">Choose your payment plan:</p>
                <div style="display: grid; gap: 0.5rem;">
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border-radius: 0.25rem; hover: background: #f1f5f9;">
                        <input type="radio" name="plan" value="2months" style="margin-right: 0.5rem;">
                        <span style="flex: 1;"><strong>2 Months:</strong> $<?php echo number_format($total_pending/2, 2); ?>/month</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border-radius: 0.25rem;">
                        <input type="radio" name="plan" value="3months" style="margin-right: 0.5rem;">
                        <span style="flex: 1;"><strong>3 Months:</strong> $<?php echo number_format($total_pending/3, 2); ?>/month</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border-radius: 0.25rem;">
                        <input type="radio" name="plan" value="6months" style="margin-right: 0.5rem;">
                        <span style="flex: 1;"><strong>6 Months:</strong> $<?php echo number_format($total_pending/6, 2); ?>/month</span>
                    </label>
                </div>
            </div>
            <button class="btn primary-btn" style="width: 100%; margin-top: 1rem;">Setup Installment Plan</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
