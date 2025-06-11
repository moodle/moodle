# core_customfield (subsystem) Upgrade notes

## 4.5

### Changed

- The field controller `\core_customfield\field_controller::get_formatted_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name.

  For more information see [MDL-82488](https://tracker.moodle.org/browse/MDL-82488)
