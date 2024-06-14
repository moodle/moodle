# availability (plugin type) Upgrade notes

## 4.5dev

### Changed

- The base class `info::get_groups` method has a `$userid` parameter to specify for which user you want to retrieve course groups (defaults to current user)

  For more information see [MDL-81850](https://tracker.moodle.org/browse/MDL-81850)
