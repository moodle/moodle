# theme (plugin type) Upgrade notes

## 4.5dev

### Removed

- Removed all references to iconhelp, icon-pre, icon-post, iconlarge, and iconsort classes

  For more information see [MDL-74251](https://tracker.moodle.org/browse/MDL-74251)

### Added

- New `core/context_header` mustache template has been added. This template can be overridden by themes to modify the context header

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)

### Deprecated

- The method `\core\output\core_renderer::render_context_header` has been deprecated please use `\core\output\core_renderer::render($contextheader)` instead

  For more information see [MDL-82160](https://tracker.moodle.org/browse/MDL-82160)
