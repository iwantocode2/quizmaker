<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly'=>true, 'secure'=>$secure, 'samesite'=>'Lax']);
    session_start();
}
function json_response($status, $body) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
function require_login_json() {
    if (empty($_SESSION['user_id'])) json_response(401, ['success'=>false,'message'=>'Please log in again.']);
}
function send_brevo_email($to, $subject, $html) {
    if (!file_exists(__DIR__.'/config.php')) return false;
    require_once __DIR__.'/config.php';
    if (!defined('BREVO_API_KEY') || BREVO_API_KEY === '' || strpos(BREVO_API_KEY, 'PASTE_') === 0) return false;
    $payload = [
      'sender'=>['name'=>BREVO_SENDER_NAME,'email'=>BREVO_SENDER_EMAIL],
      'to'=>[['email'=>$to]], 'subject'=>$subject, 'htmlContent'=>$html
    ];
    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [CURLOPT_POST=>true, CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPHEADER=>[
      'accept: application/json','api-key: '.BREVO_API_KEY,'content-type: application/json'
    ], CURLOPT_POSTFIELDS=>json_encode($payload), CURLOPT_TIMEOUT=>15]);
    $out = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $out !== false && $status >= 200 && $status < 300;
}
