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
 * Add an entry to the legacy log table.
 *
 * @deprecated since 2.7 use new events instead
 *
 * @param    int     $courseid  The course id
 * @param    string  $module  The module name  e.g. forum, journal, resource, course, user etc
 * @param    string  $action  'view', 'update', 'add' or 'delete', possibly followed by another word to clarify.
 * @param    string  $url     The file and parameters used to see the results of the action
 * @param    string  $info    Additional description information
 * @param    int     $cm      The course_module->id if there is one
 * @param    int|stdClass $user If log regards $user other than $USER
 * @return void
 */
function add_to_log($courseid, $module, $action, $url='', $info='', $cm=0, $user=0) {
    debugging('add_to_log() has been deprecated, please rewrite your code to the new events API', DEBUG_DEVELOPER);

    // This is a nasty hack that allows us to put all the legacy stuff into legacy storage,
    // this way we may move all the legacy settings there too.
    $manager = get_log_manager();
    if (method_exists($manager, 'legacy_add_to_log')) {
        $manager->legacy_add_to_log($courseid, $module, $action, $url, $info, $cm, $user);
    }
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
 * Prints a basic textarea field.
 *
 * This was 'deprecated' in 2.0, but not properly (there was no alternative) so the
 * debugging message was commented out.
 *
 * @deprecated since Moodle 3.6
 *
 * When using this function, you should
 *
 * @global object
 * @param bool $unused No longer used.
 * @param int $rows Number of rows to display  (minimum of 10 when $height is non-null)
 * @param int $cols Number of columns to display (minimum of 65 when $width is non-null)
 * @param null $width (Deprecated) Width of the element; if a value is passed, the minimum value for $cols will be 65. Value is otherwise ignored.
 * @param null $height (Deprecated) Height of the element; if a value is passe, the minimum value for $rows will be 10. Value is otherwise ignored.
 * @param string $name Name to use for the textarea element.
 * @param string $value Initial content to display in the textarea.
 * @param int $obsolete deprecated
 * @param bool $return If false, will output string. If true, will return string value.
 * @param string $id CSS ID to add to the textarea element.
 * @return string|void depending on the value of $return
 */
function print_textarea($unused, $rows, $cols, $width, $height, $name, $value='', $obsolete=0, $return=false, $id='') {
    /// $width and height are legacy fields and no longer used as pixels like they used to be.
    /// However, you can set them to zero to override the mincols and minrows values below.

    // Disabling because there is not yet a viable $OUTPUT option for cases when mforms can't be used
    debugging('print_textarea() is deprecated. Please use $OUTPUT->print_textarea() instead.', DEBUG_DEVELOPER);

    global $OUTPUT;

    $mincols = 65;
    $minrows = 10;

    if ($id === '') {
        $id = 'edit-'.$name;
    }

    if ($height && ($rows < $minrows)) {
        $rows = $minrows;
    }
    if ($width && ($cols < $mincols)) {
        $cols = $mincols;
    }

    $textarea = $OUTPUT->print_textarea($name, $id, $value, $rows, $cols);
    if ($return) {
        return $textarea;
    }

    echo $textarea;
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
        '\core_message\api::can_post_message() instead.');
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
 * Return the name of the weekday
 *
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57617.
 * @param string $englishname
 * @return string of the weekeday
 */
function calendar_wday_name($englishname) {
    debugging(__FUNCTION__ . '() is deprecated and no longer used in core.', DEBUG_DEVELOPER);
    return get_string(strtolower($englishname), 'calendar');
}

/**
 * Get the upcoming event block.
 *
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57617.
 * @param array $events list of events
 * @param moodle_url|string $linkhref link to event referer
 * @param boolean $showcourselink whether links to courses should be shown
 * @return string|null $content html block content
 */
function calendar_get_block_upcoming($events, $linkhref = null, $showcourselink = false) {
    global $CFG;

    debugging(
            __FUNCTION__ . '() has been deprecated. ' .
            'Please see block_calendar_upcoming::get_content() for the correct API usage.',
            DEBUG_DEVELOPER
        );

    require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
    require_once($CFG->dirroot . '/blocks/calendar_upcoming/block_calendar_upcoming.php');
    return block_calendar_upcoming::get_upcoming_content($events, $linkhref, $showcourselink);
}

/**
 * Display month selector options.
 *
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57617.
 * @param string $name for the select element
 * @param string|array $selected options for select elements
 */
function calendar_print_month_selector($name, $selected) {
    debugging(__FUNCTION__ . '() is deprecated and no longer used in core.', DEBUG_DEVELOPER);
    $months = array();
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), '%B');
    }
    echo html_writer::label(get_string('months'), 'menu'. $name, false, array('class' => 'accesshide'));
    echo html_writer::select($months, $name, $selected, false);
}

