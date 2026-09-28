# W11D1 — API Performance Audit

## 1. EXPLAIN Query Audit

The CIA Career database queries were reviewed using MySQL EXPLAIN.

### candidate_lessons
- Query filtered by user_id.
- Existing composite index: user_id, completed_at.
- Index is available for the query.

### candidate_quiz_scores
- Query filtered by user_id.
- Existing composite index: user_id, taken_at.
- Index is available for the query.

### candidate_reports
- Query filtered by user_id.
- Existing index: user_id.
- EXPLAIN confirmed the user_id index is selected.
- Query examined approximately 1 row in the current dataset.

### placement_readiness_scores
- Table currently contains only id, created_at, and updated_at.
- No user_id column exists, so no user-based index was added.

## 2. Performance Optimisation

Existing indexes were reused where appropriate instead of creating duplicate indexes.

## 3. ChromaDB Performance

The internship matching service uses persistent ChromaDB storage and returns role matches with match percentages and skill gaps.

The existing ChromaDB implementation was reviewed for query performance and persistent storage.

## 4. API Error Handling

CIA API routes were reviewed for HTTP 500 failures and error handling.

## Result

Database query plans and indexes were audited, unnecessary index creation was avoided, and the CIA Career performance documentation was updated.
