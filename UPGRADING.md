# Moodle Upgrade notes

This file contains important information for developers on changes to the Moodle codebase.

More detailed information on key changes can be found in the [Developer update notes](https://moodledev.io/docs/devupdate) for your version of Moodle.

The format of this change log follows the advice given at [Keep a CHANGELOG](https://keepachangelog.com).

## 4.5dev

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

#### Changed

- The class autoloader has been moved to an earlier point in the Moodle bootstrap.
  Autoloaded classes are now available to scripts using the `ABORT_AFTER_CONFIG` constant.

  For more information see [MDL-80275](https://tracker.moodle.org/browse/MDL-80275)
- The `\core\dataformat::get_format_instance` method is now public, and can be used to retrieve a writer instance for a given dataformat

  For more information see [MDL-81781](https://tracker.moodle.org/browse/MDL-81781)

#### Added

- New DML constant `SQL_INT_MAX` to define the size of a large integer with cross database platform support

  For more information see [MDL-81282](https://tracker.moodle.org/browse/MDL-81282)
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

#### Deprecated

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

#### Removed

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

### report

#### Removed

- The previously deprecated `report_helper::save_selected_report` method has been removed and can no longer be used

  For more information see [MDL-72353](https://tracker.moodle.org/browse/MDL-72353)

#### Changed

- The `report_helper::print_report_selector` method accepts an additional argument for adding content to the tertiary navigation to align with the report selector

  For more information see [MDL-78773](https://tracker.moodle.org/browse/MDL-78773)

### report_eventlist

#### Deprecated

- The following deprecated methods in `report_eventlist_list_generator` have been removed:
  - `get_core_events_list()`
  - `get_non_core_event_list()`

  For more information see [MDL-72786](https://tracker.moodle.org/browse/MDL-72786)

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

#### Deprecated

- The `core_grades_renderer::group_selector()` method has been deprecated. Please use `\core_course\output\actionbar\renderer` to render a `group_selector` renderable instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

### core_backup

#### Removed

- Final deprecation and removal of core_backup\copy\copy in backup/util/ui/classes/copy.php. Please use copy_helper from backup/util/helper/copy_helper.class.php instead.

  For more information see [MDL-75022](https://tracker.moodle.org/browse/MDL-75022)

### core_question

#### Added

- A new utility function `format_question_fragment` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

#### Changed

- column_base::from_column_name now has an ignoremissing field, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81125](https://tracker.moodle.org/browse/MDL-81125)

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

### mod_data

#### Added

- The `data_add_record` method accepts a new `$approved` parameter to set the corresponding state of the new record

  For more information see [MDL-81274](https://tracker.moodle.org/browse/MDL-81274)

#### Deprecated

- The `mod_data_renderer::render_fields_footer` method has been deprecated as it's no longer used

  For more information see [MDL-81321](https://tracker.moodle.org/browse/MDL-81321)

### core_message

#### Changed

- The `\core_message\helper::togglecontact_link_params` now accepts a new optional param called `isrequested` to indicate the status of the contact request

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

#### Deprecated

- The `core_message/remove_contact_button` template is deprecated and will be removed in the future version

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

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

### core_course

#### Added

- - New optional sectionNum parameter has been added to activitychooser AMD module initializer. - New option sectionnum parameter has been added to get_course_content_items() external function. - New optional sectionnum parameter has been added to get_content_items_for_user_in_course() function.

  For more information see [MDL-81675](https://tracker.moodle.org/browse/MDL-81675)

#### Deprecated

- The data-sectionid attribute in the activity chooser has been deprecated. Please update your code to use data-sectionnum instead.

  For more information see [MDL-81676](https://tracker.moodle.org/browse/MDL-81676)

#### Changed

- The reset course page has been improved. The words "Delete" and "Remove" have been removed from all the options to make it easier to focus on the data to be removed and avoid inconsistencies and duplicated information. Third party plugins implementing reset methods might need to:
  - Add static element in the _reset_course_form_definition method before all the options with the Delete string:
      `$mform->addElement('static', 'assigndelete', get_string('delete'));`
  - Review all the strings used in the reset page to remove the "Delete" or "Remove" words from them.

  For more information see [MDL-81872](https://tracker.moodle.org/browse/MDL-81872)

### mod_feedback

#### Deprecated

- The method `mod_feedback\output\renderer::create_template_form()` has been deprecated. It is not used anymore.

  For more information see [MDL-81742](https://tracker.moodle.org/browse/MDL-81742)

### core_completion

#### Changed

- get_overall_completion_state() function could also return COMPLETION_COMPLETE_FAIL and not only COMPLETION_COMPLETE and COMPLETION_INCOMPLETE

  For more information see [MDL-81749](https://tracker.moodle.org/browse/MDL-81749)

### core_report

#### Added

- Report has been added to subsystem components list

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)
- New coursestructure output general class has been created

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)

### theme_boost

#### Changed

- Bootstrap .no-gutters class is no longer used, use .g-0  instead.

  For more information see [MDL-81818](https://tracker.moodle.org/browse/MDL-81818)

### availability

#### Changed

- The base class `info::get_groups` method has a `$userid` parameter to specify for which user you want to retrieve course groups (defaults to current user)

  For more information see [MDL-81850](https://tracker.moodle.org/browse/MDL-81850)
