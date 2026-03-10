<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Fetch all fees
$fees_sql = "SELECT * FROM fees WHERE student_id = '$student_id'";
$fees_result = mysqli_query($conn, $fees_sql);

$total = 0;
$fees_breakdown = [];
while($fee = mysqli_fetch_assoc($fees_result)) {
    $total += $fee['amount'];
    $fees_breakdown[] = $fee;
}

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Tuition & Fee Calculator</h1>
    <p style="color: var(--text-muted);">Calculate your exact costs and explore different payment scenarios.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
    <!-- Fee Breakdown Calculator -->
    <div>
        <h2 style="margin-bottom: 2rem;">Your Fee Breakdown</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
                <table style="width: 100%; font-size: 0.9rem;">
                    <thead style="color: var(--text-muted);">
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th style="text-align: left; padding: 0.5rem 0;">Fee Category</th>
                            <th style="text-align: right; padding: 0.5rem 0;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $paid_total = 0;
                        $pending_total = 0;
                        foreach($fees_breakdown as $fee): 
                            if($fee['status'] == 'Paid') {
                                $paid_total += $fee['amount'];
                            } else {
                                $pending_total += $fee['amount'];
                            }
                        ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 0.75rem 0;">
                                    <p style="margin: 0;"><?php echo htmlspecialchars($fee['fee_type']); ?></p>
                                    <p style="margin: 0.25rem 0 0; font-size: 0.8rem; color: var(--text-muted);"><?php echo ucfirst($fee['status']); ?></p>
                                </td>
                                <td style="text-align: right; padding: 0.75rem 0; font-weight: 600;">UGX <?php echo number_format($fee['amount'] * 3800, 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="background: #f0fdf4; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0 0 0.5rem;">Total Fees</p>
                <p style="font-size: 2rem; color: var(--primary); font-weight: 900; margin: 0;">UGX <?php echo number_format($total * 3800, 0); ?></p>
            </div>

            <div style="display: grid; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #f8fafc; border-radius: 0.5rem;">
                    <span style="color: var(--success); font-weight: 600;">Paid:</span>
                    <span style="color: var(--success); font-weight: 600;">UGX <?php echo number_format($paid_total * 3800, 0); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: #fef2f2; border-radius: 0.5rem;">
                    <span style="color: var(--danger); font-weight: 600;">Pending:</span>
                    <span style="color: var(--danger); font-weight: 600;">UGX <?php echo number_format($pending_total * 3800, 0); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Comparison -->
    <div>
        <h2 style="margin-bottom: 2rem;">Payment Options Comparison</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0 0 0.75rem;">Outstanding Balance</p>
                <p style="font-size: 1.5rem; color: var(--danger); font-weight: 900; margin: 0;">UGX <?php echo number_format($pending_total * 3800, 0); ?></p>
            </div>

            <div style="border: 1px solid var(--border); border-radius: 0.5rem; overflow: hidden;">
                <!-- Full Payment -->
                <div style="padding: 1rem; border-bottom: 1px solid var(--border); background: #f8fafc;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <p style="margin: 0; font-weight: 600;">Full Payment</p>
                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Save 0%</span>
                    </div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Pay full amount immediately</p>
                    <div style="background: white; padding: 0.75rem; border-radius: 0.25rem; margin-top: 0.75rem; text-align: center;">
                        <p style="font-size: 1.25rem; color: var(--success); font-weight: 700; margin: 0;">UGX <?php echo number_format($pending_total * 3800, 0); ?></p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0 0;">Due Now</p>
                    </div>
                </div>

                <!-- 2 Month Plan -->
                <div style="padding: 1rem; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <p style="margin: 0; font-weight: 600;">2-Month Plan</p>
                        <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Quick Pay</span>
                    </div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Spread across 2 months</p>
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 0.25rem; margin-top: 0.75rem; text-align: center;">
                        <p style="font-size: 1.25rem; color: var(--warning); font-weight: 700; margin: 0;">UGX <?php echo number_format(($pending_total * 3800) / 2, 0); ?></p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0 0;">per month</p>
                    </div>
                </div>

                <!-- 3 Month Plan -->
                <div style="padding: 1rem; border-bottom: 1px solid var(--border); background: #faf5ff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <p style="margin: 0; font-weight: 600;">3-Month Plan</p>
                        <span style="background: #dbeafe; color: #075985; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Popular</span>
                    </div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Balanced payment schedule</p>
                    <div style="background: white; padding: 0.75rem; border-radius: 0.25rem; margin-top: 0.75rem; text-align: center;">
                        <p style="font-size: 1.25rem; color: var(--primary); font-weight: 700; margin: 0;">UGX <?php echo number_format(($pending_total * 3800) / 3, 0); ?></p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0 0;">per month</p>
                    </div>
                </div>

                <!-- 6 Month Plan -->
                <div style="padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <p style="margin: 0; font-weight: 600;">6-Month Plan</p>
                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Low Monthly</span>
                    </div>
                    <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">Lowest monthly payments</p>
                    <div style="background: #f8fafc; padding: 0.75rem; border-radius: 0.25rem; margin-top: 0.75rem; text-align: center;">
                        <p style="font-size: 1.25rem; color: var(--success); font-weight: 700; margin: 0;">UGX <?php echo number_format(($pending_total * 3800) / 6, 0); ?></p>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.25rem 0 0;">per month</p>
                    </div>
                </div>
            </div>

            <button class="btn primary-btn" style="width: 100%; margin-top: 1.5rem;">Set Up Payment Plan</button>
        </div>
    </div>
</div>

<!-- Additional Fee Calculator -->
<div style="background: white; padding: 3rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 3rem;">
    <h2 style="margin-bottom: 2rem;">Interactive Fee Calculator</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tuition Fees</label>
            <input type="text" value="UGX <?php echo number_format(3000 * 3800, 0); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" readonly>
        </div>
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Lab Fees</label>
            <input type="text" value="UGX <?php echo number_format(500 * 3800, 0); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" readonly>
        </div>
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Library Fees</label>
            <input type="text" value="UGX <?php echo number_format(200 * 3800, 0); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" readonly>
        </div>
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Student Activity Fees</label>
            <input type="text" value="UGX <?php echo number_format(150 * 3800, 0); ?>" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem;" readonly>
        </div>
    </div>

    <div style="background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
        <p style="color: var(--text-muted); margin: 0 0 0.5rem;">Estimated Total</p>
        <p style="font-size: 2rem; color: var(--primary); font-weight: 900; margin: 0;">UGX <?php echo number_format(3850 * 3800, 0); ?></p>
    </div>
</div>

<!-- Savings Calculator -->
<div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 3rem; border-radius: 1rem; border-left: 5px solid var(--success);">
    <h2 style="margin-bottom: 1rem; color: #166534;">How Much Can You Save?</h2>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
            <p style="color: var(--text-muted); margin: 0; font-size: 0.85rem;">With Early Payment Discount (10%)</p>
            <p style="font-size: 1.5rem; color: var(--success); font-weight: 900; margin: 0.5rem 0;">-UGX <?php echo number_format($pending_total * 0.1 * 3800, 0); ?></p>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.8rem;">By paying full now</p>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
            <p style="color: var(--text-muted); margin: 0; font-size: 0.85rem;">With Scholarship (50%)</p>
            <p style="font-size: 1.5rem; color: var(--success); font-weight: 900; margin: 0.5rem 0;">-UGX <?php echo number_format($pending_total * 0.5 * 3800, 0); ?></p>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.8rem;">If approved</p>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 0.5rem; text-align: center;">
            <p style="color: var(--text-muted); margin: 0; font-size: 0.85rem;">With Discount Code</p>
            <p style="font-size: 1.5rem; color: var(--success); font-weight: 900; margin: 0.5rem 0;">-UGX <?php echo number_format($pending_total * 0.15 * 3800, 0); ?></p>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.8rem;">EARLYBIRD15</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
