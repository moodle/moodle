# gradereport_user Upgrade notes

## 5.3dev

### Changed

- The external function `gradereport_user_get_grade_items` now includes the optional `parentcategoryid` field in its response for category grade items.

  For more information see [MDL-64304](https://tracker.moodle.org/browse/MDL-64304)

## 4.5

### Deprecated

- The `gradereport_user/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)
