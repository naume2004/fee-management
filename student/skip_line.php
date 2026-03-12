<?php
include 'db.php';
include 'header.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

$summary_sql = "SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as total_pending
    FROM fees WHERE student_id = '$student_id'";
$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);
?>

<style>
    .skip-hero {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        padding: 4rem 2rem;
        border-radius: 1.5rem;
        color: white;
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }
    .skip-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(30deg);
    }
    .skip-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
        position: relative;
    }
    .skip-hero p {
        font-size: 1.25rem;
        opacity: 0.95;
        max-width: 700px;
        margin: 0 auto;
        position: relative;
    }
    .benefit-card {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .benefit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
    }
    .benefit-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1.5rem;
    }
    .payment-option {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .payment-option:hover {
        border-color: #10b981;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
    .payment-option.selected {
        border-color: #10b981;
        background: #ecfdf5;
    }
    .payment-option-icon {
        width: 60px;
        height: 60px;
        background: #f3f4f6;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    @media (max-width: 768px) {
        .skip-hero h1 { font-size: 2rem; }
    }
</style>

<div class="skip-hero">
    <h1>⏭️ Skip the Line - Pay Online</h1>
    <p>Don't waste time waiting in queues! Pay your school fees from anywhere, anytime using our secure digital payment options.</p>
    <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <span class="stat-pill">✅ Available 24/7</span>
        <span class="stat-pill">🔒 Secure Payments</span>
        <span class="stat-pill">⚡ Instant Confirmation</span>
    </div>
</div>

<?php if (($summary['total_pending'] ?? 0) > 0): ?>
<div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="margin: 0; color: #92400e;">💰 Outstanding Balance</h3>
            <p style="margin: 0.5rem 0 0; color: #78350f;">You have <strong>UGX <?php echo number_format(($summary['total_pending'] ?? 0) * 3800, 0); ?></strong> in pending fees</p>
        </div>
        <a href="skip_pay.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" class="btn primary-btn" style="background: #10b981; white-space: nowrap;">
            Pay Now ⏭️
        </a>
    </div>
</div>
<?php endif; ?>

<h2 style="margin-bottom: 2rem;">Why Pay Online?</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 4rem;">
    <div class="benefit-card">
        <div class="benefit-icon">⏱️</div>
        <h3>Save Time</h3>
        <p style="color: var(--text-muted);">No more waiting in long queues. Complete your payment in minutes from your phone or computer.</p>
    </div>
    <div class="benefit-card">
        <div class="benefit-icon">🏠</div>
        <h3>Pay Anywhere</h3>
        <p style="color: var(--text-muted);">Pay from home, office, or on the go. Our platform is accessible 24/7 from any device.</p>
    </div>
    <div class="benefit-card">
        <div class="benefit-icon">📱</div>
        <h3>Multiple Options</h3>
        <p style="color: var(--text-muted);">Choose from Mobile Money, Cards, Bank Transfer, USSD, QR Code, or set up automatic payments.</p>
    </div>
    <div class="benefit-card">
        <div class="benefit-icon">🧾</div>
        <h3>Instant Receipts</h3>
        <p style="color: var(--text-muted);">Get immediate payment confirmation and digital receipts sent to your email.</p>
    </div>
    <div class="benefit-card">
        <div class="benefit-icon">🔔</div>
        <h3>Stay Updated</h3>
        <p style="color: var(--text-muted);">Receive SMS and email notifications about your payment status and upcoming deadlines.</p>
    </div>
    <div class="benefit-card">
        <div class="benefit-icon">📊</div>
        <h3>Track History</h3>
        <p style="color: var(--text-muted);">View your complete payment history and download statements anytime.</p>
    </div>
</div>

<h2 style="margin-bottom: 2rem;">Payment Methods Available</h2>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 4rem;">
    <div class="payment-option" onclick="window.location.href='skip_pay.php?sim_id=<?php echo $student_id; ?>&method=mobile'">
        <div class="payment-option-icon">📱</div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem;">Mobile Money</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">MTN, Airtel Money - Instant deduction</p>
        </div>
    </div>
    
    <div class="payment-option" onclick="window.location.href='skip_pay.php?sim_id=<?php echo $student_id; ?>&method=card'">
        <div class="payment-option-icon">💳</div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem;">Credit/Debit Card</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Visa, Mastercard - Secure online</p>
        </div>
    </div>
    
    <div class="payment-option" onclick="window.location.href='skip_pay.php?sim_id=<?php echo $student_id; ?>&method=ussd'">
        <div class="payment-option-icon">☎️</div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem;">USSD Payment</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Dial *XXX# - Works on basic phones</p>
        </div>
    </div>
    
    <div class="payment-option" onclick="window.location.href='skip_pay.php?sim_id=<?php echo $student_id; ?>&method=qr'">
        <div class="payment-option-icon">📲</div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem;">QR Code Payment</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Scan & Pay - Quick & contactless</p>
        </div>
    </div>
    
    <div class="payment-option" onclick="window.location.href='skip_pay.php?sim_id=<?php echo $student_id; ?>&method=bank'">
        <div class="payment-option-icon">🏦</div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem;">Bank Transfer</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Direct account transfer</p>
        </div>
    </div>
    
    <div class="payment-option" onclick="window.location.href='skip_pay.php?sim_id=<?php echo $student_id; ?>&method=autopay'">
        <div class="payment-option-icon">🔄</div>
        <div>
            <h3 style="margin: 0; font-size: 1.1rem;">Auto-Pay (Standing Order)</h3>
            <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Set & forget - Automatic monthly</p>
        </div>
    </div>
</div>

<div style="background: #f0fdf4; border-radius: 1rem; padding: 2rem; margin-bottom: 3rem; border-left: 4px solid #10b981;">
    <h3 style="color: #065f46; margin-top: 0;">🎯 Prefer to Pay in Person?</h3>
    <p style="color: #065f46; margin-bottom: 1.5rem;">Skip the unpredictable waiting! Book an appointment online and get a specific time slot.</p>
    <a href="book_appointment.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" class="btn" style="background: #065f46; color: white;">
        📅 Book Appointment
    </a>
</div>

<div style="text-align: center; padding: 3rem; background: white; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 1rem;">Need Help?</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">Our support team is available to assist you with any payment questions.</p>
    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <a href="#" class="btn secondary-btn">💬 WhatsApp Support</a>
        <a href="#" class="btn secondary-btn">📧 Email Support</a>
        <a href="#" class="btn secondary-btn">📞 Call: +256 XXX XXXX</a>
    </div>
</div>

<?php include 'footer.php'; ?>
