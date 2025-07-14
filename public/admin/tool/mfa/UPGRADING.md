# tool_mfa Upgrade notes

## 5.0

### Added

- The new factor management table uses `plugin_management_table`, so not only the functions that changed, but the file needs to be moved from `admin/tool/mfa/classes/local/admin_setting_managemfa.php` to `admin/tool/mfa/classes/table/admin_setting_managemfa.php`

  For more information see [MDL-83516](https://tracker.moodle.org/browse/MDL-83516)
- Introduce the new language string `settings:shortdescription`, which is mandatory for each factor.

  For more information see [MDL-83516](https://tracker.moodle.org/browse/MDL-83516)

### Deprecated

- The two language strings in the tool_mfa plugin, namely `inputrequired` and `setuprequired`, are deprecated.

  For more information see [MDL-83516](https://tracker.moodle.org/browse/MDL-83516)

### Removed

- The previously deprecated `setup_factor` renderer method has been removed

  For more information see [MDL-80995](https://tracker.moodle.org/browse/MDL-80995)