/**
 * Update calendar subscriptions.
 *
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57617.
 * @return bool
 */
function calendar_cron() {
    debugging(__FUNCTION__ . '() is deprecated and should not be used. Please use the core\task\calendar_cron_task instead.',
        DEBUG_DEVELOPER);

    global $CFG, $DB;

    require_once($CFG->dirroot . '/calendar/lib.php');
    // In order to execute this we need bennu.
    require_once($CFG->libdir.'/bennu/bennu.inc.php');

    mtrace('Updating calendar subscriptions:');
    cron_trace_time_and_memory();

    $time = time();
    $subscriptions = $DB->get_records_sql('SELECT * FROM {event_subscriptions} WHERE pollinterval > 0
      AND lastupdated + pollinterval < ?', array($time));
    foreach ($subscriptions as $sub) {
        mtrace("Updating calendar subscription {$sub->name} in course {$sub->courseid}");
        try {
            $log = calendar_update_subscription_events($sub->id);
            mtrace(trim(strip_tags($log)));
        } catch (moodle_exception $ex) {
            mtrace('Error updating calendar subscription: ' . $ex->getMessage());
        }
    }

    mtrace('Finished updating calendar subscriptions.');

    return true;
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
 * Previous internal API, it was not supposed to be used anywhere.
 * Return a nested array showing role assignments
 * and all relevant role capabilities for the user.
 *
 * [ra]   => [/path][roleid]=roleid
 * [rdef] => ["$contextpath:$roleid"][capability]=permission
 *
 * @access private
 * @deprecated since Moodle 3.4. MDL-49398.
 * @param int $userid - the id of the user
 * @return array access info array
 */
function get_user_access_sitewide($userid) {
    debugging('get_user_access_sitewide() is deprecated. Do not use private functions or data structures.', DEBUG_DEVELOPER);

    $accessdata = get_user_accessdata($userid);
    $accessdata['rdef'] = array();
    $roles = array();

    foreach ($accessdata['ra'] as $path => $pathroles) {
        $roles = array_merge($pathroles, $roles);
    }

    $rdefs = get_role_definitions($roles);

    foreach ($rdefs as $roleid => $rdef) {
        foreach ($rdef as $path => $caps) {
            $accessdata['rdef']["$path:$roleid"] = $caps;
        }
    }

    return $accessdata;
}

/**
 * Generates the HTML for a miniature calendar.
 *
 * @param array $courses list of course to list events from
 * @param array $groups list of group
 * @param array $users user's info
 * @param int|bool $calmonth calendar month in numeric, default is set to false
 * @param int|bool $calyear calendar month in numeric, default is set to false
 * @param string|bool $placement the place/page the calendar is set to appear - passed on the the controls function
 * @param int|bool $courseid id of the course the calendar is displayed on - passed on the the controls function
 * @param int $time the unixtimestamp representing the date we want to view, this is used instead of $calmonth
 *     and $calyear to support multiple calendars
 * @return string $content return html table for mini calendar
 * @deprecated since Moodle 3.4. MDL-59333
 */
function calendar_get_mini($courses, $groups, $users, $calmonth = false, $calyear = false, $placement = false,
                           $courseid = false, $time = 0) {
    global $PAGE;

    debugging('calendar_get_mini() has been deprecated. Please update your code to use calendar_get_view.',
        DEBUG_DEVELOPER);

    if (!empty($calmonth) && !empty($calyear)) {
        // Do this check for backwards compatibility.
        // The core should be passing a timestamp rather than month and year.
        // If a month and year are passed they will be in Gregorian.
        // Ensure it is a valid date, else we will just set it to the current timestamp.
        if (checkdate($calmonth, 1, $calyear)) {
            $time = make_timestamp($calyear, $calmonth, 1);
        } else {
            $time = time();
        }
    } else if (empty($time)) {
        // Get the current date in the calendar type being used.
        $time = time();
    }

    if ($courseid == SITEID) {
        $course = get_site();
    } else {
        $course = get_course($courseid);
    }
    $calendar = new calendar_information(0, 0, 0, $time);
    $calendar->prepare_for_view($course, $courses);

    $renderer = $PAGE->get_renderer('core_calendar');
    list($data, $template) = calendar_get_view($calendar, 'mini');
    return $renderer->render_from_template($template, $data);
}

/**
 * Gets the calendar upcoming event.
 *
 * @param array $courses array of courses
 * @param array|int|bool $groups array of groups, group id or boolean for all/no group events
 * @param array|int|bool $users array of users, user id or boolean for all/no user events
 * @param int $daysinfuture number of days in the future we 'll look
 * @param int $maxevents maximum number of events
 * @param int $fromtime start time
 * @return array $output array of upcoming events
 * @deprecated since Moodle 3.4. MDL-59333
 */
function calendar_get_upcoming($courses, $groups, $users, $daysinfuture, $maxevents, $fromtime=0) {
    debugging(
            'calendar_get_upcoming() has been deprecated. ' .
            'Please see block_calendar_upcoming::get_content() for the correct API usage.',
            DEBUG_DEVELOPER
        );

    global $COURSE;

    $display = new \stdClass;
    $display->range = $daysinfuture; // How many days in the future we 'll look.
    $display->maxevents = $maxevents;

    $output = array();

    $processed = 0;
    $now = time(); // We 'll need this later.
    $usermidnighttoday = usergetmidnight($now);

    if ($fromtime) {
        $display->tstart = $fromtime;
    } else {
        $display->tstart = $usermidnighttoday;
    }

    // This works correctly with respect to the user's DST, but it is accurate
    // only because $fromtime is always the exact midnight of some day!
    $display->tend = usergetmidnight($display->tstart + DAYSECS * $display->range + 3 * HOURSECS) - 1;

    // Get the events matching our criteria.
    $events = calendar_get_legacy_events($display->tstart, $display->tend, $users, $groups, $courses);

    // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
    // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
    // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
    // arguments to this function.
    $hrefparams = array();
    if (!empty($courses)) {
        $courses = array_diff($courses, array(SITEID));
        if (count($courses) == 1) {
            $hrefparams['course'] = reset($courses);
        }
    }

    if ($events !== false) {
        foreach ($events as $event) {
            if (!empty($event->modulename)) {
                $instances = get_fast_modinfo($event->courseid)->get_instances_of($event->modulename);
                if (empty($instances[$event->instance]->uservisible)) {
                    continue;
                }
            }

            if ($processed >= $display->maxevents) {
                break;
            }

            $event->time = calendar_format_event_time($event, $now, $hrefparams);
            $output[] = $event;
            $processed++;
        }
    }

    return $output;
}

/**
 * Creates a record in the role_allow_override table
 *
 * @param int $sroleid source roleid
 * @param int $troleid target roleid
 * @return void
 * @deprecated since Moodle 3.4. MDL-50666
 */
function allow_override($sroleid, $troleid) {
    debugging('allow_override() has been deprecated. Please update your code to use core_role_set_override_allowed.',
            DEBUG_DEVELOPER);

    core_role_set_override_allowed($sroleid, $troleid);
}

/**
 * Creates a record in the role_allow_assign table
 *
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 * @deprecated since Moodle 3.4. MDL-50666
 */
function allow_assign($fromroleid, $targetroleid) {
    debugging('allow_assign() has been deprecated. Please update your code to use core_role_set_assign_allowed.',
            DEBUG_DEVELOPER);

    core_role_set_assign_allowed($fromroleid, $targetroleid);
}

/**
 * Creates a record in the role_allow_switch table
 *
 * @param int $fromroleid source roleid
 * @param int $targetroleid target roleid
 * @return void
 * @deprecated since Moodle 3.4. MDL-50666
 */
function allow_switch($fromroleid, $targetroleid) {
    debugging('allow_switch() has been deprecated. Please update your code to use core_role_set_switch_allowed.',
            DEBUG_DEVELOPER);

    core_role_set_switch_allowed($fromroleid, $targetroleid);
}

/**
 * Organise categories into a single parent category (called the 'Top' category) per context.
 *
 * @param array $categories List of question categories in the format of ["$categoryid,$contextid" => $category].
 * @param array $pcontexts List of context ids.
 * @return array
 * @deprecated since Moodle 3.5. MDL-61132
 */
function question_add_tops($categories, $pcontexts) {
    debugging('question_add_tops() has been deprecated. You may want to pass $top = true to get_categories_for_contexts().',
            DEBUG_DEVELOPER);

    $topcats = array();
    foreach ($pcontexts as $contextid) {
        $topcat = question_get_top_category($contextid, true);
        $context = context::instance_by_id($contextid);

        $newcat = new stdClass();
        $newcat->id = "{$topcat->id},$contextid";
        $newcat->name = get_string('topfor', 'question', $context->get_context_name(false));
        $newcat->parent = 0;
        $newcat->contextid = $contextid;
        $topcats["{$topcat->id},$contextid"] = $newcat;
    }
    // Put topcats in at beginning of array - they'll be sorted into different contexts later.
    return array_merge($topcats, $categories);
}

/**
 * Checks if the question category is the highest-level category in the context that can be edited, and has no siblings.
 *
 * @param int $categoryid a category id.
 * @return bool
 * @deprecated since Moodle 3.5. MDL-61132
 */
function question_is_only_toplevel_category_in_context($categoryid) {
    debugging('question_is_only_toplevel_category_in_context() has been deprecated. '
            . 'Please update your code to use question_is_only_child_of_top_category_in_context() instead.',
            DEBUG_DEVELOPER);

    return question_is_only_child_of_top_category_in_context($categoryid);
}

/**
 * Moves messages from a particular user from the message table (unread messages) to message_read
 * This is typically only used when a user is deleted
 *
 * @param object $userid User id
 * @return boolean success
 * @deprecated since Moodle 3.5
 */
function message_move_userfrom_unread2read($userid) {
    debugging('message_move_userfrom_unread2read() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    global $DB;

    // Move all unread messages from message table to message_read.
    if ($messages = $DB->get_records_select('message', 'useridfrom = ?', array($userid), 'timecreated')) {
        foreach ($messages as $message) {
            message_mark_message_read($message, 0); // Set timeread to 0 as the message was never read.
        }
    }
    return true;
}

/**
 * Retrieve users blocked by $user1
 *
 * @param object $user1 the user whose messages are being viewed
 * @param object $user2 the user $user1 is talking to. If they are being blocked
 *                      they will have a variable called 'isblocked' added to their user object
 * @return array the users blocked by $user1
 * @deprecated since Moodle 3.5
 */
function message_get_blocked_users($user1=null, $user2=null) {
    debugging('message_get_blocked_users() is deprecated, please use \core_message\api::get_blocked_users() instead.',
        DEBUG_DEVELOPER);

    global $USER;

    if (empty($user1)) {
        $user1 = new stdClass();
        $user1->id = $USER->id;
    }

    return \core_message\api::get_blocked_users($user1->id);
}

/**
 * Retrieve $user1's contacts (online, offline and strangers)
 *
 * @param object $user1 the user whose messages are being viewed
 * @param object $user2 the user $user1 is talking to. If they are a contact
 *                      they will have a variable called 'iscontact' added to their user object
 * @return array containing 3 arrays. array($onlinecontacts, $offlinecontacts, $strangers)
 * @deprecated since Moodle 3.5
 */
function message_get_contacts($user1=null, $user2=null) {
    debugging('message_get_contacts() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    global $DB, $CFG, $USER;

    if (empty($user1)) {
        $user1 = $USER;
    }

    if (!empty($user2)) {
        $user2->iscontact = false;
    }

    $timetoshowusers = 300; // Seconds default.
    if (isset($CFG->block_online_users_timetosee)) {
        $timetoshowusers = $CFG->block_online_users_timetosee * 60;
    }

    // Rime which a user is counting as being active since.
    $timefrom = time() - $timetoshowusers;

    // People in our contactlist who are online.
    $onlinecontacts  = array();
    // People in our contactlist who are offline.
    $offlinecontacts = array();
    // People who are not in our contactlist but have sent us a message.
    $strangers       = array();

    // Get all in our contact list who are not blocked in our and count messages we have waiting from each of them.
    $rs = \core_message\api::get_contacts_with_unread_message_count($user1->id);
    foreach ($rs as $rd) {
        if ($rd->lastaccess >= $timefrom) {
            // They have been active recently, so are counted online.
            $onlinecontacts[] = $rd;

        } else {
            $offlinecontacts[] = $rd;
        }

        if (!empty($user2) && $user2->id == $rd->id) {
            $user2->iscontact = true;
        }
    }

    // Get messages from anyone who isn't in our contact list and count the number of messages we have from each of them.
    $rs = \core_message\api::get_non_contacts_with_unread_message_count($user1->id);
    // Add user id as array index, so supportuser and noreply user don't get duplicated (if they are real users).
    foreach ($rs as $rd) {
        $strangers[$rd->id] = $rd;
    }

    // Add noreply user and support user to the list, if they don't exist.
    $supportuser = core_user::get_support_user();
    if (!isset($strangers[$supportuser->id]) && !$supportuser->deleted) {
        $supportuser->messagecount = message_count_unread_messages($USER, $supportuser);
        if ($supportuser->messagecount > 0) {
            $strangers[$supportuser->id] = $supportuser;
        }
    }

    $noreplyuser = core_user::get_noreply_user();
    if (!isset($strangers[$noreplyuser->id]) && !$noreplyuser->deleted) {
        $noreplyuser->messagecount = message_count_unread_messages($USER, $noreplyuser);
        if ($noreplyuser->messagecount > 0) {
            $strangers[$noreplyuser->id] = $noreplyuser;
        }
    }

    return array($onlinecontacts, $offlinecontacts, $strangers);
}

/**
 * Mark a single message as read
 *
 * @param stdClass $message An object with an object property ie $message->id which is an id in the message table
 * @param int $timeread the timestamp for when the message should be marked read. Usually time().
 * @param bool $messageworkingempty Is the message_working table already confirmed empty for this message?
 * @return int the ID of the message in the messags table
 * @deprecated since Moodle 3.5
 */
function message_mark_message_read($message, $timeread, $messageworkingempty=false) {
    debugging('message_mark_message_read() is deprecated, please use \core_message\api::mark_message_as_read()
        or \core_message\api::mark_notification_as_read().', DEBUG_DEVELOPER);

    if (!empty($message->notification)) {
        \core_message\api::mark_notification_as_read($message, $timeread);
    } else {
        \core_message\api::mark_message_as_read($message->useridto, $message, $timeread);
    }

    return $message->id;
}


/**
 * Checks if a user can delete a message.
 *
 * @param stdClass $message the message to delete
 * @param string $userid the user id of who we want to delete the message for (this may be done by the admin
 *  but will still seem as if it was by the user)
 * @return bool Returns true if a user can delete the message, false otherwise.
 * @deprecated since Moodle 3.5
 */
function message_can_delete_message($message, $userid) {
    debugging('message_can_delete_message() is deprecated, please use \core_message\api::can_delete_message() instead.',
        DEBUG_DEVELOPER);

    return \core_message\api::can_delete_message($userid, $message->id);
}

/**
 * Deletes a message.
 *
 * This function does not verify any permissions.
 *
 * @param stdClass $message the message to delete
 * @param string $userid the user id of who we want to delete the message for (this may be done by the admin
 *  but will still seem as if it was by the user)
 * @return bool
 * @deprecated since Moodle 3.5
 */
function message_delete_message($message, $userid) {
    debugging('message_delete_message() is deprecated, please use \core_message\api::delete_message() instead.',
        DEBUG_DEVELOPER);

    return \core_message\api::delete_message($userid, $message->id);
}

/**
 * Get all of the allowed types for all of the courses and groups
 * the logged in user belongs to.
 *
 * The returned array will optionally have 5 keys:
 *      'user' : true if the logged in user can create user events
 *      'site' : true if the logged in user can create site events
 *      'category' : array of course categories that the user can create events for
 *      'course' : array of courses that the user can create events for
 *      'group': array of groups that the user can create events for
 *      'groupcourses' : array of courses that the groups belong to (can
 *                       be different from the list in 'course'.
 * @deprecated since 3.6
 * @return array The array of allowed types.
 */
function calendar_get_all_allowed_types() {
    debugging('calendar_get_all_allowed_types() is deprecated. Please use calendar_get_allowed_types() instead.',
        DEBUG_DEVELOPER);

    global $CFG, $USER, $DB;

    require_once($CFG->libdir . '/enrollib.php');

    $types = [];

    $allowed = new stdClass();

    calendar_get_allowed_types($allowed);

    if ($allowed->user) {
        $types['user'] = true;
    }

    if ($allowed->site) {
        $types['site'] = true;
    }

    if (core_course_category::has_manage_capability_on_any()) {
        $types['category'] = core_course_category::make_categories_list('moodle/category:manage');
    }

    // This function warms the context cache for the course so the calls
    // to load the course context in calendar_get_allowed_types don't result
    // in additional DB queries.
    $courses = calendar_get_default_courses(null, 'id, groupmode, groupmodeforce', true);

    // We want to pre-fetch all of the groups for each course in a single
    // query to avoid calendar_get_allowed_types from hitting the DB for
    // each separate course.
    $groups = groups_get_all_groups_for_courses($courses);

    foreach ($courses as $course) {
        $coursegroups = isset($groups[$course->id]) ? $groups[$course->id] : null;
        calendar_get_allowed_types($allowed, $course, $coursegroups);

        if (!empty($allowed->courses)) {
            $types['course'][$course->id] = $course;
        }

        if (!empty($allowed->groups)) {
            $types['groupcourses'][$course->id] = $course;

            if (!isset($types['group'])) {
                $types['group'] = array_values($allowed->groups);
            } else {
                $types['group'] = array_merge($types['group'], array_values($allowed->groups));
            }
        }
    }

    return $types;
}

/**
 * Gets array of all groups in a set of course.
 *
 * @category group
 * @param array $courses Array of course objects or course ids.
 * @return array Array of groups indexed by course id.
 */
function groups_get_all_groups_for_courses($courses) {
    global $DB;

    if (empty($courses)) {
        return [];
    }

    $groups = [];
    $courseids = [];

    foreach ($courses as $course) {
        $courseid = is_object($course) ? $course->id : $course;
        $groups[$courseid] = [];
        $courseids[] = $courseid;
    }

    $groupfields = [
        'g.id as gid',
        'g.courseid',
        'g.idnumber',
        'g.name',
        'g.description',
        'g.descriptionformat',
        'g.enrolmentkey',
        'g.picture',
        'g.hidepicture',
        'g.timecreated',
        'g.timemodified'
    ];

    $groupsmembersfields = [
        'gm.id as gmid',
        'gm.groupid',
        'gm.userid',
        'gm.timeadded',
        'gm.component',
        'gm.itemid'
    ];

    $concatidsql = $DB->sql_concat_join("'-'", ['g.id', 'COALESCE(gm.id, 0)']) . ' AS uniqid';
    list($courseidsql, $params) = $DB->get_in_or_equal($courseids);
    $groupfieldssql = implode(',', $groupfields);
    $groupmembersfieldssql = implode(',', $groupsmembersfields);
    $sql = "SELECT {$concatidsql}, {$groupfieldssql}, {$groupmembersfieldssql}
              FROM {groups} g
         LEFT JOIN {groups_members} gm
                ON gm.groupid = g.id
             WHERE g.courseid {$courseidsql}";

    $results = $DB->get_records_sql($sql, $params);

    // The results will come back as a flat dataset thanks to the left
    // join so we will need to do some post processing to blow it out
    // into a more usable data structure.
    //
    // This loop will extract the distinct groups from the result set
    // and add it's list of members to the object as a property called
    // 'members'. Then each group will be added to the result set indexed
    // by it's course id.
    //
    // The resulting data structure for $groups should be:
    // $groups = [
    //      '1' = [
    //          '1' => (object) [
    //              'id' => 1,
    //              <rest of group properties>
    //              'members' => [
    //                  '1' => (object) [
    //                      <group member properties>
    //                  ],
    //                  '2' => (object) [
    //                      <group member properties>
    //                  ]
    //              ]
    //          ],
    //          '2' => (object) [
    //              'id' => 2,
    //              <rest of group properties>
    //              'members' => [
    //                  '1' => (object) [
    //                      <group member properties>
    //                  ],
    //                  '3' => (object) [
    //                      <group member properties>
    //                  ]
    //              ]
    //          ]
    //      ]
    // ]
    //
    foreach ($results as $key => $result) {
        $groupid = $result->gid;
        $courseid = $result->courseid;
        $coursegroups = $groups[$courseid];
        $groupsmembersid = $result->gmid;
        $reducefunc = function($carry, $field) use ($result) {
            // Iterate over the groups properties and pull
            // them out into a separate object.
            list($prefix, $field) = explode('.', $field);

            if (property_exists($result, $field)) {
                $carry[$field] = $result->{$field};
            }

            return $carry;
        };

        if (isset($coursegroups[$groupid])) {
            $group = $coursegroups[$groupid];
        } else {
            $initial = [
                'id' => $groupid,
                'members' => []
            ];
            $group = (object) array_reduce(
                $groupfields,
                $reducefunc,
                $initial
            );
        }

        if (!empty($groupsmembersid)) {
            $initial = ['id' => $groupsmembersid];
            $groupsmembers = (object) array_reduce(
                $groupsmembersfields,
                $reducefunc,
                $initial
            );

            $group->members[$groupsmembers->userid] = $groupsmembers;
        }

        $coursegroups[$groupid] = $group;
        $groups[$courseid] = $coursegroups;
    }

    return $groups;
}

/**
 * Gets the capabilities that have been cached in the database for this
 * component.
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 * @todo final deprecation. To be removed in Moodle 4.0
 *
 * @access protected To be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return array of events
 */
function events_get_cached($component) {
    global $DB;

    debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.',
            DEBUG_DEVELOPER);

    $cachedhandlers = array();

    if ($storedhandlers = $DB->get_records('events_handlers', array('component'=>$component))) {
        foreach ($storedhandlers as $handler) {
            $cachedhandlers[$handler->eventname] = array (
                'id'              => $handler->id,
                'handlerfile'     => $handler->handlerfile,
                'handlerfunction' => $handler->handlerfunction,
                'schedule'        => $handler->schedule,
                'internal'        => $handler->internal);
        }
    }

    return $cachedhandlers;
}

/**
 * Remove all event handlers and queued events
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 * @todo final deprecation. To be removed in Moodle 4.0
 *
 * @category event
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 */
function events_uninstall($component) {
    debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.',
            DEBUG_DEVELOPER);
    $cachedhandlers = events_get_cached($component);
    events_cleanup($component, $cachedhandlers);

    events_get_handlers('reset');
}

/**
 * Deletes cached events that are no longer needed by the component.
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 * @todo final deprecation. To be removed in Moodle 4.0
 *
 * @access protected To be used from eventslib only
 *
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @param array $cachedhandlers array of the cached events definitions that will be
 * @return int number of unused handlers that have been removed
 */
function events_cleanup($component, $cachedhandlers) {
    global $DB;
    debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.',
            DEBUG_DEVELOPER);
    $deletecount = 0;
    foreach ($cachedhandlers as $eventname => $cachedhandler) {
        if ($qhandlers = $DB->get_records('events_queue_handlers', array('handlerid'=>$cachedhandler['id']))) {
            //debugging("Removing pending events from queue before deleting of event handler: $component - $eventname");
            foreach ($qhandlers as $qhandler) {
                events_dequeue($qhandler);
            }
        }
        $DB->delete_records('events_handlers', array('eventname'=>$eventname, 'component'=>$component));
        $deletecount++;
    }

    return $deletecount;
}

/**
 * Removes this queued handler from the events_queued_handler table
 *
 * Removes events_queue record from events_queue if no more references to this event object exists
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 * @todo final deprecation. To be removed in Moodle 4.0
 *
 * @access protected To be used from eventslib only
 *
 * @param stdClass $qhandler A row from the events_queued_handler table
 */
function events_dequeue($qhandler) {
    global $DB;
    debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.',
            DEBUG_DEVELOPER);
    // first delete the queue handler
    $DB->delete_records('events_queue_handlers', array('id'=>$qhandler->id));

    // if no more queued handler is pointing to the same event - delete the event too
    if (!$DB->record_exists('events_queue_handlers', array('queuedeventid'=>$qhandler->queuedeventid))) {
        $DB->delete_records('events_queue', array('id'=>$qhandler->queuedeventid));
    }
}

/**
 * Returns handlers for given event. Uses caching for better perf.
 * @deprecated since Moodle 3.6. Please use the Events 2 API.
 * @todo final deprecation. To be removed in Moodle 4.0
 *
 * @access protected To be used from eventslib only
 *
 * @staticvar array $handlers
 * @param string $eventname name of event or 'reset'
 * @return array|false array of handlers or false otherwise
 */
function events_get_handlers($eventname) {
    global $DB;
    static $handlers = array();
    debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.',
            DEBUG_DEVELOPER);

    if ($eventname === 'reset') {
        $handlers = array();
        return false;
    }

    if (!array_key_exists($eventname, $handlers)) {
        $handlers[$eventname] = $DB->get_records('events_handlers', array('eventname'=>$eventname));
    }

    return $handlers[$eventname];
}

