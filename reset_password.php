<?php
require_once __DIR__.'/connect.php';
$token=trim($_GET['token']??'');
if (!preg_match('/^[a-f0-9]{64}$/',$token)) { http_response_code(400); exit('Invalid reset link.'); }
$hash=hash('sha256',$token);
$st=$conn->prepare('SELECT id FROM users WHERE reset_token_hash=? AND reset_expires_at > NOW()'); $st->bind_param('s',$hash); $st->execute(); $u=$st->get_result()->fetch_assoc(); $st->close();
if (!$u) { $conn->close(); exit('This password-reset link is invalid or expired.'); }
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $pw=(string)($_POST['password']??'');
 if (strlen($pw)<10) $error='Use at least 10 characters.';
 else { $ph=password_hash($pw,PASSWORD_DEFAULT); $up=$conn->prepare('UPDATE users SET password_hash=?,reset_token_hash=NULL,reset_expires_at=NULL WHERE id=?'); $up->bind_param('si',$ph,$u['id']); $up->execute(); $up->close(); $conn->close(); header('Location: login.php'); exit; }
}
$conn->close();
?><!doctype html><html><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Reset password</title><body style="font:16px Arial;max-width:420px;margin:50px auto;padding:16px"><h1>Reset password</h1><?php if(isset($error)) echo '<p>'.htmlspecialchars($error).'</p>'; ?><form method="post"><label>New password (10+ characters)<br><input type="password" name="password" minlength="10" required></label><p><button>Save new password</button></p></form></body></html>
