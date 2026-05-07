# auth_db Upgrade notes

## 5.1.4

### Deprecated

- The `ext_addslashes()` method has been deprecated from `auth_plugin_db`, because external database queries now using parameterized statements instead. As a result, the `sybasequoting` setting has also been removed, since it was only ever used by that method.

  For more information see [MDL-88138](https://tracker.moodle.org/browse/MDL-88138)
