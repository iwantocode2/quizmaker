// QuizMaker dashboard: create, list, preview, edit, delete, share, and review results.
let questionNumber = 0;
let editingQuizId = null;
let quizLibrary = [];
let previewQuizData = null;
let previewQuestionIndex = 0;
let previewAnswers = {};
let previewRevealedAnswers = {};
let previewCompleted = false;
const $ = id => document.getElementById(id);

const escapeHtml = value => String(value ?? "").replace(/[&<>"']/g, char => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
}[char]));

function createQuestion(question = {}) {
    questionNumber += 1;
    const number = questionNumber;
    const answers = question.answers || {};
    const fieldset = document.createElement("fieldset");
    fieldset.className = "question";
    fieldset.innerHTML = `
        <legend>Question ${number}</legend>
        <label for="question-text-${number}">Question</label>
        <textarea id="question-text-${number}" class="question-text" rows="3" maxlength="2000" placeholder="Enter your question">${escapeHtml(question.text || "")}</textarea>
        <p class="answer-label">Answer choices — select the radio button beside the correct answer</p>
        ${["A", "B", "C", "D"].map(letter => `
            <label class="answer-choice" for="answer-${number}-${letter.toLowerCase()}">
                <input type="radio" name="correct-answer-${number}" value="${letter}" ${question.correct === letter ? "checked" : ""} aria-label="Mark answer ${letter} as correct">
                <span class="answer-letter">${letter}</span>
                <input id="answer-${number}-${letter.toLowerCase()}" type="text" class="answer-${letter.toLowerCase()}" maxlength="1000" placeholder="Answer ${letter}" value="${escapeHtml(answers[letter] || "")}">
            </label>`).join("")}
        <button type="button" class="remove-question" aria-label="Remove question ${number}">Remove question</button>
    `;
    fieldset.querySelector(".remove-question").addEventListener("click", () => {
        fieldset.remove();
        renumberQuestions();
    });
    $("questions").appendChild(fieldset);
    updateQuestionSummary();
}

function renumberQuestions() {
    const fieldsets = [...document.querySelectorAll("#questions .question")];
    questionNumber = fieldsets.length;
    fieldsets.forEach((fieldset, index) => {
        const number = index + 1;
        fieldset.querySelector("legend").textContent = `Question ${number}`;
        fieldset.querySelector("textarea").id = `question-text-${number}`;
        fieldset.querySelector("label[for^='question-text-']").htmlFor = `question-text-${number}`;
        fieldset.querySelectorAll("input[type='radio']").forEach(item => {
            item.name = `correct-answer-${number}`;
        });
        fieldset.querySelectorAll(".answer-choice").forEach((label, answerIndex) => {
            const letter = ["A", "B", "C", "D"][answerIndex];
            const textInput = label.querySelector("input[type='text']");
            textInput.id = `answer-${number}-${letter.toLowerCase()}`;
            label.htmlFor = textInput.id;
            label.querySelector(".answer-letter").textContent = letter;
        });
        fieldset.querySelector(".remove-question").setAttribute("aria-label", `Remove question ${number}`);
    });
    updateQuestionSummary();
}

function updateQuestionSummary() {
    const summary = document.querySelector("#question-editor summary");
    if (summary) {
        summary.innerHTML = `Edit question details (${document.querySelectorAll("#questions .question").length}) <span class="summary-hint">question text, choices, correct answer</span>`;
    }
}

function resetForm() {
    $("quizForm").reset();
    $("quiz-title").value = "";
    $("quiz-description").value = "";
    $("quiz-time").value = "2";
    $("questions").innerHTML = "";
    $("question-editor").open = false;
    editingQuizId = null;
    questionNumber = 0;
    $("saveQuizButton").innerHTML = '<span aria-hidden="true">▣</span> Save Quiz';
    updateQuestionSummary();
    $("create-status").textContent = "";
}

