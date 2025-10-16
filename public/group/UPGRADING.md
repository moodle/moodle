# core_group (subsystem) Upgrade notes

## 5.2dev

### Added

- `groups_print_activity_menu()` and `groups_get_activity_group()` now include an additional `$participationonly` parameter, which is true by default. This can be set false when we want the user to be able to select a non-participation group within an activity, for example if a teacher wants to filter assignment submissions by non-participation groups. It should never be used when the menu is displayed to students, as this may allow them to participate using non-participation groups. Non-participation groups are labeled as such.

  For more information see [MDL-81514](https://tracker.moodle.org/browse/MDL-81514)
