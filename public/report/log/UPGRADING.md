# report_log Upgrade notes

## 5.0

### Removed

- Support for the $grouplist public member in the report_log_renderable class has been removed.

  For more information see [MDL-81155](https://tracker.moodle.org/browse/MDL-81155)

## 4.5

### Added

- The `\report_log_renderable::get_activities_list()` method return values now includes an array of disabled elements, in addition to the array of activities.

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)
