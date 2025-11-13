# core_badges (subsystem) Upgrade notes

## 5.2dev

### Changed

- The create_issued_badge generator now returns the issued badge object.

  For more information see [MDL-85621](https://tracker.moodle.org/browse/MDL-85621)

### Deprecated

- The class core_badges_assertion has been deprecated and replaced by \core_badges\achievement_credential. The method badges_get_default_issuer() has also been deprecated because it is no longer needed. The file badges/endorsement.php has been removed because it stopped being used when MDL-84323 was integrated.

  For more information see [MDL-85621](https://tracker.moodle.org/browse/MDL-85621)

## 5.1

### Added

- The class core_badges_observer in badges/classes/observer.php has been moved to  core_badges\event\observer in badges/classes/event/observer.php. A compatibility  layer has been added to maintain backward compatibility, but direct use of the old  class name is now deprecated. If you've extended or directly used the old class,  you should update your code to use the new namespaced class.

  For more information see [MDL-83904](https://tracker.moodle.org/browse/MDL-83904)
- A number of new static methods have been added to `core_badges\backpack_api` to support the new Canvas Credentials backpack provider. These methods allow you to retrieve lists of providers and regions, check if Canvas Credentials fields should be displayed, and get a region URL or API URL based on a given region ID. The new methods include `get_providers`, `get_regions`, `display_canvas_credentials_fields`, `get_region_url`, `get_region_api_url`, `get_regionid_from_url`, and `is_canvas_credentials_region`.

  For more information see [MDL-86174](https://tracker.moodle.org/browse/MDL-86174)

### Removed

- Final removal of core_badges_renderer::render_badge_collection() and core_badges_renderer::render_badge_recipients()

  For more information see [MDL-80455](https://tracker.moodle.org/browse/MDL-80455)

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
