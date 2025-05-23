# mod_scorm Upgrade notes

## 5.1dev

### Deprecated

- The method `\mod_scorm\report::generate_master_checkbox()` has been deprecated and should no longer be used. SCORM report plugins calling this method should use `\mod_scorm\report::generate_toggler_checkbox()` instead.

  For more information see [MDL-79756](https://tracker.moodle.org/browse/MDL-79756)
