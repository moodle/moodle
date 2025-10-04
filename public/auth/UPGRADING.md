# core_auth (subsystem / plugintype) Upgrade notes

## 5.1

### Added

- A new method called `get_additional_upgrade_token_parameters` has been added to `oauth2_client` class. This will allow custom clients to override this one and add their extra parameters for upgrade token request.

  For more information see [MDL-80380](https://tracker.moodle.org/browse/MDL-80380)

## 5.0

### Removed

- Cas authentication is removed from core and added to the following git repository: https://github.com/moodlehq/moodle-auth_cas

  For more information see [MDL-78778](https://tracker.moodle.org/browse/MDL-78778)
- Removed auth_mnet plugin from core

  For more information see [MDL-84307](https://tracker.moodle.org/browse/MDL-84307)
