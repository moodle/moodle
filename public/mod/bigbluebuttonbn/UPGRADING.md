# mod_bigbluebuttonbn Upgrade notes

## 5.1

### Added

- Add activity_dates class to BigblueButton module.

  For more information see [MDL-83889](https://tracker.moodle.org/browse/MDL-83889)
- Add a new parameter to the mod_bigbluebuttonbn\recording::get_recordings_for_instance so to ignore instance group settings and return all recordings. This is an optional argement and no change is expected from existing calls.

  For more information see [MDL-86192](https://tracker.moodle.org/browse/MDL-86192)

## 4.5

### Added

- Added new `meeting_info` value to show presentation file on BBB activity page

  For more information see [MDL-82520](https://tracker.moodle.org/browse/MDL-82520)
- The `broker::process_meeting_events()` method has been extended to call the `::process_action()` method implemented by plugins.

  For more information see [MDL-82872](https://tracker.moodle.org/browse/MDL-82872)

### Removed

- Mobile support via plugin has been removed as it is now natively available in the Moodle App.

  For more information see [MDL-82447](https://tracker.moodle.org/browse/MDL-82447)
