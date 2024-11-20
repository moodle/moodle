# mod_assign Upgrade notes

## 4.5

### Added

- Added 2 new settings:
    - `mod_assign/defaultgradetype`
      - The value of this setting dictates which of the `GRADE_TYPE_X` constants is the default option when creating new instances of the assignment.
      - The default value is `GRADE_TYPE_VALUE` (Point)
    - `mod_assign/defaultgradescale`
      - The value of this setting dictates which of the existing scales is the default option when creating new instances of the assignment.

  For more information see [MDL-54105](https://tracker.moodle.org/browse/MDL-54105)
- A new web service called `mod_assign_remove_submission` has been created to remove the submission for a specific user ID and assignment activity ID.

  For more information see [MDL-74050](https://tracker.moodle.org/browse/MDL-74050)
- A new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- A new method, `\assign_feedback_plugin::get_grading_batch_operation_details()`, has been added to the `assign_feedback_plugin` abstract class. Assignment feedback plugins can now override this method to define bulk action buttons that will appear in the sticky footer on the assignment grading page.

  For more information see [MDL-80750](https://tracker.moodle.org/browse/MDL-80750)

### Deprecated

- The constant `ASSIGN_ATTEMPT_REOPEN_METHOD_NONE` has been deprecated, and a new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- The `\assign_feedback_plugin::get_grading_batch_operations()` method is now deprecated. Use `assign_feedback_plugin::get_grading_batch_operation_details` instead.

  For more information see [MDL-80750](https://tracker.moodle.org/browse/MDL-80750)
- The `\assign_grading_table::plugingradingbatchoperations` property has been removed. You can use `\assign_feedback_plugin::get_grading_batch_operation_details()` instead.

  For more information see [MDL-80750](https://tracker.moodle.org/browse/MDL-80750)
- The `$submissionpluginenabled` and `$submissioncount` parameters from the constructor of the `\mod_assign\output::grading_actionmenu` class have been deprecated.

  For more information see [MDL-80752](https://tracker.moodle.org/browse/MDL-80752)
- The method `\assign::process_save_grading_options()` has been deprecated as it is no longer used.

  For more information see [MDL-82681](https://tracker.moodle.org/browse/MDL-82681)

### Removed

- The default option "Never" for the `attemptreopenmethod` setting, which disallowed multiple attempts at the assignment, has been removed. This option was unnecessary because limiting attempts to 1 through the `maxattempts` setting achieves the same behavior.

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- The `\mod_assign_grading_options_form` class has been removed since it is no longer used.

  For more information see [MDL-82857](https://tracker.moodle.org/browse/MDL-82857)
