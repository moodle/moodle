# core_role (subsystem) Upgrade notes

## 4.5dev+

### Added

- Move all session management to the \core\session\manager class.
  This removes the dependancy to use the "sessions" table.
  Session management plugins (like redis) now need to inherit
  the base \core\session\handler class which implements
  SessionHandlerInterface and override methods as required.
  The following methods in \core\session\manager have been deprecated:
  * kill_all_sessions use destroy_all instead
  * kill_session use destroy instead
  * kill_sessions_for_auth_plugin use destroy_by_auth_plugin instead
  * kill_user_sessions use destroy_user_sessions instead

  For more information see [MDL-66151](https://tracker.moodle.org/browse/MDL-66151)
