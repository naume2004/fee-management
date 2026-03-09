<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? 'Mobile Money');
    $provider = mysqli_real_escape_string($conn, $_POST['provider'] ?? '');
    
    $method_with_provider = $payment_method;
    if ($provider) {
        $method_with_provider .= " ($provider)";
    }

    // Update all pending fees for this student as paid
    $sql = "UPDATE fees SET status = 'Paid', payment_method = '$method_with_provider', payment_date = NOW() 
            WHERE student_id = '$student_id' AND status = 'Pending'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?sim_id=$student_id&msg=Payment Successful via $method_with_provider!");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else if (isset($_GET['id'])) {
    $fee_id = (int)$_GET['id'];
    $student_id = isset($_GET['sim_id']) ? mysqli_real_escape_string($conn, $_GET['sim_id']) : 'S101';
    
    // Simulate payment processing for a single fee item
    $sql = "UPDATE fees SET status = 'Paid', payment_method = 'Online Payment', payment_date = NOW() WHERE id = $fee_id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?sim_id=$student_id&msg=Payment Successful!");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
