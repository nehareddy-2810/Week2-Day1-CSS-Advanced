# W11D2 — Error Handling & Fallback Strategies

## Objective

Implemented resilient AI analysis for the CIA Career skill-gap feature using a provider fallback strategy.

## Architecture

The AI request follows this order:

1. Groq — primary AI provider
2. OpenRouter — secondary provider when Groq fails
3. Local deterministic analysis — final fallback when both providers fail

## Error Handling

The implementation handles:

- Groq API errors
- OpenRouter API errors
- Connection failures
- Request timeouts
- Invalid or malformed AI JSON responses
- Missing API keys

HTTP connection and request timeouts are limited so the application does not wait indefinitely.

## Testing

Added `CiaFallbackTest` to verify that the CIA endpoint remains functional when both AI providers return server errors.

Test result:

- Tests: 1 passed
- Assertions: 5
- Result: PASS

## Outcome

The CIA Career skill-gap endpoint now has multiple fallback levels and can return a deterministic local analysis when external AI providers are unavailable.
