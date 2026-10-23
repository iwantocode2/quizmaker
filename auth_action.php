<?php
require_once __DIR__.'/connect.php';
require_once __DIR__.'/auth.php';
$token=trim($_GET['token']??''); $action=$_GET['action']??'';
if ($action==='verify' && preg_match('/^[a-f0-9]{64}$/',$token)) {
  $hash=hash('sha256',$token); $st=$conn->prepare('UPDATE users SET is_verified=1, verification_token_hash=NULL WHERE verification_token_hash=?');
  $st->bind_param('s',$hash); $st->execute(); $ok=$st->affected_rows>0; $st->close(); $conn->close();
  echo '<!doctype html><meta charset="utf-8"><title>QuizMaker verification</title><p>'.($ok?'Email verified. You can now log in.':'This verification link is invalid or already used.').'</p><p><a href="login.php">Go to QuizMaker login</a></p>'; exit;
}
http_response_code(400); echo 'Invalid verification link.';
