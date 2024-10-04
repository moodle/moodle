# tool_oauth2 Upgrade notes

## 4.5

### Added

- The `\core\oautuh2\client::get_additional_login_parameters()` method now supports adding the language code to the authentication request so that the OAuth2 login page matches the language in Moodle.

  For more information see [MDL-67554](https://tracker.moodle.org/browse/MDL-67554)
