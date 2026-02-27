<?php
include '../db.php';

// Fetch students for the selection list
$sql = "SELECT student_id, name FROM students";
$result = mysqli_query($conn, $sql);

include 'header.php';
?>

<div class="form-container" style="max-width: 500px; margin-top: 5rem;">
    <h2 style="text-align: center; margin-bottom: 2rem;">Student Portal Login</h2>
    <p style="color: var(--text-muted); text-align: center; margin-bottom: 2rem;">Please select a student account to access the university payment dashboard.</p>
    
    <div class="form-card" style="padding: 2rem; width: 100%;">
        <form action="dashboard.php" method="GET">
            <div class="form-group">
                <label for="sim_id">Select Student Account</label>
                <select name="sim_id" id="sim_id" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?php echo htmlspecialchars($row['student_id']); ?>">
                            <?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['student_id']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn primary-btn" style="width: 100%; margin-top: 1rem;">Login to Dashboard</button>
        </form>
        
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
            <p style="color: var(--text-muted); text-align: center; font-size: 0.9rem; margin-bottom: 1rem;">Or pay directly:</p>
            <form action="payment_gateway.php" method="GET" style="display: flex; gap: 1rem;">
                <select name="sim_id" style="flex: 1; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                    <option value="">Select to pay</option>
                    <?php 
                    mysqli_data_seek($result, 0);
                    while($row = mysqli_fetch_assoc($result)): ?>
                        <option value="<?php echo htmlspecialchars($row['student_id']); ?>">
                            <?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['student_id']); ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn secondary-btn" style="color: var(--primary); border: 1px solid var(--primary);">Go to Payments</button>
            </form>
        </div>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <p style="font-size: 0.85rem; color: var(--text-muted);">This is a simulation portal for university payment data tracking.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
