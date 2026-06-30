# core_filters (subsystem / plugintype) Upgrade notes

## 5.3dev

### Added

- Rendered TeX/Algebra images are now stored using the File Storage API instead of the `$CFG->dataroot/filter/{tex,algebra}/` directory. A new `rendered_images` cache definition has been added to both `filter_tex` and `filter_algebra`. The upgrade step automatically migrates existing images from the legacy dataroot location to file storage and removes the old directory.

  For more information see [MDL-87554](https://tracker.moodle.org/browse/MDL-87554)

## 5.2

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
