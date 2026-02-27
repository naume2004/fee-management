<?php
include '../db.php';

$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

// Fetch all fees for receipts
$fees_sql = "SELECT * FROM fees WHERE student_id = '$student_id' ORDER BY created_at DESC";
$fees_result = mysqli_query($conn, $fees_sql);

include 'header.php';
?>

<div class="payment-header" style="text-align: center; margin-bottom: 3rem;">
    <h1>Documents & Receipts</h1>
    <p style="color: var(--text-muted);">Download and manage your financial documents, receipts, and certificates.</p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem;">
    <div>
        <h2 style="margin-bottom: 2rem;">Payment Receipts</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 0;
                    while($fee = mysqli_fetch_assoc($fees_result)): 
                        $count++;
                        if ($count > 15) break;
                    ?>
                        <tr>
                            <td style="font-size: 0.9rem;"><?php echo date('M j, Y', strtotime($fee['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($fee['fee_type']); ?></td>
                            <td><strong>$<?php echo number_format($fee['amount'], 2); ?></strong></td>
                            <td>
                                <span class="badge <?php echo $fee['status'] == 'Paid' ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $fee['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($fee['status'] == 'Paid'): ?>
                                    <div style="display: flex; gap: 0.5rem; font-size: 0.8rem;">
                                        <button class="btn" style="padding: 0.25rem 0.5rem; background: var(--primary); color: white; border: none; border-radius: 0.25rem; cursor: pointer;" onclick="alert('Downloading receipt for <?php echo htmlspecialchars($fee['fee_type']); ?>')">📥 PDF</button>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <aside>
        <h2 style="margin-bottom: 2rem;">Quick Actions</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
            <button class="btn primary-btn" style="width: 100%; margin-bottom: 0.75rem;">📄 Download All PDFs</button>
            <button class="btn secondary-btn" style="width: 100%; margin-bottom: 0.75rem; color: var(--text-main); border: 1px solid var(--border);">📊 Export as Excel</button>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">📧 Email Receipts</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3 style="margin-bottom: 1rem;">Other Documents</h3>
            
            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem; cursor: pointer;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                <p style="margin: 0; font-weight: 600; font-size: 0.9rem;">📜 Financial Statement</p>
                <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.8rem;">2024-2025 Academic Year</p>
            </div>

            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; margin-bottom: 0.75rem; cursor: pointer;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                <p style="margin: 0; font-weight: 600; font-size: 0.9rem;">🎓 Enrollment Certificate</p>
                <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.8rem;">Proof of Enrollment</p>
            </div>

            <div style="border: 1px solid var(--border); border-radius: 0.5rem; padding: 1rem; cursor: pointer;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='white'">
                <p style="margin: 0; font-weight: 600; font-size: 0.9rem;">✓ Fee Clearance Letter</p>
                <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.8rem;">After full payment</p>
            </div>
        </div>
    </aside>
</div>

<!-- Financial Letters Section -->
<div style="margin-top: 3rem;">
    <h2 style="margin-bottom: 2rem;">Official Letters & Certificates</h2>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="font-size: 1.5rem;">📋</div>
                <div>
                    <h3 style="margin: 0;">Financial Aid Award Letter</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Official notification of approved aid</p>
                </div>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Contains details of your approved scholarships, grants, and loans for the academic year.</p>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Download Letter</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="font-size: 1.5rem;">📝</div>
                <div>
                    <h3 style="margin: 0;">Transcript Request</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Order official academic transcripts</p>
                </div>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Request official transcripts to be sent to employers, lenders, or other institutions.</p>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Request Transcript</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="font-size: 1.5rem;">✓</div>
                <div>
                    <h3 style="margin: 0;">Good Academic Standing</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">Verify your academic status</p>
                </div>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">Certificate verifying you are in good academic standing with the university.</p>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Download Certificate</button>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="font-size: 1.5rem;">📜</div>
                <div>
                    <h3 style="margin: 0;">Tax Form (1098-T)</h3>
                    <p style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.85rem;">For US tax purposes</p>
                </div>
            </div>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">IRS Form 1098-T for claiming education tax credits. Available after January 31st.</p>
            <button class="btn secondary-btn" style="width: 100%; color: var(--text-main); border: 1px solid var(--border);">Download Form</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
