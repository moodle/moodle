# core (subsystem) Upgrade notes

## 5.2dev

### Added

- Appending an exclamation mark to template names ignores theme overrides

  For more information see [MDL-77894](https://tracker.moodle.org/browse/MDL-77894)
- Redis connection timeout settings for cachestores and sessions have been split into connection timeout and read timeout to allow for finer control. These settings now also accept floats.

  For more information see [MDL-85336](https://tracker.moodle.org/browse/MDL-85336)
- The namespace for the `\core_shutdown_manager` has been moved to `\core\shutdown_manager`. The legacy namespace will continue to work for the moment.

  For more information see [MDL-87046](https://tracker.moodle.org/browse/MDL-87046)
- Added clean_string() that prevents double escaping in Mustache templates

  For more information see [MDL-87066](https://tracker.moodle.org/browse/MDL-87066)
- The `upgrade_ensure_not_running()` function has been deprecated and replaced
  with:

  - `\core\setup::warn_if_upgrade_is_running()`;
  - `\core\setup::ensure_upgrade_is_not_running()`; and
  - `\core\setup::is_upgrade_running()`.

  For more information see [MDL-87107](https://tracker.moodle.org/browse/MDL-87107)
- The CLI script used to terminate user sessions (kill_all_sessions.php) has been improved to make it safer and more flexible.  A new '--run' parameter has been introduced. Without '--run', the script performs a dry run making no changes. The script now supports targeted session termination using '--for-users' parameter.

  For more information see [MDL-87173](https://tracker.moodle.org/browse/MDL-87173)
- The __construct() method of the confirm_action class now accepts two optional new parameters: `$title` (string) to set the dialogue's heading, and `$dialogtype` (string) to specify the visual style of the action ('delete' for displaying the danger button).

  For more information see [MDL-87281](https://tracker.moodle.org/browse/MDL-87281)
- A new path_module parameter type for routing has been created

  For more information see [MDL-87283](https://tracker.moodle.org/browse/MDL-87283)
- Public require_login() function in moodlelib.php has been change to redirect users to a restricted page when the activity restrictions are visible.

  For more information see [MDL-87283](https://tracker.moodle.org/browse/MDL-87283)
- There is a new Behat `toast_message` named selector to more easily assert the presence of Toast messages on the page

  For more information see [MDL-87443](https://tracker.moodle.org/browse/MDL-87443)
- Added new `options` parameter to loadingicon.js functions for displaying the icon as `overlay`.

  For more information see [MDL-87517](https://tracker.moodle.org/browse/MDL-87517)
- The Checks API now supports multiple results being returned for a single instance of `\core\check\check`.

  To support this a new `get_results(): array;` method has been created which returns an array of `\core\check\result` objects.

  For more information see [MDL-87648](https://tracker.moodle.org/browse/MDL-87648)

### Changed

- The `arg_separator.output` has been changed from a default of `amp;` to a default of `&` in line with PHP defaults.

  If you were previously relying on the legacy default when using
  `http_build_query()` then you should _actively_ specify the value as the third
  parameter, for example:

  ```
  http_build_query($formdata, '', '&amp;');
  ```

  For more information see [MDL-71368](https://tracker.moodle.org/browse/MDL-71368)
- The `core/drag_handle` template has been modified to use a native HTML button for a more accessible experience and a consistent look with other buttons on the page.

  For more information see [MDL-86846](https://tracker.moodle.org/browse/MDL-86846)
- The Hook Manager now uses localcache instead of caching via MUC.

  For more information see [MDL-87107](https://tracker.moodle.org/browse/MDL-87107)
- `\core\output\core_renderer::confirm()`'s `$displayoptions` parameter now also accepts a `headinglevel` option that developers can use to specify the heading level of the confirmation's heading. If not specified, the confirmation heading will be rendered in an `h4` tag.

  For more information see [MDL-87694](https://tracker.moodle.org/browse/MDL-87694)

### Removed

- Removed `qtype_random` from core. `core\component::is_valid_plugin_name` has an additional check to ensure no-one can create a new plugin called qtype_random, as this would conflict with the support for restoring old backups.

  For more information see [MDL-73602](https://tracker.moodle.org/browse/MDL-73602)
- The following AMD modules have been removed following the final deprecation process. It is no longer possible or necessary to manually register modal types. See MDL-78324 for further details.

  - `core/modal_registry`
  - `core/modal_factory`

  For more information see [MDL-79182](https://tracker.moodle.org/browse/MDL-79182)
- Legacy constructors have been removed. These relate to PHP 4 and earlier.

  For more information see [MDL-82284](https://tracker.moodle.org/browse/MDL-82284)
- Removed $CFG->wwwrootendsinpublic flag to force users to configure their server accordingly.

  For more information see [MDL-87072](https://tracker.moodle.org/browse/MDL-87072)
- The following functions have been removed from `public/lib/deprecatedlib.php` as part of the depreciation process: - `print_course_request_buttons()` - `cron_run()` - `cron_run_scheduled_tasks()` - `cron_run_adhoc_tasks()` - `cron_run_inner_scheduled_task()` - `cron_run_inner_adhoc_task()` - `cron_set_process_title()` - `cron_trace_time_and_memory()` - `cron_prepare_core_renderer()` - `cron_setup_user()` - `badges_get_oauth2_service_options()` - `theme_is_device_locked()` - `theme_get_locked_theme_for_device()` - `random_bytes_emulate()` - `plagiarism_get_file_results()` - `plagiarism_update_status()` - `calendar_top_controls()` - `calendar_get_link_previous()` - `calendar_get_link_next()`

  For more information see [MDL-87423](https://tracker.moodle.org/browse/MDL-87423)
- The `MOD_PURPOSE_INTERFACE` constant has been removed from `public/lib/moodlelib.php`.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)
- The `public/lib/classes/navigation/flat_navigation_node.php` file has been removed.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)
- The `public/question/qengine.js` file has been removed.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)
- - The `public/lib/amd/src/addblockmodal.js` file has been removed. - The `request()` has been removed from `public/lib/amd/src/pending.js`. - The `triggerSelector()` has been removed from `public/lib/amd/src/comboboxsearch/search_combobox.js`. - The `public/lib/classes/navigation/flat_navigation.php` file has been removed. - The following methods have been removed from `public/lib/classes/output/action_menu.php`:
    - `\core\output\action_menu::set_alignment()`
    - `\core\output\action_menu::set_constraint()`
  - The following methods have been removed from `public/lib/classes/output/core_renderer.php`:
    - `\core\output\core_renderer::activity_information()`
    - `\core\output\core_renderer::htmllize_file_tree()`
  - The `\core\output\action_menu\link::instance` property has been removed from `public/lib/classes/output/action_menu/link.php`. - The `\core\output\renderer_base::should_display_main_logo()` has been removed from `public/lib/classes/output/renderer_base.php`. - The `\core\task\manager::ensure_adhoc_task_qos()` has been removed from `public/lib/classes/task/manager.php`. - The following methods have been removed from `public/lib/classes/task/task_base.php`:
    - `\core\task\task_base::set_blocking()`
    - `\core\task\task_base::is_blocking()`
  - The `\core\moodlenet\activity_sender::share_activity()` has been removed from `public/lib/classes/moodlenet/activity_sender.php`. - The `\core\encryption::is_sodium_installed()` has been removed from `public/lib/classes/encryption.php`. - The `\core\hook\manager::is_deprecated_plugin_callback()` has been removed from `public/lib/classes/hook/manager.php`. - The `\core\report_helper::save_selected_report()` has been removed from `public/lib/classes/report_helper.php`. - The `\core_text::reset_caches()` has been removed from `public/lib/classes/text.php`. - The following methods have been removed from `public/lib/classes/useragent.php`:
    - `\core_useragent::get_device_type_list()`
    - `\core_useragent::get_device_type_theme()`
    - `\core_useragent::get_device_type_cfg_var_name()`
  - The `M.util.set_user_preference()` has been removed from `public/lib/javascript-static.js`.

  For more information see [MDL-87425](https://tracker.moodle.org/browse/MDL-87425)
- - The `\core\output\action_menu::do_not_enhance()` has been removed from `public/lib/classes/output/action_menu.php`. - The following functions have been removed from `public/lib/javascript-static.js`:
    - `M.util.init_toggle_class_on_click()`
    - `M.util.focus_login_form()`
    - `M.util.focus_login_error()`
    - `checkall()`
    - `checknone()`
    - `select_all_in_element_with_id()`
    - `select_all_in()`
    - `deselect_all_in()`
    - `confirm_if()`
    - `findParentNode()`
    - `filterByParent()`
    - `fix_column_widths()`
    - `fix_column_width()`
    - `stripHTML()`
    - `M.form.init_smartselect()`

  For more information see [MDL-87426](https://tracker.moodle.org/browse/MDL-87426)
- The `\admin_setting_configselect::output_select_html()` has been removed from `public/lib/adminlib.php`.

  For more information see [MDL-87426](https://tracker.moodle.org/browse/MDL-87426)
- The `relateduserid` key inside the `other` array has been removed.

  For more information see [MDL-87427](https://tracker.moodle.org/browse/MDL-87427)
- - The following files have been removed:
    - `public/lib/classes/event/content_viewed.php`
    - `public/lib/classes/exception/webservice_parameter_exception.php`
  - The `lightbox` attribute has been removed from `public/lib/yui/src/notification/js/dialogue.js`. - The following functions have been removed from `public/lib/deprecatedlib.php`:
    - `get_context_instance()`
    - `can_use_rotated_text()`
    - `get_system_context()`
    - `print_arrow()`

  For more information see [MDL-87427](https://tracker.moodle.org/browse/MDL-87427)

### Fixed

- `restore_qtype_plugin::unset_excluded_fields` now returns the modified questiondata structure,
  in order to support structures that contain arrays.
  If your qtype plugin overrides `restore_qtype_plugin::remove_excluded_question_data` without
  calling the parent method, you may need to modify your overridden method to use the returned
  value.

  For more information see [MDL-85975](https://tracker.moodle.org/browse/MDL-85975)
- When responding to pcntl signals, call existing signal handlers.

  For more information see [MDL-87079](https://tracker.moodle.org/browse/MDL-87079)

## 5.1

### Added

- The following classes have been renamed and now support autoloading.
  Existing classes are currently unaffected.

  | Old class name                  | New class name                                  |
  | ---                             | ---                                             |
  | `\breadcrumb_navigation_node`   | `\core\navigation\breadcrumb_navigation_node`   |
  | `\flat_navigation_node`         | `\core\navigation\flat_navigation_node`         |
  | `\flat_navigation`              | `\core\navigation\flat_navigation_node`         |
  | `\global_navigation_for_ajax`   | `\core\navigation\global_navigation_for_ajax`   |
  | `\global_navigation`            | `\core\navigation\global_navigation`            |
  | `\navbar`                       | `\core\navigation\navbar`                       |
  | `\navigation_cache`             | `\core\navigation\navigation_cache`             |
  | `\navigation_json`              | `\core\navigation\navigation_json`              |
  | `\navigation_node_collection`   | `\core\navigation\navigation_node_collection`   |
  | `\navigation_node`              | `\core\navigation\navigation_node`              |
  | `\settings_navigation_for_ajax` | `\core\navigation\settings_navigation_for_ajax` |
  | `\settings_navigation`          | `\core\navigation\settings_navigation`          |

  For more information see [MDL-82159](https://tracker.moodle.org/browse/MDL-82159)
- - Added is_site_registered_in_hub method in lib/classes/hub/api.php to
    check if the site is registered or not.
  - Added get_secret method in lib/classes/hub/registration.php to get site's secret.

  For more information see [MDL-83448](https://tracker.moodle.org/browse/MDL-83448)
- Added a new optional param to adhoc_task_failed and scheduled_task_failed to allow skipping log finalisation when called from a separate task.

  For more information see [MDL-84442](https://tracker.moodle.org/browse/MDL-84442)
- Add a new method has_valid_group in \core\report_helper that will return true or false depending if the user has a valid group. This is mainly false in case the user is not in any group in SEPARATEGROUPS. Used in report_log and report_loglive

  For more information see [MDL-84464](https://tracker.moodle.org/browse/MDL-84464)
- There is a new `core/page_title` Javascript module for manipulating the current page title

  For more information see [MDL-84804](https://tracker.moodle.org/browse/MDL-84804)
- Added support for configurable `aspectRatio` in charts rendered using Chart.js. This enables developers to control chart sizing more precisely via the `chart_base` API and the frontend renderer.

  For more information see [MDL-85158](https://tracker.moodle.org/browse/MDL-85158)
- Output classes can now implement the core\output\externable interface. This allows these classes to define methods for exporting their data in a format suitable for use in web services.

  For more information see [MDL-85509](https://tracker.moodle.org/browse/MDL-85509)
- The following functions have been replaced with class methods.

   | Old function name               | New method name                       |
   | ---                             | ---                                   |
   | `\ajax_capture_output()`        | `\core\ajax::capture_output()`        |
   | `\ajax_check_captured_output()` | `\core\ajax::check_captured_output()` |
  It is no longer necessary to include `lib/ajax/ajaxlib.php` in any code.

  For more information see [MDL-86168](https://tracker.moodle.org/browse/MDL-86168)
- The Behat `::execute()` method now accepts an array-style callable in addition to the string `classname::method` format.

  The following formats are now accepted:

  ```php
  // String format:
  $this->execute('behat_general::i_click_on', [...]);

  // Array format:
  $this->execute([behat_general::class,' i_click_on'], [...]);
  ```

  For more information see [MDL-86231](https://tracker.moodle.org/browse/MDL-86231)
- The following classes have been moved into namespaces and now support autoloading:

  | Old class name          | New class name                         |
  | ---                     | ---                                    |
  | `\core_xml_parser`      | `\core\xml_parser`                     |
  | `\xml_format_exception` | `\core\exception\xml_format_exception` |

  For more information see [MDL-86256](https://tracker.moodle.org/browse/MDL-86256)
- The `\externallib_advanced_testcase` has been replaced by `\core_external\tests\externallib_testcase` and is now autoloadable.

  For more information see [MDL-86283](https://tracker.moodle.org/browse/MDL-86283)

### Changed

- Changes were implemented to make `checkbox-toggleall` output component more inclusive:

  * Replace the references to `master` checkboxes with `toggler`.
  * Replace the references to `slave` checkboxes with `target`.

  For more information see [MDL-79756](https://tracker.moodle.org/browse/MDL-79756)
- The `\core\attribute\deprecated` attribute constructor `$replacement` parameter now defaults to null, and can be omitted

  For more information see [MDL-84531](https://tracker.moodle.org/browse/MDL-84531)
- The `core_plugin_manager::plugintype_name[_plural]` methods now require language strings for plugin types always be defined via `type_<type>` and `type_<type>_plural` language strings

  For more information see [MDL-84948](https://tracker.moodle.org/browse/MDL-84948)
- Added a new `\core\deprecation::emit_deprecation()` method which should be used in places where a deprecation is known to occur. This method will throw debugging if no deprecation notice was found, for example:
  ```php
  public function deprecated_method(): void {
      \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
  }
  ```

  For more information see [MDL-85897](https://tracker.moodle.org/browse/MDL-85897)
- The `\core\output\local\dropdown\dialog` class constructor now accepts a `$definition['autoclose']` parameter to define autoclose behaviour of the element

  For more information see [MDL-86015](https://tracker.moodle.org/browse/MDL-86015)
- The default PHPUnit configuration now enables the following properties, ensuring PHP warnings will cause test failures (restoring pre-PHPUnit version 10 behaviour):

  * `failOnDeprecation`
  * `failOnWarning`

  For more information see [MDL-86311](https://tracker.moodle.org/browse/MDL-86311)

### Deprecated

- The following function has been deprecated and should no longer be used: `file_encode_url`. Please consider using the `core\url` factory methods instead.

  For more information see [MDL-31071](https://tracker.moodle.org/browse/MDL-31071)
- The following `core/checkbox-toggleall` templates have been deprecated:

  - `core/checkbox-toggleall-master-button` - This is replaced with `core/checkbox-toggleall-toggler-button`
  - `core/checkbox-toggleall-master` - This is replaced with `core/checkbox-toggleall-toggler`
  - `core/checkbox-toggleall-slave` - This is replaced with `core/checkbox-toggleall-target`

  The following items in the `core/checkbox-toggleall` JS module have been deprecated:

  - Method:
      - `updateSlavesFromMasterState()` - This is replaced with `updateTargetsFromTogglerState()`.

  - Usage of the following selectors:
      - `data-toggle=master` - This is replaced with `data-toggle=toggler`.
      - `data-toggle=slave` - This is replaced with `data-toggle=target`.

  The usage of these selectors will continue to be supported until they are removed by final deprecation. In the meantime, a deprecation warning in the JavaScript console will be shown if usage of these selectors is detected.

  For more information see [MDL-79756](https://tracker.moodle.org/browse/MDL-79756)
- The following global constants have been deprecated in favour of class
  constants:

   | Old constant                       | New constant                                              |
   | ---                                | ---                                                       |
   | `NAVIGATION_CACHE_NAME`            | `\core\navigation\navigation_node::CACHE_NAME`            |
   | `NAVIGATION_SITE_ADMIN_CACHE_NAME` | `\core\navigation\navigation_node::SITE_ADMIN_CACHE_NAME` |

  For more information see [MDL-82159](https://tracker.moodle.org/browse/MDL-82159)
- The `user_preference_allow_ajax_update()` has been removed. It was deprecated without replacement in Moodle 4.3.

  For more information see [MDL-86168](https://tracker.moodle.org/browse/MDL-86168)
- The `xmlize()` method from `lib/xmlize.php` has been deprecated, please instead use the `\core\xml_parser` class

  For more information see [MDL-86256](https://tracker.moodle.org/browse/MDL-86256)
- In toggle.mustache `dataattributes` parameter is deprecated. Use `extraattributes` instead

  For more information see [MDL-86990](https://tracker.moodle.org/browse/MDL-86990)

### Removed

- Final deprecation of device related theme methods. The following two methods have been removed from the core_useragent class:
    - core_useragent::get_device_type_theme
    - core_useragent::get_device_type_cfg_var_name

  For more information see [MDL-78375](https://tracker.moodle.org/browse/MDL-78375)
- Final deprecation of removing the legacy theme settings. The following method has been removed:
    - core_useragent::get_device_type_list()
  The following classes have been removed:
    - core_adminpresets\local\setting\adminpresets_admin_setting_devicedetectregex
    - admin_setting_devicedetectregex

  For more information see [MDL-79052](https://tracker.moodle.org/browse/MDL-79052)
- Removed `core\hook\manager::is_deprecated_plugin_callback()` in favor of `core\hook\manager::get_hooks_deprecating_plugin_callback()`.

  For more information see [MDL-80327](https://tracker.moodle.org/browse/MDL-80327)

## 5.0

### Added

- The `core/sortable_list` Javascript module now emits native events, removing the jQuery dependency from calling code that wants to listen for the events. Backwards compatibility with existing code using jQuery is preserved

  For more information see [MDL-72293](https://tracker.moodle.org/browse/MDL-72293)
- `\core\output\activity_header` now uses the `is_title_allowed()` method when setting the title in the constructor.

  This method has been improved to give priority to the 'notitle' option in the theme config for the current page layout, over the top-level option in the theme.

  For example, the Boost theme sets `$THEME->activityheaderconfig['notitle'] = true;` by default, but in its `secure` pagelayout, it has `'notitle' = false`.
  This prevents display of the title in all layouts except `secure`.

  For more information see [MDL-75610](https://tracker.moodle.org/browse/MDL-75610)
- Behat now supports email content verification using Mailpit.
  You can check the contents of an email using the step `Then the email to "user@example.com" with subject containing "subject" should contain "content".`
  To use this feature:
  1. Ensure that Mailpit is running
  2. Define the following constants in your `config.php`:
      - `TEST_EMAILCATCHER_MAIL_SERVER` - The Mailpit server address (e.g. `0.0.0.0:1025`)
      - `TEST_EMAILCATCHER_API_SERVER` - The Mailpit API server (qe.g. `http://localhost:8025`)

  3. Ensure that the email catcher is set up using the step `Given an email catcher server is configured`.

  For more information see [MDL-75971](https://tracker.moodle.org/browse/MDL-75971)
- A new core\ip_utils::normalize_internet_address() method is created to sanitize an IP address, a range of IP addresses, a domain name or a wildcard domain matching pattern.

  Moodle previously allowed entries such as 192.168. or .moodle.org for certain variables (eg: $CFG->proxybypass). Since MDL-74289, these formats are no longer allowed. This method converts this informations into an authorized format. For example, 192.168. becomes 192.168.0.0/16 and .moodle.org becomes *.moodle.org.

  Also a new core\ip_utils::normalize_internet_address_list() method is created. Based on core\ip_utils::normalize_internet_address(), this method normalizes a string containing a series of Internet addresses.

  For more information see [MDL-79121](https://tracker.moodle.org/browse/MDL-79121)
- The stored progress API has been updated. The `\core\output\stored_progress_bar` class has
  now has a `store_pending()` method, which will create a record for the stored process, but
  without a start time or progress percentage.
  `\core\task\stored_progress_task_trait` has been updated with a new `initialise_stored_progress()` method,
  which will call `store_pending()` for the task's progress bar. This allows the progress bar to be displayed
  in a "pending" state, to show that a process has been queued but not started.

  For more information see [MDL-81714](https://tracker.moodle.org/browse/MDL-81714)
- A new `\core\output\task_indicator` component has been added to display a progress bar and message
  for a background task using `\core\task\stored_progress_task_trait`. See the "Task indicator"
  page in the component library for usage details.

  For more information see [MDL-81714](https://tracker.moodle.org/browse/MDL-81714)
- The deprecated implementation in course/view.php, which uses the extern_server_course function to handle routing between internal and external courses, can be improved by utilizing the Hook API. This enhancement is essential for a project involving multiple universities, as the Hook API provides a more generalized and flexible approach to route users to external courses from within other plugins.

  For more information see [MDL-83473](https://tracker.moodle.org/browse/MDL-83473)
- Add after_role_switched hook that is triggered when we switch role to a new role in a course.

  For more information see [MDL-83542](https://tracker.moodle.org/browse/MDL-83542)
- New generic collapsable section output added. Use core\output\local\collapsable_section or include the core/local/collapsable_section template to use it. See the full documentation in the component library.

  For more information see [MDL-83869](https://tracker.moodle.org/browse/MDL-83869)
- A new method get_instance_record has been added to cm_info object so core can get the activity table record without using the $DB object every time. Also, the method caches de result so getting more than once per execution is much faster.

  For more information see [MDL-83892](https://tracker.moodle.org/browse/MDL-83892)
- Now lib/templates/select_menu.mustache has a new integer headinglevel context value to specify the heading level to keep the header accessibility when used as a tertiary navigation.

  For more information see [MDL-84208](https://tracker.moodle.org/browse/MDL-84208)
- The public method `get_slashargument` has been added to the `url` class.

  For more information see [MDL-84351](https://tracker.moodle.org/browse/MDL-84351)
- The new PHP enum core\output\local\properties\iconsize can be used to limit the amount of icons sizes an output component can use. The enum has the same values available in the theme_boost scss.

  For more information see [MDL-84555](https://tracker.moodle.org/browse/MDL-84555)
- A new method, `core_text::trim_ctrl_chars()`, has been introduced to clean control characters from text. This ensures cleaner input handling and prevents issues caused by invisible or non-printable characters

  For more information see [MDL-84907](https://tracker.moodle.org/browse/MDL-84907)

### Changed

- The {user_preferences}.value database field is now TEXT instead of CHAR. This means that any queries with a condition on this field in a WHERE or JOIN statement will need updating to use `$DB->sql_compare_text()`. See the `$newusers` query in `\core\task\send_new_users_password_task::execute` for an example.

  For more information see [MDL-46739](https://tracker.moodle.org/browse/MDL-46739)
- The `core_renderer::tag_list` function now has a new parameter named `displaylink`. When `displaylink` is set to `true`, the tag name will be displayed as a clickable hyperlink. Otherwise, it will be rendered as plain text.

  For more information see [MDL-75075](https://tracker.moodle.org/browse/MDL-75075)
- All uses of the following PHPUnit methods have been removed as these methods are
  deprecated upstream without direct replacement:

  - `withConsecutive`
  - `willReturnConsecutive`
  - `onConsecutive`

  Any plugin using these methods must update their uses.

  For more information see [MDL-81308](https://tracker.moodle.org/browse/MDL-81308)
- PHPSpreadSheet has been updated to version 4.0.0.

  All library usage should be via the Moodle wrapper and no change should be required.

  For more information see [MDL-81664](https://tracker.moodle.org/browse/MDL-81664)
- The Moodle subplugins.json format has been updated to accept a new `subplugintypes` object.

  This should have the same format as the current `plugintypes` format, except that the paths should be relative to the _plugin_ root instead of the Moodle document root.

  Both options can co-exist, but if both are present they must be kept in-sync.

  ```json
  {
      "subplugintypes": {
          "tiny": "plugins"
      },
      "plugintypes": {
          "tiny": "lib/editor/tiny/plugins"
      }
  }
  ```

  For more information see [MDL-83705](https://tracker.moodle.org/browse/MDL-83705)
- The behat/gherkin has been updated to 4.11.0 which introduces a breaking change where backslashes in feature files need to be escaped.

  For more information see [MDL-83848](https://tracker.moodle.org/browse/MDL-83848)
- The following test classes have been moved into autoloadable locations:

  | Old location | New classname |
  | --- | --- |
  | `\core\tests\route_testcase` | `\core\tests\router\route_testcase` |
  | `\core\router\mocking_route_loader` | `\core\tests\router\mocking_route_loader` |

  For more information see [MDL-83968](https://tracker.moodle.org/browse/MDL-83968)
- Analytics is now disabled by default on new installs.

  For more information see [MDL-84107](https://tracker.moodle.org/browse/MDL-84107)

### Deprecated

- The methods `want_read_slave` and `perf_get_reads_slave` in `lib/dml/moodle_database.php` have been deprecated in favour of renamed versions that substitute `slave` for `replica`.

  For more information see [MDL-71257](https://tracker.moodle.org/browse/MDL-71257)
- The trait `moodle_read_slave_trait` has been deprecated in favour of a functionally identical version called `moodle_read_replica_trait`. The renamed trait substitutes the terminology of `slave` with `replica`, and `master` with `primary`.

  For more information see [MDL-71257](https://tracker.moodle.org/browse/MDL-71257)
- question_make_default_categories()

  No longer creates a default category in either CONTEXT_SYSTEM, CONTEXT_COURSE, or CONTEXT_COURSECAT.
  Superceded by question_get_default_category which can optionally create one if it does not exist.

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- question_delete_course()

  No replacement. Course contexts no longer hold question categories.

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- question_delete_course_category()

  Course category contexts no longer hold question categories.

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- The 'core_renderer::sr_text()' function has been deprecated, use 'core_renderer::visually_hidden_text()' instead.

  For more information see [MDL-81825](https://tracker.moodle.org/browse/MDL-81825)
- The function imagecopybicubic() is now deprecated. The GD lib is a strict requirement, so use imagecopyresampled() instead.

  For more information see [MDL-84449](https://tracker.moodle.org/browse/MDL-84449)

### Removed

- moodle_process_email() has been deprecated with the removal of the unused and non-functioning admin/process_email.php.

  For more information see [MDL-61232](https://tracker.moodle.org/browse/MDL-61232)
- The method `site_registration_form::add_select_with_email()` has been finally deprecated and will now throw an exception if called.

  For more information see [MDL-71472](https://tracker.moodle.org/browse/MDL-71472)
- Remove support deprecated boolean 'primary' parameter in \core\output\single_button. The 4th parameter is now a string and not a boolean (the use was to set it to true to have a primary button)

  For more information see [MDL-75875](https://tracker.moodle.org/browse/MDL-75875)
- Final removal of the following constants/methods from the `\core\encyption` class, removing support for OpenSSL fallback:

  - `METHOD_OPENSSL`
  - `OPENSSL_CIPHER`
  - `is_sodium_installed`

  For more information see [MDL-78869](https://tracker.moodle.org/browse/MDL-78869)
- Final deprecation of core_renderer\activity_information()

  For more information see [MDL-78926](https://tracker.moodle.org/browse/MDL-78926)
- Final removal of `share_activity()` in `core\moodlenet\activity_sender`, please use `share_resource()` instead.

  For more information see [MDL-79086](https://tracker.moodle.org/browse/MDL-79086)
- Final deprecation of methods `task_base::is_blocking` and `task_base::set_blocking`.

  For more information see [MDL-81509](https://tracker.moodle.org/browse/MDL-81509)
- - Remove php-enum library. - It was a dependency of zipstream, but is no longer required as this
    functionality has been replaced by native PHP functionality.

  For more information see [MDL-82825](https://tracker.moodle.org/browse/MDL-82825)
- Oracle support has been removed in LMS

  For more information see [MDL-83172](https://tracker.moodle.org/browse/MDL-83172)
- The Atto HTML editor has been removed from core, along with all standard
  subplugins.

  The editor is available for continued use in the Plugins Database.

  For more information see [MDL-83282](https://tracker.moodle.org/browse/MDL-83282)
- Support for `subplugins.php` files has been removed. All subplugin metadata must be created in a `subplugins.json` file.

  For more information see [MDL-83703](https://tracker.moodle.org/browse/MDL-83703)
- set_alignment(), set_constraint() and do_not_enhance() functions have been fully removed from action_menu class.

  For more information see [MDL-83765](https://tracker.moodle.org/browse/MDL-83765)
- The `core_output_load_fontawesome_icon_map` web service has been fully removed and replaced by `core_output_load_fontawesome_icon_system_map`

  For more information see [MDL-84036](https://tracker.moodle.org/browse/MDL-84036)
- Final deprecation and removal of \core\event\course_module_instances_list_viewed

  For more information see [MDL-84593](https://tracker.moodle.org/browse/MDL-84593)

### Fixed

- url class now correctly supports multi level query parameter parsing and output.

  This was previously supported in some methods such as get_query_string, but not others. This has been fixed to be properly supported.

  For example https://example.moodle.net?test[2]=a&test[0]=b will be parsed as ['test' => [2 => 'a', 0 => 'b']]

  All parameter values that are not arrays are casted to strings.

  For more information see [MDL-77293](https://tracker.moodle.org/browse/MDL-77293)

## 4.5

### Added

- A new method, `\core_user::get_name_placeholders()`, has been added to return an array of user name fields.

  For more information see [MDL-64148](https://tracker.moodle.org/browse/MDL-64148)
- The following classes have been renamed and now support autoloading.
  Existing classes are currently unaffected.

  | Old class name     | New class name     |
  | ---                | ---                |
  | `\core_component`  | `\core\component`  |

  For more information see [MDL-66903](https://tracker.moodle.org/browse/MDL-66903)
- Added the ability for unit tests to autoload classes in the `\[component]\tests\`
  namespace from the `[path/to/component]/tests/classes` directory.

  For more information see [MDL-66903](https://tracker.moodle.org/browse/MDL-66903)
- Added a helper to load fixtures from a components `tests/fixtures/` folder:

  ```php
  advanced_testcase::load_fixture(string $component, string $fixture): void;
  ```

  For more information see [MDL-66903](https://tracker.moodle.org/browse/MDL-66903)
- Redis session cache has been improved to make a single call where two were used before.

  For more information see [MDL-69684](https://tracker.moodle.org/browse/MDL-69684)
- Added stored progress bars

  For more information see [MDL-70854](https://tracker.moodle.org/browse/MDL-70854)
- Two new functions have been introduced in the `\moodle_database` class:
  - `\moodle_database::get_counted_records_sql()`
  - `\moodle_database::get_counted_recordset_sql()`

  These methods are compatible with all databases.

  They will check the current running database engine and apply the `COUNT` window function if it is supported,
  otherwise, they will use the usual `COUNT` function.

  The `COUNT` window function optimization is applied to the following databases:
  - PostgreSQL
  - MariaDB
  - Oracle

  Note: MySQL and SQL Server do not use this optimization due to insignificant performance differences before and
  after the improvement.

  For more information see [MDL-78030](https://tracker.moodle.org/browse/MDL-78030)
- The `after_config()` callback has been converted to a hook, `\core\hook\after_config`.

  For more information see [MDL-79011](https://tracker.moodle.org/browse/MDL-79011)
- The `\core\output\select_menu` widget now supports rendering dividers between menu options. Empty elements (`null` or empty strings) within the array of options are considered and rendered as dividers in the dropdown menu.

  For more information see [MDL-80747](https://tracker.moodle.org/browse/MDL-80747)
- The `\core\output\select_menu` widget now supports a new feature: inline labels. You can render the label inside the combobox widget by passing `true` to the `$inlinelabel` parameter when calling the `->set_label()` method.

  For more information see [MDL-80747](https://tracker.moodle.org/browse/MDL-80747)
- A new hook called `\core\hook\output\after_http_headers` has been created. This hook allow plugins to modify the content after headers are sent.

  For more information see [MDL-80890](https://tracker.moodle.org/browse/MDL-80890)
- The following classes have been renamed.
  Existing classes are currently unaffected.

  | Old class name  | New class name  |
  | ---             | ---             |
  | `\core_user`    | `\core\user`    |

  For more information see [MDL-81031](https://tracker.moodle.org/browse/MDL-81031)
- New DML constant `SQL_INT_MAX` to define the size of a large integer with cross database platform support.

  For more information see [MDL-81282](https://tracker.moodle.org/browse/MDL-81282)
- Added a new `exception` L2 Namespace to APIs.

  For more information see [MDL-81903](https://tracker.moodle.org/browse/MDL-81903)
- Added a mechanism to support autoloading of legacy class files.
  This will help to reduce the number of `require_once` calls in the codebase, and move away from the use of monolithic libraries.

  For more information see [MDL-81919](https://tracker.moodle.org/browse/MDL-81919)
- The following exceptions are now also available in the `\core\exception` namespace:

    - `\coding_exception`
    - `\file_serving_exception`
    - `\invalid_dataroot_permissions`
    - `\invalid_parameter_exception`
    - `\invalid_response_exception`
    - `\invalid_state_exception`
    - `\moodle_exception`
    - `\require_login_exception`
    - `\require_login_session_timeout_exception`
    - `\required_capability_exception`
    - `\webservice_parameter_exception`

  For more information see [MDL-81919](https://tracker.moodle.org/browse/MDL-81919)
- The following classes are now also available in the `\core\` namespace and support autoloading:

  | Old class name       | New class name            |
  | ---                  | ---                       |
  | `\emoticon_manager`  | `\core\emoticon_manager`  |
  | `\lang_string`       | `\core\lang_string`       |

  For more information see [MDL-81920](https://tracker.moodle.org/browse/MDL-81920)
- The following classes have been renamed and now support autoloading.
  Existing classes are currently unaffected.

  | Old class name               | New class name                                          |
  | ---                          | ---                                                     |
  | `\moodle_url`                | `\core\url`                                             |
  | `\progress_trace`            | `\core\output\progress_trace`                           |
  | `\combined_progress_trace`   | `\core\output\progress_trace\combined_progress_trace`   |
  | `\error_log_progress_trace`  | `\core\output\progress_trace\error_log_progress_trace`  |
  | `\html_list_progress_trace`  | `\core\output\progress_trace\html_list_progress_trace`  |
  | `\html_progress_trace`       | `\core\output\progress_trace\html_progress_trace`       |
  | `\null_progress_trace`       | `\core\output\progress_trace\null_progress_trace`       |
  | `\progress_trace_buffer`     | `\core\output\progress_trace\progress_trace_buffer`     |
  | `\text_progress_trace`       | `\core\output\progress_trace\text_progress_trace`       |

  For more information see [MDL-81960](https://tracker.moodle.org/browse/MDL-81960)
- The following classes are now also available in the following new
  locations. They will continue to work in their old locations:

  | Old classname                              | New classname                                                      |
  | ---                                        | ---                                                                |
  | `\action_link`                             | `\core\output\action_link`                                         |
  | `\action_menu_filler`                      | `\core\output\action_menu\filler`                                  |
  | `\action_menu_link_primary`                | `\core\output\action_menu\link_primary`                            |
  | `\action_menu_link_secondary`              | `\core\output\action_menu\link_secondary`                          |
  | `\action_menu_link`                        | `\core\output\action_menu\link`                                    |
  | `\action_menu`                             | `\core\output\action_menu`                                         |
  | `\block_contents`                          | `\core_block\output\block_contents`                                |
  | `\block_move_target`                       | `\core_block\output\block_move_target`                             |
  | `\component_action`                        | `\core\output\actions\component_action`                            |
  | `\confirm_action`                          | `\core\output\actions\confirm_action`                              |
  | `\context_header`                          | `\core\output\context_header`                                      |
  | `\core\output\local\action_menu\subpanel`  | `\core\output\action_menu\subpanel`                                |
  | `\core_renderer_ajax`                      | `\core\output\core_renderer_ajax`                                  |
  | `\core_renderer_cli`                       | `\core\output\core_renderer_cli`                                   |
  | `\core_renderer_maintenance`               | `\core\output\core_renderer_maintenance`                           |
  | `\core_renderer`                           | `\core\output\core_renderer`                                       |
  | `\custom_menu_item`                        | `\core\output\custom_menu_item`                                    |
  | `\custom_menu`                             | `\core\output\custom_menu`                                         |
  | `\file_picker`                             | `\core\output\file_picker`                                         |
  | `\flexible_table`                          | `\core_table\flexible_table`                                       |
  | `\fragment_requirements_manager`           | `\core\output\requirements\fragment_requirements_manager`          |
  | `\help_icon`                               | `\core\output\help_icon`                                           |
  | `\html_table_cell`                         | `\core_table\output\html_table_cell`                               |
  | `\html_table_row`                          | `\core_table\output\html_table_row`                                |
  | `\html_table`                              | `\core_table\output\html_table`                                    |
  | `\html_writer`                             | `\core\output\html_writer`                                         |
  | `\image_icon`                              | `\core\output\image_icon`                                          |
  | `\initials_bar`                            | `\core\output\initials_bar`                                        |
  | `\js_writer`                               | `\core\output\js_writer`                                           |
  | `\page_requirements_manager`               | `\core\output\requirements\page_requirements_manager`              |
  | `\paging_bar`                              | `\core\output\paging_bar`                                          |
  | `\pix_emoticon`                            | `\core\output\pix_emoticon`                                        |
  | `\pix_icon_font`                           | `\core\output\pix_icon_font`                                       |
  | `\pix_icon_fontawesome`                    | `\core\output\pix_icon_fontawesome`                                |
  | `\pix_icon`                                | `\core\output\pix_icon`                                            |
  | `\plugin_renderer_base`                    | `\core\output\plugin_renderer_base`                                |
  | `\popup_action`                            | `\core\output\actions\popup_action`                                |
  | `\preferences_group`                       | `\core\output\preferences_group`                                   |
  | `\preferences_groups`                      | `\core\output\preferences_groups`                                  |
  | `\progress_bar`                            | `\core\output\progress_bar`                                        |
  | `\renderable`                              | `\core\output\renderable`                                          |
  | `\renderer_base`                           | `\core\output\renderer_base`                                       |
  | `\renderer_factory_base`                   | `\core\output\renderer_factory\renderer_factory_base`              |
  | `\renderer_factory`                        | `\core\output\renderer_factory\renderer_factory_interface`         |
  | `\single_button`                           | `\core\output\single_button`                                       |
  | `\single_select`                           | `\core\output\single_select`                                       |
  | `\standard_renderer_factory`               | `\core\output\renderer_factory\standard_renderer_factory`          |
  | `\table_dataformat_export_format`          | `\core_table\dataformat_export_format`                             |
  | `\table_default_export_format_parent`      | `\core_table\base_export_format`                                   |
  | `\table_sql`                               | `\core_table\sql_table`                                            |
  | `\tabobject`                               | `\core\output\tabobject`                                           |
  | `\tabtree`                                 | `\core\output\tabtree`                                             |
  | `\templatable`                             | `\core\output\templatable`                                         |
  | `\theme_config`                            | `\core\output\theme_config`                                        |
  | `\theme_overridden_renderer_factory`       | `\core\output\renderer_factory\theme_overridden_renderer_factory`  |
  | `\url_select`                              | `\core\output\url_select`                                          |
  | `\user_picture`                            | `\core\output\user_picture`                                        |
  | `\xhtml_container_stack`                   | `\core\output\xhtml_container_stack`                               |
  | `\YUI_config`                              | `\core\output\requirements\yui`                                    |

  For more information see [MDL-82183](https://tracker.moodle.org/browse/MDL-82183)
- A new method, `\core\output\::get_deprecated_icons()`, has been added to the `icon_system` class. All deprecated icons should be registered through this method.
  Plugins can implement a callback to `pluginname_get_deprecated_icons()` to register their deprecated icons too.
  When `$CFG->debugpageinfo` is enabled, a console message will display a list of the deprecated icons.

  For more information see [MDL-82212](https://tracker.moodle.org/browse/MDL-82212)
- Two new optional parameters have been added to the `\core\output\notification` constructor:
  - `null|string $title` - `null|string $icon`

  For more information see [MDL-82297](https://tracker.moodle.org/browse/MDL-82297)
- A new method, `\url_select::set_disabled_option()`, has been added to enable or disable an option from its url (the key for the option).

  For more information see [MDL-82490](https://tracker.moodle.org/browse/MDL-82490)
- A new static method, `\advanced_testcase::get_fixture_path()`, has been added to enable unit tests to fetch the path to a fixture.

  For more information see [MDL-82627](https://tracker.moodle.org/browse/MDL-82627)
- A new static method, `\advanced_testcase::get_mocked_http_client()`, has been added to allow unit tests to mock the `\core\http_client` and update the DI container.

  For more information see [MDL-82627](https://tracker.moodle.org/browse/MDL-82627)
- The Moodle autoloader should now be registered using `\core\component::register_autoloader` rather than manually doing so in any exceptional location which requires it.
  Note: It is not normally necessary to include the autoloader manually, as it is registered automatically when the Moodle environment is bootstrapped.

  For more information see [MDL-82747](https://tracker.moodle.org/browse/MDL-82747)
- A new JS module for interacting with the Routed REST API has been introduced.
  For more information see the documentation in the `core/fetch` module.

  For more information see [MDL-82778](https://tracker.moodle.org/browse/MDL-82778)
- The `\section_info` class now includes a new method `\section_info::get_sequence_cm_infos()` that retrieves all `\cm_info` instances associated with the course section.

  For more information see [MDL-82845](https://tracker.moodle.org/browse/MDL-82845)
- When rendering a renderable located within a namespace, the namespace
  will now be included in the renderer method name with double-underscores
  separating the namespace parts.

  Note: Only those renderables within an `output` namespace will be
  considered, for example `\core\output\action_menu\link` and only the
  parts of the namespace after `output` will be included.

  The following are examples of the new behaviour:

  | Renderable name                          | Renderer method name                |
  | ---                                      | ---                                 |
  | `\core\output\action_menu\link`          | `render_action_menu__link`          |
  | `\core\output\action_menu\link_primary`  | `render_action_menu__link_primary`  |
  | `\core\output\action\menu\link`          | `render_action__menu__link`         |
  | `\core\output\user_menu\link`            | `render_user_menu__link`            |

  For more information see [MDL-83164](https://tracker.moodle.org/browse/MDL-83164)

### Changed

- The minimum Redis server version is now 2.6.12. The minimum PHP Redis extension version is now 2.2.4.

  For more information see [MDL-69684](https://tracker.moodle.org/browse/MDL-69684)
- The class autoloader has been moved to an earlier point in the Moodle bootstrap.

  Autoloaded classes are now available to scripts using the `ABORT_AFTER_CONFIG` constant.

  For more information see [MDL-80275](https://tracker.moodle.org/browse/MDL-80275)
- The `\core\dataformat::get_format_instance()` method is now public, and can be used to retrieve a writer instance for a given dataformat.

  For more information see [MDL-81781](https://tracker.moodle.org/browse/MDL-81781)
- The `\get_home_page()` function can now return new constant `HOMEPAGE_URL`, applicable when a third-party hook has extended the default homepage options for the site.

  A new function, `\get_default_home_page_url()` has been added which will return the correct URL when this constant is returned.

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

### Deprecated

- The following method has been deprecated and should no longer be used: `reset_password_and_mail`. Please consider using `setnew_password_and_mail` as a replacement.

  For more information see [MDL-64148](https://tracker.moodle.org/browse/MDL-64148)
- - The following methods have been finally deprecated and removed:
    - `\plagiarism_plugin::get_configs()`
    - `\plagiarism_plugin::get_file_results()`
    - `\plagiarism_plugin::update_status()`, please use `{plugin name}_before_standard_top_of_body_html` instead.
  - Final deprecation and removal of `\plagiarism_get_file_results()`. Please use `\plagiarism_get_links()` instead.
  - Final deprecation and removal of `\plagiarism_update_status()`. Please use `\{plugin name}_before_standard_top_of_body_html()` instead.

  For more information see [MDL-71326](https://tracker.moodle.org/browse/MDL-71326)
- `\moodle_list` and `\list_item` were only used by `qbank_managecategories`, and these usages have been removed, so these classes, and the `lib/listlib.php` file have now been deprecated. This method was the only usage of the `QUESTION_PAGE_LENGTH` constant, which was defined in `question_category_object.php`, and so is also now deprecated.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)
- The `$timeout` property of the `\navigation_cache` class has been deprecated.

  For more information see [MDL-79628](https://tracker.moodle.org/browse/MDL-79628)
- The following classes are deprecated as they are handled by core_sms API and smsgateway_aws plugin:
  - `\core\aws\admin_settings_aws_region`
  - `\core\aws\aws_helper`
  - `\core\aws\client_factory`

  For more information see [MDL-80962](https://tracker.moodle.org/browse/MDL-80962)
- The following methods have been deprecated, existing usage should switch to use the secure `\core\encryption::encrypt()` and `\core\encryption::decrypt()` static methods:

  - `\rc4encrypt()`
  - `\rc4decrypt()`
  - `\endecrypt()`

  For more information see [MDL-81940](https://tracker.moodle.org/browse/MDL-81940)
- The following method has been deprecated and should not be used any longer: `\print_grade_menu()`.

  For more information see [MDL-82157](https://tracker.moodle.org/browse/MDL-82157)
- The following files and their contents have been deprecated:

  - `lib/soaplib.php`
  - `lib/tokeniserlib.php`

  For more information see [MDL-82191](https://tracker.moodle.org/browse/MDL-82191)
- The following functions have been initially deprecated:

  - `\get_core_subsystems()`
  - `\get_plugin_types()`
  - `\get_plugin_list()`
  - `\get_plugin_list_with_class()`
  - `\get_plugin_directory()`
  - `\normalize_component()`
  - `\get_component_directory()`
  - `\get_context_instance()`

  Note: These methods have been deprecated for a long time, but previously did not emit any deprecation notice.

  For more information see [MDL-82287](https://tracker.moodle.org/browse/MDL-82287)
- The following methods have been finally deprecated and will now throw an exception if called:

  - `\get_context_instance()`
  - `\can_use_rotated_text()`
  - `\get_system_context()`
  - `\print_arrow()`

  For more information see [MDL-82287](https://tracker.moodle.org/browse/MDL-82287)
- The `global_navigation::load_section_activities` method is now deprecated and replaced by `global_navigation::load_section_activities_navigation`.

  For more information see [MDL-82845](https://tracker.moodle.org/browse/MDL-82845)
- The following renderer methods have been deprecated from the core
  renderer:

  | method                               | replacement                           |
  | ---                                  | ---                                   |
  | `render_action_menu_link`            | `render_action_menu__link`            |
  | `render_action_menu_link_primary`    | `render_action_menu__link_primary`    |
  | `render_action_menu_link_secondary`  | `render_action_menu__link_secondary`  |
  | `render_action_menu_filler`          | `render_action_menu__filler`          |

  For more information see [MDL-83164](https://tracker.moodle.org/browse/MDL-83164)

### Removed

- The previously deprecated function `search_generate_text_SQL` has been removed and can no longer be used.

  For more information see [MDL-48940](https://tracker.moodle.org/browse/MDL-48940)
- The previously deprecated function `\core_text::reset_caches()` has been removed and can no longer be used.

  For more information see [MDL-71748](https://tracker.moodle.org/browse/MDL-71748)
- The following previously deprecated methods have been removed and can no longer be used:
    - `\renderer_base::should_display_main_logo()`

  For more information see [MDL-73165](https://tracker.moodle.org/browse/MDL-73165)
- Final deprecation of `\print_error()`. Please use the `\moodle_exception` class instead.

  For more information see [MDL-74484](https://tracker.moodle.org/browse/MDL-74484)
- Final deprecation of `\core\task\manager::ensure_adhoc_task_qos()`.

  For more information see [MDL-74843](https://tracker.moodle.org/browse/MDL-74843)
- Support for the deprecated block and activity namespaces `<component>\local\views\secondary`, which supported the overriding of secondary navigation, has now been entirely removed.

  For more information see [MDL-74939](https://tracker.moodle.org/browse/MDL-74939)
- Remove deprecation layer for YUI JS Events. The deprecation layer was introduced with MDL-70990 and MDL-72291.

  For more information see [MDL-77167](https://tracker.moodle.org/browse/MDL-77167)

### Fixed

- The `\navigation_cache` class now uses the Moodle Universal Cache (MUC) to store the navigation cache data instead of storing it in the global `$SESSION` variable.

  For more information see [MDL-79628](https://tracker.moodle.org/browse/MDL-79628)
- All the `setUp()` and `tearDown()` methods of `PHPUnit` now are required to, always, call to their parent counterparts. This is a good practice to avoid future problems, especially when updating to PHPUnit >= 10.
  This includes the following methods:
    - `setUp()`
    - `tearDown()`
    - `setUpBeforeClass()`
    - `tearDownAfterClass()`

  For more information see [MDL-81523](https://tracker.moodle.org/browse/MDL-81523)
- Use server timezone when constructing `\DateTimeImmutable` for the system `\core\clock` implementation.

  For more information see [MDL-81894](https://tracker.moodle.org/browse/MDL-81894)
