# core_backup (subsystem) Upgrade notes

## 5.0

### Added

- Added several hooks to the restore process to

   1. Hook to allow extra settings to be defined for the course restore process.
   2. Hook to allow adding extra fields to the copy course form.
   3. Hook used by `copy_helper::process_formdata()` to expand the list of required fields.
   4. Hook used to allow interaction with the copy task, before the actual task execution takes place.

  Other changes include
   1. `base_task::add_setting()` is now public to allow hook callbacks to add settings.
   2. Settings are now added to the data sent to the course_restored event.

  For more information see [MDL-83479](https://tracker.moodle.org/browse/MDL-83479)

### Removed

- Remove all MODE_HUB related code.

  For more information see [MDL-66873](https://tracker.moodle.org/browse/MDL-66873)

## 4.5

### Removed

- The `\core_backup\copy\copy` class has been deprecated and removed. Please use `\copy_helper` instead.

  For more information see [MDL-75022](https://tracker.moodle.org/browse/MDL-75022)
- The following methods in the `\base_controller` class have been removed:

  | Method                          | Replacement                                                     |
  | ---                             | ---                                                             |
  | `\base_controller::set_copy()`  | Use a restore controller for storing copy information instead.  |
  | `\base_controller::get_copy()`  | `\restore_controller::get_copy()`                               |

  For more information see [MDL-75025](https://tracker.moodle.org/browse/MDL-75025)
