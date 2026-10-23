<?php
header("Content-Type: application/json; charset=utf-8");
require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();

$sql = "SELECT q.id, q.title, q.description, q.created_at, q.time_limit_seconds,
               (SELECT COUNT(*) FROM questions qs WHERE qs.quiz_id = q.id) AS question_count
        FROM quizzes q
        WHERE q.owner_id = " . (int)$_SESSION["user_id"] . "
        ORDER BY q.created_at DESC, q.id DESC";
$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Could not load quizzes."]);
    $conn->close();
    exit;
}
$quizzes = [];
while ($row = $result->fetch_assoc()) {
    $row["question_count"] = (int)$row["question_count"];
    $quizzes[] = $row;
}
$conn->close();
echo json_encode(["success" => true, "quizzes" => $quizzes], JSON_INVALID_UTF8_SUBSTITUTE);
?>
