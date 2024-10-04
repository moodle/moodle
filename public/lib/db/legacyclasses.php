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
    \webservice_parameter_exception::class => 'exception/webservice_parameter_exception.php',

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
];
