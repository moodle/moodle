# mod_forum Upgrade notes

## 5.2dev

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
