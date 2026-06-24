# core_user (subsystem) Upgrade notes

## 5.3dev

### Changed

- The `user_convert_text_to_menu_items()` method now returns a typed array of `\core_user\output\user_action_menu\base` items

  For more information see [MDL-88938](https://tracker.moodle.org/browse/MDL-88938)

### Deprecated

- Consumers of the `\core_user\hook\extend_user_menu` hook class for extending the user menu should now call `add_menu_item()` on the hook instance, which accepts only a parameter of type `\core_user\output\user_action_menu\base`

  The previous `add_navitem` method of the hook class has been deprecated in favour of the above

  For more information see [MDL-88938](https://tracker.moodle.org/browse/MDL-88938)

## 5.2

### Added

- Added new optional parameter `userid` to the `user_remove_user_device` function.

  For more information see [MDL-87795](https://tracker.moodle.org/browse/MDL-87795)

### Deprecated

- The MoodleNet profile field has been migrated from a core user table field to a custom profile field. Any existing moodlenetprofile data will be automatically migrated to a custom profile field during upgrade. The core moodlenetprofile column will be removed from the user table.

  For more information see [MDL-87361](https://tracker.moodle.org/browse/MDL-87361)

### Removed

- - The `\profile_field_base::profile_field_base()` has been removed from `public/user/profile/lib.php`.
  - The `\core_user_renderer::unified_filter()` has been removed from `public/user/renderer.php`.

  For more information see [MDL-87426](https://tracker.moodle.org/browse/MDL-87426)

## 5.1

### Added

- New method `\core_user::get_dummy_fullname(...)` for returning dummy user fullname comprised of configured name fields only

  For more information see [MDL-82132](https://tracker.moodle.org/browse/MDL-82132)

## 5.0

### Removed

- Final removal of the following user preference helpers, please use the `core_user/repository` module instead:

  - `user_preference_allow_ajax_update`
  - `M.util.set_user_preference`
  - `lib/ajax/setuserpref.php`

  For more information see [MDL-79124](https://tracker.moodle.org/browse/MDL-79124)

## 4.5

### Added

- New `\core_user\hook\extend_user_menu` hook added to allow third party plugins to extend the user menu navigation.

  For more information see [MDL-71823](https://tracker.moodle.org/browse/MDL-71823)
- A new hook, `\core_user\hook\extend_default_homepage`, has been added to allow third-party plugins to extend the default homepage options for the site.

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

### Changed

- The visibility of the following methods have been increased to public:
  - `\core_user\form\private_files::check_access_for_dynamic_submission()`
  - `\core_user\form\private_files::get_options()`

  For more information see [MDL-78293](https://tracker.moodle.org/browse/MDL-78293)
- The user profile field `\profile_field_base::display_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name.

  For more information see [MDL-82494](https://tracker.moodle.org/browse/MDL-82494)

### Deprecated

- The `\core_user\table\participants_search::get_total_participants_count()` is no longer used since the total count can be obtained from `\core_user\table\participants_search::get_participants()`.

  For more information see [MDL-78030](https://tracker.moodle.org/browse/MDL-78030)
