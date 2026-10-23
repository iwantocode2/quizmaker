<?php
header("Content-Type: application/json; charset=utf-8");
require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();

// Retention policy: remove quiz attempts older than 30 days whenever results are loaded.
// For deletion at an exact scheduled time, configure a hosting cron job if your plan supports it.
$conn->query("DELETE FROM quiz_attempts WHERE submitted_at < (NOW() - INTERVAL 30 DAY)");

$sql = "SELECT a.id, a.participant_name, a.score, a.total_questions,
               a.percentage, a.submitted_at, q.title AS quiz_title
        FROM quiz_attempts a
        INNER JOIN quizzes q ON q.id = a.quiz_id
        WHERE q.owner_id = " . (int)$_SESSION["user_id"] . "
        ORDER BY a.submitted_at DESC, a.id DESC
        LIMIT 100";
$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Could not load results. Import quizmaker_database.sql or quizmaker_upgrade.sql."]);
    $conn->close();
    exit;
}
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$conn->close();
echo json_encode(["success" => true, "results" => $rows], JSON_INVALID_UTF8_SUBSTITUTE);
?>
