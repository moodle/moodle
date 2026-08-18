# AFLEX local setup

1. Copy `.env.example` to `.env` and replace the example passwords.
2. Run `./setup-aflex.sh`.
3. Open the `MOODLE_URL` value (default: http://localhost:8080).

The script is safe to rerun. Database and uploaded files live in Docker volumes.

## Change the branding

Sign in as an administrator, then open **Site administration → Appearance → Themes → AFLEX**.
The page provides controls for the logo, favicon, login background, primary colour, and custom SCSS.

Code-level AFLEX styles live in `public/theme/aflex/scss/aflex.scss`. Keep custom work in the AFLEX
theme rather than editing Moodle core, which makes future Moodle upgrades much easier.

User-facing core wording overrides live in `branding/lang/en_local/moodle.php`. Re-run
`./setup-aflex.sh` after changing that file to install it and purge Moodle's caches.

## Useful commands

- Start: `docker compose up -d`
- Stop: `docker compose down`
- Logs: `docker compose logs -f web`
- Purge caches: `docker compose exec web php admin/cli/purge_caches.php`
- Upgrade after updating Moodle: `docker compose exec web php admin/cli/upgrade.php --non-interactive`

For production, change all credentials, configure HTTPS, set `MOODLE_URL` to the public URL, and
enable scheduled cron execution for `admin/cli/cron.php`.
