<?php
session_start();

// Sabhi session variables delete
$_SESSION = array();

// Session destroy
session_destroy();

// Browser cache clear
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Login page par redirect
header("Location: login.php");
exit();
?>