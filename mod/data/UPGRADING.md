# mod_data Upgrade notes

## 4.5

### Added

- The `\data_add_record()` method accepts a new `$approved` parameter to set the corresponding state of the new record.

  For more information see [MDL-81274](https://tracker.moodle.org/browse/MDL-81274)

### Deprecated

- The `\mod_data_renderer::render_fields_footer()` method has been deprecated as it's no longer used.

  For more information see [MDL-81321](https://tracker.moodle.org/browse/MDL-81321)
