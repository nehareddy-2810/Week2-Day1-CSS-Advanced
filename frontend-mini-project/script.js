const questionElement = document.getElementById("question");
const answersElement = document.getElementById("answers");
const nextBtn = document.getElementById("nextBtn");

let currentQuestion = {};

// Fetch a question from the API
async function loadQuestion() {

    nextBtn.style.display = "none";
    answersElement.innerHTML = "";
    questionElement.innerHTML = "Loading...";

    try {

        const response = await fetch("https://opentdb.com/api.php?amount=1&type=multiple");
        const data = await response.json();

        currentQuestion = data.results[0];

        questionElement.innerHTML = currentQuestion.question;

        const answers = [
            ...currentQuestion.incorrect_answers,
            currentQuestion.correct_answer
        ];

        // Shuffle answers
        answers.sort(() => Math.random() - 0.5);

        answers.forEach(answer => {

            const button = document.createElement("button");

            button.innerHTML = answer;
            button.classList.add("answer-btn");

            button.addEventListener("click", () => checkAnswer(button, answer));

            answersElement.appendChild(button);

        });

    } catch (error) {

        questionElement.innerHTML = "Failed to load question.";

    }

}

// Check answer
function checkAnswer(button, selectedAnswer) {

    const buttons = document.querySelectorAll(".answer-btn");

    buttons.forEach(btn => {

        btn.disabled = true;

        if (btn.innerHTML === currentQuestion.correct_answer) {

            btn.classList.add("correct");

        }

    });

    if (selectedAnswer !== currentQuestion.correct_answer) {

        button.classList.add("wrong");

    }

    nextBtn.style.display = "inline-block";

}

// Next question
nextBtn.addEventListener("click", () => {

    loadQuestion();

});

// Load first question
loadQuestion();