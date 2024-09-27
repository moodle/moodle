# core_admin (subsystem) Upgrade notes

## 4.5dev+

### Removed

- The HTTP Server setting "Use slash arguments" (slasharguments) configuration setting and related to it has been removed. Calling the option with $CFG->slasharguments or get_config('slasharguments') is no longer available.

  For more information see [MDL-62640](https://tracker.moodle.org/browse/MDL-62640)