async function saveQuiz() {
    const title = $("quiz-title").value.trim();
    const description = $("quiz-description").value.trim();
    const timeLimitMinutes = Math.max(
        0,
        Math.min(1440, Number.parseInt($("quiz-time").value || "0", 10) || 0)
    );
    const questionElements = [...document.querySelectorAll("#questions .question")];

    if (!title) {
        alert("Please enter a quiz title.");
        $("quiz-title").focus();
        return;
    }

    if (!questionElements.length) {
        alert("Please add at least one question.");
        $("question-editor").open = true;
        return;
    }

    const questions = [];

    for (const [index, element] of questionElements.entries()) {
        const text = element.querySelector(".question-text").value.trim();
        const correct = element.querySelector("input[type=radio]:checked");

        if (!text) {
            $("question-editor").open = true;
            alert(`Please enter Question ${index + 1}.`);
            element.querySelector(".question-text").focus();
            return;
        }

        if (!correct) {
            $("question-editor").open = true;
            alert(`Please select the correct answer for Question ${index + 1}.`);
            return;
        }

        const answers = {};

        for (const letter of ["A", "B", "C", "D"]) {
            const answerText = element.querySelector(`.answer-${letter.toLowerCase()}`).value.trim();

            if (!answerText) {
                $("question-editor").open = true;
                alert(`Please fill in answer ${letter} for Question ${index + 1}.`);
                element.querySelector(`.answer-${letter.toLowerCase()}`).focus();
                return;
            }

            answers[letter] = answerText;
        }

        questions.push({ text, correct: correct.value, answers });
    }

    const wasEditing = Boolean(editingQuizId);
    const data = {
        title,
        description,
        time_limit_seconds: timeLimitMinutes * 60,
        questions
    };

    const url = wasEditing ? "update_quiz.php" : "save.php";
    if (wasEditing) data.id = editingQuizId;

    const button = $("saveQuizButton");
    button.disabled = true;
    button.textContent = wasEditing ? "Updating…" : "Saving…";

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not save the quiz.");
        }

        resetForm();
        $("create-status").textContent = wasEditing
            ? "Quiz updated successfully."
            : "Quiz saved successfully.";

        await loadQuizzes();
    } catch (error) {
        console.error(error);
        alert(error.message || "Could not connect to the server. Check your PHP and database setup.");
    } finally {
        button.disabled = false;
        if (!editingQuizId) {
            button.innerHTML = '<span aria-hidden="true">▣</span> Save Quiz';
        }
    }
}

function formatDuration(seconds) {
    const total = Math.max(0, Number(seconds) || 0);
    if (!total) return "No time limit";
    if (total % 60 === 0) return `${total / 60} min`;
    return `${Math.floor(total / 60)} min ${total % 60} sec`;
}

function formatDate(value) {
    if (!value) return "Date unavailable";

    const date = new Date(String(value).replace(" ", "T"));

    if (Number.isNaN(date.getTime())) {
        return String(value).slice(0, 10);
    }

    return date.toLocaleDateString(undefined, {
        month: "short",
        day: "numeric",
        year: "numeric"
    });
}

