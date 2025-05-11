<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

session_start();
require_once 'db.php';

if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized. Please log in."]);
  exit;
}

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch();

if (!$user || $user["username"] !== "admin") {
  echo json_encode(["status" => "error", "message" => "Only admin can update users."]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id"] ?? null;
$username = $data["username"] ?? null;
$password = $data["password"] ?? null;

if (!$id || !$username) {
  echo json_encode(["status" => "error", "message" => "User ID and username are required."]);
  exit;
}

try {
  // Check for username conflict
  $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
  $stmt->execute([$username, $id]);
  if ($stmt->rowCount() > 0) {
    echo json_encode(["status" => "error", "message" => "Username already taken by another user."]);
    exit;
  }

  if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
    $stmt->execute([$username, $hashedPassword, $id]);
  } else {
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$username, $id]);
  }

  echo json_encode(["status" => "success", "message" => "User updated successfully."]);
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "DB error: " . $e->getMessage()]);
}
