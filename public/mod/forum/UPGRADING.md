# mod_forum Upgrade notes

## 5.3dev

### Added

- Added the method `discussion::get_discussion_navigation_buttons()` that returns data for the discussion navigation template.

  For more information see [MDL-88602](https://tracker.moodle.org/browse/MDL-88602)

### Changed

- Add new mod_forum_set_read_state web service to allow clients to manually mark individual forum posts as read or unread (when manual read tracking is enabled), returning a simple status and warnings structure.

  For more information see [MDL-87887](https://tracker.moodle.org/browse/MDL-87887)

## 5.2

### Deprecated

- The forum report entity `->get_context_joins()` method is deprecated, replaced with `->get_course_modules_joins(...)`

  For more information see [MDL-86699](https://tracker.moodle.org/browse/MDL-86699)

### Removed

- The `forum_print_discussion_header()` has been removed from `public/mod/forum/deprecatedlib.php`.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)

## 5.1

### Deprecated

- The function forum_tp_get_untracked_forums() has been deprecated because it is no longer used.

  For more information see [MDL-83893](https://tracker.moodle.org/browse/MDL-83893)
