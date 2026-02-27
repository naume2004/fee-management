<?php
// Clear the simulated student session
session_start();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
