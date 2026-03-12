<?php
include '../db.php';
include 'header.php';

// Support simulation of various students via GET or default to S101
$student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';

// Fetch student details
$student_sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    echo "<div class='container' style='padding: 5rem; text-align: center;'><h2>Student not found.</h2><a href='../index.html'>Go Home</a></div>";
    include 'footer.php';
    exit();
}

// Fetch fee summary for Data Payment Dashboard
$summary_sql = "SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) as total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) as total_pending
    FROM fees WHERE student_id = '$student_id'";
$summary_result = mysqli_query($conn, $summary_sql);
$summary = mysqli_fetch_assoc($summary_result);

// Fetch all fees for history
$fees_sql = "SELECT * FROM fees WHERE student_id = '$student_id' ORDER BY created_at DESC";
$fees_result = mysqli_query($conn, $fees_sql);

// Fetch fee breakdown by type
$breakdown_sql = "SELECT fee_type, amount, status FROM fees WHERE student_id = '$student_id'";
$breakdown_result = mysqli_query($conn, $breakdown_sql);

// Fetch overdue payments
$overdue_sql = "SELECT COUNT(f.id) as overdue_count, SUM(f.amount) as overdue_amount
    FROM fees f
    LEFT JOIN payment_deadlines pd ON f.id = pd.fee_id
    WHERE f.student_id = '$student_id' AND f.status = 'Pending'
    AND pd.due_date < CURDATE()";
$overdue_result = mysqli_query($conn, $overdue_sql);
$overdue = mysqli_fetch_assoc($overdue_result);
?>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-firestore.js"></script>
<script src="../firebase-config.js"></script>

<div id="notification-toast" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; background: white; padding: 1rem 1.5rem; border-radius: 0.5rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-left: 4px solid var(--primary); animation: slideIn 0.3s ease-out;">
    <div style="display: flex; gap: 1rem; align-items: center;">
        <span style="font-size: 1.5rem;">🔔</span>
        <div>
            <p id="notif-title" style="margin: 0; font-weight: 700; font-size: 0.9rem;"></p>
            <p id="notif-body" style="margin: 0; font-size: 0.8rem; color: var(--text-muted);"></p>
        </div>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: #f0fdf4; border-left: 5px solid var(--success); padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; color: #166534; font-weight: 600;">
        ✅ <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>

<?php if (($overdue['overdue_count'] ?? 0) > 0): ?>
    <div style="background: #fee2e2; border-left: 5px solid var(--danger); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h3 style="margin: 0 0 0.5rem; color: var(--danger);">⚠️ Payment Reminder</h3>
                <p style="margin: 0; color: #7f1d1d; font-size: 0.95rem;">You have <strong><?php echo $overdue['overdue_count']; ?> overdue payment(s)</strong> totaling <strong>UGX <?php echo number_format($overdue['overdue_amount'] * 3800, 0); ?></strong></p>
                <p style="margin: 0.5rem 0 0; color: #7f1d1d; font-size: 0.85rem;">Please pay as soon as possible to avoid late fees and academic holds.</p>
            </div>
            <a href="payment_reminders.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" class="btn primary-btn" style="padding: 0.5rem 1rem; font-size: 0.9rem; white-space: nowrap;">View Details</a>
        </div>
    </div>
<?php endif; ?>

<div class="welcome-section" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: center;">
    <div style="display: flex; gap: 1.5rem; align-items: center;">
        <div style="width: 64px; height: 64px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800;">
            <?php echo substr($student['name'], 0, 1); ?>
        </div>
        <div>
            <h1 style="margin: 0;">Welcome, <?php echo explode(' ', htmlspecialchars($student['name']))[0]; ?>!</h1>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0;">ID: <strong><?php echo htmlspecialchars($student['student_id']); ?></strong> | <?php echo htmlspecialchars($student['class_name']); ?></p>
        </div>
    </div>
    <div style="text-align: right; display: flex; gap: 0.5rem;">
        <button onclick="toggleProfileModal()" class="btn secondary-btn" style="color: var(--primary); border: 1px solid var(--primary);">👤 My Profile</button>
        <span class="badge badge-success" style="padding: 0.5rem 1rem;"><?php echo htmlspecialchars($student['course']); ?></span>
    </div>
</div>

