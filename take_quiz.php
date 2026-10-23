<?php
$token = $_GET["token"] ?? "";
if (!preg_match('/^[a-f0-9]{48}$/', $token)) { http_response_code(404); $invalid = true; } else { $invalid = false; }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#6557f5">
<title>Take a Quiz | QuizMaker</title>
<style>
:root{--purple:#6557f5;--purple2:#784aff;--ink:#16284f;--muted:#7487b0;--line:#e0e8fa;--bg:#f4f7ff}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:14px/1.55 Inter,system-ui,-apple-system,"Segoe UI",sans-serif}.top{height:66px;display:flex;align-items:center;justify-content:space-between;padding:0 max(18px,calc((100% - 940px)/2));background:#fff;border-bottom:1px solid var(--line);color:var(--purple);font-size:22px;font-weight:800}.top small{color:var(--muted);font-size:11px;font-weight:600}main{max-width:940px;margin:32px auto;padding:0 16px 35px}.panel{overflow:hidden;background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:0 10px 35px rgba(36,57,115,.06)}.hero{padding:27px 30px;color:white;background:linear-gradient(115deg,#536dff,#7650f5)}.eyebrow{font-size:10px;letter-spacing:.13em;font-weight:800}.hero h1{margin:6px 0;font-size:clamp(25px,4vw,34px);line-height:1.2}.hero p{margin:5px 0 0;color:#f1efff}.body{padding:25px 30px}.muted{color:var(--muted)}.count{font-size:11px;margin:10px 0 18px;color:var(--muted)}.participant-label{display:grid;gap:7px;font-weight:700;font-size:12px;margin:14px 0 18px}.participant-label input{width:100%;max-width:440px;padding:11px 12px;border:1px solid var(--line);border-radius:9px;font:inherit}.question{padding:19px 0;border-top:1px solid #edf0fa}.question h3{margin:0 0 12px;font-size:16px}.choice{display:flex;gap:11px;align-items:flex-start;border:1px solid var(--line);border-radius:10px;padding:11px 13px;margin:8px 0;cursor:pointer;font-size:13px}.choice:has(input:checked){border-color:var(--purple);background:#f4f2ff}.choice input{margin-top:5px;accent-color:var(--purple)}button{border:0;border-radius:9px;padding:12px 18px;background:linear-gradient(110deg,var(--purple),var(--purple2));color:white;font-weight:800;cursor:pointer}button:disabled{opacity:.6;cursor:wait}.timer{display:inline-flex;align-items:center;gap:8px;margin:0 0 16px;padding:7px 10px;border-radius:8px;background:#f0efff;color:#5c4de9;font-size:12px;font-weight:800}.timer.warning{background:#fff0f2;color:#ca3656}.result{margin-top:18px;padding:18px;border-radius:12px;background:#f0edff}.error{padding:20px;color:#a32643;background:#fff0f2;border-radius:12px}@media(max-width:600px){.top{height:58px;font-size:19px}.top small{font-size:10px}main{margin:16px auto}.hero,.body{padding:21px 18px}}
</style>
</head>
<body>
<header class="top"><span>QuizMaker</span><small>Shared quiz · Public access</small></header>
<main><section class="panel" id="app"><div class="body"><p class="muted">Loading quiz…</p></div></section></main>
<script>
const token = <?= json_encode($token) ?>;
const invalid = <?= $invalid ? 'true' : 'false' ?>;
const app = document.getElementById('app');
const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
async function init() {
  if (invalid) { app.innerHTML = '<div class="body"><div class="error">This share link is invalid.</div></div>'; return; }
  try {
    const response = await fetch('public_quiz_api.php?token=' + encodeURIComponent(token));
    const data = await response.json();
    if (!response.ok || !data.success) throw new Error(data.message || 'Quiz unavailable.');
    const quiz = data.quiz;
    const timeLimit = Math.max(0, Number(quiz.time_limit_seconds) || 0);
    const durationLabel = timeLimit ? (timeLimit % 60 === 0 ? (timeLimit / 60) + ' minute(s)' : Math.floor(timeLimit / 60) + ' min ' + (timeLimit % 60) + ' sec') : 'No time limit';
    app.innerHTML = '<div class="hero"><span class="eyebrow">SHARED QUIZ</span><h1>' + esc(quiz.title) + '</h1><p>' + esc(quiz.description || 'Enter your name if you want, then press Start Quiz when you are ready.') + '</p></div><div class="body"><p class="count">' + quiz.questions.length + ' question(s) · Time limit: ' + esc(durationLabel) + '</p><form id="quiz-form"><label class="participant-label" for="participant-name">Your name (optional)<input id="participant-name" name="participant_name" type="text" maxlength="120" placeholder="Enter your name (optional)"></label><div id="timer-banner" class="timer" hidden>Time remaining: <span id="timer-value"></span></div><div id="questions-area" hidden>' + quiz.questions.map((item, i) => '<section class="question"><h3>' + (i + 1) + '. ' + esc(item.text) + '</h3>' + item.answers.map(answer => '<label class="choice"><input type="radio" name="q-' + Number(item.id) + '" value="' + esc(answer.letter) + '"><span><strong>' + esc(answer.letter) + '.</strong> ' + esc(answer.text) + '</span></label>').join('') + '</section>').join('') + '</div><button id="start-button" type="button">Start Quiz</button> <button id="submit" type="submit" hidden>Submit answers</button><div id="result" aria-live="polite"></div></form></div>';
    const form = document.getElementById('quiz-form');
    const startButton = document.getElementById('start-button');
    const submitButton = document.getElementById('submit');
    const questionsArea = document.getElementById('questions-area');
    const timerBanner = document.getElementById('timer-banner');
    let remaining = timeLimit;
    let timerId = null;
    let started = false;
    let isSubmitting = false;
    function displayTimer() {
      const mins = Math.floor(remaining / 60);
      const secs = remaining % 60;
      document.getElementById('timer-value').textContent = String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');
      if (remaining <= 10) timerBanner.classList.add('warning');
    }
    function beginQuiz() {
      if (started) return;
      started = true;
      questionsArea.hidden = false;
      startButton.hidden = true;
      submitButton.hidden = false;
      if (timeLimit > 0) {
        timerBanner.hidden = false;
        displayTimer();
        timerId = setInterval(() => {
          remaining--;
          displayTimer();
          if (remaining <= 0) {
            clearInterval(timerId);
            document.getElementById('timer-value').textContent = '00:00 — submitting…';
            form.requestSubmit();
          }
        }, 1000);
      }
    }
    startButton.addEventListener('click', beginQuiz);
    form.addEventListener('submit', async event => {
      event.preventDefault();
      if (!started || isSubmitting) return;
      isSubmitting = true;
      if (timerId) clearInterval(timerId);
      submitButton.disabled = true;
      submitButton.textContent = 'Checking answers…';
      startButton.disabled = true;
      const answers = {};
      quiz.questions.forEach(item => { const selected = form.querySelector('input[name="q-' + Number(item.id) + '"]:checked'); if (selected) answers[item.id] = selected.value; });
      try {
        const scoredResponse = await fetch('public_quiz_api.php?token=' + encodeURIComponent(token), { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ answers, participant_name: document.getElementById('participant-name').value.trim() }) });
        const scored = await scoredResponse.json();
        if (!scoredResponse.ok || !scored.success) throw new Error(scored.message || 'Could not score quiz.');
        document.getElementById('result').innerHTML = '<div class="result"><h2>Quiz submitted!</h2><p>Your score: <strong>' + Number(scored.score) + ' / ' + Number(scored.total) + '</strong> (' + Number(scored.percentage) + '%)</p><p class="muted">Your result has been saved.</p></div>';
        form.querySelectorAll('input').forEach(input => input.disabled = true);
        submitButton.remove();
      } catch (error) {
        alert(error.message);
        isSubmitting = false;
        submitButton.disabled = false;
        submitButton.textContent = 'Submit answers';
        startButton.disabled = false;
      }
    });
  } catch (error) { app.innerHTML = '<div class="body"><div class="error">' + esc(error.message) + '</div></div>'; }
}
init();
</script>
</body>
</html>
