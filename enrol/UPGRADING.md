# core_enrol (subsystem / plugintype) Upgrade notes

## 4.5.1

### Changed

- The `after_user_enrolled` hook now contains a `roleid` property to allow for listeners to determine which role was assigned during user enrolment (if any)

  The base enrolment `enrol_plugin::send_course_welcome_message_to_user` method also now accepts a `$roleid` parameter in order to correctly populate the `courserole` placeholder

  For more information see [MDL-83432](https://tracker.moodle.org/browse/MDL-83432)
