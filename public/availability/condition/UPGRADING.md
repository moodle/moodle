# availability (plugin type) Upgrade notes

## 4.5

### Changed

- The base class `\core_availability\info::get_groups()` method now accepts a `$userid` parameter to specify which user you want to retrieve course groups (defaults to current user).

  For more information see [MDL-81850](https://tracker.moodle.org/browse/MDL-81850)
