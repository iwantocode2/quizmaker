<?php

header("Content-Type: application/json");

require "connect.php";
require_once __DIR__ . "/auth.php";
require_login_json();

$id = $_GET["id"] ?? 0;

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "Quiz ID is missing."
    ]);
    exit;
}


// Get quiz

$stmt = $conn->prepare(
    "SELECT id, title, description, created_at, time_limit_seconds
     FROM quizzes
     WHERE id = ? AND owner_id = ?"
);

$ownerId = (int)$_SESSION["user_id"];
$stmt->bind_param("ii", $id, $ownerId);

$stmt->execute();

$result = $stmt->get_result();

$quiz = $result->fetch_assoc();

$stmt->close();


if (!$quiz) {

    echo json_encode([
        "success" => false,
        "message" => "Quiz not found."
    ]);

    exit;
}


// Get questions

$stmt = $conn->prepare(
    "SELECT id, question_text, correct_answer
     FROM questions
     WHERE quiz_id = ?
     ORDER BY id"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$questions = [];


while ($question = $result->fetch_assoc()) {

    $questionId = $question["id"];


    // Get answers

    $answerStmt = $conn->prepare(
        "SELECT answer_letter, answer_text
         FROM answers
         WHERE question_id = ?
         ORDER BY answer_letter"
    );

    $answerStmt->bind_param(
        "i",
        $questionId
    );

    $answerStmt->execute();

    $answerResult =
        $answerStmt->get_result();


    $answers = [];


    while ($answer = $answerResult->fetch_assoc()) {

        $answers[
            $answer["answer_letter"]
        ] = $answer["answer_text"];

    }


    $answerStmt->close();


    $questions[] = [

        "id" =>
            $question["id"],

        "text" =>
            $question["question_text"],

        "correct" =>
            $question["correct_answer"],

        "answers" =>
            $answers

    ];

}


$stmt->close();

$conn->close();


$quiz["questions"] = $questions;


echo json_encode([
    "success" => true,
    "quiz" => $quiz
]);

?>
