# core_backup (subsystem) Upgrade notes

## 4.5dev

### Removed

- Final deprecation and removal of core_backup\copy\copy in backup/util/ui/classes/copy.php. Please use copy_helper from backup/util/helper/copy_helper.class.php instead.

  For more information see [MDL-75022](https://tracker.moodle.org/browse/MDL-75022)
- Final deprecation of base_controller::get_copy(). Please use restore_controller::get_copy() instead.

  For more information see [MDL-75025](https://tracker.moodle.org/browse/MDL-75025)
- Final deprecation of base_controller::set_copy(). Please use a restore controller for storing copy information instead.

  For more information see [MDL-75025](https://tracker.moodle.org/browse/MDL-75025)
