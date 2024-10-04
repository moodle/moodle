# mod_feedback Upgrade notes

## 5.0

### Added

- Added new `mod_feedback_questions_reorder` external function

  For more information see [MDL-81745](https://tracker.moodle.org/browse/MDL-81745)

### Deprecated

- The 'mode' parameter has been deprecated from 'edit_template_action_bar' and 'templates_table' contructors.

  For more information see [MDL-81744](https://tracker.moodle.org/browse/MDL-81744)

### Removed

- The 'use_template' template has been removed as it is not needed anymore.

  For more information see [MDL-81744](https://tracker.moodle.org/browse/MDL-81744)

## 4.5

### Deprecated

- The `\feedback_check_is_switchrole()` function has been deprecated as it didn't work.

  For more information see [MDL-72424](https://tracker.moodle.org/browse/MDL-72424)
- The method `\mod_feedback\output\renderer::create_template_form()` has been deprecated. It is not used anymore.

  For more information see [MDL-81742](https://tracker.moodle.org/browse/MDL-81742)
