<?php
require_once __DIR__ . '/init.php';
session_destroy();
redirect('index.php');
?>