/**
 * This function finds the roles assigned directly to this context only
 * i.e. no roles in parent contexts
 *
 * @deprecated since Moodle 3.6. Please use the get_roles_used_in_context().
 * @todo final deprecation. To be removed in Moodle 4.0
 * @param context $context
 * @return array
 */
function get_roles_on_exact_context(context $context) {
    debugging('get_roles_on_exact_context() is deprecated, please use get_roles_used_in_context() instead.',
        DEBUG_DEVELOPER);

    return get_roles_used_in_context($context, false);
}

/**
 * Find out which roles has assignment on this context
 *
 * @deprecated since Moodle 3.6. Please use the get_roles_used_in_context().
 * @todo final deprecation. To be removed in Moodle 4.0
 * @param context $context
 * @return array
 */
function get_roles_with_assignment_on_context(context $context) {
    debugging('get_roles_with_assignment_on_context() is deprecated, please use get_roles_used_in_context() instead.',
        DEBUG_DEVELOPER);

    return get_roles_used_in_context($context, false);
}

/**
 * Add the selected user as a contact for the current user
 *
 * @deprecated since Moodle 3.6
 * @param int $contactid the ID of the user to add as a contact
 * @param int $blocked 1 if you wish to block the contact
 * @param int $userid the user ID of the user we want to add the contact for, defaults to current user if not specified.
 * @return bool/int false if the $contactid isnt a valid user id. True if no changes made.
 *                  Otherwise returns the result of update_record() or insert_record()
 */
