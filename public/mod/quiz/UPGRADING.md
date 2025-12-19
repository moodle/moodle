# mod_quiz Upgrade notes

## 5.2dev

### Changed

- The WebServices mod_quiz_get_user_best_grade and mod_quiz_get_user_quiz_attempts have been updated to return overall feedback even when quiz marks are hidden in the review options. This change aligns the WebService behaviour with Moodle LMS display logic.

  For more information see [MDL-86916](https://tracker.moodle.org/browse/MDL-86916)

## 5.1

### Added

- Add helper methods in the mod/quiz/lib.php to count the number of attempts (quiz_num_attempts), the number of users who attempted a quiz (quiz_num_users_who_attempted) and users who can attempt (quiz_num_users_who_can_attempt)

  For more information see [MDL-83898](https://tracker.moodle.org/browse/MDL-83898)
- Add a groupidlist option to quiz_num_attempt_summary, quiz_num_attempts and quiz_num_users_who_can_attempt to filter those number by groups (the new argument is a list of ids for groups)

  For more information see [MDL-86223](https://tracker.moodle.org/browse/MDL-86223)
- Additional parameter for quiz_num_attempts so we only count users with specified capabilities

  For more information see [MDL-86520](https://tracker.moodle.org/browse/MDL-86520)

### Deprecated

- Final deprecations for the quiz. The following functions have been removed:
    - quiz_has_question_use
    - quiz_update_sumgrades
    - quiz_update_all_attempt_sumgrades
    - quiz_update_all_final_grades
    - quiz_set_grade
    - quiz_save_best_grade
    - quiz_calculate_best_grade
    - quiz_calculate_best_attempt

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Initial deprecation add_random_form and associates.
  The just removed mod_quiz\form\add_random_form was the only place in core where the mod_quiz/add_random_form javascript was called, so we can deprecate this now. This also enables us to deprecate the mod_quiz/random_question_form_preview javascript and the mod_quiz/random_question_form_preview_question_list template as they are direct dependends.

  For more information see [MDL-78091](https://tracker.moodle.org/browse/MDL-78091)

### Removed

- Final deprecations for the quiz. The following files have been removed:
    - mod/quiz/accessmanager_form.php
    - mod/quiz/accessmanager.php
    - mod/quiz/accessrule/accessrulebase.php
    - mod/quiz/attemptlib.php
    - mod/quiz/cronlib.php
    - mod/quiz/override_form.php
    - mod/quiz/renderer.php
    - mod/quiz/report/attemptsreport_form.php
    - mod/quiz/report/attemptsreport_options.php
    - mod/quiz/report/attemptsreport_table.php
    - mod/quiz/report/attemptsreport.php
    - mod/quiz/report/default.php

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Final deprecations for the quiz. The following methods have been removed:
     - mod_quiz\output\renderer::no_questions_message
     - mod_quiz\output\renderer::render_mod_quiz_links_to_other_attempts
     - mod_quiz\output\renderer::render_quiz_nav_question_button
     - mod_quiz\output\renderer::render_quiz_nav_section_heading
     - mod_quiz\structure::get_slot_tags_for_slot_id
     - mod_quiz\structure::is_display_number_customised

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Final deprecations for the quiz. The following classes have been removed:
    - mod_quiz_overdue_attempt_updater
    - moodle_quiz_exception

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- The const quiz_statistics\calculator::TIME_TO_CACHE has been removed.

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Final deprecation of:
    - mod_quiz\form\add_random_form::class
    - mod_quiz\local\structure\slot_random::set_tags()
    - mod_quiz\local\structure\slot_random::set_tags_by_id()
    - const quiz_statistics\calculator::TIME_TO_CACHE
    - quiz_add_random_questions()

  For more information see [MDL-78091](https://tracker.moodle.org/browse/MDL-78091)
- Removed the deprecated class callbacks `quiz_structure_modified` and `quiz_attempt_deleted` from mod_quiz, use the `structure_modified` and `attempt_state_changed` hooks instead. These callbacks were deprecated in Moodle 4.4 and were outputting deprecation warnings since then.

  For more information see [MDL-80327](https://tracker.moodle.org/browse/MDL-80327)

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
