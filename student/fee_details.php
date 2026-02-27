<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Fetch all fees with details
$fees_sql = "SELECT * FROM fees WHERE student_id = '$student_id' ORDER BY fee_type";
$fees_result = mysqli_query($conn, $fees_sql);

$fees_breakdown = [];
$total_amount = 0;
while($fee = mysqli_fetch_assoc($fees_result)) {
    $total_amount += $fee['amount'];
    $fees_breakdown[] = $fee;
}

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Fee Details & Breakdown</h1>
    <p style="color: var(--text-muted);">Understand what each fee covers and how it's allocated to university services.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
    <!-- Fee Categories -->
    <div>
        <h2 style="margin-bottom: 2rem;">Fee Categories Breakdown</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <?php foreach($fees_breakdown as $fee):
                $percentage = $total_amount > 0 ? round(($fee['amount'] / $total_amount) * 100, 1) : 0;
            ?>
                <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div>
                            <h3 style="margin: 0; font-size: 1rem;"><?php echo htmlspecialchars($fee['fee_type']); ?></h3>
                            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">
                                <span class="badge <?php echo $fee['status'] == 'Paid' ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $fee['status']; ?>
                                </span>
                            </p>
                        </div>
                        <p style="margin: 0; font-size: 1.25rem; font-weight: 900; color: var(--primary);">$<?php echo number_format($fee['amount'], 2); ?></p>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="flex: 1;">
                            <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                <div style="width: <?php echo $percentage; ?>%; height: 100%; background: var(--primary);"></div>
                            </div>
                        </div>
                        <span style="font-size: 0.85rem; color: var(--text-muted); min-width: 35px; text-align: right;"><?php echo $percentage; ?>%</span>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div style="padding: 1.5rem; background: #f0fdf4; border-radius: 0.5rem; margin-top: 1rem;">
                <p style="color: var(--text-muted); margin: 0;">Total Fees</p>
                <p style="font-size: 1.75rem; color: var(--success); font-weight: 900; margin: 0.5rem 0 0;">$<?php echo number_format($total_amount, 2); ?></p>
            </div>
        </div>
    </div>

    <!-- Fee Descriptions -->
    <div>
        <h2 style="margin-bottom: 2rem;">What Does Each Fee Cover?</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.5rem;">
                    <div style="font-size: 1.5rem;">🎓</div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.95rem;">Tuition Fee</h3>
                        <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Core academic instruction, course materials, and classroom facilities</p>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.5rem;">
                    <div style="font-size: 1.5rem;">📚</div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.95rem;">Library Fee</h3>
                        <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Access to library resources, digital databases, and study materials</p>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.5rem;">
                    <div style="font-size: 1.5rem;">⚗️</div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.95rem;">Lab Fee</h3>
                        <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Laboratory equipment, materials, and practical training facilities</p>
                    </div>
                </div>
            </div>

            <div>
                <div style="display: flex; align-items: start; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.5rem;">
                    <div style="font-size: 1.5rem;">🎯</div>
                    <div>
                        <h3 style="margin: 0; font-size: 0.95rem;">Student Activity Fee</h3>
                        <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Clubs, sports, events, and student government activities</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Timeline -->
<div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 3rem;">
    <h2 style="margin-bottom: 2rem;">Your Payment Timeline</h2>
    
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php 
        $paid_count = 0;
        foreach($fees_breakdown as $fee):
            if($fee['status'] == 'Paid') {
                $paid_count++;
        ?>
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="min-width: 100px;">
                    <div style="background: var(--success); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900;">✓</div>
                </div>
                <div style="flex: 1;">
                    <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($fee['fee_type']); ?> - Paid</p>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">
                        Paid on <?php echo date('F j, Y', strtotime($fee['payment_date'] ?? $fee['created_at'])); ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-weight: 900; color: var(--success);">$<?php echo number_format($fee['amount'], 2); ?></p>
                </div>
            </div>
        <?php } endforeach;
        
        if($paid_count === 0) {
            echo '<p style="text-align: center; color: var(--text-muted); padding: 2rem; margin: 0;">No payments made yet</p>';
        }
        ?>
    </div>
</div>

<!-- Outstanding Balance Details -->
<div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 3rem;">
    <h2 style="margin-bottom: 2rem;">Outstanding Fees</h2>
    
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <?php 
        $pending_count = 0;
        foreach($fees_breakdown as $fee):
            if($fee['status'] == 'Pending') {
                $pending_count++;
        ?>
            <div style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; background: #fef2f2; border-radius: 0.5rem; border-left: 4px solid var(--danger);">
                <div style="min-width: 40px; font-size: 1.5rem;">⚠️</div>
                <div style="flex: 1;">
                    <p style="margin: 0; font-weight: 600;"><?php echo htmlspecialchars($fee['fee_type']); ?></p>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Due for payment</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-weight: 900; color: var(--danger);">$<?php echo number_format($fee['amount'], 2); ?></p>
                    <p style="margin: 0.25rem 0 0; font-size: 0.8rem; color: var(--text-muted);">
                        <a href="payment_gateway.php" style="color: var(--primary); text-decoration: none;">Pay Now →</a>
                    </p>
                </div>
            </div>
        <?php } endforeach;
        
        if($pending_count === 0) {
            echo '<div style="text-align: center; padding: 2rem; background: #f0fdf4; border-radius: 0.5rem;"><p style="color: var(--success); margin: 0;">✓ All fees paid! Financial clearance complete.</p></div>';
        }
        ?>
    </div>
</div>

<!-- Fee Payment Schedule -->
<div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 2rem; border-radius: 1rem; border-left: 5px solid var(--primary);">
    <h2 style="margin-bottom: 1.5rem; color: #075985;">Recommended Payment Plan</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
        <div style="background: white; padding: 1rem; border-radius: 0.5rem; text-align: center;">
            <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Month 1</p>
            <p style="margin: 0.5rem 0 0; font-size: 1.25rem; font-weight: 900; color: var(--primary);">
                $<?php 
                $pending_total = 0;
                foreach($fees_breakdown as $fee) {
                    if($fee['status'] == 'Pending') $pending_total += $fee['amount'];
                }
                echo number_format($pending_total > 0 ? $pending_total / 3 : 0, 2); 
                ?>
            </p>
        </div>
        
        <div style="background: white; padding: 1rem; border-radius: 0.5rem; text-align: center;">
            <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Month 2</p>
            <p style="margin: 0.5rem 0 0; font-size: 1.25rem; font-weight: 900; color: var(--primary);">
                $<?php echo number_format($pending_total > 0 ? $pending_total / 3 : 0, 2); ?>
            </p>
        </div>
        
        <div style="background: white; padding: 1rem; border-radius: 0.5rem; text-align: center;">
            <p style="margin: 0; color: var(--text-muted); font-size: 0.85rem;">Month 3</p>
            <p style="margin: 0.5rem 0 0; font-size: 1.25rem; font-weight: 900; color: var(--primary);">
                $<?php echo number_format($pending_total > 0 ? $pending_total / 3 : 0, 2); ?>
            </p>
        </div>
    </div>
    
    <button class="btn primary-btn" style="width: 100%; margin-top: 1.5rem; background: var(--primary);">Set Up 3-Month Payment Plan</button>
</div>

<?php include 'footer.php'; ?>
