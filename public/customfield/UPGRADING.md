# core_customfield (subsystem) Upgrade notes

## 5.0

### Added

- Added a new custom field exporter to export custom field data in `\core_customfield\external\field_data_exporter`

  For more information see [MDL-83552](https://tracker.moodle.org/browse/MDL-83552)

## 4.5

### Changed

- The field controller `\core_customfield\field_controller::get_formatted_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name.

  For more information see [MDL-82488](https://tracker.moodle.org/browse/MDL-82488)
