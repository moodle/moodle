# core_completion (subsystem) Upgrade notes

## 4.5

### Added

- A new `FEATURE_COMPLETION` plugin support constant has been added. In the future, this constant will be used to indicate when a plugin does not allow completion and it is enabled by default.

  For more information see [MDL-83008](https://tracker.moodle.org/browse/MDL-83008)

### Changed

- The `\core_completion\activity_custom_completion::get_overall_completion_state()` method can now also return `COMPLETION_COMPLETE_FAIL` and not only `COMPLETION_COMPLETE` and `COMPLETION_INCOMPLETE`.

  For more information see [MDL-81749](https://tracker.moodle.org/browse/MDL-81749)
