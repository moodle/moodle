# report (plugin type) Upgrade notes

## 4.5dev

### Removed

- The previously deprecated `report_helper::save_selected_report` method has been removed and can no longer be used

  For more information see [MDL-72353](https://tracker.moodle.org/browse/MDL-72353)

### Changed

- The `report_helper::print_report_selector` method accepts an additional argument for adding content to the tertiary navigation to align with the report selector

  For more information see [MDL-78773](https://tracker.moodle.org/browse/MDL-78773)
