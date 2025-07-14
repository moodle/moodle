# core_sms (subsystem) Upgrade notes

## 5.0

### Added

- Introducing a new function \core_sms\gateway::truncate_message() to truncate SMS message content according to the length limit of the gateway.

  For more information see [MDL-84342](https://tracker.moodle.org/browse/MDL-84342)

## 4.5

### Added

- A new `\core_sms` subsystem has been created.

  For more information see [MDL-81924](https://tracker.moodle.org/browse/MDL-81924)
