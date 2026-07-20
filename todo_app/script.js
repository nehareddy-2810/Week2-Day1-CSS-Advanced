// Select elements
const taskInput = document.querySelector("#taskInput");
const addBtn = document.querySelector("#addBtn");
const taskList = document.querySelector("#taskList");

// Load tasks from localStorage
let tasks = JSON.parse(localStorage.getItem("tasks")) || [];

// Save tasks
function saveTasks() {
    localStorage.setItem("tasks", JSON.stringify(tasks));
}

// Display tasks
function renderTasks() {

    taskList.innerHTML = "";

    tasks.forEach((task, index) => {

        const li = document.createElement("li");

        li.dataset.index = index;

        if (task.completed) {
            li.classList.add("completed");
        }

        li.innerHTML = `
            <span>${task.text}</span>
            <button class="delete">Delete</button>
        `;

        taskList.appendChild(li);

    });

}

// Add Task
addBtn.addEventListener("click", () => {

    const text = taskInput.value.trim();

    if (text === "") {
        alert("Please enter a task");
        return;
    }

    tasks.push({
        text: text,
        completed: false
    });

    saveTasks();
    renderTasks();

    taskInput.value = "";

});

// Event Delegation
taskList.addEventListener("click", (e) => {

    const li = e.target.closest("li");

    if (!li) return;

    const index = li.dataset.index;

    // Delete task
    if (e.target.classList.contains("delete")) {

        tasks.splice(index, 1);

    }
    // Complete task
    else {

        tasks[index].completed = !tasks[index].completed;

    }

    saveTasks();
    renderTasks();

});

// Display saved tasks on page load
renderTasks();