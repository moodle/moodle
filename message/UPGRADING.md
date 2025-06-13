# core_message (subsystem) Upgrade notes

## 5.0.1+

### Added

- The web service `core_message_get_member_info` additionally returns `cancreatecontact` which is a boolean value for a user's permission to add a contact.

  For more information see [MDL-72123](https://tracker.moodle.org/browse/MDL-72123)

## 4.5

### Changed

- The `\core_message\helper::togglecontact_link_params()` method now accepts a new optional `$isrequested` parameter to indicate the status of the contact request.

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

### Deprecated

- The `core_message/remove_contact_button` template is deprecated and will be removed in a future release.

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

### Removed

- Final deprecation of the `MESSAGE_DEFAULT_LOGGEDOFF`, and `MESSAGE_DEFAULT_LOGGEDIN` constants.

  For more information see [MDL-73284](https://tracker.moodle.org/browse/MDL-73284)
