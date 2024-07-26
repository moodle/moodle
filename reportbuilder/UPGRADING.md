# core_reportbuilder (subsystem) Upgrade notes

## 4.5dev

### Added

- The return type of the `set_checkbox_toggleall` callback, defined by system reports, can now be null. Use if the checkbox should not be shown for the row.

  For more information see [MDL-52046](https://tracker.moodle.org/browse/MDL-52046)
- System reports now support native entity column aggregation via each columns `set_aggregation()` method

  For more information see [MDL-76392](https://tracker.moodle.org/browse/MDL-76392)
- The following external methods now return tags data relevant to each custom report:
    - `core_reportbuilder_list_reports`
    - `core_reportbuilder_retrieve_report`

  For more information see [MDL-81433](https://tracker.moodle.org/browse/MDL-81433)
- Added a new database helper method `sql_replace_parameters` to help ensure uniqueness of parameters within a SQL expression

  For more information see [MDL-81434](https://tracker.moodle.org/browse/MDL-81434)
- New format helper `format_time` method, for use in column callbacks that represent a duration of time (e.g. "3 days 4 hours")

  For more information see [MDL-82466](https://tracker.moodle.org/browse/MDL-82466)
- Methods add_columns_from_entity(), add_filters_from_entity() and report_element_search() have been moved from \core_reportbuilder\datasource class to \core_reportbuilder\base class in order to be available also for system reports

  For more information see [MDL-82529](https://tracker.moodle.org/browse/MDL-82529)

### Removed

- The following previously deprecated local helper methods have been removed and can no longer be used:
    - `audience::get_all_audiences_menu_types`
    - `report::get_available_columns`

  For more information see [MDL-76690](https://tracker.moodle.org/browse/MDL-76690)

### Changed

- In order to better support float values in filter forms, the following filter types now cast given SQL prior to comparison:
    - `duration`
    - `filesize`
    - `number`

  For more information see [MDL-81168](https://tracker.moodle.org/browse/MDL-81168)
- The base datasource `add_all_from_entities` method accepts a new optional parameter to specify which entities to add elements from

  For more information see [MDL-81330](https://tracker.moodle.org/browse/MDL-81330)
- All time related code has been updated to the PSR-20 Clock interface, as such the following methods no longer accept a `$timenow` parameter (instead please use `\core\clock` dependency injection):
  - `core_reportbuilder_generator::create_schedule`
  - `core_reportbuilder\local\helpers\schedule::[create_schedule|calculate_next_send_time]`

  For more information see [MDL-82041](https://tracker.moodle.org/browse/MDL-82041)
- The following classes have been moved to use the new exception API as a l2 namespace:
  - `core_reportbuilder\\report_access_exception` => `core_reportbuilder\\exception\\report_access_exception` - `core_reportbuilder\\source_invalid_exception` => `core_reportbuilder\\exception\\source_invalid_exception` - `core_reportbuilder\\source_unavailable_exception` => `core_reportbuilder\\exception\\source_unavailable_exception`

  For more information see [MDL-82133](https://tracker.moodle.org/browse/MDL-82133)
