# core_table (subsystem) Upgrade notes

## 4.5rc1

### Added

- A new `$reponsive` property (defaulting to `true`) has been added to the `core_table\flexible_table` class.
  This property allows you to control whether the table is rendered as a responsive table.

  For more information see [MDL-80748](https://tracker.moodle.org/browse/MDL-80748)

### Changed

- `core_table\dynamic` declares a new method `::has_capability()` to allow classes implementing this interface to perform access checks on the dynamic table. This is a breaking change that all dynamic table implementations must implement for continued functionality.

  For more information see [MDL-82567](https://tracker.moodle.org/browse/MDL-82567)
