# enrol_self Upgrade notes

## 5.0

### Deprecated

- Class enrol_self_enrol_form is deprecated, use enrol_self\form\enrol_form instead

  For more information see [MDL-84142](https://tracker.moodle.org/browse/MDL-84142)

### Removed

- Final removal of `enrol_self_plugin::get_welcome_email_contact` method, please use `enrol_plugin::get_welcome_message_contact` instead

  For more information see [MDL-81185](https://tracker.moodle.org/browse/MDL-81185)
