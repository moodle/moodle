# core_reportbuilder (subsystem) Upgrade notes

## 4.5dev

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

### Added

- The following external methods now return tags data relevant to each custom report:
    - `core_reportbuilder_list_reports`
    - `core_reportbuilder_retrieve_report`

  For more information see [MDL-81433](https://tracker.moodle.org/browse/MDL-81433)
- Added a new database helper method `sql_replace_parameters` to help ensure uniqueness of parameters within a SQL expression

  For more information see [MDL-81434](https://tracker.moodle.org/browse/MDL-81434)
