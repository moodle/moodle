# Custom Plugins & Themes

This repository contains several custom Moodle plugins and a theme tailored for AuST.

## Themes
- **[AuST Theme](plugins/theme_aust.md)** (`theme_aust`): The main visual theme, extending Boost with AuST branding and Dark Mode.

## Local Plugins
- **[MasterBuilder](plugins/local_masterbuilder.md)** (`local_masterbuilder`): API for automated course creation and state management.
- **[Course Matrix](plugins/local_coursematrix.md)** (`local_coursematrix`): Rule-based user enrollment system.
- **[Quiz Password Verify](plugins/local_quiz_password_verify.md)** (`local_quiz_password_verify`): Custom security logic for quiz access.

## Development Workflow
1. **Edit**: Modify the code in `public/local/` or `public/theme/`.
2. **Deploy**: Push changes to GitHub. The CI/CD pipeline will deploy to Test/Prod.
3. **Upgrade**: If you changed `version.php` or DB structures, run the Moodle upgrade script or visit the Notifications page.
4. **Purge Caches**: Always purge caches after editing Theme SCSS or Language strings.
