# CIA Career Intelligence - Technical Documentation

## 1. Architecture

The CIA Career Intelligence module uses a Laravel-based architecture.

### Data Flow

User
→ Laravel Web Routes
→ CIA Career Intelligence
→ Candidate Profile
→ ChromaDB
→ Career Analysis
→ Recommendations

## 2. CIA Sub-Routes

| Route | Purpose | Authentication |
|---|---|---|
| /cia-career/dashboard | Career dashboard | Required |
| /cia-career/profile | Candidate profile | Required |
| /cia-career/skills | Skills analysis | Required |
| /cia-career/readiness | Placement readiness | Required |
| /cia-career/recommendations | Career recommendations | Required |
| /cia-career/history | Career history | Required |
| /cia-career/architecture | Architecture information | Required |

## 3. API Contracts

### GET /api/cia-career/profile

Authentication: Required

Response:

```json
{
    "candidate_id": 1,
    "skills": ["Java", "Python", "MySQL"],
    "scores": {
        "technical": 80,
        "interview": 75
    }
}