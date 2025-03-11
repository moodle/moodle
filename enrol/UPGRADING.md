# core_enrol (subsystem / plugintype) Upgrade notes

## 5.0dev+

### Added

- New method enrol_plugin::get_instance_name_for_management_page() can be used to display additional details next to the instance name.

  For more information see [MDL-84139](https://tracker.moodle.org/browse/MDL-84139)

### Changed

- The `after_user_enrolled` hook now contains a `roleid` property to allow for listeners to determine which role was assigned during user enrolment (if any)

  The base enrolment `enrol_plugin::send_course_welcome_message_to_user` method also now accepts a `$roleid` parameter in order to correctly populate the `courserole` placeholder

  For more information see [MDL-83432](https://tracker.moodle.org/browse/MDL-83432)

### Removed

- Removed enrol_mnet plugin from core

  For more information see [MDL-84310](https://tracker.moodle.org/browse/MDL-84310)
