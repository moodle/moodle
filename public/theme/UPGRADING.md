# theme (plugin type) Upgrade notes

## 5.3dev

### Removed

- Classic theme has been removed from core. During the upgrade, if the Classic theme files are not present, compatible Classic settings are migrated to the Boost theme and the Classic theme is uninstalled. The uninstallation removes all Classic theme settings, and any course, course category, cohort and user themes that referenced Classic are reset. To keep using the Classic theme with all its settings and theme selections intact, install it manually from the Moodle github repository BEFORE running the upgrade: the migration and uninstallation are then skipped entirely. Installing Classic after the upgrade results in a clean-slate theme, previous Classic settings and course/user theme selections are not restored and have to be configured again manually.
  The upgrade cannot change values forced in config.php: sites with $CFG->theme set to 'classic' there must update or remove that line manually, otherwise the site keeps requesting the removed theme and falls back to the default theme with a warning on every page.
  The migration copies the settings that were explicitly customised in Classic (unaddable blocks, brand colour, raw initial/pre SCSS, background and login background images) to Boost, keeping any existing Boost customisation where Classic held its default value. On sites where both themes had been customised, the resulting Boost configuration is therefore a mix of migrated Classic values and pre-existing Boost values, and should be reviewed after the upgrade. Theme presets and uploaded preset files are not migrated: presets are theme-specific SCSS entry points which Classic compiled wrapped in its own pre and post SCSS, so they would compile differently (or fail to compile) under Boost. Sites using a custom Classic preset should create an equivalent Boost preset after the upgrade. Migrated raw SCSS snippets that reference Classic variables or partials may also need to be reviewed. The Classic-only navbardark setting (dark navbar) has no Boost equivalent and is not migrated.
  Block positions are not migrated. Classic provided two block columns (side-pre and side-post) while Boost provides one (side-pre): blocks placed in regions that do not exist in Boost are not lost, they are displayed at the end of the default block region of each page, but their previous column placement and ordering are not preserved. Sites and courses relying on specific block positioning should review their block layout after the upgrade.

  For more information see [MDL-88351](https://tracker.moodle.org/browse/MDL-88351)

## 5.2

### Added

- The manual completion button and activity dates have been moved to the activity header to improve visibility and proximity to the activity name. A new theme layout option, `activityinfoinheader`, has been introduced to control this behaviour and is enabled by default. Themes that set `activityinfoinheader` to false must manually override the relevant template (such as `activity_header` or `activity_info`) to ensure the completion information and the activity dates are displayed correctly.

  For more information see [MDL-87662](https://tracker.moodle.org/browse/MDL-87662)
- The `core_courseformat\base` class now includes `set_show_restrictions_expanded()` and `get_show_restrictions_expanded()` to allow course formats to define whether restrictions are displayed as expanded (defaulting to collapsed).

  For more information see [MDL-87929](https://tracker.moodle.org/browse/MDL-87929)

### Deprecated

- These icons are no longer in use and have been deprecated:
    - `core:t/blocks_drawer`
    - `core:t/blocks_drawer_rtl`
    - `core:t/index_drawer`

  For more information see [MDL-88085](https://tracker.moodle.org/browse/MDL-88085)

## 5.1

### Deprecated

- These icons are no longer in use and have been deprecated:
    - core:e/insert_col_after
    - core:e/insert_col_before
    - core:e/split_cells
    - core:e/text_color
    - core:t/locktime
    - tool_policy/level

  For more information see [MDL-85436](https://tracker.moodle.org/browse/MDL-85436)

## 4.5

### Added

- Added a new `\renderer_base::get_page` getter method.

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)
- New `core/context_header` mustache template has been added. This template can be overridden by themes to modify the context header.

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)

### Deprecated

- The method `\core\output\core_renderer::render_context_header` has been deprecated please use `\core\output\core_renderer::render($contextheader)` instead

  For more information see [MDL-82160](https://tracker.moodle.org/browse/MDL-82160)

### Removed

- Removed all references to `iconhelp`, `icon-pre`, `icon-post`, `iconlarge`, and `iconsort` CSS classes.

  For more information see [MDL-74251](https://tracker.moodle.org/browse/MDL-74251)
