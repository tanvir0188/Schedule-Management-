<?php
// CORS headers for local testing
header("Access-Control-Allow-Origin: http://localhost:3000"); // Change port if needed
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Start the session
session_start();

// Allow preflight OPTIONS request
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
  http_response_code(200);
  exit;
}

// Ensure it's a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  echo json_encode(["status" => "error", "message" => "Invalid request method. Use POST."]);
  exit;
}

// Check if admin is logged in
if (!isset($_SESSION["user_id"])) {
  echo json_encode(["status" => "error", "message" => "Admin is not logged in."]);
  exit;
}

// Clear all session variables
$_SESSION = [];

// Destroy the session
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}
session_destroy();

// Respond with success
echo json_encode(["status" => "success", "message" => "Admin logged out successfully."]);
