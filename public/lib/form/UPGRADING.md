# core_form (subsystem) Upgrade notes

## 5.1.8

### Changed

- The `duration` form element type now more strictly enforces units as defined by the caller, to avoid undefined behaviour during submission. An exception will be thrown where `defaultunit` is not part of the element `units` array

  For more information see [MDL-89434](https://tracker.moodle.org/browse/MDL-89434)

## 5.0

### Changed

- The `cohort` form element now accepts new `includes` option, which is passed to the corresponding external service to determine which cohorts to return (self, parents, all)

  For more information see [MDL-83641](https://tracker.moodle.org/browse/MDL-83641)

## 4.5

### Added

- The `duration` form field type has been modified to validate that the supplied value is a positive value.
  Previously it could be any numeric value, but every usage of this field in Moodle was expecting a positive value. When a negative value was provided and accepted, subtle bugs could occur.
  Where a negative duration _is_ allowed, the `allownegative` attribute can be set to `true`.

  For more information see [MDL-82687](https://tracker.moodle.org/browse/MDL-82687)
