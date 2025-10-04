# mod_scorm Upgrade notes

## 5.1

### Added

- Create a manager class to regroup common functionalities for course overview page

  For more information see [MDL-83899](https://tracker.moodle.org/browse/MDL-83899)
- Add a new generator for scorm attempts to simulate user's attempt.

  For more information see [MDL-83899](https://tracker.moodle.org/browse/MDL-83899)
- Add group id list to \mod_scorm\manager::count_users_who_attempted and \mod_scorm\manager::count_participants so we can filter by groups. Empty array means no filtering.

  For more information see [MDL-86216](https://tracker.moodle.org/browse/MDL-86216)

### Deprecated

- The method `\mod_scorm\report::generate_master_checkbox()` has been deprecated and should no longer be used. SCORM report plugins calling this method should use `\mod_scorm\report::generate_toggler_checkbox()` instead.

  For more information see [MDL-79756](https://tracker.moodle.org/browse/MDL-79756)
