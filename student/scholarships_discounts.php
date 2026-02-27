<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Scholarships & Discounts</h1>
    <p style="color: var(--text-muted);">Apply for scholarships and use discount codes to reduce your school fees.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
    <!-- Available Scholarships -->
    <div>
        <h2 style="margin-bottom: 2rem;">Available Scholarships</h2>
        
        <div class="scholarship-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-top: 4px solid var(--success);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">Merit Scholarship</h3>
                <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">50% OFF</span>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0;">
                For students with excellent academic performance (GPA 3.5+)
            </p>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; font-size: 0.85rem; color: var(--text-muted);">
                <p style="margin: 0.25rem 0;"><strong>Benefit:</strong> 50% discount on tuition fees</p>
                <p style="margin: 0.25rem 0;"><strong>Requirements:</strong> GPA ≥ 3.5</p>
                <p style="margin: 0.25rem 0;"><strong>Deadline:</strong> December 31, 2024</p>
            </div>
            <button class="btn primary-btn" style="width: 100%;">Apply Now</button>
        </div>

        <div class="scholarship-card" style="background: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-top: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">Need-Based Grant</h3>
                <span style="background: #fef9c3; color: #854d0e; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">UP TO 75%</span>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0;">
                For students with demonstrated financial need
            </p>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; font-size: 0.85rem; color: var(--text-muted);">
                <p style="margin: 0.25rem 0;"><strong>Benefit:</strong> Up to 75% tuition discount</p>
                <p style="margin: 0.25rem 0;"><strong>Requirements:</strong> Financial assessment</p>
                <p style="margin: 0.25rem 0;"><strong>Deadline:</strong> March 31, 2025</p>
            </div>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Learn More</button>
        </div>

        <div class="scholarship-card" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-top: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0;">Excellence Award</h3>
                <span style="background: #dbeafe; color: #075985; padding: 0.25rem 0.75rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 600;">$2,000</span>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0.5rem 0;">
                For outstanding achievements in sports, arts, or community service
            </p>
            <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin: 1rem 0; font-size: 0.85rem; color: var(--text-muted);">
                <p style="margin: 0.25rem 0;"><strong>Benefit:</strong> $2,000 award (can be applied to fees)</p>
                <p style="margin: 0.25rem 0;"><strong>Requirements:</strong> Portfolio submission</p>
                <p style="margin: 0.25rem 0;"><strong>Deadline:</strong> January 15, 2025</p>
            </div>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Apply Now</button>
        </div>
    </div>

    <!-- Discount Codes -->
    <div>
        <h2 style="margin-bottom: 2rem;">Discount Codes</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Apply a Discount Code</h3>
            
            <div class="form-group">
                <label>Discount Code</label>
                <input type="text" placeholder="Enter your discount code" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: 0.5rem; margin-bottom: 1rem;">
            </div>
            
            <button class="btn primary-btn" style="width: 100%;">Apply Discount</button>
        </div>

        <h2 style="margin-bottom: 1.5rem;">Active Discounts</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid var(--border); border-radius: 0.5rem; margin-bottom: 1rem;">
                <div>
                    <p style="margin: 0; font-weight: 600;">EARLYBIRD15</p>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">15% off tuition fees</p>
                </div>
                <span class="badge badge-success">Valid</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; border: 1px solid var(--border); border-radius: 0.5rem;">
                <div>
                    <p style="margin: 0; font-weight: 600;">SEMESTER20</p>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">20% off library and student fees</p>
                </div>
                <span class="badge badge-success">Valid</span>
            </div>
        </div>

        <h2 style="margin-bottom: 1.5rem;">Your Scholarships</h2>
        
        <div style="background: #f0fdf4; border-radius: 1rem; padding: 2rem; text-align: center;">
            <p style="color: var(--text-muted); margin: 0;">
                No active scholarships or awards currently applied.
            </p>
            <a href="#apply" style="color: var(--success); text-decoration: underline; margin-top: 0.5rem; display: inline-block;">Apply for a scholarship today →</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
