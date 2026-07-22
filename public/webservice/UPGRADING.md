# core_webservice (subsystem / plugintype) Upgrade notes

## 5.3dev

### Added

- New "allowcorsrequests" property in external functions definition for allowing specific AJAX Web Services to support CORS

  For more information see [MDL-87150](https://tracker.moodle.org/browse/MDL-87150)

## 5.2

### Changed

- The WebService core_webservice_get_site_info now returns three new fields: "usercanviewconfig" indicating whether the current user can see the administration tree, "usercanchangeconfig" indicating whether the current user can change the site configuration, and site secret.

  For more information see [MDL-87034](https://tracker.moodle.org/browse/MDL-87034)
