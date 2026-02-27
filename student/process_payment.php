<?php
include '../db.php';

if (isset($_GET['id'])) {
    $fee_id = (int)$_GET['id'];
    
    // Simulate payment processing
    $sql = "UPDATE fees SET status = 'Paid', payment_method = 'Credit Card', payment_date = NOW() WHERE id = $fee_id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: dashboard.php?msg=Payment Successful!");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
