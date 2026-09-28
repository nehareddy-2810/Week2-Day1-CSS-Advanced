import json
import math
import chromadb


ROLES = [
    {"role": "Python Developer", "required_skills": ["python", "sql", "git"]},
    {"role": "Full Stack Developer", "required_skills": ["html", "css", "javascript", "python", "sql"]},
    {"role": "Java Developer", "required_skills": ["java", "sql", "git"]},
    {"role": "Frontend Developer", "required_skills": ["html", "css", "javascript", "react"]},
    {"role": "Backend Developer", "required_skills": ["python", "sql", "api", "git"]},
    {"role": "Data Analyst", "required_skills": ["python", "sql", "excel"]},
    {"role": "Machine Learning Engineer", "required_skills": ["python", "machine learning", "sql"]},
    {"role": "AI Engineer", "required_skills": ["python", "ai", "machine learning"]},
    {"role": "Cloud Engineer", "required_skills": ["linux", "cloud", "python"]},
    {"role": "DevOps Engineer", "required_skills": ["linux", "docker", "git", "cloud"]},
    {"role": "Software Engineer", "required_skills": ["java", "python", "git", "sql"]},
    {"role": "Web Developer", "required_skills": ["html", "css", "javascript"]},
    {"role": "React Developer", "required_skills": ["javascript", "react", "html", "css"]},
    {"role": "Node.js Developer", "required_skills": ["javascript", "node", "api", "sql"]},
    {"role": "Database Developer", "required_skills": ["sql", "mysql", "database"]},
    {"role": "QA Engineer", "required_skills": ["testing", "selenium", "python"]},
    {"role": "Cybersecurity Analyst", "required_skills": ["linux", "networking", "security"]},
    {"role": "Technical Support Engineer", "required_skills": ["linux", "networking", "sql"]},
    {"role": "Business Analyst", "required_skills": ["sql", "excel", "communication"]},
    {"role": "AI/ML Intern", "required_skills": ["python", "ai", "machine learning", "sql"]}
]


# Create a common skill vocabulary
SKILL_VOCAB = sorted({
    skill
    for role in ROLES
    for skill in role["required_skills"]
})


def create_embedding(skills):
    """Convert skills into a simple numerical vector."""
    skills = {skill.lower() for skill in skills}
    return [
        1.0 if skill in skills else 0.0
        for skill in SKILL_VOCAB
    ]


def get_skill_gaps(candidate_skills, required_skills):
    candidate = {skill.lower() for skill in candidate_skills}
    return sorted(
        set(required_skills) - candidate
    )


def seed_database():
    client = chromadb.PersistentClient(path="./chroma_data")

    collection = client.get_or_create_collection(
        name="internship_roles",
        configuration={"hnsw": {"space": "cosine"}}
    )

    ids = [str(index) for index in range(len(ROLES))]

    documents = [
        f"{role['role']} requires: {', '.join(role['required_skills'])}"
        for role in ROLES
    ]

    embeddings = [
        create_embedding(role["required_skills"])
        for role in ROLES
    ]

    metadatas = [
        {
            "role": role["role"],
            "required_skills": json.dumps(role["required_skills"])
        }
        for role in ROLES
    ]

    collection.upsert(
        ids=ids,
        documents=documents,
        embeddings=embeddings,
        metadatas=metadatas
    )

    return collection


def match_roles(candidate_skills):
    collection = seed_database()

    candidate_embedding = create_embedding(candidate_skills)

    results = collection.query(
        query_embeddings=[candidate_embedding],
        n_results=5
    )

    matches = []

    for index in range(len(results["ids"][0])):
        role = results["metadatas"][0][index]

        distance = results["distances"][0][index]

        # Convert cosine distance to similarity percentage
        score = max(0, min(100, (1 - distance) * 100))

        required_skills = json.loads(
            role["required_skills"]
        )

        matches.append({
            "role": role["role"],
            "match_percentage": round(score, 2),
            "skill_gap": get_skill_gaps(
                candidate_skills,
                required_skills
            )
        })

    return matches


if __name__ == "__main__":

    candidate_skills = [
        "python",
        "sql",
        "html",
        "css",
        "javascript",
        "git"
    ]

    matches = match_roles(candidate_skills)

    print(json.dumps({
        "success": True,
        "candidate_skills": candidate_skills,
        "matches": matches
    }, indent=2))