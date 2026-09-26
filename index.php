<?php
// index.php
require_once 'config/database.php';
require_once 'config/functions.php';

// Redirect to login page
redirect(BASE_URL . '/auth/login.php');
?>
