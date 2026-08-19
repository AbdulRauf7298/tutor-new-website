# SECURITY

## Core Controls
- Capability checks on admin/API actions
- Ownership checks for student/teacher/parent data boundaries
- Nonces for state-changing actions
- Input sanitization/validation and output escaping
- Prepared SQL for custom table queries

## Required Reviews Before Production
- Authentication and authorization
- REST API access control
- SQL injection/XSS/CSRF checks
- File access and upload restrictions
- Secrets handling and token storage practices
