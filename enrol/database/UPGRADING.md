# enrol_database Upgrade notes

## 4.5.16

### Deprecated

- The protected `db_get_sql()` method of `enrol_database_plugin` has been deprecated in favour of the new protected `db_get_query()` method. It takes the external database `ADOConnection` as its first parameter and returns an array of `[$sql, $params]`, so condition values are bound by the driver instead of being escaped and concatenated into the statement. The placeholders are generated with the connection's `Param()` method, so each ADOdb driver (including Oracle `oci8`) gets the syntax it binds natively. The plugin itself no longer calls `db_get_sql()`.

  For more information see [MDL-90353](https://tracker.moodle.org/browse/MDL-90353)
- The `db_addslashes()` method of `enrol_database_plugin` has been deprecated, because external database queries now use parameterised statements instead.

  For more information see [MDL-90353](https://tracker.moodle.org/browse/MDL-90353)

### Removed

- The `dbsybasequoting` admin setting has been removed, since it was only ever used by the now-deprecated `db_addslashes()` method. Any stored value is deleted on upgrade.

  For more information see [MDL-90353](https://tracker.moodle.org/browse/MDL-90353)
