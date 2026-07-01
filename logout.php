<?php
session_start();

require_once 'config/database.php'; 

session_unset(); 
session_destroy(); 
header("Location: " . $base_url);
exit();
?>