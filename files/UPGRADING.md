# core_files (subsystem) Upgrade notes

## 4.5

### Added

- A new hook, `\core_files\hook\after_file_created`, has been created to allow the inspection of files after they have been saved in the filesystem.

  For more information see [MDL-75850](https://tracker.moodle.org/browse/MDL-75850)
- A new hook, `\core_files\hook\before_file_created`, has been created to allow modification of a file immediately before it is stored in the file system.

  For more information see [MDL-83245](https://tracker.moodle.org/browse/MDL-83245)
