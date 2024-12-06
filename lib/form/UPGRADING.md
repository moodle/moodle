# core_form (subsystem) Upgrade notes

## 4.5.1

### Changed

- The `cohort` form element now accepts new `includes` option, which is passed to the corresponding external service to determine which cohorts to return (self, parents, all)

  For more information see [MDL-83641](https://tracker.moodle.org/browse/MDL-83641)

## 4.5

### Added

- The `duration` form field type has been modified to validate that the supplied value is a positive value.
  Previously it could be any numeric value, but every usage of this field in Moodle was expecting a positive value. When a negative value was provided and accepted, subtle bugs could occur.
  Where a negative duration _is_ allowed, the `allownegative` attribute can be set to `true`.

  For more information see [MDL-82687](https://tracker.moodle.org/browse/MDL-82687)
