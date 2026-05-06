<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains mappings for legacy classes that do not fit the standard class naming conventions.
 *
 * In time these classes should be renamed to fit the standard class naming conventions but this is not an overnight process.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Like other files in the db directory this file uses an array.
// The old class name is the key, the path to the file containing the class is the value.
// The array must be called $legacyclasses.
$legacyclasses = [
    \bootstrap_renderer::class => 'output/bootstrap_renderer.php',

    // Core API.
    \emoticon_manager::class => 'emoticon_manager.php',
    \lang_string::class => 'lang_string.php',
    \moodle_url::class => 'url.php',

    // Exception API.
    \coding_exception::class => 'exception/coding_exception.php',
    \file_serving_exception::class => 'exception/file_serving_exception.php',
    \invalid_dataroot_permissions::class => 'exception/invalid_dataroot_permissions.php',
    \invalid_parameter_exception::class => 'exception/invalid_parameter_exception.php',
    \invalid_response_exception::class => 'exception/invalid_response_exception.php',
    \invalid_state_exception::class => 'exception/invalid_state_exception.php',
    \moodle_exception::class => 'exception/moodle_exception.php',
    \require_login_exception::class => 'exception/require_login_exception.php',
    \require_login_session_timeout_exception::class => 'exception/require_login_session_timeout_exception.php',
    \required_capability_exception::class => 'exception/required_capability_exception.php',

    // Cache API.
    \cache::class => [
        'core_cache',
        'cache.php',
    ],
    \cache_application::class => [
        'core_cache',
        'application_cache.php',
    ],
    \cache_definition::class => [
        'core_cache',
        'definition.php',
    ],
    \cache_request::class => [
        'core_cache',
        'request_cache.php',
    ],
    \cache_session::class => [
        'core_cache',
        'session_cache.php',
    ],
    \cache_cached_object::class => [
        'core_cache',
        'cached_object.php',
    ],
    \cache_config::class => [
        'core_cache',
        'config.php',
    ],
    \cache_config_writer::class => [
        'core_cache',
        'config_writer.php',
    ],
    \cache_config_disabled::class => [
        'core_cache',
        'disabled_config.php',
    ],
    \cache_disabled::class => [
        'core_cache',
        'disabled_cache.php',
    ],
    \config_writer::class => [
        'core_cache',
        'config_writer.php',
    ],
    \cache_data_source::class => [
        'core_cache',
        'data_source_interface.php',
    ],
    \cache_data_source_versionable::class => [
        'core_cache',
        'versionable_data_source_interface.php',
    ],
    \cache_exception::class => [
        'core_cache',
        'exception/cache_exception.php',
    ],
    \cache_factory::class => [
        'core_cache',
        'factory.php',
    ],
    \cache_factory_disabled::class => [
        'core_cache',
        'disabled_factory.php',
    ],
    \cache_helper::class => [
        'core_cache',
        'helper.php',
    ],
    \cache_is_key_aware::class => [
        'core_cache',
        'key_aware_cache_interface.php',
    ],
    \cache_is_lockable::class => [
        'core_cache',
        'lockable_cache_interface.php',
    ],
    \cache_is_searchable::class => [
        'core_cache',
        'searchable_cache_interface.php',
    ],
    \cache_is_configurable::class => [
        'core_cache',
        'configurable_cache_interface.php',
    ],
    \cache_loader::class => [
        'core_cache',
        'loader_interface.php',
    ],
    \cache_loader_with_locking::class => [
        'core_cache',
        'loader_with_locking_interface.php',
    ],
    \cache_lock_interface::class => [
        'core_cache',
        'cache_lock_interface.php',
    ],
    \cache_store::class => [
        'core_cache',
        'store.php',
    ],
    \cache_store_interface::class => [
        'core_cache',
        'store_interface.php',
    ],
    \cache_ttl_wrapper::class => [
        'core_cache',
        'ttl_wrapper.php',
    ],
    \cacheable_object::class => [
        'core_cache',
        'cacheable_object_interface.php',
    ],
    \cacheable_object_array::class => [
        'core_cache',
        'cacheable_object_array.php',
    ],
    \cache_definition_mappings_form::class => [
        'core_cache',
        'form/cache_definition_mappings_form.php',
    ],
    \cache_definition_sharing_form::class => [
        'core_cache',
        'form/cache_definition_sharing_form.php',
    ],
    \cache_lock_form::class => [
        'core_cache',
        'form/cache_lock_form.php',
    ],
    \cache_mode_mappings_form::class => [
        'core_cache',
        'form/cache_mode_mappings_form.php',
    ],
    \cachestore_addinstance_form::class => [
        'core_cache',
        'form/cachestore_addinstance_form.php',
    ],

    // Navigation API.
    \breadcrumb_navigation_node::class => 'navigation/breadcrumb_navigation_node.php',
    \flat_navigation::class => 'navigation/flat_navigation.php',
    \global_navigation::class => 'navigation/global_navigation.php',
    \global_navigation_for_ajax::class => 'navigation/global_navigation_for_ajax.php',
    \navbar::class => 'navigation/navbar.php',
    \navigation_cache::class => 'navigation/navigation_cache.php',
    \navigation_json::class => 'navigation/navigation_json.php',
    \navigation_node::class => 'navigation/navigation_node.php',
    \navigation_node_collection::class => 'navigation/navigation_node_collection.php',
    \settings_navigation::class => 'navigation/settings_navigation.php',
    \settings_navigation_ajax::class => 'navigation/settings_navigation_ajax.php',

    // Output API.
    \theme_config::class => 'output/theme_config.php',
    \xhtml_container_stack::class => 'output/xhtml_container_stack.php',

    \renderable::class => 'output/renderable.php',
    \templatable::class => 'output/templatable.php',

    // Output API: Renderer Factories.
    \renderer_factory::class => 'output/renderer_factory/renderer_factory_interface.php',
    \renderer_factory_base::class => 'output/renderer_factory/renderer_factory_base.php',
    \standard_renderer_factory::class => 'output/renderer_factory/standard_renderer_factory.php',
    \theme_overridden_renderer_factory::class => 'output/renderer_factory/theme_overridden_renderer_factory.php',

    // Output API: Renderers.
    \renderer_base::class => 'output/renderer_base.php',
    \plugin_renderer_base::class => 'output/plugin_renderer_base.php',
    \core_renderer::class => 'output/core_renderer.php',
    \core_renderer_cli::class => 'output/core_renderer_cli.php',
    \core_renderer_ajax::class => 'output/core_renderer_ajax.php',
    \core_renderer_maintenance::class => 'output/core_renderer_maintenance.php',
    \page_requirements_manager::class => 'output/requirements/page_requirements_manager.php',
    \YUI_config::class => 'output/requirements/yui.php',
    \fragment_requirements_manager::class => 'output/requirements/fragment_requirements_manager.php',

    // Output API: components.
    \file_picker::class => 'output/file_picker.php',
    \user_picture::class => 'output/user_picture.php',
    \help_icon::class => 'output/help_icon.php',
    \pix_icon_font::class => 'output/pix_icon_font.php',
    \pix_icon_fontawesome::class => 'output/pix_icon_fontawesome.php',
    \pix_icon::class => 'output/pix_icon.php',
    \image_icon::class => 'output/image_icon.php',
    \pix_emoticon::class => 'output/pix_emoticon.php',
    \single_button::class => 'output/single_button.php',
    \single_select::class => 'output/single_select.php',
    \url_select::class => 'output/url_select.php',
    \action_link::class => 'output/action_link.php',
    \html_writer::class => 'output/html_writer.php',
    \js_writer::class => 'output/js_writer.php',
    \paging_bar::class => 'output/paging_bar.php',
    \initials_bar::class => 'output/initials_bar.php',
    \custom_menu_item::class => 'output/custom_menu_item.php',
    \custom_menu::class => 'output/custom_menu.php',
    \tabobject::class => 'output/tabobject.php',
    \context_header::class => 'output/context_header.php',
    \tabtree::class => 'output/tabtree.php',
    \action_menu::class => 'output/action_menu.php',
    \action_menu_filler::class => 'output/action_menu/filler.php',
    \action_menu_link::class => 'output/action_menu/link.php',
    \action_menu_link_primary::class => 'output/action_menu/link_primary.php',
    \action_menu_link_secondary::class => 'output/action_menu/link_secondary.php',
    \core\output\local\action_menu\subpanel::class => 'output/action_menu/subpanel.php',
    \preferences_groups::class => 'output/preferences_groups.php',
    \preferences_group::class => 'output/preferences_group.php',
    \progress_bar::class => 'output/progress_bar.php',
    \component_action::class => 'output/actions/component_action.php',
    \confirm_action::class => 'output/actions/confirm_action.php',
    \popup_action::class => 'output/actions/popup_action.php',

    // Block Subsystem.
    \block_contents::class => [
        'core_block',
        'output/block_contents.php',
    ],
    \block_move_target::class => [
        'core_block',
        'output/block_move_target.php',
    ],

    // Table Subsystem.
    \html_table::class => [
        'core_table',
        'output/html_table.php',
    ],
    \html_table_row::class => [
        'core_table',
        'output/html_table_row.php',
    ],
    \html_table_cell::class => [
        'core_table',
        'output/html_table_cell.php',
    ],
    \flexible_table::class => [
        'core_table',
        'flexible_table.php',
    ],
    \table_sql::class => [
        'core_table',
        'sql_table.php',
    ],
    \table_default_export_format_parent::class => [
        'core_table',
        'base_export_format.php',
    ],
    \table_dataformat_export_format::class => [
        'core_table',
        'dataformat_export_format.php',
    ],

    // Course drag-and-drop upload system.
    \dndupload_handler::class => [
        'core_course',
        'dndupload_handler.php',
    ],
    \dndupload_ajax_processor::class => [
        'core_course',
        'dndupload_ajax_processor.php',
    ],

    // The progress_trace classes.
    \combined_progress_trace::class => 'output/progress_trace/combined_progress_trace.php',
    \error_log_progress_trace::class => 'output/progress_trace/error_log_progress_trace.php',
    \html_list_progress_trace::class => 'output/progress_trace/html_list_progress_trace.php',
    \html_progress_trace::class => 'output/progress_trace/html_progress_trace.php',
    \null_progress_trace::class => 'output/progress_trace/null_progress_trace.php',
    \progress_trace::class => 'output/progress_trace.php',
    \progress_trace_buffer::class => 'output/progress_trace/progress_trace_buffer.php',
    \text_progress_trace::class => 'output/progress_trace/text_progress_trace.php',

    // Filters subsystem.
    \filter_manager::class => [
        'core_filters',
        'filter_manager.php',
    ],
    \filterobject::class => [
        'core_filters',
        'filter_object.php',
    ],
    \moodle_text_filter::class => [
        'core_filters',
        'text_filter.php',
    ],
    \null_filter_manager::class => [
        'core_filters',
        'null_filter_manager.php',
    ],
    \performance_measuring_filter_manager::class => [
        'core_filters',
        'performance_measuring_filter_manager.php',
    ],
    \filter_local_settings_form::class => [
        'core_filters',
        'form/local_settings_form.php',
    ],
    \course_modinfo::class => [
        'core_course',
        'modinfo.php',
    ],
    \cm_info::class => [
        'core_course',
        'cm_info.php',
    ],
    \cached_cm_info::class => [
        'core_course',
        'cached_cm_info.php',
    ],
    \section_info::class => [
        'core_course',
        'section_info.php',
    ],
    \comment::class => [
        'core_comment',
        'manager.php',
    ],
    \comment_exception::class => [
        'core_comment',
        'comment_exception.php',
    ],
    \course_request::class => [
        'core_course',
        'course_request.php',
    ],
    \core_course\output\activitychooserbutton::class => [
        'core_courseformat',
        'output/local/content/activitychooserbutton.php',
    ],

    \testing_util::class => 'test/testing_util.php',
    \phpunit_util::class => 'test/phpunit/phpunit_util.php',
    \phpunit_coverage_info::class => 'test/phpunit/coverage_info.php',
    \phpunit_message_sink::class => 'test/phpunit/message_sink.php',
    \phpunit_phpmailer_sink::class => 'test/phpunit/phpmailer_sink.php',
    \phpunit_event_mock::class => 'test/phpunit/event_mock.php',
    \phpunit_event_sink::class => 'test/phpunit/event_sink.php',
    \tests_finder::class => 'test/test_finder.php',

    // Admin classes (core_admin).
    // Admin base classes.
    \admin_setting::class => ['core_admin', 'setting.php'],

    // Admin page classes.
    \admin_page_pluginsoverview::class => ['core_admin', 'setting/page/pluginsoverview.php'],
    \admin_page_managemods::class => ['core_admin', 'setting/page/managemods.php'],
    \admin_page_manageblocks::class => ['core_admin', 'setting/page/manageblocks.php'],
    \admin_page_managemessageoutputs::class => ['core_admin', 'setting/page/managemessageoutputs.php'],
    \admin_page_manageqbehaviours::class => ['core_admin', 'setting/page/manageqbehaviours.php'],
    \admin_page_manageqtypes::class => ['core_admin', 'setting/page/manageqtypes.php'],
    \admin_page_manageportfolios::class => ['core_admin', 'setting/page/manageportfolios.php'],
    \admin_page_managerepositories::class => ['core_admin', 'setting/page/managerepositories.php'],
    \admin_page_managefilters::class => ['core_admin', 'setting/page/managefilters.php'],

    // Admin setting classes.
    \admin_setting_flag::class => ['core_admin', 'setting/setting/flag.php'],
    \admin_setting_heading::class => ['core_admin', 'setting/setting/heading.php'],
    \admin_setting_description::class => ['core_admin', 'setting/setting/description.php'],
    \admin_setting_configtext::class => ['core_admin', 'setting/setting/configtext.php'],
    \admin_setting_configtext_with_maxlength::class => ['core_admin', 'setting/setting/configtext_with_maxlength.php'],
    \admin_setting_configtextarea::class => ['core_admin', 'setting/setting/configtextarea.php'],
    \admin_setting_configbackupfilenamemustachetemplate::class => [
        'core_admin',
        'setting/setting/configbackupfilenamemustachetemplate.php',
    ],
    \admin_setting_confightmleditor::class => ['core_admin', 'setting/setting/confightmleditor.php'],
    \admin_setting_configpasswordunmask::class => ['core_admin', 'setting/setting/configpasswordunmask.php'],
    \admin_setting_configpasswordunmask_with_advanced::class => [
        'core_admin',
        'setting/setting/configpasswordunmask_with_advanced.php',
    ],
    \admin_setting_encryptedpassword::class => ['core_admin', 'setting/setting/encryptedpassword.php'],
    \admin_setting_configempty::class => ['core_admin', 'setting/setting/configempty.php'],
    \admin_setting_configfile::class => ['core_admin', 'setting/setting/configfile.php'],
    \admin_setting_configexecutable::class => ['core_admin', 'setting/setting/configexecutable.php'],
    \admin_setting_configdirectory::class => ['core_admin', 'setting/setting/configdirectory.php'],
    \admin_setting_configcheckbox::class => ['core_admin', 'setting/setting/configcheckbox.php'],
    \admin_setting_configmulticheckbox::class => ['core_admin', 'setting/setting/configmulticheckbox.php'],
    \admin_setting_configmulticheckbox2::class => ['core_admin', 'setting/setting/configmulticheckbox2.php'],
    \admin_setting_configselect::class => ['core_admin', 'setting/setting/configselect.php'],
    \admin_setting_configmultiselect::class => ['core_admin', 'setting/setting/configmultiselect.php'],
    \admin_setting_configtime::class => ['core_admin', 'setting/setting/configtime.php'],
    \admin_setting_configduration::class => ['core_admin', 'setting/setting/configduration.php'],
    \admin_setting_configduration_with_advanced::class => ['core_admin', 'setting/setting/configduration_with_advanced.php'],
    \admin_setting_configiplist::class => ['core_admin', 'setting/setting/configiplist.php'],
    \admin_setting_configmixedhostiplist::class => ['core_admin', 'setting/setting/configmixedhostiplist.php'],
    \admin_setting_configportlist::class => ['core_admin', 'setting/setting/configportlist.php'],
    \admin_setting_users_with_capability::class => ['core_admin', 'setting/setting/users_with_capability.php'],
    \admin_setting_special_adminseesall::class => ['core_admin', 'setting/setting/special_adminseesall.php'],
    \admin_setting_special_selectsetup::class => ['core_admin', 'setting/setting/special_selectsetup.php'],
    \admin_setting_sitesetselect::class => ['core_admin', 'setting/setting/sitesetselect.php'],
    \admin_setting_bloglevel::class => ['core_admin', 'setting/setting/bloglevel.php'],
    \admin_setting_courselist_frontpage::class => ['core_admin', 'setting/setting/courselist_frontpage.php'],
    \admin_setting_sitesetcheckbox::class => ['core_admin', 'setting/setting/sitesetcheckbox.php'],
    \admin_setting_sitesettext::class => ['core_admin', 'setting/setting/sitesettext.php'],
    \admin_setting_requiredtext::class => ['core_admin', 'setting/setting/requiredtext.php'],
    \admin_setting_requiredpasswordunmask::class => ['core_admin', 'setting/setting/requiredpasswordunmask.php'],
    \admin_setting_special_frontpagedesc::class => ['core_admin', 'setting/setting/special_frontpagedesc.php'],
    \admin_setting_emoticons::class => ['core_admin', 'setting/setting/emoticons.php'],
    \admin_setting_langlist::class => ['core_admin', 'setting/setting/langlist.php'],
    \admin_setting_countrycodes::class => ['core_admin', 'setting/setting/countrycodes.php'],
    \admin_settings_country_select::class => ['core_admin', 'setting/setting/country_select.php'],
    \admin_settings_num_course_sections::class => ['core_admin', 'setting/setting/num_course_sections.php'],
    \admin_settings_coursecat_select::class => ['core_admin', 'setting/setting/coursecat_select.php'],
    \admin_setting_special_backupdays::class => ['core_admin', 'setting/setting/special_backupdays.php'],
    \admin_setting_special_backup_auto_destination::class => ['core_admin', 'setting/setting/special_backup_auto_destination.php'],
    \admin_setting_special_debug::class => ['core_admin', 'setting/setting/special_debug.php'],
    \admin_setting_special_calendar_weekend::class => ['core_admin', 'setting/setting/special_calendar_weekend.php'],
    \admin_setting_question_behaviour::class => ['core_admin', 'setting/setting/question_behaviour.php'],
    \admin_setting_pickroles::class => ['core_admin', 'setting/setting/pickroles.php'],
    \admin_setting_pickfilters::class => ['core_admin', 'setting/setting/pickfilters.php'],
    \admin_setting_configtext_with_advanced::class => ['core_admin', 'setting/setting/configtext_with_advanced.php'],
    \admin_setting_configcheckbox_with_advanced::class => ['core_admin', 'setting/setting/configcheckbox_with_advanced.php'],
    \admin_setting_configcheckbox_with_lock::class => ['core_admin', 'setting/setting/configcheckbox_with_lock.php'],
    \admin_setting_configselect_autocomplete::class => ['core_admin', 'setting/setting/configselect_autocomplete.php'],
    \admin_setting_configselect_with_advanced::class => ['core_admin', 'setting/setting/configselect_with_advanced.php'],
    \admin_setting_configselect_with_lock::class => ['core_admin', 'setting/setting/configselect_with_lock.php'],
    \admin_setting_special_gradebookroles::class => ['core_admin', 'setting/setting/special_gradebookroles.php'],
    \admin_setting_regradingcheckbox::class => ['core_admin', 'setting/setting/regradingcheckbox.php'],
    \admin_setting_special_coursecontact::class => ['core_admin', 'setting/setting/special_coursecontact.php'],
    \admin_setting_special_gradelimiting::class => ['core_admin', 'setting/setting/special_gradelimiting.php'],
    \admin_setting_special_grademinmaxtouse::class => ['core_admin', 'setting/setting/special_grademinmaxtouse.php'],
    \admin_setting_special_gradeexport::class => ['core_admin', 'setting/setting/special_gradeexport.php'],
    \admin_setting_special_gradeexportdefault::class => ['core_admin', 'setting/setting/special_gradeexportdefault.php'],
    \admin_setting_special_gradepointdefault::class => ['core_admin', 'setting/setting/special_gradepointdefault.php'],
    \admin_setting_special_gradepointmax::class => ['core_admin', 'setting/setting/special_gradepointmax.php'],
    \admin_setting_gradecat_combo::class => ['core_admin', 'setting/setting/gradecat_combo.php'],
    \admin_setting_grade_profilereport::class => ['core_admin', 'setting/setting/grade_profilereport.php'],
    \admin_setting_my_grades_report::class => ['core_admin', 'setting/setting/my_grades_report.php'],
    \admin_setting_special_registerauth::class => ['core_admin', 'setting/setting/special_registerauth.php'],
    \admin_setting_manageenrols::class => ['core_admin', 'setting/setting/manageenrols.php'],
    \admin_setting_manageauths::class => ['core_admin', 'setting/setting/manageauths.php'],
    \admin_setting_manageantiviruses::class => ['core_admin', 'setting/setting/manageantiviruses.php'],
    \admin_setting_manageformats::class => ['core_admin', 'setting/setting/manageformats.php'],
    \admin_setting_managecustomfields::class => ['core_admin', 'setting/setting/managecustomfields.php'],
    \admin_setting_managedataformats::class => ['core_admin', 'setting/setting/managedataformats.php'],
    \admin_setting_manage_plugins::class => ['core_admin', 'setting/setting/manage_plugins.php'],
    \admin_setting_manage_fileconverter_plugins::class => ['core_admin', 'setting/setting/manage_fileconverter_plugins.php'],
    \admin_setting_managemediaplayers::class => ['core_admin', 'setting/setting/managemediaplayers.php'],
    \admin_setting_managecontentbankcontenttypes::class => ['core_admin', 'setting/setting/managecontentbankcontenttypes.php'],
    \admin_setting_managerepository::class => ['core_admin', 'setting/setting/managerepository.php'],
    \admin_setting_enablemobileservice::class => ['core_admin', 'setting/setting/enablemobileservice.php'],
    \admin_setting_manageexternalservices::class => ['core_admin', 'setting/setting/manageexternalservices.php'],
    \admin_setting_webservicesoverview::class => ['core_admin', 'setting/setting/webservicesoverview.php'],
    \admin_setting_managewebserviceprotocols::class => ['core_admin', 'setting/setting/managewebserviceprotocols.php'],
    \admin_setting_configcolourpicker::class => ['core_admin', 'setting/setting/configcolourpicker.php'],
    \admin_setting_configstoredfile::class => ['core_admin', 'setting/setting/configstoredfile.php'],
    \admin_setting_configmultiselect_modules::class => ['core_admin', 'setting/setting/configmultiselect_modules.php'],
    \admin_setting_php_extension_enabled::class => ['core_admin', 'setting/setting/php_extension_enabled.php'],
    \admin_setting_servertimezone::class => ['core_admin', 'setting/setting/servertimezone.php'],
    \admin_setting_forcetimezone::class => ['core_admin', 'setting/setting/forcetimezone.php'],
    \admin_setting_searchsetupinfo::class => ['core_admin', 'setting/setting/searchsetupinfo.php'],
    \admin_setting_scsscode::class => ['core_admin', 'setting/setting/scsscode.php'],
    \admin_setting_filetypes::class => ['core_admin', 'setting/setting/filetypes.php'],
    \admin_setting_agedigitalconsentmap::class => ['core_admin', 'setting/setting/agedigitalconsentmap.php'],
    \admin_settings_sitepolicy_handler_select::class => ['core_admin', 'setting/setting/sitepolicy_handler_select.php'],
    \admin_setting_configthemepreset::class => ['core_admin', 'setting/setting/configthemepreset.php'],
    \admin_settings_h5plib_handler_select::class => ['core_admin', 'setting/setting/h5plib_handler_select.php'],
    \admin_setting_check::class => ['core_admin', 'setting/setting/check.php'],
    \admin_setting_savebutton::class => ['core_admin', 'setting/setting/savebutton.php'],

    // Admin setting page classes.
    \admin_settingdependency::class => ['core_admin', 'setting/settingpage/dependency.php'],
    \admin_settingpage::class => ['core_admin', 'setting/settingpage/settingpage.php'],

    // Admin tree interfaces and classes.
    \part_of_admin_tree::class => ['core_admin', 'setting/tree/part_of_admin_tree.php'],
    \parentable_part_of_admin_tree::class => ['core_admin', 'setting/tree/parentable_part_of_admin_tree.php'],
    \admin_category::class => ['core_admin', 'setting/tree/category.php'],
    \admin_root::class => ['core_admin', 'setting/tree/root.php'],
    \admin_externalpage::class => ['core_admin', 'setting/tree/externalpage.php'],
];
