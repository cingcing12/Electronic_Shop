<?php
// init.php

session_start();

require_once __DIR__ . '/config.php';

// Connect to database (PDO)
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

require_once __DIR__ . '/includes/functions.php';




?>
