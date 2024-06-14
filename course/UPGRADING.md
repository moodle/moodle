# core_course (subsystem) Upgrade notes

## 4.5dev

### Added

- - New optional sectionNum parameter has been added to activitychooser AMD module initializer. - New option sectionnum parameter has been added to get_course_content_items() external function. - New optional sectionnum parameter has been added to get_content_items_for_user_in_course() function.

  For more information see [MDL-81675](https://tracker.moodle.org/browse/MDL-81675)

### Deprecated

- The data-sectionid attribute in the activity chooser has been deprecated. Please update your code to use data-sectionnum instead.

  For more information see [MDL-81676](https://tracker.moodle.org/browse/MDL-81676)

### Changed

- The reset course page has been improved. The words "Delete" and "Remove" have been removed from all the options to make it easier to focus on the data to be removed and avoid inconsistencies and duplicated information. Third party plugins implementing reset methods might need to:
  - Add static element in the _reset_course_form_definition method before all the options with the Delete string:
      `$mform->addElement('static', 'assigndelete', get_string('delete'));`
  - Review all the strings used in the reset page to remove the "Delete" or "Remove" words from them.

  For more information see [MDL-81872](https://tracker.moodle.org/browse/MDL-81872)
