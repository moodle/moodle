# core_admin (subsystem) Upgrade notes

## 5.1

### Added

- - Added `searchmatchtype` property to `admin_settings`
    to track search match type.
  - Plugins that extend either `admin_settings` or `admin_externalpage`
    are encouraged to specify a search match type from the available
    types in `admin_search`.

  For more information see [MDL-85518](https://tracker.moodle.org/browse/MDL-85518)
