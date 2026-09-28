import json
import sys

try:
    from crewai import Agent, Task, Crew, Process

    skill_gaps = sys.argv[1] if len(sys.argv) > 1 else "Python, SQL, communication"

    career_advisor = Agent(
        role="Career Advisor",
        goal="Recommend 3 suitable career paths based on the user's skill gaps",
        backstory="You are an experienced career advisor who helps technology professionals choose suitable career paths.",
        verbose=False
    )

    task = Task(
        description=f"""
        Analyze these skill gaps: {skill_gaps}.
        Recommend exactly 3 suitable technology career paths.
        For each path provide the career name, reason, and key skills to improve.
        Return only valid JSON.
        """,
        expected_output="A JSON object containing exactly 3 career recommendations.",
        agent=career_advisor
    )

    crew = Crew(
        agents=[career_advisor],
        tasks=[task],
        process=Process.sequential,
        verbose=False
    )

    result = crew.kickoff()

    print(json.dumps({
        "success": True,
        "recommendations": str(result)
    }))

except Exception as e:
    print(json.dumps({
        "success": False,
        "error": str(e)
    }))