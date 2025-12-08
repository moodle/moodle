# AuST Theme Developer Documentation

## Overview
**Plugin Name**: `theme_aust`
**Location**: `public/theme/aust`
**Parent Theme**: `boost` (Bootstrap 4 based)
**Purpose**: Applies AuST branding (Blue/Gold) and implements a custom Dark Mode.

## File Structure
- **`config.php`**: Defines the theme configuration, including the parent theme (`boost`) and the SCSS compilation order.
- **`scss/post.scss`**: **The most important file.** This contains all the custom CSS overrides. It is compiled *after* the Boost presets, allowing us to override Bootstrap variables and Moodle styles.
- **`templates/`**: Contains Mustache templates that override core Moodle renderers (e.g., login page, navbar).

## SCSS Architecture (`post.scss`)
The `post.scss` file is organized into sections:
1.  **Global Variables**: `$aust-blue`, `$aust-gold`.
2.  **Global Overrides**: Styles that apply to both Light and Dark modes (e.g., Navbar color, Buttons).
3.  **Dark Mode Media Query**:
    ```scss
    @media (prefers-color-scheme: dark) {
        // All dark mode logic lives here
    }
    ```

## Dark Mode Implementation
We use a "brute-force" approach for Dark Mode because Moodle's default Boost theme does not natively support it well.
-   **Backgrounds**: We force `background-color: #121212 !important` on the body and main containers.
-   **Text**: We force `color: #e0e0e0 !important`.
-   **Components**: Specific overrides for Modals, Cards, Drawers, and Form inputs.
-   **YUI Dialogs**: Special handling for `.yui3-widget` classes to fix legacy Moodle popups.

## How to Add Styles
1.  Open `public/theme/aust/scss/post.scss`.
2.  Add your CSS.
    -   If it's for **branding**, add it to the top section.
    -   If it's for **dark mode**, add it inside the `@media (prefers-color-scheme: dark)` block.
3.  **Purge Caches**: You MUST purge Moodle caches for changes to take effect.
