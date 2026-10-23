<?php
header("Content-Type: application/json; charset=utf-8");
require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();
if (($_SERVER["REQUEST_METHOD"] ?? "GET") !== "POST") {
    http_response_code(405); header("Allow: POST");
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    $conn->close(); exit;
}
// NOTE: The current project has no login/session system. Protect the dashboard before deploying
// this destructive endpoint publicly; otherwise anyone who can reach it could clear results.
if (!$conn->query("DELETE a FROM quiz_attempts a INNER JOIN quizzes q ON q.id = a.quiz_id WHERE q.owner_id = " . (int)$_SESSION["user_id"])) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Could not clear results."]);
    $conn->close(); exit;
}
echo json_encode(["success" => true, "deleted" => $conn->affected_rows, "message" => "All quiz results were cleared."]);
$conn->close();
?>
