# core_question (subsystem) Upgrade notes

## 4.5dev

### Added

- A new utility function `format_question_fragment` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

### Changed

- column_base::from_column_name now has an ignoremissing field, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81125](https://tracker.moodle.org/browse/MDL-81125)
