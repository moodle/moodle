# core_table (subsystem) Upgrade notes

## 4.5

### Added

- A new `$reponsive` property (defaulting to `true`) has been added to the `\core_table\flexible_table` class.
  This property allows you to control whether the table is rendered as a responsive table.

  For more information see [MDL-80748](https://tracker.moodle.org/browse/MDL-80748)

### Changed

- The `\core_table\dynamic` class declares a new method `::has_capability()` to allow classes implementing this interface to perform access checks on the dynamic table.
  Note: This is a breaking change. All implementations of the `\core_table\dynamic` table interface _must_ implement the new `has_capability(): bool` method for continued functionality.

  For more information see [MDL-82567](https://tracker.moodle.org/browse/MDL-82567)
