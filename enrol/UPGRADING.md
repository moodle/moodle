# core_enrol (subsystem / plugintype) Upgrade notes

## 5.0

### Added

- New method enrol_plugin::get_instance_name_for_management_page() can be used to display additional details next to the instance name.

  For more information see [MDL-84139](https://tracker.moodle.org/browse/MDL-84139)
- Plugins implementing enrol_page_hook() method are encouraged to use the renderable \core_enrol\output\enrol_page to produce HTML for the enrolment page. Forms should be displayed in a modal dialogue. See enrol_self plugin as an example.

  For more information see [MDL-84142](https://tracker.moodle.org/browse/MDL-84142)
- It's now possible for themes to override the course enrolment index page by overriding the new course renderer `enrolment_options` method

  For more information see [MDL-84143](https://tracker.moodle.org/browse/MDL-84143)

### Changed

- The `after_user_enrolled` hook now contains a `roleid` property to allow for listeners to determine which role was assigned during user enrolment (if any)

  The base enrolment `enrol_plugin::send_course_welcome_message_to_user` method also now accepts a `$roleid` parameter in order to correctly populate the `courserole` placeholder

  For more information see [MDL-83432](https://tracker.moodle.org/browse/MDL-83432)

### Removed

- Final removal of base `enrol_plugin` class method `update_communication`

  For more information see [MDL-80491](https://tracker.moodle.org/browse/MDL-80491)
- Removed enrol_mnet plugin from core

  For more information see [MDL-84310](https://tracker.moodle.org/browse/MDL-84310)
