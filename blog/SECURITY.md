# CIA Career Security Audit — W11D3

## Scope

Security audit of the CIA Career endpoints in the Laravel application.

## Endpoints Audited

- GET `/cia-career`
- POST `/cia-career/readiness`
- POST `/cia-career/skill-gap`

## Security Checks

### 1. Authentication and Authorization

**Status: PASS**

CIA Career routes are protected by Laravel `auth` middleware.

The authenticated user's ID is used when generating the skill profile and saving placement readiness data.

### 2. CSRF Protection

**Status: PASS**

The CIA endpoints are defined in `routes/web.php` and use Laravel's web middleware stack, which provides CSRF protection for state-changing requests.

### 3. Input Validation

**Status: PASS**

The skill-gap endpoint validates `target_role` as required, a string, and maximum 100 characters.

The placement readiness endpoint validates score fields as numeric values between 0 and 100.

### 4. API Key Protection

**Status: PASS**

No API keys are hardcoded in the audited controllers or configuration.

Groq and OpenRouter credentials are loaded through environment configuration.

A Git history check for `.env` returned no matching API keys or secret values.

### 5. SQL Injection

**Status: PASS**

The CIA skill-gap implementation uses Laravel Query Builder methods with parameter binding.

A controller audit found no usage of `DB::raw`, `whereRaw`, `selectRaw`, `orderByRaw`, or `havingRaw`.

No obvious SQL injection vector was identified in the audited controller code.

### 6. Sensitive Data Exposure

**Status: PASS**

The audited CIA controllers do not log API keys or external provider responses.

External AI provider failures are handled without exposing provider credentials to the client.

### 7. External API Error Handling

**Status: PASS**

Groq and OpenRouter requests use connection and request timeouts.

Provider failures and exceptions fall through to the configured fallback strategy.

The CIA skill-gap feature ultimately provides deterministic local analysis when external AI providers are unavailable.

### 8. User ID Security Fix

**Status: FIXED**

The placement readiness controller previously used a default user ID when authentication was unavailable.

The fallback was removed and replaced with the authenticated user's ID:

`$request->user()->id`

This prevents readiness data from being unintentionally associated with a default account.

## OWASP Top 10 Areas Reviewed

- Broken Access Control
- Cryptographic Failures
- Injection
- Security Misconfiguration
- Identification and Authentication Failures
- Security Logging and Monitoring Failures

## Evidence

Security checks performed during W11D3:

- Laravel route audit
- Controller code review
- PHP syntax validation
- Git history secret scan
- Raw SQL usage scan

## Conclusion

The audited CIA Career endpoints have authentication, CSRF protection, input validation, environment-based API credentials, parameterized database access, and external API error handling.

One security issue involving a default user ID was identified and fixed in commit:

`83c1bf7 fix(cia-career): prevent default user id fallback`
