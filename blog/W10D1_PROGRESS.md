# W10D1 Progress — Resume Intelligence ATS Scorer

## Completed Work

* Added PDF resume text extraction using Python `pypdf`.
* Added Groq-based ATS evaluation logic.
* Added scoring for Keywords, Formatting, Experience, and Skills.
* Added total ATS score calculation.
* Added keyword gap detection.
* Added rewrite suggestions for Summary, Skills, Experience, and Projects.
* Added JSON response handling and error handling for missing API configuration and PDF extraction issues.

## Testing

* Verified that the resume PDF path is accessible.
* Verified that PDF text extraction works.
* Groq API integration was implemented.
* Final Groq scoring test is pending valid API-key configuration.

## Technologies Used

* Python
* pypdf
* Groq API
* python-dotenv
* JSON
* VS Code

## Outcome

A resume intelligence backend module was created to extract resume content and evaluate it against a Full Stack Developer job description using ATS scoring criteria.
