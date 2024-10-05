# core_grades (subsystem) Upgrade notes

## 4.5

### Changed

- The grade `itemname` property contained in the return structure of the following external methods is now PARAM_RAW:
    - `core_grades_get_gradeitems`
    - `gradereport_user_get_grade_items`

  For more information see [MDL-80017](https://tracker.moodle.org/browse/MDL-80017)

### Deprecated

- The behat step definition `\behat_grade::i_confirm_in_search_within_the_gradebook_widget_exists()` has been deprecated. Please use `\behat_general::i_confirm_in_search_combobox_exists()` instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The behat step definition `\behat_grade::i_confirm_in_search_within_the_gradebook_widget_does_not_exist()` has been deprecated. Please use `\behat_general::i_confirm_in_search_combobox_does_not_exist()` instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The behat step definition `\behat_grade::i_click_on_in_search_widget()` has been deprecated. Please use `\behat_general::i_click_on_in_search_combobox()` instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The `\core_grades_renderer::group_selector()` method has been deprecated. Please use `\core_course\output\actionbar\renderer` to render a `group_selector` renderable instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

### Removed

- The following previously deprecated Behat step helper methods have been removed and can no longer be used:
   - `\behat_grade::select_in_gradebook_navigation_selector()`
   - `\behat_grade::select_in_gradebook_tabs()`

  For more information see [MDL-74581](https://tracker.moodle.org/browse/MDL-74581)
