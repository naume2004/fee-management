<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Fetch pending fees
$pending_sql = "SELECT SUM(amount) as total FROM fees WHERE student_id = '$student_id' AND status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending = mysqli_fetch_assoc($pending_result);
$total_pending = $pending['total'] ?? 0;

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Flexible Payment Plans</h1>
    <p style="color: var(--text-muted);">Manage your school fees with customizable installment options.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
    <!-- Available Plans -->
    <div>
        <h2 style="margin-bottom: 2rem;">Available Plans</h2>
        
        <div class="plan-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <h3 style="margin: 0;">Standard Plan</h3>
                <span class="badge badge-success">Popular</span>
            </div>
            <p style="color: var(--text-muted); margin: 0.5rem 0; font-size: 0.9rem;">Split payments over 3 months</p>
            <div style="margin: 1.5rem 0;">
                <p style="color: var(--text-muted); font-size: 0.85rem;">Monthly Payment:</p>
                <p style="font-size: 1.5rem; color: var(--primary); font-weight: 900;">UGX <?php echo number_format(($total_pending * 3800)/3, 0); ?></p>
            </div>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
                    <strong>Payment Schedule:</strong><br>
                    Month 1: UGX <?php echo number_format(($total_pending * 3800)/3, 0); ?><br>
                    Month 2: UGX <?php echo number_format(($total_pending * 3800)/3, 0); ?><br>
                    Month 3: UGX <?php echo number_format(($total_pending * 3800)/3, 0); ?>
                </p>
            </div>
            <button class="btn primary-btn" style="width: 100%;">Select Plan</button>
        </div>

        <div class="plan-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid #f59e0b;">
            <h3 style="margin: 0 0 0.5rem;">Extended Plan</h3>
            <p style="color: var(--text-muted); margin: 0.5rem 0; font-size: 0.9rem;">Split payments over 6 months</p>
            <div style="margin: 1.5rem 0;">
                <p style="color: var(--text-muted); font-size: 0.85rem;">Monthly Payment:</p>
                <p style="font-size: 1.5rem; color: var(--warning); font-weight: 900;">UGX <?php echo number_format(($total_pending * 3800)/6, 0); ?></p>
            </div>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
                    <strong>Payment Schedule:</strong><br>
                    6 equal monthly installments
                </p>
            </div>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Select Plan</button>
        </div>

        <div class="plan-card" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid var(--success);">
            <h3 style="margin: 0 0 0.5rem;">Quick Pay</h3>
            <p style="color: var(--text-muted); margin: 0.5rem 0; font-size: 0.9rem;">2-month express payment</p>
            <div style="margin: 1.5rem 0;">
                <p style="color: var(--text-muted); font-size: 0.85rem;">Monthly Payment:</p>
                <p style="font-size: 1.5rem; color: var(--success); font-weight: 900;">UGX <?php echo number_format(($total_pending * 3800)/2, 0); ?></p>
            </div>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">
                    <strong>Payment Schedule:</strong><br>
                    Month 1: UGX <?php echo number_format(($total_pending * 3800)/2, 0); ?><br>
                    Month 2: UGX <?php echo number_format(($total_pending * 3800)/2, 0); ?>
                </p>
            </div>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Select Plan</button>
        </div>
    </div>

    <!-- Active Plans & History -->
    <div>
        <h2 style="margin-bottom: 2rem;">Your Active Plans</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <p style="color: var(--text-muted); text-align: center; padding: 2rem; margin: 0;">
                No active payment plans.<br>
                <a href="#select-plan" style="color: var(--primary); text-decoration: underline;">Create one today</a>
            </p>
        </div>

        <h2 style="margin-bottom: 1.5rem; margin-top: 3rem;">Plan History</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="margin: 0;">Standard Plan - 2024</h4>
                    <span class="badge badge-success">Completed</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0;">
                    <strong>Duration:</strong> January - March 2024<br>
                    <strong>Monthly Amount:</strong> UGX <?php echo number_format(400 * 3800, 0); ?><br>
                    <strong>Total Paid:</strong> UGX <?php echo number_format(1200 * 3800, 0); ?>
                </p>
            </div>

            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h4 style="margin: 0;">Extended Plan - 2023</h4>
                    <span class="badge badge-warning">Completed</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0;">
                    <strong>Duration:</strong> July - December 2023<br>
                    <strong>Monthly Amount:</strong> UGX <?php echo number_format(200 * 3800, 0); ?><br>
                    <strong>Total Paid:</strong> UGX <?php echo number_format(1200 * 3800, 0); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
