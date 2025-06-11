# core_course (subsystem) Upgrade notes

## 4.5

### Added

- - New optional `sectionNum` parameter has been added to `activitychooser` AMD module initializer.
  - New option `sectionnum` parameter has been added to `get_course_content_items()` external function.
  - New optional `sectionnum` parameter has been added to `get_content_items_for_user_in_course()` function.

  For more information see [MDL-81675](https://tracker.moodle.org/browse/MDL-81675)
- The `core_course_get_courses_by_field` web service now accepts a new parameter `sectionid` to be able to retrieve the course that has the indicated section.

  For more information see [MDL-81699](https://tracker.moodle.org/browse/MDL-81699)
- Added new `activitychooserbutton` output class to display the activitychooser button. New `action_links` can be added to the button via hooks converting it into a dropdown.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- New `\core_course\hook\before_activitychooserbutton_exported` hook added to allow third-party plugins to extend activity chooser button options.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- The following methods have been updated to accept a section name in addition to the section number:
  - `\behat_course::i_open_section_edit_menu()`
  - `\behat_course::i_show_section()`
  - `\behat_course::i_hide_section(),`
  - `\behat_course::i_wait_until_section_is_available()`
  - `\behat_course::show_section_link_exists()`
  - `\behat_course::hide_section_link_exists()`
  - `\behat_course::section_exists()`

  For more information see [MDL-82259](https://tracker.moodle.org/browse/MDL-82259)

### Changed

- The reset course page has been improved. The words "Delete" and "Remove" have been removed from all the options to make it easier to focus on the data to be removed and avoid inconsistencies and duplicated information. Third party plugins implementing reset methods might need to:
  - Add static element in the _reset_course_form_definition method before all the options with the Delete string:
      `$mform->addElement('static', 'assigndelete', get_string('delete'));`
  - Review all the strings used in the reset page to remove the "Delete" or "Remove" words from them.

  For more information see [MDL-81872](https://tracker.moodle.org/browse/MDL-81872)
- The external function `core_course_get_contents` now returns the `component` and `itemid` of sections.

  For more information see [MDL-82385](https://tracker.moodle.org/browse/MDL-82385)

### Deprecated

- The `data-sectionid` attribute in the activity chooser has been deprecated. Please update your code to use `data-sectionnum` instead.

  For more information see [MDL-81676](https://tracker.moodle.org/browse/MDL-81676)
- The `$course` parameter in the constructor of the `\core_course\output\actionbar\group_selector` class has been deprecated and is no longer used.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)

### Removed

- The previously deprecated `\print_course_request_buttons()` method has been removed and can no longer be used.

  For more information see [MDL-73976](https://tracker.moodle.org/browse/MDL-73976)
- The `$course` class property in the `\core_course\output\actionbar\group_selector` class has been removed.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)
