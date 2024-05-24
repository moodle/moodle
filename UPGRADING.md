# Moodle Upgrade notes

This file contains important information for developers on changes to the Moodle codebase.

More detailed information on key changes can be found in the [Developer update notes](https://moodledev.io/docs/devupdate) for your version of Moodle.

The format of this change log follows the advice given at [Keep a CHANGELOG](https://keepachangelog.com).

## 4.5dev

### core

#### Removed

- The previously deprecated function `search_generate_text_SQL` has been removed and can no longer be used.

  For more information see [MDL-48940](https://tracker.moodle.org/browse/MDL-48940)
- The previously deprecated function `core_text::reset_caches()` has been removed and can no longer be used.

  For more information see [MDL-71748](https://tracker.moodle.org/browse/MDL-71748)
- The following previously deprecated methods have been removed and can no longer be used:
    - `renderer_base::should_display_main_logo`

  For more information see [MDL-73165](https://tracker.moodle.org/browse/MDL-73165)
- Final deprecation of print_error(). Use moodle_exception instead.

  For more information see [MDL-74484](https://tracker.moodle.org/browse/MDL-74484)

### report

#### Removed

- The previously deprecated `report_helper::save_selected_report` method has been removed and can no longer be used

  For more information see [MDL-72353](https://tracker.moodle.org/browse/MDL-72353)

### core_grades

#### Removed

- The following previously deprecated Behat step helper methods have been removed and can no longer be used:
   - `behat_grade::select_in_gradebook_navigation_selector`
   - `behat_grade::select_in_gradebook_tabs`

  For more information see [MDL-74581](https://tracker.moodle.org/browse/MDL-74581)

### core_reportbuilder

#### Removed

- The following previously deprecated local helper methods have been removed and can no longer be used:
    - `audience::get_all_audiences_menu_types`
    - `report::get_available_columns`

  For more information see [MDL-76690](https://tracker.moodle.org/browse/MDL-76690)

#### Changed

- In order to better support float values in filter forms, the following filter types now cast given SQL prior to comparison:
    - `duration`
    - `filesize`
    - `number`

  For more information see [MDL-81168](https://tracker.moodle.org/browse/MDL-81168)
- The base datasource `add_all_from_entities` method accepts a new optional parameter to specify which entities to add elements from

  For more information see [MDL-81330](https://tracker.moodle.org/browse/MDL-81330)

#### Added

- The following external methods now return tags data relevant to each custom report:
    - `core_reportbuilder_list_reports`
    - `core_reportbuilder_retrieve_report`

  For more information see [MDL-81433](https://tracker.moodle.org/browse/MDL-81433)
- Added a new database helper method `sql_replace_parameters` to help ensure uniqueness of parameters within a SQL expression

  For more information see [MDL-81434](https://tracker.moodle.org/browse/MDL-81434)

### mod_assign

#### Removed

- The default option "Never" for `attemptreopenmethod` setting, which disallowed multiple attempts at the assignment, has been removed. This option was unnecessary because limiting attempts to 1 through the `maxattempts` setting achieves the same behavior.

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)

#### Deprecated

- The constant `ASSIGN_ATTEMPT_REOPEN_METHOD_NONE` has been deprecated, and a new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)

#### Added

- A new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)

### core_question

#### Changed

- column_base::from_column_name now has an ignoremissing field, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81125](https://tracker.moodle.org/browse/MDL-81125)

### mod_data

#### Added

- The `data_add_record` method accepts a new `$approved` parameter to set the corresponding state of the new record

  For more information see [MDL-81274](https://tracker.moodle.org/browse/MDL-81274)

### core_courseformat

#### Added

- The constructor of `core_courseformat\output\local\state\cm` has been updated to accept a new optional parameter, `$istrackeduser`.
  If `istrackeduser` is pre-computed for the course module's course, it can be provided here to avoid an additional function call.

  For more information see [MDL-81610](https://tracker.moodle.org/browse/MDL-81610)