function renderQuizList() {
    const list = $("quiz-list");
    const query = ($("quiz-search").value || "").trim().toLowerCase();

    const shown = quizLibrary.filter(quiz =>
        `${quiz.title || ""} ${quiz.description || ""}`.toLowerCase().includes(query)
    );

    $("quiz-count").textContent = quizLibrary.length;

    if (!quizLibrary.length) {
        list.innerHTML = `<div class="empty-state">
            <span class="empty-illustration" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <path d="M5 4.5A2.5 2.5 0 0 1 7.5 2H20v17H7.5A2.5 2.5 0 0 0 5 21.5z"/>
                    <path d="M5 5v16.5M9 7h7M9 11h7"/>
                </svg>
            </span>
            <strong>Your quiz library is ready</strong>
            <p>Create your first quiz and it will appear here.</p>
            <a class="empty-create-link" href="#create">＋ Create your first quiz</a>
        </div>`;
        return;
    }

    if (!shown.length) {
        list.innerHTML = '<div class="empty-state"><strong>No matching quizzes</strong><p>Try another search term.</p></div>';
        return;
    }

    list.innerHTML = shown.map((quiz, index) => `
        <article class="quiz-row-card" data-search="${escapeHtml(`${quiz.title || ""} ${quiz.description || ""}`.toLowerCase())}">
            <div class="quiz-type-icon type-${index % 4}" aria-hidden="true">
                <svg viewBox="0 0 24 24">
                    <rect x="5" y="3" width="14" height="18" rx="3"/>
                    <path d="M9 8h6M9 12h6M9 16h4"/>
                </svg>
            </div>
            <div class="quiz-row-main">
                <h3 title="${escapeHtml(quiz.title)}">${escapeHtml(quiz.title)}</h3>
                <p>${Number(quiz.question_count || 0)} questions <span aria-hidden="true">·</span> ${escapeHtml(formatDuration(quiz.time_limit_seconds))}</p>
                <small>${escapeHtml(formatDate(quiz.created_at))}</small>
            </div>
            <div class="quiz-actions" aria-label="Actions for ${escapeHtml(quiz.title)}">
                <button type="button" class="quiz-more" aria-haspopup="menu" aria-expanded="false" title="More actions" aria-label="More actions for ${escapeHtml(quiz.title)}">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="5" r="1.5"/>
                        <circle cx="12" cy="12" r="1.5"/>
                        <circle cx="12" cy="19" r="1.5"/>
                    </svg>
                </button>
                <div class="quiz-menu" role="menu" hidden>
                    <button type="button" role="menuitem" class="view-quiz" data-id="${Number(quiz.id)}">Preview quiz</button>
                    <button type="button" role="menuitem" class="share-quiz" data-id="${Number(quiz.id)}">Share quiz link</button>
                    <button type="button" role="menuitem" class="edit-quiz" data-id="${Number(quiz.id)}">Edit quiz</button>
                    <button type="button" role="menuitem" class="delete-quiz" data-id="${Number(quiz.id)}">Delete quiz</button>
                </div>
            </div>
        </article>
    `).join("");
}

async function loadQuizzes() {
    const list = $("quiz-list");
    list.innerHTML = '<p class="loading-state">Loading quizzes…</p>';

    try {
        const response = await fetch("get_quizzes.php", {
            headers: { "Accept": "application/json" }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not load quizzes.");
        }

        quizLibrary = result.quizzes || [];
        renderQuizList();

        if (!previewQuizData && quizLibrary.length) {
            openPreview(quizLibrary[0].id, false);
        }

        if (previewQuizData && !quizLibrary.some(
            quiz => Number(quiz.id) === Number(previewQuizData.id)
        )) {
            clearPreview();
        }
    } catch (error) {
        console.error(error);
        list.innerHTML = `<div class="error-state">
            <strong>We couldn't load your quizzes</strong>
            <p>Check that your website's PHP endpoints and database are available.</p>
        </div>`;
    }
}

async function loadResults() {
    const body = $("results-body");

    try {
        const response = await fetch("get_results.php", {
            headers: { "Accept": "application/json" }
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not load results.");
        }

        const results = result.results || [];

        if (!results.length) {
            body.innerHTML = '<tr><td colspan="4" class="table-empty"><span class="table-empty-title">No quiz results yet</span><br>Share a quiz and participant scores will appear here.</td></tr>';
            $("results-side-title").textContent = "No results yet";
            $("results-side-copy").textContent = "Share a quiz and invite people to answer it. Their scores will appear here.";
            return;
        }

        body.innerHTML = results.map(item => `
            <tr>
                <td>${escapeHtml(item.quiz_title)}</td>
                <td>${escapeHtml(item.participant_name)}</td>
                <td><strong>${Number(item.score)} / ${Number(item.total_questions)}</strong>
                    <span class="result-percent">(${Number(item.percentage)}%)</span>
                </td>
                <td>${escapeHtml(formatDate(item.submitted_at))}</td>
            </tr>
        `).join("");

        const average = Math.round(
            results.reduce((total, item) => total + Number(item.percentage || 0), 0) / results.length
        );

        $("results-side-title").textContent =
            `${results.length} recent ${results.length === 1 ? "result" : "results"}`;

        $("results-side-copy").textContent =
            `Your recent submissions have an average score of ${average}%. Keep sharing quizzes to collect more responses.`;
    } catch (error) {
        console.error(error);
        body.innerHTML = '<tr><td colspan="4" class="table-empty"><span class="table-empty-title">Results are unavailable</span><br>Check that your PHP endpoints and database are available.</td></tr>';
    }
}

async function shareQuiz(id, button = null) {
    if (button) button.disabled = true;

    try {
        const response = await fetch("share_quiz.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not create a share link.");
        }

        const shareUrl = new URL(result.url, window.location.href).href;

        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(shareUrl);
            alert("Share link copied to clipboard.\n\n" + shareUrl);
        } else {
            prompt("Copy this share link and send it to your quiz takers:", shareUrl);
        }
    } catch (error) {
        console.error(error);
        alert(error.message || "Could not create a share link. Check your server setup.");
    } finally {
        if (button) button.disabled = false;
    }
}

