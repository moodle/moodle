# report (plugin type) Upgrade notes

## 4.5

### Added

- Report has been added to subsystem components list.

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)
- A new general output class, `\core_report\output\coursestructure`, has been created.

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)

### Changed

- The `\core\report_helper::print_report_selector()` method accepts a new `$additional`` argument for adding content to the tertiary navigation to align with the report selector.

  For more information see [MDL-78773](https://tracker.moodle.org/browse/MDL-78773)

### Removed

- The previously deprecated `\core\report_helper::save_selected_report()` method has been removed and can no longer be used.

  For more information see [MDL-72353](https://tracker.moodle.org/browse/MDL-72353)
