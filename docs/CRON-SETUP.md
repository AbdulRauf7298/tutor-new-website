# CRON-SETUP

## Background Jobs
- Class reminders (24h, 1h, 15m)
- Notification queue processing
- Recurring class generation
- Google sync retries
- Payment reminders

## Recommended Trigger
Server cron every 5 minutes:
`*/5 * * * * php /path/to/wp-cron.php`

(Adapt command to Hostinger environment.)
