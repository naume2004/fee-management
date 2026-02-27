<?php
include 'db.php';

$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['load_data'])) {
    $sql_file = file_get_contents('school_students.sql');
    $queries = array_filter(array_map('trim', explode(';', $sql_file)), function($q) {
        return !empty($q) && strpos(trim($q), '--') !== 0;
    });
    
    $error_count = 0;
    $success_count = 0;
    
    foreach ($queries as $query) {
        if (!empty(trim($query))) {
            if (mysqli_query($conn, $query)) {
                $success_count++;
            } else {
                $error_count++;
                $status .= "Error: " . mysqli_error($conn) . "<br>";
            }
        }
    }
    
    if ($error_count === 0) {
        $status = "✓ Successfully loaded school student data! ($success_count queries executed)";
    } else {
        $status = "⚠ Completed with errors. Success: $success_count, Errors: $error_count<br>$status";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Load School Student Data</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.html" class="logo">FeeFlow</a>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="admin/admin.php">Admin Panel</a></li>
            </ul>
        </div>
    </nav>

    <main class="container main-content">
        <div style="max-width: 700px; margin: 4rem auto;">
            <div style="background: white; padding: 3rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                <h1 style="text-align: center; margin-bottom: 1rem;">Load School Student Data</h1>
                <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">This will populate the database with 15 sample students and their fee records.</p>

                <?php if ($status): ?>
                    <div style="background: <?php echo strpos($status, '✓') === 0 ? '#dcfce7' : '#fee2e2'; ?>; border-left: 5px solid <?php echo strpos($status, '✓') === 0 ? 'var(--success)' : 'var(--danger)'; ?>; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                        <p style="margin: 0; color: <?php echo strpos($status, '✓') === 0 ? '#166534' : '#7f1d1d'; ?>"><?php echo $status; ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" style="margin-bottom: 2rem;">
                    <div style="background: #fffbeb; border-left: 5px solid var(--warning); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                        <p style="margin: 0; color: #92400e;"><strong>⚠️ Warning:</strong> This will delete existing students and fees data and replace it with sample school data.</p>
                    </div>

                    <div style="background: #f0fdf4; border-left: 5px solid var(--success); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                        <h3 style="margin-top: 0;">Data to be loaded:</h3>
                        <ul style="margin: 1rem 0; color: var(--text-muted);">
                            <li>15 Students (S101 - S115)</li>
                            <li>45+ Fee Records (Tuition, Library, Lab, Activity fees)</li>
                            <li>Payment history with various statuses</li>
                            <li>10 Payment deadlines with reminder tracking</li>
                            <li>10 Scheduled appointments</li>
                            <li>Live queue data</li>
                        </ul>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <button type="submit" name="load_data" class="btn primary-btn" style="width: 100%;">Load School Data</button>
                        <a href="index.html" class="btn secondary-btn" style="width: 100%; text-align: center; text-decoration: none; color: var(--primary); border: 1px solid var(--primary);">Cancel</a>
                    </div>
                </form>

                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                    <h3 style="margin-top: 0;">Sample Students Loaded:</h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; font-size: 0.9rem; color: var(--text-muted);">
                        <div>S101 - Sarah Johnson</div>
                        <div>S102 - Jackie Smith</div>
                        <div>S103 - Winnie Davis</div>
                        <div>S104 - Michael Brown</div>
                        <div>S105 - Emily Wilson</div>
                        <div>S106 - David Miller</div>
                        <div>S107 - Jessica Taylor</div>
                        <div>S108 - James Anderson</div>
                        <div>S109 - Lisa Moore</div>
                        <div>S110 - Christopher Garcia</div>
                        <div>S111 - Amanda Martinez</div>
                        <div>S112 - Ryan Thompson</div>
                        <div>S113 - Sophia Lee</div>
                        <div>S114 - Daniel White</div>
                        <div>S115 - Olivia Harris</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-info">
                <h3>FeeFlow</h3>
                <p>Simplifying university fee payments</p>
            </div>
        </div>
    </footer>
</body>
</html>
