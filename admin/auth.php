<?php
// session-based authentication for staff pages
session_start();
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    // redirect to login
    header('Location: login.php');
    exit();
}
?>