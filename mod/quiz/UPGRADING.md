# mod_quiz Upgrade notes

## 5.0

### Added

- quiz_attempt now has 2 additional state values, NOT_STARTED and SUBMITTED. These represent attempts when an attempt has been

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- New quiz setting "precreateperiod" controls the period before timeopen during which attempts will be pre-created using the new
  NOT_STARTED state. This setting is marked advanced and locked by default, so can only be set by administrators. This setting
  is read by the \mod_quiz\task\precreate_attempts task to identify quizzes due for pre-creation.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)

### Changed

- quiz_attempt_save_started now sets the IN_PROGRESS state, timestarted, and saves the attempt, while the new quiz_attempt_save_not_started function sets the NOT_STARTED state and saves the attempt.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- quiz_attempt_save_started Now takes an additional $timenow parameter, to specify the timestart of the attempt. This was previously
  set in quiz_create_attempt, but is now set in quiz_attempt_save_started and quiz_attempt_save_not_started.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- The `quiz_question_tostring` method now includes a new boolean parameter, `displaytaglink`. This parameter specifies whether the tag name in the question bank should be displayed as a clickable hyperlink (`true`) or as plain text (`false`).

  For more information see [MDL-75075](https://tracker.moodle.org/browse/MDL-75075)
- The `\mod_quiz\attempt_walkthrough_from_csv_test` unit test has been marked as final and should not be extended by other tests.

  All shared functionality has been moved to a new autoloadable test-case:
  `\mod_quiz\tests\attempt_walkthrough_testcase`.

  To support this testcase the existing `$files` instance property should be replaced with a new static method, `::get_test_files`.
  Both the existing instance property and the new static method can co-exist.

  For more information see [MDL-81521](https://tracker.moodle.org/browse/MDL-81521)

### Deprecated

- quiz_attempt::process_finish is now deprecated, and its functionality is split between ::process_submit, which saves the
  submission, sets the finish time and sets the SUBMITTED status, and ::process_grade_submission which performs automated
  grading and sets the FINISHED status.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- The webservice function `mod_quiz_get_user_attempts` is now deprecated in favour of `mod_quiz_get_user_quiz_attempts`.

  With the introduction of the new NOT_STARTED quiz attempt state, `mod_quiz_get_user_attempts` has been modified to not return NOT_STARTED attempts, allowing clients such as the mobile app to continue working without modifications.

  `mod_quiz_get_user_quiz_attempts` will return attempts in all states, as `mod_quiz_get_user_attempts` did before. Once clients are updated to handle NOT_STARTED attempts, they can migrate to use this function.

  A minor modification to `mod_quiz_start_attempt` has been made to allow it to transparently start an existing attempt that is in the NOT_STARTED state, rather than creating a new one.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)

### Removed

- Final removal of quiz_delete_override() and quiz_delete_all_overrides()

  For more information see [MDL-80944](https://tracker.moodle.org/browse/MDL-80944)

## 4.5

### Added

- The following methods of the `quiz_overview_report` class now take a new optional `$slots` parameter used to only regrade some slots in each attempt (default all):
  - `\quiz_overview_report::regrade_attempts()`
  - `\quiz_overview_report::regrade_batch_of_attempts()`

  For more information see [MDL-79546](https://tracker.moodle.org/browse/MDL-79546)
