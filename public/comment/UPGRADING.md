# core_comment (subsystem) Upgrade notes

## 5.1

### Added

- The following classes have been renamed and now support autoloading.
        Existing classes are currently unaffected.

        | Old class name       | New class name                    |
        | ---                  | ---                               |
        | `\comment`           | `\core_comment\manager`           |
        | `\comment_exception` | `\core_comment\comment_exception` |

  For more information see [MDL-86254](https://tracker.moodle.org/browse/MDL-86254)

### Deprecated

- The `public/comment/locallib.php` file and the `comment_manager` class have been deprecated. All related functionality should now be accessed via the `\core_comment\manager` class.

  For more information see [MDL-86254](https://tracker.moodle.org/browse/MDL-86254)
- The `public/comment/lib.php` file is now empty and will be removed in Moodle 6.0. Please, do not include in your code anymore.

  For more information see [MDL-86254](https://tracker.moodle.org/browse/MDL-86254)
