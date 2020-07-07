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
 * This is a whitelist of components that are part of the core and their
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
 * @deprecated use the text formatting in a standard way instead (http://docs.moodle.org/dev/Output_functions)
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
 * Unzip one zip file to a destination dir
 * Both parameters must be FULL paths
 * If destination isn't specified, it will be the
 * SAME directory where the zip file resides.
 *
 * @global object
 * @param string $zipfile The zip file to unzip
 * @param string $destination The location to unzip to
 * @param bool $showstatus_ignored Unused
 * @deprecated since 2.0 MDL-15919
 */
function unzip_file($zipfile, $destination = '', $showstatus_ignored = true) {
    debugging(__FUNCTION__ . '() is deprecated. '
            . 'Please use the application/zip file_packer implementation instead.', DEBUG_DEVELOPER);

    // Extract everything from zipfile.
    $path_parts = pathinfo(cleardoubleslashes($zipfile));
    $zippath = $path_parts["dirname"];       //The path of the zip file
    $zipfilename = $path_parts["basename"];  //The name of the zip file
    $extension = $path_parts["extension"];    //The extension of the file

    //If no file, error
    if (empty($zipfilename)) {
        return false;
    }

    //If no extension, error
    if (empty($extension)) {
        return false;
    }

    //Clear $zipfile
    $zipfile = cleardoubleslashes($zipfile);

    //Check zipfile exists
    if (!file_exists($zipfile)) {
        return false;
    }

    //If no destination, passed let's go with the same directory
    if (empty($destination)) {
        $destination = $zippath;
    }

    //Clear $destination
    $destpath = rtrim(cleardoubleslashes($destination), "/");

    //Check destination path exists
    if (!is_dir($destpath)) {
        return false;
    }

    $packer = get_file_packer('application/zip');

    $result = $packer->extract_to_pathname($zipfile, $destpath);

    if ($result === false) {
        return false;
    }

    foreach ($result as $status) {
        if ($status !== true) {
            return false;
        }
    }

    return true;
}

/**
 * Zip an array of files/dirs to a destination zip file
 * Both parameters must be FULL paths to the files/dirs
 *
 * @global object
 * @param array $originalfiles Files to zip
 * @param string $destination The destination path
 * @return bool Outcome
 *
 * @deprecated since 2.0 MDL-15919
 */
function zip_files($originalfiles, $destination) {
    debugging(__FUNCTION__ . '() is deprecated. '
            . 'Please use the application/zip file_packer implementation instead.', DEBUG_DEVELOPER);

    // Extract everything from destination.
    $path_parts = pathinfo(cleardoubleslashes($destination));
    $destpath = $path_parts["dirname"];       //The path of the zip file
    $destfilename = $path_parts["basename"];  //The name of the zip file
    $extension = $path_parts["extension"];    //The extension of the file

    //If no file, error
    if (empty($destfilename)) {
        return false;
    }

    //If no extension, add it
    if (empty($extension)) {
        $extension = 'zip';
        $destfilename = $destfilename.'.'.$extension;
    }

    //Check destination path exists
    if (!is_dir($destpath)) {
        return false;
    }

    //Check destination path is writable. TODO!!

    //Clean destination filename
    $destfilename = clean_filename($destfilename);

    //Now check and prepare every file
    $files = array();
    $origpath = NULL;

    foreach ($originalfiles as $file) {  //Iterate over each file
        //Check for every file
        $tempfile = cleardoubleslashes($file); // no doubleslashes!
        //Calculate the base path for all files if it isn't set
        if ($origpath === NULL) {
            $origpath = rtrim(cleardoubleslashes(dirname($tempfile)), "/");
        }
        //See if the file is readable
        if (!is_readable($tempfile)) {  //Is readable
            continue;
        }
        //See if the file/dir is in the same directory than the rest
        if (rtrim(cleardoubleslashes(dirname($tempfile)), "/") != $origpath) {
            continue;
        }
        //Add the file to the array
        $files[] = $tempfile;
    }

    $zipfiles = array();
    $start = strlen($origpath)+1;
    foreach($files as $file) {
        $zipfiles[substr($file, $start)] = $file;
    }

    $packer = get_file_packer('application/zip');

    return $packer->archive_to_pathname($zipfiles, $destpath . '/' . $destfilename);
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
            print_error() instead of error()');
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
    throw new coding_exception('get_generic_section_name() is deprecated. Please use appropriate functionality from class format_base');
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
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * $actions = course_get_cm_edit_actions($mod, $indent, $section);
 * return ' ' . $courserenderer->course_section_cm_edit_actions($actions);
 */
function make_editing_buttons() {
    throw new coding_exception('Function make_editing_buttons() is removed, please see PHPdocs in '.
            'lib/deprecatedlib.php on how to replace it');
}

/**
 * @deprecated since 2.5
 */
function print_section() {
    throw new coding_exception('Function print_section() is removed. Please use course renderer function '.
            'course_section_cm_list() instead.');
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
 * @see download_as_dataformat (lib/dataformatlib.php)
 */
function put_records_csv() {
    throw new coding_exception('put_records_csv() can not be used anymore. Please use ' .
        'lib/dataformatlib.php download_as_dataformat() instead.');
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
function message_page_type_list() {
    throw new coding_exception('message_page_type_list() can not be used anymore.');
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
 * Returns list of courses, for whole site, or category
 *
 * Similar to get_courses, but allows paging
 * Important: Using c.* for fields is extremely expensive because
 *            we are using distinct. You almost _NEVER_ need all the fields
 *            in such a large SELECT
 *
 * @deprecated since Moodle 3.7
 * @todo The final deprecation of this function will take place in Moodle 41 - see MDL-65319.
 *
 * @param string|int $categoryid Either a category id or 'all' for everything
 * @param string $sort A field and direction to sort by
 * @param string $fields The additional fields to return
 * @param int $totalcount Reference for the number of courses
 * @param string $limitfrom The course to start from
 * @param string $limitnum The number of courses to limit to
 * @return array Array of courses
 */
function get_courses_page($categoryid="all", $sort="c.sortorder ASC", $fields="c.*",
                          &$totalcount, $limitfrom="", $limitnum="") {
    debugging('Function get_courses_page() is deprecated. Please use core_course_category::get_courses() ' .
        'or core_course_category::search_courses()', DEBUG_DEVELOPER);
    global $USER, $CFG, $DB;

    $params = array();

    $categoryselect = "";
    if ($categoryid !== "all" && is_numeric($categoryid)) {
        $categoryselect = "WHERE c.category = :catid";
        $params['catid'] = $categoryid;
    } else {
        $categoryselect = "";
    }

    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
    $params['contextlevel'] = CONTEXT_COURSE;

    $totalcount = 0;
    if (!$limitfrom) {
        $limitfrom = 0;
    }
    $visiblecourses = array();

    $sql = "SELECT $fields $ccselect
              FROM {course} c
              $ccjoin
           $categoryselect
          ORDER BY $sort";

    // Pull out all course matching the cat.
    $rs = $DB->get_recordset_sql($sql, $params);
    // Iteration will have to be done inside loop to keep track of the limitfrom and limitnum.
    foreach ($rs as $course) {
        context_helper::preload_from_record($course);
        if (core_course_category::can_view_course_info($course)) {
            $totalcount++;
            if ($totalcount > $limitfrom && (!$limitnum or count($visiblecourses) < $limitnum)) {
                $visiblecourses [$course->id] = $course;
            }
        }
    }
    $rs->close();
    return $visiblecourses;
}

/**
 * Returns the models that generated insights in the provided context.
 *
 * @deprecated since Moodle 3.8 MDL-66091 - please do not use this function any more.
 * @todo MDL-65799 This will be deleted in Moodle 4.2
 * @see \core_analytics\manager::cached_models_with_insights
 * @param  \context $context
 * @return int[]
 */
function report_insights_context_insights(\context $context) {

    debugging('report_insights_context_insights is deprecated. Please use ' .
        '\core_analytics\manager::cached_models_with_insights instead', DEBUG_DEVELOPER);

    return \core_analytics\manager::cached_models_with_insights($context);
}

/**
 * Retrieve all metadata for the requested modules
 *
 * @deprecated since 3.9.
 * @param object $course The Course
 * @param array $modnames An array containing the list of modules and their
 * names
 * @param int $sectionreturn The section to return to
 * @return array A list of stdClass objects containing metadata about each
 * module
 */
function get_module_metadata($course, $modnames, $sectionreturn = null) {
    global $OUTPUT;

    debugging('get_module_metadata is deprecated. Please use \core_course\local\service\content_item_service instead.');

    // get_module_metadata will be called once per section on the page and courses may show
    // different modules to one another
    static $modlist = array();
    if (!isset($modlist[$course->id])) {
        $modlist[$course->id] = array();
    }

    $return = array();
    $urlbase = new moodle_url('/course/mod.php', array('id' => $course->id, 'sesskey' => sesskey()));
    if ($sectionreturn !== null) {
        $urlbase->param('sr', $sectionreturn);
    }
    foreach($modnames as $modname => $modnamestr) {
        if (!course_allowed_module($course, $modname)) {
            continue;
        }
        if (isset($modlist[$course->id][$modname])) {
            // This module is already cached
            $return += $modlist[$course->id][$modname];
            continue;
        }
        $modlist[$course->id][$modname] = array();

        // Create an object for a default representation of this module type in the activity chooser. It will be used
        // if module does not implement callback get_shortcuts() and it will also be passed to the callback if it exists.
        $defaultmodule = new stdClass();
        $defaultmodule->title = $modnamestr;
        $defaultmodule->name = $modname;
        $defaultmodule->link = new moodle_url($urlbase, array('add' => $modname));
        $defaultmodule->icon = $OUTPUT->pix_icon('icon', '', $defaultmodule->name, array('class' => 'icon'));
        $sm = get_string_manager();
        if ($sm->string_exists('modulename_help', $modname)) {
            $defaultmodule->help = get_string('modulename_help', $modname);
            if ($sm->string_exists('modulename_link', $modname)) {  // Link to further info in Moodle docs.
                $link = get_string('modulename_link', $modname);
                $linktext = get_string('morehelp');
                $defaultmodule->help .= html_writer::tag('div',
                    $OUTPUT->doc_link($link, $linktext, true), array('class' => 'helpdoclink'));
            }
        }
        $defaultmodule->archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);

        // Each module can implement callback modulename_get_shortcuts() in its lib.php and return the list
        // of elements to be added to activity chooser.
        $items = component_callback($modname, 'get_shortcuts', array($defaultmodule), null);
        if ($items !== null) {
            foreach ($items as $item) {
                // Add all items to the return array. All items must have different links, use them as a key in the return array.
                if (!isset($item->archetype)) {
                    $item->archetype = $defaultmodule->archetype;
                }
                if (!isset($item->icon)) {
                    $item->icon = $defaultmodule->icon;
                }
                // If plugin returned the only one item with the same link as default item - cache it as $modname,
                // otherwise append the link url to the module name.
                $item->name = (count($items) == 1 &&
                    $item->link->out() === $defaultmodule->link->out()) ? $modname : $modname . ':' . $item->link;

                // If the module provides the helptext property, append it to the help text to match the look and feel
                // of the default course modules.
                if (isset($item->help) && isset($item->helplink)) {
                    $linktext = get_string('morehelp');
                    $item->help .= html_writer::tag('div',
                        $OUTPUT->doc_link($item->helplink, $linktext, true), array('class' => 'helpdoclink'));
                }
                $modlist[$course->id][$modname][$item->name] = $item;
            }
            $return += $modlist[$course->id][$modname];
            // If get_shortcuts() callback is defined, the default module action is not added.
            // It is a responsibility of the callback to add it to the return value unless it is not needed.
            continue;
        }

        // The callback get_shortcuts() was not found, use the default item for the activity chooser.
        $modlist[$course->id][$modname][$modname] = $defaultmodule;
        $return[$modname] = $defaultmodule;
    }

    core_collator::asort_objects_by_property($return, 'title');
    return $return;
}

/**
 * Runs a single cron task. This function assumes it is displaying output in pseudo-CLI mode.
 *
 * The function will fail if the task is disabled.
 *
 * Warning: Because this function closes the browser session, it may not be safe to continue
 * with other processing (other than displaying the rest of the page) after using this function!
 *
 * @deprecated since Moodle 3.9 MDL-63580. Please use the \core\task\manager::run_from_cli($task).
 * @todo final deprecation. To be removed in Moodle 4.3 MDL-63594.
 * @param \core\task\scheduled_task $task Task to run
 * @return bool True if cron run successful
 */
function cron_run_single_task(\core\task\scheduled_task $task) {
    debugging('cron_run_single_task() is deprecated. Please use \\core\task\manager::run_from_cli() instead.',
        DEBUG_DEVELOPER);
    return \core\task\manager::run_from_cli($task);
}

/**
 * Executes cron functions for a specific type of plugin.
 *
 * @param string $plugintype Plugin type (e.g. 'report')
 * @param string $description If specified, will display 'Starting (whatever)'
 *   and 'Finished (whatever)' lines, otherwise does not display
 *
 * @deprecated since Moodle 3.9 MDL-52846. Please use new task API.
 * @todo MDL-61165 This will be deleted in Moodle 4.3.
 */
function cron_execute_plugin_type($plugintype, $description = null) {
    global $DB;

    // Get list from plugin => function for all plugins.
    $plugins = get_plugin_list_with_function($plugintype, 'cron');

    // Modify list for backward compatibility (different files/names).
    $plugins = cron_bc_hack_plugin_functions($plugintype, $plugins);

    // Return if no plugins with cron function to process.
    if (!$plugins) {
        return;
    }

    if ($description) {
        mtrace('Starting '.$description);
    }

    foreach ($plugins as $component => $cronfunction) {
        $dir = core_component::get_component_directory($component);

        // Get cron period if specified in version.php, otherwise assume every cron.
        $cronperiod = 0;
        if (file_exists("$dir/version.php")) {
            $plugin = new stdClass();
            include("$dir/version.php");
            if (isset($plugin->cron)) {
                $cronperiod = $plugin->cron;
            }
        }

        // Using last cron and cron period, don't run if it already ran recently.
        $lastcron = get_config($component, 'lastcron');
        if ($cronperiod && $lastcron) {
            if ($lastcron + $cronperiod > time()) {
                // Do not execute cron yet.
                continue;
            }
        }

        mtrace('Processing cron function for ' . $component . '...');
        debugging("Use of legacy cron is deprecated ($cronfunction). Please use scheduled tasks.", DEBUG_DEVELOPER);
        cron_trace_time_and_memory();
        $pre_dbqueries = $DB->perf_get_queries();
        $pre_time = microtime(true);

        $cronfunction();

        mtrace("done. (" . ($DB->perf_get_queries() - $pre_dbqueries) . " dbqueries, " .
                round(microtime(true) - $pre_time, 2) . " seconds)");

        set_config('lastcron', time(), $component);
        core_php_time_limit::raise();
    }

    if ($description) {
        mtrace('Finished ' . $description);
    }
}

/**
 * Used to add in old-style cron functions within plugins that have not been converted to the
 * new standard API. (The standard API is frankenstyle_name_cron() in lib.php; some types used
 * cron.php and some used a different name.)
 *
 * @param string $plugintype Plugin type e.g. 'report'
 * @param array $plugins Array from plugin name (e.g. 'report_frog') to function name (e.g.
 *   'report_frog_cron') for plugin cron functions that were already found using the new API
 * @return array Revised version of $plugins that adds in any extra plugin functions found by
 *   looking in the older location
 *
 * @deprecated since Moodle 3.9 MDL-52846. Please use new task API.
 * @todo MDL-61165 This will be deleted in Moodle 4.3.
 */
function cron_bc_hack_plugin_functions($plugintype, $plugins) {
    global $CFG; // Mandatory in case it is referenced by include()d PHP script.

    if ($plugintype === 'report') {
        // Admin reports only - not course report because course report was
        // never implemented before, so doesn't need BC.
        foreach (core_component::get_plugin_list($plugintype) as $pluginname => $dir) {
            $component = $plugintype . '_' . $pluginname;
            if (isset($plugins[$component])) {
                // We already have detected the function using the new API.
                continue;
            }
            if (!file_exists("$dir/cron.php")) {
                // No old style cron file present.
                continue;
            }
            include_once("$dir/cron.php");
            $cronfunction = $component . '_cron';
            if (function_exists($cronfunction)) {
                $plugins[$component] = $cronfunction;
            } else {
                debugging("Invalid legacy cron.php detected in $component, " .
                        "please use lib.php instead");
            }
        }
    } else if (strpos($plugintype, 'grade') === 0) {
        // Detect old style cron function names.
        // Plugin gradeexport_frog used to use grade_export_frog_cron() instead of
        // new standard API gradeexport_frog_cron(). Also applies to gradeimport, gradereport.
        foreach (core_component::get_plugin_list($plugintype) as $pluginname => $dir) {
            $component = $plugintype.'_'.$pluginname;
            if (isset($plugins[$component])) {
                // We already have detected the function using the new API.
                continue;
            }
            if (!file_exists("$dir/lib.php")) {
                continue;
            }
            include_once("$dir/lib.php");
            $cronfunction = str_replace('grade', 'grade_', $plugintype) . '_' .
                    $pluginname . '_cron';
            if (function_exists($cronfunction)) {
                $plugins[$component] = $cronfunction;
            }
        }
    }

    return $plugins;
}

/**
 * Returns the SQL used by the participants table.
 *
 * @deprecated since Moodle 3.9 MDL-68612 - See \core_user\table\participants_search for an improved way to fetch participants.
 * @param int $courseid The course id
 * @param int $groupid The groupid, 0 means all groups and USERSWITHOUTGROUP no group
 * @param int $accesssince The time since last access, 0 means any time
 * @param int $roleid The role id, 0 means all roles and -1 no roles
 * @param int $enrolid The enrolment id, 0 means all enrolment methods will be returned.
 * @param int $statusid The user enrolment status, -1 means all enrolments regardless of the status will be returned, if allowed.
 * @param string|array $search The search that was performed, empty means perform no search
 * @param string $additionalwhere Any additional SQL to add to where
 * @param array $additionalparams The additional params
 * @return array
 */
function user_get_participants_sql($courseid, $groupid = 0, $accesssince = 0, $roleid = 0, $enrolid = 0, $statusid = -1,
                                   $search = '', $additionalwhere = '', $additionalparams = array()) {
    global $DB, $USER, $CFG;

    $deprecatedtext = __FUNCTION__ . '() is deprecated. ' .
                 'Please use \core\table\participants_search::class with table filtersets instead.';
    debugging($deprecatedtext, DEBUG_DEVELOPER);

    // Get the context.
    $context = \context_course::instance($courseid, MUST_EXIST);

    $isfrontpage = ($courseid == SITEID);

    // Default filter settings. We only show active by default, especially if the user has no capability to review enrolments.
    $onlyactive = true;
    $onlysuspended = false;
    if (has_capability('moodle/course:enrolreview', $context) && (has_capability('moodle/course:viewsuspendedusers', $context))) {
        switch ($statusid) {
            case ENROL_USER_ACTIVE:
                // Nothing to do here.
                break;
            case ENROL_USER_SUSPENDED:
                $onlyactive = false;
                $onlysuspended = true;
                break;
            default:
                // If the user has capability to review user enrolments, but statusid is set to -1, set $onlyactive to false.
                $onlyactive = false;
                break;
        }
    }

    list($esql, $params) = get_enrolled_sql($context, null, $groupid, $onlyactive, $onlysuspended, $enrolid);

    $joins = array('FROM {user} u');
    $wheres = array();

    $userfields = get_extra_user_fields($context);
    $userfieldssql = user_picture::fields('u', $userfields);

    if ($isfrontpage) {
        $select = "SELECT $userfieldssql, u.lastaccess";
        $joins[] = "JOIN ($esql) e ON e.id = u.id"; // Everybody on the frontpage usually.
        if ($accesssince) {
            $wheres[] = user_get_user_lastaccess_sql($accesssince);
        }
    } else {
        $select = "SELECT $userfieldssql, COALESCE(ul.timeaccess, 0) AS lastaccess";
        $joins[] = "JOIN ($esql) e ON e.id = u.id"; // Course enrolled users only.
        // Not everybody has accessed the course yet.
        $joins[] = 'LEFT JOIN {user_lastaccess} ul ON (ul.userid = u.id AND ul.courseid = :courseid)';
        $params['courseid'] = $courseid;
        if ($accesssince) {
            $wheres[] = user_get_course_lastaccess_sql($accesssince);
        }
    }

    // Performance hacks - we preload user contexts together with accounts.
    $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
    $ccjoin = 'LEFT JOIN {context} ctx ON (ctx.instanceid = u.id AND ctx.contextlevel = :contextlevel)';
    $params['contextlevel'] = CONTEXT_USER;
    $select .= $ccselect;
    $joins[] = $ccjoin;

    // Limit list to users with some role only.
    if ($roleid) {
        // We want to query both the current context and parent contexts.
        list($relatedctxsql, $relatedctxparams) = $DB->get_in_or_equal($context->get_parent_context_ids(true),
            SQL_PARAMS_NAMED, 'relatedctx');

        // Get users without any role.
        if ($roleid == -1) {
            $wheres[] = "u.id NOT IN (SELECT userid FROM {role_assignments} WHERE contextid $relatedctxsql)";
            $params = array_merge($params, $relatedctxparams);
        } else {
            $wheres[] = "u.id IN (SELECT userid FROM {role_assignments} WHERE roleid = :roleid AND contextid $relatedctxsql)";
            $params = array_merge($params, array('roleid' => $roleid), $relatedctxparams);
        }
    }

    if (!empty($search)) {
        if (!is_array($search)) {
            $search = [$search];
        }
        foreach ($search as $index => $keyword) {
            $searchkey1 = 'search' . $index . '1';
            $searchkey2 = 'search' . $index . '2';
            $searchkey3 = 'search' . $index . '3';
            $searchkey4 = 'search' . $index . '4';
            $searchkey5 = 'search' . $index . '5';
            $searchkey6 = 'search' . $index . '6';
            $searchkey7 = 'search' . $index . '7';

            $conditions = array();
            // Search by fullname.
            $fullname = $DB->sql_fullname('u.firstname', 'u.lastname');
            $conditions[] = $DB->sql_like($fullname, ':' . $searchkey1, false, false);

            // Search by email.
            $email = $DB->sql_like('email', ':' . $searchkey2, false, false);
            if (!in_array('email', $userfields)) {
                $maildisplay = 'maildisplay' . $index;
                $userid1 = 'userid' . $index . '1';
                // Prevent users who hide their email address from being found by others
                // who aren't allowed to see hidden email addresses.
                $email = "(". $email ." AND (" .
                        "u.maildisplay <> :$maildisplay " .
                        "OR u.id = :$userid1". // User can always find himself.
                        "))";
                $params[$maildisplay] = core_user::MAILDISPLAY_HIDE;
                $params[$userid1] = $USER->id;
            }
            $conditions[] = $email;

            // Search by idnumber.
            $idnumber = $DB->sql_like('idnumber', ':' . $searchkey3, false, false);
            if (!in_array('idnumber', $userfields)) {
                $userid2 = 'userid' . $index . '2';
                // Users who aren't allowed to see idnumbers should at most find themselves
                // when searching for an idnumber.
                $idnumber = "(". $idnumber . " AND u.id = :$userid2)";
                $params[$userid2] = $USER->id;
            }
            $conditions[] = $idnumber;

            if (!empty($CFG->showuseridentity)) {
                // Search all user identify fields.
                $extrasearchfields = explode(',', $CFG->showuseridentity);
                foreach ($extrasearchfields as $extrasearchfield) {
                    if (in_array($extrasearchfield, ['email', 'idnumber', 'country'])) {
                        // Already covered above. Search by country not supported.
                        continue;
                    }
                    $param = $searchkey3 . $extrasearchfield;
                    $condition = $DB->sql_like($extrasearchfield, ':' . $param, false, false);
                    $params[$param] = "%$keyword%";
                    if (!in_array($extrasearchfield, $userfields)) {
                        // User cannot see this field, but allow match if their own account.
                        $userid3 = 'userid' . $index . '3' . $extrasearchfield;
                        $condition = "(". $condition . " AND u.id = :$userid3)";
                        $params[$userid3] = $USER->id;
                    }
                    $conditions[] = $condition;
                }
            }

            // Search by middlename.
            $middlename = $DB->sql_like('middlename', ':' . $searchkey4, false, false);
            $conditions[] = $middlename;

            // Search by alternatename.
            $alternatename = $DB->sql_like('alternatename', ':' . $searchkey5, false, false);
            $conditions[] = $alternatename;

            // Search by firstnamephonetic.
            $firstnamephonetic = $DB->sql_like('firstnamephonetic', ':' . $searchkey6, false, false);
            $conditions[] = $firstnamephonetic;

            // Search by lastnamephonetic.
            $lastnamephonetic = $DB->sql_like('lastnamephonetic', ':' . $searchkey7, false, false);
            $conditions[] = $lastnamephonetic;

            $wheres[] = "(". implode(" OR ", $conditions) .") ";
            $params[$searchkey1] = "%$keyword%";
            $params[$searchkey2] = "%$keyword%";
            $params[$searchkey3] = "%$keyword%";
            $params[$searchkey4] = "%$keyword%";
            $params[$searchkey5] = "%$keyword%";
            $params[$searchkey6] = "%$keyword%";
            $params[$searchkey7] = "%$keyword%";
        }
    }

    if (!empty($additionalwhere)) {
        $wheres[] = $additionalwhere;
        $params = array_merge($params, $additionalparams);
    }

    $from = implode("\n", $joins);
    if ($wheres) {
        $where = 'WHERE ' . implode(' AND ', $wheres);
    } else {
        $where = '';
    }

    return array($select, $from, $where, $params);
}

/**
 * Returns the total number of participants for a given course.
 *
 * @deprecated since Moodle 3.9 MDL-68612 - See \core_user\table\participants_search for an improved way to fetch participants.
 * @param int $courseid The course id
 * @param int $groupid The groupid, 0 means all groups and USERSWITHOUTGROUP no group
 * @param int $accesssince The time since last access, 0 means any time
 * @param int $roleid The role id, 0 means all roles
 * @param int $enrolid The applied filter for the user enrolment ID.
 * @param int $status The applied filter for the user's enrolment status.
 * @param string|array $search The search that was performed, empty means perform no search
 * @param string $additionalwhere Any additional SQL to add to where
 * @param array $additionalparams The additional params
 * @return int
 */
function user_get_total_participants($courseid, $groupid = 0, $accesssince = 0, $roleid = 0, $enrolid = 0, $statusid = -1,
                                     $search = '', $additionalwhere = '', $additionalparams = array()) {
    global $DB;

    $deprecatedtext = __FUNCTION__ . '() is deprecated. ' .
                      'Please use \core\table\participants_search::class with table filtersets instead.';
    debugging($deprecatedtext, DEBUG_DEVELOPER);

    list($select, $from, $where, $params) = user_get_participants_sql($courseid, $groupid, $accesssince, $roleid, $enrolid,
        $statusid, $search, $additionalwhere, $additionalparams);

    return $DB->count_records_sql("SELECT COUNT(u.id) $from $where", $params);
}

/**
 * Returns the participants for a given course.
 *
 * @deprecated since Moodle 3.9 MDL-68612 - See \core_user\table\participants_search for an improved way to fetch participants.
 * @param int $courseid The course id
 * @param int $groupid The groupid, 0 means all groups and USERSWITHOUTGROUP no group
 * @param int $accesssince The time since last access
 * @param int $roleid The role id
 * @param int $enrolid The applied filter for the user enrolment ID.
 * @param int $status The applied filter for the user's enrolment status.
 * @param string $search The search that was performed
 * @param string $additionalwhere Any additional SQL to add to where
 * @param array $additionalparams The additional params
 * @param string $sort The SQL sort
 * @param int $limitfrom return a subset of records, starting at this point (optional).
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
 * @return moodle_recordset
 */
function user_get_participants($courseid, $groupid = 0, $accesssince, $roleid, $enrolid = 0, $statusid, $search,
                               $additionalwhere = '', $additionalparams = array(), $sort = '', $limitfrom = 0, $limitnum = 0) {
    global $DB;

    $deprecatedtext = __FUNCTION__ . '() is deprecated. ' .
                      'Please use \core\table\participants_search::class with table filtersets instead.';
    debugging($deprecatedtext, DEBUG_DEVELOPER);

    list($select, $from, $where, $params) = user_get_participants_sql($courseid, $groupid, $accesssince, $roleid, $enrolid,
        $statusid, $search, $additionalwhere, $additionalparams);

    return $DB->get_recordset_sql("$select $from $where $sort", $params, $limitfrom, $limitnum);
}

/**
 * Returns the list of full course categories to be used in html_writer::select()
 *
 * Calls {@see core_course_category::make_categories_list()} to build the list.
 *
 * @deprecated since Moodle 4.0
 * @todo This will be finally removed for Moodle 4.4 as part of MDL-69124.
 * @return array array mapping course category id to the display name
 */
function make_categories_options() {
    $deprecatedtext = __FUNCTION__ . '() is deprecated. Please use \core_course_category::make_categories_list() instead.';
    debugging($deprecatedtext, DEBUG_DEVELOPER);

    return core_course_category::make_categories_list('', 0, ' / ');
}
