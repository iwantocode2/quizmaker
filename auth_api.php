<?php
require_once __DIR__.'/connect.php';
require_once __DIR__.'/auth.php';
$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true) ?? [];
if ($action === 'status') {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success'=>true,'logged_in'=>!empty($_SESSION['user_id']),'email'=>$_SESSION['email'] ?? null]); exit;
}
if ($action === 'logout') {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); }
  session_destroy(); json_response(200,['success'=>true]);
}
if ($action === 'register') {
  $email = strtolower(trim((string)($data['email'] ?? '')));
  $password = (string)($data['password'] ?? '');
  if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10) json_response(400,['success'=>false,'message'=>'Enter a valid email and password of at least 10 characters.']);
  $count = $conn->query('SELECT COUNT(*) AS n FROM users');
  if (!$count) json_response(500,['success'=>false,'message'=>'Run quizmaker_auth_migration.sql first.']);
  $token = bin2hex(random_bytes(32)); $hash = hash('sha256',$token); $passHash = password_hash($password,PASSWORD_DEFAULT);
  $st=$conn->prepare('INSERT INTO users (email,password_hash,verification_token_hash) VALUES (?,?,?)');
  $st->bind_param('sss',$email,$passHash,$hash);
  if (!$st->execute()) json_response(500,['success'=>false,'message'=>'Could not create account.']);
  $st->close(); $conn->close();
  $url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/').'/auth_action.php?token='.rawurlencode($token).'&action=verify';
  $sent=send_brevo_email($email,'Verify your QuizMaker account','<p>Welcome to QuizMaker.</p><p><a href="'.htmlspecialchars($url,ENT_QUOTES,'UTF-8').'">Verify your email address</a></p><p>This link is for your account only.</p>');
  json_response(200,['success'=>true,'message'=>$sent?'Account created. Check your email to verify it.':'Account created, but the verification email could not be sent. Check config.php, the Brevo sender verification, and PHP cURL. Then use the resend instructions in README.']);
}
if ($action === 'login') {
  $email=strtolower(trim((string)($data['email']??''))); $password=(string)($data['password']??'');
  $st=$conn->prepare('SELECT id,email,password_hash,is_verified FROM users WHERE email=?');
  $st->bind_param('s',$email); $st->execute(); $u=$st->get_result()->fetch_assoc(); $st->close(); $conn->close();
  if (!$u || !password_verify($password,$u['password_hash'])) json_response(401,['success'=>false,'message'=>'Email or password is incorrect.']);
  if (!(int)$u['is_verified']) json_response(403,['success'=>false,'message'=>'Verify your email before logging in.']);
  session_regenerate_id(true); $_SESSION['user_id']=(int)$u['id']; $_SESSION['email']=$u['email'];
  json_response(200,['success'=>true,'message'=>'Logged in.']);
}
if ($action === 'forgot') {
  $email=strtolower(trim((string)($data['email']??'')));
  if (!filter_var($email,FILTER_VALIDATE_EMAIL)) json_response(400,['success'=>false,'message'=>'Enter a valid email address.']);
  $st=$conn->prepare('SELECT id FROM users WHERE email=? AND is_verified=1'); $st->bind_param('s',$email); $st->execute(); $u=$st->get_result()->fetch_assoc(); $st->close();
  if ($u) {
    $otp=(string)random_int(100000,999999); $hash=password_hash($otp,PASSWORD_DEFAULT);
    $up=$conn->prepare('UPDATE users SET reset_otp_hash=?, reset_otp_expires_at=DATE_ADD(NOW(), INTERVAL 10 MINUTE), reset_otp_attempts=0 WHERE id=?');
    if (!$up) { $conn->close(); json_response(500,['success'=>false,'message'=>'Run the latest quizmaker_auth_migration.sql first.']); }
    $up->bind_param('si',$hash,$u['id']); $up->execute(); $up->close();
    $sent=send_brevo_email($email,'Your QuizMaker password reset code','<p>Your QuizMaker password reset code is:</p><p style="font-size:28px;font-weight:bold;letter-spacing:6px">'.htmlspecialchars($otp,ENT_QUOTES,'UTF-8').'</p><p>This code expires in 10 minutes. If you did not request this, ignore this email.</p>');
    if (!$sent) { $conn->close(); json_response(502,['success'=>false,'message'=>'Could not send the OTP email. Check Brevo config, verified sender, and PHP cURL.']); }
  }
  $conn->close(); json_response(200,['success'=>true,'message'=>'If this verified account exists, a 6-digit reset code has been emailed.']);
}
if ($action === 'reset_password') {
  $email=strtolower(trim((string)($data['email']??''))); $otp=trim((string)($data['otp']??'')); $password=(string)($data['password']??'');
  if (!filter_var($email,FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{6}$/',$otp) || strlen($password)<10) json_response(400,['success'=>false,'message'=>'Enter your email, 6-digit code, and a password of at least 10 characters.']);
  $st=$conn->prepare('SELECT id,reset_otp_hash,reset_otp_expires_at,reset_otp_attempts,(reset_otp_expires_at IS NOT NULL AND reset_otp_expires_at >= NOW()) AS otp_time_valid FROM users WHERE email=? AND is_verified=1'); $st->bind_param('s',$email); $st->execute(); $u=$st->get_result()->fetch_assoc(); $st->close();
  if (!$u || empty($u['reset_otp_hash']) || (int)$u['reset_otp_attempts']>=5 || !(int)$u['otp_time_valid'] || !password_verify($otp,$u['reset_otp_hash'])) {
    if ($u && !empty($u['reset_otp_hash'])) { $up=$conn->prepare('UPDATE users SET reset_otp_attempts=reset_otp_attempts+1 WHERE id=?'); $up->bind_param('i',$u['id']); $up->execute(); $up->close(); }
    $conn->close(); json_response(400,['success'=>false,'message'=>'The code is invalid, expired, or has too many attempts. Request a new code.']);
  }
  $ph=password_hash($password,PASSWORD_DEFAULT); $up=$conn->prepare('UPDATE users SET password_hash=?,reset_otp_hash=NULL,reset_otp_expires_at=NULL,reset_otp_attempts=0 WHERE id=?'); $up->bind_param('si',$ph,$u['id']); $ok=$up->execute(); $up->close(); $conn->close();
  json_response($ok?200:500,['success'=>$ok,'message'=>$ok?'Password changed. You can now log in.':'Could not update the password.']);
}
json_response(400,['success'=>false,'message'=>'Unknown action.']);
