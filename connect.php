<?php
// Replace the four placeholder values with the exact MySQL details from InfinityFree.
$host = "sql202.infinityfree.com";
$user = "if0_43146270";
$password = "n91UPbimKao2";
$database = "if0_43146270_quizmaker";

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("Database connection failed. Please check connect.php configuration.");
}
$conn->set_charset("utf8mb4");
?>
