# core_files (subsystem) Upgrade notes

## 5.0

### Added

- A new function `file_clear_draft_area()` has been added to delete the files in a draft area.

  For more information see [MDL-72050](https://tracker.moodle.org/browse/MDL-72050)
- Adds a new ad-hoc task `core_files\task\asynchronous_mimetype_upgrade_task` to upgrade the mimetype of files
  asynchronously during core upgrades. The upgradelib also comes with a new utility function
  `upgrade_create_async_mimetype_upgrade_task` for creating said ad-hoc task.

  For more information see [MDL-81437](https://tracker.moodle.org/browse/MDL-81437)

## 4.5

### Added

- A new hook, `\core_files\hook\after_file_created`, has been created to allow the inspection of files after they have been saved in the filesystem.

  For more information see [MDL-75850](https://tracker.moodle.org/browse/MDL-75850)
- A new hook, `\core_files\hook\before_file_created`, has been created to allow modification of a file immediately before it is stored in the file system.

  For more information see [MDL-83245](https://tracker.moodle.org/browse/MDL-83245)
