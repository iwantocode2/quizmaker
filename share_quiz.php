<?php
header("Content-Type: application/json; charset=utf-8");
require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();

$payload = json_decode(file_get_contents("php://input"), true) ?? [];
$id = filter_var($payload["id"] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "A valid quiz ID is required."]);
    exit;
}

// The migration adds share_token. Tokens are random, unguessable public identifiers.
$check = $conn->prepare("SELECT share_token FROM quizzes WHERE id = ? AND owner_id = ?");
if (!$check) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database setup is incomplete. Run share_quizzes_migration.sql first."]);
    exit;
}
$ownerId = (int)$_SESSION["user_id"];
$check->bind_param("ii", $id, $ownerId);
$check->execute();
$row = $check->get_result()->fetch_assoc();
$check->close();
if (!$row) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Quiz not found."]);
    exit;
}
$token = $row["share_token"];
if (!$token) {
    $token = bin2hex(random_bytes(24));
    $update = $conn->prepare("UPDATE quizzes SET share_token = ? WHERE id = ? AND owner_id = ? AND (share_token IS NULL OR share_token = '')");
    $update->bind_param("sii", $token, $id, $ownerId);
    $update->execute();
    $update->close();
    // If two requests raced, use the token actually stored in the database.
    $again = $conn->prepare("SELECT share_token FROM quizzes WHERE id = ? AND owner_id = ?");
    $again->bind_param("ii", $id, $ownerId);
    $again->execute();
    $token = $again->get_result()->fetch_assoc()["share_token"] ?? $token;
    $again->close();
}
$scheme = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$host = $_SERVER["HTTP_HOST"] ?? "localhost";
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER["SCRIPT_NAME"] ?? "/")), "/");
$url = $scheme . "://" . $host . ($basePath ? $basePath . "/" : "/") . "take_quiz.php?token=" . rawurlencode($token);
echo json_encode(["success" => true, "url" => $url]);
$conn->close();
?>
