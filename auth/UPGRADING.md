# core_auth (subsystem / plugintype) Upgrade notes

## 4.5.5

### Added

- A new method called `get_additional_upgrade_token_parameters` has been added to `oauth2_client` class. This will allow custom clients to override this one and add their extra parameters for upgrade token request.

  For more information see [MDL-80380](https://tracker.moodle.org/browse/MDL-80380)
