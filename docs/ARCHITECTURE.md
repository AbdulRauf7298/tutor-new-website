# ARCHITECTURE

## Phase 0 Scope
This phase defines architecture and delivery roadmap only. It does not introduce Phase 1+ feature implementation.

## System Layers
1. Theme layer (UI/UX, templates, accessibility, responsive dashboards)
2. Plugin core (domain business logic)
3. API layer (secure REST endpoints)
4. Integration layer (Google, Conceptboard, WooCommerce, SMTP)
5. Data layer (WordPress + custom operational tables + migrations)
6. Jobs layer (WP-Cron/background synchronization and reminders)

## Plugin/Theme Boundary
- Plugin: student/teacher/parent management, enrollment, sessions, attendance, messaging, notifications, reports, payments mapping.
- Theme: branding, navigation, public pages, dashboard rendering.

## Database Strategy
Hybrid approach:
- CPT/meta for content entities (subjects, curriculums, programs, exams, courses)
- Custom tables (`wp_edu_*`) for high-volume operational data.

Planned operational tables:
- `wp_edu_students`, `wp_edu_teachers`, `wp_edu_parents`
- `wp_edu_enrollments`, `wp_edu_class_sessions`, `wp_edu_class_students`, `wp_edu_teacher_availability`
- `wp_edu_attendance`, `wp_edu_homework`, `wp_edu_assignments`, `wp_edu_submissions`
- `wp_edu_notifications`, `wp_edu_messages`, `wp_edu_audit_logs`
- `wp_edu_payment_links`, `wp_edu_google_integrations`

## Migrations
Versioned migration pipeline:
- `001_initial`
- `002_users`
- `003_enrollments`
- `004_classes`
- `005_google`
- `...`

Upgrades run safely during plugin version updates.

## Permission Matrix (summary)
- Administrator / Education Administrator: full scoped management
- Teacher: assigned students/courses/classes only
- Student: own data only
- Parent: linked children only
- Accountant: finance/payment/reporting scope only

## Integrations
- Google OAuth (server-side)
- Google Calendar API for event scheduling
- Meet links through supported Calendar conference workflows
- Conceptboard secure URL mapping by class session
- WooCommerce for checkout/subscriptions/payment records
- SMTP for transactional email delivery

## Phase Roadmap
- Phase 1: modular skeleton + migrations + capability map
- Phase 2: people profiles + enrollment CRUD
- Phase 3: dashboards + secure role data
- Phase 4: scheduling/conflict/timezone
- Phase 5: Google integrations
- Phase 6-14: attendance, academics, payments, notifications, reports, hardening, deployment

## Phase 0 Completion Report
- PHASE: 0
- STATUS: Completed
- FILES CREATED: documentation and secure config templates
- DATABASE CHANGES: none
- FEATURES: architecture and roadmap definitions only
- NEXT PHASE: 1 (pending approval)
