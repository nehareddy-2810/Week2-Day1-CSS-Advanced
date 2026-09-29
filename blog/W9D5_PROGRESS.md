# W9D5 Progress — Learning Path Generator

## Completed

- Created a LangGraph state machine for learning path generation.
- Implemented AssessState node.
- Implemented IdentifyGaps node.
- Implemented BuildPlan node.
- Implemented ValidatePlan node.
- Implemented OutputPlan node.
- Connected all nodes using LangGraph edges.
- Generated an 8-week learning schedule.
- Added daily learning activities for each week.
- Added validation to confirm the generated plan contains 8 weeks.

## Workflow

AssessState → IdentifyGaps → BuildPlan → ValidatePlan → OutputPlan

## Test Result

The workflow was executed successfully with candidate skills:

Python, SQL, HTML, CSS, JavaScript, Git

The system identified skill gaps and generated an 8-week learning plan.

## Technologies

- Python
- LangGraph
- JSON
- VS Code