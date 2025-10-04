# mod_folder Upgrade notes

## 5.1

### Deprecated

- The \mod_folder\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

## 5.0

### Removed

- Method htmllize_tree() has been removed. Please use renderable_tree_elements instead

  For more information see [MDL-79214](https://tracker.moodle.org/browse/MDL-79214)
