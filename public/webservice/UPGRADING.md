# core_webservice (subsystem / plugintype) Upgrade notes

## 5.2dev

### Changed

- The WebService core_webservice_get_site_info now returns three new fields: "usercanviewconfig" indicating whether the current user can see the administration tree, "usercanchangeconfig" indicating whether the current user can change the site configuration, and site secret.

  For more information see [MDL-87034](https://tracker.moodle.org/browse/MDL-87034)
