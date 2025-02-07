# core_question (subsystem) Upgrade notes

## 4.5.2

### Added

- The `get_bulk_actions()` method on the base `plugin_features_base` class has been changed to allow a qbank view object to be passed through. This is nullable and therefore optional for qbank plugins which don't need to do so.

  For more information see [MDL-79281](https://tracker.moodle.org/browse/MDL-79281)

## 4.5

### Added

- A new utility function `\question_utils::format_question_fragment()` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

### Changed

- `\core_question\local\bank\column_base::from_column_name()` method now accepts a `bool $ingoremissing` parameter, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81407](https://tracker.moodle.org/browse/MDL-81407)
