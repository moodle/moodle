# core_admin (subsystem) Upgrade notes

## 5.3dev

### Deprecated

- The `core_admin_renderer::upgradekey_form_page(...)` method has been deprecated, existing callers and/or overrides of this method should instead use replacement `core_admin_renderer::upgradekey_form_page_with_validation(...)`

  For more information see [MDL-87896](https://tracker.moodle.org/browse/MDL-87896)

## 5.1

### Added

- - Added `searchmatchtype` property to `admin_settings`
    to track search match type.
  - Plugins that extend either `admin_settings` or `admin_externalpage`
    are encouraged to specify a search match type from the available
    types in `admin_search`.

  For more information see [MDL-85518](https://tracker.moodle.org/browse/MDL-85518)
