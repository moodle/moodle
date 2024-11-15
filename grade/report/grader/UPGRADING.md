# gradereport_grader Upgrade notes

## 5.0dev

### Deprecated

- The method `gradereport_grader::get_right_avg_row()` has been finally deprecated and will now throw an exception if called.

  For more information see [MDL-78890](https://tracker.moodle.org/browse/MDL-78890)

## 4.5

### Deprecated

- The `gradereport_grader/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)
