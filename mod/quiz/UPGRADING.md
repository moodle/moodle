# mod_quiz Upgrade notes

## 4.5

### Added

- The following methods of the `quiz_overview_report` class now take a new optional `$slots` parameter used to only regrade some slots in each attempt (default all):
  - `\quiz_overview_report::regrade_attempts()`
  - `\quiz_overview_report::regrade_batch_of_attempts()`

  For more information see [MDL-79546](https://tracker.moodle.org/browse/MDL-79546)
