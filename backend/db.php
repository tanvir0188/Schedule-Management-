<?php
$host = "localhost:3306";
$dbname = "united_machinery";
$username = "root";
$password = "";

try {
  // Create a PDO connection with utf8mb4 support
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
  ]);

  // Set error mode to exceptions
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // echo "Database connection successful!";
} catch (PDOException $e) {
  // If connection fails, stop execution and show an error
  die("Database connection failed: " . $e->getMessage());
}
