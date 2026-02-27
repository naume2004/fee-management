<?php
session_start();
include '../db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = mysqli_prepare($conn, "SELECT password, name FROM staff WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hash, $name);
        if (mysqli_stmt_fetch($stmt) && password_verify($password, $hash)) {
            // successful login
            $_SESSION['staff_logged_in'] = true;
            $_SESSION['staff_username'] = $username;
            $_SESSION['staff_name'] = $name;
            header('Location: admin.php');
            exit();
        } else {
            $error = 'Invalid username or password';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - FeeFlow</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="form-container" style="max-width: 400px; margin: 8rem auto;">
        <h2 style="text-align:center; margin-bottom:2rem;">Staff Login</h2>
        <?php if ($error): ?>
            <div style="background:#fee2e2; color:#7f1d1d; padding:1rem; border-radius:0.5rem; margin-bottom:1rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group" style="margin-bottom:1rem;">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:0.5rem;" required>
            </div>
            <div class="form-group" style="margin-bottom:1rem;">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" style="width:100%; padding:0.75rem; border:1px solid var(--border); border-radius:0.5rem;" required>
            </div>
            <button type="submit" class="btn primary-btn" style="width:100%;">Login</button>
        </form>
        <p style="text-align:center; margin-top:1.5rem; font-size:0.85rem; color:var(--text-muted);">Use <code>admin</code>/<code>admin123</code> via seeded data.</p>
    </div>
</body>
</html>
