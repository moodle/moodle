# Moodle Upgrade notes

This file contains important information for developers on changes to the Moodle codebase.

More detailed information on key changes can be found in the [Developer update notes](https://moodledev.io/docs/devupdate) for your version of Moodle.

The format of this change log follows the advice given at [Keep a CHANGELOG](https://keepachangelog.com).

## 5.2dev

### core

#### Added

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

#### Changed

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

#### Removed

- The following AMD modules have been removed following the final deprecation process. It is no longer possible or necessary to manually register modal types. See MDL-78324 for further details.

  - `core/modal_registry`
  - `core/modal_factory`

  For more information see [MDL-79182](https://tracker.moodle.org/browse/MDL-79182)
- Removed $CFG->wwwrootendsinpublic flag to force users to configure their server accordingly.

  For more information see [MDL-87072](https://tracker.moodle.org/browse/MDL-87072)

#### Fixed

- `restore_qtype_plugin::unset_excluded_fields` now returns the modified questiondata structure,
  in order to support structures that contain arrays.
  If your qtype plugin overrides `restore_qtype_plugin::remove_excluded_question_data` without
  calling the parent method, you may need to modify your overridden method to use the returned
  value.

  For more information see [MDL-85975](https://tracker.moodle.org/browse/MDL-85975)
- When responding to pcntl signals, call existing signal handlers.

  For more information see [MDL-87079](https://tracker.moodle.org/browse/MDL-87079)

### core_badges

#### Changed

- The create_issued_badge generator now returns the issued badge object.

  For more information see [MDL-85621](https://tracker.moodle.org/browse/MDL-85621)

#### Deprecated

- The class core_badges_assertion has been deprecated and replaced by \core_badges\achievement_credential. The method badges_get_default_issuer() has also been deprecated because it is no longer needed. The file badges/endorsement.php has been removed because it stopped being used when MDL-84323 was integrated.

  For more information see [MDL-85621](https://tracker.moodle.org/browse/MDL-85621)

### core_completion

#### Changed

- The `completion_info::clear_criteria` method takes an optional `$removetypecriteria` to determine whether to remove course type criteria from other courses that refer to the current course

  For more information see [MDL-86332](https://tracker.moodle.org/browse/MDL-86332)

### core_course

#### Added

- The external function `core_course_get_course_contents` now includes the `candisplay` property for each returned module. If this is false, the module should not be displayed on the course page (for example, for question banks).

  For more information see [MDL-85405](https://tracker.moodle.org/browse/MDL-85405)
- Two optional new strings, `modulename_summary` and `modulename_tip`, have been added to modules and will be displayed in the activity chooser interface when defined.

  For more information see [MDL-87117](https://tracker.moodle.org/browse/MDL-87117)

#### Deprecated

- The following methods have been deprecated and should no longer be used: - `course_delete_module` - `course_module_flag_for_async_deletion` Please consider using the equivalent methods, delete and delete_async, in `core_courseformat\local\cmactions` instead.

  For more information see [MDL-86856](https://tracker.moodle.org/browse/MDL-86856)
- Deprecates set_coursemodule_groupmode in favor of core_courseformat\cmactions::set_groupmode

  For more information see [MDL-86857](https://tracker.moodle.org/browse/MDL-86857)
- The `course_set_marker` function has been deprecated and should no longer be used. Please consider using the equivalent methods, `set_marker` or `remove_all_markers`, in `core_courseformat\local\sectionactions` instead.

  For more information see [MDL-86860](https://tracker.moodle.org/browse/MDL-86860)

### core_courseformat

#### Added

- Add delete method to the core_courseformat\cmactions

  For more information see [MDL-86856](https://tracker.moodle.org/browse/MDL-86856)
- Add set_groupmode method to the core_courseformat\cmactions (course format actions)

  For more information see [MDL-86857](https://tracker.moodle.org/browse/MDL-86857)
- Added `set_marker` and `remove_all_markers` methods to the `core_courseformat\sectionactions` class.

  For more information see [MDL-86860](https://tracker.moodle.org/browse/MDL-86860)
- Added the `set_visibility` method to the `core_courseformat\sectionactions` class. To optimize performance, this method does not return the list of affected resources, avoiding unnecessary database queries since the return value is unused.

  For more information see [MDL-86861](https://tracker.moodle.org/browse/MDL-86861)

#### Changed

- The `$cm` attribute in `activityoverviewbase` has been updated to public visibility, allowing direct access to the course module instance

  For more information see [MDL-86660](https://tracker.moodle.org/browse/MDL-86660)
- A new `available` attribute has been added to `activityname_exporter` class. It allows the external API to return the activity's availability status relative to the current user.

  For more information see [MDL-86660](https://tracker.moodle.org/browse/MDL-86660)
- Two new public static methods have been added to the `overviewtable` class: - `is_cm_displayable`: Determines if a course module should be listed in the overview table. - `is_cm_available`: Checks if a course module is accessible to the user (and should therefore be rendered as a link).

  For more information see [MDL-86660](https://tracker.moodle.org/browse/MDL-86660)
- Subsections are now always displayed inline within their respective sections (the separate subsection page is no longer used). Descriptions are no longer shown for delegated sections.

  For more information see [MDL-87276](https://tracker.moodle.org/browse/MDL-87276)

#### Deprecated

- The `set_section_visible` function has been deprecated and should no longer be used. Please consider using the equivalent method, `set_visibility`, in `core_courseformat\local\sectionactions` instead.

  For more information see [MDL-86861](https://tracker.moodle.org/browse/MDL-86861)

### core_grades

#### Removed

- In Moodle 4.2, the legacy Gradebook base widget from 4.1 has been removed and replaced with a simpler class-based system due to a breaking change and excessive complexity in the old pattern. The files `core/grades/basewidget.js` and templates in `grade/templates/searchwidget/` have been deleted, with minimal expected third-party impact.

  For more information see [MDL-78325](https://tracker.moodle.org/browse/MDL-78325)

### core_group

#### Added

- `groups_print_activity_menu()` and `groups_get_activity_group()` now include an additional `$participationonly` parameter, which is true by default. This can be set false when we want the user to be able to select a non-participation group within an activity, for example if a teacher wants to filter assignment submissions by non-participation groups. It should never be used when the menu is displayed to students, as this may allow them to participate using non-participation groups. Non-participation groups are labeled as such.

  For more information see [MDL-81514](https://tracker.moodle.org/browse/MDL-81514)

### core_reportbuilder

#### Added

- The text filter "Contains" and "Not contains" operators now support `*` and `?` wildcard characters for better text content filtering

  For more information see [MDL-84082](https://tracker.moodle.org/browse/MDL-84082)
- The base entity class now implements a default `initialise` method, that will automatically call each of the following methods to load entity report data:

  * `get_available_columns()`
  * `get_available_filters()`
  * `get_available_conditions()`

  This change allows for a lot of boilerplate to be removed from report entity classes

  For more information see [MDL-86678](https://tracker.moodle.org/browse/MDL-86678)
- There are two new entities intended for reports specific to course module data, in order to provide a baseline in terms of module reporting and API usage:

  * `core_course\reportbuilder\local\entities\{course_module,course_module_base}`

  For more information see [MDL-86699](https://tracker.moodle.org/browse/MDL-86699)

#### Deprecated

- The following `user_filter_manager` methods have been deprecated:

  * `reset_all()` - to be replaced by new `reset()` method
  * `reset_single()`
  * `merge()`

  For more information see [MDL-86997](https://tracker.moodle.org/browse/MDL-86997)
- The following enrolment entity formatter methods have been deprecated:

  * `enrolment_status()`
  * `enrolment_values()`

  For more information see [MDL-87000](https://tracker.moodle.org/browse/MDL-87000)

### core_webservice

#### Changed

- The WebService core_webservice_get_site_info now returns three new fields: "usercanviewconfig" indicating whether the current user can see the administration tree, "usercanchangeconfig" indicating whether the current user can change the site configuration, and site secret.

  For more information see [MDL-87034](https://tracker.moodle.org/browse/MDL-87034)

### mod_feedback

#### Deprecated

- The method `feedback_init_feedback_session()` has been deprecated, along with all other direct access to `$SESSION` from the module

  For more information see [MDL-86607](https://tracker.moodle.org/browse/MDL-86607)

### mod_forum

#### Deprecated

- The forum report entity `->get_context_joins()` method is deprecated, replaced with `->get_course_modules_joins(...)`

  For more information see [MDL-86699](https://tracker.moodle.org/browse/MDL-86699)

### mod_glossary

#### Added

- Function mod_glossary_rating_can_see_item_ratings is now implemented for checking permissions to view ratings.

  For more information see [MDL-86960](https://tracker.moodle.org/browse/MDL-86960)

### mod_quiz

#### Changed

- The WebServices mod_quiz_get_user_best_grade and mod_quiz_get_user_quiz_attempts have been updated to return overall feedback even when quiz marks are hidden in the review options. This change aligns the WebService behaviour with Moodle LMS display logic.

  For more information see [MDL-86916](https://tracker.moodle.org/browse/MDL-86916)

### qbank_columnsortorder

#### Removed

- The Behat selector `column move handle` for the `qbank_columnsortorder` plugin has been removed.
  When interacting with the column's move handle, please use the move handle's accessible name and type.
  For example: - `And I drag "Move Created by" "button" and I drop it in "Move T" "button"`

  For more information see [MDL-86855](https://tracker.moodle.org/browse/MDL-86855)

### tool_mobile

#### Changed

- The WS tool_mobile_get_public_config now returns whether MFA and reCAPTCHA are enabled for login/recover password.

  For more information see [MDL-87003](https://tracker.moodle.org/browse/MDL-87003)

## 5.1

### core

#### Added

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

#### Changed

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

#### Deprecated

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

#### Removed

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

### core_admin

#### Added

- - Added `searchmatchtype` property to `admin_settings`
    to track search match type.
  - Plugins that extend either `admin_settings` or `admin_externalpage`
    are encouraged to specify a search match type from the available
    types in `admin_search`.

  For more information see [MDL-85518](https://tracker.moodle.org/browse/MDL-85518)

### core_ai

#### Added

- Error message handler for AI subsystem.
  - Object creation
    Use `core_ai\error\factory::create($errorcode, $reason, $errorsource)` to generate the appropriate error object.

  - Extensibility
    Add new error types by extending `core_ai\error\base` and registering them in the factory.
    Please see `core_ai\error\ratelimit` as an example.

  For more information see [MDL-83147](https://tracker.moodle.org/browse/MDL-83147)
- - Added `get_enabled_actions_in_course_module` method in public/ai/classes/manager.php to get enabled AI actions in course module. - Added `is_ai_tools_enabled_in_course` method in public/ai/classes/manager.php to check if AI tools is enabled in course. - Added `is_action_enabled_in_context` method in public/ai/classes/manager.php to check if an action is enabled in a particular context. - Added `get_ai_fields_from_course_module` method in public/ai/classes/manager.php to get the AI related fields from the course module. - Added `is_html_editor_placement_available` method in public/ai/placement/editor/classes/utils.php to check if editor placement is enabled. - Added `get_actions_available` method in public/ai/placement/editor/classes/utils.php to get available actions for editor placement.

  For more information see [MDL-85738](https://tracker.moodle.org/browse/MDL-85738)

#### Changed

- The method `has_model_settings` inside `core_ai\aimodel\base` is now determined by values returned from a new method called `get_model_settings`.

  For more information see [MDL-84779](https://tracker.moodle.org/browse/MDL-84779)

### core_auth

#### Added

- A new method called `get_additional_upgrade_token_parameters` has been added to `oauth2_client` class. This will allow custom clients to override this one and add their extra parameters for upgrade token request.

  For more information see [MDL-80380](https://tracker.moodle.org/browse/MDL-80380)

### core_badges

#### Added

- The class core_badges_observer in badges/classes/observer.php has been moved to  core_badges\event\observer in badges/classes/event/observer.php. A compatibility  layer has been added to maintain backward compatibility, but direct use of the old  class name is now deprecated. If you've extended or directly used the old class,  you should update your code to use the new namespaced class.

  For more information see [MDL-83904](https://tracker.moodle.org/browse/MDL-83904)
- A number of new static methods have been added to `core_badges\backpack_api` to support the new Canvas Credentials backpack provider. These methods allow you to retrieve lists of providers and regions, check if Canvas Credentials fields should be displayed, and get a region URL or API URL based on a given region ID. The new methods include `get_providers`, `get_regions`, `display_canvas_credentials_fields`, `get_region_url`, `get_region_api_url`, `get_regionid_from_url`, and `is_canvas_credentials_region`.

  For more information see [MDL-86174](https://tracker.moodle.org/browse/MDL-86174)

#### Removed

- Final removal of core_badges_renderer::render_badge_collection() and core_badges_renderer::render_badge_recipients()

  For more information see [MDL-80455](https://tracker.moodle.org/browse/MDL-80455)

### core_block

#### Changed

- Subcontext visibility is now turned on by default when adding blocks. This change makes it much easier to manage blocks, for example, in courses that lack a view page.

  For more information see [MDL-85433](https://tracker.moodle.org/browse/MDL-85433)

#### Removed

- Removed block_section_links from Moodle 5.1.

  For more information see [MDL-80556](https://tracker.moodle.org/browse/MDL-80556)

### core_comment

#### Added

- The following classes have been renamed and now support autoloading.
        Existing classes are currently unaffected.

        | Old class name       | New class name                    |
        | ---                  | ---                               |
        | `\comment`           | `\core_comment\manager`           |
        | `\comment_exception` | `\core_comment\comment_exception` |

  For more information see [MDL-86254](https://tracker.moodle.org/browse/MDL-86254)

#### Deprecated

- The `public/comment/locallib.php` file and the `comment_manager` class have been deprecated. All related functionality should now be accessed via the `\core_comment\manager` class.

  For more information see [MDL-86254](https://tracker.moodle.org/browse/MDL-86254)
- The `public/comment/lib.php` file is now empty and will be removed in Moodle 6.0. Please, do not include in your code anymore.

  For more information see [MDL-86254](https://tracker.moodle.org/browse/MDL-86254)

### core_course

#### Added

- The following classes have been renamed and now support autoloading.
  Existing classes are currently unaffected.

   | Old class name    | New class name                |
   | ---               | ---                           |
   | `\course_request` | `\core_course\course_request` |

  For more information see [MDL-82322](https://tracker.moodle.org/browse/MDL-82322)
- Activities can now specify an additional purpose in their PLUGINNAME_supports function by using the new FEATURE_MOD_OTHERPURPOSE feature.

  For more information see [MDL-85598](https://tracker.moodle.org/browse/MDL-85598)
- Added new `gradable` property to `core_course\local\entity\content_item`

  For more information see [MDL-86036](https://tracker.moodle.org/browse/MDL-86036)
- - The following classes have been renamed and now support autoloading.
    Existing classes are currently unaffected.

    | Old class name    | New class name           |
    | ---               | ---                      |
    | `\cm_info`        | `\course\cm_info
    | `\cached_cm_info` | `\course\cached_cm_info` |
    | `\section_info`   | `\course\section_info`   |
    | `\course_modinfo` | `\course\modinfo`        |

  For more information see [MDL-86155](https://tracker.moodle.org/browse/MDL-86155)
- Removed fictitious `__empty()` magic method.

  The `empty()` method does not make use of any `__empty()` method. It is not a
  defined magic method.

  For more information see [MDL-86155](https://tracker.moodle.org/browse/MDL-86155)

#### Changed

- The 'Show description' checkbox is now present in all course formats. Activity descriptions can be displayed via the Additional activities block (formerly the Main menu block), regardless of whether the course format's has_view_page() function returns false.

  For more information see [MDL-85433](https://tracker.moodle.org/browse/MDL-85433)

#### Deprecated

- The core_course_get_course_content_items is now deprecated. Use core_courseformat_get_section_content_items instead.

  For more information see [MDL-80295](https://tracker.moodle.org/browse/MDL-80295)
- The course_section_add_cm_control course renderer method is deprecated. Use section_add_cm_controls instead.

  For more information see [MDL-80295](https://tracker.moodle.org/browse/MDL-80295)
- Passing the section number (integer) to the core_course\output\activitychooserbutton is deprecated. You must use a core_course\section_info instead.

  For more information see [MDL-80295](https://tracker.moodle.org/browse/MDL-80295)
- The getModulesData and activityModules methods from core_course/local/activitychooser/repository are deprecated. Use getSectionModulesData and sectionActivityModules instead

  For more information see [MDL-80295](https://tracker.moodle.org/browse/MDL-80295)
- The duplicatesection param in course/view.php is deprecated. Use course/format/update.php with action section_duplicate instead.

  For more information see [MDL-84216](https://tracker.moodle.org/browse/MDL-84216)
- The changenumsections.php script is deprecated. Please use course/format/update.php instead.

  For more information see [MDL-85284](https://tracker.moodle.org/browse/MDL-85284)
- The `\course\cm_info::$extra` and `\course\cm_info::$score` properties will now
  emit appropriate debugging.

  These have been deprecated for a long time, but did not emit any debugging.

  For more information see [MDL-86155](https://tracker.moodle.org/browse/MDL-86155)
- The `MAX_MODINFO_CACHE_SIZE` constant has been deprecated and replaced with a class constant.

  For more information see [MDL-86155](https://tracker.moodle.org/browse/MDL-86155)
- The course renderer method course_activitychooser is now deprecated. Its logic is not part of the new section_renderer::add_cm_controls method.

  For more information see [MDL-86337](https://tracker.moodle.org/browse/MDL-86337)

#### Removed

- The activitychoosertabmode setting has been removed. Consider implementing your own setting in your theme if needed.

  For more information see [MDL-85533](https://tracker.moodle.org/browse/MDL-85533)

### core_courseformat

#### Added

- From now on, the activity chooser will use core_courseformat_get_section_content_items to get the available modules for a specific section

  For more information see [MDL-80295](https://tracker.moodle.org/browse/MDL-80295)
- Added new core_courseformat\output\local\overview\overviewdialog output class to create dialog elements in the course overview page. Overview dialog will display a combination of title, description and a list of items (label: value).

  For more information see [MDL-83896](https://tracker.moodle.org/browse/MDL-83896)
- A new interface, `main_activity_interface`, is now available. Course format plugins should implement it if they intend to display only a single activity in the course page.

  For more information see [MDL-85433](https://tracker.moodle.org/browse/MDL-85433)
- The core_courseformat\local\overview\overviewfactory now includes a new method, activity_has_overview_integration, which determines if a module supports overview integration.

  For more information see [MDL-85509](https://tracker.moodle.org/browse/MDL-85509)
- New needs_filtering_by_groups() and get_groups_for_filtering() had been created in activityoverviewbase class for a better management of groups filtering in Activities overview page by activities.   needs_filtering_by_groups() returns whether the user needs to filter by groups in the current module, and get_groups_for_filtering() returns which is the filter the user should use with groups API.

  For more information see [MDL-85852](https://tracker.moodle.org/browse/MDL-85852)
- A new has_error() function has been created in activityoverviewbase class to raise when a user is trying to check information about a module set as SEPARATE_GROUPS but the user is not in any group.

  For more information see [MDL-85852](https://tracker.moodle.org/browse/MDL-85852)
- New optional $nogroupserror parameter has been added to activityname class constructor. A set_nogroupserror() setter to change the value after the constructor has been also added.

  For more information see [MDL-85852](https://tracker.moodle.org/browse/MDL-85852)
- Added new `core_courseformat\output\local\overview\overviewaction` output class to create action buttons that now include a badge right next to the button text. It essentially extends the existing action_link class to add a badge, making important actions stand out more on the course overview. Plus, this new structure also makes these badged action links easier to export this information for web services.

  For more information see [MDL-85981](https://tracker.moodle.org/browse/MDL-85981)
- Add a new modinfo::get_instance_of() to retrieve an instance of a cm via its name and instance id. Add a new modinfo::sort_cm_array() to sort an array of cms in their order of appearance in the course page. Replaces calls to get_course_and_cm_from_instance() and get_instances_of() whenever it was just used to retrieve a single instance of a cm.

  For more information see [MDL-86021](https://tracker.moodle.org/browse/MDL-86021)
- The `core_course\output\activitychooserbutton` has been moved to `core_courseformat\output\local\activitychooserbutton` . From now on, format plugins can provide alternative outputs for this element. Also, all the javascript and templates related to the activity chooser are now located inside the core_courseformat subsystem.

  For more information see [MDL-86337](https://tracker.moodle.org/browse/MDL-86337)
- All activity chooser related code has been moved to the `core_courseformat` subsystem. This includes all templates, javascript, and the main output class. If your theme overrides any of these, you will need to update your code accordingly.

  For more information see [MDL-86337](https://tracker.moodle.org/browse/MDL-86337)

#### Changed

- The param $maxsections of get_num_sections_data in addsection output is not used anymore. If your format overrides this method, you should add a default value 0 to be consistent with the new implementation.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)

#### Deprecated

- The maxsections setting is now considered deprecated and will be removed in Moodle 6.0. Consider implementing your own setting in your format plugin if needed.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)
- The format base method get_max_sections has been deprecated, as the maxsections setting is also deprecated and no longer in use.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)
- The course format "numsections" option to increment and decrement the number of sections of the course one by one is now deprecated and will be removed in Moodle 6.0.

  For more information see [MDL-85284](https://tracker.moodle.org/browse/MDL-85284)

### core_customfield

#### Changed

- Added parameters 'component', 'area' and 'itemid' to the `api::get_instance_fields_data()` and `api::get_instances_fields_data()` methods. Added a new field 'shared' to the customfield_category DB table. Added 'component', 'area' and 'itemid' fields to the customfield_data DB table. Modified the customfield_data DB table unique index to include the new fields.

  For more information see [MDL-86065](https://tracker.moodle.org/browse/MDL-86065)

### core_grades

#### Added

- New 'is_gradable()' function has been created to return whether the item has any gradeitem that is GRADE_TYPE_VALUE or GRADE_TYPE_SCALE.

  For more information see [MDL-85837](https://tracker.moodle.org/browse/MDL-85837)
- - New grade_item::is_gradable function has been created to return whether the grade item is GRADE_TYPE_VALUE or GRADE_TYPE_SCALE.

  For more information see [MDL-86173](https://tracker.moodle.org/browse/MDL-86173)

#### Removed

- The previously deprecate methods have been removed:
    - grade_structure::get_grade_analysis_icon
    - grade_structure::get_reset_icon
    - grade_structure::get_edit_icon
    - grade_structure::get_hiding_icon
    - grade_structure::get_locking_icon
    - grade_structure::get_calculation_icon

  For more information see [MDL-77307](https://tracker.moodle.org/browse/MDL-77307)

### core_message

#### Added

- The web service `core_message_get_member_info` additionally returns `cancreatecontact` which is a boolean value for a user's permission to add a contact.

  For more information see [MDL-72123](https://tracker.moodle.org/browse/MDL-72123)
- The `contexturl` property to `\core\message\message` instances can now contain `\core\url` values in addition to plain strings

  For more information see [MDL-83080](https://tracker.moodle.org/browse/MDL-83080)

### core_question

#### Added

- The question backup API has been improved to only include questions that are actually used or owned by backed up activities.
  Any activities that use question references should be supported automatically. Activities that use *question set references* (for example, random quiz questions) need to add a call to `backup_question_set_reference_trait::annotate_set_reference_bank_entries()` alongside the call to `backup_question_set_reference_trait::add_question_set_references()` in their backup step. See `backup_quiz_activity_structure_step::define_structure()` for an example.

  For more information see [MDL-41924](https://tracker.moodle.org/browse/MDL-41924)

#### Changed

- `core_question_search_shared_banks` will now search all question banks, not just those outside the current course.
  This makes the service usable in cases outside of the current "Switch banks" UI, which require searching all banks on the site.
  It also makes the autocomplete in the "Switch banks" UI more consistent, as it was previously excluding some of the banks listed in the UI (Question banks in this course), but not others (Recently viewed question banks).
  This change has also adds a 'requiredcapabilties' parameter to the function, which accepts an list of abbreviated capabilities for  checking access against question banks before they are returned.

  For more information see [MDL-85069](https://tracker.moodle.org/browse/MDL-85069)
- `question_edit_contexts` now only considers the provided context when checking permissions, rather than all parent contexts as well. As questions now exist only at the activity module context level, permissions can be inherited or overridden as normal for each question bank. The previous pattern of checking for a permission in any parent context circumvented the override system, and no longer makes sense.

  For more information see [MDL-85754](https://tracker.moodle.org/browse/MDL-85754)

#### Deprecated

- Intial deprecation of core_question_bank_renderer::render_question_pagination() and the associated template file. Rendering the question pagination is now done via ajax based pagination.

  For more information see [MDL-78091](https://tracker.moodle.org/browse/MDL-78091)

#### Removed

- Final deprecation of:
    - core_question\local\bank\random_question_loader::get_next_question_id()
    - core_question\local\bank\random_question_loader::get_category_key()
    - core_question\local\bank\random_question_loader::ensure_questions_for_category_loaded()
    - core_question\local\bank\random_question_loader::get_question_ids()
    - core_question\local\bank\random_question_loader::is_question_available()
    - core_question\local\bank\random_question_loader::get_questions()
    - core_question\local\bank\random_question_loader::count_questions()
    - core_question\local\bank\view::display_top_pagnation()
    - core_question\local\bank\view::display_bottom_pagination()
    - question_finder::get_questions_from_categories_with_usage_counts()
    - question_finder::get_questions_from_categories_and_tags_with_usage_counts()

  For more information see [MDL-78091](https://tracker.moodle.org/browse/MDL-78091)

#### Fixed

- The unit test repeated\_restore\_test::test\_restore\_course\_with\_same\_stamp\_questions was passing incorrectly on 5.x for question types that use answers.
  Maintainers of third-party question types may want to re-run the test with the fix in place, or if they have copied parts of this test as the basis of a test in their own plugin, review the changes and see if they should be reflected in their own test.

  For more information see [MDL-85556](https://tracker.moodle.org/browse/MDL-85556)

### core_reportbuilder

#### Added

- The `count[distinct]` aggregation types support optional `'callback'` value to customise the formatted output when applied to columns

  For more information see [MDL-82464](https://tracker.moodle.org/browse/MDL-82464)
- The `report_action` class now accepts a `pix_icon` to include inside the rendered action element

  For more information see [MDL-85216](https://tracker.moodle.org/browse/MDL-85216)
- Report schedule types are now extendable by third-party plugins by extending the `core_reportbuilder\local\schedules\base` class in your component namespace: `<component>\reportbuilder\schedule\<type>`

  For more information see [MDL-86066](https://tracker.moodle.org/browse/MDL-86066)
- The report column class has a new `get_effective_type()` method to determine the returned column type, taking into account applied aggregation method

  For more information see [MDL-86151](https://tracker.moodle.org/browse/MDL-86151)

#### Deprecated

- The following methods from the `schedule` helper class have been deprecated, in favour of usage of the new schedule type system:

  * `create_schedule`
  * `get_report_empty_options`
  * `send_schedule_message`

  For more information see [MDL-86066](https://tracker.moodle.org/browse/MDL-86066)

### core_user

#### Added

- New method `\core_user::get_dummy_fullname(...)` for returning dummy user fullname comprised of configured name fields only

  For more information see [MDL-82132](https://tracker.moodle.org/browse/MDL-82132)

### block_site_main_menu

#### Added

- The "Main menu" block has been renamed to "Additional activities." Its title is now customizable, and it can be used in course formats without a dedicated view page (for instance, Single activity). On the Home page, this block has also been renamed; administrators will need to manually revert the name if they wish to retain "Main menu" after upgrading.

  For more information see [MDL-85392](https://tracker.moodle.org/browse/MDL-85392)

### format_social

#### Deprecated

- The social course format is now disabled by default for all new and upgraded installations. Existing courses using this format will continue to function, but administrators must re-enable it to create new social courses.

  For more information see [MDL-85660](https://tracker.moodle.org/browse/MDL-85660)

### format_topics

#### Added

- Now the custom sections format won't ask for initial sections on the creation form. Instead it will use the system number of sections settings directly.

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)

### format_weeks

#### Added

- The weekly sections format now has a system setting called Maximum initial number of weeks that replaced the old "Max sections" when creating a new course

  For more information see [MDL-84291](https://tracker.moodle.org/browse/MDL-84291)

### gradereport_grader

#### Removed

- The previously deprecated methods have been removed:
    - grade_report_grader::get_left_icons_row
    - grade_report_grader::get_right_icons_row
    - grade_report_grader::get_icons

  For more information see [MDL-77307](https://tracker.moodle.org/browse/MDL-77307)

### gradereport_singleview

#### Added

- The `grade/report/singleview/js/singleview.js` file has been removed. And the `grade/report/singleview/amd/src/singleview.js` file has been added. The new file is converted from YUI to native JS.

  For more information see [MDL-84071](https://tracker.moodle.org/browse/MDL-84071)

### mod_assign

#### Added

- Within mod_assign, time() calls have been changed to use the core clock class; this means Behat and PHPunit tests that mock the time will now work as expected in mod_assign.

  For more information see [MDL-85679](https://tracker.moodle.org/browse/MDL-85679)

### mod_bigbluebuttonbn

#### Added

- Add activity_dates class to BigblueButton module.

  For more information see [MDL-83889](https://tracker.moodle.org/browse/MDL-83889)
- Add a new parameter to the mod_bigbluebuttonbn\recording::get_recordings_for_instance so to ignore instance group settings and return all recordings. This is an optional argement and no change is expected from existing calls.

  For more information see [MDL-86192](https://tracker.moodle.org/browse/MDL-86192)

### mod_book

#### Deprecated

- The \mod_book\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

### mod_choice

#### Added

- Add manager class to the mod_choice activity. For now this is only for the purpose of implementing the activity overview page but can be improved to be used elsewhere.

  For more information see [MDL-83890](https://tracker.moodle.org/browse/MDL-83890)
- Add new generator for choice responses

  For more information see [MDL-83890](https://tracker.moodle.org/browse/MDL-83890)

### mod_data

#### Added

- Database entries generator could create 'approved' entries.

  For more information see [MDL-83891](https://tracker.moodle.org/browse/MDL-83891)
- New get_approval_requested(), get_all_entries(), filter_entries_by_user(), filter_entries_by_approval() and get_comments() functions have been added to mod_data manager class.

  For more information see [MDL-83891](https://tracker.moodle.org/browse/MDL-83891)

### mod_feedback

#### Added

- Two new methods, `feedback_get_completeds` and `feedback_get_completeds_count`, have been added to the feedback API. These methods allow you to retrieve completed items based on multiple groups.

  For more information see [MDL-85850](https://tracker.moodle.org/browse/MDL-85850)

### mod_folder

#### Deprecated

- The \mod_folder\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

### mod_forum

#### Deprecated

- The function forum_tp_get_untracked_forums() has been deprecated because it is no longer used.

  For more information see [MDL-83893](https://tracker.moodle.org/browse/MDL-83893)

### mod_glossary

#### Added

- Added mod_glossary_get_comments(): a method for retrieving comments linked to a glossary.

  For more information see [MDL-85840](https://tracker.moodle.org/browse/MDL-85840)

### mod_h5pactivity

#### Added

- count_attempts() and count_users_attempts() in manager class accept  a new parameter to filter by groups.

  For more information see [MDL-85853](https://tracker.moodle.org/browse/MDL-85853)

### mod_imscp

#### Deprecated

- The \mod_imscp\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

### mod_label

#### Removed

- The dndmedia setting has been removed. From now on dropping a media file into a course will always ask the user if they want to create a label.

  For more information see [MDL-83081](https://tracker.moodle.org/browse/MDL-83081)

### mod_lesson

#### Added

- Added new 'count_all_submissions', 'count_submitted_participants' and 'count_all_participants' functions needed by the overview page.

  For more information see [MDL-83896](https://tracker.moodle.org/browse/MDL-83896)

### mod_page

#### Deprecated

- The \mod_page\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

### mod_qbank

#### Changed

- The bulk_action_base class has gotten a get_bulk_action_classes function to let bulk actions specify additional classes to add to the bulk action menu entry. If none is defined in the child, '' is returned.

  For more information see [MDL-84548](https://tracker.moodle.org/browse/MDL-84548)

### mod_quiz

#### Added

- Add helper methods in the mod/quiz/lib.php to count the number of attempts (quiz_num_attempts), the number of users who attempted a quiz (quiz_num_users_who_attempted) and users who can attempt (quiz_num_users_who_can_attempt)

  For more information see [MDL-83898](https://tracker.moodle.org/browse/MDL-83898)
- Add a groupidlist option to quiz_num_attempt_summary, quiz_num_attempts and quiz_num_users_who_can_attempt to filter those number by groups (the new argument is a list of ids for groups)

  For more information see [MDL-86223](https://tracker.moodle.org/browse/MDL-86223)
- Additional parameter for quiz_num_attempts so we only count users with specified capabilities

  For more information see [MDL-86520](https://tracker.moodle.org/browse/MDL-86520)

#### Deprecated

- Final deprecations for the quiz. The following functions have been removed:
    - quiz_has_question_use
    - quiz_update_sumgrades
    - quiz_update_all_attempt_sumgrades
    - quiz_update_all_final_grades
    - quiz_set_grade
    - quiz_save_best_grade
    - quiz_calculate_best_grade
    - quiz_calculate_best_attempt

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Initial deprecation add_random_form and associates.
  The just removed mod_quiz\form\add_random_form was the only place in core where the mod_quiz/add_random_form javascript was called, so we can deprecate this now. This also enables us to deprecate the mod_quiz/random_question_form_preview javascript and the mod_quiz/random_question_form_preview_question_list template as they are direct dependends.

  For more information see [MDL-78091](https://tracker.moodle.org/browse/MDL-78091)

#### Removed

- Final deprecations for the quiz. The following files have been removed:
    - mod/quiz/accessmanager_form.php
    - mod/quiz/accessmanager.php
    - mod/quiz/accessrule/accessrulebase.php
    - mod/quiz/attemptlib.php
    - mod/quiz/cronlib.php
    - mod/quiz/override_form.php
    - mod/quiz/renderer.php
    - mod/quiz/report/attemptsreport_form.php
    - mod/quiz/report/attemptsreport_options.php
    - mod/quiz/report/attemptsreport_table.php
    - mod/quiz/report/attemptsreport.php
    - mod/quiz/report/default.php

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Final deprecations for the quiz. The following methods have been removed:
     - mod_quiz\output\renderer::no_questions_message
     - mod_quiz\output\renderer::render_mod_quiz_links_to_other_attempts
     - mod_quiz\output\renderer::render_quiz_nav_question_button
     - mod_quiz\output\renderer::render_quiz_nav_section_heading
     - mod_quiz\structure::get_slot_tags_for_slot_id
     - mod_quiz\structure::is_display_number_customised

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Final deprecations for the quiz. The following classes have been removed:
    - mod_quiz_overdue_attempt_updater
    - moodle_quiz_exception

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- The const quiz_statistics\calculator::TIME_TO_CACHE has been removed.

  For more information see [MDL-76612](https://tracker.moodle.org/browse/MDL-76612)
- Final deprecation of:
    - mod_quiz\form\add_random_form::class
    - mod_quiz\local\structure\slot_random::set_tags()
    - mod_quiz\local\structure\slot_random::set_tags_by_id()
    - const quiz_statistics\calculator::TIME_TO_CACHE
    - quiz_add_random_questions()

  For more information see [MDL-78091](https://tracker.moodle.org/browse/MDL-78091)
- Removed the deprecated class callbacks `quiz_structure_modified` and `quiz_attempt_deleted` from mod_quiz, use the `structure_modified` and `attempt_state_changed` hooks instead. These callbacks were deprecated in Moodle 4.4 and were outputting deprecation warnings since then.

  For more information see [MDL-80327](https://tracker.moodle.org/browse/MDL-80327)

### mod_resource

#### Deprecated

- The \mod_resource\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

### mod_scorm

#### Added

- Create a manager class to regroup common functionalities for course overview page

  For more information see [MDL-83899](https://tracker.moodle.org/browse/MDL-83899)
- Add a new generator for scorm attempts to simulate user's attempt.

  For more information see [MDL-83899](https://tracker.moodle.org/browse/MDL-83899)
- Add group id list to \mod_scorm\manager::count_users_who_attempted and \mod_scorm\manager::count_participants so we can filter by groups. Empty array means no filtering.

  For more information see [MDL-86216](https://tracker.moodle.org/browse/MDL-86216)

#### Deprecated

- The method `\mod_scorm\report::generate_master_checkbox()` has been deprecated and should no longer be used. SCORM report plugins calling this method should use `\mod_scorm\report::generate_toggler_checkbox()` instead.

  For more information see [MDL-79756](https://tracker.moodle.org/browse/MDL-79756)

### mod_url

#### Deprecated

- The \mod_url\event\course_module_instance_list_viewed event is now deprecated. Use \core\event\course_resources_list_viewed instead.

  For more information see [MDL-84632](https://tracker.moodle.org/browse/MDL-84632)

### mod_wiki

#### Added

- Create a manager class to regroup common functionalities and a wiki_mode enum related to the two different modes

  For more information see [MDL-83900](https://tracker.moodle.org/browse/MDL-83900)

### mod_workshop

#### Deprecated

- The function `workshop::count_submissions` has been deprecated and should no longer be used, use `workshop::count_all_submissions` instead.

  For more information see [MDL-84809](https://tracker.moodle.org/browse/MDL-84809)
- The function `workshop::count_assessments` has been deprecated and should no longer be used, use `workshop::count_all_assessments` instead.

  For more information see [MDL-84809](https://tracker.moodle.org/browse/MDL-84809)

### qtype_multichoice

#### Changed

- Restrict override of margin-bottom for fitem_id_answer_* and fitem_id_fraction_* divs to own edit form. Question type plugins currently benefitting from the unlimited style override will need to change their styles.css accordingly. An example can be found in calculatedmulti's style sheet.

  For more information see [MDL-85240](https://tracker.moodle.org/browse/MDL-85240)

### report_progress

#### Added

- Add download widget for report to download multiple formats.

  For more information see [MDL-83838](https://tracker.moodle.org/browse/MDL-83838)

#### Changed

- Added a new optional parameter $activegroup to render_groups_select()

  For more information see [MDL-82381](https://tracker.moodle.org/browse/MDL-82381)

#### Deprecated

- `report_progress\output\renderer::render_download_buttons` No replacement. We no longer need to render the download custom button links.

  For more information see [MDL-83838](https://tracker.moodle.org/browse/MDL-83838)

### theme

#### Deprecated

- These icons are no longer in use and have been deprecated:
    - core:e/insert_col_after
    - core:e/insert_col_before
    - core:e/split_cells
    - core:e/text_color
    - core:t/locktime
    - tool_policy/level

  For more information see [MDL-85436](https://tracker.moodle.org/browse/MDL-85436)

### theme_boost

#### Added

- Theme can now inherit from their grand-parent and parents.  So if a child theme inherit from a parent theme that declares a new layout, the child theme can use it without redeclaring it. Also inheritance for layout uses the expected grandparent > parent > child with precedence to the child theme.

  For more information see [MDL-79319](https://tracker.moodle.org/browse/MDL-79319)
- Tables affected by unwanted styling (e.g., borders) from the reset of Bootstrap _reboot.scss styles can now opt out and preserve the original behavior by adding the styleless .table-reboot class.

  For more information see [MDL-86548](https://tracker.moodle.org/browse/MDL-86548)

#### Deprecated

- The `core:e/text_highlight` and `core:e/text_highlight_picker` icons are deprecated and will be removed in Moodle 6.0. The UX team recommended this change to reduce visual clutter and improve readability. The icons were removed because they didn't indicate status changes, were repetitive across all notifications, and took up space that could be used for more content.

  For more information see [MDL-85146](https://tracker.moodle.org/browse/MDL-85146)

### tiny_premium

#### Added

- The `tiny_premium_get_api_key` web service now returns an additional field `usecloud` to indicate whether the cloud version or self-hosted version of Tiny Premium plugins should be used.

  For more information see [MDL-85727](https://tracker.moodle.org/browse/MDL-85727)

## 5.0

### core

#### Added

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

#### Changed

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

#### Deprecated

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

#### Removed

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

#### Fixed

- url class now correctly supports multi level query parameter parsing and output.

  This was previously supported in some methods such as get_query_string, but not others. This has been fixed to be properly supported.

  For example https://example.moodle.net?test[2]=a&test[0]=b will be parsed as ['test' => [2 => 'a', 0 => 'b']]

  All parameter values that are not arrays are casted to strings.

  For more information see [MDL-77293](https://tracker.moodle.org/browse/MDL-77293)

### core_adminpresets

#### Removed

- Remove chat and survey from Adminpresets.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)
- Removed block_mnet_hosts from admin presets

  For more information see [MDL-84309](https://tracker.moodle.org/browse/MDL-84309)

### core_ai

#### Added

- - A new hook, `\core_ai\hook\after_ai_action_settings_form_hook`, has been introduced. It will allows AI provider plugins to add additional form elements for action settings configuration.

  For more information see [MDL-82980](https://tracker.moodle.org/browse/MDL-82980)
- - AI provider plugins that want to implement `pre-defined models` and display additional settings for models must now extend the `\core_ai\aimodel\base` class.

  For more information see [MDL-82980](https://tracker.moodle.org/browse/MDL-82980)

#### Changed

- - The `\core_ai\form\action_settings_form` class has been updated to automatically include action buttons such as Save and Cancel.
  - AI provider plugins should update their form classes by removing the `$this->add_action_buttons();` call, as it is no longer required.

  For more information see [MDL-82980](https://tracker.moodle.org/browse/MDL-82980)

#### Deprecated

- The ai_provider_management_table has been refactored to inherit from flexible_table instead of plugin_management_table. As a result the methods get_plugintype and get_action_url are now unused and have been deprecated in the class.

  For more information see [MDL-82922](https://tracker.moodle.org/browse/MDL-82922)

### core_analytics

#### Removed

- Remove chat and survey from core_analytics.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

### core_auth

#### Removed

- Cas authentication is removed from core and added to the following git repository: https://github.com/moodlehq/moodle-auth_cas

  For more information see [MDL-78778](https://tracker.moodle.org/browse/MDL-78778)
- Removed auth_mnet plugin from core

  For more information see [MDL-84307](https://tracker.moodle.org/browse/MDL-84307)

### core_backup

#### Added

- Added several hooks to the restore process to

   1. Hook to allow extra settings to be defined for the course restore process.
   2. Hook to allow adding extra fields to the copy course form.
   3. Hook used by `copy_helper::process_formdata()` to expand the list of required fields.
   4. Hook used to allow interaction with the copy task, before the actual task execution takes place.

  Other changes include
   1. `base_task::add_setting()` is now public to allow hook callbacks to add settings.
   2. Settings are now added to the data sent to the course_restored event.

  For more information see [MDL-83479](https://tracker.moodle.org/browse/MDL-83479)

#### Removed

- Remove all MODE_HUB related code.

  For more information see [MDL-66873](https://tracker.moodle.org/browse/MDL-66873)

### core_badges

#### Added

- Added fields `courseid` and `coursefullname` to `badgeclass_exporter`, which is used in the return structure of external function `core_badges_get_badge`.

  For more information see [MDL-83026](https://tracker.moodle.org/browse/MDL-83026)
- Added field `coursefullname` to `user_badge_exporter`, which is used in the return structure of external functions `core_badges_get_user_badge_by_hash` and `core_badges_get_user_badges`.

  For more information see [MDL-83026](https://tracker.moodle.org/browse/MDL-83026)
- The class in badges/lib/bakerlib.php has been moved to core_badges\png_metadata_handler. If you've extended or directly used the old bakerlib.php, you'll need to update your code to use the new namespaced class.

  For more information see [MDL-83886](https://tracker.moodle.org/browse/MDL-83886)

#### Removed

- The following previously deprecated renderer methods have been removed:

  * `print_badge_table_actions`
  * `render_badge_management`

  For more information see [MDL-79162](https://tracker.moodle.org/browse/MDL-79162)
- The fields imageauthorname, imageauthoremail, and imageauthorurl have been removed from badges due to confusion and their absence from the official specification. These fields also do not appear in OBv3.0. Additionally, the image_author_json.php file has been removed as it is no longer needed.

  For more information see [MDL-83909](https://tracker.moodle.org/browse/MDL-83909)

### core_block

#### Removed

- Removed block_mnet_hosts plugin from core

  For more information see [MDL-84309](https://tracker.moodle.org/browse/MDL-84309)

### core_calendar

#### Deprecated

- Initial deprecation of calendar_sub_month. Use \core_calendar\type_factory::get_calendar_instance()->get_prev_month() instead.

  For more information see [MDL-79434](https://tracker.moodle.org/browse/MDL-79434)
- calendar_day_representation(), calendar_time_representation() and calendar_format_event_time() functions have been deprecated and can't be used anymore. Use humandate and humantimeperiod classes instead.

  For more information see [MDL-83873](https://tracker.moodle.org/browse/MDL-83873)
- calendar_get_courselink(), calendar_events_by_day() functions have been deprecated.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)
- Initial deprecation of calendar_add_month(). Use \core_calendar\type_factory::get_calendar_instance()->get_next_month() instead.

  For more information see [MDL-84657](https://tracker.moodle.org/browse/MDL-84657)

#### Removed

- Final removal of calendar functions:
    - calendar_top_controls()
    - calendar_get_link_previous()
    - calendar_get_link_next()

  For more information see [MDL-79434](https://tracker.moodle.org/browse/MDL-79434)
- prepare_for_view(), calendar_add_event_metadata() functions have been removed.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)
- core_calendar_renderer::event() method has been removed.

  For more information see [MDL-84617](https://tracker.moodle.org/browse/MDL-84617)

### core_completion

#### Added

- Add hook after_cm_completion_updated triggered when an activity completion is updated.

  For more information see [MDL-83542](https://tracker.moodle.org/browse/MDL-83542)
- The method `count_modules_completed` now delegate the logic to count the completed modules to the DBMS improving the performance of the method.

  For more information see [MDL-83917](https://tracker.moodle.org/browse/MDL-83917)

### core_course

#### Added

- Now the core_courseformat\local\content\cm\completion output is more reusable. All the HTML has been moved to its own mustache file, and the output class has a new set_smallbutton method to decide wether to rendered it as a small button (like in the course page) or as a normal one (for other types of pages).

  For more information see [MDL-83872](https://tracker.moodle.org/browse/MDL-83872)
- New core_course\output\activity_icon class to render activity icons with or without purpose color. This output will centralize the way Moodle renders activity icons

  For more information see [MDL-84555](https://tracker.moodle.org/browse/MDL-84555)

#### Deprecated

- The core_course_edit_module and core_course_edit_section external functions are now deprecated. Use core_courseformat_update_course instead

  For more information see [MDL-82342](https://tracker.moodle.org/browse/MDL-82342)
- The core_course_get_module external function is now deprecated. Use fragment API using component core_courseformat and fragment cmitem instead

  For more information see [MDL-82342](https://tracker.moodle.org/browse/MDL-82342)
- The course_format_ajax_support function is now deprecated. Use course_get_format($course)->supports_ajax() instead.

  For more information see [MDL-82351](https://tracker.moodle.org/browse/MDL-82351)
- course_get_cm_edit_actions is now deprecated. Formats should extend core_courseformat\output\local\content\cm\controlmenu instead.

  For more information see [MDL-83527](https://tracker.moodle.org/browse/MDL-83527)

#### Removed

- Final deprecation of edit_default_completion()

  For more information see [MDL-78711](https://tracker.moodle.org/browse/MDL-78711)
- Final removal of core_course\output\activity_information

  For more information see [MDL-78926](https://tracker.moodle.org/browse/MDL-78926)
- Final deprecation of core_course_renderer\render_activity_information()

  For more information see [MDL-78926](https://tracker.moodle.org/browse/MDL-78926)

### core_courseformat

#### Added

- A new core_courseformat\base::get_generic_section_name method is created to know how a specific format name the sections. This method is also used by plugins to know how to name the sections instead of using using a direct get_string on "sectionnamer" that may not exists.

  For more information see [MDL-82349](https://tracker.moodle.org/browse/MDL-82349)
- A new course/format/update.php url is added as a non-ajax alternative to the core_courseformat_course_update webservice

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- Add core_courseformat\base::invalidate_all_session_caches to reset course editor cache for all users when course is changed. This method can be used as an alternative to core_courseformat\base::session_cache_reset for resetting the cache for the current user  in case the change in the course should be reflected for all users.

  For more information see [MDL-83185](https://tracker.moodle.org/browse/MDL-83185)
- Add after_course_content_updated hook triggered when a course content is updated (module modified, ...) through edition.

  For more information see [MDL-83542](https://tracker.moodle.org/browse/MDL-83542)

#### Changed

- From now on, deleting an activity without Ajax will be consistent with deleting an activity using Ajax. This ensures that all activity deletions will use the recycle bin and avoid code duplication. If your format uses the old non-Ajax method to bypass the recycle bin it won't work anymore as the non-Ajax deletions are now handled in course/format/update.php.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)

#### Deprecated

- The state actions section_move and all related functions are final deprecated and cannot be used anymore. Use the newer section_move_after from now on.

  For more information see [MDL-80116](https://tracker.moodle.org/browse/MDL-80116)
- The core_courseformat::base get_section_number and set_section_number are now final deprecated. Use get_sectionum and set_sectionnum instead.

  For more information see [MDL-80116](https://tracker.moodle.org/browse/MDL-80116)
- All course editing YUI modules are now deprecated. All course formats not using components must migrate before 6.0. Follow the devdocs guide https://moodledev.io/docs/5.0/apis/plugintypes/format/migration to know how to proceed.

  For more information see [MDL-82341](https://tracker.moodle.org/browse/MDL-82341)
- The core_courseformat\base::get_non_ajax_cm_action_url is now deprecated. Use get_update_url instead.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- Many get actions from course/view.php and course/mod.php are now deprecated. Use the new course/format/update.php instead to replace all direct edit urls  in your code. The affected actions are: indent, duplicate, hide, show, stealth, delete, groupmode and marker (highlight). The course/format/updates.php uses the same parameters as the core_courseformat_course_update webservice

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- Deprecate the use of element ID selectors in favor of querySelector for Reactive component initialisation. We will use '#id' instead of 'id' for example.

  For more information see [MDL-83339](https://tracker.moodle.org/browse/MDL-83339)
- The core_courseformat_create_module web service has been deprecated. Please use core_courseformat_new_module as its replacement.

  For more information see [MDL-83469](https://tracker.moodle.org/browse/MDL-83469)
- The state mutation addModule, primarily used for creating mod_subsection instances, has been deprecated. Replace it with newModule. Additionally, all course formats using links with data-action="addModule" must be updated to use data-action="newModule" and include a data-sectionid attribute specifying the target section ID.

  For more information see [MDL-83469](https://tracker.moodle.org/browse/MDL-83469)
- Using arrays to define course menu items is deprecated. All course formats that extend the section or activity control menus (format_NAME\output\courseformat\content\section\controlmenu or format_NAME\output\courseformat\cm\section\controlmenu) should return standard action_menu_link objects instead.

  For more information see [MDL-83527](https://tracker.moodle.org/browse/MDL-83527)
- The externservercourse.php feature used to override the course view page has been deprecated in favor of using hooks. The following hooks are available to do  something similar: \core_course\hook\before_course_viewed.

  For more information see [MDL-83764](https://tracker.moodle.org/browse/MDL-83764)

#### Removed

- Protected function `core_courseformat\output\local\content\section\availability::availability_info()` has been fully removed. Use `core_courseformat\output\local\content\section\availability::get_availability_data()` instead.

  For more information see [MDL-78489](https://tracker.moodle.org/browse/MDL-78489)
- The old UI for moving activities and sections without javascript is not avaiable anymore from the actions dropdown. From now, on the only UI to move activities and sections is using the move action in the course editor. Format plugins can still use the old links to make the "move here" elements appear, but they will show deprecated messages. All the non-ajax moving will be removed in Moodle 6.0.

  For more information see [MDL-83562](https://tracker.moodle.org/browse/MDL-83562)

#### Fixed

- HTML IDs relating to section collapse/expand have been changed in the course format templates.
  - core_courseformat/local/content/section/header #collapssesection{{num}} has been changed to #collapsesectionid{{id}}
  - core_courseformat/local/content/section/content #coursecontentcollapse{{num}} had been changed to #coursecontentcollapseid{{id}}

  For more information see [MDL-82679](https://tracker.moodle.org/browse/MDL-82679)

### core_customfield

#### Added

- Added a new custom field exporter to export custom field data in `\core_customfield\external\field_data_exporter`

  For more information see [MDL-83552](https://tracker.moodle.org/browse/MDL-83552)

### core_enrol

#### Added

- New method enrol_plugin::get_instance_name_for_management_page() can be used to display additional details next to the instance name.

  For more information see [MDL-84139](https://tracker.moodle.org/browse/MDL-84139)
- Plugins implementing enrol_page_hook() method are encouraged to use the renderable \core_enrol\output\enrol_page to produce HTML for the enrolment page. Forms should be displayed in a modal dialogue. See enrol_self plugin as an example.

  For more information see [MDL-84142](https://tracker.moodle.org/browse/MDL-84142)
- It's now possible for themes to override the course enrolment index page by overriding the new course renderer `enrolment_options` method

  For more information see [MDL-84143](https://tracker.moodle.org/browse/MDL-84143)

#### Changed

- The `after_user_enrolled` hook now contains a `roleid` property to allow for listeners to determine which role was assigned during user enrolment (if any)

  The base enrolment `enrol_plugin::send_course_welcome_message_to_user` method also now accepts a `$roleid` parameter in order to correctly populate the `courserole` placeholder

  For more information see [MDL-83432](https://tracker.moodle.org/browse/MDL-83432)

#### Removed

- Final removal of base `enrol_plugin` class method `update_communication`

  For more information see [MDL-80491](https://tracker.moodle.org/browse/MDL-80491)
- Removed enrol_mnet plugin from core

  For more information see [MDL-84310](https://tracker.moodle.org/browse/MDL-84310)

### core_files

#### Added

- A new function `file_clear_draft_area()` has been added to delete the files in a draft area.

  For more information see [MDL-72050](https://tracker.moodle.org/browse/MDL-72050)
- Adds a new ad-hoc task `core_files\task\asynchronous_mimetype_upgrade_task` to upgrade the mimetype of files
  asynchronously during core upgrades. The upgradelib also comes with a new utility function
  `upgrade_create_async_mimetype_upgrade_task` for creating said ad-hoc task.

  For more information see [MDL-81437](https://tracker.moodle.org/browse/MDL-81437)

### core_form

#### Changed

- The `cohort` form element now accepts new `includes` option, which is passed to the corresponding external service to determine which cohorts to return (self, parents, all)

  For more information see [MDL-83641](https://tracker.moodle.org/browse/MDL-83641)

### core_grades

#### Added

- `grade_regrade_final_grades()` now has an additional `async` parameter, which allows full course
  regrades to be performed in the background. This avoids blocking the user for long periods and
  while making changes to a large course. The actual regrade is performed using the
  `\core_course\task\regrade_final_grades` adhoc task, which calls `grade_regrade_final_grades()`
  with `async: false`.

  For more information see [MDL-81714](https://tracker.moodle.org/browse/MDL-81714)

#### Changed

- The `grade_object::fetch_all_helper()` now accepts a new `$sort` parameter with a default value is `id ASC` to sort the grade instances

  For more information see [MDL-85115](https://tracker.moodle.org/browse/MDL-85115)

#### Deprecated

- Deprecate print_graded_users_selector() from Moodle 2 era

  For more information see [MDL-84673](https://tracker.moodle.org/browse/MDL-84673)

#### Removed

- Removed unused grade_edit_tree_column_select class

  For more information see [MDL-77668](https://tracker.moodle.org/browse/MDL-77668)
- The previously deprecated `grade_helper::get_lang_string` method has been removed

  For more information see [MDL-78780](https://tracker.moodle.org/browse/MDL-78780)
- Final deprecation of
    grade_structure::get_element_type_string(),
    grade_structure::get_element_header(),
    grade_structure::get_element_icon(),
    grade_structure::get_activity_link()

  For more information see [MDL-79907](https://tracker.moodle.org/browse/MDL-79907)
- The external function core_grades_get_enrolled_users_for_search_widget has been fully removed.

  For more information see [MDL-84036](https://tracker.moodle.org/browse/MDL-84036)
- The external function core_grades_get_groups_for_search_widget has been fully removed.

  For more information see [MDL-84036](https://tracker.moodle.org/browse/MDL-84036)

### core_mnet

#### Removed

- Remove deprecated mnet_peer::get_public_key()

  For more information see [MDL-78304](https://tracker.moodle.org/browse/MDL-78304)

### core_portfolio

#### Removed

- Removed portfolio_mahara plugin from core

  For more information see [MDL-84308](https://tracker.moodle.org/browse/MDL-84308)

### core_question

#### Added

- The `get_bulk_actions()` method on the base `plugin_features_base` class has been changed to allow a qbank view object to be passed through. This is nullable and therefore optional for qbank plugins which don't need to do so.

  For more information see [MDL-79281](https://tracker.moodle.org/browse/MDL-79281)
- Question bank Condition classes can now implement a function called "filter_invalid_values($filterconditions)" to remove anything from the filterconditions array which is invalid or should not be there.

  For more information see [MDL-83784](https://tracker.moodle.org/browse/MDL-83784)

#### Changed

- question_attempt_step's constructor now accepts the class constant TIMECREATED_ON_FIRST_RENDER as a value for the
  $timecreated parameter. Calling question_attempt::render for the first time will now set the first step's timecreated
  to the current time if it is set to this value. Note, null could not be used here as it is already used to indicate
  timecreated should be set to the current time.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- The definition of the abstract `core_question\local\bank\condition` class has changed to make it clearer which methods are required  in child classes.
  The `get_filter_class` method is no longer declared as abstract, and will return `null` by default to use the base  `core/datafilter/filtertype` class. If you have defined this method to return `null` in your own class, it will continue to work, but it is no longer necessary.
  `build_query_from_filter` and `get_condition_key` are now declared as abstract, since all filter condition classes must define these  (as well as existing abstract methods) to function. Again, exsiting child classes will continue to work if they did before, as they  already needed these methods.

  For more information see [MDL-83859](https://tracker.moodle.org/browse/MDL-83859)

#### Deprecated

- question_type::generate_test

  No replacement, not used anywhere in core.

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- Deprecated method `mod_quiz\question\bank\qbank_helper::get_version_options` in favour of `core_question\local\bank\version_options::get_version_options` so that the method is in core rather than a module, and can safely be used from anywhere as required.

  For more information see [MDL-77713](https://tracker.moodle.org/browse/MDL-77713)
- Behat steps `behat_qbank_comment::i_should_see_on_the_column` and `behat_qbank_comment::i_click_on_the_row_containing` have been deprecated in favour of the new component named selectors, `qbank_comment > Comment count link` and `qbank_comment > Comment count text` which can be used with the standard `should exist` and `I click on` steps to replace the custom steps.

  For more information see [MDL-79122](https://tracker.moodle.org/browse/MDL-79122)

#### Removed

- Final deprecation of:
    - core_question\local\bank\action_column_base::class
    - core_question\local\bank\menu_action_column_base::class
    - core_question\local\bank\menuable_action::interface
    - core_question\local\bank\view::print_choose_category_message()
    - core_question\local\bank\view::display_advanced_search_form()
    - core_question\local\bank\view::display_showtext_checkbox()
    - core_question\local\bank\view::init_search_conditions()
    - core_question\local\bank\view::get_current_category()
    - core_question\local\bank\view::display_options_form()
    - core_question\local\bank\view::start_table()
    - core_question\local\bank\view::end_table()
    - core_question\statistics\questions\all_calculated_for_qubaid_condition::TIME_TO_CACHE
    - core_question\statistics\responses\analyser::TIME_TO_CACHE
    - core_question_bank_renderer::render_category_condition_advanced()
    - core_question_bank_renderer::render_hidden_condition_advanced()
    - core_question_bank_renderer::render_category_condition()

  For more information see [MDL-78090](https://tracker.moodle.org/browse/MDL-78090)

#### Fixed

- Duplication or multiple restores of questions has been modified to avoid errors where a question with the same stamp already exists in the target category.
  To achieve this all data for the question is hashed, excluding any ID fields.

  The question data from the backup is first reformatted to match the questiondata structure returned by calling `get_question_options()` (see  https://docs.moodle.org/dev/Question_data_structures#Representation_1:_%24questiondata). Common question elements will be handled automatically, but any elements that the qtype adds to the backup will need to be handled by overriding `restore_qtype_plugin::convert_backup_to_questiondata`. See `restore_qtype_match_plugin` as an example.
  If a qtype plugin calls any `$this->add_question_*()` methods in its `restore_qtype_*_plugin::define_question_plugin_structure()` method, the ID fields used in these records will be excluded automatically.
  If a qtype plugin defines its own tables with ID fields, it must define `restore_qtype_*_plugin::define_excluded_identity_hash_fields()` to return  an array of paths to these fields within the question data. This should be all that is required for the majority of plugins. See the PHPDoc of `restore_qtype_plugin::define_excluded_identity_hash_fields()` for a full explanation of how these paths should be defined, and  `restore_qtype_truefalse_plugin` for an example.
  If the data structure for a qtype returned by calling `get_question_options()` contains data other than ID fields that are not contained in the backup structure or vice-versa, it will need to override `restore_qtype_*_plugin::remove_excluded_question_data()`  to remove the inconsistent data. See `restore_qtype_multianswer_plugin` as  an example.

  For more information see [MDL-83541](https://tracker.moodle.org/browse/MDL-83541)

### core_reportbuilder

#### Added

- New `report` helper class `get_report_row_count` method for retrieving row count of custom or system report, without having to retrieve the report content

  For more information see [MDL-74488](https://tracker.moodle.org/browse/MDL-74488)
- New `get_deprecated_tables` method in base entity, to be overridden when an entity no longer uses a table (due to column/filter re-factoring, etc) in order to avoid breaking third-party reports

  For more information see [MDL-78118](https://tracker.moodle.org/browse/MDL-78118)
- The base report class, used by both `\core_reportbuilder\system_report` and `\core_reportbuilder\datasource`, contains new methods for enhancing report rendering

  * `set_report_action` allows for an action button to belong to your report, and be rendered alongside the filters button;
  * `set_report_info_container` allows for content to be rendered by your report, between the action buttons and the table content

  For more information see [MDL-82936](https://tracker.moodle.org/browse/MDL-82936)
- The base aggregation class has a new `column_groupby` method, to be implemented in aggregation types to determime whether report tables should group by the fields of the aggregated column

  For more information see [MDL-83361](https://tracker.moodle.org/browse/MDL-83361)
- There is a new `date` aggregation type, that can be applied in custom and system reports

  For more information see [MDL-83361](https://tracker.moodle.org/browse/MDL-83361)
- The `core_reportbuilder_testcase` class has been moved to new autoloaded `core_reportbuilder\tests\core_reportbuilder_testcase` location, affected tests no longer have to manually require `/reportbuilder/tests/helpers.php`

  For more information see [MDL-84000](https://tracker.moodle.org/browse/MDL-84000)
- Columns added to system reports can render help icons in table headers via `[set|get]_help_icon` column instance methods

  For more information see [MDL-84016](https://tracker.moodle.org/browse/MDL-84016)
- The `groupconcat[distinct]` aggregation types support optional `'separator'` value to specify the text to display between aggregated items

  For more information see [MDL-84537](https://tracker.moodle.org/browse/MDL-84537)

#### Changed

- The `get_active_conditions` method of the base report class has a new `$checkavailable` parameter to determine whether to check the returned conditions availability

  For more information see [MDL-82809](https://tracker.moodle.org/browse/MDL-82809)
- When the `select` filter contains upto two options only then the operator field is removed, switching to a simpler value selection field only (this may affect your Behat scenarios)

  For more information see [MDL-82913](https://tracker.moodle.org/browse/MDL-82913)
- Report table instances no longer populate the `countsql` and `countparams` class properties. Instead calling code can access `totalrows` to obtain the same value, or by calling the helper method `report::get_report_row_count`

  For more information see [MDL-83718](https://tracker.moodle.org/browse/MDL-83718)
- For columns implementing custom sorting via their `set_is_sortable` method, the specified sort fields must also be part of the columns initially selected fields

  For more information see [MDL-83718](https://tracker.moodle.org/browse/MDL-83718)
- The `select` filter type is now stricter in it's filtering, in that it will now discard values that aren't present in available filter options

  For more information see [MDL-84213](https://tracker.moodle.org/browse/MDL-84213)
- Aggregation types can access passed options set via the base class constructor in the `$this->options[]` class property. As such, their `format_value` method is no longer static and is always called from an instantiated class instance

  For more information see [MDL-84537](https://tracker.moodle.org/browse/MDL-84537)
- New `$options` argument added to the `column::set_aggregation` method for system reports, to set aggregation type-specific options

  Report entities can call new `column::set_aggregation_options` to achieve the same

  For more information see [MDL-84537](https://tracker.moodle.org/browse/MDL-84537)

#### Deprecated

- The `schedule` helper class `get_schedule_report_count` method is now deprecated, existing code should instead use `report::get_report_row_count`

  For more information see [MDL-74488](https://tracker.moodle.org/browse/MDL-74488)
- The `render_new_report_button` method of the `core_reportbuilder` renderer has been deprecated. Instead, refer to the report instance `set_report_action` method

  For more information see [MDL-82936](https://tracker.moodle.org/browse/MDL-82936)
- Use of the `course_completion` table is deprecated in the `completion` entity, please use `course_completions` instead

  For more information see [MDL-84135](https://tracker.moodle.org/browse/MDL-84135)

#### Removed

- The following deprecated report entity elements have been removed:

  - `comment:context`
  - `comment:contexturl`
  - `enrolment:method` (plus enrolment formatter `enrolment_name` method)
  - `enrolment:role`
  - `file:context`
  - `file:contexturl`
  - `instance:context` (tag)
  - `instance:contexturl` (tag)

  Use of the `context` table is also deprecated in the `file` and `instance` (tag) entities

  For more information see [MDL-78118](https://tracker.moodle.org/browse/MDL-78118)
- Various Oracle-specific support/workarounds in APIs and component report entities have been removed

  For more information see [MDL-80173](https://tracker.moodle.org/browse/MDL-80173)
- Final removal of support for `get_default_table_aliases` method. Entities must now implement `get_default_tables`, which is now abstract, to define the tables they use

  For more information see [MDL-80430](https://tracker.moodle.org/browse/MDL-80430)

### core_repository

#### Removed

- Final removal of base `repository` class method `get_file_size`

  For more information see [MDL-78706](https://tracker.moodle.org/browse/MDL-78706)

### core_sms

#### Added

- Introducing a new function \core_sms\gateway::truncate_message() to truncate SMS message content according to the length limit of the gateway.

  For more information see [MDL-84342](https://tracker.moodle.org/browse/MDL-84342)

### core_tag

#### Changed

- The `core_tag\taglist` class now includes a new property called `displaylink`, which has a default value of `true`. When `displaylink` is set to `true`, the tag name will be displayed as a clickable hyperlink. If `displaylink` is set to `false`, the tag name will be rendered as plain text instead.

  For more information see [MDL-75075](https://tracker.moodle.org/browse/MDL-75075)

### core_user

#### Removed

- Final removal of the following user preference helpers, please use the `core_user/repository` module instead:

  - `user_preference_allow_ajax_update`
  - `M.util.set_user_preference`
  - `lib/ajax/setuserpref.php`

  For more information see [MDL-79124](https://tracker.moodle.org/browse/MDL-79124)

### aiplacement_courseassist

#### Added

- The `aiplacement_courseassist` templates and CSS have been modified. These changes allow for multiple actions to be nested in a dropdown menu.

  For more information see [MDL-82942](https://tracker.moodle.org/browse/MDL-82942)

### block_site_main_menu

#### Removed

- 'Activity' selector in site_main_menu block has been deleted.

  For more information see [MDL-83733](https://tracker.moodle.org/browse/MDL-83733)

### block_social_activities

#### Removed

- 'Activity' selector in social_activities block has been deleted.

  For more information see [MDL-83733](https://tracker.moodle.org/browse/MDL-83733)

### editor_tiny

#### Added

- New external function `editor_tiny_get_configuration`.
  TinyMCE subplugins can provide configuration to the new external function by implementing the `plugin_with_configuration_for_external` interface and/or overriding the `is_enabled_for_external` method.

  For more information see [MDL-84353](https://tracker.moodle.org/browse/MDL-84353)

### enrol_guest

#### Deprecated

- Class enrol_guest_enrol_form is deprecated, use enrol_guest\form\enrol_form instead

  For more information see [MDL-84142](https://tracker.moodle.org/browse/MDL-84142)

### enrol_self

#### Deprecated

- Class enrol_self_enrol_form is deprecated, use enrol_self\form\enrol_form instead

  For more information see [MDL-84142](https://tracker.moodle.org/browse/MDL-84142)

#### Removed

- Final removal of `enrol_self_plugin::get_welcome_email_contact` method, please use `enrol_plugin::get_welcome_message_contact` instead

  For more information see [MDL-81185](https://tracker.moodle.org/browse/MDL-81185)

### format_topics

#### Deprecated

- In format topics, the section controlmenu class deprecates the get_course_url method. This may affect formats extending the topics format and adding extra items to the section menu. Use $this->format->get_update_url instead.

  For more information see [MDL-82767](https://tracker.moodle.org/browse/MDL-82767)
- The get_highlight_control in the section controlmenu class is now deprecated. Use get_section_highlight_item instead

  For more information see [MDL-83527](https://tracker.moodle.org/browse/MDL-83527)

### gradereport

#### Removed

- The previously deprecated `grade_report::get_lang_string` method has been removed

  For more information see [MDL-78780](https://tracker.moodle.org/browse/MDL-78780)

### gradereport_grader

#### Deprecated

- The method `gradereport_grader::get_right_avg_row()` has been finally deprecated and will now throw an exception if called.

  For more information see [MDL-78890](https://tracker.moodle.org/browse/MDL-78890)

#### Removed

- The `behat_gradereport_grader::get_grade_item_id` step helper has been removed, please use the equivalent `behat_grades` method instead

  For more information see [MDL-77107](https://tracker.moodle.org/browse/MDL-77107)

### mlbackend_php

#### Removed

- The plugin `mlbackend_php` has been removed and replaced by `mlbackend_python` as the new default value for the Analytics setting `predictionsprocessor`. The plugin is available at https://github.com/moodlehq/moodle-mlbackend_php.

  For more information see [MDL-84107](https://tracker.moodle.org/browse/MDL-84107)

### mnetservice

#### Deprecated

- The plugintype mnetservice was deprecated. MNet has been deprecated for many years now and will be removed.

  For more information see [MDL-84311](https://tracker.moodle.org/browse/MDL-84311)

### mnetservice_enrol

#### Removed

- Removed mnetservice_enrol plugin from core

  For more information see [MDL-84311](https://tracker.moodle.org/browse/MDL-84311)

### mod

#### Removed

- Remove mod_survey for Moodle 5.0

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)
- Remove mod_chat from Moodle 5.0

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

### mod_assign

#### Added

- Assign sub-plugins have a new method `assign_plugin::settings_validation` which can be overridden to validate the data when the assignments form is saved.

  For more information see [MDL-83440](https://tracker.moodle.org/browse/MDL-83440)
- There is a new method `submission_summary_for_messages()` for submission sub-plugins to summarise what has been submitted for inclusion in confirmation messages to students.

  For more information see [MDL-84387](https://tracker.moodle.org/browse/MDL-84387)
- When the assignment activity sends notifications, it now sets more information in the $a object passed to the languages strings for the message subject and body. This is to give more flexibility to people using Language Customisation. The avaiable information is the same as the template context for the two templates in the next paragraph, but without the messagetext/html.
  Also, the overall structure of these messages is now rendered using templates mod_assign/messages/notification_html and mod_assign/messages/notification_html, so they can be overridden by themes. As a result of this, the methods format_notification_message_text and format_notification_message_html (which should have been private to mod_assign and not used anywhere else) have been removed.

  For more information see [MDL-84733](https://tracker.moodle.org/browse/MDL-84733)

#### Deprecated

- The assign_course_index_summary is now deprecated. The assign index is now generated using the mod_assign\course\overview integration class.

  For more information see [MDL-83888](https://tracker.moodle.org/browse/MDL-83888)

#### Fixed

- The unit test for the privacy provider has been marked as final.

  A number of core tests had been incorrectly configured to extend this test
  but should instead be extending `\mod_assign\tests\provider_testcase`.

  Any community plugins extending the `\mod_assign\privacy\provider_test` test
  class should be updated to extend `\mod_assign\tests\provider_testcase` instead.

  For more information see [MDL-81520](https://tracker.moodle.org/browse/MDL-81520)

### mod_book

#### Deprecated

- The method book_get_nav_classes has been finally
  deprecated and will now throw an exception if called.

  For more information see [MDL-81328](https://tracker.moodle.org/browse/MDL-81328)

### mod_choice

#### Changed

- The WebService `mod_choice_get_choice_results` has a new parameter `groupid` that allows specifying the group to get the results for. The default behaviour hasn't changed: if a choice has groups and the parameter isn't specified the WebService will return the results for the active group.

  For more information see [MDL-78449](https://tracker.moodle.org/browse/MDL-78449)
- The function `choice_get_response_data` has a new parameter that allows specifying the group to get the results for. The default behaviour hasn't changed: if a choice has groups and the parameter isn't used, the function will return the results for the active group.

  For more information see [MDL-78449](https://tracker.moodle.org/browse/MDL-78449)

### mod_data

#### Deprecated

- The following unused capabilities have been deprecated:

  * `mod/data:comment`
  * `mod/data:managecomments`

  For more information see [MDL-84267](https://tracker.moodle.org/browse/MDL-84267)

#### Removed

- Final deprecation and removal of the following classes:
    - data_preset_importer
    - data_preset_existing_importer
    - data_preset_upload_importer
    - data_import_preset_zip_form

  For more information see [MDL-75189](https://tracker.moodle.org/browse/MDL-75189)
- - Final deprecation of \mod_data_renderer::import_setting_mappings(). Please use \mod_data_renderer::importing_preset() instead. - Final deprecation of data_print_template() function. Please use mod_data\manager::get_template and mod_data\template::parse_entries instead. - Final deprecation of data_preset_name(). Please use preset::get_name_from_plugin() instead. - Final deprecation of data_get_available_presets(). Please use manager::get_available_presets() instead. - Final deprecation of data_get_available_site_presets(). Please use manager::get_available_saved_presets() instead. - Final deprecation of data_delete_site_preset(). Please use preset::delete() instead. - Final deprecation of is_directory_a_preset(). Please use preset::is_directory_a_preset() instead. - Final deprecation of data_presets_save(). Please use preset::save() instead. - Final deprecation of data_presets_generate_xml(). Please use preset::generate_preset_xml() instead. - Final deprecation of data_presets_export(). Please use preset::export() instead. - Final deprecation of data_user_can_delete_preset(). Please use preset::can_manage() instead. - Final deprecation of data_view(). Please use mod_data\manager::set_module_viewed() instead.

  For more information see [MDL-75189](https://tracker.moodle.org/browse/MDL-75189)

### mod_feedback

#### Added

- Added new `mod_feedback_questions_reorder` external function

  For more information see [MDL-81745](https://tracker.moodle.org/browse/MDL-81745)

#### Deprecated

- The 'mode' parameter has been deprecated from 'edit_template_action_bar' and 'templates_table' contructors.

  For more information see [MDL-81744](https://tracker.moodle.org/browse/MDL-81744)

#### Removed

- The 'use_template' template has been removed as it is not needed anymore.

  For more information see [MDL-81744](https://tracker.moodle.org/browse/MDL-81744)

### mod_folder

#### Removed

- Method htmllize_tree() has been removed. Please use renderable_tree_elements instead

  For more information see [MDL-79214](https://tracker.moodle.org/browse/MDL-79214)

### mod_h5pactivity

#### Changed

- The external function get_user_attempts now returns the total number of attempts.

  For more information see [MDL-82775](https://tracker.moodle.org/browse/MDL-82775)

### mod_imscp

#### Removed

- Final removal of deprecated `imscp_libxml_disable_entity_loader` function

  For more information see [MDL-78635](https://tracker.moodle.org/browse/MDL-78635)

### mod_lesson

#### Removed

- Remove unused /mod/lesson/tabs.php

  For more information see [MDL-82937](https://tracker.moodle.org/browse/MDL-82937)

### mod_lti

#### Removed

- Final removal of deprecated `lti_libxml_disable_entity_loader` function

  For more information see [MDL-78635](https://tracker.moodle.org/browse/MDL-78635)

### mod_quiz

#### Added

- quiz_attempt now has 2 additional state values, NOT_STARTED and SUBMITTED. These represent attempts when an attempt has been

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- New quiz setting "precreateperiod" controls the period before timeopen during which attempts will be pre-created using the new
  NOT_STARTED state. This setting is marked advanced and locked by default, so can only be set by administrators. This setting
  is read by the \mod_quiz\task\precreate_attempts task to identify quizzes due for pre-creation.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)

#### Changed

- quiz_attempt_save_started now sets the IN_PROGRESS state, timestarted, and saves the attempt, while the new quiz_attempt_save_not_started function sets the NOT_STARTED state and saves the attempt.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- quiz_attempt_save_started Now takes an additional $timenow parameter, to specify the timestart of the attempt. This was previously
  set in quiz_create_attempt, but is now set in quiz_attempt_save_started and quiz_attempt_save_not_started.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- The `quiz_question_tostring` method now includes a new boolean parameter, `displaytaglink`. This parameter specifies whether the tag name in the question bank should be displayed as a clickable hyperlink (`true`) or as plain text (`false`).

  For more information see [MDL-75075](https://tracker.moodle.org/browse/MDL-75075)
- The `\mod_quiz\attempt_walkthrough_from_csv_test` unit test has been marked as final and should not be extended by other tests.

  All shared functionality has been moved to a new autoloadable test-case:
  `\mod_quiz\tests\attempt_walkthrough_testcase`.

  To support this testcase the existing `$files` instance property should be replaced with a new static method, `::get_test_files`.
  Both the existing instance property and the new static method can co-exist.

  For more information see [MDL-81521](https://tracker.moodle.org/browse/MDL-81521)

#### Deprecated

- quiz_attempt::process_finish is now deprecated, and its functionality is split between ::process_submit, which saves the
  submission, sets the finish time and sets the SUBMITTED status, and ::process_grade_submission which performs automated
  grading and sets the FINISHED status.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)
- The webservice function `mod_quiz_get_user_attempts` is now deprecated in favour of `mod_quiz_get_user_quiz_attempts`.

  With the introduction of the new NOT_STARTED quiz attempt state, `mod_quiz_get_user_attempts` has been modified to not return NOT_STARTED attempts, allowing clients such as the mobile app to continue working without modifications.

  `mod_quiz_get_user_quiz_attempts` will return attempts in all states, as `mod_quiz_get_user_attempts` did before. Once clients are updated to handle NOT_STARTED attempts, they can migrate to use this function.

  A minor modification to `mod_quiz_start_attempt` has been made to allow it to transparently start an existing attempt that is in the NOT_STARTED state, rather than creating a new one.

  For more information see [MDL-68806](https://tracker.moodle.org/browse/MDL-68806)

#### Removed

- Final removal of quiz_delete_override() and quiz_delete_all_overrides()

  For more information see [MDL-80944](https://tracker.moodle.org/browse/MDL-80944)

### mod_wiki

#### Removed

- Final deprecation of mod_wiki_renderer\wiki_info()

  For more information see [MDL-78926](https://tracker.moodle.org/browse/MDL-78926)

### qbank

#### Removed

- Final deprecation of:
    - qbank_managecategories\output\renderer::class
    - qbank_statistics\helper::calculate_average_question_discriminative_efficiency()
    - qbank_statistics\helper::calculate_average_question_discrimination_index()
    - qbank_statistics\helper::get_all_places_where_questions_were_attempted()
    - qbank_statistics\helper::calculate_average_question_stats_item()
    - qbank_statistics\helper::calculate_average_question_facility()
    - qbank_statistics\helper::load_statistics_for_place()
    - qbank_statistics\helper::extract_item_value()
    - template qbank_managecategories/category_condition_advanced
    - template qbank_managecategories/category_condition
    - template qbank_managecategories/listitem

  For more information see [MDL-78090](https://tracker.moodle.org/browse/MDL-78090)

### qbank_bulkmove

#### Deprecated

- qbank_bulkmove/helper::get_displaydata

  Superceded by a modal and webservice, see qbank_bulkmove/modal_question_bank_bulkmove and core_question_external\move_questions

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- qbank_bulkmove\output\renderer::render_bulk_move_form

  Superceded by qbank_bulkmove\output\bulk_move

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)

### report_insights

#### Removed

- report_insights_set_notuseful_prediction() external function has been fully removed.

  For more information see [MDL-84036](https://tracker.moodle.org/browse/MDL-84036)
- report_insights_set_fixed_prediction() external function has been fully removed.

  For more information see [MDL-84036](https://tracker.moodle.org/browse/MDL-84036)

### report_log

#### Removed

- Support for the $grouplist public member in the report_log_renderable class has been removed.

  For more information see [MDL-81155](https://tracker.moodle.org/browse/MDL-81155)

### theme_boost

#### Changed

- From now on, themes can customise the activity icon colours using simple CSS variables. The new variables are $activity-icon-administration-bg, $activity-icon-assessment-bg, $activity-icon-collaboration-bg, $activity-icon-communication-bg, $activity-icon-content-bg, $activity-icon-interactivecontent-bg. All previous `$activity-icon-*-filter` elements can be removed, as they are no longer in use.

  For more information see [MDL-83725](https://tracker.moodle.org/browse/MDL-83725)

#### Deprecated

- Added new bs4-compat SCSS file (initially deprecated) to help third-party plugins the migration process from BS4 to BS5

  For more information see [MDL-80519](https://tracker.moodle.org/browse/MDL-80519)
- New `theme_boost/bs4-compat` JS module added (directly deprecated) to allow third-party-plugins to directly convert old Bootstrap 4 data attribute syntax to the new Bootstrap 5

  For more information see [MDL-84450](https://tracker.moodle.org/browse/MDL-84450)

#### Removed

- Remove SCSS deprecated in 4.4

  For more information see [MDL-80156](https://tracker.moodle.org/browse/MDL-80156)
- Remove chat and survey styles. Important note: the styles have been moved to the plugins as CSS files (and not SCSS) so themes might now need to override the mod_chat and mod_survey styles specifically as css does not have any definition for primary, gray and other colors accessible in the original scss version.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

### tool_admin_presets

#### Deprecated

- behat_admin_presets::following_in_the_should_download_between_and_bytes is deprecated. Use: the following element should download a file that:

  For more information see [MDL-83035](https://tracker.moodle.org/browse/MDL-83035)

### tool_behat

#### Added

- New Behat step `\behat_general::the_url_should_match()` has been added to allow checking the current URL. You can use it to check whether a user has been redirected to the expected location.
  e.g. `And the url should match "/mod/forum/view\.php\?id=[0-9]+"`

  For more information see [MDL-83617](https://tracker.moodle.org/browse/MDL-83617)

### tool_brickfield

#### Deprecated

- tool_brickfield\local\areas\core_question\answerbase::find_system_areas

  No replacement. System context no longer a valid context to assign a question category

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)
- tool_brickfield\local\areas\core_question\base::find_system_areas

  No replacement. System context no longer a valid context to assign a question category

  For more information see [MDL-71378](https://tracker.moodle.org/browse/MDL-71378)

#### Removed

- Remove chat and survey support from tool_brickfield.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

### tool_lp

#### Deprecated

- behat_tool_lp_data_generators::the_following_lp_exist is deprecated. Use the following "core_competency > [competency|framework|plan...]" exist:

  For more information see [MDL-82866](https://tracker.moodle.org/browse/MDL-82866)

### tool_mfa

#### Added

- The new factor management table uses `plugin_management_table`, so not only the functions that changed, but the file needs to be moved from `admin/tool/mfa/classes/local/admin_setting_managemfa.php` to `admin/tool/mfa/classes/table/admin_setting_managemfa.php`

  For more information see [MDL-83516](https://tracker.moodle.org/browse/MDL-83516)
- Introduce the new language string `settings:shortdescription`, which is mandatory for each factor.

  For more information see [MDL-83516](https://tracker.moodle.org/browse/MDL-83516)

#### Deprecated

- The two language strings in the tool_mfa plugin, namely `inputrequired` and `setuprequired`, are deprecated.

  For more information see [MDL-83516](https://tracker.moodle.org/browse/MDL-83516)

#### Removed

- The previously deprecated `setup_factor` renderer method has been removed

  For more information see [MDL-80995](https://tracker.moodle.org/browse/MDL-80995)

### tool_mobile

#### Removed

- Remove chat and survey support from tool_mobile.

  For more information see [MDL-82457](https://tracker.moodle.org/browse/MDL-82457)

## 4.5

### core

#### Added

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

#### Changed

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

#### Deprecated

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

#### Removed

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

#### Fixed

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

### core_availability

#### Removed

- The previously deprecated renderer `render_core_availability_multiple_messages` method has been removed.

  For more information see [MDL-82223](https://tracker.moodle.org/browse/MDL-82223)

### core_backup

#### Removed

- The `\core_backup\copy\copy` class has been deprecated and removed. Please use `\copy_helper` instead.

  For more information see [MDL-75022](https://tracker.moodle.org/browse/MDL-75022)
- The following methods in the `\base_controller` class have been removed:

  | Method                          | Replacement                                                     |
  | ---                             | ---                                                             |
  | `\base_controller::set_copy()`  | Use a restore controller for storing copy information instead.  |
  | `\base_controller::get_copy()`  | `\restore_controller::get_copy()`                               |

  For more information see [MDL-75025](https://tracker.moodle.org/browse/MDL-75025)

### core_badges

#### Added

- The following new webservices have been added:

   - `core_badges_enable_badges`

   - `core_badges_disable_badges`

  For more information see [MDL-82168](https://tracker.moodle.org/browse/MDL-82168)

#### Changed

- New fields have been added to the return structure of the `core_badges_get_user_badge_by_hash` and `core_badges_get_user_badges` external functions:
    - `recipientid`: The ID of the user who received the badge.
    - `recipientfullname`: The full name of the user who received the badge.

  For more information see [MDL-82742](https://tracker.moodle.org/browse/MDL-82742)

#### Deprecated

- The `badges/newbadge.php` page has been deprecated and merged with `badges/edit.php`. Please, use `badges/edit.php` instead.

  For more information see [MDL-43938](https://tracker.moodle.org/browse/MDL-43938)
- The `OPEN_BADGES_V1` constant is deprecated and should not be used anymore.

  For more information see [MDL-70983](https://tracker.moodle.org/browse/MDL-70983)
- The `course_badges` systemreport has been deprecated and merged with the badges systemreport. Please, use the badges systemreport instead.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)
- The `$showmanage` parameter to the `\core_badges\output\standard_action_bar` constructor has been deprecated and should not be used anymore.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)
- The `badges/view.php` page has been deprecated and merged with `badges/index.php`. Please, use `badges/index.php` instead.

  For more information see [MDL-82503](https://tracker.moodle.org/browse/MDL-82503)

#### Removed

- Final removal of `BADGE_BACKPACKAPIURL` and `BADGE_BACKPACKWEBURL` constants.

  For more information see [MDL-70983](https://tracker.moodle.org/browse/MDL-70983)

### core_cache

#### Added

- The following classes have been renamed and now support autoloading.

  Existing classes are currently unaffected.

  | Old class name                     | New class name                                     |
  | ---                                | ---                                                |
  | `\cache_definition`                | `\core_cache\definition`                           |
  | `\cache_request`                   | `\core_cache\request_cache`                        |
  | `\cache_session`                   | `\core_cache\session_cache`                        |
  | `\cache_cached_object`             | `\core_cache\cached_object`                        |
  | `\cache_config`                    | `\core_cache\config`                               |
  | `\cache_config_writer`             | `\core_cache\config_writer`                        |
  | `\cache_config_disabled`           | `\core_cache\disabled_config`                      |
  | `\cache_disabled`                  | `\core_cache\disabled_cache`                       |
  | `\config_writer`                   | `\core_cache\config_writer`                        |
  | `\cache_data_source`               | `\core_cache\data_source_interface`                |
  | `\cache_data_source_versionable`   | `\core_cache\versionable_data_source_interface`    |
  | `\cache_exception`                 | `\core_cache\exception/cache_exception`            |
  | `\cache_factory`                   | `\core_cache\factory`                              |
  | `\cache_factory_disabled`          | `\core_cache\disabled_factory`                     |
  | `\cache_helper`                    | `\core_cache\helper`                               |
  | `\cache_is_key_aware`              | `\core_cache\key_aware_cache_interface`            |
  | `\cache_is_lockable`               | `\core_cache\lockable_cache_interface`             |
  | `\cache_is_searchable`             | `\core_cache\searchable_cache_interface`           |
  | `\cache_is_configurable`           | `\core_cache\configurable_cache_interface`         |
  | `\cache_loader`                    | `\core_cache\loader_interface`                     |
  | `\cache_loader_with_locking`       | `\core_cache\loader_with_locking_interface`        |
  | `\cache_lock_interface`            | `\core_cache\cache_lock_interface`                 |
  | `\cache_store`                     | `\core_cache\store`                                |
  | `\cache_store_interface`           | `\core_cache\store_interface`                      |
  | `\cache_ttl_wrapper`               | `\core_cache\ttl_wrapper`                          |
  | `\cacheable_object`                | `\core_cache\cacheable_object_interface`           |
  | `\cacheable_object_array`          | `\core_cache\cacheable_object_array`               |
  | `\cache_definition_mappings_form`  | `\core_cache\form/cache_definition_mappings_form`  |
  | `\cache_definition_sharing_form`   | `\core_cache\form/cache_definition_sharing_form`   |
  | `\cache_lock_form`                 | `\core_cache\form/cache_lock_form`                 |
  | `\cache_mode_mappings_form`        | `\core_cache\form/cache_mode_mappings_form`        |

  For more information see [MDL-82158](https://tracker.moodle.org/browse/MDL-82158)

### core_communication

#### Changed

- The `\core_communication\helper::get_enrolled_users_for_course()` method now accepts an additional argument that can filter only active enrolments.

  For more information see [MDL-81951](https://tracker.moodle.org/browse/MDL-81951)

### core_completion

#### Added

- A new `FEATURE_COMPLETION` plugin support constant has been added. In the future, this constant will be used to indicate when a plugin does not allow completion and it is enabled by default.

  For more information see [MDL-83008](https://tracker.moodle.org/browse/MDL-83008)

#### Changed

- The `\core_completion\activity_custom_completion::get_overall_completion_state()` method can now also return `COMPLETION_COMPLETE_FAIL` and not only `COMPLETION_COMPLETE` and `COMPLETION_INCOMPLETE`.

  For more information see [MDL-81749](https://tracker.moodle.org/browse/MDL-81749)

### core_course

#### Added

- - New optional `sectionNum` parameter has been added to `activitychooser` AMD module initializer.
  - New option `sectionnum` parameter has been added to `get_course_content_items()` external function.
  - New optional `sectionnum` parameter has been added to `get_content_items_for_user_in_course()` function.

  For more information see [MDL-81675](https://tracker.moodle.org/browse/MDL-81675)
- The `core_course_get_courses_by_field` web service now accepts a new parameter `sectionid` to be able to retrieve the course that has the indicated section.

  For more information see [MDL-81699](https://tracker.moodle.org/browse/MDL-81699)
- Added new `activitychooserbutton` output class to display the activitychooser button. New `action_links` can be added to the button via hooks converting it into a dropdown.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- New `\core_course\hook\before_activitychooserbutton_exported` hook added to allow third-party plugins to extend activity chooser button options.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- The following methods have been updated to accept a section name in addition to the section number:
  - `\behat_course::i_open_section_edit_menu()`
  - `\behat_course::i_show_section()`
  - `\behat_course::i_hide_section(),`
  - `\behat_course::i_wait_until_section_is_available()`
  - `\behat_course::show_section_link_exists()`
  - `\behat_course::hide_section_link_exists()`
  - `\behat_course::section_exists()`

  For more information see [MDL-82259](https://tracker.moodle.org/browse/MDL-82259)

#### Changed

- The reset course page has been improved. The words "Delete" and "Remove" have been removed from all the options to make it easier to focus on the data to be removed and avoid inconsistencies and duplicated information. Third party plugins implementing reset methods might need to:
  - Add static element in the _reset_course_form_definition method before all the options with the Delete string:
      `$mform->addElement('static', 'assigndelete', get_string('delete'));`
  - Review all the strings used in the reset page to remove the "Delete" or "Remove" words from them.

  For more information see [MDL-81872](https://tracker.moodle.org/browse/MDL-81872)
- The external function `core_course_get_contents` now returns the `component` and `itemid` of sections.

  For more information see [MDL-82385](https://tracker.moodle.org/browse/MDL-82385)

#### Deprecated

- The `data-sectionid` attribute in the activity chooser has been deprecated. Please update your code to use `data-sectionnum` instead.

  For more information see [MDL-81676](https://tracker.moodle.org/browse/MDL-81676)
- The `$course` parameter in the constructor of the `\core_course\output\actionbar\group_selector` class has been deprecated and is no longer used.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)

#### Removed

- The previously deprecated `\print_course_request_buttons()` method has been removed and can no longer be used.

  For more information see [MDL-73976](https://tracker.moodle.org/browse/MDL-73976)
- The `$course` class property in the `\core_course\output\actionbar\group_selector` class has been removed.

  For more information see [MDL-82393](https://tracker.moodle.org/browse/MDL-82393)

### core_courseformat

#### Added

- The constructor of `\core_courseformat\output\local\state\cm` has been updated to accept a new optional parameter, `$istrackeduser`.
  If `istrackeduser` is pre-computed for the course module's course, it can be provided here to avoid an additional function call.

  For more information see [MDL-81610](https://tracker.moodle.org/browse/MDL-81610)
- Added new `core_courseformat_create_module` webservice to create new module (with quickcreate feature) instances in the course.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)
- A new `$disabled` parameter has been added to the following `html_writer` methods:

  - `\core\output\html_writer::select()`
  - `\core\output\html_writer::select_optgroup()`
  - `\core\output\html_writer::select_option()`

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)
- A new class, `\core_courseformat\output\local\content\basecontrolmenu`, has been created.
  The following existing classes extend the new class:

   - `\core_courseformat\output\local\content\cm\controlmenu`
   - `\core_courseformat\output\local\content\section\controlmenu`

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)
- Course sections now use an action menu to display possible actions that a user may take in each section. This action menu is rendered using the `\core_courseformat\output\local\content\cm\delegatedcontrolmenu` renderable class.

  For more information see [MDL-82510](https://tracker.moodle.org/browse/MDL-82510)

### core_customfield

#### Changed

- The field controller `\core_customfield\field_controller::get_formatted_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name.

  For more information see [MDL-82488](https://tracker.moodle.org/browse/MDL-82488)

### core_external

#### Changed

- The external function `core_webservice_external::get_site_info` now returns the default home page URL when needed.

  For more information see [MDL-82844](https://tracker.moodle.org/browse/MDL-82844)

### core_files

#### Added

- A new hook, `\core_files\hook\after_file_created`, has been created to allow the inspection of files after they have been saved in the filesystem.

  For more information see [MDL-75850](https://tracker.moodle.org/browse/MDL-75850)
- A new hook, `\core_files\hook\before_file_created`, has been created to allow modification of a file immediately before it is stored in the file system.

  For more information see [MDL-83245](https://tracker.moodle.org/browse/MDL-83245)

### core_filters

#### Added

- Added support for autoloading of filters from `\filter_[filtername]\filter`. Existing classes should be renamed to use the new namespace.

  For more information see [MDL-82427](https://tracker.moodle.org/browse/MDL-82427)

#### Deprecated

- The `\core_filters\filter_manager::text_filtering_hash` method has been finally deprecated and removed.

  For more information see [MDL-82427](https://tracker.moodle.org/browse/MDL-82427)

### core_form

#### Added

- The `duration` form field type has been modified to validate that the supplied value is a positive value.
  Previously it could be any numeric value, but every usage of this field in Moodle was expecting a positive value. When a negative value was provided and accepted, subtle bugs could occur.
  Where a negative duration _is_ allowed, the `allownegative` attribute can be set to `true`.

  For more information see [MDL-82687](https://tracker.moodle.org/browse/MDL-82687)

### core_grades

#### Changed

- The grade `itemname` property contained in the return structure of the following external methods is now PARAM_RAW:
    - `core_grades_get_gradeitems`
    - `gradereport_user_get_grade_items`

  For more information see [MDL-80017](https://tracker.moodle.org/browse/MDL-80017)

#### Deprecated

- The behat step definition `\behat_grade::i_confirm_in_search_within_the_gradebook_widget_exists()` has been deprecated. Please use `\behat_general::i_confirm_in_search_combobox_exists()` instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The behat step definition `\behat_grade::i_confirm_in_search_within_the_gradebook_widget_does_not_exist()` has been deprecated. Please use `\behat_general::i_confirm_in_search_combobox_does_not_exist()` instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The behat step definition `\behat_grade::i_click_on_in_search_widget()` has been deprecated. Please use `\behat_general::i_click_on_in_search_combobox()` instead.

  For more information see [MDL-80744](https://tracker.moodle.org/browse/MDL-80744)
- The `\core_grades_renderer::group_selector()` method has been deprecated. Please use `\core_course\output\actionbar\renderer` to render a `group_selector` renderable instead.

  For more information see [MDL-80745](https://tracker.moodle.org/browse/MDL-80745)

#### Removed

- The following previously deprecated Behat step helper methods have been removed and can no longer be used:
   - `\behat_grade::select_in_gradebook_navigation_selector()`
   - `\behat_grade::select_in_gradebook_tabs()`

  For more information see [MDL-74581](https://tracker.moodle.org/browse/MDL-74581)

### core_message

#### Changed

- The `\core_message\helper::togglecontact_link_params()` method now accepts a new optional `$isrequested` parameter to indicate the status of the contact request.

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

#### Deprecated

- The `core_message/remove_contact_button` template is deprecated and will be removed in a future release.

  For more information see [MDL-81428](https://tracker.moodle.org/browse/MDL-81428)

#### Removed

- Final deprecation of the `MESSAGE_DEFAULT_LOGGEDOFF`, and `MESSAGE_DEFAULT_LOGGEDIN` constants.

  For more information see [MDL-73284](https://tracker.moodle.org/browse/MDL-73284)

### core_question

#### Added

- A new utility function `\question_utils::format_question_fragment()` has been created so that question content can filter based on filters.

  For more information see [MDL-78662](https://tracker.moodle.org/browse/MDL-78662)

#### Changed

- `\core_question\local\bank\column_base::from_column_name()` method now accepts a `bool $ingoremissing` parameter, which can be used to ignore if the class does not exist, instead of throwing an exception.

  For more information see [MDL-81407](https://tracker.moodle.org/browse/MDL-81407)

### core_report

#### Added

- Report has been added to subsystem components list.

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)
- A new general output class, `\core_report\output\coursestructure`, has been created.

  For more information see [MDL-81771](https://tracker.moodle.org/browse/MDL-81771)

#### Changed

- The `\core\report_helper::print_report_selector()` method accepts a new `$additional`` argument for adding content to the tertiary navigation to align with the report selector.

  For more information see [MDL-78773](https://tracker.moodle.org/browse/MDL-78773)

#### Removed

- The previously deprecated `\core\report_helper::save_selected_report()` method has been removed and can no longer be used.

  For more information see [MDL-72353](https://tracker.moodle.org/browse/MDL-72353)

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
- Added a new database helper method `sql_replace_parameters` to help ensure uniqueness of parameters within a SQL expression.

  For more information see [MDL-81434](https://tracker.moodle.org/browse/MDL-81434)
- A new static method, `\core_reportbuilder\local\helpers\format::format_time()`, has been added for use in column callbacks that represent a duration of time (for example "3 days 4 hours").

  For more information see [MDL-82466](https://tracker.moodle.org/browse/MDL-82466)
- The following methods have been moved from `\core_reportbuilder\datasource` class to its parent class `\core_reportbuilder\base` to make them available for use in system reports:

    - `add_columns_from_entity()`
    - `add_filters_from_entity()`
    - `report_element_search()`

  For more information see [MDL-82529](https://tracker.moodle.org/browse/MDL-82529)

#### Changed

- In order to better support float values in filter forms, the following filter types now cast given SQL prior to comparison:

    - `duration`
    - `filesize`
    - `number`

  For more information see [MDL-81168](https://tracker.moodle.org/browse/MDL-81168)
- The base datasource `\core_reportbuilder\datasource::add_all_from_entities()` method accepts a new optional `array $entitynames` parameter to specify which entities to add elements from.

  For more information see [MDL-81330](https://tracker.moodle.org/browse/MDL-81330)
- All time-related code has been updated to the PSR-20 Clock interface, as such the following methods no longer accept a `$timenow` parameter (instead please use `\core\clock` dependency injection):
  - `core_reportbuilder_generator::create_schedule`
  - `core_reportbuilder\local\helpers\schedule::create_schedule()`
  - `core_reportbuilder\local\helpers\schedule::calculate_next_send_time()`

  For more information see [MDL-82041](https://tracker.moodle.org/browse/MDL-82041)
- The following classes have been moved to use the new exception API as a L2 namespace:

  | Old class                                           | New class                                                     |
  | -----------                                         | -----------                                                   |
  | `\core_reportbuilder\report_access_exception`       | `\core_reportbuilder\exception\report_access_exception`       |
  | `\core_reportbuilder\source_invalid_exception`      | `\core_reportbuilder\exception\source_invalid_exception`      |
  | `\core_reportbuilder\source_unavailable_exception`  | `\core_reportbuilder\exception\source_unavailable_exception`  |

  For more information see [MDL-82133](https://tracker.moodle.org/browse/MDL-82133)

#### Removed

- Support for the following entity classes, renamed since 4.1, have now been removed completely:

  - `\core_admin\local\entities\task_log`
  - `\core_cohort\local\entities\cohort`
  - `\core_cohort\local\entities\cohort_member`
  - `\core_course\local\entities\course_category`
  - `\report_configlog\local\entities\config_change`

  For more information see [MDL-74583](https://tracker.moodle.org/browse/MDL-74583)
- The following previously deprecated local helper methods have been removed and can no longer be used:
    - `\core_reportbuilder\local\helpers\audience::get_all_audiences_menu_types()`
    - `\core_reportbuilder\local\helpers\report::get_available_columns()`

  For more information see [MDL-76690](https://tracker.moodle.org/browse/MDL-76690)

### core_role

#### Added

- All session management has been moved to the `\core\session\manager` class.
  This removes the dependancy to use the `sessions` table.

  Session management plugins (like Redis) should now inherit
  the base `\core\session\handler` class, which implements
  `SessionHandlerInterface`, and override methods as required.

  The following methods in `\core\session\manager` have been deprecated:
  | Old method name                  | New method name           |
  | ---                              | ---                       |
  | `kill_all_sessions`              | `destroy_all`             |
  | `kill_session`                   | `destroy`                 |
  | `kill_sessions_for_auth_plugin`  | `destroy_by_auth_plugin`  |
  | `kill_user_sessions`             | `destroy_user_sessions`   |

  For more information see [MDL-66151](https://tracker.moodle.org/browse/MDL-66151)

### core_sms

#### Added

- A new `\core_sms` subsystem has been created.

  For more information see [MDL-81924](https://tracker.moodle.org/browse/MDL-81924)

### core_table

#### Added

- A new `$reponsive` property (defaulting to `true`) has been added to the `\core_table\flexible_table` class.
  This property allows you to control whether the table is rendered as a responsive table.

  For more information see [MDL-80748](https://tracker.moodle.org/browse/MDL-80748)

#### Changed

- The `\core_table\dynamic` class declares a new method `::has_capability()` to allow classes implementing this interface to perform access checks on the dynamic table.
  Note: This is a breaking change. All implementations of the `\core_table\dynamic` table interface _must_ implement the new `has_capability(): bool` method for continued functionality.

  For more information see [MDL-82567](https://tracker.moodle.org/browse/MDL-82567)

### core_user

#### Added

- New `\core_user\hook\extend_user_menu` hook added to allow third party plugins to extend the user menu navigation.

  For more information see [MDL-71823](https://tracker.moodle.org/browse/MDL-71823)
- A new hook, `\core_user\hook\extend_default_homepage`, has been added to allow third-party plugins to extend the default homepage options for the site.

  For more information see [MDL-82066](https://tracker.moodle.org/browse/MDL-82066)

#### Changed

- The visibility of the following methods have been increased to public:
  - `\core_user\form\private_files::check_access_for_dynamic_submission()`
  - `\core_user\form\private_files::get_options()`

  For more information see [MDL-78293](https://tracker.moodle.org/browse/MDL-78293)
- The user profile field `\profile_field_base::display_name()` method now accepts an optional `$escape` parameter to define whether to escape the returned name.

  For more information see [MDL-82494](https://tracker.moodle.org/browse/MDL-82494)

#### Deprecated

- The `\core_user\table\participants_search::get_total_participants_count()` is no longer used since the total count can be obtained from `\core_user\table\participants_search::get_participants()`.

  For more information see [MDL-78030](https://tracker.moodle.org/browse/MDL-78030)

### availability

#### Changed

- The base class `\core_availability\info::get_groups()` method now accepts a `$userid` parameter to specify which user you want to retrieve course groups (defaults to current user).

  For more information see [MDL-81850](https://tracker.moodle.org/browse/MDL-81850)

### customfield_number

#### Added

- A new hook, `\customfield_number\hook\add_custom_providers`, has been added which allows automatic calculation of number course custom field.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
- A new class, `\customfield_number\local\numberproviders\nofactivities`, has been added that allows to automatically calculate number of activities of a given type in a given course.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
- Added new webservice `customfield_number_recalculate_value`, has been added to recalculate a value of number course custom field.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)
- A new task, `\customfield_number\task\cron`, cron task that recalculates automatically calculated number course custom fields.

  For more information see [MDL-82715](https://tracker.moodle.org/browse/MDL-82715)

### customfield_select

#### Changed

- The field controller `get_options` method now returns each option pre-formatted.

  For more information see [MDL-82481](https://tracker.moodle.org/browse/MDL-82481)

### editor_tiny

#### Changed

- The `helplinktext` language string is no longer required by editor plugins, instead the `pluginname` will be used in the help dialogue.

  For more information see [MDL-81572](https://tracker.moodle.org/browse/MDL-81572)

### factor_sms

#### Removed

- The following classes are removed as the SMS feature now takes advantage of `core_sms` API:
  - `\factor_sms\event\sms_sent`
  - `\factor_sms\local\smsgateway\aws_sns`
  - `\factor_sms\local\smsgateway\gateway_interface`

  For more information see [MDL-80962](https://tracker.moodle.org/browse/MDL-80962)

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

### mod

#### Added

- Added new `FEATURE_QUICKCREATE` for modules that can be quickly created in the course wihout filling a previous form.

  For more information see [MDL-81767](https://tracker.moodle.org/browse/MDL-81767)

### mod_assign

#### Added

- Added 2 new settings:
    - `mod_assign/defaultgradetype`
      - The value of this setting dictates which of the `GRADE_TYPE_X` constants is the default option when creating new instances of the assignment.
      - The default value is `GRADE_TYPE_VALUE` (Point)
    - `mod_assign/defaultgradescale`
      - The value of this setting dictates which of the existing scales is the default option when creating new instances of the assignment.

  For more information see [MDL-54105](https://tracker.moodle.org/browse/MDL-54105)
- A new web service called `mod_assign_remove_submission` has been created to remove the submission for a specific user ID and assignment activity ID.

  For more information see [MDL-74050](https://tracker.moodle.org/browse/MDL-74050)
- A new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- A new method, `\assign_feedback_plugin::get_grading_batch_operation_details()`, has been added to the `assign_feedback_plugin` abstract class. Assignment feedback plugins can now override this method to define bulk action buttons that will appear in the sticky footer on the assignment grading page.

  For more information see [MDL-80750](https://tracker.moodle.org/browse/MDL-80750)

#### Deprecated

- The constant `ASSIGN_ATTEMPT_REOPEN_METHOD_NONE` has been deprecated, and a new default value for `attemptreopenmethod` has been set to "Automatically until pass".

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- The `\assign_feedback_plugin::get_grading_batch_operations()` method is now deprecated. Use `assign_feedback_plugin::get_grading_batch_operation_details` instead.

  For more information see [MDL-80750](https://tracker.moodle.org/browse/MDL-80750)
- The `\assign_grading_table::plugingradingbatchoperations` property has been removed. You can use `\assign_feedback_plugin::get_grading_batch_operation_details()` instead.

  For more information see [MDL-80750](https://tracker.moodle.org/browse/MDL-80750)
- The `$submissionpluginenabled` and `$submissioncount` parameters from the constructor of the `\mod_assign\output::grading_actionmenu` class have been deprecated.

  For more information see [MDL-80752](https://tracker.moodle.org/browse/MDL-80752)
- The method `\assign::process_save_grading_options()` has been deprecated as it is no longer used.

  For more information see [MDL-82681](https://tracker.moodle.org/browse/MDL-82681)

#### Removed

- The default option "Never" for the `attemptreopenmethod` setting, which disallowed multiple attempts at the assignment, has been removed. This option was unnecessary because limiting attempts to 1 through the `maxattempts` setting achieves the same behavior.

  For more information see [MDL-80741](https://tracker.moodle.org/browse/MDL-80741)
- The `\mod_assign_grading_options_form` class has been removed since it is no longer used.

  For more information see [MDL-82857](https://tracker.moodle.org/browse/MDL-82857)

### mod_bigbluebuttonbn

#### Added

- Added new `meeting_info` value to show presentation file on BBB activity page

  For more information see [MDL-82520](https://tracker.moodle.org/browse/MDL-82520)
- The `broker::process_meeting_events()` method has been extended to call the `::process_action()` method implemented by plugins.

  For more information see [MDL-82872](https://tracker.moodle.org/browse/MDL-82872)

#### Removed

- Mobile support via plugin has been removed as it is now natively available in the Moodle App.

  For more information see [MDL-82447](https://tracker.moodle.org/browse/MDL-82447)

### mod_data

#### Added

- The `\data_add_record()` method accepts a new `$approved` parameter to set the corresponding state of the new record.

  For more information see [MDL-81274](https://tracker.moodle.org/browse/MDL-81274)

#### Deprecated

- The `\mod_data_renderer::render_fields_footer()` method has been deprecated as it's no longer used.

  For more information see [MDL-81321](https://tracker.moodle.org/browse/MDL-81321)

### mod_feedback

#### Deprecated

- The `\feedback_check_is_switchrole()` function has been deprecated as it didn't work.

  For more information see [MDL-72424](https://tracker.moodle.org/browse/MDL-72424)
- The method `\mod_feedback\output\renderer::create_template_form()` has been deprecated. It is not used anymore.

  For more information see [MDL-81742](https://tracker.moodle.org/browse/MDL-81742)

### mod_quiz

#### Added

- The following methods of the `quiz_overview_report` class now take a new optional `$slots` parameter used to only regrade some slots in each attempt (default all):
  - `\quiz_overview_report::regrade_attempts()`
  - `\quiz_overview_report::regrade_batch_of_attempts()`

  For more information see [MDL-79546](https://tracker.moodle.org/browse/MDL-79546)

### qbank_managecategories

#### Changed

- The `\qbank_managecategories\question_category_object` class has been deprecated.
  Methods previously part of this class have been moved to either

   - `\qbank_managecategories\question_categories`,
    for the parts used within this plugin for display a list of categories; or

  `\core_question\category_manager`,
    for the parts used for generate CRUD operations on question categories, including outside of this plugin.

  This change will allow `\qbank_managecategories\question_category_object` to be deprecated, and avoids other parts of the system wishing to manipulate question categories from having to violate cross-component communication rules.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)

#### Deprecated

- Category lists are now generated by templates. The following classes have been deprecated:
  - `\qbank_managecategories\question_category_list`
  - `\qbank_managecategories\question_category_list_item`

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)
- The following methods of `\qbank_managecategories\helper`have been deprecated and moved to
  `\core_question\category_manager`:

  | Method                                               | Replacement                                                                  |
  | ---                                                  | ---                                                                          |
  | `question_is_only_child_of_top_category_in_context`  | `\core_question\category_manager::is_only_child_of_top_category_in_context`  |
  | `question_is_top_category`                           | `\core_question\category_manager::is_top_category`                           |
  | `question_can_delete_cat`                            | `\core_question\category_manager::can_delete_cat`                            |

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)
- `\qbank_managecategories\question_category_object` is now completely deprecated. Its methods have either been migrated to `\qbank_managecategories\question_categories`, `\core_question\category_manager`, or are no longer used at all.

  For more information see [MDL-72397](https://tracker.moodle.org/browse/MDL-72397)

### report_eventlist

#### Deprecated

- The following deprecated methods in `report_eventlist_list_generator` have been removed:
  - `\report_eventlist_list_generator::get_core_events_list()`
  - `\report_eventlist_list_generator::get_non_core_event_list()`

  For more information see [MDL-72786](https://tracker.moodle.org/browse/MDL-72786)

### report_log

#### Added

- The `\report_log_renderable::get_activities_list()` method return values now includes an array of disabled elements, in addition to the array of activities.

  For more information see [MDL-82146](https://tracker.moodle.org/browse/MDL-82146)

### repository_onedrive

#### Removed

- The following previously deprecated methods have been removed and can no longer be used:
  - `\repository_onedrive::can_import_skydrive_files()`
  - `\repository_onedrive::import_skydrive_files()`

  For more information see [MDL-72620](https://tracker.moodle.org/browse/MDL-72620)

### theme

#### Added

- Added a new `\renderer_base::get_page` getter method.

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)
- New `core/context_header` mustache template has been added. This template can be overridden by themes to modify the context header.

  For more information see [MDL-81597](https://tracker.moodle.org/browse/MDL-81597)

#### Deprecated

- The method `\core\output\core_renderer::render_context_header` has been deprecated please use `\core\output\core_renderer::render($contextheader)` instead

  For more information see [MDL-82160](https://tracker.moodle.org/browse/MDL-82160)

#### Removed

- Removed all references to `iconhelp`, `icon-pre`, `icon-post`, `iconlarge`, and `iconsort` CSS classes.

  For more information see [MDL-74251](https://tracker.moodle.org/browse/MDL-74251)

### theme_boost

#### Added

- Bridged `theme-color-level` using a new `shift-color` function to prepare for its deprecation in Boostrap 5.

  For more information see [MDL-81816](https://tracker.moodle.org/browse/MDL-81816)
- Upon upgrading Font Awesome from version 4 to 6, the solid family was selected by default.

  Support for the `regular`, and `brands` families of icons has now been added, allowing icons defined with `\core\outut\icon_system::FONTAWESOME` to use them.

  Icons can select the FontAwesome family (`fa-regular`, `fa-brands`, `fa-solid`) by using the relevant class name when display the icon.

  For more information see [MDL-82210](https://tracker.moodle.org/browse/MDL-82210)

#### Changed

- The Bootstrap `.no-gutters` class is no longer used, use `.g-0`  instead.

  For more information see [MDL-81818](https://tracker.moodle.org/browse/MDL-81818)
- The `.page-header-headings` CSS class now has a background colour applied to the maintenance and secure layouts.
  You may need to override this class in your maintenance and secure layouts if both of the following are true:
  - Your theme plugin inherits from `theme_boost` and uses this CSS class
  - Your theme plugin applies a different styling for the page header for the maintenance and secure layouts.

  For more information see [MDL-83047](https://tracker.moodle.org/browse/MDL-83047)

### tool

#### Removed

- The Convert to InnoDB plugin (`tool_innodb`) has been completely removed.

  For more information see [MDL-78776](https://tracker.moodle.org/browse/MDL-78776)

### tool_behat

#### Added

- Behat tests are now checking for deprecated icons. This check can be disabled by using the `--no-icon-deprecations` option in the behat CLI.

  For more information see [MDL-82212](https://tracker.moodle.org/browse/MDL-82212)

### tool_oauth2

#### Added

- The `\core\oautuh2\client::get_additional_login_parameters()` method now supports adding the language code to the authentication request so that the OAuth2 login page matches the language in Moodle.

  For more information see [MDL-67554](https://tracker.moodle.org/browse/MDL-67554)
