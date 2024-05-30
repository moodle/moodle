# core_courseformat (subsystem / plugintype) Upgrade notes

## 4.5dev

### Added

- The constructor of `core_courseformat\output\local\state\cm` has been updated to accept a new optional parameter, `$istrackeduser`.
  If `istrackeduser` is pre-computed for the course module's course, it can be provided here to avoid an additional function call.

  For more information see [MDL-81610](https://tracker.moodle.org/browse/MDL-81610)
