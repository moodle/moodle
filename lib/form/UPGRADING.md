# core_form (subsystem) Upgrade notes

## 4.5beta

### Added

- Previously, the `duration` form field type would allow users to input positive or negative durations. However looking at all the uses, everyone was expecting this input type to only accept `times >= 0` seconds, and almost no-one was bothering to write manual form validation, leading to subtle bugs. So now, by default this field type will validate the input value is not negative. If you need the previous behaviour, there is a new option `allownegative` which you can set to true. (The default is false.)

  For more information see [MDL-82687](https://tracker.moodle.org/browse/MDL-82687)
