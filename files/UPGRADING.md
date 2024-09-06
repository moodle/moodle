# core_files (subsystem) Upgrade notes

## 4.5dev+

### Added

- The following are the changes made:
  - New hook after_file_created
  - In the \core_files\file_storage, new additional param $notify (default is true) added to:
    - ::create_file_from_storedfile()
    - ::create_file_from_pathname()
    - ::create_file_from_string()
    - ::create_file()
    If true, it will trigger the after_file_created hook to re-create the image.

  For more information see [MDL-75850](https://tracker.moodle.org/browse/MDL-75850)
