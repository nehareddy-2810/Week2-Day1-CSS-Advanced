# W11D1 Performance Notes

## Findings

CIA Career queries were reviewed using MySQL EXPLAIN and SHOW INDEX.

Existing indexes on candidate_lessons, candidate_quiz_scores, and candidate_reports support user-based queries.

The placement_readiness_scores table was inspected and no additional index was added because it currently has no user_id column.

## Optimisation Approach

Existing indexes were reused and duplicate or invalid indexes were avoided.