function clearPreview() {
    previewQuizData = null;
    previewQuestionIndex = 0;
    previewAnswers = {};
    previewRevealedAnswers = {};
    previewCompleted = false;

    $("preview-step-label").textContent = "Choose a quiz";
    $("preview-progress-text").textContent = "0%";
    $("preview-progress-bar").style.width = "0%";

    $("preview-content").innerHTML = `
        <div class="preview-placeholder">
            <div class="placeholder-symbol">✓</div>
            <h3>Quiz Preview</h3>
            <p>Select Preview on a quiz card to see its questions here.</p>
        </div>`;
}

function renderPreview() {
    if (!previewQuizData) {
        clearPreview();
        return;
    }

    const questions = previewQuizData.questions || [];
    const total = questions.length;
    const percent = previewCompleted
        ? 100
        : (total ? Math.round(((previewQuestionIndex + 1) / total) * 100) : 0);

    $("preview-step-label").textContent = previewCompleted
        ? "Preview complete"
        : `Question ${previewQuestionIndex + 1} of ${total}`;

    $("preview-progress-text").textContent = `${percent}%`;
    $("preview-progress-bar").style.width = `${percent}%`;

    if (previewCompleted) {
        $("preview-content").innerHTML = `
            <div class="preview-complete">
                <div class="placeholder-symbol">✓</div>
                <h3>Preview complete</h3>
                <p>You reached the end of “${escapeHtml(previewQuizData.title)}”. This preview does not submit answers or record a score.</p>
                <div class="preview-controls">
                    <button type="button" class="preview-restart">↻ Restart preview</button>
                    <button type="button" class="preview-share">↗ Share quiz</button>
                </div>
            </div>`;
        return;
    }

    if (!total) {
        $("preview-content").innerHTML = `
            <div class="preview-placeholder">
                <h3>This quiz has no questions yet</h3>
                <p>Edit the quiz and add some questions to preview it.</p>
            </div>`;
        return;
    }

    const item = questions[previewQuestionIndex];
    const selected = previewAnswers[item.id] || "";
    const answers = item.answers || {};
    const answerRevealed = Boolean(previewRevealedAnswers[item.id]);
    const correctLetter = String(item.correct || "").toUpperCase();
    const correctAnswerText = answers[correctLetter] || "";

    $("preview-content").innerHTML = `
        <span class="preview-tag">QUIZ</span>
        <h3 class="preview-title">${escapeHtml(previewQuizData.title)}</h3>
        <p class="preview-question">${escapeHtml(item.text)}</p>
        <div class="preview-options">
            ${["A", "B", "C", "D"].map(letter => `
                <button type="button" class="preview-option ${selected === letter ? "selected" : ""}" data-letter="${letter}" aria-pressed="${selected === letter}">
                    <span class="option-dot">${selected === letter ? "✓" : ""}</span>
                    <span><strong>${letter}.</strong> ${escapeHtml(answers[letter] || "")}</span>
                </button>
            `).join("")}
        </div>
        <div class="preview-answer-controls">
            <button type="button" class="preview-reveal-answer" aria-expanded="${answerRevealed}">${answerRevealed ? "Hide Answer" : "Reveal Answer"}</button>
        </div>
        ${answerRevealed ? `
            <div class="preview-correct-answer" role="status" aria-live="polite">
                <strong>Correct answer: ${escapeHtml(correctLetter || "Not set")}${correctAnswerText ? ". " + escapeHtml(correctAnswerText) : ""}</strong>
            </div>` : ""}
        <div class="preview-controls">
            <button type="button" class="preview-prev" ${previewQuestionIndex === 0 ? "disabled" : ""}>← Previous</button>
            <button type="button" class="preview-next">${previewQuestionIndex === total - 1 ? "Finish Preview ✓" : "Next →"}</button>
        </div>
        <p class="preview-note">Preview mode · Answers are not graded here.</p>`;
}

