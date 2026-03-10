<?php
include '../db.php';

// Fetch the currently serving token
$serving_sql = "SELECT token_number, counter_number FROM queue WHERE status = 'Serving' LIMIT 1";
$serving_result = mysqli_query($conn, $serving_sql);
$serving = mysqli_fetch_assoc($serving_result);

// Fetch total people waiting
$waiting_sql = "SELECT COUNT(*) as waiting_count FROM queue WHERE status = 'Waiting'";
$waiting_result = mysqli_query($conn, $waiting_sql);
$waiting = mysqli_fetch_assoc($waiting_result);

// Calculate estimated wait time (approx 5 mins per person)
$wait_time = ($waiting['waiting_count'] ?? 0) * 5;

// Fetch full queue list for display
$queue_list_sql = "SELECT * FROM queue WHERE status IN ('Waiting', 'Serving') ORDER BY status DESC, token_number ASC";
$queue_list_result = mysqli_query($conn, $queue_list_sql);

include 'header.php';
?>

<div class="queue-header" style="text-align: center; margin-bottom: 4rem;">
    <h1>Live Queue Monitor</h1>
    <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">Check the current progress of the Finance Office queue in real-time. This page updates automatically to keep you informed of your turn.</p>
</div>

<div class="queue-monitor-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
    <div class="current-serving-panel" style="flex: 1;">
        <div class="queue-box" style="padding: 3rem 2rem;">
            <h3>Currently Serving</h3>
            <div class="current-number">
                <?php echo $serving['token_number'] ?? '---'; ?>
            </div>
            <p style="font-size: 1.25rem; font-weight: 600; color: var(--success); margin-bottom: 1rem;">
                Counter #<?php echo $serving['counter_number'] ?? '1'; ?>
            </p>
            <div style="padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <p style="color: var(--text-muted);">Estimated Wait Time: <strong><?php echo $wait_time; ?> minutes</strong></p>
                <p style="color: var(--text-muted);">People in Queue: <strong><?php echo $waiting['waiting_count'] ?? 0; ?> students</strong></p>
            </div>
        </div>
        
        <div class="join-queue-promo" style="background: white; padding: 2.5rem; border-radius: 1rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <h3>Need to join the line?</h3>
            <p style="margin: 1rem 0 1.5rem; color: var(--text-muted);">You can generate a digital token from your student dashboard and get notified when it's almost your turn.</p>
            <a href="dashboard.php" class="btn primary-btn">Generate Digital Token</a>
        </div>
    </div>
    
    <div class="queue-list-panel" style="flex: 1;">
        <h2>Queue Overview</h2>
        <div style="margin-top: 1.5rem;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Token #</th>
                        <th>Student ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($queue_list_result) > 0): ?>
                        <?php while($q = mysqli_fetch_assoc($queue_list_result)): ?>
                            <tr style="<?php echo $q['status'] == 'Serving' ? 'background: #f0fdf4; font-weight: 600;' : ''; ?>">
                                <td>#<?php echo $q['token_number']; ?></td>
                                <td><?php echo $q['student_id'] ?? '---'; ?></td>
                                <td>
                                    <span class="badge <?php echo $q['status'] == 'Serving' ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $q['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem;">No one is currently in the queue.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 2rem; padding: 2rem; background: #fffbeb; border-radius: 1rem; border-left: 4px solid #f59e0b;">
            <p style="font-size: 0.9rem; color: #92400e;">
                💡 <strong>Tip:</strong> If your estimated wait time is more than 30 minutes, consider grabbing a coffee or catching up on your studies. We'll keep this page updated!
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
