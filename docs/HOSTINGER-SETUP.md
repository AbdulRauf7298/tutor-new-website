# HOSTINGER-SETUP

## Recommended Stack
- PHP 8.1 or newer
- HTTPS certificate enabled
- MySQL/MariaDB supported by host plan

## PHP Settings
- `memory_limit` >= 256M
- `max_execution_time` >= 120
- `upload_max_filesize` >= 64M
- `post_max_size` >= 64M

## Extensions
- curl, json, mbstring, openssl, mysqli, zip, gd/imagemagick

## File/Permissions
- Standard WordPress permissions (no world-writable)
- Protect config and secret files from web access

## Cron
Use server cron to trigger WP-Cron regularly for reminders/sync jobs.

## Backups
Enable daily backups for database + uploads + plugin/theme files.
