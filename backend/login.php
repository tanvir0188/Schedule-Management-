<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once 'db.php'; // Ensure this contains the $pdo connection

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
  exit;
}

// Parse JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data["username"]) || empty($data["password"])) {
  echo json_encode(["status" => "error", "message" => "Username and password are required."]);
  exit;
}

$username = trim($data["username"]);
$password = $data["password"];

try {
  // Prepare query
  $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Verify password
  if (!$user || !password_verify($password, $user["password"])) {
    echo json_encode(["status" => "error", "message" => "Invalid username or password."]);
    exit;
  }

  // Store user data in session
  $_SESSION["user_id"] = $user["id"];
  $_SESSION["username"] = $user["username"];

  // Optional: Log session for debugging
  // file_put_contents("session_debug.log", print_r($_SESSION, true));

  echo json_encode([
    "status" => "success",
    "message" => "Login successful.",
    "user" => [
      "id" => $user["id"],
      "username" => $user["username"]
    ]
  ]);
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
