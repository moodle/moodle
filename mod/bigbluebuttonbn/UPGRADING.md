# mod_bigbluebuttonbn Upgrade notes

## 4.5

### Added

- Added new `meeting_info` value to show presentation file on BBB activity page

  For more information see [MDL-82520](https://tracker.moodle.org/browse/MDL-82520)
- The `broker::process_meeting_events()` method has been extended to call the `::process_action()` method implemented by plugins.

  For more information see [MDL-82872](https://tracker.moodle.org/browse/MDL-82872)

### Removed

- Mobile support via plugin has been removed as it is now natively available in the Moodle App.

  For more information see [MDL-82447](https://tracker.moodle.org/browse/MDL-82447)