function message_add_contact($contactid, $blocked = 0, $userid = 0) {
    debugging('message_add_contact() is deprecated. Please use \core_message\api::create_contact_request() instead. ' .
        'If you wish to block or unblock a user please use \core_message\api::is_blocked() and ' .
        '\core_message\api::block_user() or \core_message\api::unblock_user() respectively.', DEBUG_DEVELOPER);

    global $USER, $DB;

    if (!$DB->record_exists('user', array('id' => $contactid))) {
        return false;
    }

    if (empty($userid)) {
        $userid = $USER->id;
    }

    // Check if a record already exists as we may be changing blocking status.
    if (\core_message\api::is_contact($userid, $contactid)) {
        $isblocked = \core_message\api::is_blocked($userid, $contactid);
        // Check if blocking status has been changed.
        if ($isblocked != $blocked) {
            if ($blocked == 1) {
                if (!$isblocked) {
                    \core_message\api::block_user($userid, $contactid);
                }
            } else {
                \core_message\api::unblock_user($userid, $contactid);
            }

            return true;
        } else {
            // No change to blocking status.
            return true;
        }
    } else {
        if ($blocked == 1) {
            if (!\core_message\api::is_blocked($userid, $contactid)) {
                \core_message\api::block_user($userid, $contactid);
            }
        } else {
            \core_message\api::unblock_user($userid, $contactid);
            if (!\core_message\api::does_contact_request_exist($userid, $contactid)) {
                \core_message\api::create_contact_request($userid, $contactid);
            }
        }

        return true;
    }
}

