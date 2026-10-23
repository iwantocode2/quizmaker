
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign in — QuizMaker</title>
<link rel="stylesheet" href="login.css">

<style>
.auth-wrap {
  max-width: 430px;
  margin: 7vh auto;
  padding: 22px;
}

.auth-card {
  background: #fff;
  border: 1px solid #e0e8fa;
  border-radius: 22px;
  padding: 30px;
  box-shadow: 0 10px 35px #24397310;
}

.brand {
  font-size: 14px;
  font-weight: 800;
  color: #6557f5;
  letter-spacing: .08em;
}

.auth-card h1 {
  margin: 12px 0 8px;
  font-size: 30px;
}

.auth-card label {
  display: block;
  font-weight: 650;
  margin: 16px 0 7px;
}

.auth-card input {
  box-sizing: border-box;
  width: 100%;
  padding: 13px;
  border: 1px solid #dce4f5;
  border-radius: 10px;
  font: inherit;
}

.password-wrapper {
  position: relative;
  width: 100%;
}

.password-wrapper input {
  padding-right: 48px;
}

.auth-card .password-toggle {
  position: absolute;
  top: 50%;
  right: 7px;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  margin: 0;
  padding: 7px;
  border: 0;
  border-radius: 8px;
  background: transparent;
  color: #68748d;
  cursor: pointer;
}

.auth-card .password-toggle:hover {
  background: #f0efff;
  color: #6557f5;
}

.password-toggle svg {
  width: 21px;
  height: 21px;
  fill: none;
  stroke: currentColor;
  stroke-width: 1.8;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.auth-card form > button[type="submit"] {
  margin-top: 18px;
  width: 100%;
  padding: 13px 16px;
  border: 0;
  border-radius: 10px;
  background: #6557f5;
  color: white;
  font-weight: 800;
  cursor: pointer;
}

.auth-card form > button[type="submit"]:hover {
  background: #5548df;
}

.auth-card p {
  line-height: 1.5;
}

.auth-links {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-top: 16px;
  flex-wrap: wrap;
}

.auth-links a,
.auth-foot a {
  color: #5548df;
  text-decoration: none;
  font-weight: 650;
}

.auth-message {
  min-height: 22px;
  color: #a32643;
}

.auth-foot {
  text-align: center;
  margin-top: 22px;
}

@media (max-width: 480px) {
  .auth-wrap {
    margin: 3vh auto;
    padding: 16px;
  }

  .auth-card {
    padding: 24px;
  }
}
</style>
</head>

<body>
<main class="auth-wrap">
  <section class="auth-card">
    <div class="brand">QUIZMAKER</div>

    <h1>Welcome back</h1>
    <p>Sign in to open your private quiz dashboard.</p>

    <form id="login">
      <label for="email">Email</label>
      <input
        id="email"
        type="email"
        name="email"
        required
        autocomplete="email"
      >

      <label for="password">Password</label>
      <div class="password-wrapper">
        <input
          id="password"
          type="password"
          name="password"
          required
          autocomplete="current-password"
        >

        <button
          type="button"
          id="togglePassword"
          class="password-toggle"
          aria-label="Show password"
          aria-pressed="false"
          title="Show password"
        >
          <svg
            id="passwordEye"
            viewBox="0 0 24 24"
            aria-hidden="true"
          >
            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </button>
      </div>

      <button type="submit">Sign in</button>
    </form>

    <div class="auth-links">
      <a href="forgot_password.php">Forgot password?</a>
      <a href="create_account.php">Create account</a>
    </div>

    <p id="message" class="auth-message" role="status" aria-live="polite"></p>
  </section>

  <p class="auth-foot">
    Your quizzes stay private to your account unless you share them.
  </p>
</main>

<script>
const msg = document.getElementById('message');
const loginForm = document.getElementById('login');
const passwordInput = document.getElementById('password');
const togglePassword = document.getElementById('togglePassword');
const passwordEye = document.getElementById('passwordEye');

togglePassword.addEventListener('click', () => {
  const showPassword = passwordInput.type === 'password';

  passwordInput.type = showPassword ? 'text' : 'password';

  togglePassword.setAttribute('aria-pressed', String(showPassword));
  togglePassword.setAttribute(
    'aria-label',
    showPassword ? 'Hide password' : 'Show password'
  );
  togglePassword.title = showPassword ? 'Hide password' : 'Show password';

  passwordEye.innerHTML = showPassword
    ? '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.9"/><path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.8 10.8 0 0 0 4.1-.8"/>'
    : '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>';
});

loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  msg.textContent = 'Signing in…';

  try {
    const fields = Object.fromEntries(new FormData(e.target));

    const r = await fetch('auth_api.php?action=login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(fields)
    });

    const d = await r.json();

    if (!r.ok || !d.success) {
      throw Error(d.message || 'Sign in failed');
    }

    location.href = 'index.html';
  } catch (x) {
    msg.textContent = x.message || 'Unable to sign in. Please try again.';
  }
});

fetch('auth_api.php?action=status')
  .then(r => r.json())
  .then(d => {
    if (d.logged_in) {
      location.href = 'index.html';
    }
  })
  .catch(() => {});
</script>
</body>
</html>
```