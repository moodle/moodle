# Moodle Upgrade notes

This file contains important information for developers on changes to the Moodle codebase.

More detailed information on key changes can be found in the [Developer update notes](https://moodledev.io/docs/devupdate) for your version of Moodle.

The format of this change log follows the advice given at [Keep a CHANGELOG](https://keepachangelog.com).

## 4.5dev+

### core_badges

#### Deprecated

- The badges/newbadge.php page has been deprecated and merged with badges/edit.php. Please, use badges/edit.php instead.

  For more information see [MDL-43938](https://tracker.moodle.org/browse/MDL-43938)
- OPEN_BADGES_V1 is deprecated and should not be used anymore.

  For more information see [MDL-70983](https://tracker.moodle.org/browse/MDL-70983)
- The course_badges systemreport has been deprecated and merged with the badges systemreport. Please, use the badges systemreport instead.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)
- The $showmanage parameter in the core_badges\output\standard_action_bar constructor has been deprecated and should not be used anymore.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)
- The badges/view.php page has been deprecated and merged with badges/index.php. Please, use badges/index.php instead.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)

#### Removed

- Final removal of BADGE_BACKPACKAPIURL and BADGE_BACKPACKWEBURL.

  For more information see [MDL-70983](https://tracker.moodle.org/browse/MDL-70983)

#### Added

- New webservices enable_badges and disable_badges have been added.

  For more information see [MDL-82168](https://tracker.moodle.org/browse/MDL-82168)

#### Changed

- Added fields `recipientid` and `recipientfullname` to `user_badge_exporter`, which is used in the return structure of external functions `core_badges_get_user_badge_by_hash` and `core_badges_get_user_badges`.

  For more information see [MDL-82742](https://tracker.moodle.org/browse/MDL-82742)

### core

#### Removed

- The previously deprecated function `search_generate_text_SQL` has been removed and can no longer be used.

  For more information see [MDL-48940](https://tracker.moodle.org/browse/MDL-48940)
- The previously deprecated function `core_text::reset_caches()` has been removed and can no longer be used.

  For more information see [MDL-71748](https://tracker.moodle.org/browse/MDL-71748)
- The following previously deprecated methods have been removed and can no longer be used:
    - `renderer_base::should_display_main_logo`

  For more information see [MDL-73165](https://tracker.moodle.org/browse/MDL-73165)
- Final deprecation of print_error(). Use moodle_exception instead.

  For more information see [MDL-74484](https://tracker.moodle.org/browse/MDL-74484)
- Final deprecation of \core\task\manager::ensure_adhoc_task_qos()

  For more information see [MDL-74843](https://tracker.moodle.org/browse/MDL-74843)
- Support for deprecated `<component>\local\views\secondary` block/activity namespace, for overriding secondary navigation, is now entirely removed

  For more information see [MDL-74939](https://tracker.moodle.org/browse/MDL-74939)
- Remove deprecation layer for YUI Events. The deprecation layer was introduced with MDL-70990 and MDL-72291.

  For more information see [MDL-77167](https://tracker.moodle.org/browse/MDL-77167)

#### Deprecated

- The following method has been deprecated and should no longer be used: `reset_password_and_mail`. Please consider using `setnew_password_and_mail` as a replacement.

  For more information see [MDL-64148](https://tracker.moodle.org/browse/MDL-64148)
- - Final deprecation and removal of the following functions:
    - `plagiarism_plugin::get_configs()`
    - `plagiarism_plugin::get_file_results()`
    - `plagiarism_plugin::update_status()`, please use `{plugin name}_before_standard_top_of_body_html` instead.
  - Final deprecation and removal of `plagiarism_get_file_results()`. Please use `plagiarism_get_links()` instead. - Final deprecation and removal of `plagiarism_update_status()`. Please use `{plugin name}_before_standard_top_of_body_html()` instead.

  For more information see [MDL-71326](https://tracker.moodle.org/browse/MDL-71326)
- `moodle_list` and `list_item` were only used by `qbank_managecategories`, and these usages have been removed, so these classes (and thus all of listlib.php) are now deprecated. This method was the only usage of the `QUESTION_PAGE_LENGTH` constant, which was defined in `question_category_object.php`, and so is also now deprecated.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)
- The following methods have been deprecated, existing usage should switch to secure `\core\encryption` library:
  - `rc4encrypt`
  - `rc4decrypt`
  - `endecrypt`

  For more information see [MDL-81940](https://tracker.moodle.org/browse/MDL-81940)
- The following method has been deprecated and should not be used any longer: `print_grade_menu`.

  For more information see [MDL-82157](https://tracker.moodle.org/browse/MDL-82157)
- The following files and their contents have been deprecated:
  - `lib/soaplib.php`
  - `lib/tokeniserlib.php`

  For more information see [MDL-82191](https://tracker.moodle.org/browse/MDL-82191)
- The following methods have been formally deprecated:
  - `get_core_subsystems`
  - `get_plugin_types`
  - `get_plugin_list`
  - `get_plugin_list_with_class`
  - `get_plugin_directory`
  - `normalize_component`
  - `get_component_directory`
  - `get_context_instance`
  Note: These methods have been deprecated for a long time, but previously did not emit any deprecation notice.

  For more information see [MDL-82287](https://tracker.moodle.org/browse/MDL-82287)
- The following methods have been finally deprecated and will now throw an exception if called:
  - `get_context_instance`
  - `can_use_rotated_text`
  - `get_system_context`
  - `print_arrow`

  For more information see [MDL-82287](https://tracker.moodle.org/browse/MDL-82287)

#### Added

- Add \core_user::get_name_placeholders() to return an array of user name fields.

  For more information see [MDL-64148](https://tracker.moodle.org/browse/MDL-64148)
- The following classes have been renamed and now support autoloading.
  Existing classes are currently unaffected.
  | Old class name | New class name |
  | --- | --- |
  | `\core_component` | `\core\component` |

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
   - The minimum Redis server version is now 2.6.12.
   - The minimum PHP Redis extension version is now 2.2.4.

  For more information see [MDL-69684](https://tracker.moodle.org/browse/MDL-69684)
- Added stored progress bars

  For more information see [MDL-70854](https://tracker.moodle.org/browse/MDL-70854)
- Two new functions have been introduced in the \moodle_database class:
  - `get_counted_records_sql()`
  - `get_counted_recordset_sql()`
  These methods are compatible with all databases.
  They will check the current running database engine and apply the COUNT window function if it is supported,
  otherwise, they will use the usual COUNT function.
  The COUNT window function optimization is applied to the following databases:
  - PostgreSQL
  - MariaDB
  - Oracle
  MySQL and SQL Server do not use this optimization due to insignificant performance differences before and
  after the improvement.

  For more information see [MDL-78030](https://tracker.moodle.org/browse/MDL-78030)
- The `after_config()` callback has been converted to a hook, `\core\hook\after_config`.

  For more information see [MDL-79011](https://tracker.moodle.org/browse/MDL-79011)
- The core\output\select_menu widget now supports rendering dividers between menu options. Empty elements (null or empty strings) within the array of options are considered and rendered as dividers in the dropdown menu.

  For more information see [MDL-80747](https://tracker.moodle.org/browse/MDL-80747)
- The `core\output\select_menu` widget now supports a new feature: inline labels. You can render the label inside the combobox widget by passing `true` to the `$inlinelabel` parameter when calling the `->set_label()` method.

  For more information see [MDL-80747](https://tracker.moodle.org/browse/MDL-80747)
- The following classes have been renamed.
  Existing classes are currently unaffected.
  | Old class name | New class name |
  | --- | --- |
  | `\core_user` | `\core\user` |

  For more information see [MDL-81031](https://tracker.moodle.org/browse/MDL-81031)
- New DML constant `SQL_INT_MAX` to define the size of a large integer with cross database platform support

  For more information see [MDL-81282](https://tracker.moodle.org/browse/MDL-81282)
- The function update_display_mode will update the eye icon (enabled/disabled) in the availability. The $pluginname is represented to the plugin need to update. The $displaymode is represented to the eye icon. Whether it enabled or disabled.

  For more information see [MDL-81533](https://tracker.moodle.org/browse/MDL-81533)
- Added an `exception` L2 Namespace to APIs

  For more information see [MDL-81903](https://tracker.moodle.org/browse/MDL-81903)
- Added a mechanism to support autoloading of legacy class files.
  This will help to reduce the number of require_once calls in the codebase, and move away from the use of monolithic libraries.

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
- The following classes have been moved into the `\core` namespace and now support autoloading:
  - `emoticon_manager`
  - `lang_string`

  For more information see [MDL-81920](https://tracker.moodle.org/browse/MDL-81920)
- The following classes have been renamed and now support autoloading. Existing classes are currently unaffected.
  | Old class name | New class name |
  | --- | --- |
  | `\moodle_url` | `\core\url` |
  | `\progress_trace` | `\core\output\progress_trace` |
  | `\combined_progress_trace` | `\core\output\progress_trace\combined_progress_trace` |
  | `\error_log_progress_trace` | `\core\output\progress_trace\error_log_progress_trace` |
  | `\html_list_progress_trace` | `\core\output\progress_trace\html_list_progress_trace` |
  | `\html_progress_trace` | `\core\output\progress_trace\html_progress_trace` |
  | `\null_progress_trace` | `\core\output\progress_trace\null_progress_trace` |
  | `\progress_trace_buffer` | `\core\output\progress_trace\progress_trace_buffer` |
  | `\text_progress_trace` | `\core\output\progress_trace\text_progress_trace` |

  For more information see [MDL-81960](https://tracker.moodle.org/browse/MDL-81960)
- The following classes are now also available in the following new locations. They will continue to work in their old locations:
  | Old classname | New classname |
  | --- | --- |
  | `\action_link` | `\core\output\action_link` |
  | `\action_menu_filler` | `\core\output\action_menu\filler` |
  | `\action_menu_link_primary` | `\core\output\action_menu\link_primary` |
  | `\action_menu_link_secondary` | `\core\output\action_menu\link_secondary` |
  | `\action_menu_link` | `\core\output\action_menu\link` |
  | `\action_menu` | `\core\output\action_menu` |
  | `\block_contents` | `\core_block\output\block_contents` |
  | `\block_move_target` | `\core_block\output\block_move_target` |
  | `\component_action` | `\core\output\actions\component_action` |
  | `\confirm_action` | `\core\output\actions\confirm_action` |
  | `\context_header` | `\core\output\context_header` |
  | `\core\output\local\action_menu\subpanel` | `\core\output\action_menu\subpanel` |
  | `\core_renderer_ajax` | `\core\output\core_renderer_ajax` |
  | `\core_renderer_cli` | `\core\output\core_renderer_cli` |
  | `\core_renderer_maintenance` | `\core\output\core_renderer_maintenance` |
  | `\core_renderer` | `\core\output\core_renderer` |
  | `\custom_menu_item` | `\core\output\custom_menu_item` |
  | `\custom_menu` | `\core\output\custom_menu` |
  | `\file_picker` | `\core\output\file_picker` |
  | `\flexible_table` | `\core_table\flexible_table` |
  | `\fragment_requirements_manager` | `\core\output\requirements\fragment_requirements_manager` |
  | `\help_icon` | `\core\output\help_icon` |
  | `\html_table_cell` | `\core_table\output\html_table_cell` |
  | `\html_table_row` | `\core_table\output\html_table_row` |
  | `\html_table` | `\core_table\output\html_table` |
  | `\html_writer` | `\core\output\html_writer` |
  | `\image_icon` | `\core\output\image_icon` |
  | `\initials_bar` | `\core\output\initials_bar` |
  | `\js_writer` | `\core\output\js_writer` |
  | `\page_requirements_manager` | `\core\output\requirements\page_requirements_manager` |
  | `\paging_bar` | `\core\output\paging_bar` |
  | `\pix_emoticon` | `\core\output\pix_emoticon` |
  | `\pix_icon_font` | `\core\output\pix_icon_font` |
  | `\pix_icon_fontawesome` | `\core\output\pix_icon_fontawesome` |
  | `\pix_icon` | `\core\output\pix_icon` |
  | `\plugin_renderer_base` | `\core\output\plugin_renderer_base` |
  | `\popup_action` | `\core\output\actions\popup_action` |
  | `\preferences_group` | `\core\output\preferences_group` |
  | `\preferences_groups` | `\core\output\preferences_groups` |
  | `\progress_bar` | `\core\output\progress_bar` |
  | `\renderable` | `\core\output\renderable` |
  | `\renderer_base` | `\core\output\renderer_base` |
  | `\renderer_factory_base` | `\core\output\renderer_factory\renderer_factory_base` |
  | `\renderer_factory` | `\core\output\renderer_factory\renderer_factory_interface` |
  | `\single_button` | `\core\output\single_button` |
  | `\single_select` | `\core\output\single_select` |
  | `\standard_renderer_factory` | `\core\output\renderer_factory\standard_renderer_factory` |
  | `\table_dataformat_export_format` | `\core_table\dataformat_export_format` |
  | `\table_default_export_format_parent` | `\core_table\base_export_format` |
  | `\table_sql` | `\core_table\sql_table` |
  | `\tabobject` | `\core\output\tabobject` |
  | `\tabtree` | `\core\output\tabtree` |
  | `\templatable` | `\core\output\templatable` |
  | `\theme_config` | `\core\output\theme_config` |
  | `\theme_overridden_renderer_factory` | `\core\output\renderer_factory\theme_overridden_renderer_factory` |
  | `\url_select` | `\core\output\url_select` |
  | `\user_picture` | `\core\output\user_picture` |
  | `\xhtml_container_stack` | `\core\output\xhtml_container_stack` |
  | `\YUI_config` | `\core\output\requirements\yui` |

  For more information see [MDL-82183](https://tracker.moodle.org/browse/MDL-82183)
- A new method, get_deprecated_icons(), has been added to the icon_system class. All deprecated icons should be registered through this method. Plugins can implement a callback to pluginname_get_deprecated_icons() to register their deprecated icons too. When $CFG->debugpageinfo is enabled, a console message will display a list of the deprecated icons.

  For more information see [MDL-82212](https://tracker.moodle.org/browse/MDL-82212)
- Add optional icon and title to notification. Two parameters have been added to the `core\output\notification` so when creating a notification you can pass an icon and a title.

  For more information see [MDL-82297](https://tracker.moodle.org/browse/MDL-82297)
- Add set_disabled_option method to url_select to enable or disable an option from its url (the key for the option).

  For more information see [MDL-82490](https://tracker.moodle.org/browse/MDL-82490)
- There is a new method called `get_fixture_path()` that supports getting the path to the fixture

  For more information see [MDL-82627](https://tracker.moodle.org/browse/MDL-82627)
- There is a new method called `get_mocked_http_client()` that supports mocking the `http_client`

  For more information see [MDL-82627](https://tracker.moodle.org/browse/MDL-82627)
- The Moodle autoloader should now be registered using `\core\component::register_autoloader` rather than manually doing so in any exceptional location which requires it. It is not normally necessary to include the autoloader manually, as it is registered automatically when the Moodle environment is bootstrapped.

  For more information see [MDL-82747](https://tracker.moodle.org/browse/MDL-82747)
- A new JS module for interacting with the Routed REST API has been introduced.
  For more information see the documentation in the `core/fetch` module.

  For more information see [MDL-82778](https://tracker.moodle.org/browse/MDL-82778)

#### Changed

- The class autoloader has been moved to an earlier point in the Moodle bootstrap.
  Autoloaded classes are now available to scripts using the `ABORT_AFTER_CONFIG` constant.

  For more information see [MDL-80275](https://tracker.moodle.org/browse/MDL-80275)
- The `\core\dataformat::get_format_instance` method is now public, and can be used to retrieve a writer instance for a given dataformat

  For more information see [MDL-81781](https://tracker.moodle.org/browse/MDL-81781)
- The `get_home_page()` method can now return new constant `HOMEPAGE_URL`, applicable when a third-party hook has extended the default homepage options for the site
  A new method, `get_default_home_page_url()` has been added which will return the correct URL when this constant is returned

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

#### Fixed

- All the setup and tear down methods of `PHPUnit` now are required to, always, call to their parent counterparts. This is a good practice to avoid future problems, especially when updating to PHPUnit >= 10.
  This includes the following methods:
    - `setUp()`
    - `tearDown()`
    - `setUpBeforeClass()`
    - `tearDownAfterClass()`

  For more information see [MDL-81523](https://tracker.moodle.org/browse/MDL-81523)
- Use server timezone when constructing `\DateTimeImmutable` for the system `\core\clock` implementation.

  For more information see [MDL-81894](https://tracker.moodle.org/browse/MDL-81894)

### core_reportbuilder

#### Added

- The return type of the `set_checkbox_toggleall` callback, defined by system reports, can now be null. Use if the checkbox should not be shown for the row.

  For more information see [MDL-52046](https://tracker.moodle.org/browse/MDL-52046)
- System reports now support native entity column aggregation via each columns `set_aggregation()` method

  For more information see [MDL-76392](https://tracker.moodle.org/browse/MDL-76392)
- The following external methods now return tags data relevant to each custom report:
    - `core_reportbuilder_list_reports`
    - `core_reportbuilder_retrieve_report`

  For more information see [MDL-81433](https://tracker.moodle.org/browse/MDL-81433)
- Added a new database helper method `sql_replace_parameters` to help ensure uniqueness of parameters within a SQL expression

  For more information see [MDL-81434](https://tracker.moodle.org/browse/MDL-81434)
- New format helper `format_time` method, for use in column callbacks that represent a duration of time (e.g. "3 days 4 hours")

  For more information see [MDL-82466](https://tracker.moodle.org/browse/MDL-82466)
- Methods add_columns_from_entity(), add_filters_from_entity() and report_element_search() have been moved from \core_reportbuilder\datasource class to \core_reportbuilder\base class in order to be available also for system reports

  For more information see [MDL-82529](https://tracker.moodle.org/browse/MDL-82529)

#### Removed

- Support for the following entity classes, renamed since 4.1, has now been removed completely:
  - `core_admin\local\entities\task_log`
  - `core_cohort\local\entities\cohort`
  - `core_cohort\local\entities\cohort_member`
  - `core_course\local\entities\course_category`
  - `report_configlog\local\entities\config_change`

  For more information see [MDL-74583](https://tracker.moodle.org/browse/MDL-74583)
- The following previously deprecated local helper methods have been removed and can no longer be used:
    - `audience::get_all_audiences_menu_types`
    - `report::get_available_columns`

  For more information see [MDL-76690](https://tracker.moodle.org/browse/MDL-76690)

#### Changed

- In order to better support float values in filter forms, the following filter types now cast given SQL prior to comparison:
    - `duration`
    - `filesize`
    - `number`

  For more information see [MDL-81168](https://tracker.moodle.org/browse/MDL-81168)
- The base datasource `add_all_from_entities` method accepts a new optional parameter to specify which entities to add elements from

  For more information see [MDL-81330](https://tracker.moodle.org/browse/MDL-81330)
- All time related code has been updated to the PSR-20 Clock interface, as such the following methods no longer accept a `$timenow` parameter (instead please use `\core\clock` dependency injection):
  - `core_reportbuilder_generator::create_schedule`
  - `core_reportbuilder\local\helpers\schedule::[create_schedule|calculate_next_send_time]`

  For more information see [MDL-82041](https://tracker.moodle.org/browse/MDL-82041)
- The following classes have been moved to use the new exception API as a l2 namespace:
  - `core_reportbuilder\\report_access_exception` => `core_reportbuilder\\exception\\report_access_exception` - `core_reportbuilder\\source_invalid_exception` => `core_reportbuilder\\exception\\source_invalid_exception` - `core_reportbuilder\\source_unavailable_exception` => `core_reportbuilder\\exception\\source_unavailable_exception`

  For more information see [MDL-82133](https://tracker.moodle.org/browse/MDL-82133)

### mod_assign

#### Added

- Added 2 new settings:
    - `mod_assign/defaultgradetype`
      - The value of this setting dictates which of the GRADE_TYPE_X constants is the default option when creating new instances of the assignment.
      - The default value is GRADE_TYPE_VALUE (Point)
    - `mod_assign/defaultgradescale`
      - The value of this setting dictates which of the existing scales is the default option when creating new instances of the assignment.

  For more information see [MDL-54105](https://tracker.moodle.org/browse/MDL-54105)
- A new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)

#### Removed

- The default option "Never" for `attemptreopenmethod` setting, which disallowed multiple attempts at the assignment, has been removed. This option was unnecessary because limiting attempts to 1 through the `maxattempts` setting achieves the same behavior.

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)

#### Deprecated

- The constant `ASSIGN_ATTEMPT_REOPEN_METHOD_NONE` has been deprecated, and a new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- The `$submissionpluginenabled` and `$submissioncount` parameters from the constructor of the `mod_assign\output::grading_actionmenu` class have been deprecated.

  For more information see [MDL-80752](https://tracker.moodle.org/browse/MDL-80752)
- Method assign_grading_table::col_picture has been deprecated.

  For more information see [MDL-82292](https://tracker.moodle.org/browse/MDL-82292)
- Method assign_grading_table::col_userid has been deprecated.

  For more information see [MDL-82295](https://tracker.moodle.org/browse/MDL-82295)
- The mod_assign_grading_options_form class has been deprecated since it is no longer used.

  For more information see [MDL-82857](https://tracker.moodle.org/browse/MDL-82857)

### core_role

#### Added

- Move all session management to the \core\session\manager class.
  This removes the dependancy to use the "sessions" table.
  Session management plugins (like redis) now need to inherit
  the base \core\session\handler class which implements
  SessionHandlerInterface and override methods as required.
  The following methods in \core\session\manager have been deprecated:
  * kill_all_sessions use destroy_all instead
  * kill_session use destroy instead
  * kill_sessions_for_auth_plugin use destroy_by_auth_plugin instead
  * kill_user_sessions use destroy_user_sessions instead

  For more information see [MDL-66151](https://tracker.moodle.org/browse/MDL-66151)

### tool_oauth2

#### Added

- The `get_additional_login_parameters()` method now supports adding the language code to the authentication request so that the OAuth2 login page matches the language in Moodle.

  For more information see [MDL-67554](https://tracker.moodle.org/browse/MDL-67554)

### report

#### Removed

- The previously deprecated `report_helper::save_selected_report` method has been removed and can no longer be used

  For more information see [MDL-72353](https://tracker.moodle.org/browse/MDL-72353)

#### Changed

- The `report_helper::print_report_selector` method accepts an additional argument for adding content to the tertiary navigation to align with the report selector

  For more information see [MDL-78773](https://tracker.moodle.org/browse/MDL-78773)

### qbank_managecategories

#### Deprecated

- question_category_list and question_category_list_item are no longer used, and are deprecated. Category lists are now generated by templates.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)
- The methods `question_is_only_child_of_top_category_in_context`, `question_is_top_category` and `question_can_delete_cat` from `qbank_managecategories\helper` class have been deprecated and moved to the `\core_question\category_manager` class, minus the misleading `question_` prefix. Following the creation of this class, it does not make sense for them to live inside the `qbank_managecategories` plugin.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)
- `qbank_managecategories\question_category_object` is now completely deprecated. Its methods have either been migrated to `qbank_managecategories\question_categories`, `core_question\category_manager`, or are no longer used at all.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)

#### Changed

- The methods in the `question_category_object` class that are still required following this change have been split between `\qbank_managecategories\question_categories` (for the parts used within this plugin for display a list of categories) and `\core_question\category_manager` (for the parts used for generate CRUD operations on question categories, including outside of this plugin). This will allow `question_category_object` to be deprecated, and avoids other parts of the system wishing to manipulate question categories from having to violate cross-component communication rules.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)

### mod_feedback

#### Deprecated

- The `feedback_check_is_switchrole` method has been deprecated as it didn't work

  For more information see [MDL-72424](https://tracker.moodle.org/browse/MDL-72424)
- The method `mod_feedback\output\renderer::create_template_form()` has been deprecated. It is not used anymore.

  For more information see [MDL-81742](https://tracker.moodle.org/browse/MDL-81742)

### repository_onedrive

#### Removed

- The following previously deprecated methods have been removed and can no longer be used:
  - `can_import_skydrive_files`
  - `import_skydrive_files`

  For more information see [MDL-72620](https://tracker.moodle.org/browse/MDL-72620)

### report_eventlist

#### Deprecated

- The following deprecated methods in `report_eventlist_list_generator` have been removed:
  - `get_core_events_list()`
  - `get_non_core_event_list()`

  For more information see [MDL-72786](https://tracker.moodle.org/browse/MDL-72786)

### core_message

#### Removed

- Final deprecation MESSAGE_DEFAULT_LOGGEDOFF / MESSAGE_DEFAULT_LOGGEDIN.

  For more information see [MDL-73284](https://tracker.moodle.org/browse/MDL-73284)

#### Changed

- The `\core_message\helper::togglecontact_link_params` now accepts a new optional param called `isrequested` to indicate the status of the contact request

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

#### Deprecated

- The `core_message/remove_contact_button` template is deprecated and will be removed in the future version

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

### core_course

#### Removed

- The previously deprecated `print_course_request_buttons` method has been removed and can no longer be used

  For more information see [MDL-73976](https://tracker.moodle.org/browse/MDL-73976)
- The $course class property in the core_course\output\actionbar\group_selector class has been removed.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)

#### Added

- - New optional sectionNum parameter has been added to activitychooser AMD module initializer. - New option sectionnum parameter has been added to get_course_content_items() external function. - New optional sectionnum parameter has been added to get_content_items_for_user_in_course() function.

  For more information see [MDL-81675](https://tracker.moodle.org/browse/MDL-81675)
- Webservices `core_course_get_courses_by_field` now accepts a new parameter `sectionid` to be able to retrieve the course that has the indicated section

  For more information see [MDL-81699](https://tracker.moodle.org/browse/MDL-81699)
- Added new 'activitychooserbutton' output class to display the activitychooser button. New action_links can be added to the button via hooks converting it into a dropdown.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- New `core_course\hook\before_activitychooserbutton_exported` hook added to allow third-party plugins to extend activity chooser button options

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- i_open_section_edit_menu(), i_show_section(), i_hide_section(), i_wait_until_section_is_available(), show_section_link_exists(), hide_section_link_exists() and section_exists() functions have been improved to accept not only section number but also section name.

  For more information see [MDL-82259](https://tracker.moodle.org/browse/MDL-82259)

#### Deprecated

- The data-sectionid attribute in the activity chooser has been deprecated. Please update your code to use data-sectionnum instead.

  For more information see [MDL-81676](https://tracker.moodle.org/browse/MDL-81676)
- The $course parameter in the constructor of the core_course\output\actionbar\group_selector class has been deprecated and is no longer used.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)

#### Changed

- The reset course page has been improved. The words "Delete" and "Remove" have been removed from all the options to make it easier to focus on the data to be removed and avoid inconsistencies and duplicated information. Third party plugins implementing reset methods might need to:
  - Add static element in the _reset_course_form_definition method before all the options with the Delete string:
      `$mform->addElement('static', 'assigndelete', get_string('delete'));`
  - Review all the strings used in the reset page to remove the "Delete" or "Remove" words from them.

  For more information see [MDL-81872](https://tracker.moodle.org/browse/MDL-81872)
- The external function core_course::get_course_contents now returns the component and itemid of sections.

  For more information see [MDL-82385](https://tracker.moodle.org/browse/MDL-82385)

### theme

#### Removed

- Removed all references to iconhelp, icon-pre, icon-post, iconlarge, and iconsort classes

  For more information see [MDL-74251](https://tracker.moodle.org/browse/MDL-74251)

#### Added

- New `core/context_header` mustache template has been added. This template can be overridden by themes to modify the context header

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)

#### Deprecated

- The method `\core\output\core_renderer::render_context_header` has been deprecated please use `\core\output\core_renderer::render($contextheader)` instead

  For more information see [MDL-82160](https://tracker.moodle.org/browse/MDL-82160)

### core_grades

#### Removed

- The following previously deprecated Behat step helper methods have been removed and can no longer be used:
   - `behat_grade::select_in_gradebook_navigation_selector`
   - `behat_grade::select_in_gradebook_tabs`

  For more information see [MDL-74581](https://tracker.moodle.org/browse/MDL-74581)

#### Changed

- The grade `itemname` property contained in the return structure of the following external methods is now PARAM_RAW:
    - `core_grades_get_gradeitems`
    - `gradereport_user_get_grade_items`

  For more information see [MDL-80017](https://tracker.moodle.org/browse/MDL-80017)

#### Deprecated

- The behat step definition behat_grade::i_confirm_in_search_within_the_gradebook_widget_exists has been deprecated. Please use behat_general::i_confirm_in_search_combobox_exists instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The behat step definition behat_grade::i_confirm_in_search_within_the_gradebook_widget_does_not_exist has been deprecated. Please use behat_general::i_confirm_in_search_combobox_does_not_exist instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The behat step definition behat_grade::i_click_on_in_search_widget has been deprecated. Please use behat_general::i_click_on_in_search_combobox instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The `core_grades_renderer::group_selector()` method has been deprecated. Please use `\core_course\output\actionbar\renderer` to render a `group_selector` renderable instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

### core_backup

#### Removed

- Final deprecation and removal of core_backup\copy\copy in backup/util/ui/classes/copy.php. Please use copy_helper from backup/util/helper/copy_helper.class.php instead.

  For more information see [MDL-75022](https://tracker.moodle.org/browse/MDL-75022)
- Final deprecation of base_controller::get_copy(). Please use restore_controller::get_copy() instead.

  For more information see [MDL-75025](https://tracker.moodle.org/browse/MDL-75025)
- Final deprecation of base_controller::set_copy(). Please use a restore controller for storing copy information instead.

  For more information see [MDL-75025](https://tracker.moodle.org/browse/MDL-75025)

### core_files

#### Added

- The following are the changes made:
  - New hook after_file_created
  - In the \core_files\file_storage, new additional param $notify (default is true) added to:
    - ::create_file_from_storedfile()
    - ::create_file_from_pathname()
    - ::create_file_from_string()
    - ::create_file()
    If true, it will trigger the after_file_created hook to re-create the image.

  For more information see [MDL-75850](https://tracker.moodle.org/browse/MDL-75850)

### core_user

#### Deprecated

- The participants_search::get_total_participants_count() is no longer used since the total count can be obtained from ::get_participants()

  For more information see [MDL-78030](https://tracker.moodle.org/browse/MDL-78030)

#### Changed

- The visibility of the methods: check_access_for_dynamic_submission() and get_options() in core_user\form\private_files has been changed from protected to public.

  For more information see [MDL-78293](https://tracker.moodle.org/browse/MDL-78293)

#### Added

- New `\core_user\hook\extend_default_homepage` hook added to allow third-party plugins to extend the default homepage options for the site

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

### core_question

#### Added

- A new utility function `format_question_fragment` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

#### Changed

- column_base::from_column_name now has an ignoremissing field, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81125](https://tracker.moodle.org/browse/MDL-81125)

### tool

#### Removed

- The Convert to InnoDB plugin (tool_innodb) has been completely removed.

  For more information see [MDL-78776](https://tracker.moodle.org/browse/MDL-78776)

### core_webservice

#### Deprecated

- The `token_table` and `token_filter` classes have been deprecated, in favour of new report builder implementation.

  For more information see [MDL-79496](https://tracker.moodle.org/browse/MDL-79496)

### quiz

#### Added

- The functions quiz_overview_report::regrade_attempts and regrade_batch_of_attempts now have a new optional parameter $slots to only regrade some slots in each attempt (default all).

  For more information see [MDL-79546](https://tracker.moodle.org/browse/MDL-79546)

### gradereport_grader

#### Deprecated

- The `gradereport_grader/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

### gradereport_singleview

#### Deprecated

- The `gradereport_singleview/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

### gradereport_user

#### Deprecated

- The `gradereport_user/group` ESM has been deprecated. Please use `core_course/actionbar/group` instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

### core_table

#### Added

- A new `$reponsive` property (defaulting to `true`) has been added to the `core_table\flexible_table` class.
  This property allows you to control whether the table is rendered as a responsive table.

  For more information see [MDL-80748](https://tracker.moodle.org/browse/MDL-80748)

#### Changed

- `core_table\dynamic` declares a new method `::has_capability()` to allow classes implementing this interface to perform access checks on the dynamic table. This is a breaking change that all dynamic table implementations must implement for continued functionality.

  For more information see [MDL-82567](https://tracker.moodle.org/browse/MDL-82567)

### mod_data

#### Added

- The `data_add_record` method accepts a new `$approved` parameter to set the corresponding state of the new record

  For more information see [MDL-81274](https://tracker.moodle.org/browse/MDL-81274)

#### Deprecated

- The `mod_data_renderer::render_fields_footer` method has been deprecated as it's no longer used

  For more information see [MDL-81321](https://tracker.moodle.org/browse/MDL-81321)

### core_admin

#### Added

- Add availability_management_table is a table which extends from plugin_management_table. Create the availability_management_table can reusable the toggle button for enabled column.

  For more information see [MDL-81533](https://tracker.moodle.org/browse/MDL-81533)

### editor_tiny

#### Changed

- The `helplinktext` language string is no longer required by editor plugins, instead the `pluginname` will be used in the help dialogue

  For more information see [MDL-81572](https://tracker.moodle.org/browse/MDL-81572)

### output

#### Added

- Added a new `renderer_base::get_page` getter method

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)

### core_courseformat

#### Added

- The constructor of `core_courseformat\output\local\state\cm` has been updated to accept a new optional parameter, `$istrackeduser`.
  If `istrackeduser` is pre-computed for the course module's course, it can be provided here to avoid an additional function call.

  For more information see [MDL-81610](https://tracker.moodle.org/browse/MDL-81610)
- Added new 'create_module' webservice to create new module (with quickcreate feature) instances in the course.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- New $disabled parameter has been added to select, select_optgroup and select_option html_writers to create disabled option elements.

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)
- New \core_courseformat\output\local\content\basecontrolmenu class has been created. Existing \core_courseformat\output\local\content\cm\controlmenu and \core_courseformat\output\local\content\section\controlmenu classes extend the new \core_courseformat\output\local\content\basecontrolmenu class.

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)
- New \core_courseformat\output\local\content\cm\delegatedcontrolmenu class has been created extending \core_courseformat\output\local\content\basecontrolmenu class to render delegated section action menu combining section and module action menu.

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)

### core_completion

#### Changed

- get_overall_completion_state() function could also return COMPLETION_COMPLETE_FAIL and not only COMPLETION_COMPLETE and COMPLETION_INCOMPLETE

  For more information see [MDL-81749](https://tracker.moodle.org/browse/MDL-81749)

#### Added

- A new FEATURE_COMPLETION plugin support constant has been added. In the future, this constant will be used to indicate when a plugin does not allow completion and it is enabled by default.

  For more information see [MDL-83008](https://tracker.moodle.org/browse/MDL-83008)

### mod

#### Added

- Added new FEATURE_QUICKCREATE for modules that can be quickly created in the course wihout filling a previous form.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)

### core_report

#### Added

- Report has been added to subsystem components list

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)
- New coursestructure output general class has been created

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)

### theme_boost

#### Added

- Bridged theme-color-level using a new shift-color function to prepare for its deprecation in Boostrap 5.

  For more information see [MDL-81816](https://tracker.moodle.org/browse/MDL-81816)
- Upon upgrading Font Awesome from version 4 to 6, the solid family was selected by default. However, FA6 includes additional families such as regular and brands. Support for these families has now been integrated, allowing icons defined with icon_system::FONTAWESOME to use them. Icons can add the FontAwesome family (fa-regular, fa-brands, fa-solid) near the icon name to display it using this styling.

  For more information see [MDL-82210](https://tracker.moodle.org/browse/MDL-82210)

#### Changed

- Bootstrap .no-gutters class is no longer used, use .g-0  instead.

  For more information see [MDL-81818](https://tracker.moodle.org/browse/MDL-81818)
- The `.page-header-headings` CSS class now has a background colour applied to the maintenance and secure layouts.
  You may need to override this class in your maintenance and secure layouts if both of the following are true:
  * Your theme plugin inherits from `theme_boost` and uses this CSS class
  * Your theme plugin applies a different styling for the page header for the maintenance and secure layouts.

  For more information see [MDL-83047](https://tracker.moodle.org/browse/MDL-83047)

### availability

#### Changed

- The base class `info::get_groups` method has a `$userid` parameter to specify for which user you want to retrieve course groups (defaults to current user)

  For more information see [MDL-81850](https://tracker.moodle.org/browse/MDL-81850)

### core_communication

#### Changed

- The get_enrolled_users_for_course() method now accepts an additional argument that can filter only active enrolments.

  For more information see [MDL-81951](https://tracker.moodle.org/browse/MDL-81951)

### report_log

#### Added

- get_activities_list() function returns also an array of disabled elements, apart from the array of activities.

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)

### core_cache

#### Added

- The following classes have been renamed and now support autoloading.
  Existing classes are currently unaffected.
  | Old class name | New class name |
  | --- | --- |
  | `\cache_definition` | `\core_cache\definition` |
  | `\cache_request` | `\core_cache\request_cache` |
  | `\cache_session` | `\core_cache\session_cache` |
  | `\cache_cached_object` | `\core_cache\cached_object` |
  | `\cache_config` | `\core_cache\config` |
  | `\cache_config_writer` | `\core_cache\config_writer` |
  | `\cache_config_disabled` | `\core_cache\disabled_config` |
  | `\cache_disabled` | `\core_cache\disabled_cache` |
  | `\config_writer` | `\core_cache\config_writer` |
  | `\cache_data_source` | `\core_cache\data_source_interface` |
  | `\cache_data_source_versionable` | `\core_cache\versionable_data_source_interface` |
  | `\cache_exception` | `\core_cache\exception/cache_exception` |
  | `\cache_factory` | `\core_cache\factory` |
  | `\cache_factory_disabled` | `\core_cache\disabled_factory` |
  | `\cache_helper` | `\core_cache\helper` |
  | `\cache_is_key_aware` | `\core_cache\key_aware_cache_interface` |
  | `\cache_is_lockable` | `\core_cache\lockable_cache_interface` |
  | `\cache_is_searchable` | `\core_cache\searchable_cache_interface` |
  | `\cache_is_configurable` | `\core_cache\configurable_cache_interface` |
  | `\cache_loader` | `\core_cache\loader_interface` |
  | `\cache_loader_with_locking` | `\core_cache\loader_with_locking_interface` |
  | `\cache_lock_interface` | `\core_cache\cache_lock_interface` |
  | `\cache_store` | `\core_cache\store` |
  | `\cache_store_interface` | `\core_cache\store_interface` |
  | `\cache_ttl_wrapper` | `\core_cache\ttl_wrapper` |
  | `\cacheable_object` | `\core_cache\cacheable_object_interface` |
  | `\cacheable_object_array` | `\core_cache\cacheable_object_array` |
  | `\cache_definition_mappings_form` | `\core_cache\form/cache_definition_mappings_form` |
  | `\cache_definition_sharing_form` | `\core_cache\form/cache_definition_sharing_form` |
  | `\cache_lock_form` | `\core_cache\form/cache_lock_form` |
  | `\cache_mode_mappings_form` | `\core_cache\form/cache_mode_mappings_form` |

  For more information see [MDL-82158](https://tracker.moodle.org/browse/MDL-82158)

### tool_behat

#### Added

- Behat tests are now checking for deprecated icons. This check can be disabled by using the --no-icon-deprecations option in the behat CLI.

  For more information see [MDL-82212](https://tracker.moodle.org/browse/MDL-82212)

### core_availability

#### Removed

- The previously deprecated renderer `render_core_availability_multiple_messages` method has been removed

  For more information see [MDL-82223](https://tracker.moodle.org/browse/MDL-82223)

### core_filters

#### Added

- Added support for autoloading of filters from `\filter_[filtername]\filter`. Existing classes should be renamed to use the new namespace.

  For more information see [MDL-82427](https://tracker.moodle.org/browse/MDL-82427)

#### Deprecated

- The `filter_manager::text_filtering_hash` method has been finally deprecated and removed.

  For more information see [MDL-82427](https://tracker.moodle.org/browse/MDL-82427)

### mod_bigbluebuttonbn

#### Removed

- Mobile support via plugin has been removed.

  For more information see [MDL-82447](https://tracker.moodle.org/browse/MDL-82447)

#### Added

- Added new meeting_info value to show presentation file on BBB activity page

  For more information see [MDL-82520](https://tracker.moodle.org/browse/MDL-82520)

### customfield_select

#### Changed

- The field controller `get_options` method now returns each option pre-formatted

  For more information see [MDL-82481](https://tracker.moodle.org/browse/MDL-82481)

### core_form

#### Added

- Previously, the 'duration' form field type would allow users to input positive or negative durations. However looking at all the uses, everyone was expecting this input type to only accept times >= 0 seconds, and almost no-one was bothering to write manual form validation, leading to subtle bugs. So now, by default this field type will validate the input value is not negative. If you need the previous behaviour, there is a new option 'allownegative' which you can set to true. (The default is false.)

  For more information see [MDL-82687](https://tracker.moodle.org/browse/MDL-82687)

### core_external

#### Changed

- The external function core_webservice_external::get_site_info now returns the default home page URL when needed.

  For more information see [MDL-82844](https://tracker.moodle.org/browse/MDL-82844)
