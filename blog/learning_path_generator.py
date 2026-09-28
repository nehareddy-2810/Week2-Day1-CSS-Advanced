import json
from typing import TypedDict

from langgraph.graph import StateGraph, START, END


class LearningState(TypedDict):
    candidate_skills: list[str]
    skill_gaps: list[str]
    learning_plan: list[dict]
    validated: bool
    result: dict


def assess_state(state: LearningState):
    skills = [skill.lower() for skill in state["candidate_skills"]]
    return {"candidate_skills": skills}


def identify_gaps(state: LearningState):
    target_skills = [
        "react",
        "node.js",
        "rest api",
        "database",
        "docker",
        "testing",
        "cloud",
        "system design"
    ]

    candidate_skills = set(state["candidate_skills"])

    gaps = [
        skill for skill in target_skills
        if skill not in candidate_skills
    ]

    return {"skill_gaps": gaps}


def build_plan(state: LearningState):
    topics = [
        "React fundamentals",
        "Advanced React and API integration",
        "Node.js and REST APIs",
        "Database design and SQL",
        "Docker and deployment",
        "Testing and debugging",
        "Cloud fundamentals",
        "System design and final project"
    ]

    plan = []

    for number, topic in enumerate(topics, start=1):
        plan.append({
            "week": number,
            "topic": topic,
            "days": [
                "Day 1: Introduction and concepts",
                "Day 2: Core concepts",
                "Day 3: Hands-on practice",
                "Day 4: Build a small feature",
                "Day 5: Testing and debugging",
                "Day 6: Mini project",
                "Day 7: Review and assessment"
            ]
        })

    return {"learning_plan": plan}


def validate_plan(state: LearningState):
    is_valid = len(state["learning_plan"]) == 8

    return {"validated": is_valid}


def output_plan(state: LearningState):
    return {
        "result": {
            "success": state["validated"],
            "candidate_skills": state["candidate_skills"],
            "skill_gaps": state["skill_gaps"],
            "learning_plan": state["learning_plan"]
        }
    }


# Create LangGraph
workflow = StateGraph(LearningState)

workflow.add_node("AssessState", assess_state)
workflow.add_node("IdentifyGaps", identify_gaps)
workflow.add_node("BuildPlan", build_plan)
workflow.add_node("ValidatePlan", validate_plan)
workflow.add_node("OutputPlan", output_plan)

workflow.add_edge(START, "AssessState")
workflow.add_edge("AssessState", "IdentifyGaps")
workflow.add_edge("IdentifyGaps", "BuildPlan")
workflow.add_edge("BuildPlan", "ValidatePlan")
workflow.add_edge("ValidatePlan", "OutputPlan")
workflow.add_edge("OutputPlan", END)

app = workflow.compile()


if __name__ == "__main__":

    initial_state = {
        "candidate_skills": [
            "python",
            "sql",
            "html",
            "css",
            "javascript",
            "git"
        ],
        "skill_gaps": [],
        "learning_plan": [],
        "validated": False,
        "result": {}
    }

    final_state = app.invoke(initial_state)

    print(json.dumps(
        final_state["result"],
        indent=2
    ))