async function openPreview(id, scroll = true) {
    try {
        const response = await fetch(`get_quiz.php?id=${encodeURIComponent(id)}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not load quiz.");
        }

        previewQuizData = result.quiz;
        previewQuestionIndex = 0;
        previewAnswers = {};
        previewRevealedAnswers = {};
        previewCompleted = false;

        renderPreview();

        if (scroll && window.innerWidth <= 900) {
            $("preview-panel-title").scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }
    } catch (error) {
        console.error(error);
        alert(error.message || "Could not load quiz preview.");
    }
}

async function handleQuizCardClick(event) {
    const button = event.target.closest("button[data-id]");
    if (!button) return;

    const id = Number(button.dataset.id);
    if (!id) return;

    const openMenu = button.closest(".quiz-menu");

    if (openMenu) {
        openMenu.hidden = true;
        const trigger = openMenu.previousElementSibling;
        if (trigger) trigger.setAttribute("aria-expanded", "false");
    }

    if (button.classList.contains("share-quiz")) {
        await shareQuiz(id, button);
        return;
    }

    if (button.classList.contains("delete-quiz")) {
        if (!confirm("Delete this quiz and its questions? Recorded attempts for this quiz will also be removed.")) {
            return;
        }

        button.disabled = true;

        try {
            const response = await fetch("delete_quiz.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || "Could not delete the quiz.");
            }

            if (previewQuizData && Number(previewQuizData.id) === id) {
                clearPreview();
            }

            await loadQuizzes();
            await loadResults();
        } catch (error) {
            console.error(error);
            alert(error.message || "Could not delete quiz.");
        }

        return;
    }

    if (button.classList.contains("view-quiz")) {
        await openPreview(id);
        return;
    }

    try {
        const response = await fetch(`get_quiz.php?id=${encodeURIComponent(id)}`);
        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || "Could not load quiz.");
        }

        const quiz = result.quiz;
        editingQuizId = Number(quiz.id);

        $("quiz-title").value = quiz.title || "";
        $("quiz-description").value = quiz.description || "";
        $("quiz-time").value = quiz.time_limit_seconds == null
            ? 2
            : Math.ceil(Number(quiz.time_limit_seconds) / 60);

        $("questions").innerHTML = "";
        questionNumber = 0;

        (quiz.questions || []).forEach(createQuestion);

        $("question-editor").open = true;
        $("saveQuizButton").innerHTML = '<span aria-hidden="true">▣</span> Update Quiz';
        $("create-status").textContent = `Editing “${quiz.title}”. Save your changes when done.`;

        $("create").scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

        $("quiz-title").focus({ preventScroll: true });
    } catch (error) {
        console.error(error);
        alert(error.message || "Could not load quiz.");
    }
}

async function generateQuizWithAI() {
    const topic = prompt("What topic should the AI quiz cover?");
    if (!topic || !topic.trim()) return;

    const countRaw = prompt("How many questions? Enter 5, 10, or 15.", "5");
    const count = [5, 10, 15].includes(Number(countRaw)) ? Number(countRaw) : 5;

    const button = $("ai-generate-button");
    if (!button) return;

    const originalLabel = button.textContent;
    button.disabled = true;
    button.textContent = "Generating…";

    try {
        const response = await fetch("ai_generate.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ topic: topic.trim(), count })
        });

        const data = await response.json();

        if (!response.ok || !data.success || !Array.isArray(data.questions)) {
            throw new Error(data.message || "AI generation failed.");
        }

        $("questions").innerHTML = "";
        questionNumber = 0;

        data.questions.forEach(q => createQuestion({
            text: q.text,
            correct: q.correct,
            answers: q.answers
        }));

        $("question-editor").open = true;
        $("create-status").textContent =
            "AI drafted " + data.questions.length + " questions. Review every answer before saving.";

        if (!$("quiz-title").value.trim()) {
            $("quiz-title").value = topic.trim() + " Quiz";
        }
    } catch (error) {
        console.error(error);
        alert(error.message || "Could not generate quiz. Check your AI API configuration.");
    } finally {
        button.disabled = false;
        button.textContent = originalLabel || "Generate with AI";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    $("addQuestionButton").addEventListener("click", () => {
        $("question-editor").open = true;
        createQuestion();
    });

    const aiGenerateButton = $("ai-generate-button");
    if (aiGenerateButton) {
        aiGenerateButton.addEventListener("click", generateQuizWithAI);
    }

    $("saveQuizButton").addEventListener("click", saveQuiz);
    $("resetQuizButton").addEventListener("click", resetForm);
    $("quiz-list").addEventListener("click", handleQuizCardClick);

    $("quiz-list").addEventListener("click", event => {
        const trigger = event.target.closest(".quiz-more");
        if (!trigger) return;

        const menu = trigger.nextElementSibling;
        const shouldOpen = Boolean(menu && menu.hidden);

        document.querySelectorAll(".quiz-menu").forEach(item => {
            item.hidden = true;
        });

        document.querySelectorAll(".quiz-more").forEach(item => {
            item.setAttribute("aria-expanded", "false");
        });

        if (menu) menu.hidden = !shouldOpen;
        trigger.setAttribute("aria-expanded", String(shouldOpen));
    });

    document.addEventListener("click", event => {
        if (event.target.closest(".quiz-actions")) return;

        document.querySelectorAll(".quiz-menu").forEach(item => {
            item.hidden = true;
        });

        document.querySelectorAll(".quiz-more").forEach(item => {
            item.setAttribute("aria-expanded", "false");
        });
    });

    $("quiz-search").addEventListener("input", renderQuizList);

    $("preview-exit").addEventListener("click", () => {
        clearPreview();

        if (window.innerWidth <= 900) {
            $("quizzes").scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }
    });

    $("preview-content").addEventListener("click", async event => {
        const button = event.target.closest("button");
        if (!button || !previewQuizData) return;

        if (button.classList.contains("preview-option")) {
            const item = previewQuizData.questions[previewQuestionIndex];
            previewAnswers[item.id] = button.dataset.letter;
            renderPreview();
        } else if (button.classList.contains("preview-reveal-answer")) {
            const item = previewQuizData.questions[previewQuestionIndex];
            previewRevealedAnswers[item.id] = !previewRevealedAnswers[item.id];
            renderPreview();
        } else if (button.classList.contains("preview-prev")) {
            previewQuestionIndex = Math.max(0, previewQuestionIndex - 1);
            renderPreview();
        } else if (button.classList.contains("preview-next")) {
            if (previewQuestionIndex < previewQuizData.questions.length - 1) {
                previewQuestionIndex++;
            } else {
                previewCompleted = true;
            }
            renderPreview();
        } else if (button.classList.contains("preview-restart")) {
            previewQuestionIndex = 0;
            previewAnswers = {};
            previewRevealedAnswers = {};
            previewCompleted = false;
            renderPreview();
        } else if (button.classList.contains("preview-share")) {
            await shareQuiz(Number(previewQuizData.id), button);
        }
    });

    document.querySelectorAll(".nav-link").forEach(link => {
        link.addEventListener("click", () => {
            document.querySelectorAll(".nav-link").forEach(item => {
                item.classList.toggle("active", item === link);
            });
        });
    });

    // Import a draft generated in the dedicated AI Quiz Maker dashboard.
    // Keep the draft until the editor has successfully populated.
    try {
        const aiDraftRaw = sessionStorage.getItem("quizmaker_ai_draft");

        if (aiDraftRaw) {
            const aiDraft = JSON.parse(aiDraftRaw);
            const titleInput = $("quiz-title");
            const questionsContainer = $("questions");
            const editor = $("question-editor");
            const status = $("create-status");

            if (!titleInput || !questionsContainer || !editor || !status) {
                throw new Error("Quiz editor fields were not found. Make sure index.html and script.js are uploaded together.");
            }

            if (!Array.isArray(aiDraft.questions) || aiDraft.questions.length === 0) {
                throw new Error("The saved AI draft has no questions. Return to AI Quiz Maker and generate the quiz again.");
            }

            questionsContainer.innerHTML = "";
            questionNumber = 0;

            aiDraft.questions.forEach(q => createQuestion({
                text: q.text,
                correct: q.correct,
                answers: q.answers
            }));

            titleInput.value = aiDraft.title || "AI Generated Quiz";
            editor.open = true;
            status.textContent = "AI-generated questions imported. Review every answer, then click Save Quiz.";

            sessionStorage.removeItem("quizmaker_ai_draft");
            history.replaceState(null, "", location.pathname + location.search + "#create");

            $("create").scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }
    } catch (error) {
        console.error("Could not import AI quiz draft:", error);

        const status = $("create-status");
        if (status) {
            status.textContent = "AI import failed: " +
                (error.message || "unknown error") +
                " Return to AI Quiz Maker and try again if needed.";
        }
    }

    updateQuestionSummary();
    loadQuizzes();

    const clearResultsButton = $("clear-results-button");

    if (clearResultsButton) {
        clearResultsButton.addEventListener("click", async () => {
            if (!confirm("Delete ALL saved quiz results? This cannot be undone.")) {
                return;
            }

            clearResultsButton.disabled = true;

            try {
                const response = await fetch("clear_results.php", {
                    method: "POST",
                    headers: { "Accept": "application/json" }
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(result.message || "Could not clear results.");
                }

                await loadResults();
                alert("All quiz results were cleared.");
            } catch (error) {
                console.error(error);
                alert(error.message || "Could not clear results.");
            } finally {
                clearResultsButton.disabled = false;
            }
        });
    }

    loadResults();
});


document.addEventListener("DOMContentLoaded", function () {
    const passwordInput = document.getElementById("password");
    const toggleButton = document.getElementById("togglePassword");
    const eyeIcon = document.getElementById("passwordEye");

    if (!passwordInput || !toggleButton) return;

    toggleButton.addEventListener("click", function () {
        const showingPassword = passwordInput.type === "password";

        passwordInput.type = showingPassword ? "text" : "password";

        toggleButton.setAttribute(
            "aria-label",
            showingPassword ? "Hide password" : "Show password"
        );

        toggleButton.setAttribute(
            "aria-pressed",
            String(showingPassword)
        );

        if (eyeIcon) {
            eyeIcon.innerHTML = showingPassword
                ? '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a14.5 14.5 0 0 1-3 3.8"/><path d="M6.2 6.2A16 16 0 0 0 2 12s3.6 7 10 7a10.5 10.5 0 0 0 4-.8"/>'
                : '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>';
        }
    });
});
