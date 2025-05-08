<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["username"])) {
  echo json_encode(["status" => "error", "message" => "Not logged in."]);
  exit;
}

echo json_encode([
  "status" => "success",
  "user" => [
    "id" => $_SESSION["user_id"],
    "username" => $_SESSION["username"]
  ]
]);
