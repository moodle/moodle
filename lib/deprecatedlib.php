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
 * deprecatedlib.php - Old functions retained only for backward compatibility
 *
 * Old functions retained only for backward compatibility.  New code should not
 * use any of these functions.
 *
 * @package    core
 * @subpackage deprecated
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated
 */

defined('MOODLE_INTERNAL') || die();

/* === Functions that needs to be kept longer in deprecated lib than normal time period === */

/**
 * @deprecated since 2.7 use new events instead
 */
function add_to_log() {
    throw new coding_exception('add_to_log() has been removed, please rewrite your code to the new events API');
}

/**
 * @deprecated since 2.6
 */
function events_trigger() {
    throw new coding_exception('events_trigger() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * List all core subsystems and their location
 *
 * This is a list of components that are part of the core and their
 * language strings are defined in /lang/en/<<subsystem>>.php. If a given
 * plugin is not listed here and it does not have proper plugintype prefix,
 * then it is considered as course activity module.
 *
 * The location is optionally dirroot relative path. NULL means there is no special
 * directory for this subsystem. If the location is set, the subsystem's
 * renderer.php is expected to be there.
 *
 * @deprecated since 2.6, use core_component::get_core_subsystems()
 *
 * @param bool $fullpaths false means relative paths from dirroot, use true for performance reasons
 * @return array of (string)name => (string|null)location
 */
function get_core_subsystems($fullpaths = false) {
    global $CFG;

    // NOTE: do not add any other debugging here, keep forever.

    $subsystems = core_component::get_core_subsystems();

    if ($fullpaths) {
        return $subsystems;
    }

    debugging('Short paths are deprecated when using get_core_subsystems(), please fix the code to use fullpaths instead.', DEBUG_DEVELOPER);

    $dlength = strlen($CFG->dirroot);

    foreach ($subsystems as $k => $v) {
        if ($v === null) {
            continue;
        }
        $subsystems[$k] = substr($v, $dlength+1);
    }

    return $subsystems;
}

/**
 * Lists all plugin types.
 *
 * @deprecated since 2.6, use core_component::get_plugin_types()
 *
 * @param bool $fullpaths false means relative paths from dirroot
 * @return array Array of strings - name=>location
 */
function get_plugin_types($fullpaths = true) {
    global $CFG;

    // NOTE: do not add any other debugging here, keep forever.

    $types = core_component::get_plugin_types();

    if ($fullpaths) {
        return $types;
    }

    debugging('Short paths are deprecated when using get_plugin_types(), please fix the code to use fullpaths instead.', DEBUG_DEVELOPER);

    $dlength = strlen($CFG->dirroot);

    foreach ($types as $k => $v) {
        if ($k === 'theme') {
            $types[$k] = 'theme';
            continue;
        }
        $types[$k] = substr($v, $dlength+1);
    }

    return $types;
}

/**
 * Use when listing real plugins of one type.
 *
 * @deprecated since 2.6, use core_component::get_plugin_list()
 *
 * @param string $plugintype type of plugin
 * @return array name=>fulllocation pairs of plugins of given type
 */
function get_plugin_list($plugintype) {

    // NOTE: do not add any other debugging here, keep forever.

    if ($plugintype === '') {
        $plugintype = 'mod';
    }

    return core_component::get_plugin_list($plugintype);
}

/**
 * Get a list of all the plugins of a given type that define a certain class
 * in a certain file. The plugin component names and class names are returned.
 *
 * @deprecated since 2.6, use core_component::get_plugin_list_with_class()
 *
 * @param string $plugintype the type of plugin, e.g. 'mod' or 'report'.
 * @param string $class the part of the name of the class after the
 *      frankenstyle prefix. e.g 'thing' if you are looking for classes with
 *      names like report_courselist_thing. If you are looking for classes with
 *      the same name as the plugin name (e.g. qtype_multichoice) then pass ''.
 * @param string $file the name of file within the plugin that defines the class.
 * @return array with frankenstyle plugin names as keys (e.g. 'report_courselist', 'mod_forum')
 *      and the class names as values (e.g. 'report_courselist_thing', 'qtype_multichoice').
 */
function get_plugin_list_with_class($plugintype, $class, $file) {

    // NOTE: do not add any other debugging here, keep forever.

    return core_component::get_plugin_list_with_class($plugintype, $class, $file);
}

/**
 * Returns the exact absolute path to plugin directory.
 *
 * @deprecated since 2.6, use core_component::get_plugin_directory()
 *
 * @param string $plugintype type of plugin
 * @param string $name name of the plugin
 * @return string full path to plugin directory; NULL if not found
 */
function get_plugin_directory($plugintype, $name) {

    // NOTE: do not add any other debugging here, keep forever.

    if ($plugintype === '') {
        $plugintype = 'mod';
    }

    return core_component::get_plugin_directory($plugintype, $name);
}

/**
 * Normalize the component name using the "frankenstyle" names.
 *
 * @deprecated since 2.6, use core_component::normalize_component()
 *
 * @param string $component
 * @return array two-items list of [(string)type, (string|null)name]
 */
function normalize_component($component) {

    // NOTE: do not add any other debugging here, keep forever.

    return core_component::normalize_component($component);
}

/**
 * Return exact absolute path to a plugin directory.
 *
 * @deprecated since 2.6, use core_component::normalize_component()
 *
 * @param string $component name such as 'moodle', 'mod_forum'
 * @return string full path to component directory; NULL if not found
 */
function get_component_directory($component) {

    // NOTE: do not add any other debugging here, keep forever.

    return core_component::get_component_directory($component);
}

/**
 * Get the context instance as an object. This function will create the
 * context instance if it does not exist yet.
 *
 * @deprecated since 2.2, use context_course::instance() or other relevant class instead
 * @todo This will be deleted in Moodle 2.8, refer MDL-34472
 * @param integer $contextlevel The context level, for example CONTEXT_COURSE, or CONTEXT_MODULE.
 * @param integer $instance The instance id. For $level = CONTEXT_COURSE, this would be $course->id,
 *      for $level = CONTEXT_MODULE, this would be $cm->id. And so on. Defaults to 0
 * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
 *      MUST_EXIST means throw exception if no record or multiple records found
 * @return context The context object.
 */
function get_context_instance($contextlevel, $instance = 0, $strictness = IGNORE_MISSING) {

    debugging('get_context_instance() is deprecated, please use context_xxxx::instance() instead.', DEBUG_DEVELOPER);

    $instances = (array)$instance;
    $contexts = array();

    $classname = context_helper::get_class_for_level($contextlevel);

    // we do not load multiple contexts any more, PAGE should be responsible for any preloading
    foreach ($instances as $inst) {
        $contexts[$inst] = $classname::instance($inst, $strictness);
    }

    if (is_array($instance)) {
        return $contexts;
    } else {
        return $contexts[$instance];
    }
}
/* === End of long term deprecated api list === */

/**
 * @deprecated since 2.7 - use new file picker instead
 */
function clam_log_upload() {
    throw new coding_exception('clam_log_upload() can not be used any more, please use file picker instead');
}

/**
 * @deprecated since 2.7 - use new file picker instead
 */
function clam_log_infected() {
    throw new coding_exception('clam_log_infected() can not be used any more, please use file picker instead');
}

/**
 * @deprecated since 2.7 - use new file picker instead
 */
function clam_change_log() {
    throw new coding_exception('clam_change_log() can not be used any more, please use file picker instead');
}

/**
 * @deprecated since 2.7 - infected files are now deleted in file picker
 */
function clam_replace_infected_file() {
    throw new coding_exception('clam_replace_infected_file() can not be used any more, please use file picker instead');
}

/**
 * @deprecated since 2.7
 */
function clam_handle_infected_file() {
    throw new coding_exception('clam_handle_infected_file() can not be used any more, please use file picker instead');
}

/**
 * @deprecated since 2.7
 */
function clam_scan_moodle_file() {
    throw new coding_exception('clam_scan_moodle_file() can not be used any more, please use file picker instead');
}


/**
 * @deprecated since 2.7 PHP 5.4.x should be always compatible.
 */
function password_compat_not_supported() {
    throw new coding_exception('Do not use password_compat_not_supported() - bcrypt is now always available');
}

/**
 * @deprecated since 2.6
 */
function session_get_instance() {
    throw new coding_exception('session_get_instance() is removed, use \core\session\manager instead');
}

/**
 * @deprecated since 2.6
 */
function session_is_legacy() {
    throw new coding_exception('session_is_legacy() is removed, do not use any more');
}

/**
 * @deprecated since 2.6
 */
function session_kill_all() {
    throw new coding_exception('session_kill_all() is removed, use \core\session\manager::kill_all_sessions() instead');
}

/**
 * @deprecated since 2.6
 */
function session_touch() {
    throw new coding_exception('session_touch() is removed, use \core\session\manager::touch_session() instead');
}

/**
 * @deprecated since 2.6
 */
function session_kill() {
    throw new coding_exception('session_kill() is removed, use \core\session\manager::kill_session() instead');
}

/**
 * @deprecated since 2.6
 */
function session_kill_user() {
    throw new coding_exception('session_kill_user() is removed, use \core\session\manager::kill_user_sessions() instead');
}

/**
 * @deprecated since 2.6
 */
function session_set_user() {
    throw new coding_exception('session_set_user() is removed, use \core\session\manager::set_user() instead');
}

/**
 * @deprecated since 2.6
 */
function session_is_loggedinas() {
    throw new coding_exception('session_is_loggedinas() is removed, use \core\session\manager::is_loggedinas() instead');
}

/**
 * @deprecated since 2.6
 */
function session_get_realuser() {
    throw new coding_exception('session_get_realuser() is removed, use \core\session\manager::get_realuser() instead');
}

/**
 * @deprecated since 2.6
 */
function session_loginas() {
    throw new coding_exception('session_loginas() is removed, use \core\session\manager::loginas() instead');
}

/**
 * @deprecated since 2.6
 */
function js_minify() {
    throw new coding_exception('js_minify() is removed, use core_minify::js_files() or core_minify::js() instead.');
}

/**
 * @deprecated since 2.6
 */
function css_minify_css() {
    throw new coding_exception('css_minify_css() is removed, use core_minify::css_files() or core_minify::css() instead.');
}

// === Deprecated before 2.6.0 ===

/**
 * @deprecated
 */
function check_gd_version() {
    throw new coding_exception('check_gd_version() is removed, GD extension is always available now');
}

/**
 * @deprecated
 */
function update_login_count() {
    throw new coding_exception('update_login_count() is removed, all calls need to be removed');
}

/**
 * @deprecated
 */
function reset_login_count() {
    throw new coding_exception('reset_login_count() is removed, all calls need to be removed');
}

/**
 * @deprecated
 */
function update_log_display_entry() {

    throw new coding_exception('The update_log_display_entry() is removed, please use db/log.php description file instead.');
}

/**
 * @deprecated use the text formatting in a standard way instead (https://moodledev.io/docs/apis/subsystems/output#output-functions)
 *             this was abused mostly for embedding of attachments
 */
function filter_text() {
    throw new coding_exception('filter_text() can not be used anymore, use format_text(), format_string() etc instead.');
}

/**
 * @deprecated Loginhttps is no longer supported
 */
function httpsrequired() {
    throw new coding_exception('httpsrequired() can not be used any more. Loginhttps is no longer supported.');
}

/**
 * @deprecated since 3.1 - replacement legacy file API methods can be found on the moodle_url class, for example:
 * The moodle_url::make_legacyfile_url() method can be used to generate a legacy course file url. To generate
 * course module file.php url the moodle_url::make_file_url() should be used.
 */
function get_file_url() {
    throw new coding_exception('get_file_url() can not be used anymore. Please use ' .
        'moodle_url factory methods instead.');
}

/**
 * @deprecated use get_enrolled_users($context) instead.
 */
function get_course_participants() {
    throw new coding_exception('get_course_participants() can not be used any more, use get_enrolled_users() instead.');
}

/**
 * @deprecated use is_enrolled($context, $userid) instead.
 */
function is_course_participant() {
    throw new coding_exception('is_course_participant() can not be used any more, use is_enrolled() instead.');
}

/**
 * @deprecated
 */
function get_recent_enrolments() {
    throw new coding_exception('get_recent_enrolments() is removed as it returned inaccurate results.');
}

/**
 * @deprecated use clean_param($string, PARAM_FILE) instead.
 */
function detect_munged_arguments() {
    throw new coding_exception('detect_munged_arguments() can not be used any more, please use clean_param(,PARAM_FILE) instead.');
}


/**
 * @deprecated since 2.0 MDL-15919
 */
function unzip_file() {
    throw new coding_exception(__FUNCTION__ . '() is deprecated. '
        . 'Please use the application/zip file_packer implementation instead.');
}

/**
 * @deprecated since 2.0 MDL-15919
 */
function zip_files() {
    throw new coding_exception(__FUNCTION__ . '() is deprecated. '
        . 'Please use the application/zip file_packer implementation instead.');
}

/**
 * @deprecated use groups_get_all_groups() instead.
 */
function mygroupid() {
    throw new coding_exception('mygroupid() can not be used any more, please use groups_get_all_groups() instead.');
}

/**
 * @deprecated since Moodle 2.0 MDL-14617 - please do not use this function any more.
 */
function groupmode() {
    throw new coding_exception('groupmode() can not be used any more, please use groups_get_* instead.');
}

/**
 * @deprecated Since year 2006 - please do not use this function any more.
 */
function set_current_group() {
    throw new coding_exception('set_current_group() can not be used anymore, please use $SESSION->currentgroup[$courseid] instead');
}

/**
 * @deprecated Since year 2006 - please do not use this function any more.
 */
function get_current_group() {
    throw new coding_exception('get_current_group() can not be used any more, please use groups_get_* instead');
}

/**
 * @deprecated Since Moodle 2.8
 */
function groups_filter_users_by_course_module_visible() {
    throw new coding_exception('groups_filter_users_by_course_module_visible() is removed. ' .
            'Replace with a call to \core_availability\info_module::filter_user_list(), ' .
            'which does basically the same thing but includes other restrictions such ' .
            'as profile restrictions.');
}

/**
 * @deprecated Since Moodle 2.8
 */
function groups_course_module_visible() {
    throw new coding_exception('groups_course_module_visible() is removed, use $cm->uservisible to decide whether the current
        user can ' . 'access an activity.', DEBUG_DEVELOPER);
}

/**
 * @deprecated since 2.0
 */
function error() {
    throw new coding_exception('notlocalisederrormessage', 'error', $link, $message, 'error() is a removed, please call
            throw new \moodle_exception() instead of error()');
}


/**
 * @deprecated use $PAGE->theme->name instead.
 */
function current_theme() {
    throw new coding_exception('current_theme() can not be used any more, please use $PAGE->theme->name instead');
}

/**
 * @deprecated
 */
function formerr() {
    throw new coding_exception('formerr() is removed. Please change your code to use $OUTPUT->error_text($string).');
}

/**
 * @deprecated use $OUTPUT->skip_link_target() in instead.
 */
function skip_main_destination() {
    throw new coding_exception('skip_main_destination() can not be used any more, please use $OUTPUT->skip_link_target() instead.');
}

/**
 * @deprecated use $OUTPUT->container() instead.
 */
function print_container() {
    throw new coding_exception('print_container() can not be used any more. Please use $OUTPUT->container() instead.');
}

/**
 * @deprecated use $OUTPUT->container_start() instead.
 */
function print_container_start() {
    throw new coding_exception('print_container_start() can not be used any more. Please use $OUTPUT->container_start() instead.');
}

/**
 * @deprecated use $OUTPUT->container_end() instead.
 */
function print_container_end() {
    throw new coding_exception('print_container_end() can not be used any more. Please use $OUTPUT->container_end() instead.');
}

/**
 * @deprecated since Moodle 2.0 MDL-19077 - use $OUTPUT->notification instead.
 */
function notify() {
    throw new coding_exception('notify() is removed, please use $OUTPUT->notification() instead');
}

/**
 * @deprecated use $OUTPUT->continue_button() instead.
 */
function print_continue() {
    throw new coding_exception('print_continue() can not be used any more. Please use $OUTPUT->continue_button() instead.');
}

/**
 * @deprecated use $PAGE methods instead.
 */
function print_header() {

    throw new coding_exception('print_header() can not be used any more. Please use $PAGE methods instead.');
}

/**
 * @deprecated use $PAGE methods instead.
 */
function print_header_simple() {

    throw new coding_exception('print_header_simple() can not be used any more. Please use $PAGE methods instead.');
}

/**
 * @deprecated use $OUTPUT->block() instead.
 */
function print_side_block() {
    throw new coding_exception('print_side_block() can not be used any more, please use $OUTPUT->block() instead.');
}

/**
 * @deprecated since Moodle 3.6
 */
function print_textarea() {
    throw new coding_exception(
        'print_textarea() has been removed. Please use $OUTPUT->print_textarea() instead.'
    );
}

/**
 * Returns an image of an up or down arrow, used for column sorting. To avoid unnecessary DB accesses, please
 * provide this function with the language strings for sortasc and sortdesc.
 *
 * @deprecated use $OUTPUT->arrow() instead.
 * @todo final deprecation of this function once MDL-45448 is resolved
 *
 * If no sort string is associated with the direction, an arrow with no alt text will be printed/returned.
 *
 * @global object
 * @param string $direction 'up' or 'down'
 * @param string $strsort The language string used for the alt attribute of this image
 * @param bool $return Whether to print directly or return the html string
 * @return string|void depending on $return
 *
 */
function print_arrow($direction='up', $strsort=null, $return=false) {
    global $OUTPUT;

    debugging('print_arrow() is deprecated. Please use $OUTPUT->arrow() instead.', DEBUG_DEVELOPER);

    if (!in_array($direction, array('up', 'down', 'right', 'left', 'move'))) {
        return null;
    }

    $return = null;

    switch ($direction) {
        case 'up':
            $sortdir = 'asc';
            break;
        case 'down':
            $sortdir = 'desc';
            break;
        case 'move':
            $sortdir = 'asc';
            break;
        default:
            $sortdir = null;
            break;
    }

    // Prepare language string
    $strsort = '';
    if (empty($strsort) && !empty($sortdir)) {
        $strsort  = get_string('sort' . $sortdir, 'grades');
    }

    $return = ' ' . $OUTPUT->pix_icon('t/' . $direction, $strsort) . ' ';

    if ($return) {
        return $return;
    } else {
        echo $return;
    }
}

/**
 * @deprecated since Moodle 2.0
 */
function choose_from_menu() {
    throw new coding_exception('choose_from_menu() is removed. Please change your code to use html_writer::select().');
}

/**
 * @deprecated use $OUTPUT->help_icon_scale($courseid, $scale) instead.
 */
function print_scale_menu_helpbutton() {
    throw new coding_exception('print_scale_menu_helpbutton() can not be used any more. '.
        'Please use $OUTPUT->help_icon_scale($courseid, $scale) instead.');
}

/**
 * @deprecated use html_writer::checkbox() instead.
 */
function print_checkbox() {
    throw new coding_exception('print_checkbox() can not be used any more. Please use html_writer::checkbox() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function update_module_button() {
    throw new coding_exception('update_module_button() can not be used anymore. Activity modules should ' .
        'not add the edit module button, the link is already available in the Administration block. Themes ' .
        'can choose to display the link in the buttons row consistently for all module types.');
}

/**
 * @deprecated use $OUTPUT->navbar() instead
 */
function print_navigation () {
    throw new coding_exception('print_navigation() can not be used any more, please update use $OUTPUT->navbar() instead.');
}

/**
 * @deprecated Please use $PAGE->navabar methods instead.
 */
function build_navigation() {
    throw new coding_exception('build_navigation() can not be used any more, please use $PAGE->navbar methods instead.');
}

/**
 * @deprecated not relevant with global navigation in Moodle 2.x+
 */
function navmenu() {
    throw new coding_exception('navmenu() can not be used any more, it is no longer relevant with global navigation.');
}

/// CALENDAR MANAGEMENT  ////////////////////////////////////////////////////////////////


/**
 * @deprecated please use calendar_event::create() instead.
 */
function add_event() {
    throw new coding_exception('add_event() can not be used any more, please use calendar_event::create() instead.');
}

/**
 * @deprecated please calendar_event->update() instead.
 */
function update_event() {
    throw new coding_exception('update_event() is removed, please use calendar_event->update() instead.');
}

/**
 * @deprecated please use calendar_event->delete() instead.
 */
function delete_event() {
    throw new coding_exception('delete_event() can not be used any more, please use '.
        'calendar_event->delete() instead.');
}

/**
 * @deprecated please use calendar_event->toggle_visibility(false) instead.
 */
function hide_event() {
    throw new coding_exception('hide_event() can not be used any more, please use '.
        'calendar_event->toggle_visibility(false) instead.');
}

/**
 * @deprecated please use calendar_event->toggle_visibility(true) instead.
 */
function show_event() {
    throw new coding_exception('show_event() can not be used any more, please use '.
        'calendar_event->toggle_visibility(true) instead.');
}

/**
 * @deprecated since Moodle 2.2 use core_text::xxxx() instead.
 */
function textlib_get_instance() {
    throw new coding_exception('textlib_get_instance() can not be used any more, please use '.
        'core_text::functioname() instead.');
}

/**
 * @deprecated since 2.4
 */
function get_generic_section_name() {
    throw new coding_exception('get_generic_section_name() is deprecated. Please use appropriate functionality '
            .'from class core_courseformat\\base');
}

/**
 * @deprecated since 2.4
 */
function get_all_sections() {
    throw new coding_exception('get_all_sections() is removed. See phpdocs for this function');
}

/**
 * @deprecated since 2.4
 */
function add_mod_to_section() {
    throw new coding_exception('Function add_mod_to_section() is removed, please use course_add_cm_to_section()');
}

/**
 * @deprecated since 2.4
 */
function get_all_mods() {
    throw new coding_exception('Function get_all_mods() is removed. Use get_fast_modinfo() and get_module_types_names() instead. See phpdocs for details');
}

/**
 * @deprecated since 2.4
 */
function get_course_section() {
    throw new coding_exception('Function get_course_section() is removed. Please use course_create_sections_if_missing() and get_fast_modinfo() instead.');
}

/**
 * @deprecated since 2.4
 */
function format_weeks_get_section_dates() {
    throw new coding_exception('Function format_weeks_get_section_dates() is removed. It is not recommended to'.
            ' use it outside of format_weeks plugin');
}

/**
 * @deprecated since 2.5
 */
function get_print_section_cm_text() {
    throw new coding_exception('Function get_print_section_cm_text() is removed. Please use '.
            'cm_info::get_formatted_content() and cm_info::get_formatted_name()');
}

/**
 * @deprecated since 2.5
 */
function print_section_add_menus() {
    throw new coding_exception('Function print_section_add_menus() is removed. Please use course renderer '.
            'function course_section_add_cm_control()');
}

/**
 * @deprecated since 2.5. Please use:
 */
function make_editing_buttons() {
    throw new coding_exception('Function make_editing_buttons() is removed, please see PHPdocs in '.
            'lib/deprecatedlib.php on how to replace it');
}

/**
 * @deprecated since 2.5
 */
function print_section() {
    throw new coding_exception(
        'Function print_section() is removed.' .
        ' Please use core_courseformat\\output\\local\\content\\section' .
        ' to render a course section instead.'
    );
}

/**
 * @deprecated since 2.5
 */
function print_overview() {
    throw new coding_exception('Function print_overview() is removed. Use block course_overview to display this information');
}

/**
 * @deprecated since 2.5
 */
function print_recent_activity() {
    throw new coding_exception('Function print_recent_activity() is removed. It is not recommended to'.
            ' use it outside of block_recent_activity');
}

/**
 * @deprecated since 2.5
 */
function delete_course_module() {
    throw new coding_exception('Function delete_course_module() is removed. Please use course_delete_module() instead.');
}

/**
 * @deprecated since 2.5
 */
function update_category_button() {
    throw new coding_exception('Function update_category_button() is removed. Pages to view '.
            'and edit courses are now separate and no longer depend on editing mode.');
}

/**
 * @deprecated since 2.5
 */
function make_categories_list() {
    throw new coding_exception('Global function make_categories_list() is removed. Please use '.
        'core_course_category::make_categories_list() and core_course_category::get_parents()');
}

/**
 * @deprecated since 2.5
 */
function category_delete_move() {
    throw new coding_exception('Function category_delete_move() is removed. Please use ' .
        'core_course_category::delete_move() instead.');
}

/**
 * @deprecated since 2.5
 */
function category_delete_full() {
    throw new coding_exception('Function category_delete_full() is removed. Please use ' .
        'core_course_category::delete_full() instead.');
}

/**
 * @deprecated since 2.5
 */
function move_category() {
    throw new coding_exception('Function move_category() is removed. Please use core_course_category::change_parent() instead.');
}

/**
 * @deprecated since 2.5
 */
function course_category_hide() {
    throw new coding_exception('Function course_category_hide() is removed. Please use core_course_category::hide() instead.');
}

/**
 * @deprecated since 2.5
 */
function course_category_show() {
    throw new coding_exception('Function course_category_show() is removed. Please use core_course_category::show() instead.');
}

/**
 * @deprecated since 2.5. Please use core_course_category::get($catid, IGNORE_MISSING) or
 *     core_course_category::get($catid, MUST_EXIST).
 */
function get_course_category() {
    throw new coding_exception('Function get_course_category() is removed. Please use core_course_category::get(), ' .
        'see phpdocs for more details');
}

/**
 * @deprecated since 2.5
 */
function create_course_category() {
    throw new coding_exception('Function create_course_category() is removed. Please use core_course_category::create(), ' .
        'see phpdocs for more details');
}

/**
 * @deprecated since 2.5. Please use core_course_category::get() and core_course_category::get_children()
 */
function get_all_subcategories() {
    throw new coding_exception('Function get_all_subcategories() is removed. Please use appropriate methods() '.
        'of core_course_category class. See phpdocs for more details');
}

/**
 * @deprecated since 2.5. Please use core_course_category::get($parentid)->get_children().
 */
function get_child_categories() {
    throw new coding_exception('Function get_child_categories() is removed. Use core_course_category::get_children() or see ' .
        'phpdocs for more details.');
}

/**
 * @deprecated since 2.5
 */
function get_categories() {
    throw new coding_exception('Function get_categories() is removed. Please use ' .
            'appropriate functions from class core_course_category');
}

/**
* @deprecated since 2.5
*/
function print_course_search() {
    throw new coding_exception('Function print_course_search() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function print_my_moodle() {
    throw new coding_exception('Function print_my_moodle() is removed, please use course renderer ' .
            'function frontpage_my_courses()');
}

/**
 * @deprecated since 2.5
 */
function print_remote_course() {
    throw new coding_exception('Function print_remote_course() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function print_remote_host() {
    throw new coding_exception('Function print_remote_host() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function print_whole_category_list() {
    throw new coding_exception('Function print_whole_category_list() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function print_category_info() {
    throw new coding_exception('Function print_category_info() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function get_course_category_tree() {
    throw new coding_exception('Function get_course_category_tree() is removed, please use course ' .
            'renderer or core_course_category class, see function phpdocs for more info');
}

/**
 * @deprecated since 2.5
 */
function print_courses() {
    throw new coding_exception('Function print_courses() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function print_course() {
    throw new coding_exception('Function print_course() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function get_category_courses_array() {
    throw new coding_exception('Function get_category_courses_array() is removed, please use methods of ' .
        'core_course_category class');
}

/**
 * @deprecated since 2.5
 */
function get_category_courses_array_recursively() {
    throw new coding_exception('Function get_category_courses_array_recursively() is removed, please use ' .
        'methods of core_course_category class', DEBUG_DEVELOPER);
}

/**
 * @deprecated since Moodle 2.5 MDL-27814 - please do not use this function any more.
 */
function blog_get_context_url() {
    throw new coding_exception('Function  blog_get_context_url() is removed, getting params from context is not reliable for blogs.');
}

/**
 * @deprecated since 2.5
 */
function get_courses_wmanagers() {
    throw new coding_exception('Function get_courses_wmanagers() is removed, please use ' .
        'core_course_category::get_courses()');
}

/**
 * @deprecated since 2.5
 */
function convert_tree_to_html() {
    throw new coding_exception('Function convert_tree_to_html() is removed. Consider using class tabtree and core_renderer::render_tabtree()');
}

/**
 * @deprecated since 2.5
 */
function convert_tabrows_to_tree() {
    throw new coding_exception('Function convert_tabrows_to_tree() is removed. Consider using class tabtree');
}

/**
 * @deprecated since 2.5 - do not use, the textrotate.js will work it out automatically
 */
function can_use_rotated_text() {
    debugging('can_use_rotated_text() is removed. JS feature detection is used automatically.');
}

/**
 * @deprecated since Moodle 2.2 MDL-35009 - please do not use this function any more.
 */
function get_context_instance_by_id() {
    throw new coding_exception('get_context_instance_by_id() is now removed, please use context::instance_by_id($id) instead.');
}

/**
 * Returns system context or null if can not be created yet.
 *
 * @see context_system::instance()
 * @deprecated since 2.2
 * @param bool $cache use caching
 * @return context system context (null if context table not created yet)
 */
function get_system_context($cache = true) {
    debugging('get_system_context() is deprecated, please use context_system::instance() instead.', DEBUG_DEVELOPER);
    return context_system::instance(0, IGNORE_MISSING, $cache);
}

/**
 * @deprecated since 2.2, use $context->get_parent_context_ids() instead
 */
function get_parent_contexts() {
    throw new coding_exception('get_parent_contexts() is removed, please use $context->get_parent_context_ids() instead.');
}

/**
 * @deprecated since Moodle 2.2
 */
function get_parent_contextid() {
    throw new coding_exception('get_parent_contextid() is removed, please use $context->get_parent_context() instead.');
}

/**
 * @deprecated since 2.2
 */
function get_child_contexts() {
    throw new coding_exception('get_child_contexts() is removed, please use $context->get_child_contexts() instead.');
}

/**
 * @deprecated since 2.2
 */
function create_contexts() {
    throw new coding_exception('create_contexts() is removed, please use context_helper::create_instances() instead.');
}

/**
 * @deprecated since 2.2
 */
function cleanup_contexts() {
    throw new coding_exception('cleanup_contexts() is removed, please use context_helper::cleanup_instances() instead.');
}

/**
 * @deprecated since 2.2
 */
function build_context_path() {
    throw new coding_exception('build_context_path() is removed, please use context_helper::build_all_paths() instead.');
}

/**
 * @deprecated since 2.2
 */
function rebuild_contexts() {
    throw new coding_exception('rebuild_contexts() is removed, please use $context->reset_paths(true) instead.');
}

/**
 * @deprecated since Moodle 2.2
 */
function preload_course_contexts() {
    throw new coding_exception('preload_course_contexts() is removed, please use context_helper::preload_course() instead.');
}

/**
 * @deprecated since Moodle 2.2
 */
function context_moved() {
    throw new coding_exception('context_moved() is removed, please use context::update_moved() instead.');
}

/**
 * @deprecated since 2.2
 */
function fetch_context_capabilities() {
    throw new coding_exception('fetch_context_capabilities() is removed, please use $context->get_capabilities() instead.');
}

/**
 * @deprecated since 2.2
 */
function context_instance_preload() {
    throw new coding_exception('context_instance_preload() is removed, please use context_helper::preload_from_record() instead.');
}

/**
 * @deprecated since 2.2
 */
function get_contextlevel_name() {
    throw new coding_exception('get_contextlevel_name() is removed, please use context_helper::get_level_name() instead.');
}

/**
 * @deprecated since 2.2
 */
function print_context_name() {
    throw new coding_exception('print_context_name() is removed, please use $context->get_context_name() instead.');
}

/**
 * @deprecated since 2.2, use $context->mark_dirty() instead
 */
function mark_context_dirty() {
    throw new coding_exception('mark_context_dirty() is removed, please use $context->mark_dirty() instead.');
}

/**
 * @deprecated since Moodle 2.2
 */
function delete_context() {
    throw new coding_exception('delete_context() is removed, please use context_helper::delete_instance() ' .
            'or $context->delete_content() instead.');
}

/**
 * @deprecated since 2.2
 */
function get_context_url() {
    throw new coding_exception('get_context_url() is removed, please use $context->get_url() instead.');
}

/**
 * @deprecated since 2.2
 */
function get_course_context() {
    throw new coding_exception('get_course_context() is removed, please use $context->get_course_context(true) instead.');
}

/**
 * @deprecated since 2.2
 */
function get_user_courses_bycap() {
    throw new coding_exception('get_user_courses_bycap() is removed, please use enrol_get_users_courses() instead.');
}

/**
 * @deprecated since Moodle 2.2
 */
function get_role_context_caps() {
    throw new coding_exception('get_role_context_caps() is removed, it is really slow. Don\'t use it.');
}

/**
 * @deprecated since 2.2
 */
function get_courseid_from_context() {
    throw new coding_exception('get_courseid_from_context() is removed, please use $context->get_course_context(false) instead.');
}

/**
 * @deprecated since 2.2
 */
function context_instance_preload_sql() {
    throw new coding_exception('context_instance_preload_sql() is removed, please use context_helper::get_preload_record_columns_sql() instead.');
}

/**
 * @deprecated since 2.2
 */
function get_related_contexts_string() {
    throw new coding_exception('get_related_contexts_string() is removed, please use $context->get_parent_context_ids(true) instead.');
}

/**
 * @deprecated since 2.6
 */
function get_plugin_list_with_file() {
    throw new coding_exception('get_plugin_list_with_file() is removed, please use core_component::get_plugin_list_with_file() instead.');
}

/**
 * @deprecated since 2.6
 */
function check_browser_operating_system() {
    throw new coding_exception('check_browser_operating_system is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function check_browser_version() {
    throw new coding_exception('check_browser_version is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_device_type() {
    throw new coding_exception('get_device_type is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_device_type_list() {
    throw new coding_exception('get_device_type_list is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_selected_theme_for_device_type() {
    throw new coding_exception('get_selected_theme_for_device_type is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_device_cfg_var_name() {
    throw new coding_exception('get_device_cfg_var_name is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function set_user_device_type() {
    throw new coding_exception('set_user_device_type is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_user_device_type() {
    throw new coding_exception('get_user_device_type is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_browser_version_classes() {
    throw new coding_exception('get_browser_version_classes is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since Moodle 2.6
 */
function generate_email_supportuser() {
    throw new coding_exception('generate_email_supportuser is removed, please use core_user::get_support_user');
}

/**
 * @deprecated since Moodle 2.6
 */
function badges_get_issued_badge_info() {
    throw new coding_exception('Function badges_get_issued_badge_info() is removed. Please use core_badges_assertion class and methods to generate badge assertion.');
}

/**
 * @deprecated since 2.6
 */
function can_use_html_editor() {
    throw new coding_exception('can_use_html_editor is removed, please update your code to assume it returns true.');
}


/**
 * @deprecated since Moodle 2.7, use {@link user_count_login_failures()} instead.
 */
function count_login_failures() {
    throw new coding_exception('count_login_failures() can not be used any more, please use user_count_login_failures().');
}

/**
 * @deprecated since 2.7 MDL-33099/MDL-44088 - please do not use this function any more.
 */
function ajaxenabled() {
    throw new coding_exception('ajaxenabled() can not be used anymore. Update your code to work with JS at all times.');
}

/**
 * @deprecated Since Moodle 2.7 MDL-44070
 */
function coursemodule_visible_for_user() {
    throw new coding_exception('coursemodule_visible_for_user() can not be used any more,
            please use \core_availability\info_module::is_user_visible()');
}

/**
 * @deprecated since Moodle 2.8 MDL-36014, MDL-35618 this functionality is removed
 */
function enrol_cohort_get_cohorts() {
    throw new coding_exception('Function enrol_cohort_get_cohorts() is removed, use '.
        'cohort_get_available_cohorts() instead');
}

/**
 * @deprecated since Moodle 2.8 MDL-36014 please use cohort_can_view_cohort()
 */
function enrol_cohort_can_view_cohort() {
    throw new coding_exception('Function enrol_cohort_can_view_cohort() is removed, use cohort_can_view_cohort() instead');
}

/**
 * @deprecated since Moodle 2.8 MDL-36014 use cohort_get_available_cohorts() instead
 */
function cohort_get_visible_list() {
    throw new coding_exception('Function cohort_get_visible_list() is removed. Please use function cohort_get_available_cohorts() ".
        "that correctly checks capabilities.');
}

/**
 * @deprecated since Moodle 2.8 MDL-35618 this functionality is removed
 */
function enrol_cohort_enrol_all_users() {
    throw new coding_exception('enrol_cohort_enrol_all_users() is removed. This functionality is moved to enrol_manual.');
}

/**
 * @deprecated since Moodle 2.8 MDL-35618 this functionality is removed
 */
function enrol_cohort_search_cohorts() {
    throw new coding_exception('enrol_cohort_search_cohorts() is removed. This functionality is moved to enrol_manual.');
}

/* === Apis deprecated in since Moodle 2.9 === */

/**
 * @deprecated since Moodle 2.9 MDL-49371 - please do not use this function any more.
 */
function message_current_user_is_involved() {
    throw new coding_exception('message_current_user_is_involved() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.9 MDL-45898 - please do not use this function any more.
 */
function profile_display_badges() {
    throw new coding_exception('profile_display_badges() can not be used any more.');
}

/**
 * @deprecated since Moodle 2.9 MDL-45774 - Please do not use this function any more.
 */
function useredit_shared_definition_preferences() {
    throw new coding_exception('useredit_shared_definition_preferences() can not be used any more.');
}


/**
 * @deprecated since Moodle 2.9
 */
function calendar_normalize_tz() {
    throw new coding_exception('calendar_normalize_tz() can not be used any more, please use core_date::normalise_timezone() instead.');
}

/**
 * @deprecated since Moodle 2.9
 */
function get_user_timezone_offset() {
    throw new coding_exception('get_user_timezone_offset() can not be used any more, please use standard PHP DateTimeZone class instead');

}

/**
 * @deprecated since Moodle 2.9
 */
function get_timezone_offset() {
    throw new coding_exception('get_timezone_offset() can not be used any more, please use standard PHP DateTimeZone class instead');
}

/**
 * @deprecated since Moodle 2.9
 */
function get_list_of_timezones() {
    throw new coding_exception('get_list_of_timezones() can not be used any more, please use core_date::get_list_of_timezones() instead');
}

/**
 * @deprecated since Moodle 2.9
 */
function update_timezone_records() {
    throw new coding_exception('update_timezone_records() can not be used any more, please use standard PHP DateTime class instead');
}

/**
 * @deprecated since Moodle 2.9
 */
function calculate_user_dst_table() {
    throw new coding_exception('calculate_user_dst_table() can not be used any more, please use standard PHP DateTime class instead');
}

/**
 * @deprecated since Moodle 2.9
 */
function dst_changes_for_year() {
    throw new coding_exception('dst_changes_for_year() can not be used any more, please use standard DateTime class instead');
}

/**
 * @deprecated since Moodle 2.9
 */
function get_timezone_record() {
    throw new coding_exception('get_timezone_record() can not be used any more, please use standard PHP DateTime class instead');
}

/* === Apis deprecated since Moodle 3.0 === */
/**
 * @deprecated since Moodle 3.0 MDL-49360 - please do not use this function any more.
 */
function get_referer() {
    throw new coding_exception('get_referer() can not be used any more. Please use get_local_referer() instead.');
}

/**
 * @deprecated since Moodle 3.0 use \core_useragent::is_web_crawler instead.
 */
function is_web_crawler() {
    throw new coding_exception('is_web_crawler() can not be used any more. Please use core_useragent::is_web_crawler() instead.');
}

/**
 * @deprecated since Moodle 3.0 MDL-50287 - please do not use this function any more.
 */
function completion_cron() {
    throw new coding_exception('completion_cron() can not be used any more. Functionality has been moved to scheduled tasks.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_tags() {
    throw new coding_exception('Function coursetag_get_tags() can not be used any more. ' .
            'Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_all_tags() {
    throw new coding_exception('Function coursetag_get_all_tags() can not be used any more. Userid is no ' .
        'longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_jscript() {
    throw new coding_exception('Function coursetag_get_jscript() can not be used any more and is obsolete.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_jscript_links() {
    throw new coding_exception('Function coursetag_get_jscript_links() can not be used any more and is obsolete.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_records() {
    throw new coding_exception('Function coursetag_get_records() can not be used any more. ' .
            'Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_store_keywords() {
    throw new coding_exception('Function coursetag_store_keywords() can not be used any more. ' .
            'Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_delete_keyword() {
    throw new coding_exception('Function coursetag_delete_keyword() can not be used any more. ' .
            'Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_tagged_courses() {
    throw new coding_exception('Function coursetag_get_tagged_courses() can not be used any more. ' .
            'Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_delete_course_tags() {
    throw new coding_exception('Function coursetag_delete_course_tags() is deprecated. ' .
            'Use core_tag_tag::remove_all_item_tags().');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->update() instead
 */
function tag_type_set() {
    throw new coding_exception('tag_type_set() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->update().');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->update() instead
 */
function tag_description_set() {
    throw new coding_exception('tag_description_set() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->update().');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get_item_tags() instead
 */
function tag_get_tags() {
    throw new coding_exception('tag_get_tags() can not be used anymore. Please use ' .
        'core_tag_tag::get_item_tags().');
}

/**
 * @deprecated since 3.1
 */
function tag_get_tags_array() {
    throw new coding_exception('tag_get_tags_array() can not be used anymore. Please use ' .
        'core_tag_tag::get_item_tags_array().');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get_item_tags_array() or $OUTPUT->tag_list(core_tag_tag::get_item_tags())
 */
function tag_get_tags_csv() {
    throw new coding_exception('tag_get_tags_csv() can not be used anymore. Please use ' .
        'core_tag_tag::get_item_tags_array() or $OUTPUT->tag_list(core_tag_tag::get_item_tags()).');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get_item_tags() instead
 */
function tag_get_tags_ids() {
    throw new coding_exception('tag_get_tags_ids() can not be used anymore. Please consider using ' .
        'core_tag_tag::get_item_tags() or similar methods.');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get_by_name() or core_tag_tag::get_by_name_bulk()
 */
function tag_get_id() {
    throw new coding_exception('tag_get_id() can not be used anymore. Please use ' .
        'core_tag_tag::get_by_name() or core_tag_tag::get_by_name_bulk()');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->update() instead
 */
function tag_rename() {
    throw new coding_exception('tag_rename() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->update()');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::remove_item_tag() instead
 */
function tag_delete_instance() {
    throw new coding_exception('tag_delete_instance() can not be used anymore. Please use ' .
        'core_tag_tag::remove_item_tag()');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get_by_name()->get_tagged_items() instead
 */
function tag_find_records() {
    throw new coding_exception('tag_find_records() can not be used anymore. Please use ' .
        'core_tag_tag::get_by_name()->get_tagged_items()');
}

/**
 * @deprecated since 3.1
 */
function tag_add() {
    throw new coding_exception('tag_add() can not be used anymore. You can use ' .
        'core_tag_tag::create_if_missing(), however it should not be necessary since tags are ' .
        'created automatically when assigned to items');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::set_item_tags() or core_tag_tag::add_item_tag() instead
 */
function tag_assign() {
    throw new coding_exception('tag_assign() can not be used anymore. Please use ' .
        'core_tag_tag::set_item_tags() or core_tag_tag::add_item_tag() instead. Tag instance ' .
        'ordering should not be set manually');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->count_tagged_items() instead
 */
function tag_record_count() {
    throw new coding_exception('tag_record_count() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->count_tagged_items().');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->is_item_tagged_with() instead
 */
function tag_record_tagged_with() {
    throw new coding_exception('tag_record_tagged_with() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->is_item_tagged_with().');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->flag() instead
 */
function tag_set_flag() {
    throw new coding_exception('tag_set_flag() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->flag()');
}

/**
 * @deprecated since 3.1. Use core_tag_tag::get($tagid)->reset_flag() instead
 */
function tag_unset_flag() {
    throw new coding_exception('tag_unset_flag() can not be used anymore. Please use ' .
        'core_tag_tag::get($tagid)->reset_flag()');
}

/**
 * @deprecated since 3.1
 */
function tag_print_cloud() {
    throw new coding_exception('tag_print_cloud() can not be used anymore. Please use ' .
        'core_tag_collection::get_tag_cloud(), templateable core_tag\output\tagcloud and ' .
        'template core_tag/tagcloud.');
}

/**
 * @deprecated since 3.0
 */
function tag_autocomplete() {
    throw new coding_exception('tag_autocomplete() can not be used anymore. New form ' .
        'element "tags" does proper autocomplete.');
}

/**
 * @deprecated since 3.1
 */
function tag_print_description_box() {
    throw new coding_exception('tag_print_description_box() can not be used anymore. ' .
        'See core_tag_renderer for similar code');
}

/**
 * @deprecated since 3.1
 */
function tag_print_management_box() {
    throw new coding_exception('tag_print_management_box() can not be used anymore. ' .
        'See core_tag_renderer for similar code');
}

/**
 * @deprecated since 3.1
 */
function tag_print_search_box() {
    throw new coding_exception('tag_print_search_box() can not be used anymore. ' .
        'See core_tag_renderer for similar code');
}

/**
 * @deprecated since 3.1
 */
function tag_print_search_results() {
    throw new coding_exception('tag_print_search_results() can not be used anymore. ' .
        'In /tag/search.php the search results are printed using the core_tag/tagcloud template.');
}

/**
 * @deprecated since 3.1
 */
function tag_print_tagged_users_table() {
    throw new coding_exception('tag_print_tagged_users_table() can not be used anymore. ' .
        'See core_user_renderer for similar code');
}

/**
 * @deprecated since 3.1
 */
function tag_print_user_box() {
    throw new coding_exception('tag_print_user_box() can not be used anymore. ' .
        'See core_user_renderer for similar code');
}

/**
 * @deprecated since 3.1
 */
function tag_print_user_list() {
    throw new coding_exception('tag_print_user_list() can not be used anymore. ' .
        'See core_user_renderer for similar code');
}

/**
 * @deprecated since 3.1
 */
function tag_display_name() {
    throw new coding_exception('tag_display_name() can not be used anymore. Please use ' .
        'core_tag_tag::make_display_name().');

}

/**
 * @deprecated since 3.1
 */
function tag_normalize() {
    throw new coding_exception('tag_normalize() can not be used anymore. Please use ' .
        'core_tag_tag::normalize().');
}

/**
 * @deprecated since 3.1
 */
function tag_get_related_tags_csv() {
    throw new coding_exception('tag_get_related_tags_csv() can not be used anymore. Please ' .
        'consider looping through array or using $OUTPUT->tag_list(core_tag_tag::get_item_tags()).');
}

/**
 * @deprecated since 3.1
 */
function tag_set() {
    throw new coding_exception('tag_set() can not be used anymore. Please use ' .
        'core_tag_tag::set_item_tags().');
}

/**
 * @deprecated since 3.1
 */
function tag_set_add() {
    throw new coding_exception('tag_set_add() can not be used anymore. Please use ' .
        'core_tag_tag::add_item_tag().');
}

/**
 * @deprecated since 3.1
 */
function tag_set_delete() {
    throw new coding_exception('tag_set_delete() can not be used anymore. Please use ' .
        'core_tag_tag::remove_item_tag().');
}

/**
 * @deprecated since 3.1
 */
function tag_get() {
    throw new coding_exception('tag_get() can not be used anymore. Please use ' .
        'core_tag_tag::get() or core_tag_tag::get_by_name().');
}

/**
 * @deprecated since 3.1
 */
function tag_get_related_tags() {
    throw new coding_exception('tag_get_related_tags() can not be used anymore. Please use ' .
        'core_tag_tag::get_correlated_tags(), core_tag_tag::get_related_tags() or ' .
        'core_tag_tag::get_manual_related_tags().');
}

/**
 * @deprecated since 3.1
 */
function tag_delete() {
    throw new coding_exception('tag_delete() can not be used anymore. Please use ' .
        'core_tag_tag::delete_tags().');
}

/**
 * @deprecated since 3.1
 */
function tag_delete_instances() {
    throw new coding_exception('tag_delete_instances() can not be used anymore. Please use ' .
        'core_tag_tag::delete_instances().');
}

/**
 * @deprecated since 3.1
 */
function tag_cleanup() {
    throw new coding_exception('tag_cleanup() can not be used anymore. Please use ' .
        '\core\task\tag_cron_task::cleanup().');
}

/**
 * @deprecated since 3.1
 */
function tag_bulk_delete_instances() {
    throw new coding_exception('tag_bulk_delete_instances() can not be used anymore. Please use ' .
        '\core\task\tag_cron_task::bulk_delete_instances().');

}

/**
 * @deprecated since 3.1
 */
function tag_compute_correlations() {
    throw new coding_exception('tag_compute_correlations() can not be used anymore. Please use ' .
        'use \core\task\tag_cron_task::compute_correlations().');
}

/**
 * @deprecated since 3.1
 */
function tag_process_computed_correlation() {
    throw new coding_exception('tag_process_computed_correlation() can not be used anymore. Please use ' .
        'use \core\task\tag_cron_task::process_computed_correlation().');
}

/**
 * @deprecated since 3.1
 */
function tag_cron() {
    throw new coding_exception('tag_cron() can not be used anymore. Please use ' .
        'use \core\task\tag_cron_task::execute().');
}

/**
 * @deprecated since 3.1
 */
function tag_find_tags() {
    throw new coding_exception('tag_find_tags() can not be used anymore.');
}

/**
 * @deprecated since 3.1
 */
function tag_get_name() {
    throw new coding_exception('tag_get_name() can not be used anymore.');
}

/**
 * @deprecated since 3.1
 */
function tag_get_correlated() {
    throw new coding_exception('tag_get_correlated() can not be used anymore. Please use ' .
        'use core_tag_tag::get_correlated_tags().');

}

/**
 * @deprecated since 3.1
 */
function tag_cloud_sort() {
    throw new coding_exception('tag_cloud_sort() can not be used anymore. Similar method can ' .
        'be found in core_tag_collection::cloud_sort().');
}

/**
 * @deprecated since Moodle 3.1
 */
function events_load_def() {
    throw new coding_exception('events_load_def() has been deprecated along with all Events 1 API in favour of Events 2 API.');

}

/**
 * @deprecated since Moodle 3.1
 */
function events_queue_handler() {
    throw new coding_exception('events_queue_handler() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * @deprecated since Moodle 3.1
 */
function events_dispatch() {
    throw new coding_exception('events_dispatch() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * @deprecated since Moodle 3.1
 */
function events_process_queued_handler() {
    throw new coding_exception(
        'events_process_queued_handler() has been deprecated along with all Events 1 API in favour of Events 2 API.'
    );
}

/**
 * @deprecated since Moodle 3.1
 */
function events_update_definition() {
    throw new coding_exception(
        'events_update_definition has been deprecated along with all Events 1 API in favour of Events 2 API.'
    );
}

/**
 * @deprecated since Moodle 3.1
 */
function events_cron() {
    throw new coding_exception('events_cron() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * @deprecated since Moodle 3.1
 */
function events_trigger_legacy() {
    throw new coding_exception('events_trigger_legacy() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * @deprecated since Moodle 3.1
 */
function events_is_registered() {
    throw new coding_exception('events_is_registered() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * @deprecated since Moodle 3.1
 */
function events_pending_count() {
    throw new coding_exception('events_pending_count() has been deprecated along with all Events 1 API in favour of Events 2 API.');
}

/**
 * @deprecated since Moodle 3.0 - this is a part of clamav plugin now.
 */
function clam_message_admins() {
    throw new coding_exception('clam_message_admins() can not be used anymore. Please use ' .
        'message_admins() method of \antivirus_clamav\scanner class.');
}

/**
 * @deprecated since Moodle 3.0 - this is a part of clamav plugin now.
 */
function get_clam_error_code() {
    throw new coding_exception('get_clam_error_code() can not be used anymore. Please use ' .
        'get_clam_error_code() method of \antivirus_clamav\scanner class.');
}

/**
 * @deprecated since 3.1
 */
function course_get_cm_rename_action() {
    throw new coding_exception('course_get_cm_rename_action() can not be used anymore. Please use ' .
        'inplace_editable https://docs.moodle.org/dev/Inplace_editable.');

}

/**
 * @deprecated since Moodle 3.1
 */
function course_scale_used() {
    throw new coding_exception('course_scale_used() can not be used anymore. Plugins can ' .
        'implement <modname>_scale_used_anywhere, all implementations of <modname>_scale_used are now ignored');
}

/**
 * @deprecated since Moodle 3.1
 */
function site_scale_used() {
    throw new coding_exception('site_scale_used() can not be used anymore. Plugins can implement ' .
        '<modname>_scale_used_anywhere, all implementations of <modname>_scale_used are now ignored');
}

/**
 * @deprecated since Moodle 3.1. Use external_api::external_function_info().
 */
function external_function_info() {
    throw new coding_exception('external_function_info() can not be used any'.
        'more. Please use external_api::external_function_info() instead.');
}

/**
 * @deprecated since Moodle 3.2
 * @see csv_import_reader::load_csv_content()
 */
function get_records_csv() {
    throw new coding_exception('get_records_csv() can not be used anymore. Please use ' .
        'lib/csvlib.class.php csv_import_reader() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function put_records_csv() {
    throw new coding_exception(__FUNCTION__ . '() has been removed, please use \core\dataformat::download_data() instead');
}

/**
 * @deprecated since Moodle 3.2
 */
function css_is_colour() {
    throw new coding_exception('css_is_colour() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function css_is_width() {
    throw new coding_exception('css_is_width() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function css_sort_by_count() {
    throw new coding_exception('css_sort_by_count() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_course_contexts() {
    throw new coding_exception('message_get_course_contexts() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_remove_url_params() {
    throw new coding_exception('message_remove_url_params() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_count_messages() {
    throw new coding_exception('message_count_messages() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_count_blocked_users() {
    throw new coding_exception('message_count_blocked_users() can not be used anymore. Please use ' .
        '\core_message\api::count_blocked_users() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_contact_link() {
    throw new coding_exception('message_contact_link() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_recent_notifications() {
    throw new coding_exception('message_get_recent_notifications() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_history_link() {
    throw new coding_exception('message_history_link() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_search() {
    throw new coding_exception('message_search() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_shorten_message() {
    throw new coding_exception('message_shorten_message() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_fragment() {
    throw new coding_exception('message_get_fragment() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_history() {
    throw new coding_exception('message_get_history() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_contact_add_remove_link() {
    throw new coding_exception('message_get_contact_add_remove_link() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_contact_block_link() {
    throw new coding_exception('message_get_contact_block_link() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_mark_messages_read() {
    throw new coding_exception('message_mark_messages_read() can not be used anymore. Please use ' .
        '\core_message\api::mark_all_messages_as_read() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_can_post_message() {
    throw new coding_exception('message_can_post_message() can not be used anymore. Please use ' .
        '\core_message\api::can_send_message() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_is_user_non_contact_blocked() {
    throw new coding_exception('message_is_user_non_contact_blocked() can not be used anymore. Please use ' .
        '\core_message\api::is_user_non_contact_blocked() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function message_is_user_blocked() {
    throw new coding_exception('message_is_user_blocked() can not be used anymore. Please use ' .
        '\core_message\api::is_user_blocked() instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function print_log() {
    throw new coding_exception('print_log() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function print_mnet_log() {
    throw new coding_exception('print_mnet_log() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function print_log_csv() {
    throw new coding_exception('print_log_csv() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function print_log_xls() {
    throw new coding_exception('print_log_xls() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function print_log_ods() {
    throw new coding_exception('print_log_ods() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function build_logs_array() {
    throw new coding_exception('build_logs_array() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function get_logs_usercourse() {
    throw new coding_exception('get_logs_usercourse() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function get_logs_userday() {
    throw new coding_exception('get_logs_userday() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function get_logs() {
    throw new coding_exception('get_logs() can not be used anymore. Please use the ' .
        'report_log framework instead.');
}

/**
 * @deprecated since Moodle 3.2
 */
function prevent_form_autofill_password() {
    throw new coding_exception('prevent_form_autofill_password() can not be used anymore.');
}

/**
 * @deprecated since Moodle 3.3 MDL-57370
 */
function message_get_recent_conversations($userorid, $limitfrom = 0, $limitto = 100) {
    throw new coding_exception('message_get_recent_conversations() can not be used any more. ' .
        'Please use \core_message\api::get_conversations() instead.', DEBUG_DEVELOPER);
}

/**
 * @deprecated since Moodle 3.2
 */
function calendar_preferences_button() {
    throw new coding_exception('calendar_preferences_button() can not be used anymore. The calendar ' .
        'preferences are now linked to the user preferences page.');
}

/**
 * @deprecated since 3.3
 */
function calendar_wday_name() {
    throw new coding_exception('Function calendar_wday_name() is removed and no longer used in core.');
}

/**
 * @deprecated since 3.3
 */
function calendar_get_block_upcoming() {
    throw new coding_exception('Function calendar_get_block_upcoming() is removed,' .
        'Please see block_calendar_upcoming::get_content() for the correct API usage.');
}

/**
 * @deprecated since 3.3
 */
function calendar_print_month_selector() {
    throw new coding_exception('Function calendar_print_month_selector() is removed and can no longer used in core.');
}

/**
 * @deprecated since 3.3
 */
function calendar_cron() {
    throw new coding_exception('Function calendar_cron() is removed. Please use the core\task\calendar_cron_task instead.');
}

/**
 * @deprecated since Moodle 3.4 and removed immediately. MDL-49398.
 */
function load_course_context() {
    throw new coding_exception('load_course_context() is removed. Do not use private functions or data structures.');
}

/**
 * @deprecated since Moodle 3.4 and removed immediately. MDL-49398.
 */
function load_role_access_by_context() {
    throw new coding_exception('load_role_access_by_context() is removed. Do not use private functions or data structures.');
}

/**
 * @deprecated since Moodle 3.4 and removed immediately. MDL-49398.
 */
function dedupe_user_access() {
    throw new coding_exception('dedupe_user_access() is removed. Do not use private functions or data structures.');
}

/**
 * @deprecated since Moodle 3.4. MDL-49398.
 */
function get_user_access_sitewide() {
    throw new coding_exception('get_user_access_sitewide() is removed. Do not use private functions or data structures.');
}

/**
 * @deprecated since Moodle 3.4. MDL-59333
 */
function calendar_get_mini() {
    throw new coding_exception('calendar_get_mini() has been removed. Please update your code to use calendar_get_view.');
}

/**
 * @deprecated since Moodle 3.4. MDL-59333
 */
function calendar_get_upcoming() {
    throw new coding_exception('calendar_get_upcoming() has been removed. ' .
            'Please see block_calendar_upcoming::get_content() for the correct API usage.');
}

/**
 * @deprecated since Moodle 3.4. MDL-50666
 */
function allow_override() {
    throw new coding_exception('allow_override() has been removed. Please update your code to use core_role_set_override_allowed.');
}

/**
 * @deprecated since Moodle 3.4. MDL-50666
 */
function allow_assign() {
    throw new coding_exception('allow_assign() has been removed. Please update your code to use core_role_set_assign_allowed.');
}

/**
 * @deprecated since Moodle 3.4. MDL-50666
 */
function allow_switch() {
    throw new coding_exception('allow_switch() has been removed. Please update your code to use core_role_set_switch_allowed.');
}

/**
 * @deprecated since Moodle 3.5. MDL-61132
 */
function question_add_tops() {
    throw new coding_exception(
        'question_add_tops() has been removed. You may want to pass $top = true to get_categories_for_contexts().'
    );
}

/**
 * @deprecated since Moodle 3.5. MDL-61132
 */
function question_is_only_toplevel_category_in_context() {
    throw new coding_exception('question_is_only_toplevel_category_in_context() has been removed. '
            . 'Please update your code to use question_is_only_child_of_top_category_in_context() instead.');
}

/**
 * @deprecated since Moodle 3.5
 */
function message_move_userfrom_unread2read() {
    throw new coding_exception('message_move_userfrom_unread2read() has been removed.');
}

/**
 * @deprecated since Moodle 3.5
 */
function message_get_blocked_users() {
    throw new coding_exception(
        'message_get_blocked_users() has been removed, please use \core_message\api::get_blocked_users() instead.'
    );
}

/**
 * @deprecated since Moodle 3.5
 */
function message_get_contacts() {
    throw new coding_exception('message_get_contacts() has been removed.');
}

/**
 * @deprecated since Moodle 3.5
 */
function message_mark_message_read() {
    throw new coding_exception('message_mark_message_read() has been removed, please use \core_message\api::mark_message_as_read()
        or \core_message\api::mark_notification_as_read().');
}

/**
 * @deprecated since Moodle 3.5
 */
function message_can_delete_message() {
    throw new coding_exception(
        'message_can_delete_message() has been removed, please use \core_message\api::can_delete_message() instead.'
    );
}

/**
 * @deprecated since Moodle 3.5
 */
function message_delete_message() {
    throw new coding_exception(
        'message_delete_message() has been removed, please use \core_message\api::delete_message() instead.'
    );
}

/**
 * @deprecated since 3.6
 */
function calendar_get_all_allowed_types() {
    throw new coding_exception(
        'calendar_get_all_allowed_types() has been removed. Please use calendar_get_allowed_types() instead.'
    );

}

/**
 * @deprecated since Moodle 3.6.
 */
function groups_get_all_groups_for_courses() {
    throw new coding_exception(
        'groups_get_all_groups_for_courses() has been removed and can not be used anymore.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 */
function events_get_cached() {
    throw new coding_exception(
        'Events API using $handlers array has been removed in favour of Events 2 API, please use it instead.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 */
function events_uninstall() {
    throw new coding_exception(
        'Events API using $handlers array has been removed in favour of Events 2 API, please use it instead.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 */
function events_cleanup() {
    throw new coding_exception(
        'Events API using $handlers array has been removed in favour of Events 2 API, please use it instead.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 */
function events_dequeue() {
    throw new coding_exception(
        'Events API using $handlers array has been removed in favour of Events 2 API, please use it instead.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 */
function events_get_handlers() {
    throw new coding_exception(
        'Events API using $handlers array has been removed in favour of Events 2 API, please use it instead.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the get_roles_used_in_context().
 */
function get_roles_on_exact_context() {
    throw new coding_exception(
        'get_roles_on_exact_context() has been removed, please use get_roles_used_in_context() instead.'
    );
}

/**
 * @deprecated since Moodle 3.6. Please use the get_roles_used_in_context().
 */
function get_roles_with_assignment_on_context() {
    throw new coding_exception(
        'get_roles_with_assignment_on_context() has been removed, please use get_roles_used_in_context() instead.'
    );
}

/**
 * @deprecated since Moodle 3.6
 */
function message_add_contact() {
    throw new coding_exception(
        'message_add_contact() has been removed. Please use \core_message\api::create_contact_request() instead. ' .
        'If you wish to block or unblock a user please use \core_message\api::is_blocked() and ' .
        '\core_message\api::block_user() or \core_message\api::unblock_user() respectively.'
    );
}

/**
 * @deprecated since Moodle 3.6
 */
function message_remove_contact() {
    throw new coding_exception(
        'message_remove_contact() has been removed. Please use \core_message\api::remove_contact() instead.'
    );
}

/**
 * @deprecated since Moodle 3.6
 */
function message_unblock_contact() {
    throw new coding_exception(
        'message_unblock_contact() has been removed. Please use \core_message\api::unblock_user() instead.'
    );
}

/**
 * @deprecated since Moodle 3.6
 */
function message_block_contact() {
    throw new coding_exception(
        'message_block_contact() has been removed. Please use \core_message\api::is_blocked() and ' .
        '\core_message\api::block_user() instead.'
    );
}

/**
 * @deprecated since Moodle 3.6
 */
function message_get_contact() {
    throw new coding_exception(
        'message_get_contact() has been removed. Please use \core_message\api::get_contact() instead.'
    );
}

/**
 * @deprecated since Moodle 3.7
 */
function get_courses_page() {
    throw new coding_exception(
        'Function get_courses_page() has been removed. Please use core_course_category::get_courses() ' .
        'or core_course_category::search_courses()'
    );
}

/**
 * @deprecated since Moodle 3.8
 */
function report_insights_context_insights(\context $context) {
    throw new coding_exception(
        'Function report_insights_context_insights() ' .
        'has been removed. Please use \core_analytics\manager::cached_models_with_insights instead'
    );
}

/**
 * @deprecated since 3.9
 */
function get_module_metadata() {
    throw new coding_exception(
        'get_module_metadata() has been removed. Please use \core_course\local\service\content_item_service instead.');
}

/**
 * @deprecated since Moodle 3.9 MDL-63580. Please use the \core\task\manager::run_from_cli($task).
 */
function cron_run_single_task() {
    throw new coding_exception(
        'cron_run_single_task() has been removed. Please use \\core\task\manager::run_from_cli() instead.'
    );
}

/**
 * @deprecated since Moodle 3.9 MDL-52846. Please use new task API.
 */
function cron_execute_plugin_type() {
    throw new coding_exception(
        'cron_execute_plugin_type() has been removed. Please, use the Task API instead: ' .
        'https://moodledev.io/docs/apis/subsystems/task.'
    );
}

/**
 * @deprecated since Moodle 3.9 MDL-52846. Please use new task API.
 */
function cron_bc_hack_plugin_functions() {
    throw new coding_exception(
        'cron_bc_hack_plugin_functions() has been removed. Please, use the Task API instead: ' .
        'https://moodledev.io/docs/apis/subsystems/task.'
    );
}

/**
 * @deprecated since Moodle 3.9 MDL-68612 - See \core_user\table\participants_search for an improved way to fetch participants.
 */
function user_get_participants_sql() {
    $deprecatedtext = __FUNCTION__ . '() has been removed. ' .
                 'Please use \core\table\participants_search::class with table filtersets instead.';
    throw new coding_exception($deprecatedtext);
}

/**
 * @deprecated since Moodle 3.9 MDL-68612 - See \core_user\table\participants_search for an improved way to fetch participants.
 */
function user_get_total_participants() {
    $deprecatedtext = __FUNCTION__ . '() has been removed. ' .
                      'Please use \core\table\participants_search::class with table filtersets instead.';
    throw new coding_exception($deprecatedtext);
}

/**
 * @deprecated since Moodle 3.9 MDL-68612 - See \core_user\table\participants_search for an improved way to fetch participants.
 */
function user_get_participants() {
    $deprecatedtext = __FUNCTION__ . '() has been removed. ' .
                      'Please use \core\table\participants_search::class with table filtersets instead.';
    throw new coding_exception($deprecatedtext);
}

/**
 * @deprecated Since Moodle 3.9. MDL-65835
 */
function plagiarism_save_form_elements() {
    throw new coding_exception(
        'Function plagiarism_save_form_elements() has been removed. ' .
        'Please use {plugin name}_coursemodule_edit_post_actions() instead.'
    );
}

/**
 * @deprecated Since Moodle 3.9. MDL-65835
 */
function plagiarism_get_form_elements_module() {
    throw new coding_exception(
        'Function plagiarism_get_form_elements_module() has been removed. ' .
        'Please use {plugin name}_coursemodule_standard_elements() instead.'
    );
}

/**
 * @deprecated Since Moodle 3.9 - MDL-68500 please use {@see \core\dataformat::download_data}
 */
function download_as_dataformat() {
    throw new coding_exception(__FUNCTION__ . '() has been removed, please use \core\dataformat::download_data() instead');
}

/**
 * @deprecated since Moodle 3.10
 */
function make_categories_options() {
    throw new coding_exception(__FUNCTION__ . '() has been removed. ' .
        'Please use \core_course_category::make_categories_list() instead.');
}

/**
 * @deprecated since 3.10
 */
function message_count_unread_messages() {
    throw new coding_exception('message_count_unread_messages has been removed.');
}

/**
 * @deprecated since 3.10
 */
function serialise_tool_proxy() {
    throw new coding_exception('serialise_tool_proxy has been removed.');
}

/**
 * @deprecated Since Moodle 3.11.
 */
function badges_check_backpack_accessibility() {
    throw new coding_exception('badges_check_backpack_accessibility() can not be used any more, it was only used for OBv1.0');
}

/**
 * @deprecated Since Moodle 3.11.
 */
function badges_setup_backpack_js() {
    throw new coding_exception('badges_setup_backpack_js() can not be used any more, it was only used for OBv1.0');
}

/**
 * @deprecated Since Moodle 3.11.
 */
function badges_local_backpack_js() {
    throw new coding_exception('badges_local_backpack_js() can not be used any more, it was only used for OBv1.0');
}

/**
 * @deprecated since Moodle 3.11 MDL-45242
 */
function get_extra_user_fields() {
    throw new coding_exception('get_extra_user_fields() has been removed. Please use the \core_user\fields API instead.');
}

/**
 * @deprecated since Moodle 3.11 MDL-45242
 */
function get_extra_user_fields_sql() {
    throw new coding_exception('get_extra_user_fields_sql() has been removed. Please use the \core_user\fields API instead.');
}

/**
 * @deprecated since Moodle 3.11 MDL-45242
 */
function get_user_field_name() {
    throw new coding_exception('get_user_field_name() has been removed. Please use \core_user\fields::get_display_name() instead');
}

/**
 * @deprecated since Moodle 3.11 MDL-45242
 */
function get_all_user_name_fields() {
    throw new coding_exception('get_all_user_name_fields() is deprecated. Please use the \core_user\fields API instead');
}

/**
 * @deprecated since Moodle 3.11 MDL-71051
 */
function profile_display_fields() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 3.11 MDL-71051
 */
function profile_edit_category() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 3.11 MDL-71051
 */
function profile_edit_field() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71953
 */
function calendar_process_subscription_row() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71953
 */
function calendar_import_icalendar_events() {
    throw new coding_exception(__FUNCTION__ . '() has been removed. Please use calendar_import_events_from_ical() instead.');
}

/**
 * @deprecated since Moodle 4.0. Tabs navigation has been replaced with tertiary navigation.
 */
function grade_print_tabs() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0. Dropdown box navigation has been replaced with tertiary navigation.
 */
function print_grade_plugin_selector() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0. Please use {@link course_modinfo::purge_course_section_cache_by_id()}
 *             or {@link course_modinfo::purge_course_section_cache_by_number()} instead.
 */
function course_purge_section_cache() {
    throw new coding_exception(__FUNCTION__ . '() has been removed. ' .
        'Please use course_modinfo::purge_course_section_cache_by_id() ' .
        'or course_modinfo::purge_course_section_cache_by_number() instead.');
}

/**
 * @deprecated since Moodle 4.0. Please use {@link course_modinfo::purge_course_module_cache()} instead.
 */
function course_purge_module_cache() {
    throw new coding_exception(__FUNCTION__ . '() has been removed. ' .
        'Please use course_modinfo::purge_course_module_cache() instead.');
}

/**
 * @deprecated since Moodle 4.0. Please use {@link course_modinfo::get_array_of_activities()} instead.
 */
function get_array_of_activities() {
    throw new coding_exception(__FUNCTION__ . '() has been removed. ' .
        'Please use course_modinfo::get_array_of_activities() instead.');
}

/**
 * Abort execution by throwing of a general exception,
 * default exception handler displays the error message in most cases.
 *
 * @deprecated since Moodle 4.1
 * @todo MDL-74484 Final deprecation in Moodle 4.5.
 * @param string $errorcode The name of the language string containing the error message.
 *      Normally this should be in the error.php lang file.
 * @param string $module The language file to get the error message from.
 * @param string $link The url where the user will be prompted to continue.
 *      If no url is provided the user will be directed to the site index page.
 * @param object $a Extra words and phrases that might be required in the error string
 * @param string $debuginfo optional debugging information
 * @return void, always throws exception!
 */
function print_error($errorcode, $module = 'error', $link = '', $a = null, $debuginfo = null) {
    debugging("The function print_error() is deprecated. " .
            "Please throw a new moodle_exception instance instead.", DEBUG_DEVELOPER);
    throw new \moodle_exception($errorcode, $module, $link, $a, $debuginfo);
}

/**
 * Execute cron tasks
 *
 * @param int|null $keepalive The keepalive time for this cron run.
 * @deprecated since 4.2 Use \core\cron::run_main_process() instead.
 */
function cron_run(?int $keepalive = null): void {
    debugging(
        'The cron_run() function is deprecated. Please use \core\cron::run_main_process() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_main_process($keepalive);
}

/**
 * Execute all queued scheduled tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 * @deprecated since 4.2 Use \core\cron::run_scheduled_tasks() instead.
 */
function cron_run_scheduled_tasks(int $timenow) {
    debugging(
        'The cron_run_scheduled_tasks() function is deprecated. Please use \core\cron::run_scheduled_tasks() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_scheduled_tasks($timenow);
}

/**
 * Execute all queued adhoc tasks, applying necessary concurrency limits and time limits.
 *
 * @param   int     $timenow The time this process started.
 * @param   int     $keepalive Keep this function alive for N seconds and poll for new adhoc tasks.
 * @param   bool    $checklimits Should we check limits?
 * @deprecated since 4.2 Use \core\cron::run_adhoc_tasks() instead.
 */
function cron_run_adhoc_tasks(int $timenow, $keepalive = 0, $checklimits = true) {
    debugging(
        'The cron_run_adhoc_tasks() function is deprecated. Please use \core\cron::run_adhoc_tasks() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_adhoc_tasks($timenow, $keepalive, $checklimits);
}

/**
 * Shared code that handles running of a single scheduled task within the cron.
 *
 * Not intended for calling directly outside of this library!
 *
 * @param \core\task\task_base $task
 * @deprecated since 4.2 Use \core\cron::run_inner_scheduled_task() instead.
 */
function cron_run_inner_scheduled_task(\core\task\task_base $task) {
    debugging(
        'The cron_run_inner_scheduled_task() function is deprecated. Please use \core\cron::run_inner_scheduled_task() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_inner_scheduled_task($task);
}

/**
 * Shared code that handles running of a single adhoc task within the cron.
 *
 * @param \core\task\adhoc_task $task
 * @deprecated since 4.2 Use \core\cron::run_inner_adhoc_task() instead.
 */
function cron_run_inner_adhoc_task(\core\task\adhoc_task $task) {
    debugging(
        'The cron_run_inner_adhoc_task() function is deprecated. Please use \core\cron::run_inner_adhoc_task() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::run_inner_adhoc_task($task);
}

/**
 * Sets the process title
 *
 * This makes it very easy for a sysadmin to immediately see what task
 * a cron process is running at any given moment.
 *
 * @param string $title process status title
 * @deprecated since 4.2 Use \core\cron::set_process_title() instead.
 */
function cron_set_process_title(string $title) {
    debugging(
        'The cron_set_process_title() function is deprecated. Please use \core\cron::set_process_title() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::set_process_title($title);
}

/**
 * Output some standard information during cron runs. Specifically current time
 * and memory usage. This method also does gc_collect_cycles() (before displaying
 * memory usage) to try to help PHP manage memory better.
 *
 * @deprecated since 4.2 Use \core\cron::trace_time_and_memory() instead.
 */
function cron_trace_time_and_memory() {
    debugging(
        'The cron_trace_time_and_memory() function is deprecated. Please use \core\cron::trace_time_and_memory() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::trace_time_and_memory();
}

/**
 * Prepare the output renderer for the cron run.
 *
 * This involves creating a new $PAGE, and $OUTPUT fresh for each task and prevents any one task from influencing
 * any other.
 *
 * @param   bool    $restore Whether to restore the original PAGE and OUTPUT
 * @deprecated since 4.2 Use \core\cron::prepare_core_renderer() instead.
 */
function cron_prepare_core_renderer($restore = false) {
    debugging(
        'The cron_prepare_core_renderer() function is deprecated. Please use \core\cron::prepare_core_renderer() instead.',
        DEBUG_DEVELOPER
    );
    \core\cron::prepare_core_renderer($restore);
}

/**
 * Sets up current user and course environment (lang, etc.) in cron.
 * Do not use outside of cron script!
 *
 * @param stdClass $user full user object, null means default cron user (admin),
 *                 value 'reset' means reset internal static caches.
 * @param stdClass $course full course record, null means $SITE
 * @param bool $leavepagealone If specified, stops it messing with global page object
 * @deprecated since 4.2. Use \core\core::setup_user() instead.
 * @return void
 */
function cron_setup_user($user = null, $course = null, $leavepagealone = false) {
    debugging(
        'The cron_setup_user() function is deprecated. ' .
            'Please use \core\cron::setup_user() and reset_user_cache() as appropriate instead.',
        DEBUG_DEVELOPER
    );

    if ($user === 'reset') {
        \core\cron::reset_user_cache();
        return;
    }

    \core\cron::setup_user($user, $course, $leavepagealone);
}

/**
 * Get OAuth2 services for the external backpack.
 *
 * @return array
 * @throws coding_exception
 * @deprecated since 4.3.
 */
function badges_get_oauth2_service_options() {
    debugging(
        'badges_get_oauth2_service_options() is deprecated. Don\'t use it.',
        DEBUG_DEVELOPER
    );
    global $DB;

    $issuers = core\oauth2\api::get_all_issuers();
    $options = ['' => 'None'];
    foreach ($issuers as $issuer) {
        $options[$issuer->get('id')] = $issuer->get('name');
    }

    return $options;
}

/**
 * Checks if the given device has a theme defined in config.php.
 *
 * @param string $device The device
 * @deprecated since 4.3.
 * @return bool
 */
function theme_is_device_locked($device) {
    debugging(
        __FUNCTION__ . '() is deprecated.' .
            'All functions associated with device specific themes are being removed.',
        DEBUG_DEVELOPER
    );
    global $CFG;
    $themeconfigname = core_useragent::get_device_type_cfg_var_name($device);
    return isset($CFG->config_php_settings[$themeconfigname]);
}

/**
 * Returns the theme named defined in config.php for the given device.
 *
 * @param string $device The device
 * @deprecated since 4.3.
 * @return string or null
 */
function theme_get_locked_theme_for_device($device) {
    debugging(
        __FUNCTION__ . '() is deprecated.' .
            'All functions associated with device specific themes are being removed.',
        DEBUG_DEVELOPER
    );
    global $CFG;

    if (!theme_is_device_locked($device)) {
        return null;
    }

    $themeconfigname = core_useragent::get_device_type_cfg_var_name($device);
    return $CFG->config_php_settings[$themeconfigname];
}

/**
 * Try to generate cryptographically secure pseudo-random bytes.
 *
 * Note this is achieved by fallbacking between:
 *  - PHP 7 random_bytes().
 *  - OpenSSL openssl_random_pseudo_bytes().
 *  - In house random generator getting its entropy from various, hard to guess, pseudo-random sources.
 *
 * @param int $length requested length in bytes
 * @deprecated since 4.3.
 * @return string binary data
 */
function random_bytes_emulate($length) {
    debugging(
            __FUNCTION__ . '() is deprecated.' .
            'Please use random_bytes instead.',
            DEBUG_DEVELOPER
    );
    return random_bytes($length);
}

/**
 * @deprecated since Moodle 4.0
 */
function question_preview_url() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function question_preview_popup_params() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function question_hash() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71573
 */
function question_make_export_url() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0
 */
function question_get_export_single_question_url() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_remove_stale_questions_from_category() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function flatten_category_tree() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function add_indented_names() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_category_select_menu() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function get_categories_for_contexts() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_category_options() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_add_context_in_key() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}

/**
 * @deprecated since Moodle 4.0 MDL-71585
 */
function question_fix_top_names() {
    throw new coding_exception(__FUNCTION__ . '() has been removed.');
}
