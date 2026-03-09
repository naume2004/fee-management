<?php
include '../db.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['username']);
    $student_id = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT student_id, name FROM students WHERE name = '$name' AND student_id = '$student_id'";
    $result = mysqli_query($conn, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['student_id'] = $row['student_id'];
        header("Location: dashboard.php?sim_id=" . $row['student_id']);
        exit();
    } else {
        $error = "Invalid Student Name or ID. Please check your credentials.";
    }
}

include 'header.php';
?>

<div class="form-container" style="max-width: 500px; margin-top: 5rem;">
    <h2 style="text-align: center; margin-bottom: 2rem;">Student Portal Login</h2>
    <p style="color: var(--text-muted); text-align: center; margin-bottom: 2rem;">Login using your <strong>Full Name</strong> and <strong>Student ID</strong>.</p>
    
    <?php if ($error): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="form-card" style="padding: 2.5rem; width: 100%; background: white; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
        <form action="login.php" method="POST">
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="username" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Full Name</label>
                <input type="text" name="username" id="username" placeholder="e.g. John Doe" 
                       style="width: 100%; padding: 0.85rem; border-radius: 0.5rem; border: 1px solid var(--border);" required>
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Student ID (Password)</label>
                <input type="password" name="password" id="password" placeholder="e.g. S101" 
                       style="width: 100%; padding: 0.85rem; border-radius: 0.5rem; border: 1px solid var(--border);" required>
            </div>
            
            <button type="submit" class="btn primary-btn" style="width: 100%; padding: 1rem; font-weight: 700;">Login to Dashboard</button>
        </form>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); text-align: center;">
            <p style="color: var(--text-muted); font-size: 0.85rem;">Don't have your credentials? Please contact the school registrar.</p>
        </div>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <p style="font-size: 0.85rem; color: var(--text-muted);">Secure Portal &bull; © 2026 FeeFlow</p>
    </div>
</div>

<?php include 'footer.php'; ?>
