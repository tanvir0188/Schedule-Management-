<?php
require_once 'db.php';

$username = "arnob";
$password_plain = "1234";

// Hash the password securely
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

try {
  $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $stmt->execute([$username, $hashed_password]);

  echo "User created successfully!";
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
