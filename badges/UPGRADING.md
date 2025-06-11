# core_badges (subsystem) Upgrade notes

## 4.5

### Added

- The following new webservices have been added:

   - `core_badges_enable_badges`

   - `core_badges_disable_badges`

  For more information see [MDL-82168](https://tracker.moodle.org/browse/MDL-82168)

### Changed

- New fields have been added to the return structure of the `core_badges_get_user_badge_by_hash` and `core_badges_get_user_badges` external functions:
    - `recipientid`: The ID of the user who received the badge.
    - `recipientfullname`: The full name of the user who received the badge.

  For more information see [MDL-82742](https://tracker.moodle.org/browse/MDL-82742)

### Deprecated

- The `badges/newbadge.php` page has been deprecated and merged with `badges/edit.php`. Please, use `badges/edit.php` instead.

  For more information see [MDL-43938](https://tracker.moodle.org/browse/MDL-43938)
- The `OPEN_BADGES_V1` constant is deprecated and should not be used anymore.

  For more information see [MDL-70983](https://tracker.moodle.org/browse/MDL-70983)
- The `course_badges` systemreport has been deprecated and merged with the badges systemreport. Please, use the badges systemreport instead.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)
- The `$showmanage` parameter to the `\core_badges\output\standard_action_bar` constructor has been deprecated and should not be used anymore.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)
- The `badges/view.php` page has been deprecated and merged with `badges/index.php`. Please, use `badges/index.php` instead.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)

### Removed

- Final removal of `BADGE_BACKPACKAPIURL` and `BADGE_BACKPACKWEBURL` constants.

  For more information see [MDL-70983](https://tracker.moodle.org/browse/MDL-70983)
