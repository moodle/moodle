# core_backup (subsystem) Upgrade notes

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
