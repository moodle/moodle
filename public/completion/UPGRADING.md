# core_completion (subsystem) Upgrade notes

## 5.0

### Added

- Add hook after_cm_completion_updated triggered when an activity completion is updated.

  For more information see [MDL-83542](https://tracker.moodle.org/browse/MDL-83542)
- The method `count_modules_completed` now delegate the logic to count the completed modules to the DBMS improving the performance of the method.

  For more information see [MDL-83917](https://tracker.moodle.org/browse/MDL-83917)

## 4.5

### Added

- A new `FEATURE_COMPLETION` plugin support constant has been added. In the future, this constant will be used to indicate when a plugin does not allow completion and it is enabled by default.

  For more information see [MDL-83008](https://tracker.moodle.org/browse/MDL-83008)

### Changed

- The `\core_completion\activity_custom_completion::get_overall_completion_state()` method can now also return `COMPLETION_COMPLETE_FAIL` and not only `COMPLETION_COMPLETE` and `COMPLETION_INCOMPLETE`.

  For more information see [MDL-81749](https://tracker.moodle.org/browse/MDL-81749)
