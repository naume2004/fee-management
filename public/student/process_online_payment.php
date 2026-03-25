<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
    $amount_ugx = mysqli_real_escape_string($conn, $_POST['amount']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $provider = mysqli_real_escape_string($conn, $_POST['provider'] ?? '');
    $transaction_ref = mysqli_real_escape_string($conn, $_POST['transaction_ref'] ?? '');
    $sender_name = mysqli_real_escape_string($conn, $_POST['sender_name'] ?? '');
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number'] ?? '');
    $bank_name = mysqli_real_escape_string($conn, $_POST['bank_name'] ?? '');
    $schedule = mysqli_real_escape_string($conn, $_POST['schedule'] ?? 'monthly');
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date'] ?? '');
    
    $payment_method = "";
    switch($payment_type) {
        case 'mobile':
            $payment_method = "Mobile Money ($provider)";
            break;
        case 'card':
            $payment_method = "Credit/Debit Card";
            break;
        case 'ussd':
            $payment_method = "USSD Payment ($provider)";
            break;
        case 'qr':
            $payment_method = "QR Code Payment";
            break;
        case 'bank':
            $payment_method = "Bank Transfer - Ref: $transaction_ref";
            break;
        case 'autopay':
            $payment_method = "Auto-Pay ($bank_name - $schedule)";
            break;
        default:
            $payment_method = "Online Payment";
    }
    
    $amount_usd = $amount_ugx / 3800;
    
    mysqli_begin_transaction($conn);
    
    try {
        $update_fees = "UPDATE fees SET 
            status = 'Paid', 
            payment_method = '$payment_method', 
            payment_date = NOW() 
            WHERE student_id = '$student_id' AND status = 'Pending'";
        
        if (!mysqli_query($conn, $update_fees)) {
            throw new Exception("Error updating fees");
        }
        
        $insert_payment = "INSERT INTO online_payments 
            (student_id, amount_ugx, amount_usd, payment_type, payment_method, phone, transaction_ref, sender_name, status, created_at) 
            VALUES 
            ('$student_id', '$amount_ugx', '$amount_usd', '$payment_type', '$payment_method', '$phone', '$transaction_ref', '$sender_name', 'Completed', NOW())";
        
        if (!mysqli_query($conn, $insert_payment)) {
            throw new Error("Error recording payment");
        }
        
        $payment_id = mysqli_insert_id($conn);
        
        if ($payment_type === 'autopay') {
            $insert_autopay = "INSERT INTO standing_orders 
                (student_id, bank_name, account_number, schedule, amount_ugx, start_date, status, created_at) 
                VALUES 
                ('$student_id', '$bank_name', '$account_number', '$schedule', '$amount_ugx', '$start_date', 'Active', NOW())";
            mysqli_query($conn, $insert_autopay);
        }
        
        $update_deadlines = "UPDATE payment_deadlines pd
            INNER JOIN fees f ON pd.fee_id = f.id
            SET pd.reminder_sent = TRUE, pd.reminder_count = pd.reminder_count + 1, pd.last_reminder_date = NOW()
            WHERE f.student_id = '$student_id' AND f.status = 'Paid'";
        mysqli_query($conn, $update_deadlines);
        
        mysqli_commit($conn);
        
        header("Location: payment_receipt.php?id=$payment_id&sim_id=$student_id&msg=Payment Successful!");
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Payment failed: " . $e->getMessage();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
