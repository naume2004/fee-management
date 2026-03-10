<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Fetch pending fees for calculating assistance needed
$pending_sql = "SELECT SUM(amount) as total FROM fees WHERE student_id = '$student_id' AND status = 'Pending'";
$pending_result = mysqli_query($conn, $pending_sql);
$pending = mysqli_fetch_assoc($pending_result);
$total_pending = $pending['total'] ?? 0;

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Financial Aid & Support</h1>
    <p style="color: var(--text-muted);">Explore all available financial assistance options to help with your school fees.</p>
</div>

<div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 3rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem;">
        <div style="text-align: center;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">💰</div>
            <h3 style="margin: 0 0 0.5rem;">Current Balance</h3>
            <p style="font-size: 1.5rem; color: var(--danger); font-weight: 900; margin: 0;">UGX <?php echo number_format($total_pending * 3800, 0); ?></p>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
            <h3 style="margin: 0 0 0.5rem;">Financial Status</h3>
            <p style="font-size: 1.1rem; margin: 0;"><span class="badge badge-danger">Outstanding</span></p>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎯</div>
            <h3 style="margin: 0 0 0.5rem;">Eligibility</h3>
            <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;">Check available aid options</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin-bottom: 3rem;">
    <!-- Financial Aid Types -->
    <div>
        <h2 style="margin-bottom: 2rem;">Types of Financial Aid</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 1.5rem; border-left: 5px solid var(--success);">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                <div style="font-size: 1.5rem;">🎓</div>
                <div>
                    <h3 style="margin: 0;">Scholarships</h3>
                    <p style="margin: 0.5rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Merit-based and need-based awards</p>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <p style="margin: 0;">Available scholarships covering tuition, books, and living expenses for eligible students.</p>
            </div>
            <button class="btn primary-btn" style="width: 100%;">Explore Scholarships</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 1.5rem; border-left: 5px solid #f59e0b;">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                <div style="font-size: 1.5rem;">💳</div>
                <div>
                    <h3 style="margin: 0;">Student Loans</h3>
                    <p style="margin: 0.5rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Low-interest financing options</p>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <p style="margin: 0;">Borrow up to 100% of your fees with flexible repayment terms starting after graduation.</p>
            </div>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">View Loan Options</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 5px solid var(--primary);">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1rem;">
                <div style="font-size: 1.5rem;">🎁</div>
                <div>
                    <h3 style="margin: 0;">Grants & Bursaries</h3>
                    <p style="margin: 0.5rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Free money you don't repay</p>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <p style="margin: 0;">Direct government and institutional grants for students in financial need.</p>
            </div>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Check Eligibility</button>
        </div>
    </div>

    <!-- Application Status -->
    <div>
        <h2 style="margin-bottom: 2rem;">Your Aid Applications</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 1.5rem;">Pending Applications</h3>
            
            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <p style="font-weight: 600; margin: 0;">Merit Scholarship 2024</p>
                        <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">Applied: December 10, 2024</p>
                    </div>
                    <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">Review In Progress</span>
                </div>
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; font-size: 0.85rem;">
                    <p style="margin: 0; color: var(--text-muted);">Your application is being reviewed. Expected decision by January 31, 2025.</p>
                </div>
            </div>

            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <p style="font-weight: 600; margin: 0;">Need-Based Grant</p>
                        <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.25rem 0 0;">Applied: November 15, 2024</p>
                    </div>
                    <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">Approved</span>
                </div>
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; font-size: 0.85rem;">
                    <p style="margin: 0; color: var(--text-muted);"><strong>Award Amount:</strong> UGX <?php echo number_format(5000 * 3800, 0); ?></p>
                    <p style="margin: 0.5rem 0 0; color: var(--text-muted);"><strong>Status:</strong> Funds will be applied to next semester fees.</p>
                </div>
            </div>
        </div>

        <h2 style="margin-bottom: 1.5rem;">Recommended for You</h2>
        
        <div style="background: #def3ff; border-radius: 1rem; padding: 1.5rem;">
            <p style="font-weight: 600; color: #075985; margin: 0;">Based on your current balance of UGX <?php echo number_format($total_pending * 3800, 0); ?>, you may qualify for:</p>
            <ul style="list-style: none; margin: 1rem 0 0; padding: 0;">
                <li style="padding: 0.5rem 0; color: var(--text-muted); font-size: 0.9rem;">✓ Emergency Hardship Grant</li>
                <li style="padding: 0.5rem 0; color: var(--text-muted); font-size: 0.9rem;">✓ Extended Payment Plans</li>
                <li style="padding: 0.5rem 0; color: var(--text-muted); font-size: 0.9rem;">✓ Work-Study Program</li>
            </ul>
            <button class="btn primary-btn" style="width: 100%; margin-top: 1rem;">Learn More</button>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div style="background: white; padding: 3rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    <h2 style="margin-bottom: 2rem; text-align: center;">Frequently Asked Questions</h2>
    
    <div style="display: grid; gap: 1.5rem;">
        <details style="cursor: pointer;">
            <summary style="font-weight: 600; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; user-select: none;">When can I apply for scholarships?</summary>
            <div style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                Scholarships are typically available year-round, but specific programs have different deadlines. Merit scholarships usually have deadlines in December-January, while need-based grants may be available throughout the year. Check the Scholarships page for current deadlines.
            </div>
        </details>

        <details style="cursor: pointer;">
            <summary style="font-weight: 600; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; user-select: none;">How long does it take to get approved for financial aid?</summary>
            <div style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                Processing times vary: Merit scholarships (4-6 weeks), Need-based grants (2-4 weeks), Student loans (1-2 weeks). You can track your application status in the "Your Aid Applications" section above.
            </div>
        </details>

        <details style="cursor: pointer;">
            <summary style="font-weight: 600; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; user-select: none;">Can I apply for multiple types of aid?</summary>
            <div style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                Yes! You can apply for scholarships, grants, and loans simultaneously. However, your total aid cannot exceed 100% of your educational costs. Our system automatically ensures you don't receive more than needed.
            </div>
        </details>

        <details style="cursor: pointer;">
            <summary style="font-weight: 600; padding: 1rem; background: #f8fafc; border-radius: 0.5rem; user-select: none;">What documents do I need for a financial aid application?</summary>
            <div style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                Typical documents include: Valid ID, Income verification (tax returns or pay stubs), Financial statements, Academic transcripts, and supporting letters for need-based aid. Specific requirements vary by program.
            </div>
        </details>
    </div>
</div>

<?php include 'footer.php'; ?>
