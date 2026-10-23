<?php
header("Content-Type: application/json; charset=utf-8");
require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();
$data = json_decode(file_get_contents("php://input"), true) ?? [];
$quizId = filter_var($data["id"] ?? null, FILTER_VALIDATE_INT);
if (!$quizId || $quizId < 1) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid quiz ID."]);
    $conn->close(); exit;
}
try {
    $conn->begin_transaction();
    $ownerId = (int)$_SESSION["user_id"];
    $check = $conn->prepare("SELECT id FROM quizzes WHERE id = ? AND owner_id = ?");
    $check->bind_param("ii", $quizId, $ownerId); $check->execute();
    $owned = $check->get_result()->num_rows > 0; $check->close();
    if (!$owned) throw new Exception("Quiz not found.");
    $stmt = $conn->prepare("DELETE FROM quiz_attempts WHERE quiz_id = ?");
    if (!$stmt) throw new Exception("The results table is missing. Import quizmaker_database.sql or run quizmaker_upgrade.sql.");
    $stmt->bind_param("i", $quizId);
    if (!$stmt->execute()) throw new Exception("Could not remove saved results for this quiz.");
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM answers WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
    $stmt->bind_param("i", $quizId);
    if (!$stmt->execute()) throw new Exception("Could not remove quiz answers.");
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quizId);
    if (!$stmt->execute()) throw new Exception("Could not remove quiz questions.");
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $quizId, $ownerId);
    if (!$stmt->execute()) throw new Exception("Could not delete quiz.");
    if ($stmt->affected_rows === 0) throw new Exception("Quiz not found.");
    $stmt->close();
    $conn->commit();
    echo json_encode(["success" => true, "message" => "Quiz deleted successfully."]);
} catch (Throwable $error) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $error->getMessage()]);
}
$conn->close();
?>
