# tutor-new-website

This repository now includes a WordPress-first implementation foundation for an international live online education platform:

- **Custom plugin**: `/wp-content/plugins/tutor-education-platform`
- **Custom theme**: `/wp-content/themes/tutor-custom-theme`

## Implemented foundation

### Plugin (`tutor-education-platform`)
- Custom roles and capabilities:
  - Education Administrator
  - Teacher
  - Student
  - Parent/Guardian
  - Accountant
- Dynamic education content types:
  - Subjects, Curriculums, Programs, Competitive Exams, Courses
- Class Session post type with secure meta management for:
  - Schedule (UTC storage + timezone)
  - Teacher/student assignment
  - Google Calendar event ID
  - Google Meet space ID + URL
  - Conceptboard URL
  - Session status (including `meeting_creation_failed` handling)
- Student dashboard shortcode:
  - `[tep_student_dashboard_next_class]`
  - Shows the authenticated student's next class, with **Join Live Class** and **Open Conceptboard** links.
- Admin “Google Integration” settings page with secure server-side status toggle (no secrets exposed in frontend output).

### Theme (`tutor-custom-theme`)
- Custom lightweight branding shell
- Responsive homepage with core conversion sections
- WordPress-compatible header/footer/nav/content templates

## Security and architecture notes
- Business logic is in the plugin, not the theme.
- Meta save handlers use capability checks, nonce verification, sanitization, and URL escaping.
- Google-related data is stored server-side in post meta/options, and not embedded as frontend secrets.

## Usage
1. Place this repository in an existing WordPress install root.
2. Activate plugin: **Tutor Education Platform**.
3. Activate theme: **Tutor Custom Theme**.
4. Add class sessions in WP Admin → Class Sessions.
5. Add `[tep_student_dashboard_next_class]` to the student dashboard page.
