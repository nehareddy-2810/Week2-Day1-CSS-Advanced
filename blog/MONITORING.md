# CIA Career Intelligence — Logging, Monitoring & Alerting

## Overview

The CIA Career Intelligence module now includes application-level logging and monitoring using Laravel's logging system.

## Logging

A dedicated `cia` logging channel is configured in `config/logging.php`.

The channel uses the daily log driver, stores CIA logs in `storage/logs/cia.log`, and retains logs for 14 days.

## Monitored Events

The CIA controller records:

- Skill-gap request started.
- Skill-gap request completed.
- AI provider success.
- Groq request failures.
- OpenRouter request failures.
- Invalid AI response formats.
- Missing AI provider configuration.
- Local fallback activation.

## Alerting

When both Groq and OpenRouter are unavailable, the application records a `critical` CIA log event:

`CIA AI providers unavailable; deterministic local fallback used`

This provides an alertable event for future integration with an external monitoring platform.

## Performance Monitoring

Each skill-gap request records execution time in milliseconds.

Recorded monitoring information includes:

- `duration_ms`
- `profile_skills`
- `score`

## Security

CIA logs do not contain API keys, authorization tokens, complete candidate skill profiles, or AI request payloads.

Only operational information required for monitoring is recorded.

## Testing

The following tests passed:

- `CiaFallbackTest`
- `CiaMonitoringTest`

Result:

- Tests: 2 passed
- Assertions: 7

The monitoring test verifies that the dedicated CIA logging channel can record a monitoring event.

## Future Improvements

A production deployment can connect critical CIA events to an external monitoring and alerting platform such as Sentry or another centralized logging service.
