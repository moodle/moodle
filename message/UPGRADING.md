# core_message (subsystem) Upgrade notes

## 4.5dev

### Removed

- Final deprecation MESSAGE_DEFAULT_LOGGEDOFF / MESSAGE_DEFAULT_LOGGEDIN.

  For more information see [MDL-73284](https://tracker.moodle.org/browse/MDL-73284)

### Changed

- The `\core_message\helper::togglecontact_link_params` now accepts a new optional param called `isrequested` to indicate the status of the contact request

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

### Deprecated

- The `core_message/remove_contact_button` template is deprecated and will be removed in the future version

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)
