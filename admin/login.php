<?php
session_start();
include '../db.php';

// Already logged in → go straight to admin
if (!empty($_SESSION['staff_logged_in'])) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $credential = trim($_POST['credential'] ?? '');   // email OR username
    $password   = $_POST['password'] ?? '';

    if ($credential && $password) {
        // Accept either username OR email
        $stmt = mysqli_prepare($conn,
            "SELECT password, name, username FROM staff WHERE username = ? OR email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'ss', $credential, $credential);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $hash, $name, $username);

        if (mysqli_stmt_fetch($stmt) && password_verify($password, $hash)) {
            $_SESSION['staff_logged_in'] = true;
            $_SESSION['staff_username']  = $username;
            $_SESSION['staff_name']      = $name;
            mysqli_stmt_close($stmt);
            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid email/username or password. Please try again.';
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = 'Please enter both your credentials and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login — FeeFlow</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-wrap { width: 100%; max-width: 420px; padding: 1.5rem; }
        .login-card { background: white; border-radius: 1.25rem; box-shadow: 0 20px 40px rgba(0,0,0,0.25); padding: 2.5rem; }
        .login-logo { text-align: center; margin-bottom: 2rem; }
        .login-logo .logo-icon { width: 56px; height: 56px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1rem; }
        .login-logo h1 { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin: 0 0 0.25rem; }
        .login-logo p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
        .form-field { margin-bottom: 1.25rem; }
        .form-field label { display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem; color: var(--text-main); }
        .form-field .input-wrap { position: relative; }
        .form-field .input-icon { position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 1rem; pointer-events: none; }
        .form-field input { width: 100%; padding: 0.75rem 0.75rem 0.75rem 2.4rem; border: 1.5px solid var(--border); border-radius: 0.6rem; font-family: inherit; font-size: 0.95rem; transition: border-color 0.2s; color: var(--text-main); }
        .form-field input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
        .error-box { background: #fef2f2; border-left: 4px solid var(--danger); padding: 0.85rem 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; color: #7f1d1d; font-size: 0.9rem; }
        .login-btn { width: 100%; padding: 0.85rem; background: linear-gradient(135deg, var(--primary), #7c3aed); color: white; border: none; border-radius: 0.6rem; font-size: 1rem; font-weight: 700; cursor: pointer; transition: opacity 0.2s, transform 0.1s; font-family: inherit; }
        .login-btn:hover { opacity: 0.92; }
        .login-btn:active { transform: scale(0.99); }
        .hint-box { margin-top: 1.5rem; padding: 1rem 1.25rem; background: #f0f9ff; border: 1px dashed #7dd3fc; border-radius: 0.6rem; }
        .hint-box p { margin: 0 0 0.35rem; font-size: 0.8rem; color: #0369a1; }
        .hint-box p:last-child { margin: 0; }
        .hint-box code { background: #dbeafe; padding: 0.1rem 0.35rem; border-radius: 0.25rem; font-weight: 600; color: #1e40af; }
        .divider { display: flex; align-items: center; gap: 0.75rem; margin: 0.5rem 0 1rem; }
        .divider span { color: var(--text-muted); font-size: 0.8rem; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        @media (max-width: 480px) { .login-card { padding: 1.75rem 1.25rem; } }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <div class="logo-icon">🏫</div>
            <h1>FeeFlow Admin</h1>
            <p>Staff &amp; Finance Office Portal</p>
        </div>

        <?php if ($error): ?>
            <div class="error-box">⚠️ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-field">
                <label for="credential">Email Address or Username</label>
                <div class="input-wrap">
                    <span class="input-icon">✉️</span>
                    <input
                        type="text"
                        name="credential"
                        id="credential"
                        placeholder="admin@school.edu  or  admin"
                        value="<?php echo htmlspecialchars($_POST['credential'] ?? ''); ?>"
                        autocomplete="username"
                        required>
                </div>
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required>
                </div>
            </div>

            <button type="submit" class="login-btn">Login to Admin Panel →</button>
        </form>

        <div class="divider"><span>demo credentials</span></div>

        <div class="hint-box">
            <p>📧 Email: <code>admin@school.edu</code></p>
            <p>👤 Username: <code>admin</code></p>
            <p>🔑 Password: <code>admin123</code></p>
        </div>
    </div>
</div>
</body>
</html>
