# tutor-new-website

WordPress-first implementation foundation for an international live online education platform.

## Current Implementation Status
- Custom plugin foundation: `/wp-content/plugins/tutor-education-platform`
- Custom theme foundation: `/wp-content/themes/tutor-custom-theme`
- Phase status: **Phase 2 in progress (people profiles and enrollment CRUD foundation)**

## Documentation
All operational and architecture documents are in `/docs`:

- `ARCHITECTURE.md`
- `INSTALLATION.md`
- `HOSTINGER-SETUP.md`
- `GOOGLE-CLOUD-SETUP.md`
- `GOOGLE-MEET-SETUP.md`
- `GOOGLE-CALENDAR-SETUP.md`
- `CONCEPTBOARD-SETUP.md`
- `PAYMENT-SETUP.md`
- `SMTP-SETUP.md`
- `CRON-SETUP.md`
- `BACKUP-SETUP.md`
- `ADMIN-GUIDE.md`
- `TEACHER-GUIDE.md`
- `STUDENT-GUIDE.md`
- `PARENT-GUIDE.md`
- `SECURITY.md`
- `TROUBLESHOOTING.md`
- `DEPLOYMENT.md`

## Notes
- Business logic must remain in the plugin.
- Theme is presentation-only.
- Google and payment secrets must never be committed.
