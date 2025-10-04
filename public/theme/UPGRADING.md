# theme (plugin type) Upgrade notes

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
