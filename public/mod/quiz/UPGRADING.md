# mod_quiz Upgrade notes

## 5.3dev

### Added

- - Add a new external web service get_users_in_report to get the list of users in quiz report.

  For more information see [MDL-81096](https://tracker.moodle.org/browse/MDL-81096)
- A new function print_action_bar has been added to mod/quiz/classes/local/reports/report_base.php. This function is intended to be called by the Quiz report to render a new navigation bar with user-specific filters. Depending on the settings, the new navigation bar will consist of four components:
    + Quiz Report selectors: Displays a list of the current quiz reports that the user can access.
    + User search: Allows users to search and filter reports based on query string.
    + Group selector: Provides a dropdown or selector to filter reports based on user groups.
    + Initial bars filters
  Quiz Report selectors is render by mod/quiz/classes/output/quiz_report_action_selector.php User search, group selector, initial bars filters is implement in mod/quiz/classes/output/quiz_report_navigation_bar.php Add new has_permission to check user permission for each quiz report. Add new setup_report_data function to allow reports customize their sql data and table class. Override QuizUserSearch from combosearch/user to allow quiz report can search and filter user.

  For more information see [MDL-81096](https://tracker.moodle.org/browse/MDL-81096)
- A new duedate field added to 'quiz' and 'quiz_overrides' tables

  For more information see [MDL-82521](https://tracker.moodle.org/browse/MDL-82521)

### Changed

- If your plugin implements its own quiz report (a subclass of `mod_quiz\local\reports\report_base`), you need to update it to keep the old group selector working, because `report_base::print_header_and_tabs()` no longer prints it, and to pick up the new navigation bar (report selector, group selector, user search and initials filter).
  There are two ways to upgrade, depending on how much of the new bar you want:
  - Minimal (see `quiz_statistics_report` for an example): pass `null` for `$options` when calling `$this->print_action_bar('statistics', null, $cm, $reporturl);`. This restores the report selector and group selector while omitting the user search and initials filter widgets.
  IMPORTANT: If your plugin calls `print_standard_header_and_messages()` but does NOT implement `setup_report_data()`, you must explicitly replace `$options` with `null` as the 4th parameter (`$this->print_standard_header_and_messages($cm, $course, $quiz, null, ...)`). Passing `$options` without implementing `setup_report_data()` will cause the AJAX user search widget to fail.
  - Full (see `quiz_responses_report` or `quiz_overview_report` for an example): override `setup_report_data(stdClass $quiz, cm_info $cm, stdClass $course, ?context $context = null): array` to build and return `[$options, $table, $allowedjoins]` for your report (this is what the new `mod_quiz_get_users_in_report` web service calls to populate the search widget), and pass your `attempts_report_options` instance as `$options` into `print_standard_header_and_messages()`.
  If your report enforces its own capability check instead of `mod/quiz:viewreports`, override `has_permission(context $context): void` (see `quiz_grading_report` for an example) rather than calling `require_capability()` directly in `display()`, since `has_permission()` is also called by the `mod_quiz_get_users_in_report` web service before it builds your report's data.

  For more information see [MDL-81096](https://tracker.moodle.org/browse/MDL-81096)

## 5.2

### Added

- `mod_quiz_cm_info_dynamic()` now uses the new `quiz_overrides` cache via `override_manager`, performing a single cache fetch per quiz/user. This significantly reduces cache calls on course pages with many quizzes and groups.
  The new `mod_quiz:quiz_overrides` cache is keyed by `quizid_userid` using datasource `\mod_quiz\cache\quiz_overrides_cache`. This cache returns all applicable overrides for a user in a quiz (the user override, if any, plus all group overrides for groups they belong to in the quiz's course).
  New class `\mod_quiz\local\quiz_overrides_cache_manager` to interact with the cache:
    - `get_overrides(int $quizid, int $userid): array`
    - `purge_for_user(int $quizid, int $userid): void`
    - `purge_for_users(int $quizid, array $userids): void`
    - `purge_for_group(int $quizid, int $groupid): void`
    - `purge_for_group_members(int $groupid, array $userids): void`

  Hook callbacks in `db/hooks.php` to keep the cache in sync with group membership changes:
    - `\core_group\hook\after_group_membership_added`
    - `\core_group\hook\after_group_membership_removed`

  For more information see [MDL-86493](https://tracker.moodle.org/browse/MDL-86493)

### Changed

- The WebServices mod_quiz_get_user_best_grade and mod_quiz_get_user_quiz_attempts have been updated to return overall feedback even when quiz marks are hidden in the review options. This change aligns the WebService behaviour with Moodle LMS display logic.

  For more information see [MDL-86916](https://tracker.moodle.org/browse/MDL-86916)

### Deprecated

- The language strings `addpagebreak` and `removepagebreak` have been deprecated and should no longer be used. These have been replaced by the `addpagebreakafter` and `removepagebreakafter` language strings.

  For more information see [MDL-81608](https://tracker.moodle.org/browse/MDL-81608)
- The quiz overrides cache implementation has been replaced with a faster alternative with a different API. This should be a transparent change but any direct references will still need to be updated.

  For more information see [MDL-86493](https://tracker.moodle.org/browse/MDL-86493)
- The `mod_quiz_output_fragment_switch_question_bank()` Fragment API callback is deprecated in favour of `core_question\route\api\bank::banks()`, available via the route `/api/rest/v2/core/question/banks?courseid=X`.

  For more information see [MDL-87264](https://tracker.moodle.org/browse/MDL-87264)
- The "gobacktoquiz" and "selectquestionbank" lang strings have been deprecated. These are only used by the question bank switching UI, so have been replaced with the "switchergoback" and "switcherselectbank" strings in the core_question component.

  For more information see [MDL-87264](https://tracker.moodle.org/browse/MDL-87264)

### Removed

- - The following functions have been removed from `public/mod/quiz/deprecatedlib.php`:
    - `quiz_has_question_use()`
    - `quiz_update_sumgrades()`
    - `quiz_update_all_attempt_sumgrades()`
    - `quiz_update_all_final_grades()`
    - `quiz_set_grade()`
    - `quiz_save_best_grade()`
    - `quiz_calculate_best_grade()`
    - `quiz_calculate_best_attempt()`
    - `quiz_delete_override()`
    - `quiz_delete_all_overrides()`
    - `quiz_add_random_questions()`
  - The following functions have been removed from `public/mod/quiz/classes/output/renderer.php`:
    - `\mod_quiz\output\renderer::no_questions_message()`
    - `\mod_quiz\output\renderer::render_mod_quiz_links_to_other_attempts()`
    - `\mod_quiz\output\renderer::render_quiz_nav_question_button()`
    - `\mod_quiz\output\renderer::render_quiz_nav_section_heading()`
  - The following functions have been removed from `public/mod/quiz/classes/local/structure/slot_random.php`:
    - `\mod_quiz\local\structure\slot_random::set_tags()`
    - `\mod_quiz\local\structure\slot_random::set_tags_by_id()`
  - The `\mod_quiz\structure::is_display_number_customised()` has been removed from `public/mod/quiz/classes/structure.php`.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)

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
