<?php
session_start();

// remove only one session variable
unset($_SESSION['admin_id']);


header("Location: login.php");
exit;
