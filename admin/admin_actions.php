<?php
include '../db.php';
include 'auth.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    if ($action == 'approve_payment') {
        $sql = "UPDATE fees SET status = 'Paid', payment_date = NOW() WHERE id = $id";
    } elseif ($action == 'complete_appt') {
        $sql = "UPDATE appointments SET status = 'Completed' WHERE id = $id";
    }
    
    if (isset($sql) && mysqli_query($conn, $sql)) {
        header("Location: admin.php?msg=Action Successful");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: admin.php");
    exit();
}
?>
