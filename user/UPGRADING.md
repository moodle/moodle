# core_user (subsystem) Upgrade notes

## 4.5beta

### Added

- New `\core_user\hook\extend_user_menu` hook added to allow third party plugin to extend the user menu navigation

  For more information see [MDL-71823](https://tracker.moodle.org/browse/MDL-71823)
- New `\core_user\hook\extend_default_homepage` hook added to allow third-party plugins to extend the default homepage options for the site

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

### Changed

- The visibility of the methods: `check_access_for_dynamic_submission()` and `get_options()` in `\core_user\form\private_files` has been changed from protected to public.

  For more information see [MDL-78293](https://tracker.moodle.org/browse/MDL-78293)
- The user profile field `display_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name

  For more information see [MDL-82494](https://tracker.moodle.org/browse/MDL-82494)

### Deprecated

- The `participants_search::get_total_participants_count()` is no longer used since the total count can be obtained from `::get_participants()`

  For more information see [MDL-78030](https://tracker.moodle.org/browse/MDL-78030)
