// Employee dataset
const employees = [
    { id: 1, name: "Neha", salary: 30000 },
    { id: 2, name: "Rahul", salary: 45000 },
    { id: 3, name: "Anjali", salary: 60000 },
    { id: 4, name: "Kiran", salary: 25000 }
];

// Arrow Function
const displayEmployees = () => {

    // filter()
    const filtered = employees.filter(emp => emp.salary >= 30000);

    // map()
    const names = filtered.map(emp => emp.name);

    // reduce()
    const totalSalary = filtered.reduce((sum, emp) => sum + emp.salary, 0);

    // Destructuring
    const { id, name, salary } = filtered[0];

    // Template Literals
    document.getElementById("output").innerHTML = `
        <h2>Employee Details</h2>

        <p><strong>Names:</strong> ${names.join(", ")}</p>

        <p><strong>Total Salary:</strong> ₹${totalSalary}</p>

        <hr>

        <h3>First Employee</h3>

        <p>ID: ${id}</p>

        <p>Name: ${name}</p>

        <p>Salary: ₹${salary}</p>
    `;
};

// Event Listener
document.getElementById("showData").addEventListener("click", displayEmployees);