<!-- Quick Access Menu -->
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; border-radius: 1rem; margin-bottom: 3rem; color: white;">
    <h2 style="margin-top: 0; margin-bottom: 1.5rem; color: white;">Quick Access</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <!-- Skip the Line -->
        <a href="skip_line.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(16, 185, 129, 0.3); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">⏭️</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Skip the Line</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Pay online now</p>
        </a>
        
        <!-- Pay Online (Legacy) -->
        <a href="payment_gateway.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">💳</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Payment Gateway</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Make payments now</p>
        </a>
        
        <!-- Installments -->
        <a href="payment_plans.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📅</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Installments</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">View payment plans</p>
        </a>
        
        <!-- Payment History -->
        <a href="payment_history.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📊</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">History</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Payment records</p>
        </a>
        
        <!-- Book Appointment -->
        <a href="book_appointment.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(59, 130, 246, 0.3); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📅</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Book Appointment</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">Schedule a visit</p>
        </a>
        
        <!-- Dashboard Alerts -->
        <a href="payment_reminders.php?sim_id=<?php echo htmlspecialchars($student_id); ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem; color: white; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(255,255,255,0.3);">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🔔</div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600;">Dashboard Alerts</h3>
            <p style="margin: 0.5rem 0 0; font-size: 0.85rem; opacity: 0.9;">See payment status and reminders instantly when you log in.</p>
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Paid</h3>
        <div class="value" style="color: var(--success);">UGX <?php echo number_format(($summary['total_paid'] ?? 0) * 3800, 0); ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Completed transactions</p>
    </div>
    <div class="stat-card">
        <h3>Total Pending</h3>
        <div class="value" style="color: var(--danger);">UGX <?php echo number_format(($summary['total_pending'] ?? 0) * 3800, 0); ?></div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">Outstanding balance</p>
    </div>
    <div class="stat-card">
        <h3>Financial Clearance</h3>
        <div class="value" style="font-size: 1.5rem;">
            <?php 
                $total = ($summary['total_paid'] ?? 0) + ($summary['total_pending'] ?? 0);
                echo ($total > 0 && ($summary['total_pending'] ?? 0) <= 0) ? '✅ CLEARED' : '❌ PENDING';
            ?>
        </div>
        <p style="margin-top: 0.5rem; color: var(--text-muted); font-size: 0.85rem;">School status</p>
    </div>
</div>

