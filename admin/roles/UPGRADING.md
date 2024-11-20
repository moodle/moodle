# core_role (subsystem) Upgrade notes

## 4.5

### Added

- All session management has been moved to the `\core\session\manager` class.
  This removes the dependancy to use the `sessions` table.

  Session management plugins (like Redis) should now inherit
  the base `\core\session\handler` class, which implements
  `SessionHandlerInterface`, and override methods as required.

  The following methods in `\core\session\manager` have been deprecated:
  | Old method name                  | New method name           |
  | ---                              | ---                       |
  | `kill_all_sessions`              | `destroy_all`             |
  | `kill_session`                   | `destroy`                 |
  | `kill_sessions_for_auth_plugin`  | `destroy_by_auth_plugin`  |
  | `kill_user_sessions`             | `destroy_user_sessions`   |

  For more information see [MDL-66151](https://tracker.moodle.org/browse/MDL-66151)
