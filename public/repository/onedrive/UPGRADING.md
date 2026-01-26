# repository_onedrive Upgrade notes

## 5.2dev

### Removed

- - The following methods have been removed from `public/repository/onedrive/lib.php`:
    - `\repository_onedrive::can_import_skydrive_files()`
    - `\repository_onedrive::import_skydrive_files()`

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)

## 4.5

### Removed

- The following previously deprecated methods have been removed and can no longer be used:
  - `\repository_onedrive::can_import_skydrive_files()`
  - `\repository_onedrive::import_skydrive_files()`

  For more information see [MDL-72620](https://tracker.moodle.org/browse/MDL-72620)
