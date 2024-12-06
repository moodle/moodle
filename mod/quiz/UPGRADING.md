# mod_quiz Upgrade notes

## 4.5.1

### Changed

- The `\mod_quiz\attempt_walkthrough_from_csv_test` unit test has been marked as final and should not be extended by other tests.

  All shared functionality has been moved to a new autoloadable test-case:
  `\mod_quiz\tests\attempt_walkthrough_testcase`.

  To support this testcase the existing `$files` instance property should be replaced with a new static method, `::get_test_files`.
  Both the existing instance property and the new static method can co-exist.

  For more information see [MDL-81521](https://tracker.moodle.org/browse/MDL-81521)

## 4.5

### Added

- The following methods of the `quiz_overview_report` class now take a new optional `$slots` parameter used to only regrade some slots in each attempt (default all):
  - `\quiz_overview_report::regrade_attempts()`
  - `\quiz_overview_report::regrade_batch_of_attempts()`

  For more information see [MDL-79546](https://tracker.moodle.org/browse/MDL-79546)
