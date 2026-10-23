<?php
require_once __DIR__.'/connect.php';
require_once __DIR__.'/auth.php';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#6655f5"><title>AI Quiz Maker — QuizMaker</title>
<link rel="stylesheet" href="style.css">
<style>
.ai-wrap{max-width:1000px;margin:0 auto;padding:34px 22px 60px}.ai-top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:28px}.ai-top a{color:#6655f5;text-decoration:none;font-weight:700}.ai-hero{background:linear-gradient(130deg,#f0edff,#f7f8ff);border:1px solid #e3ddff;border-radius:24px;padding:32px;margin-bottom:22px}.ai-hero .eyebrow{color:#6655f5;font-weight:800;letter-spacing:.12em;font-size:12px}.ai-hero h1{font-size:clamp(28px,4vw,42px);margin:10px 0}.ai-hero p{color:#606078;max-width:650px;line-height:1.6}.ai-form,.ai-output{background:#fff;border:1px solid #e7e7ef;border-radius:20px;padding:24px;margin-bottom:22px;box-shadow:0 8px 26px #25234b0a}.ai-form label{display:block;font-weight:700;margin-bottom:8px}.ai-form input,.ai-form select{width:100%;padding:13px 14px;border:1px solid #dcdce8;border-radius:10px;font:inherit;margin-bottom:18px}.ai-form button,.ai-use{border:0;border-radius:11px;padding:13px 18px;background:#6655f5;color:white;font-weight:800;cursor:pointer}.ai-form button:disabled{opacity:.6;cursor:wait}.ai-question{border:1px solid #e8e8f1;border-radius:14px;padding:16px;margin:14px 0}.ai-question h3{margin:0 0 12px;font-size:16px}.ai-question li{margin:6px 0;color:#48485d}.ai-correct{font-weight:700;color:#198754}.ai-status{margin:14px 0;color:#55556b}.ai-actions{display:flex;gap:12px;align-items:center;flex-wrap:wrap}.ai-secondary{display:inline-block;padding:12px 16px;border:1px solid #ddd9f8;border-radius:11px;color:#6655f5;text-decoration:none;font-weight:700}
</style>
</head>
<body>
<script src="auth-gate.js" defer></script>
<div class="ai-wrap">
  <div class="ai-top"><a href="index.html">← Back to QuizMaker dashboard</a><div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap"><span class="workspace-pill"><span class="workspace-dot"></span> Private workspace</span><button type="button" id="logoutBtn" class="ai-secondary" style="cursor:pointer;background:#fff">Log out</button></div></div>
  <section class="ai-hero"><div class="eyebrow">✦ AI-POWERED QUIZ CREATION</div><h1>Your AI Quiz Maker</h1><p>Generate multiple-choice questions from a topic, review the answer key, then send the draft to your quiz editor to save and share it.</p></section>
  <form class="ai-form" id="aiForm"><h2>Generate a new quiz</h2><label for="topic">What should your quiz be about?</label><input id="topic" name="topic" maxlength="300" placeholder="e.g. The solar system, Philippine history, biology" required><label for="count">Number of questions</label><select id="count" name="count"><option value="5">5 questions</option><option value="10">10 questions</option><option value="15">15 questions</option></select><button id="generateBtn" type="submit">✦ Generate Quiz with AI</button><p class="ai-status" id="status" role="status">Your generated draft is private to your account until you choose to save or share it.</p></form>
  <section class="ai-output" id="output" hidden><h2 id="outputTitle">Generated questions</h2><div id="questions"></div><div class="ai-actions"><button class="ai-use" id="useDraft" type="button">Use these questions in my quiz editor →</button><a class="ai-secondary" href="index.html">Cancel</a></div></section>
</div>
<script>
document.getElementById('logoutBtn').addEventListener('click',async()=>{const b=document.getElementById('logoutBtn');b.disabled=true;b.textContent='Logging out…';try{await fetch('auth_api.php?action=logout',{method:'POST',credentials:'same-origin'});}finally{location.href='login.php';}});
let generatedQuestions=[];
const form=document.getElementById('aiForm'), btn=document.getElementById('generateBtn'), statusEl=document.getElementById('status'), output=document.getElementById('output'), questionsEl=document.getElementById('questions');
form.addEventListener('submit', async e=>{e.preventDefault();const topic=document.getElementById('topic').value.trim();const count=Number(document.getElementById('count').value);if(!topic)return;btn.disabled=true;btn.textContent='Generating…';statusEl.textContent='Asking the AI to create your questions. This may take a moment.';output.hidden=true;try{const response=await fetch('ai_generate.php',{method:'POST',headers:{'Content-Type':'application/json'},credentials:'same-origin',body:JSON.stringify({topic,count})});const data=await response.json();if(!response.ok||!data.success||!Array.isArray(data.questions))throw new Error(data.message||'AI generation failed.');generatedQuestions=data.questions;questionsEl.innerHTML='';generatedQuestions.forEach((q,i)=>{const card=document.createElement('article');card.className='ai-question';const title=document.createElement('h3');title.textContent=(i+1)+'. '+q.text;card.appendChild(title);const list=document.createElement('ol');list.type='A';['A','B','C','D'].forEach(letter=>{const li=document.createElement('li');li.textContent=q.answers[letter];if(letter===q.correct){li.className='ai-correct';li.textContent+=' ✓ Correct answer';}list.appendChild(li)});card.appendChild(list);questionsEl.appendChild(card)});document.getElementById('outputTitle').textContent=topic+' — Generated Quiz';output.hidden=false;statusEl.textContent='Generated '+generatedQuestions.length+' questions. Review them before using or saving.';output.scrollIntoView({behavior:'smooth',block:'start'});}catch(err){statusEl.textContent=err.message||'Could not generate the quiz. Check your Gemini API configuration.';}finally{btn.disabled=false;btn.textContent='✦ Generate Quiz with AI';}});
document.getElementById('useDraft').addEventListener('click',()=>{
  if(!generatedQuestions.length){statusEl.textContent='Generate a quiz first, then use the questions in your editor.';return;}
  const draft=JSON.stringify({title:document.getElementById('topic').value.trim()+' Quiz',questions:generatedQuestions});
  let stored=false;
  // Keep sessionStorage for the normal flow, with localStorage as a mobile-browser fallback.
  try{sessionStorage.setItem('quizmaker_ai_draft',draft);stored=true;}catch(e){console.warn('sessionStorage unavailable; trying localStorage',e);}
  try{localStorage.setItem('quizmaker_ai_draft',draft);stored=true;}catch(e){console.warn('localStorage unavailable',e);}
  if(!stored){statusEl.textContent='Your browser blocked local draft storage. Allow site data/storage for this website, then try again.';return;}
  const useButton=document.getElementById('useDraft');useButton.disabled=true;useButton.textContent='Opening quiz editor…';
  location.href=new URL('index.html#create',window.location.href).href;
});
</script>
</body></html>
