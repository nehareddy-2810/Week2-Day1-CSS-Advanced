import json
import os
import sys

from dotenv import load_dotenv
from pypdf import PdfReader
from groq import Groq


load_dotenv()


def extract_pdf_text(pdf_path):
    """Extract text from a resume PDF."""

    reader = PdfReader(pdf_path)

    pages = []

    for page in reader.pages:
        text = page.extract_text() or ""
        pages.append(text)

    return "\n".join(pages).strip()


def score_resume(resume_text, job_description):
    """Score a resume against a job description using Groq."""

    api_key = os.getenv("GROQ_API_KEY")

    if not api_key:
        raise RuntimeError(
            "GROQ_API_KEY is missing from the .env file."
        )

    client = Groq(api_key=api_key)

    prompt = f"""
You are an ATS resume evaluator.

Evaluate the resume against the job description.

Give four scores from 0 to 100:

1. Keywords
2. Formatting
3. Experience
4. Skills

Calculate the total score as the average of the four scores.

Also provide:
- keyword_gaps
- rewrite_suggestions for Summary
- rewrite_suggestions for Skills
- rewrite_suggestions for Experience
- rewrite_suggestions for Projects

Return ONLY valid JSON in this exact structure:

{{
  "scores": {{
    "keywords": 0,
    "formatting": 0,
    "experience": 0,
    "skills": 0
  }},
  "total_score": 0,
  "keyword_gaps": [],
  "rewrite_suggestions": {{
    "summary": [],
    "skills": [],
    "experience": [],
    "projects": []
  }}
}}

JOB DESCRIPTION:
{job_description}

RESUME:
{resume_text}
"""

    response = client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {
                "role": "system",
                "content": "You are an ATS resume evaluation assistant."
            },
            {
                "role": "user",
                "content": prompt
            }
        ],
        temperature=0.2,
        response_format={"type": "json_object"}
    )

    content = response.choices[0].message.content

    return json.loads(content)


if __name__ == "__main__":

    if len(sys.argv) < 2:
        print("Usage: python resume_ats_scorer.py <resume.pdf>")
        sys.exit(1)

    pdf_path = sys.argv[1]

    job_description = """
Full Stack Developer Intern

Required skills:
Python, JavaScript, HTML, CSS, SQL, Git, REST APIs,
React, Node.js and database fundamentals.

Responsibilities:
Develop web applications, create REST APIs,
work with databases, debug applications and
collaborate using Git.
"""

    try:
        resume_text = extract_pdf_text(pdf_path)

        if not resume_text:
            raise ValueError(
                "No text could be extracted from the PDF."
            )

        result = score_resume(
            resume_text,
            job_description
        )

        print(json.dumps(result, indent=2))

    except Exception as error:
        print(json.dumps({
            "success": False,
            "error": str(error)
        }, indent=2))

        sys.exit(1)