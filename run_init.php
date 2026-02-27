<?php
chdir('c:/xampp/htdocs/fee-management');
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['load_data'] = true;
include 'load_school_data.php';
echo strip_tags($status);
?>