<div class="dashboard-sections" style="display: grid; grid-template-columns: 1fr 2fr; gap: 3rem; margin-top: 2rem;">
    <aside class="financial-breakdown">
        <div class="table-section" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h2 style="font-size: 1.25rem; margin-bottom: 1.5rem;">Fee Breakdown</h2>
            <ul style="list-style: none;">
                <?php while($item = mysqli_fetch_assoc($breakdown_result)): ?>
                    <li style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                        <div>
                            <p style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($item['fee_type']); ?></p>
                            <span class="badge <?php echo $item['status'] == 'Paid' ? 'badge-success' : 'badge-danger'; ?>" style="font-size: 0.65rem;">
                                <?php echo $item['status']; ?>
                            </span>
                        </div>
                        <p style="font-weight: 700;">UGX <?php echo number_format($item['amount'] * 3800, 0); ?></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
        
        <div style="margin-top: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 1px dashed var(--border);">
            <h3 style="font-size: 1rem; margin-bottom: 1rem;">Recent Notifications</h3>
            <div id="notif-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                <p style="font-size: 0.85rem; color: var(--text-muted); text-align: center;">Loading notifications...</p>
            </div>
        </div>
    </aside>

    <div class="payment-history">
        <div class="admin-dashboard-header" style="margin-bottom: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; align-items: center;">
            <button class="btn" onclick="window.print()" style="background: white; border: 1px solid var(--border); color: var(--text-main); font-size: 0.8rem; padding: 0.4rem 0.75rem; display: flex; align-items: center; gap: 0.4rem;">🖨️ Print Statements</button>
            <a href="payment_gateway.php?sim_id=<?php echo $student_id; ?>" class="btn" style="background: var(--primary); color: white; font-size: 0.8rem; padding: 0.4rem 0.75rem; text-decoration: none; display: flex; align-items: center; gap: 0.4rem;">💳 Make Payment</a>
        </div>

        <div class="table-section" style="background: white; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <tr>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Fee Item</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Amount</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Status</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Date</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Method</th>
                        <th style="padding: 0.75rem 1rem; text-align: left; color: #64748b; font-weight: 600; font-size: 0.85rem;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($fees_result, 0); 
                    if (mysqli_num_rows($fees_result) > 0): 
                        while($fee = mysqli_fetch_assoc($fees_result)): 
                    ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($fee['fee_type']); ?></td>
                            <td style="padding: 1rem; font-weight: 500;">UGX <?php echo number_format($fee['amount'] * 3800, 0); ?></td>
                            <td style="padding: 1rem;">
                                <span class="badge <?php echo $fee['status'] == 'Paid' ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $fee['status']; ?>
                                </span>
                            </td>
                            <td style="padding: 1rem; color: #64748b; font-size: 0.85rem;"><?php echo $fee['payment_date'] ? date('M d, Y', strtotime($fee['payment_date'])) : '-'; ?></td>
                            <td style="padding: 1rem; color: #64748b; font-size: 0.85rem;"><?php echo htmlspecialchars($fee['payment_method'] ?? '-'); ?></td>
                            <td style="padding: 1rem;">
                                <?php if($fee['status'] != 'Paid'): ?>
                                    <a href="payment_gateway.php?fee_id=<?php echo $fee['id']; ?>" class="btn" style="background: #10b981; color: white; padding: 0.4rem 0.8rem; font-size: 0.75rem; border-radius: 0.4rem; text-decoration: none;">Pay UGX <?php echo number_format($fee['amount'] * 3800, 0); ?></a>
                                <?php else: ?>
                                    <span style="color: #10b981; font-weight: 600; font-size: 0.75rem;">✓ Paid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div id="profile-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; backdrop-filter: blur(4px);">
    <div style="background: white; max-width: 500px; margin: 5% auto; border-radius: 1rem; padding: 2.5rem; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
        <button onclick="toggleProfileModal()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">×</button>
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 100px; height: 100px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 1rem;">
                <?php echo substr($student['name'], 0, 1); ?>
            </div>
            <h2 style="margin: 0;"><?php echo htmlspecialchars($student['name']); ?></h2>
            <p style="color: var(--text-muted);"><?php echo htmlspecialchars($student['student_id']); ?></p>
        </div>
        
        <div style="display: grid; gap: 1rem;">
            <div style="padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid var(--border);">
                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Email Address</p>
                <p style="margin: 0.25rem 0 0; font-weight: 600;"><?php echo htmlspecialchars($student['email']); ?></p>
            </div>
            <div style="padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid var(--border);">
                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Class / Grade</p>
                <p style="margin: 0.25rem 0 0; font-weight: 600;"><?php echo htmlspecialchars($student['class_name']); ?></p>
            </div>
            <div style="padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid var(--border);">
                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Gender</p>
                <p style="margin: 0.25rem 0 0; font-weight: 600;"><?php echo htmlspecialchars($student['gender']); ?></p>
            </div>
            <div style="padding: 1rem; background: #f8fafc; border-radius: 0.5rem; border: 1px solid var(--border);">
                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Academic Year</p>
                <p style="margin: 0.25rem 0 0; font-weight: 600;"><?php echo htmlspecialchars($student['academic_year']); ?></p>
            </div>
        </div>
        
        <button onclick="alert('Profile editing is currently disabled. Please contact the administration office.')" class="btn primary-btn" style="width: 100%; margin-top: 2rem;">Edit Profile Details</button>
    </div>
</div>

<script>
    function toggleProfileModal() {
        const modal = document.getElementById('profile-modal');
        modal.style.display = modal.style.display === 'none' ? 'block' : 'none';
    }

    // Listen for real-time notifications from Firestore
    if (typeof db !== 'undefined') {
        const studentId = '<?php echo $student_id; ?>';
        
        db.collection('notifications')
            .where('student_id', '==', studentId)
            .orderBy('timestamp', 'desc')
            .limit(5)
            .onSnapshot((snapshot) => {
                const notifList = document.getElementById('notif-list');
                const toast = document.getElementById('notification-toast');
                
                if (snapshot.empty) {
                    notifList.innerHTML = '<p style="font-size: 0.85rem; color: var(--text-muted); text-align: center;">No notifications yet.</p>';
                    return;
                }

                let html = '';
                snapshot.docChanges().forEach((change) => {
                    if (change.type === "added" && !snapshot.metadata.fromCache) {
                        const n = change.doc.data();
                        showToast(n.title, n.message);
                    }
                });

                snapshot.forEach((doc) => {
                    const n = doc.data();
                    html += `
                        <div style="padding: 0.75rem; background: white; border-radius: 0.5rem; border: 1px solid var(--border); border-left: 3px solid ${n.status === 'unread' ? 'var(--primary)' : '#ccc'};">
                            <p style="margin: 0; font-weight: 700; font-size: 0.85rem;">${n.title}</p>
                            <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: var(--text-muted); line-height: 1.4;">${n.message}</p>
                        </div>
                    `;
                });
                notifList.innerHTML = html;
            });

        function showToast(title, message) {
            const toast = document.getElementById('notification-toast');
            document.getElementById('notif-title').textContent = title;
            document.getElementById('notif-body').textContent = message;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 5000);
        }
    }
</script>

<style>
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>

<?php include 'footer.php'; ?>
