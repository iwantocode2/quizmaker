<?php
header("Content-Type: application/json; charset=utf-8");
require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();

$data = json_decode(file_get_contents("php://input"), true);
$title = trim((string)($data["title"] ?? ""));
$description = trim((string)($data["description"] ?? ""));
$timeLimit = max(0, min(86400, (int)($data["time_limit_seconds"] ?? 120)));
$questions = $data["questions"] ?? [];

if ($title === "" || !is_array($questions) || count($questions) < 1) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "A quiz title and at least one question are required."]);
    $conn->close(); exit;
}

try {
    $conn->begin_transaction();
    $stmt = $conn->prepare("INSERT INTO quizzes (owner_id, title, description, time_limit_seconds) VALUES (?, ?, ?, ?)");
    if (!$stmt) throw new Exception("Database schema is missing schedule columns. Ensure the quizmaker database tables are installed.");
    $ownerId = (int)$_SESSION["user_id"];
    $stmt->bind_param("issi", $ownerId, $title, $description, $timeLimit);
    if (!$stmt->execute()) throw new Exception("Could not save quiz: " . $stmt->error);
    $quizId = (int)$conn->insert_id;
    $stmt->close();

    foreach ($questions as $question) {
        $questionText = trim((string)($question["text"] ?? ""));
        $correct = strtoupper(trim((string)($question["correct"] ?? "")));
        $answers = $question["answers"] ?? [];
        if ($questionText === "" || !in_array($correct, ["A", "B", "C", "D"], true) || !is_array($answers)) throw new Exception("Each question needs text, four answers, and one correct answer.");
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, correct_answer) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $quizId, $questionText, $correct);
        if (!$stmt->execute()) throw new Exception("Could not save a question.");
        $questionId = (int)$conn->insert_id;
        $stmt->close();
        foreach (["A", "B", "C", "D"] as $letter) {
            $answerText = trim((string)($answers[$letter] ?? ""));
            if ($answerText === "") throw new Exception("Every question must have all four answer choices filled in.");
            $answerStmt = $conn->prepare("INSERT INTO answers (question_id, answer_letter, answer_text) VALUES (?, ?, ?)");
            $answerStmt->bind_param("iss", $questionId, $letter, $answerText);
            if (!$answerStmt->execute()) throw new Exception("Could not save an answer choice.");
            $answerStmt->close();
        }
    }
    $conn->commit();
    echo json_encode(["success" => true, "message" => "Quiz saved successfully.", "id" => $quizId]);
} catch (Throwable $error) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}
$conn->close();
?>
