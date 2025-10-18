<?php
require_once __DIR__ . '/../init.php'; // Adjust path since inside admin folder

$username = 'adminuser';
$email = 'admin@example.com';
$password = 'admin1234@'; // Change this before running!

// Check if admin user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    die("Admin user already exists.\n");
}

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert admin user
$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
$stmt->execute([$username, $email, $hash]);

echo "Admin user created successfully.\n";
