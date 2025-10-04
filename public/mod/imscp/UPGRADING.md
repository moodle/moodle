# mod_imscp Upgrade notes

## 5.1

### Deprecated

- The \mod_imscp\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

## 5.0

### Removed

- Final removal of deprecated `imscp_libxml_disable_entity_loader` function

  For more information see [MDL-78635](https://tracker.moodle.org/browse/MDL-78635)
