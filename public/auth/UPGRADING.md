# core_auth (subsystem / plugintype) Upgrade notes

## 5.3dev

### Added

- A new `\core_auth\validate_user` class has been introduced to centralise user validation checks for authentication flows. It is available via DI and provides the following validation methods:

  | Method | Purpose |
  |---|---|
  | `validate_before_external_login()` | Runs all pre-login checks for external services |
  | `validate_before_token_login()` | Runs all pre-login checks for token-based login |
  | `validate_before_web_login()` | Runs all pre-login checks for web login |
  | `validate_maintenance_mode_access()` | Checks maintenance mode access |
  | `validate_not_deleted()` | Ensures user is not deleted |
  | `validate_is_confirmed()` | Ensures user is confirmed |
  | `validate_is_not_suspended()` | Ensures user is not suspended |
  | `validate_auth_not_disabled()` | Ensures auth plugin is enabled |
  | `validate_credentials_not_expired()` | Checks password expiry |
  | `validate_user_is_not_guest_user()` | Ensures user is not a guest |

  Each method throws a specific exception from `\core_auth\exception` on failure.

  For more information see [MDL-88580](https://tracker.moodle.org/browse/MDL-88580)

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
