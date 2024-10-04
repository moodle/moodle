# editor_tiny Upgrade notes

## 5.0

### Added

- New external function `editor_tiny_get_configuration`.
  TinyMCE subplugins can provide configuration to the new external function by implementing the `plugin_with_configuration_for_external` interface and/or overriding the `is_enabled_for_external` method.

  For more information see [MDL-84353](https://tracker.moodle.org/browse/MDL-84353)

## 4.5

### Changed

- The `helplinktext` language string is no longer required by editor plugins, instead the `pluginname` will be used in the help dialogue.

  For more information see [MDL-81572](https://tracker.moodle.org/browse/MDL-81572)
