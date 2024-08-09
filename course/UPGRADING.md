# core_course (subsystem) Upgrade notes

## 4.5dev

### Added

- - New optional sectionNum parameter has been added to activitychooser AMD module initializer. - New option sectionnum parameter has been added to get_course_content_items() external function. - New optional sectionnum parameter has been added to get_content_items_for_user_in_course() function.

  For more information see [MDL-81675](https://tracker.moodle.org/browse/MDL-81675)
- Webservices `core_course_get_courses_by_field` now accepts a new parameter `sectionid` to be able to retrieve the course that has the indicated section

  For more information see [MDL-81699](https://tracker.moodle.org/browse/MDL-81699)
- i_open_section_edit_menu(), i_show_section(), i_hide_section(), i_wait_until_section_is_available(), show_section_link_exists(), hide_section_link_exists() and section_exists() functions have been improved to accept not only section number but also section name.

  For more information see [MDL-82259](https://tracker.moodle.org/browse/MDL-82259)

### Deprecated

- The data-sectionid attribute in the activity chooser has been deprecated. Please update your code to use data-sectionnum instead.

  For more information see [MDL-81676](https://tracker.moodle.org/browse/MDL-81676)
- The $course parameter in the constructor of the core_course\output\actionbar\group_selector class has been deprecated and is no longer used.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)

### Changed

- The reset course page has been improved. The words "Delete" and "Remove" have been removed from all the options to make it easier to focus on the data to be removed and avoid inconsistencies and duplicated information. Third party plugins implementing reset methods might need to:
  - Add static element in the _reset_course_form_definition method before all the options with the Delete string:
      `$mform->addElement('static', 'assigndelete', get_string('delete'));`
  - Review all the strings used in the reset page to remove the "Delete" or "Remove" words from them.

  For more information see [MDL-81872](https://tracker.moodle.org/browse/MDL-81872)

### Removed

- The $course class property in the core_course\output\actionbar\group_selector class has been removed.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)
