# ALEA — Staging Deploy Repo

Deploys two add-only files to the ALEA **staging** site via cPanel Git Version Control.

- `wp-content/themes/theratio-child/functions.php` — Lead tracking, LocalBusiness schema, perf cleanup, WhatsApp pre-fill, trust-bar shortcode
- `wp-content/mu-plugins/alea-quiet-theratio-warnings.php` — silences the Theratio PHP-8 warning storm

`.cpanel.yml` copies these into `/home2/aleampo8/staging.aleamodular.com/` on **Deploy HEAD Commit**.

To later deploy to **live**, change `DEPLOYPATH` in `.cpanel.yml` to `/home2/aleampo8/public_html`.
