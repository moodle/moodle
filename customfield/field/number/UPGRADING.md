# customfield_number Upgrade notes

## 4.5dev+

### Added

- New 'customfield_number\hook\add_custom_providers' hook has been added.
  It allows automatic calculation of number course custom field.
  Added new class '\customfield_number\local\numberproviders\nofactivities'
  that allows to automatically calculate number of activities of a given
  type in a given course.
  Added new webservice customfield_number_recalculate_value to recalculate
  a value of number course custom field.
  Added 'customfield_number\task\cron' cron task that recalculates
  automatically calculated number course custom fields.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)

