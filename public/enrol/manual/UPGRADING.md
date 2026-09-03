# enrol_manual Upgrade notes

## 5.3dev

### Deprecated

- The manual enrol instance `->enrol_cohort(...)` method is deprecated as it is no longer used/lacked group support

  For more information see [MDL-89439](https://tracker.moodle.org/browse/MDL-89439)

## 5.2

### Removed

- The unused parameter 'roleid' has been removed from the external function `unenrol_users()`

  For more information see [MDL-51152](https://tracker.moodle.org/browse/MDL-51152)
