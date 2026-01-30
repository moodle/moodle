# core_filters (subsystem / plugintype) Upgrade notes

## 5.2dev

### Removed

- MimeTeX support has been removed from both `filter_tex` and `filter_algebra`. These filters now depend on LaTeX tools (`latex`, `dvips`, and `convert`/`dvisvgm`), and as a result, the `pathmimetex` setting has been removed.

  For more information see [MDL-85233](https://tracker.moodle.org/browse/MDL-85233)

## 4.5

### Added

- Added support for autoloading of filters from `\filter_[filtername]\filter`. Existing classes should be renamed to use the new namespace.

  For more information see [MDL-82427](https://tracker.moodle.org/browse/MDL-82427)

### Deprecated

- The `\core_filters\filter_manager::text_filtering_hash` method has been finally deprecated and removed.

  For more information see [MDL-82427](https://tracker.moodle.org/browse/MDL-82427)
