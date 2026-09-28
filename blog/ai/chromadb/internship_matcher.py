import chromadb
import json

# Persistent ChromaDB database
client = chromadb.PersistentClient(
    path="ai/chromadb/data"
)

collection = client.get_or_create_collection(
    name="internship_roles"
)

# 20 internship role profiles
roles = [
    ("Full Stack Developer", ["html", "css", "javascript", "react", "node", "mysql"]),
    ("Backend Developer", ["python", "node", "api", "mysql", "sql"]),
    ("Frontend Developer", ["html", "css", "javascript", "react"]),
    ("Java Developer", ["java", "spring", "mysql", "sql"]),
    ("Python Developer", ["python", "django", "flask", "sql"]),
    ("Cloud Engineer", ["aws", "azure", "docker", "linux"]),
    ("DevOps Engineer", ["docker", "git", "linux", "ci/cd"]),
    ("Data Analyst", ["python", "sql", "excel", "data analysis"]),
    ("Data Scientist", ["python", "machine learning", "pandas", "numpy"]),
    ("AI Engineer", ["python", "machine learning", "llm", "api"]),
    ("ML Engineer", ["python", "machine learning", "scikit-learn", "sql"]),
    ("Software Engineer", ["java", "python", "git", "sql"]),
    ("Web Developer", ["html", "css", "javascript", "php"]),
    ("Laravel Developer", ["php", "laravel", "mysql", "javascript"]),
    ("React Developer", ["react", "javascript", "html", "css"]),
    ("Node.js Developer", ["node", "javascript", "express", "mongodb"]),
    ("Database Developer", ["mysql", "sql", "database", "postgresql"]),
    ("QA Engineer", ["testing", "selenium", "javascript", "sql"]),
    ("AI/Automation Intern", ["python", "ai", "api", "automation"]),
    ("Technical Support Engineer", ["linux", "sql", "networking", "troubleshooting"]),
]

# Seed roles
if collection.count() == 0:
    for i, (role, skills) in enumerate(roles):
        collection.add(
            ids=[f"role-{i}"],
            documents=[
                f"{role}. Required skills: {', '.join(skills)}"
            ],
            metadatas=[
                {
                    "role": role,
                    "required_skills": json.dumps(skills)
                }
            ]
        )

# Candidate skill profile
candidate_skills = [
    "java",
    "python",
    "html",
    "css",
    "javascript",
    "mysql",
    "sql",
    "react",
    "git",
    "php",
    "laravel"
]

candidate_text = "Candidate skills: " + ", ".join(candidate_skills)

# Semantic similarity search
results = collection.query(
    query_texts=[candidate_text],
    n_results=5
)

matches = []

for i in range(len(results["ids"][0])):
    metadata = results["metadatas"][0][i]

    required = json.loads(metadata["required_skills"])

    matched = [
        skill for skill in required
        if skill.lower() in [x.lower() for x in candidate_skills]
    ]

    gaps = [
        skill for skill in required
        if skill.lower() not in [x.lower() for x in candidate_skills]
    ]

    score = round((len(matched) / len(required)) * 100, 2)

    matches.append({
        "role": metadata["role"],
        "match_percentage": score,
        "matched_skills": matched,
        "skill_gap": gaps
    })

print(json.dumps(matches))