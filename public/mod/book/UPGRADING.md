# mod_book Upgrade notes

## 5.1

### Deprecated

- The \mod_book\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

## 5.0

### Deprecated

- The method book_get_nav_classes has been finally
  deprecated and will now throw an exception if called.

  For more information see [MDL-81328](https://tracker.moodle.org/browse/MDL-81328)
