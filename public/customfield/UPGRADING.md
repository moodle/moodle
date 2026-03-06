# core_customfield (subsystem) Upgrade notes

## 5.2dev

### Added

- Added new `\core_customfield\api::is_shortname_unique(...)` method to determine whether a shortname is available for use inside a given handler

  For more information see [MDL-87059](https://tracker.moodle.org/browse/MDL-87059)
- A new WebService `core_customfield_convert_category` has been added. It allows the conversion of any entity custom field category to a shared category.

  For more information see [MDL-87690](https://tracker.moodle.org/browse/MDL-87690)

### Changed

- The WebService `core_customfield_reload_template` now returns a new parameter "canconvert".

  For more information see [MDL-87690](https://tracker.moodle.org/browse/MDL-87690)

### Deprecated

- The Javascript module `core_customfield/repository/toggle_shared` has been deprecated. Please, use `core_customfield/repository` instead.

  For more information see [MDL-87690](https://tracker.moodle.org/browse/MDL-87690)

## 5.1

### Changed

- Added parameters 'component', 'area' and 'itemid' to the `api::get_instance_fields_data()` and `api::get_instances_fields_data()` methods. Added a new field 'shared' to the customfield_category DB table. Added 'component', 'area' and 'itemid' fields to the customfield_data DB table. Modified the customfield_data DB table unique index to include the new fields.

  For more information see [MDL-86065](https://tracker.moodle.org/browse/MDL-86065)

## 5.0

### Added

- Added a new custom field exporter to export custom field data in `\core_customfield\external\field_data_exporter`

  For more information see [MDL-83552](https://tracker.moodle.org/browse/MDL-83552)

## 4.5

### Changed

- The field controller `\core_customfield\field_controller::get_formatted_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name.

  For more information see [MDL-82488](https://tracker.moodle.org/browse/MDL-82488)
