# core_tag (subsystem) Upgrade notes

## 5.0

### Changed

- The `core_tag\taglist` class now includes a new property called `displaylink`, which has a default value of `true`. When `displaylink` is set to `true`, the tag name will be displayed as a clickable hyperlink. If `displaylink` is set to `false`, the tag name will be rendered as plain text instead.

  For more information see [MDL-75075](https://tracker.moodle.org/browse/MDL-75075)
