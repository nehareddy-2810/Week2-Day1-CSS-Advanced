// 1
const add = (a, b = 0) => a + b;

// 2
const multiply = (a, b = 1) => a * b;

// 3
const square = n => n * n;

// 4
const greet = (name = "Student") => `Hello ${name}`;

// 5
const evenOrOdd = n => n % 2 === 0 ? "Even" : "Odd";

// 6
const sumAll = (...numbers) =>
numbers.reduce((a, b) => a + b, 0);

// 7
const mergeArrays = (a, b) => [...a, ...b];

// 8
const copyObject = obj => ({...obj});

// 9
const largest = (...numbers) =>
Math.max(...numbers);

// 10
const average = (...numbers) =>
sumAll(...numbers)/numbers.length;

function runFunctions(){

console.log(add(10,20));

console.log(multiply(5,5));

console.log(square(8));

console.log(greet("Neha"));

console.log(evenOrOdd(7));

console.log(sumAll(10,20,30));

console.log(mergeArrays([1,2],[3,4]));

console.log(copyObject({name:"Neha",age:22}));

console.log(largest(8,4,20,15));

console.log(average(10,20,30));

document.getElementById("output").innerHTML =
"<h2>Check Console (F12) for Function Output</h2>";

}

// Promise Example

function promiseExample(){

return new Promise((resolve,reject)=>{

let success=true;

if(success){

resolve("Promise Resolved");

}

else{

reject("Promise Rejected");

}

});

}

promiseExample()

.then(result=>console.log(result))

.catch(error=>console.log(error));

// Async Await

async function asyncExample(){

try{

let result=await promiseExample();

console.log(result);

}catch(error){

console.log(error);

}

}

asyncExample();

// Fetch API

async function getPosts(){

try{

const response=await fetch("https://jsonplaceholder.typicode.com/posts");

const data=await response.json();

let output="<h2>Posts</h2>";

data.slice(0,5).forEach(post=>{

output+=`<p><b>${post.title}</b></p>`;

});

document.getElementById("output").innerHTML=output;

}

catch(error){

document.getElementById("output").innerHTML="Error Loading Data";

console.log(error);

}

}