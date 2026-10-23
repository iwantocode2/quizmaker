<?php
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Manila");
require "connect.php";

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";
$token = trim($_GET["token"] ?? "");
if (!preg_match('/^[a-f0-9]{48}$/', $token)) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "This share link is invalid."]);
    $conn->close();
    exit;
}

$quizStmt = $conn->prepare("SELECT id, title, description, time_limit_seconds FROM quizzes WHERE share_token = ?");
if (!$quizStmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database setup is incomplete. Import quizmaker_database.sql or run quizmaker_upgrade.sql."]);
    $conn->close();
    exit;
}
$quizStmt->bind_param("s", $token);
$quizStmt->execute();
$quiz = $quizStmt->get_result()->fetch_assoc();
$quizStmt->close();
if (!$quiz) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Quiz not found or this link is no longer available."]);
    $conn->close();
    exit;
}


if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];
    $answers = $data["answers"] ?? null;
    $participantName = trim((string)($data["participant_name"] ?? ""));
    if ($participantName === "") $participantName = "Guest";
    $participantName = function_exists("mb_substr") ? mb_substr($participantName, 0, 120, "UTF-8") : substr($participantName, 0, 120);
    if (!is_array($answers)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Submit your answers to get a score."]);
        $conn->close();
        exit;
    }

    $questionStmt = $conn->prepare("SELECT id, correct_answer FROM questions WHERE quiz_id = ? ORDER BY id");
    $questionStmt->bind_param("i", $quiz["id"]);
    $questionStmt->execute();
    $questionResult = $questionStmt->get_result();
    $total = 0;
    $score = 0;
    while ($question = $questionResult->fetch_assoc()) {
        $total++;
        $given = strtoupper(trim((string)($answers[(string)$question["id"]] ?? "")));
        if ($given !== "" && $given === strtoupper((string)$question["correct_answer"])) $score++;
    }
    $questionStmt->close();
    $percentage = $total ? (int)round($score / $total * 100) : 0;

    // Opportunistically remove attempts older than 30 days whenever a quiz is submitted.
    $conn->query("DELETE FROM quiz_attempts WHERE submitted_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $attemptStmt = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, participant_name, score, total_questions, percentage) VALUES (?, ?, ?, ?, ?)");
    if (!$attemptStmt) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "The results table is missing. Import quizmaker_database.sql or run quizmaker_upgrade.sql."]);
        $conn->close();
        exit;
    }
    $quizId = (int)$quiz["id"];
    $attemptStmt->bind_param("isiii", $quizId, $participantName, $score, $total, $percentage);
    if (!$attemptStmt->execute()) {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Your score was calculated, but could not be saved."]);
        $attemptStmt->close();
        $conn->close();
        exit;
    }
    $attemptId = $conn->insert_id;
    $attemptStmt->close();
    $conn->close();
    echo json_encode([
        "success" => true,
        "attempt_id" => $attemptId,
        "participant_name" => $participantName,
        "score" => $score,
        "total" => $total,
        "percentage" => $percentage
    ]);
    exit;
}

if ($method !== "GET") {
    http_response_code(405);
    header("Allow: GET, POST");
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    $conn->close();
    exit;
}

$questionStmt = $conn->prepare("SELECT id, question_text FROM questions WHERE quiz_id = ? ORDER BY id");
$questionStmt->bind_param("i", $quiz["id"]);
$questionStmt->execute();
$questionResult = $questionStmt->get_result();
$questions = [];
while ($question = $questionResult->fetch_assoc()) {
    $answerStmt = $conn->prepare("SELECT answer_letter, answer_text FROM answers WHERE question_id = ? ORDER BY answer_letter");
    $answerStmt->bind_param("i", $question["id"]);
    $answerStmt->execute();
    $answerResult = $answerStmt->get_result();
    $answers = [];
    while ($answer = $answerResult->fetch_assoc()) {
        $answers[] = ["letter" => $answer["answer_letter"], "text" => $answer["answer_text"]];
    }
    $answerStmt->close();
    $questions[] = ["id" => $question["id"], "text" => $question["question_text"], "answers" => $answers];
}
$questionStmt->close();
$conn->close();
$quiz["questions"] = $questions;
echo json_encode(["success" => true, "quiz" => $quiz], JSON_INVALID_UTF8_SUBSTITUTE);
?>
