# gradereport_grader Upgrade notes

## 5.1

### Removed

- The previously deprecated methods have been removed:
    - grade_report_grader::get_left_icons_row
    - grade_report_grader::get_right_icons_row
    - grade_report_grader::get_icons

  For more information see [MDL-77307](https://tracker.moodle.org/browse/MDL-77307)

## 5.0

### Deprecated

- The method `gradereport_grader::get_right_avg_row()` has been finally deprecated and will now throw an exception if called.

  For more information see [MDL-78890](https://tracker.moodle.org/browse/MDL-78890)

### Removed

- The `behat_gradereport_grader::get_grade_item_id` step helper has been removed, please use the equivalent `behat_grades` method instead

  For more information see [MDL-77107](https://tracker.moodle.org/browse/MDL-77107)

## 4.5

### Deprecated

- The `gradereport_grader/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)