/**
 * Remove a contact.
 *
 * @deprecated since Moodle 3.6
 * @param int $contactid the user ID of the contact to remove
 * @param int $userid the user ID of the user we want to remove the contacts for, defaults to current user if not specified.
 * @return bool returns the result of delete_records()
 */
function message_remove_contact($contactid, $userid = 0) {
    debugging('message_remove_contact() is deprecated. Please use \core_message\api::remove_contact() instead.',
        DEBUG_DEVELOPER);

    global $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    \core_message\api::remove_contact($userid, $contactid);

    return true;
}

/**
 * Unblock a contact.
 *
 * @deprecated since Moodle 3.6
 * @param int $contactid the user ID of the contact to unblock
 * @param int $userid the user ID of the user we want to unblock the contact for, defaults to current user
 *  if not specified.
 * @return bool returns the result of delete_records()
 */
function message_unblock_contact($contactid, $userid = 0) {
    debugging('message_unblock_contact() is deprecated. Please use \core_message\api::unblock_user() instead.',
        DEBUG_DEVELOPER);

    global $DB, $USER;

    if (!$DB->record_exists('user', array('id' => $contactid))) {
        return false;
    }

    if (empty($userid)) {
        $userid = $USER->id;
    }

    \core_message\api::unblock_user($userid, $contactid);

    return true;
}

/**
 * Block a user.
 *
 * @deprecated since Moodle 3.6
 * @param int $contactid the user ID of the user to block
 * @param int $userid the user ID of the user we want to unblock the contact for, defaults to current user
 *  if not specified.
 * @return bool
 */
function message_block_contact($contactid, $userid = 0) {
    debugging('message_block_contact() is deprecated. Please use \core_message\api::is_blocked() and ' .
        '\core_message\api::block_user() instead.', DEBUG_DEVELOPER);

    global $DB, $USER;

    if (!$DB->record_exists('user', array('id' => $contactid))) {
        return false;
    }

    if (empty($userid)) {
        $userid = $USER->id;
    }

    if (!\core_message\api::is_blocked($userid, $contactid)) {
        \core_message\api::block_user($userid, $contactid);
    }

    return true;
}

/**
 * Load a user's contact record
 *
 * @deprecated since Moodle 3.6
 * @param int $contactid the user ID of the user whose contact record you want
 * @return array message contacts
 */
function message_get_contact($contactid) {
    debugging('message_get_contact() is deprecated. Please use \core_message\api::get_contact() instead.',
        DEBUG_DEVELOPER);

    global $USER;

    return \core_message\api::get_contact($USER->id, $contactid);
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
