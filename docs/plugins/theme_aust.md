# AuST Theme (`theme_aust`)

## Overview
The **AuST Theme** is a child theme of Moodle's **Boost** theme, customized to match AuST branding guidelines. It includes specific overrides for colors, fonts, and a comprehensive Dark Mode implementation.

## Key Features
- **AuST Branding**: Uses AuST Blue (`#003366`) and Gold (`#FDB913`).
- **Dark Mode**: Complete dark mode support via `prefers-color-scheme: dark` media queries.
- **Custom SCSS**: Extensive SCSS overrides in `scss/post.scss`.

## File Structure
- `config.php`: Theme configuration.
- `scss/post.scss`: **Main stylesheet**. All custom CSS/SCSS goes here.
- `templates/`: Custom Mustache templates for overriding Moodle UI components.

## Customizing Styles
To make style changes:
1. Edit `public/theme/aust/scss/post.scss`.
2. **Purge Caches**: Go to Site Administration > Development > Purge caches.
3. Refresh the page.

### Dark Mode
Dark mode is implemented using a media query:
```scss
@media (prefers-color-scheme: dark) {
    // Overrides for dark mode
    body {
        background-color: #121212 !important;
        color: #e0e0e0 !important;
    }
    // ...
}
```
Any dark-mode specific issues should be addressed within this block in `post.scss`.

## Developer Info
For technical details on the SCSS structure and overrides, see the **[Developer Documentation](../dev/theme_aust_dev.md)**.
