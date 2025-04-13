# core_badges (subsystem) Upgrade notes

## 5.0

### Added

- Added fields `courseid` and `coursefullname` to `badgeclass_exporter`, which is used in the return structure of external function `core_badges_get_badge`.

  For more information see [MDL-83026](https://tracker.moodle.org/browse/MDL-83026)
- Added field `coursefullname` to `user_badge_exporter`, which is used in the return structure of external functions `core_badges_get_user_badge_by_hash` and `core_badges_get_user_badges`.

  For more information see [MDL-83026](https://tracker.moodle.org/browse/MDL-83026)
- The class in badges/lib/bakerlib.php has been moved to core_badges\png_metadata_handler. If you've extended or directly used the old bakerlib.php, you'll need to update your code to use the new namespaced class.

  For more information see [MDL-83886](https://tracker.moodle.org/browse/MDL-83886)

### Removed

- The following previously deprecated renderer methods have been removed:

  * `print_badge_table_actions`
  * `render_badge_management`

  For more information see [MDL-79162](https://tracker.moodle.org/browse/MDL-79162)
- The fields imageauthorname, imageauthoremail, and imageauthorurl have been removed from badges due to confusion and their absence from the official specification. These fields also do not appear in OBv3.0. Additionally, the image_author_json.php file has been removed as it is no longer needed.

  For more information see [MDL-83909](https://tracker.moodle.org/browse/MDL-83909)

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
