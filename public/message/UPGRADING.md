# core_message (subsystem) Upgrade notes

## 5.2dev

### Removed

- - The following methods have been removed from `public/message/classes/api.php`:
    - `\core_message\api::search_users_in_course()`
    - `\core_message\api::search_users()`
    - `\core_message\api::get_contacts()`
    - `\core_message\api::get_contacts_with_unread_message_count()`
    - `\core_message\api::get_non_contacts_with_unread_message_count()`
    - `\core_message\api::get_messages()`
    - `\core_message\api::get_most_recent_message()`
    - `\core_message\api::get_profile()`
    - `\core_message\api::delete_conversation()`
    - `\core_message\api::mark_all_read_for_user()`
    - `\core_message\api::can_post_message()`
    - `\core_message\api::is_user_non_contact_blocked()`
    - `\core_message\api::is_user_blocked()`
    - `\core_message\api::get_individual_conversations_between_users()`
    - `\core_message\api::create_conversation_between_users()`
  - The following methods have been removed from `public/message/classes/helper.php`:
    - `\core_message\helper::get_messages()`
    - `\core_message\helper::create_messages()`
    - `\core_message\helper::get_conversations_legacy_formatter()`

  For more information see [MDL-87426](https://tracker.moodle.org/browse/MDL-87426)

## 5.1

### Added

- The web service `core_message_get_member_info` additionally returns `cancreatecontact` which is a boolean value for a user's permission to add a contact.

  For more information see [MDL-72123](https://tracker.moodle.org/browse/MDL-72123)
- The `contexturl` property to `\core\message\message` instances can now contain `\core\url` values in addition to plain strings

  For more information see [MDL-83080](https://tracker.moodle.org/browse/MDL-83080)

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
