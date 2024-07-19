# core_user (subsystem) Upgrade notes

## 4.5dev

### Changed

- The visibility of the methods: check_access_for_dynamic_submission() and get_options() in core_user\form\private_files has been changed from protected to public.

  For more information see [MDL-78293](https://tracker.moodle.org/browse/MDL-78293)

### Added

- New `\core_user\hook\extend_default_homepage` hook added to allow third-party plugins to extend the default homepage options for the site

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

