# customfield_number Upgrade notes

## 4.5

### Added

- A new hook, `\customfield_number\hook\add_custom_providers`, has been added which allows automatic calculation of number course custom field.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
- A new class, `\customfield_number\local\numberproviders\nofactivities`, has been added that allows to automatically calculate number of activities of a given type in a given course.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
- Added new webservice `customfield_number_recalculate_value`, has been added to recalculate a value of number course custom field.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
- A new task, `\customfield_number\task\cron`, cron task that recalculates automatically calculated number course custom fields.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
