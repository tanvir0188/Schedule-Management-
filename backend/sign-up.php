<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db.php';

// Make sure user is logged in
if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
  exit;
}

// Fetch user info from DB to check if they are admin
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

if (!$user || $user["username"] !== "admin") {
  echo json_encode(["status" => "error", "message" => "Only admin can create new users."]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data["username"]) || empty($data["password"])) {
  echo json_encode(["status" => "error", "message" => "Username and password are required."]);
  exit;
}

$username = $data["username"];
$password = password_hash($data["password"], PASSWORD_BCRYPT);

try {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
  $stmt->execute([$username]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => "error", "message" => "Username already exists."]);
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $stmt->execute([$username, $password]);

  echo json_encode(["status" => "success", "message" => "User created successfully."]);
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
