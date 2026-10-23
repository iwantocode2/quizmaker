
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Password recovery — QuizMaker</title>
<link rel="stylesheet" href="login.css">

<style>
.auth-wrap{max-width:430px;margin:7vh auto;padding:22px}
.auth-card{background:#fff;border:1px solid #e0e8fa;border-radius:22px;padding:30px;box-shadow:0 10px 35px #24397310}
.brand{font-size:14px;font-weight:800;color:#6557f5;letter-spacing:.08em}
.auth-card h1{margin:12px 0 8px;font-size:30px}
.auth-card label{display:block;font-weight:650;margin:16px 0 7px}
.auth-card input{box-sizing:border-box;width:100%;padding:13px;border:1px solid #dce4f5;border-radius:10px;font:inherit}
.auth-card button{margin-top:18px;width:100%;padding:13px 16px;border:0;border-radius:10px;background:#6557f5;color:white;font-weight:800;cursor:pointer}
.auth-card p{line-height:1.5}
.auth-message{min-height:24px;color:#a32643}
.auth-foot{text-align:center;margin-top:22px}
.auth-foot a{color:#5548df;text-decoration:none;font-weight:650}
.subtle{color:#667085;font-size:14px}
.auth-card hr{border:0;border-top:1px solid #e8ebf4;margin:24px 0}

/* Show/hide password */
.password-wrap{position:relative}
.password-wrap input{padding-right:52px}
.password-toggle{
  position:absolute;
  right:8px;
  top:50%;
  transform:translateY(-50%);
  width:36px!important;
  height:36px;
  margin:0!important;
  padding:6px!important;
  background:transparent!important;
  color:#68738a!important;
  border:0;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
}
.password-toggle svg{width:21px;height:21px}
.password-toggle:focus-visible{outline:2px solid #6557f5;outline-offset:2px}
</style>
</head>

<body>
<main class="auth-wrap">
<section class="auth-card">
<div class="brand">QUIZMAKER</div>
<h1>Reset your password</h1>
<p>Request a 6-digit code by email, then choose a new password.</p>

<form id="requestOtp">
<label for="requestEmail">Account email</label>
<input id="requestEmail" type="email" name="email" required autocomplete="email">
<button type="submit">Send OTP</button>
</form>

<hr>

<form id="resetWithOtp">
<label for="resetEmail">Account email</label>
<input id="resetEmail" type="email" name="email" required autocomplete="email">

<label for="otp">6-digit code</label>
<input id="otp" type="text" name="otp" inputmode="numeric"
pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code">

<label for="newPassword">New password</label>
<div class="password-wrap">
  <input id="newPassword" type="password" name="password"
    minlength="10" required autocomplete="new-password">
  <button class="password-toggle" type="button" id="togglePassword"
    aria-label="Show password" aria-pressed="false">
    <svg id="eyeIcon" viewBox="0 0 24 24" fill="none"
      stroke="currentColor" stroke-width="2"
      stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/>
      <circle cx="12" cy="12" r="3"/>
    </svg>
  </button>
</div>

<p class="subtle">Code expires after 10 minutes. Password must be at least 10 characters.</p>
<button type="submit">Reset password</button>
</form>

<p id="message" class="auth-message" role="status" aria-live="polite"></p>
</section>

<p class="auth-foot"><a href="login.php">Back to sign in</a></p>
</main>

<script>
const msg = document.getElementById('message');
const password = document.getElementById('newPassword');
const togglePassword = document.getElementById('togglePassword');
const eyeIcon = document.getElementById('eyeIcon');

togglePassword.addEventListener('click', () => {
  const show = password.type === 'password';

  password.type = show ? 'text' : 'password';
  togglePassword.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  togglePassword.setAttribute('aria-pressed', String(show));

  eyeIcon.innerHTML = show
    ? '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 002.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0112 5c6.4 0 10 7 10 7a15.5 15.5 0 01-3 3.8"/><path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.6"/>'
    : '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>';
});

async function send(action, form) {
  const fields = Object.fromEntries(new FormData(form));

  const r = await fetch('auth_api.php?action=' + action, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(fields)
  });

  const d = await r.json();

  if (!r.ok || !d.success) {
    throw Error(d.message || 'Request failed');
  }

  return d;
}

document.getElementById('requestOtp').addEventListener('submit', async e => {
  e.preventDefault();
  msg.style.color = '#6557f5';
  msg.textContent = 'Sending code…';

  try {
    const d = await send('forgot', e.target);
    msg.style.color = '#16804a';
    msg.textContent = d.message || 'If the account is eligible, instructions will be sent.';
    document.getElementById('resetEmail').value = e.target.email.value;
  } catch (x) {
    msg.style.color = '#a32643';
    msg.textContent = x.message || 'Could not send the code. Please try again.';
  }
});

document.getElementById('resetWithOtp').addEventListener('submit', async e => {
  e.preventDefault();
  msg.style.color = '#6557f5';
  msg.textContent = 'Checking code…';

  try {
    const d = await send('reset_password', e.target);
    msg.style.color = '#16804a';
    msg.textContent = d.message || 'Password reset successfully. You can now sign in.';
    e.target.reset();
    password.type = 'password';
    togglePassword.setAttribute('aria-label', 'Show password');
    togglePassword.setAttribute('aria-pressed', 'false');
    eyeIcon.innerHTML = '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>';
  } catch (x) {
    msg.style.color = '#a32643';
    msg.textContent = x.message || 'Password reset failed. Please try again.';
  }
});
</script>
</body>
</html>
