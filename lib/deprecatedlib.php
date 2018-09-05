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
 * Function to call all event handlers when triggering an event
 *
 * @deprecated since 2.6
 *
 * @param string $eventname name of the event
 * @param mixed $eventdata event data object
 * @return int number of failed events
 */
function events_trigger($eventname, $eventdata) {
    debugging('events_trigger() is deprecated, please use new events instead', DEBUG_DEVELOPER);
    return events_trigger_legacy($eventname, $eventdata);
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
 * Adds a file upload to the log table so that clam can resolve the filename to the user later if necessary
 *
 * @deprecated since 2.7 - use new file picker instead
 *
 */
function clam_log_upload($newfilepath, $course=null, $nourl=false) {
    throw new coding_exception('clam_log_upload() can not be used any more, please use file picker instead');
}

/**
 * This function logs to error_log and to the log table that an infected file has been found and what's happened to it.
 *
 * @deprecated since 2.7 - use new file picker instead
 *
 */
function clam_log_infected($oldfilepath='', $newfilepath='', $userid=0) {
    throw new coding_exception('clam_log_infected() can not be used any more, please use file picker instead');
}

/**
 * Some of the modules allow moving attachments (glossary), in which case we need to hunt down an original log and change the path.
 *
 * @deprecated since 2.7 - use new file picker instead
 *
 */
function clam_change_log($oldpath, $newpath, $update=true) {
    throw new coding_exception('clam_change_log() can not be used any more, please use file picker instead');
}

/**
 * Replaces the given file with a string.
 *
 * @deprecated since 2.7 - infected files are now deleted in file picker
 *
 */
function clam_replace_infected_file($file) {
    throw new coding_exception('clam_replace_infected_file() can not be used any more, please use file picker instead');
}

/**
 * Deals with an infected file - either moves it to a quarantinedir
 * (specified in CFG->quarantinedir) or deletes it.
 *
 * If moving it fails, it deletes it.
 *
 * @deprecated since 2.7
 */
function clam_handle_infected_file($file, $userid=0, $basiconly=false) {
    throw new coding_exception('clam_handle_infected_file() can not be used any more, please use file picker instead');
}

/**
 * If $CFG->runclamonupload is set, we scan a given file. (called from {@link preprocess_files()})
 *
 * @deprecated since 2.7
 */
function clam_scan_moodle_file(&$file, $course) {
    throw new coding_exception('clam_scan_moodle_file() can not be used any more, please use file picker instead');
}


/**
 * Checks whether the password compatibility library will work with the current
 * version of PHP. This cannot be done using PHP version numbers since the fix
 * has been backported to earlier versions in some distributions.
 *
 * See https://github.com/ircmaxell/password_compat/issues/10 for more details.
 *
 * @deprecated since 2.7 PHP 5.4.x should be always compatible.
 *
 */
function password_compat_not_supported() {
    throw new coding_exception('Do not use password_compat_not_supported() - bcrypt is now always available');
}

/**
 * Factory method that was returning moodle_session object.
 *
 * @deprecated since 2.6
 */
function session_get_instance() {
    throw new coding_exception('session_get_instance() is removed, use \core\session\manager instead');
}

/**
 * Returns true if legacy session used.
 *
 * @deprecated since 2.6
 */
function session_is_legacy() {
    throw new coding_exception('session_is_legacy() is removed, do not use any more');
}

/**
 * Terminates all sessions, auth hooks are not executed.
 *
 * @deprecated since 2.6
 */
function session_kill_all() {
    throw new coding_exception('session_kill_all() is removed, use \core\session\manager::kill_all_sessions() instead');
}

/**
 * Mark session as accessed, prevents timeouts.
 *
 * @deprecated since 2.6
 */
function session_touch($sid) {
    throw new coding_exception('session_touch() is removed, use \core\session\manager::touch_session() instead');
}

/**
 * Terminates one sessions, auth hooks are not executed.
 *
 * @deprecated since 2.6
 */
function session_kill($sid) {
    throw new coding_exception('session_kill() is removed, use \core\session\manager::kill_session() instead');
}

/**
 * Terminates all sessions of one user, auth hooks are not executed.
 *
 * @deprecated since 2.6
 */
function session_kill_user($userid) {
    throw new coding_exception('session_kill_user() is removed, use \core\session\manager::kill_user_sessions() instead');
}

/**
 * Setup $USER object - called during login, loginas, etc.
 *
 * Call sync_user_enrolments() manually after log-in, or log-in-as.
 *
 * @deprecated since 2.6
 */
function session_set_user($user) {
    throw new coding_exception('session_set_user() is removed, use \core\session\manager::set_user() instead');
}

/**
 * Is current $USER logged-in-as somebody else?
 * @deprecated since 2.6
 */
function session_is_loggedinas() {
    throw new coding_exception('session_is_loggedinas() is removed, use \core\session\manager::is_loggedinas() instead');
}

/**
 * Returns the $USER object ignoring current login-as session
 * @deprecated since 2.6
 */
function session_get_realuser() {
    throw new coding_exception('session_get_realuser() is removed, use \core\session\manager::get_realuser() instead');
}

/**
 * Login as another user - no security checks here.
 * @deprecated since 2.6
 */
function session_loginas($userid, $context) {
    throw new coding_exception('session_loginas() is removed, use \core\session\manager::loginas() instead');
}

/**
 * Minify JavaScript files.
 *
 * @deprecated since 2.6
 */
function js_minify($files) {
    throw new coding_exception('js_minify() is removed, use core_minify::js_files() or core_minify::js() instead.');
}

/**
 * Minify CSS files.
 *
 * @deprecated since 2.6
 */
function css_minify_css($files) {
    throw new coding_exception('css_minify_css() is removed, use core_minify::css_files() or core_minify::css() instead.');
}

// === Deprecated before 2.6.0 ===

/**
 * Hack to find out the GD version by parsing phpinfo output
 *
 * @deprecated
 */
function check_gd_version() {
    throw new coding_exception('check_gd_version() is removed, GD extension is always available now');
}

/**
 * Not used any more, the account lockout handling is now
 * part of authenticate_user_login().
 * @deprecated
 */
function update_login_count() {
    throw new coding_exception('update_login_count() is removed, all calls need to be removed');
}

/**
 * Not used any more, replaced by proper account lockout.
 * @deprecated
 */
function reset_login_count() {
    throw new coding_exception('reset_login_count() is removed, all calls need to be removed');
}

/**
 * @deprecated
 */
function update_log_display_entry($module, $action, $mtable, $field) {

    throw new coding_exception('The update_log_display_entry() is removed, please use db/log.php description file instead.');
}

/**
 * @deprecated use the text formatting in a standard way instead (http://docs.moodle.org/dev/Output_functions)
 *             this was abused mostly for embedding of attachments
 */
function filter_text($text, $courseid = NULL) {
    throw new coding_exception('filter_text() can not be used anymore, use format_text(), format_string() etc instead.');
}

/**
 * @deprecated Loginhttps is no longer supported
 */
function httpsrequired() {
    throw new coding_exception('httpsrequired() can not be used any more. Loginhttps is no longer supported.');
}

/**
 * Given a physical path to a file, returns the URL through which it can be reached in Moodle.
 *
 * @deprecated since 3.1 - replacement legacy file API methods can be found on the moodle_url class, for example:
 * The moodle_url::make_legacyfile_url() method can be used to generate a legacy course file url. To generate
 * course module file.php url the moodle_url::make_file_url() should be used.
 *
 * @param string $path Physical path to a file
 * @param array $options associative array of GET variables to append to the URL
 * @param string $type (questionfile|rssfile|httpscoursefile|coursefile)
 * @return string URL to file
 */
function get_file_url($path, $options=null, $type='coursefile') {
    debugging('Function get_file_url() is deprecated, please use moodle_url factory methods instead.', DEBUG_DEVELOPER);
    global $CFG;

    $path = str_replace('//', '/', $path);
    $path = trim($path, '/'); // no leading and trailing slashes

    // type of file
    switch ($type) {
       case 'questionfile':
            $url = $CFG->wwwroot."/question/exportfile.php";
            break;
       case 'rssfile':
            $url = $CFG->wwwroot."/rss/file.php";
            break;
         case 'coursefile':
        default:
            $url = $CFG->wwwroot."/file.php";
    }

    if ($CFG->slasharguments) {
        $parts = explode('/', $path);
        foreach ($parts as $key => $part) {
        /// anchor dash character should not be encoded
            $subparts = explode('#', $part);
            $subparts = array_map('rawurlencode', $subparts);
            $parts[$key] = implode('#', $subparts);
        }
        $path  = implode('/', $parts);
        $ffurl = $url.'/'.$path;
        $separator = '?';
    } else {
        $path = rawurlencode('/'.$path);
        $ffurl = $url.'?file='.$path;
        $separator = '&amp;';
    }

    if ($options) {
        foreach ($options as $name=>$value) {
            $ffurl = $ffurl.$separator.$name.'='.$value;
            $separator = '&amp;';
        }
    }

    return $ffurl;
}

/**
 * @deprecated use get_enrolled_users($context) instead.
 */
function get_course_participants($courseid) {
    throw new coding_exception('get_course_participants() can not be used any more, use get_enrolled_users() instead.');
}

/**
 * @deprecated use is_enrolled($context, $userid) instead.
 */
function is_course_participant($userid, $courseid) {
    throw new coding_exception('is_course_participant() can not be used any more, use is_enrolled() instead.');
}

/**
 * @deprecated
 */
function get_recent_enrolments($courseid, $timestart) {
    throw new coding_exception('get_recent_enrolments() is removed as it returned inaccurate results.');
}

/**
 * @deprecated use clean_param($string, PARAM_FILE) instead.
 */
function detect_munged_arguments($string, $allowdots=1) {
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
function mygroupid($courseid) {
    throw new coding_exception('mygroupid() can not be used any more, please use groups_get_all_groups() instead.');
}

/**
 * @deprecated since Moodle 2.0 MDL-14617 - please do not use this function any more.
 */
function groupmode($course, $cm=null) {
    throw new coding_exception('groupmode() can not be used any more, please use groups_get_* instead.');
}

/**
 * @deprecated Since year 2006 - please do not use this function any more.
 */
function set_current_group($courseid, $groupid) {
    throw new coding_exception('set_current_group() can not be used anymore, please use $SESSION->currentgroup[$courseid] instead');
}

/**
 * @deprecated Since year 2006 - please do not use this function any more.
 */
function get_current_group($courseid, $full = false) {
    throw new coding_exception('get_current_group() can not be used any more, please use groups_get_* instead');
}

/**
 * @deprecated Since Moodle 2.8
 */
function groups_filter_users_by_course_module_visible($cm, $users) {
    throw new coding_exception('groups_filter_users_by_course_module_visible() is removed. ' .
            'Replace with a call to \core_availability\info_module::filter_user_list(), ' .
            'which does basically the same thing but includes other restrictions such ' .
            'as profile restrictions.');
}

/**
 * @deprecated Since Moodle 2.8
 */
function groups_course_module_visible($cm, $userid=null) {
    throw new coding_exception('groups_course_module_visible() is removed, use $cm->uservisible to decide whether the current
        user can ' . 'access an activity.', DEBUG_DEVELOPER);
}

/**
 * @deprecated since 2.0
 */
function error($message, $link='') {
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
function formerr($error) {
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
function print_container($message, $clearfix=false, $classes='', $idbase='', $return=false) {
    throw new coding_exception('print_container() can not be used any more. Please use $OUTPUT->container() instead.');
}

/**
 * @deprecated use $OUTPUT->container_start() instead.
 */
function print_container_start($clearfix=false, $classes='', $idbase='', $return=false) {
    throw new coding_exception('print_container_start() can not be used any more. Please use $OUTPUT->container_start() instead.');
}

/**
 * @deprecated use $OUTPUT->container_end() instead.
 */
function print_container_end($return=false) {
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
function print_continue($link, $return = false) {
    throw new coding_exception('print_continue() can not be used any more. Please use $OUTPUT->continue_button() instead.');
}

/**
 * @deprecated use $PAGE methods instead.
 */
function print_header($title='', $heading='', $navigation='', $focus='',
                      $meta='', $cache=true, $button='&nbsp;', $menu=null,
                      $usexml=false, $bodytags='', $return=false) {

    throw new coding_exception('print_header() can not be used any more. Please use $PAGE methods instead.');
}

/**
 * @deprecated use $PAGE methods instead.
 */
function print_header_simple($title='', $heading='', $navigation='', $focus='', $meta='',
                       $cache=true, $button='&nbsp;', $menu='', $usexml=false, $bodytags='', $return=false) {

    throw new coding_exception('print_header_simple() can not be used any more. Please use $PAGE methods instead.');
}

/**
 * @deprecated use $OUTPUT->block() instead.
 */
function print_side_block($heading='', $content='', $list=NULL, $icons=NULL, $footer='', $attributes = array(), $title='') {
    throw new coding_exception('print_side_block() can not be used any more, please use $OUTPUT->block() instead.');
}

/**
 * Prints a basic textarea field.
 *
 * @deprecated since Moodle 2.0
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
    // debugging('print_textarea() has been deprecated. You should be using mforms and the editor element.');

    global $CFG;

    $mincols = 65;
    $minrows = 10;
    $str = '';

    if ($id === '') {
        $id = 'edit-'.$name;
    }

    if ($height && ($rows < $minrows)) {
        $rows = $minrows;
    }
    if ($width && ($cols < $mincols)) {
        $cols = $mincols;
    }

    editors_head_setup();
    $editor = editors_get_preferred_editor(FORMAT_HTML);
    $editor->set_text($value);
    $editor->use_editor($id, array('legacy'=>true));

    $str .= "\n".'<textarea class="form-textarea" id="'. $id .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'" spellcheck="true">'."\n";
    $str .= htmlspecialchars($value); // needed for editing of cleaned text!
    $str .= '</textarea>'."\n";

    if ($return) {
        return $str;
    }
    echo $str;
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
function choose_from_menu ($options, $name, $selected='', $nothing='choose', $script='',
                           $nothingvalue='0', $return=false, $disabled=false, $tabindex=0,
                           $id='', $listbox=false, $multiple=false, $class='') {
    throw new coding_exception('choose_from_menu() is removed. Please change your code to use html_writer::select().');

}

/**
 * @deprecated use $OUTPUT->help_icon_scale($courseid, $scale) instead.
 */
function print_scale_menu_helpbutton($courseid, $scale, $return=false) {
    throw new coding_exception('print_scale_menu_helpbutton() can not be used any more. '.
        'Please use $OUTPUT->help_icon_scale($courseid, $scale) instead.');
}

/**
 * @deprecated use html_writer::checkbox() instead.
 */
function print_checkbox($name, $value, $checked = true, $label = '', $alt = '', $script='', $return=false) {
    throw new coding_exception('print_checkbox() can not be used any more. Please use html_writer::checkbox() instead.');
}

/**
 * Prints the 'update this xxx' button that appears on module pages.
 *
 * @deprecated since Moodle 3.2
 *
 * @param string $cmid the course_module id.
 * @param string $ignored not used any more. (Used to be courseid.)
 * @param string $string the module name - get_string('modulename', 'xxx')
 * @return string the HTML for the button, if this user has permission to edit it, else an empty string.
 */
function update_module_button($cmid, $ignored, $string) {
    global $CFG, $OUTPUT;

    debugging('update_module_button() has been deprecated and should not be used anymore. Activity modules should not add the ' .
        'edit module button, the link is already available in the Administration block. Themes can choose to display the link ' .
        'in the buttons row consistently for all module types.', DEBUG_DEVELOPER);

    if (has_capability('moodle/course:manageactivities', context_module::instance($cmid))) {
        $string = get_string('updatethis', '', $string);

        $url = new moodle_url("$CFG->wwwroot/course/mod.php", array('update' => $cmid, 'return' => true, 'sesskey' => sesskey()));
        return $OUTPUT->single_button($url, $string);
    } else {
        return '';
    }
}

/**
 * @deprecated use $OUTPUT->navbar() instead
 */
function print_navigation ($navigation, $separator=0, $return=false) {
    throw new coding_exception('print_navigation() can not be used any more, please update use $OUTPUT->navbar() instead.');
}

/**
 * @deprecated Please use $PAGE->navabar methods instead.
 */
function build_navigation($extranavlinks, $cm = null) {
    throw new coding_exception('build_navigation() can not be used any more, please use $PAGE->navbar methods instead.');
}

/**
 * @deprecated not relevant with global navigation in Moodle 2.x+
 */
function navmenu($course, $cm=NULL, $targetwindow='self') {
    throw new coding_exception('navmenu() can not be used any more, it is no longer relevant with global navigation.');
}

/// CALENDAR MANAGEMENT  ////////////////////////////////////////////////////////////////


/**
 * @deprecated please use calendar_event::create() instead.
 */
function add_event($event) {
    throw new coding_exception('add_event() can not be used any more, please use calendar_event::create() instead.');
}

/**
 * @deprecated please calendar_event->update() instead.
 */
function update_event($event) {
    throw new coding_exception('update_event() is removed, please use calendar_event->update() instead.');
}

/**
 * @deprecated please use calendar_event->delete() instead.
 */
function delete_event($id) {
    throw new coding_exception('delete_event() can not be used any more, please use '.
        'calendar_event->delete() instead.');
}

/**
 * @deprecated please use calendar_event->toggle_visibility(false) instead.
 */
function hide_event($event) {
    throw new coding_exception('hide_event() can not be used any more, please use '.
        'calendar_event->toggle_visibility(false) instead.');
}

/**
 * @deprecated please use calendar_event->toggle_visibility(true) instead.
 */
function show_event($event) {
    throw new coding_exception('show_event() can not be used any more, please use '.
        'calendar_event->toggle_visibility(true) instead.');
}

/**
 * @deprecated since Moodle 2.2 use core_text::xxxx() instead.
 * @see core_text
 */
function textlib_get_instance() {
    throw new coding_exception('textlib_get_instance() can not be used any more, please use '.
        'core_text::functioname() instead.');
}

/**
 * @deprecated since 2.4
 * @see get_section_name()
 * @see format_base::get_section_name()

 */
function get_generic_section_name($format, stdClass $section) {
    throw new coding_exception('get_generic_section_name() is deprecated. Please use appropriate functionality from class format_base');
}

/**
 * Returns an array of sections for the requested course id
 *
 * It is usually not recommended to display the list of sections used
 * in course because the course format may have it's own way to do it.
 *
 * If you need to just display the name of the section please call:
 * get_section_name($course, $section)
 * {@link get_section_name()}
 * from 2.4 $section may also be just the field course_sections.section
 *
 * If you need the list of all sections it is more efficient to get this data by calling
 * $modinfo = get_fast_modinfo($courseorid);
 * $sections = $modinfo->get_section_info_all()
 * {@link get_fast_modinfo()}
 * {@link course_modinfo::get_section_info_all()}
 *
 * Information about one section (instance of section_info):
 * get_fast_modinfo($courseorid)->get_sections_info($section)
 * {@link course_modinfo::get_section_info()}
 *
 * @deprecated since 2.4
 */
function get_all_sections($courseid) {

    throw new coding_exception('get_all_sections() is removed. See phpdocs for this function');
}

/**
 * This function is deprecated, please use {@link course_add_cm_to_section()}
 * Note that course_add_cm_to_section() also updates field course_modules.section and
 * calls rebuild_course_cache()
 *
 * @deprecated since 2.4
 */
function add_mod_to_section($mod, $beforemod = null) {
    throw new coding_exception('Function add_mod_to_section() is removed, please use course_add_cm_to_section()');
}

/**
 * Returns a number of useful structures for course displays
 *
 * Function get_all_mods() is deprecated in 2.4
 * Instead of:
 * <code>
 * get_all_mods($courseid, $mods, $modnames, $modnamesplural, $modnamesused);
 * </code>
 * please use:
 * <code>
 * $mods = get_fast_modinfo($courseorid)->get_cms();
 * $modnames = get_module_types_names();
 * $modnamesplural = get_module_types_names(true);
 * $modnamesused = get_fast_modinfo($courseorid)->get_used_module_names();
 * </code>
 *
 * @deprecated since 2.4
 */
function get_all_mods($courseid, &$mods, &$modnames, &$modnamesplural, &$modnamesused) {
    throw new coding_exception('Function get_all_mods() is removed. Use get_fast_modinfo() and get_module_types_names() instead. See phpdocs for details');
}

/**
 * Returns course section - creates new if does not exist yet
 *
 * This function is deprecated. To create a course section call:
 * course_create_sections_if_missing($courseorid, $sections);
 * to get the section call:
 * get_fast_modinfo($courseorid)->get_section_info($sectionnum);
 *
 * @see course_create_sections_if_missing()
 * @see get_fast_modinfo()
 * @deprecated since 2.4
 */
function get_course_section($section, $courseid) {
    throw new coding_exception('Function get_course_section() is removed. Please use course_create_sections_if_missing() and get_fast_modinfo() instead.');
}

/**
 * @deprecated since 2.4
 * @see format_weeks::get_section_dates()
 */
function format_weeks_get_section_dates($section, $course) {
    throw new coding_exception('Function format_weeks_get_section_dates() is removed. It is not recommended to'.
            ' use it outside of format_weeks plugin');
}

/**
 * Deprecated. Instead of:
 * list($content, $name) = get_print_section_cm_text($cm, $course);
 * use:
 * $content = $cm->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
 * $name = $cm->get_formatted_name();
 *
 * @deprecated since 2.5
 * @see cm_info::get_formatted_content()
 * @see cm_info::get_formatted_name()
 */
function get_print_section_cm_text(cm_info $cm, $course) {
    throw new coding_exception('Function get_print_section_cm_text() is removed. Please use '.
            'cm_info::get_formatted_content() and cm_info::get_formatted_name()');
}

/**
 * Deprecated. Please use:
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * $output = $courserenderer->course_section_add_cm_control($course, $section, $sectionreturn,
 *    array('inblock' => $vertical));
 * echo $output;
 *
 * @deprecated since 2.5
 * @see core_course_renderer::course_section_add_cm_control()
 */
function print_section_add_menus($course, $section, $modnames = null, $vertical=false, $return=false, $sectionreturn=null) {
    throw new coding_exception('Function print_section_add_menus() is removed. Please use course renderer '.
            'function course_section_add_cm_control()');
}

/**
 * Deprecated. Please use:
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * $actions = course_get_cm_edit_actions($mod, $indent, $section);
 * return ' ' . $courserenderer->course_section_cm_edit_actions($actions);
 *
 * @deprecated since 2.5
 * @see course_get_cm_edit_actions()
 * @see core_course_renderer->course_section_cm_edit_actions()
 */
function make_editing_buttons(stdClass $mod, $absolute_ignored = true, $moveselect = true, $indent=-1, $section=null) {
    throw new coding_exception('Function make_editing_buttons() is removed, please see PHPdocs in '.
            'lib/deprecatedlib.php on how to replace it');
}

/**
 * Deprecated. Please use:
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * echo $courserenderer->course_section_cm_list($course, $section, $sectionreturn,
 *     array('hidecompletion' => $hidecompletion));
 *
 * @deprecated since 2.5
 * @see core_course_renderer::course_section_cm_list()
 */
function print_section($course, $section, $mods, $modnamesused, $absolute=false, $width="100%", $hidecompletion=false, $sectionreturn=null) {
    throw new coding_exception('Function print_section() is removed. Please use course renderer function '.
            'course_section_cm_list() instead.');
}

/**
 * @deprecated since 2.5
 */
function print_overview($courses, array $remote_courses=array()) {
    throw new coding_exception('Function print_overview() is removed. Use block course_overview to display this information');
}

/**
 * @deprecated since 2.5
 */
function print_recent_activity($course) {
    throw new coding_exception('Function print_recent_activity() is removed. It is not recommended to'.
            ' use it outside of block_recent_activity');
}

/**
 * @deprecated since 2.5
 */
function delete_course_module($id) {
    throw new coding_exception('Function delete_course_module() is removed. Please use course_delete_module() instead.');
}

/**
 * @deprecated since 2.5
 */
function update_category_button($categoryid = 0) {
    throw new coding_exception('Function update_category_button() is removed. Pages to view '.
            'and edit courses are now separate and no longer depend on editing mode.');
}

/**
 * This function is deprecated! For list of categories use
 * coursecat::make_all_categories($requiredcapability, $excludeid, $separator)
 * For parents of one particular category use
 * coursecat::get($id)->get_parents()
 *
 * @deprecated since 2.5
 */
function make_categories_list(&$list, &$parents, $requiredcapability = '',
        $excludeid = 0, $category = NULL, $path = "") {
    throw new coding_exception('Global function make_categories_list() is removed. Please use '.
            'coursecat::make_categories_list() and coursecat::get_parents()');
}

/**
 * @deprecated since 2.5
 */
function category_delete_move($category, $newparentid, $showfeedback=true) {
    throw new coding_exception('Function category_delete_move() is removed. Please use coursecat::delete_move() instead.');
}

/**
 * @deprecated since 2.5
 */
function category_delete_full($category, $showfeedback=true) {
    throw new coding_exception('Function category_delete_full() is removed. Please use coursecat::delete_full() instead.');
}

/**
 * This function is deprecated. Please use
 * $coursecat = coursecat::get($category->id);
 * if ($coursecat->can_change_parent($newparentcat->id)) {
 *     $coursecat->change_parent($newparentcat->id);
 * }
 *
 * Alternatively you can use
 * $coursecat->update(array('parent' => $newparentcat->id));
 *
 * @see coursecat::change_parent()
 * @see coursecat::update()
 * @deprecated since 2.5
 */
function move_category($category, $newparentcat) {
    throw new coding_exception('Function move_category() is removed. Please use coursecat::change_parent() instead.');
}

/**
 * This function is deprecated. Please use
 * coursecat::get($category->id)->hide();
 *
 * @see coursecat::hide()
 * @deprecated since 2.5
 */
function course_category_hide($category) {
    throw new coding_exception('Function course_category_hide() is removed. Please use coursecat::hide() instead.');
}

/**
 * This function is deprecated. Please use
 * coursecat::get($category->id)->show();
 *
 * @see coursecat::show()
 * @deprecated since 2.5
 */
function course_category_show($category) {
    throw new coding_exception('Function course_category_show() is removed. Please use coursecat::show() instead.');
}

/**
 * This function is deprecated.
 * To get the category with the specified it please use:
 * coursecat::get($catid, IGNORE_MISSING);
 * or
 * coursecat::get($catid, MUST_EXIST);
 *
 * To get the first available category please use
 * coursecat::get_default();
 *
 * @deprecated since 2.5
 */
function get_course_category($catid=0) {
    throw new coding_exception('Function get_course_category() is removed. Please use coursecat::get(), see phpdocs for more details');
}

/**
 * This function is deprecated. It is replaced with the method create() in class coursecat.
 * {@link coursecat::create()} also verifies the data, fixes sortorder and logs the action
 *
 * @deprecated since 2.5
 */
function create_course_category($category) {
    throw new coding_exception('Function create_course_category() is removed. Please use coursecat::create(), see phpdocs for more details');
}

/**
 * This function is deprecated.
 *
 * To get visible children categories of the given category use:
 * coursecat::get($categoryid)->get_children();
 * This function will return the array or coursecat objects, on each of them
 * you can call get_children() again
 *
 * @see coursecat::get()
 * @see coursecat::get_children()
 *
 * @deprecated since 2.5
 */
function get_all_subcategories($catid) {
    throw new coding_exception('Function get_all_subcategories() is removed. Please use appropriate methods() of coursecat
            class. See phpdocs for more details');
}

/**
 * This function is deprecated. Please use functions in class coursecat:
 * - coursecat::get($parentid)->has_children()
 * tells if the category has children (visible or not to the current user)
 *
 * - coursecat::get($parentid)->get_children()
 * returns an array of coursecat objects, each of them represents a children category visible
 * to the current user (i.e. visible=1 or user has capability to view hidden categories)
 *
 * - coursecat::get($parentid)->get_children_count()
 * returns number of children categories visible to the current user
 *
 * - coursecat::count_all()
 * returns total count of all categories in the system (both visible and not)
 *
 * - coursecat::get_default()
 * returns the first category (usually to be used if count_all() == 1)
 *
 * @deprecated since 2.5
 */
function get_child_categories($parentid) {
    throw new coding_exception('Function get_child_categories() is removed. Use coursecat::get_children() or see phpdocs for
            more details.');
}

/**
 *
 * @deprecated since 2.5
 *
 * This function is deprecated. Use appropriate functions from class coursecat.
 * Examples:
 *
 * coursecat::get($categoryid)->get_children()
 * - returns all children of the specified category as instances of class
 * coursecat, which means on each of them method get_children() can be called again.
 * Only categories visible to the current user are returned.
 *
 * coursecat::get(0)->get_children()
 * - returns all top-level categories visible to the current user.
 *
 * Sort fields can be specified, see phpdocs to {@link coursecat::get_children()}
 *
 * coursecat::make_categories_list()
 * - returns an array of all categories id/names in the system.
 * Also only returns categories visible to current user and can additionally be
 * filetered by capability, see phpdocs to {@link coursecat::make_categories_list()}
 *
 * make_categories_options()
 * - Returns full course categories tree to be used in html_writer::select()
 *
 * Also see functions {@link coursecat::get_children_count()}, {@link coursecat::count_all()},
 * {@link coursecat::get_default()}
 */
function get_categories($parent='none', $sort=NULL, $shallow=true) {
    throw new coding_exception('Function get_categories() is removed. Please use coursecat::get_children() or see phpdocs for other alternatives');
}

/**
* This function is deprecated, please use course renderer:
* $renderer = $PAGE->get_renderer('core', 'course');
* echo $renderer->course_search_form($value, $format);
*
* @deprecated since 2.5
*/
function print_course_search($value="", $return=false, $format="plain") {
    throw new coding_exception('Function print_course_search() is removed, please use course renderer');
}

/**
 * This function is deprecated, please use:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->frontpage_my_courses()
 *
 * @deprecated since 2.5
 */
function print_my_moodle() {
    throw new coding_exception('Function print_my_moodle() is removed, please use course renderer function frontpage_my_courses()');
}

/**
 * This function is deprecated, it is replaced with protected function
 * {@link core_course_renderer::frontpage_remote_course()}
 * It is only used from function {@link core_course_renderer::frontpage_my_courses()}
 *
 * @deprecated since 2.5
 */
function print_remote_course($course, $width="100%") {
    throw new coding_exception('Function print_remote_course() is removed, please use course renderer');
}

/**
 * This function is deprecated, it is replaced with protected function
 * {@link core_course_renderer::frontpage_remote_host()}
 * It is only used from function {@link core_course_renderer::frontpage_my_courses()}
 *
 * @deprecated since 2.5
 */
function print_remote_host($host, $width="100%") {
    throw new coding_exception('Function print_remote_host() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 *
 * See http://docs.moodle.org/dev/Courses_lists_upgrade_to_2.5
 */
function print_whole_category_list($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $showcourses = true, $categorycourses=NULL) {
    throw new coding_exception('Function print_whole_category_list() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 */
function print_category_info($category, $depth = 0, $showcourses = false, array $courses = null) {
    throw new coding_exception('Function print_category_info() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 *
 * This function is not used any more in moodle core and course renderer does not have render function for it.
 * Combo list on the front page is displayed as:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->frontpage_combo_list()
 *
 * The new class {@link coursecat} stores the information about course category tree
 * To get children categories use:
 * coursecat::get($id)->get_children()
 * To get list of courses use:
 * coursecat::get($id)->get_courses()
 *
 * See http://docs.moodle.org/dev/Courses_lists_upgrade_to_2.5
 */
function get_course_category_tree($id = 0, $depth = 0) {
    throw new coding_exception('Function get_course_category_tree() is removed, please use course renderer or coursecat class,
            see function phpdocs for more info');
}

/**
 * @deprecated since 2.5
 *
 * To print a generic list of courses use:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->courses_list($courses);
 *
 * To print list of all courses:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->frontpage_available_courses();
 *
 * To print list of courses inside category:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->course_category($category); // this will also print subcategories
 */
function print_courses($category) {
    throw new coding_exception('Function print_courses() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 *
 * Please use course renderer to display a course information box.
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->courses_list($courses); // will print list of courses
 * echo $renderer->course_info_box($course); // will print one course wrapped in div.generalbox
 */
function print_course($course, $highlightterms = '') {
    throw new coding_exception('Function print_course() is removed, please use course renderer');
}

/**
 * @deprecated since 2.5
 *
 * This function is not used any more in moodle core and course renderer does not have render function for it.
 * Combo list on the front page is displayed as:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->frontpage_combo_list()
 *
 * The new class {@link coursecat} stores the information about course category tree
 * To get children categories use:
 * coursecat::get($id)->get_children()
 * To get list of courses use:
 * coursecat::get($id)->get_courses()
 */
function get_category_courses_array($categoryid = 0) {
    throw new coding_exception('Function get_category_courses_array() is removed, please use methods of coursecat class');
}

/**
 * @deprecated since 2.5
 */
function get_category_courses_array_recursively(array &$flattened, $category) {
    throw new coding_exception('Function get_category_courses_array_recursively() is removed, please use methods of coursecat class', DEBUG_DEVELOPER);
}

/**
 * @deprecated since Moodle 2.5 MDL-27814 - please do not use this function any more.
 */
function blog_get_context_url($context=null) {
    throw new coding_exception('Function  blog_get_context_url() is removed, getting params from context is not reliable for blogs.');
}

/**
 * @deprecated since 2.5
 *
 * To get list of all courses with course contacts ('managers') use
 * coursecat::get(0)->get_courses(array('recursive' => true, 'coursecontacts' => true));
 *
 * To get list of courses inside particular category use
 * coursecat::get($id)->get_courses(array('coursecontacts' => true));
 *
 * Additionally you can specify sort order, offset and maximum number of courses,
 * see {@link coursecat::get_courses()}
 */
function get_courses_wmanagers($categoryid=0, $sort="c.sortorder ASC", $fields=array()) {
    throw new coding_exception('Function get_courses_wmanagers() is removed, please use coursecat::get_courses()');
}

/**
 * @deprecated since 2.5
 */
function convert_tree_to_html($tree, $row=0) {
    throw new coding_exception('Function convert_tree_to_html() is removed. Consider using class tabtree and core_renderer::render_tabtree()');
}

/**
 * @deprecated since 2.5
 */
function convert_tabrows_to_tree($tabrows, $selected, $inactive, $activated) {
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
 * @see context::instance_by_id($id)
 */
function get_context_instance_by_id($id, $strictness = IGNORE_MISSING) {
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
 * @see context::get_parent_context_ids()
 * @deprecated since 2.2, use $context->get_parent_context_ids() instead
 */
function get_parent_contexts(context $context, $includeself = false) {
    throw new coding_exception('get_parent_contexts() is removed, please use $context->get_parent_context_ids() instead.');
}

/**
 * @deprecated since Moodle 2.2
 * @see context::get_parent_context()
 */
function get_parent_contextid(context $context) {
    throw new coding_exception('get_parent_contextid() is removed, please use $context->get_parent_context() instead.');
}

/**
 * @see context::get_child_contexts()
 * @deprecated since 2.2
 */
function get_child_contexts(context $context) {
    throw new coding_exception('get_child_contexts() is removed, please use $context->get_child_contexts() instead.');
}

/**
 * @see context_helper::create_instances()
 * @deprecated since 2.2
 */
function create_contexts($contextlevel = null, $buildpaths = true) {
    throw new coding_exception('create_contexts() is removed, please use context_helper::create_instances() instead.');
}

/**
 * @see context_helper::cleanup_instances()
 * @deprecated since 2.2
 */
function cleanup_contexts() {
    throw new coding_exception('cleanup_contexts() is removed, please use context_helper::cleanup_instances() instead.');
}

/**
 * Populate context.path and context.depth where missing.
 *
 * @deprecated since 2.2
 */
function build_context_path($force = false) {
    throw new coding_exception('build_context_path() is removed, please use context_helper::build_all_paths() instead.');
}

/**
 * @deprecated since 2.2
 */
function rebuild_contexts(array $fixcontexts) {
    throw new coding_exception('rebuild_contexts() is removed, please use $context->reset_paths(true) instead.');
}

/**
 * @deprecated since Moodle 2.2
 * @see context_helper::preload_course()
 */
function preload_course_contexts($courseid) {
    throw new coding_exception('preload_course_contexts() is removed, please use context_helper::preload_course() instead.');
}

/**
 * @deprecated since Moodle 2.2
 * @see context::update_moved()
 */
function context_moved(context $context, context $newparent) {
    throw new coding_exception('context_moved() is removed, please use context::update_moved() instead.');
}

/**
 * @see context::get_capabilities()
 * @deprecated since 2.2
 */
function fetch_context_capabilities(context $context) {
    throw new coding_exception('fetch_context_capabilities() is removed, please use $context->get_capabilities() instead.');
}

/**
 * @deprecated since 2.2
 * @see context_helper::preload_from_record()
 */
function context_instance_preload(stdClass $rec) {
    throw new coding_exception('context_instance_preload() is removed, please use context_helper::preload_from_record() instead.');
}

/**
 * Returns context level name
 *
 * @deprecated since 2.2
 * @see context_helper::get_level_name()
 */
function get_contextlevel_name($contextlevel) {
    throw new coding_exception('get_contextlevel_name() is removed, please use context_helper::get_level_name() instead.');
}

/**
 * @deprecated since 2.2
 * @see context::get_context_name()
 */
function print_context_name(context $context, $withprefix = true, $short = false) {
    throw new coding_exception('print_context_name() is removed, please use $context->get_context_name() instead.');
}

/**
 * @deprecated since 2.2, use $context->mark_dirty() instead
 * @see context::mark_dirty()
 */
function mark_context_dirty($path) {
    throw new coding_exception('mark_context_dirty() is removed, please use $context->mark_dirty() instead.');
}

/**
 * @deprecated since Moodle 2.2
 * @see context_helper::delete_instance() or context::delete_content()
 */
function delete_context($contextlevel, $instanceid, $deleterecord = true) {
    if ($deleterecord) {
        throw new coding_exception('delete_context() is removed, please use context_helper::delete_instance() instead.');
    } else {
        throw new coding_exception('delete_context() is removed, please use $context->delete_content() instead.');
    }
}

/**
 * @deprecated since 2.2
 * @see context::get_url()
 */
function get_context_url(context $context) {
    throw new coding_exception('get_context_url() is removed, please use $context->get_url() instead.');
}

/**
 * @deprecated since 2.2
 * @see context::get_course_context()
 */
function get_course_context(context $context) {
    throw new coding_exception('get_course_context() is removed, please use $context->get_course_context(true) instead.');
}

/**
 * @deprecated since 2.2
 * @see enrol_get_users_courses()
 */
function get_user_courses_bycap($userid, $cap, $accessdata_ignored, $doanything_ignored, $sort = 'c.sortorder ASC', $fields = null, $limit_ignored = 0) {

    throw new coding_exception('get_user_courses_bycap() is removed, please use enrol_get_users_courses() instead.');
}

/**
 * @deprecated since Moodle 2.2
 */
function get_role_context_caps($roleid, context $context) {
    throw new coding_exception('get_role_context_caps() is removed, it is really slow. Don\'t use it.');
}

/**
 * @see context::get_course_context()
 * @deprecated since 2.2
 */
function get_courseid_from_context(context $context) {
    throw new coding_exception('get_courseid_from_context() is removed, please use $context->get_course_context(false) instead.');
}

/**
 * If you are using this methid, you should have something like this:
 *
 *    list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
 *
 * To prevent the use of this deprecated function, replace the line above with something similar to this:
 *
 *    $ctxselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
 *                                                                        ^
 *    $ctxjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
 *                                    ^       ^                ^        ^
 *    $params = array('contextlevel' => CONTEXT_COURSE);
 *                                      ^
 * @see context_helper:;get_preload_record_columns_sql()
 * @deprecated since 2.2
 */
function context_instance_preload_sql($joinon, $contextlevel, $tablealias) {
    throw new coding_exception('context_instance_preload_sql() is removed, please use context_helper::get_preload_record_columns_sql() instead.');
}

/**
 * @deprecated since 2.2
 * @see context::get_parent_context_ids()
 */
function get_related_contexts_string(context $context) {
    throw new coding_exception('get_related_contexts_string() is removed, please use $context->get_parent_context_ids(true) instead.');
}

/**
 * @deprecated since 2.6
 * @see core_component::get_plugin_list_with_file()
 */
function get_plugin_list_with_file($plugintype, $file, $include = false) {
    throw new coding_exception('get_plugin_list_with_file() is removed, please use core_component::get_plugin_list_with_file() instead.');
}

/**
 * @deprecated since 2.6
 */
function check_browser_operating_system($brand) {
    throw new coding_exception('check_browser_operating_system is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function check_browser_version($brand, $version = null) {
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
function get_device_type_list($incusertypes = true) {
    throw new coding_exception('get_device_type_list is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_selected_theme_for_device_type($devicetype = null) {
    throw new coding_exception('get_selected_theme_for_device_type is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function get_device_cfg_var_name($devicetype = null) {
    throw new coding_exception('get_device_cfg_var_name is removed, please update your code to use core_useragent instead.');
}

/**
 * @deprecated since 2.6
 */
function set_user_device_type($newdevice) {
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
 * @see core_user::get_support_user()
 */
function generate_email_supportuser() {
    throw new coding_exception('generate_email_supportuser is removed, please use core_user::get_support_user');
}

/**
 * @deprecated since Moodle 2.6
 */
function badges_get_issued_badge_info($hash) {
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
function count_login_failures($mode, $username, $lastlogin) {
    throw new coding_exception('count_login_failures() can not be used any more, please use user_count_login_failures().');
}

/**
 * @deprecated since 2.7 MDL-33099/MDL-44088 - please do not use this function any more.
 */
function ajaxenabled(array $browsers = null) {
    throw new coding_exception('ajaxenabled() can not be used anymore. Update your code to work with JS at all times.');
}

/**
 * @deprecated Since Moodle 2.7 MDL-44070
 */
function coursemodule_visible_for_user($cm, $userid=0) {
    throw new coding_exception('coursemodule_visible_for_user() can not be used any more,
            please use \core_availability\info_module::is_user_visible()');
}

/**
 * @deprecated since Moodle 2.8 MDL-36014, MDL-35618 this functionality is removed
 */
function enrol_cohort_get_cohorts(course_enrolment_manager $manager) {
    throw new coding_exception('Function enrol_cohort_get_cohorts() is removed, use enrol_cohort_search_cohorts() or '.
        'cohort_get_available_cohorts() instead');
}

/**
 * This function is deprecated, use {@link cohort_can_view_cohort()} instead since it also
 * takes into account current context
 *
 * @deprecated since Moodle 2.8 MDL-36014 please use cohort_can_view_cohort()
 */
function enrol_cohort_can_view_cohort($cohortid) {
    throw new coding_exception('Function enrol_cohort_can_view_cohort() is removed, use cohort_can_view_cohort() instead');
}

/**
 * It is advisable to use {@link cohort_get_available_cohorts()} instead.
 *
 * @deprecated since Moodle 2.8 MDL-36014 use cohort_get_available_cohorts() instead
 */
function cohort_get_visible_list($course, $onlyenrolled=true) {
    throw new coding_exception('Function cohort_get_visible_list() is removed. Please use function cohort_get_available_cohorts() ".
        "that correctly checks capabilities.');
}

/**
 * @deprecated since Moodle 2.8 MDL-35618 this functionality is removed
 */
function enrol_cohort_enrol_all_users(course_enrolment_manager $manager, $cohortid, $roleid) {
    throw new coding_exception('enrol_cohort_enrol_all_users() is removed. This functionality is moved to enrol_manual.');
}

/**
 * @deprecated since Moodle 2.8 MDL-35618 this functionality is removed
 */
function enrol_cohort_search_cohorts(course_enrolment_manager $manager, $offset = 0, $limit = 25, $search = '') {
    throw new coding_exception('enrol_cohort_search_cohorts() is removed. This functionality is moved to enrol_manual.');
}

/* === Apis deprecated in since Moodle 2.9 === */

/**
 * Is $USER one of the supplied users?
 *
 * $user2 will be null if viewing a user's recent conversations
 *
 * @deprecated since Moodle 2.9 MDL-49371 - please do not use this function any more.
 */
function message_current_user_is_involved($user1, $user2) {
    throw new coding_exception('message_current_user_is_involved() can not be used any more.');
}

/**
 * Print badges on user profile page.
 *
 * @deprecated since Moodle 2.9 MDL-45898 - please do not use this function any more.
 */
function profile_display_badges($userid, $courseid = 0) {
    throw new coding_exception('profile_display_badges() can not be used any more.');
}

/**
 * Adds user preferences elements to user edit form.
 *
 * @deprecated since Moodle 2.9 MDL-45774 - Please do not use this function any more.
 */
function useredit_shared_definition_preferences($user, &$mform, $editoroptions = null, $filemanageroptions = null) {
    throw new coding_exception('useredit_shared_definition_preferences() can not be used any more.');
}


/**
 * Convert region timezone to php supported timezone
 *
 * @deprecated since Moodle 2.9
 */
function calendar_normalize_tz($tz) {
    throw new coding_exception('calendar_normalize_tz() can not be used any more, please use core_date::normalise_timezone() instead.');
}

/**
 * Returns a float which represents the user's timezone difference from GMT in hours
 * Checks various settings and picks the most dominant of those which have a value
 * @deprecated since Moodle 2.9
 */
function get_user_timezone_offset($tz = 99) {
    throw new coding_exception('get_user_timezone_offset() can not be used any more, please use standard PHP DateTimeZone class instead');

}

/**
 * Returns an int which represents the systems's timezone difference from GMT in seconds
 * @deprecated since Moodle 2.9
 */
function get_timezone_offset($tz) {
    throw new coding_exception('get_timezone_offset() can not be used any more, please use standard PHP DateTimeZone class instead');
}

/**
 * Returns a list of timezones in the current language.
 * @deprecated since Moodle 2.9
 */
function get_list_of_timezones() {
    throw new coding_exception('get_list_of_timezones() can not be used any more, please use core_date::get_list_of_timezones() instead');
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 */
function update_timezone_records($timezones) {
    throw new coding_exception('update_timezone_records() can not be used any more, please use standard PHP DateTime class instead');
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 */
function calculate_user_dst_table($fromyear = null, $toyear = null, $strtimezone = null) {
    throw new coding_exception('calculate_user_dst_table() can not be used any more, please use standard PHP DateTime class instead');
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 */
function dst_changes_for_year($year, $timezone) {
    throw new coding_exception('dst_changes_for_year() can not be used any more, please use standard DateTime class instead');
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 */
function get_timezone_record($timezonename) {
    throw new coding_exception('get_timezone_record() can not be used any more, please use standard PHP DateTime class instead');
}

/* === Apis deprecated since Moodle 3.0 === */
/**
 * @deprecated since Moodle 3.0 MDL-49360 - please do not use this function any more.
 */
function get_referer($stripquery = true) {
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
function coursetag_get_tags($courseid, $userid=0, $tagtype='', $numtags=0, $unused = '') {
    throw new coding_exception('Function coursetag_get_tags() can not be used any more. Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_all_tags($unused='', $numtags=0) {
    throw new coding_exception('Function coursetag_get_all_tag() can not be used any more. Userid is no longer used for tagging courses.');
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
function coursetag_get_jscript_links($elementid, $coursetagslinks) {
    throw new coding_exception('Function coursetag_get_jscript_links() can not be used any more and is obsolete.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_records($courseid, $userid) {
    throw new coding_exception('Function coursetag_get_records() can not be used any more. Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_store_keywords($tags, $courseid, $userid=0, $tagtype='official', $myurl='') {
    throw new coding_exception('Function coursetag_store_keywords() can not be used any more. Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_delete_keyword($tagid, $userid, $courseid) {
    throw new coding_exception('Function coursetag_delete_keyword() can not be used any more. Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_get_tagged_courses($tagid) {
    throw new coding_exception('Function coursetag_get_tagged_courses() can not be used any more. Userid is no longer used for tagging courses.');
}

/**
 * @deprecated since 3.0
 */
function coursetag_delete_course_tags($courseid, $showfeedback=false) {
    throw new coding_exception('Function coursetag_delete_course_tags() is deprecated. Use core_tag_tag::remove_all_item_tags().');
}

/**
 * Set the type of a tag.  At this time (version 2.2) the possible values are 'default' or 'official'.  Official tags will be
 * displayed separately "at tagging time" (while selecting the tags to apply to a record).
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string   $tagid tagid to modify
 * @param    string   $type either 'default' or 'official'
 * @return   bool     true on success, false otherwise
 */
function tag_type_set($tagid, $type) {
    debugging('Function tag_type_set() is deprecated and can be replaced with use core_tag_tag::get($tagid)->update().', DEBUG_DEVELOPER);
    if ($tag = core_tag_tag::get($tagid, '*')) {
        return $tag->update(array('isstandard' => ($type === 'official') ? 1 : 0));
    }
    return false;
}

/**
 * Set the description of a tag
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    int      $tagid the id of the tag
 * @param    string   $description the tag's description string to be set
 * @param    int      $descriptionformat the moodle text format of the description
 *                    {@link http://docs.moodle.org/dev/Text_formats_2.0#Database_structure}
 * @return   bool     true on success, false otherwise
 */
function tag_description_set($tagid, $description, $descriptionformat) {
    debugging('Function tag_type_set() is deprecated and can be replaced with core_tag_tag::get($tagid)->update().', DEBUG_DEVELOPER);
    if ($tag = core_tag_tag::get($tagid, '*')) {
        return $tag->update(array('description' => $description, 'descriptionformat' => $descriptionformat));
    }
    return false;
}

/**
 * Get the array of db record of tags associated to a record (instances).
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param string $record_type the record type for which we want to get the tags
 * @param int $record_id the record id for which we want to get the tags
 * @param string $type the tag type (either 'default' or 'official'). By default, all tags are returned.
 * @param int $userid (optional) only required for course tagging
 * @return array the array of tags
 */
function tag_get_tags($record_type, $record_id, $type=null, $userid=0) {
    debugging('Method tag_get_tags() is deprecated and replaced with core_tag_tag::get_item_tags(). ' .
        'Component is now required when retrieving tag instances.', DEBUG_DEVELOPER);
    $standardonly = ($type === 'official' ? core_tag_tag::STANDARD_ONLY :
        (!empty($type) ? core_tag_tag::NOT_STANDARD_ONLY : core_tag_tag::BOTH_STANDARD_AND_NOT));
    $tags = core_tag_tag::get_item_tags(null, $record_type, $record_id, $standardonly, $userid);
    $rv = array();
    foreach ($tags as $id => $t) {
        $rv[$id] = $t->to_object();
    }
    return $rv;
}

/**
 * Get the array of tags display names, indexed by id.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string $record_type the record type for which we want to get the tags
 * @param    int    $record_id   the record id for which we want to get the tags
 * @param    string $type        the tag type (either 'default' or 'official'). By default, all tags are returned.
 * @return   array  the array of tags (with the value returned by core_tag_tag::make_display_name), indexed by id
 */
function tag_get_tags_array($record_type, $record_id, $type=null) {
    debugging('Method tag_get_tags_array() is deprecated and replaced with core_tag_tag::get_item_tags_array(). ' .
        'Component is now required when retrieving tag instances.', DEBUG_DEVELOPER);
    $standardonly = ($type === 'official' ? core_tag_tag::STANDARD_ONLY :
        (!empty($type) ? core_tag_tag::NOT_STANDARD_ONLY : core_tag_tag::BOTH_STANDARD_AND_NOT));
    return core_tag_tag::get_item_tags_array('', $record_type, $record_id, $standardonly);
}

/**
 * Get a comma-separated string of tags associated to a record.
 *
 * Use {@link tag_get_tags()} to get the same information in an array.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string   $record_type the record type for which we want to get the tags
 * @param    int      $record_id   the record id for which we want to get the tags
 * @param    int      $html        either TAG_RETURN_HTML or TAG_RETURN_TEXT, depending on the type of output desired
 * @param    string   $type        either 'official' or 'default', if null, all tags are returned
 * @return   string   the comma-separated list of tags.
 */
function tag_get_tags_csv($record_type, $record_id, $html=null, $type=null) {
    global $CFG, $OUTPUT;
    debugging('Method tag_get_tags_csv() is deprecated. Instead you should use either ' .
            'core_tag_tag::get_item_tags_array() or $OUTPUT->tag_list(core_tag_tag::get_item_tags()). ' .
        'Component is now required when retrieving tag instances.', DEBUG_DEVELOPER);
    $standardonly = ($type === 'official' ? core_tag_tag::STANDARD_ONLY :
        (!empty($type) ? core_tag_tag::NOT_STANDARD_ONLY : core_tag_tag::BOTH_STANDARD_AND_NOT));
    if ($html != TAG_RETURN_TEXT) {
        return $OUTPUT->tag_list(core_tag_tag::get_item_tags('', $record_type, $record_id, $standardonly), '');
    } else {
        return join(', ', core_tag_tag::get_item_tags_array('', $record_type, $record_id, $standardonly, 0, false));
    }
}

/**
 * Get an array of tag ids associated to a record.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string    $record_type the record type for which we want to get the tags
 * @param    int       $record_id the record id for which we want to get the tags
 * @return   array     tag ids, indexed and sorted by 'ordering'
 */
function tag_get_tags_ids($record_type, $record_id) {
    debugging('Method tag_get_tags_ids() is deprecated. Please consider using core_tag_tag::get_item_tags() or similar methods.', DEBUG_DEVELOPER);
    $tag_ids = array();
    $tagobjects = core_tag_tag::get_item_tags(null, $record_type, $record_id);
    foreach ($tagobjects as $tagobject) {
        $tag = $tagobject->to_object();
        if ( array_key_exists($tag->ordering, $tag_ids) ) {
            $tag->ordering++;
        }
        $tag_ids[$tag->ordering] = $tag->id;
    }
    ksort($tag_ids);
    return $tag_ids;
}

/**
 * Returns the database ID of a set of tags.
 *
 * @deprecated since 3.1
 * @param    mixed $tags one tag, or array of tags, to look for.
 * @param    bool  $return_value specify the type of the returned value. Either TAG_RETURN_OBJECT, or TAG_RETURN_ARRAY (default).
 *                               If TAG_RETURN_ARRAY is specified, an array will be returned even if only one tag was passed in $tags.
 * @return   mixed tag-indexed array of ids (or objects, if second parameter is TAG_RETURN_OBJECT), or only an int, if only one tag
 *                 is given *and* the second parameter is null. No value for a key means the tag wasn't found.
 */
function tag_get_id($tags, $return_value = null) {
    global $CFG, $DB;
    debugging('Method tag_get_id() is deprecated and can be replaced with core_tag_tag::get_by_name() or core_tag_tag::get_by_name_bulk(). ' .
        'You need to specify tag collection when retrieving tag by name', DEBUG_DEVELOPER);

    if (!is_array($tags)) {
        if(is_null($return_value) || $return_value == TAG_RETURN_OBJECT) {
            if ($tagobject = core_tag_tag::get_by_name(core_tag_collection::get_default(), $tags)) {
                return $tagobject->id;
            } else {
                return 0;
            }
        }
        $tags = array($tags);
    }

    $records = core_tag_tag::get_by_name_bulk(core_tag_collection::get_default(), $tags,
        $return_value == TAG_RETURN_OBJECT ? '*' : 'id, name');
    foreach ($records as $name => $record) {
        if ($return_value != TAG_RETURN_OBJECT) {
            $records[$name] = $record->id ? $record->id : null;
        } else {
            $records[$name] = $record->to_object();
        }
    }
    return $records;
}

/**
 * Change the "value" of a tag, and update the associated 'name'.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    int      $tagid  the id of the tag to modify
 * @param    string   $newrawname the new rawname
 * @return   bool     true on success, false otherwise
 */
function tag_rename($tagid, $newrawname) {
    debugging('Function tag_rename() is deprecated and may be replaced with core_tag_tag::get($tagid)->update().', DEBUG_DEVELOPER);
    if ($tag = core_tag_tag::get($tagid, '*')) {
        return $tag->update(array('rawname' => $newrawname));
    }
    return false;
}

/**
 * Delete one instance of a tag.  If the last instance was deleted, it will also delete the tag, unless its type is 'official'.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string $record_type the type of the record for which to remove the instance
 * @param    int    $record_id   the id of the record for which to remove the instance
 * @param    int    $tagid       the tagid that needs to be removed
 * @param    int    $userid      (optional) the userid
 * @return   bool   true on success, false otherwise
 */
function tag_delete_instance($record_type, $record_id, $tagid, $userid = null) {
    debugging('Function tag_delete_instance() is deprecated and replaced with core_tag_tag::remove_item_tag() instead. ' .
        'Component is required for retrieving instances', DEBUG_DEVELOPER);
    $tag = core_tag_tag::get($tagid);
    core_tag_tag::remove_item_tag('', $record_type, $record_id, $tag->rawname, $userid);
}

/**
 * Find all records tagged with a tag of a given type ('post', 'user', etc.)
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @category tag
 * @param    string   $tag       tag to look for
 * @param    string   $type      type to restrict search to.  If null, every matching record will be returned
 * @param    int      $limitfrom (optional, required if $limitnum is set) return a subset of records, starting at this point.
 * @param    int      $limitnum  (optional, required if $limitfrom is set) return a subset comprising this many records.
 * @return   array of matching objects, indexed by record id, from the table containing the type requested
 */
function tag_find_records($tag, $type, $limitfrom='', $limitnum='') {
    debugging('Function tag_find_records() is deprecated and replaced with core_tag_tag::get_by_name()->get_tagged_items(). '.
        'You need to specify tag collection when retrieving tag by name', DEBUG_DEVELOPER);

    if (!$tag || !$type) {
        return array();
    }

    $tagobject = core_tag_tag::get_by_name(core_tag_area::get_collection('', $type), $tag);
    return $tagobject->get_tagged_items('', $type, $limitfrom, $limitnum);
}

/**
 * Adds one or more tag in the database.  This function should not be called directly : you should
 * use tag_set.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   mixed    $tags     one tag, or an array of tags, to be created
 * @param   string   $type     type of tag to be created ("default" is the default value and "official" is the only other supported
 *                             value at this time). An official tag is kept even if there are no records tagged with it.
 * @return array     $tags ids indexed by their lowercase normalized names. Any boolean false in the array indicates an error while
 *                             adding the tag.
 */
function tag_add($tags, $type="default") {
    debugging('Function tag_add() is deprecated. You can use core_tag_tag::create_if_missing(), however it should not be necessary ' .
        'since tags are created automatically when assigned to items', DEBUG_DEVELOPER);
    if (!is_array($tags)) {
        $tags = array($tags);
    }
    $objects = core_tag_tag::create_if_missing(core_tag_collection::get_default(), $tags,
            $type === 'official');

    // New function returns the tags in different format, for BC we keep the format that this function used to have.
    $rv = array();
    foreach ($objects as $name => $tagobject) {
        if (isset($tagobject->id)) {
            $rv[$tagobject->name] = $tagobject->id;
        } else {
            $rv[$name] = false;
        }
    }
    return $rv;
}

/**
 * Assigns a tag to a record; if the record already exists, the time and ordering will be updated.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param string $record_type the type of the record that will be tagged
 * @param int $record_id the id of the record that will be tagged
 * @param string $tagid the tag id to set on the record.
 * @param int $ordering the order of the instance for this record
 * @param int $userid (optional) only required for course tagging
 * @param string|null $component the component that was tagged
 * @param int|null $contextid the context id of where this tag was assigned
 * @return bool true on success, false otherwise
 */
function tag_assign($record_type, $record_id, $tagid, $ordering, $userid = 0, $component = null, $contextid = null) {
    global $DB;
    $message = 'Function tag_assign() is deprecated. Use core_tag_tag::set_item_tags() or core_tag_tag::add_item_tag() instead. ' .
        'Tag instance ordering should not be set manually';
    if ($component === null || $contextid === null) {
        $message .= '. You should specify the component and contextid of the item being tagged in your call to tag_assign.';
    }
    debugging($message, DEBUG_DEVELOPER);

    if ($contextid) {
        $context = context::instance_by_id($contextid);
    } else {
        $context = context_system::instance();
    }

    // Get the tag.
    $tag = $DB->get_record('tag', array('id' => $tagid), 'name, rawname', MUST_EXIST);

    $taginstanceid = core_tag_tag::add_item_tag($component, $record_type, $record_id, $context, $tag->rawname, $userid);

    // Alter the "ordering" of tag_instance. This should never be done manually and only remains here for the backward compatibility.
    $taginstance = new stdClass();
    $taginstance->id = $taginstanceid;
    $taginstance->ordering     = $ordering;
    $taginstance->timemodified = time();

    $DB->update_record('tag_instance', $taginstance);

    return true;
}

/**
 * Count how many records are tagged with a specific tag.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   string   $record_type record to look for ('post', 'user', etc.)
 * @param   int      $tagid       is a single tag id
 * @return  int      number of mathing tags.
 */
function tag_record_count($record_type, $tagid) {
    debugging('Method tag_record_count() is deprecated and replaced with core_tag_tag::get($tagid)->count_tagged_items(). '.
        'Component is now required when retrieving tag instances.', DEBUG_DEVELOPER);
    return core_tag_tag::get($tagid)->count_tagged_items('', $record_type);
}

/**
 * Determine if a record is tagged with a specific tag
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   string   $record_type the record type to look for
 * @param   int      $record_id   the record id to look for
 * @param   string   $tag         a tag name
 * @return  bool/int true if it is tagged, 0 (false) otherwise
 */
function tag_record_tagged_with($record_type, $record_id, $tag) {
    debugging('Method tag_record_tagged_with() is deprecated and replaced with core_tag_tag::get($tagid)->is_item_tagged_with(). '.
        'Component is now required when retrieving tag instances.', DEBUG_DEVELOPER);
    return core_tag_tag::is_item_tagged_with('', $record_type, $record_id, $tag);
}

/**
 * Flag a tag as inappropriate.
 *
 * @deprecated since 3.1
 * @param int|array $tagids a single tagid, or an array of tagids
 */
function tag_set_flag($tagids) {
    debugging('Function tag_set_flag() is deprecated and replaced with core_tag_tag::get($tagid)->flag().', DEBUG_DEVELOPER);
    $tagids = (array) $tagids;
    foreach ($tagids as $tagid) {
        if ($tag = core_tag_tag::get($tagid, '*')) {
            $tag->flag();
        }
    }
}

/**
 * Remove the inappropriate flag on a tag.
 *
 * @deprecated since 3.1
 * @param int|array $tagids a single tagid, or an array of tagids
 */
function tag_unset_flag($tagids) {
    debugging('Function tag_unset_flag() is deprecated and replaced with core_tag_tag::get($tagid)->reset_flag().', DEBUG_DEVELOPER);
    $tagids = (array) $tagids;
    foreach ($tagids as $tagid) {
        if ($tag = core_tag_tag::get($tagid, '*')) {
            $tag->reset_flag();
        }
    }
}

/**
 * Prints or returns a HTML tag cloud with varying classes styles depending on the popularity and type of each tag.
 *
 * @deprecated since 3.1
 *
 * @param    array     $tagset Array of tags to display
 * @param    int       $nr_of_tags Limit for the number of tags to return/display, used if $tagset is null
 * @param    bool      $return     if true the function will return the generated tag cloud instead of displaying it.
 * @param    string    $sort (optional) selected sorting, default is alpha sort (name) also timemodified or popularity
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_cloud($tagset=null, $nr_of_tags=150, $return=false, $sort='') {
    global $OUTPUT;

    debugging('Function tag_print_cloud() is deprecated and replaced with function core_tag_collection::get_tag_cloud(), '
            . 'templateable core_tag\output\tagcloud and template core_tag/tagcloud.', DEBUG_DEVELOPER);

    // Set up sort global - used to pass sort type into core_tag_collection::cloud_sort through usort() avoiding multiple sort functions.
    if ($sort == 'popularity') {
        $sort = 'count';
    } else if ($sort == 'date') {
        $sort = 'timemodified';
    } else {
        $sort = 'name';
    }

    if (is_null($tagset)) {
        // No tag set received, so fetch tags from database.
        // Always add query by tagcollid even when it's not known to make use of the table index.
        $tagcloud = core_tag_collection::get_tag_cloud(0, false, $nr_of_tags, $sort);
    } else {
        $tagsincloud = $tagset;

        $etags = array();
        foreach ($tagsincloud as $tag) {
            $etags[] = $tag;
        }

        core_tag_collection::$cloudsortfield = $sort;
        usort($tagsincloud, "core_tag_collection::cloud_sort");

        $tagcloud = new \core_tag\output\tagcloud($tagsincloud);
    }

    $output = $OUTPUT->render_from_template('core_tag/tagcloud', $tagcloud->export_for_template($OUTPUT));
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Function that returns tags that start with some text, for use by the autocomplete feature
 *
 * @package core_tag
 * @deprecated since 3.0
 * @access  private
 * @param   string   $text string that the tag names will be matched against
 * @return  mixed    an array of objects, or false if no records were found or an error occured.
 */
function tag_autocomplete($text) {
    debugging('Function tag_autocomplete() is deprecated without replacement. ' .
            'New form element "tags" does proper autocomplete.', DEBUG_DEVELOPER);
    global $DB;
    return $DB->get_records_sql("SELECT tg.id, tg.name, tg.rawname
                                   FROM {tag} tg
                                  WHERE tg.name LIKE ?", array(core_text::strtolower($text)."%"));
}

/**
 * Prints a box with the description of a tag and its related tags
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   stdClass    $tag_object
 * @param   bool        $return     if true the function will return the generated tag cloud instead of displaying it.
 * @return  string/null a HTML box showing a description of the tag object and it's relationsips or null if output is done directly
 *                      in the function.
 */
function tag_print_description_box($tag_object, $return=false) {
    global $USER, $CFG, $OUTPUT;
    require_once($CFG->libdir.'/filelib.php');

    debugging('Function tag_print_description_box() is deprecated without replacement. ' .
            'See core_tag_renderer for similar code.', DEBUG_DEVELOPER);

    $relatedtags = array();
    if ($tag = core_tag_tag::get($tag_object->id)) {
        $relatedtags = $tag->get_related_tags();
    }

    $content = !empty($tag_object->description);
    $output = '';

    if ($content) {
        $output .= $OUTPUT->box_start('generalbox tag-description');
    }

    if (!empty($tag_object->description)) {
        $options = new stdClass();
        $options->para = false;
        $options->overflowdiv = true;
        $tag_object->description = file_rewrite_pluginfile_urls($tag_object->description, 'pluginfile.php', context_system::instance()->id, 'tag', 'description', $tag_object->id);
        $output .= format_text($tag_object->description, $tag_object->descriptionformat, $options);
    }

    if ($content) {
        $output .= $OUTPUT->box_end();
    }

    if ($relatedtags) {
        $output .= $OUTPUT->tag_list($relatedtags, get_string('relatedtags', 'tag'), 'tag-relatedtags');
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints a box that contains the management links of a tag
 *
 * @deprecated since 3.1
 * @param  core_tag_tag|stdClass    $tag_object
 * @param  bool        $return     if true the function will return the generated tag cloud instead of displaying it.
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_management_box($tag_object, $return=false) {
    global $USER, $CFG, $OUTPUT;

    debugging('Function tag_print_description_box() is deprecated without replacement. ' .
            'See core_tag_renderer for similar code.', DEBUG_DEVELOPER);

    $tagname  = core_tag_tag::make_display_name($tag_object);
    $output = '';

    if (!isguestuser()) {
        $output .= $OUTPUT->box_start('box','tag-management-box');
        $systemcontext   = context_system::instance();
        $links = array();

        // Add a link for users to add/remove this from their interests
        if (core_tag_tag::is_enabled('core', 'user') && core_tag_area::get_collection('core', 'user') == $tag_object->tagcollid) {
            if (core_tag_tag::is_item_tagged_with('core', 'user', $USER->id, $tag_object->name)) {
                $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=removeinterest&amp;sesskey='. sesskey() .
                        '&amp;tag='. rawurlencode($tag_object->name) .'">'.
                        get_string('removetagfrommyinterests', 'tag', $tagname) .'</a>';
            } else {
                $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=addinterest&amp;sesskey='. sesskey() .
                        '&amp;tag='. rawurlencode($tag_object->name) .'">'.
                        get_string('addtagtomyinterests', 'tag', $tagname) .'</a>';
            }
        }

        // Flag as inappropriate link.  Only people with moodle/tag:flag capability.
        if (has_capability('moodle/tag:flag', $systemcontext)) {
            $links[] = '<a href="'. $CFG->wwwroot .'/tag/user.php?action=flaginappropriate&amp;sesskey='.
                    sesskey() . '&amp;id='. $tag_object->id . '">'. get_string('flagasinappropriate',
                            'tag', rawurlencode($tagname)) .'</a>';
        }

        // Edit tag: Only people with moodle/tag:edit capability who either have it as an interest or can manage tags
        if (has_capability('moodle/tag:edit', $systemcontext) ||
            has_capability('moodle/tag:manage', $systemcontext)) {
            $links[] = '<a href="' . $CFG->wwwroot . '/tag/edit.php?id=' . $tag_object->id . '">' .
                    get_string('edittag', 'tag') . '</a>';
        }

        $output .= implode(' | ', $links);
        $output .= $OUTPUT->box_end();
    }

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints the tag search box
 *
 * @deprecated since 3.1
 * @param  bool        $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_search_box($return=false) {
    global $CFG, $OUTPUT;

    debugging('Function tag_print_search_box() is deprecated without replacement. ' .
            'See core_tag_renderer for similar code.', DEBUG_DEVELOPER);

    $query = optional_param('query', '', PARAM_RAW);

    $output = $OUTPUT->box_start('','tag-search-box');
    $output .= '<form action="'.$CFG->wwwroot.'/tag/search.php" style="display:inline">';
    $output .= '<div>';
    $output .= '<label class="accesshide" for="searchform_search">'.get_string('searchtags', 'tag').'</label>';
    $output .= '<input id="searchform_search" name="query" type="text" size="40" value="'.s($query).'" />';
    $output .= '<button id="searchform_button" type="submit">'. get_string('search', 'tag') .'</button><br />';
    $output .= '</div>';
    $output .= '</form>';
    $output .= $OUTPUT->box_end();

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints the tag search results
 *
 * @deprecated since 3.1
 * @param string       $query text that tag names will be matched against
 * @param int          $page current page
 * @param int          $perpage nr of users displayed per page
 * @param bool         $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_search_results($query,  $page, $perpage, $return=false) {
    global $CFG, $USER, $OUTPUT;

    debugging('Function tag_print_search_results() is deprecated without replacement. ' .
            'In /tag/search.php the search results are printed using the core_tag/tagcloud template.', DEBUG_DEVELOPER);

    $query = clean_param($query, PARAM_TAG);

    $count = count(tag_find_tags($query, false));
    $tags = array();

    if ( $found_tags = tag_find_tags($query, true,  $page * $perpage, $perpage) ) {
        $tags = array_values($found_tags);
    }

    $baseurl = $CFG->wwwroot.'/tag/search.php?query='. rawurlencode($query);
    $output = '';

    // link "Add $query to my interests"
    $addtaglink = '';
    if (core_tag_tag::is_enabled('core', 'user') && !core_tag_tag::is_item_tagged_with('core', 'user', $USER->id, $query)) {
        $addtaglink = html_writer::link(new moodle_url('/tag/user.php', array('action' => 'addinterest', 'sesskey' => sesskey(),
            'tag' => $query)), get_string('addtagtomyinterests', 'tag', s($query)));
    }

    if ( !empty($tags) ) { // there are results to display!!
        $output .= $OUTPUT->heading(get_string('searchresultsfor', 'tag', htmlspecialchars($query)) ." : {$count}", 3, 'main');

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            $output .= $OUTPUT->box($addtaglink, 'box', 'tag-management-box');
        }

        $nr_of_lis_per_ul = 6;
        $nr_of_uls = ceil( sizeof($tags) / $nr_of_lis_per_ul );

        $output .= '<ul id="tag-search-results">';
        for($i = 0; $i < $nr_of_uls; $i++) {
            foreach (array_slice($tags, $i * $nr_of_lis_per_ul, $nr_of_lis_per_ul) as $tag) {
                $output .= '<li>';
                $tag_link = html_writer::link(core_tag_tag::make_url($tag->tagcollid, $tag->rawname),
                    core_tag_tag::make_display_name($tag));
                $output .= $tag_link;
                $output .= '</li>';
            }
        }
        $output .= '</ul>';
        $output .= '<div>&nbsp;</div>'; // <-- small layout hack in order to look good in Firefox

        $output .= $OUTPUT->paging_bar($count, $page, $perpage, $baseurl);
    }
    else { //no results were found!!
        $output .= $OUTPUT->heading(get_string('noresultsfor', 'tag', htmlspecialchars($query)), 3, 'main');

        //print a link "Add $query to my interests"
        if (!empty($addtaglink)) {
            $output .= $OUTPUT->box($addtaglink, 'box', 'tag-management-box');
        }
    }

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints a table of the users tagged with the tag passed as argument
 *
 * @deprecated since 3.1
 * @param  stdClass    $tagobject the tag we wish to return data for
 * @param  int         $limitfrom (optional, required if $limitnum is set) prints users starting at this point.
 * @param  int         $limitnum (optional, required if $limitfrom is set) prints this many users.
 * @param  bool        $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_tagged_users_table($tagobject, $limitfrom='', $limitnum='', $return=false) {

    debugging('Function tag_print_tagged_users_table() is deprecated without replacement. ' .
            'See core_user_renderer for similar code.', DEBUG_DEVELOPER);

    //List of users with this tag
    $tagobject = core_tag_tag::get($tagobject->id);
    $userlist = $tagobject->get_tagged_items('core', 'user', $limitfrom, $limitnum);

    $output = tag_print_user_list($userlist, true);

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints an individual user box
 *
 * @deprecated since 3.1
 * @param user_object  $user  (contains the following fields: id, firstname, lastname and picture)
 * @param bool         $return if true return html string
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_user_box($user, $return=false) {
    global $CFG, $OUTPUT;

    debugging('Function tag_print_user_box() is deprecated without replacement. ' .
            'See core_user_renderer for similar code.', DEBUG_DEVELOPER);

    $usercontext = context_user::instance($user->id);
    $profilelink = '';

    if ($usercontext and (has_capability('moodle/user:viewdetails', $usercontext) || has_coursecontact_role($user->id))) {
        $profilelink = $CFG->wwwroot .'/user/view.php?id='. $user->id;
    }

    $output = $OUTPUT->box_start('user-box', 'user'. $user->id);
    $fullname = fullname($user);
    $alt = '';

    if (!empty($profilelink)) {
        $output .= '<a href="'. $profilelink .'">';
        $alt = $fullname;
    }

    $output .= $OUTPUT->user_picture($user, array('size'=>100));
    $output .= '<br />';

    if (!empty($profilelink)) {
        $output .= '</a>';
    }

    //truncate name if it's too big
    if (core_text::strlen($fullname) > 26) {
        $fullname = core_text::substr($fullname, 0, 26) .'...';
    }

    $output .= '<strong>'. $fullname .'</strong>';
    $output .= $OUTPUT->box_end();

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Prints a list of users
 *
 * @deprecated since 3.1
 * @param  array       $userlist an array of user objects
 * @param  bool        $return if true return html string, otherwise output the result
 * @return string|null a HTML string or null if this function does the output
 */
function tag_print_user_list($userlist, $return=false) {

    debugging('Function tag_print_user_list() is deprecated without replacement. ' .
            'See core_user_renderer for similar code.', DEBUG_DEVELOPER);

    $output = '<div><ul class="inline-list">';

    foreach ($userlist as $user){
        $output .= '<li>'. tag_print_user_box($user, true) ."</li>\n";
    }
    $output .= "</ul></div>\n";

    if ($return) {
        return $output;
    }
    else {
        echo $output;
    }
}

/**
 * Function that returns the name that should be displayed for a specific tag
 *
 * @package  core_tag
 * @category tag
 * @deprecated since 3.1
 * @param    stdClass|core_tag_tag   $tagobject a line out of tag table, as returned by the adobd functions
 * @param    int      $html TAG_RETURN_HTML (default) will return htmlspecialchars encoded string, TAG_RETURN_TEXT will not encode.
 * @return   string
 */
function tag_display_name($tagobject, $html=TAG_RETURN_HTML) {
    debugging('Function tag_display_name() is deprecated. Use core_tag_tag::make_display_name().', DEBUG_DEVELOPER);
    if (!isset($tagobject->name)) {
        return '';
    }
    return core_tag_tag::make_display_name($tagobject, $html != TAG_RETURN_TEXT);
}

/**
 * Function that normalizes a list of tag names.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   array/string $rawtags array of tags, or a single tag.
 * @param   int          $case    case to use for returned value (default: lower case). Either TAG_CASE_LOWER (default) or TAG_CASE_ORIGINAL
 * @return  array        lowercased normalized tags, indexed by the normalized tag, in the same order as the original array.
 *                       (Eg: 'Banana' => 'banana').
 */
function tag_normalize($rawtags, $case = TAG_CASE_LOWER) {
    debugging('Function tag_normalize() is deprecated. Use core_tag_tag::normalize().', DEBUG_DEVELOPER);

    if ( !is_array($rawtags) ) {
        $rawtags = array($rawtags);
    }

    return core_tag_tag::normalize($rawtags, $case == TAG_CASE_LOWER);
}

/**
 * Get a comma-separated list of tags related to another tag.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    array    $related_tags the array returned by tag_get_related_tags
 * @param    int      $html    either TAG_RETURN_HTML (default) or TAG_RETURN_TEXT : return html links, or just text.
 * @return   string   comma-separated list
 */
function tag_get_related_tags_csv($related_tags, $html=TAG_RETURN_HTML) {
    global $OUTPUT;
    debugging('Method tag_get_related_tags_csv() is deprecated. Consider '
            . 'looping through array or using $OUTPUT->tag_list(core_tag_tag::get_item_tags())',
        DEBUG_DEVELOPER);
    if ($html != TAG_RETURN_TEXT) {
        return $OUTPUT->tag_list($related_tags, '');
    }

    $tagsnames = array();
    foreach ($related_tags as $tag) {
        $tagsnames[] = core_tag_tag::make_display_name($tag, false);
    }
    return implode(', ', $tagsnames);
}

/**
 * Used to require that the return value from a function is an array.
 * Only used in the deprecated function {@link tag_get_id()}
 * @deprecated since 3.1
 */
define('TAG_RETURN_ARRAY', 0);
/**
 * Used to require that the return value from a function is an object.
 * Only used in the deprecated function {@link tag_get_id()}
 * @deprecated since 3.1
 */
define('TAG_RETURN_OBJECT', 1);
/**
 * Use to specify that HTML free text is expected to be returned from a function.
 * Only used in deprecated functions {@link tag_get_tags_csv()}, {@link tag_display_name()},
 * {@link tag_get_related_tags_csv()}
 * @deprecated since 3.1
 */
define('TAG_RETURN_TEXT', 2);
/**
 * Use to specify that encoded HTML is expected to be returned from a function.
 * Only used in deprecated functions {@link tag_get_tags_csv()}, {@link tag_display_name()},
 * {@link tag_get_related_tags_csv()}
 * @deprecated since 3.1
 */
define('TAG_RETURN_HTML', 3);

/**
 * Used to specify that we wish a lowercased string to be returned
 * Only used in deprecated function {@link tag_normalize()}
 * @deprecated since 3.1
 */
define('TAG_CASE_LOWER', 0);
/**
 * Used to specify that we do not wish the case of the returned string to change
 * Only used in deprecated function {@link tag_normalize()}
 * @deprecated since 3.1
 */
define('TAG_CASE_ORIGINAL', 1);

/**
 * Used to specify that we want all related tags returned, no matter how they are related.
 * Only used in deprecated function {@link tag_get_related_tags()}
 * @deprecated since 3.1
 */
define('TAG_RELATED_ALL', 0);
/**
 * Used to specify that we only want back tags that were manually related.
 * Only used in deprecated function {@link tag_get_related_tags()}
 * @deprecated since 3.1
 */
define('TAG_RELATED_MANUAL', 1);
/**
 * Used to specify that we only want back tags where the relationship was automatically correlated.
 * Only used in deprecated function {@link tag_get_related_tags()}
 * @deprecated since 3.1
 */
define('TAG_RELATED_CORRELATED', 2);

/**
 * Set the tags assigned to a record.  This overwrites the current tags.
 *
 * This function is meant to be fed the string coming up from the user interface, which contains all tags assigned to a record.
 *
 * Due to API change $component and $contextid are now required. Instead of
 * calling  this function you can use {@link core_tag_tag::set_item_tags()} or
 * {@link core_tag_tag::set_related_tags()}
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, 'tag' for tags, etc.)
 * @param int $itemid the id of the record to tag
 * @param array $tags the array of tags to set on the record. If given an empty array, all tags will be removed.
 * @param string|null $component the component that was tagged
 * @param int|null $contextid the context id of where this tag was assigned
 * @return bool|null
 */
function tag_set($itemtype, $itemid, $tags, $component = null, $contextid = null) {
    debugging('Function tag_set() is deprecated. Use ' .
        ' core_tag_tag::set_item_tags() instead', DEBUG_DEVELOPER);

    if ($itemtype === 'tag') {
        return core_tag_tag::get($itemid, '*', MUST_EXIST)->set_related_tags($tags);
    } else {
        $context = $contextid ? context::instance_by_id($contextid) : context_system::instance();
        return core_tag_tag::set_item_tags($component, $itemtype, $itemid, $context, $tags);
    }
}

/**
 * Adds a tag to a record, without overwriting the current tags.
 *
 * This function remains here for backward compatiblity. It is recommended to use
 * {@link core_tag_tag::add_item_tag()} or {@link core_tag_tag::add_related_tags()} instead
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, etc.)
 * @param int $itemid the id of the record to tag
 * @param string $tag the tag to add
 * @param string|null $component the component that was tagged
 * @param int|null $contextid the context id of where this tag was assigned
 * @return bool|null
 */
function tag_set_add($itemtype, $itemid, $tag, $component = null, $contextid = null) {
    debugging('Function tag_set_add() is deprecated. Use ' .
        ' core_tag_tag::add_item_tag() instead', DEBUG_DEVELOPER);

    if ($itemtype === 'tag') {
        return core_tag_tag::get($itemid, '*', MUST_EXIST)->add_related_tags(array($tag));
    } else {
        $context = $contextid ? context::instance_by_id($contextid) : context_system::instance();
        return core_tag_tag::add_item_tag($component, $itemtype, $itemid, $context, $tag);
    }
}

/**
 * Removes a tag from a record, without overwriting other current tags.
 *
 * This function remains here for backward compatiblity. It is recommended to use
 * {@link core_tag_tag::remove_item_tag()} instead
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param string $itemtype the type of record to tag ('post' for blogs, 'user' for users, etc.)
 * @param int $itemid the id of the record to tag
 * @param string $tag the tag to delete
 * @param string|null $component the component that was tagged
 * @param int|null $contextid the context id of where this tag was assigned
 * @return bool|null
 */
function tag_set_delete($itemtype, $itemid, $tag, $component = null, $contextid = null) {
    debugging('Function tag_set_delete() is deprecated. Use ' .
        ' core_tag_tag::remove_item_tag() instead', DEBUG_DEVELOPER);
    return core_tag_tag::remove_item_tag($component, $itemtype, $itemid, $tag);
}

/**
 * Simple function to just return a single tag object when you know the name or something
 *
 * See also {@link core_tag_tag::get()} and {@link core_tag_tag::get_by_name()}
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string $field        which field do we use to identify the tag: id, name or rawname
 * @param    string $value        the required value of the aforementioned field
 * @param    string $returnfields which fields do we want returned. This is a comma seperated string containing any combination of
 *                                'id', 'name', 'rawname' or '*' to include all fields.
 * @return   mixed  tag object
 */
function tag_get($field, $value, $returnfields='id, name, rawname, tagcollid') {
    global $DB;
    debugging('Function tag_get() is deprecated. Use ' .
        ' core_tag_tag::get() or core_tag_tag::get_by_name()',
        DEBUG_DEVELOPER);
    if ($field === 'id') {
        $tag = core_tag_tag::get((int)$value, $returnfields);
    } else if ($field === 'name') {
        $tag = core_tag_tag::get_by_name(0, $value, $returnfields);
    } else {
        $params = array($field => $value);
        return $DB->get_record('tag', $params, $returnfields);
    }
    if ($tag) {
        return $tag->to_object();
    }
    return null;
}

/**
 * Returns tags related to a tag
 *
 * Related tags of a tag come from two sources:
 *   - manually added related tags, which are tag_instance entries for that tag
 *   - correlated tags, which are calculated
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    string   $tagid          is a single **normalized** tag name or the id of a tag
 * @param    int      $type           the function will return either manually (TAG_RELATED_MANUAL) related tags or correlated
 *                                    (TAG_RELATED_CORRELATED) tags. Default is TAG_RELATED_ALL, which returns everything.
 * @param    int      $limitnum       (optional) return a subset comprising this many records, the default is 10
 * @return   array    an array of tag objects
 */
function tag_get_related_tags($tagid, $type=TAG_RELATED_ALL, $limitnum=10) {
    debugging('Method tag_get_related_tags() is deprecated, '
        . 'use core_tag_tag::get_correlated_tags(), core_tag_tag::get_related_tags() or '
        . 'core_tag_tag::get_manual_related_tags()', DEBUG_DEVELOPER);
    $result = array();
    if ($tag = core_tag_tag::get($tagid)) {
        if ($type == TAG_RELATED_CORRELATED) {
            $tags = $tag->get_correlated_tags();
        } else if ($type == TAG_RELATED_MANUAL) {
            $tags = $tag->get_manual_related_tags();
        } else {
            $tags = $tag->get_related_tags();
        }
        $tags = array_slice($tags, 0, $limitnum);
        foreach ($tags as $id => $tag) {
            $result[$id] = $tag->to_object();
        }
    }
    return $result;
}

/**
 * Delete one or more tag, and all their instances if there are any left.
 *
 * @package  core_tag
 * @deprecated since 3.1
 * @param    mixed    $tagids one tagid (int), or one array of tagids to delete
 * @return   bool     true on success, false otherwise
 */
function tag_delete($tagids) {
    debugging('Method tag_delete() is deprecated, use core_tag_tag::delete_tags()',
        DEBUG_DEVELOPER);
    return core_tag_tag::delete_tags($tagids);
}

/**
 * Deletes all the tag instances given a component and an optional contextid.
 *
 * @deprecated since 3.1
 * @param string $component
 * @param int $contextid if null, then we delete all tag instances for the $component
 */
function tag_delete_instances($component, $contextid = null) {
    debugging('Method tag_delete() is deprecated, use core_tag_tag::delete_instances()',
        DEBUG_DEVELOPER);
    core_tag_tag::delete_instances($component, null, $contextid);
}

/**
 * Clean up the tag tables, making sure all tagged object still exists.
 *
 * This should normally not be necessary, but in case related tags are not deleted when the tagged record is removed, this should be
 * done once in a while, perhaps on an occasional cron run.  On a site with lots of tags, this could become an expensive function to
 * call: don't run at peak time.
 *
 * @package core_tag
 * @deprecated since 3.1
 */
function tag_cleanup() {
    debugging('Method tag_cleanup() is deprecated, use \core\task\tag_cron_task::cleanup()',
        DEBUG_DEVELOPER);

    $task = new \core\task\tag_cron_task();
    return $task->cleanup();
}

/**
 * This function will delete numerous tag instances efficiently.
 * This removes tag instances only. It doesn't check to see if it is the last use of a tag.
 *
 * @deprecated since 3.1
 * @param array $instances An array of tag instance objects with the addition of the tagname and tagrawname
 *        (used for recording a delete event).
 */
function tag_bulk_delete_instances($instances) {
    debugging('Method tag_bulk_delete_instances() is deprecated, '
        . 'use \core\task\tag_cron_task::bulk_delete_instances()',
        DEBUG_DEVELOPER);

    $task = new \core\task\tag_cron_task();
    return $task->bulk_delete_instances($instances);
}

/**
 * Calculates and stores the correlated tags of all tags. The correlations are stored in the 'tag_correlation' table.
 *
 * Two tags are correlated if they appear together a lot. Ex.: Users tagged with "computers" will probably also be tagged with "algorithms".
 *
 * The rationale for the 'tag_correlation' table is performance. It works as a cache for a potentially heavy load query done at the
 * 'tag_instance' table. So, the 'tag_correlation' table stores redundant information derived from the 'tag_instance' table.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   int      $mincorrelation Only tags with more than $mincorrelation correlations will be identified.
 */
function tag_compute_correlations($mincorrelation = 2) {
    debugging('Method tag_compute_correlations() is deprecated, '
        . 'use \core\task\tag_cron_task::compute_correlations()',
        DEBUG_DEVELOPER);

    $task = new \core\task\tag_cron_task();
    return $task->compute_correlations($mincorrelation);
}

/**
 * This function processes a tag correlation and makes changes in the database as required.
 *
 * The tag correlation object needs have both a tagid property and a correlatedtags property that is an array.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   stdClass $tagcorrelation
 * @return  int/bool The id of the tag correlation that was just processed or false.
 */
function tag_process_computed_correlation(stdClass $tagcorrelation) {
    debugging('Method tag_process_computed_correlation() is deprecated, '
        . 'use \core\task\tag_cron_task::process_computed_correlation()',
        DEBUG_DEVELOPER);

    $task = new \core\task\tag_cron_task();
    return $task->process_computed_correlation($tagcorrelation);
}

/**
 * Tasks that should be performed at cron time
 *
 * @package core_tag
 * @deprecated since 3.1
 */
function tag_cron() {
    debugging('Method tag_cron() is deprecated, use \core\task\tag_cron_task::execute()',
        DEBUG_DEVELOPER);

    $task = new \core\task\tag_cron_task();
    $task->execute();
}

/**
 * Search for tags with names that match some text
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   string        $text      escaped string that the tag names will be matched against
 * @param   bool          $ordered   If true, tags are ordered by their popularity. If false, no ordering.
 * @param   int/string    $limitfrom (optional, required if $limitnum is set) return a subset of records, starting at this point.
 * @param   int/string    $limitnum  (optional, required if $limitfrom is set) return a subset comprising this many records.
 * @param   int           $tagcollid
 * @return  array/boolean an array of objects, or false if no records were found or an error occured.
 */
function tag_find_tags($text, $ordered=true, $limitfrom='', $limitnum='', $tagcollid = null) {
    debugging('Method tag_find_tags() is deprecated without replacement', DEBUG_DEVELOPER);
    global $DB;

    $text = core_text::strtolower(clean_param($text, PARAM_TAG));

    list($sql, $params) = $DB->get_in_or_equal($tagcollid ? array($tagcollid) :
        array_keys(core_tag_collection::get_collections(true)));
    array_unshift($params, "%{$text}%");

    if ($ordered) {
        $query = "SELECT tg.id, tg.name, tg.rawname, tg.tagcollid, COUNT(ti.id) AS count
                    FROM {tag} tg LEFT JOIN {tag_instance} ti ON tg.id = ti.tagid
                   WHERE tg.name LIKE ? AND tg.tagcollid $sql
                GROUP BY tg.id, tg.name, tg.rawname
                ORDER BY count DESC";
    } else {
        $query = "SELECT tg.id, tg.name, tg.rawname, tg.tagcollid
                    FROM {tag} tg
                   WHERE tg.name LIKE ? AND tg.tagcollid $sql";
    }
    return $DB->get_records_sql($query, $params, $limitfrom , $limitnum);
}

/**
 * Get the name of a tag
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   mixed    $tagids the id of the tag, or an array of ids
 * @return  mixed    string name of one tag, or id-indexed array of strings
 */
function tag_get_name($tagids) {
    debugging('Method tag_get_name() is deprecated without replacement', DEBUG_DEVELOPER);
    global $DB;

    if (!is_array($tagids)) {
        if ($tag = $DB->get_record('tag', array('id'=>$tagids))) {
            return $tag->name;
        }
        return false;
    }

    $tag_names = array();
    foreach($DB->get_records_list('tag', 'id', $tagids) as $tag) {
        $tag_names[$tag->id] = $tag->name;
    }

    return $tag_names;
}

/**
 * Returns the correlated tags of a tag, retrieved from the tag_correlation table. Make sure cron runs, otherwise the table will be
 * empty and this function won't return anything.
 *
 * Correlated tags are calculated in cron based on existing tag instances.
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   int      $tagid   is a single tag id
 * @param   int      $notused  this argument is no longer used
 * @return  array    an array of tag objects or an empty if no correlated tags are found
 */
function tag_get_correlated($tagid, $notused = null) {
    debugging('Method tag_get_correlated() is deprecated, '
        . 'use core_tag_tag::get_correlated_tags()', DEBUG_DEVELOPER);
    $result = array();
    if ($tag = core_tag_tag::get($tagid)) {
        $tags = $tag->get_correlated_tags(true);
        // Convert to objects for backward-compatibility.
        foreach ($tags as $id => $tag) {
            $result[$id] = $tag->to_object();
        }
    }
    return $result;
}

/**
 * This function is used by print_tag_cloud, to usort() the tags in the cloud. See php.net/usort for the parameters documentation.
 * This was originally in blocks/blog_tags/block_blog_tags.php, named blog_tags_sort().
 *
 * @package core_tag
 * @deprecated since 3.1
 * @param   string $a Tag name to compare against $b
 * @param   string $b Tag name to compare against $a
 * @return  int    The result of the comparison/validation 1, 0 or -1
 */
function tag_cloud_sort($a, $b) {
    debugging('Method tag_cloud_sort() is deprecated, similar method can be found in core_tag_collection::cloud_sort()', DEBUG_DEVELOPER);
    global $CFG;

    if (empty($CFG->tagsort)) {
        $tagsort = 'name'; // by default, sort by name
    } else {
        $tagsort = $CFG->tagsort;
    }

    if (is_numeric($a->$tagsort)) {
        return ($a->$tagsort == $b->$tagsort) ? 0 : ($a->$tagsort > $b->$tagsort) ? 1 : -1;
    } elseif (is_string($a->$tagsort)) {
        return strcmp($a->$tagsort, $b->$tagsort);
    } else {
        return 0;
    }
}

/**
 * Loads the events definitions for the component (from file). If no
 * events are defined for the component, we simply return an empty array.
 *
 * @access protected To be used from eventslib only
 * @deprecated since Moodle 3.1
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return array Array of capabilities or empty array if not exists
 */
function events_load_def($component) {
    global $CFG;
    if ($component === 'unittest') {
        $defpath = $CFG->dirroot.'/lib/tests/fixtures/events.php';
    } else {
        $defpath = core_component::get_component_directory($component).'/db/events.php';
    }

    $handlers = array();

    if (file_exists($defpath)) {
        require($defpath);
    }

    // make sure the definitions are valid and complete; tell devs what is wrong
    foreach ($handlers as $eventname => $handler) {
        if ($eventname === 'reset') {
            debugging("'reset' can not be used as event name.");
            unset($handlers['reset']);
            continue;
        }
        if (!is_array($handler)) {
            debugging("Handler of '$eventname' must be specified as array'");
            unset($handlers[$eventname]);
            continue;
        }
        if (!isset($handler['handlerfile'])) {
            debugging("Handler of '$eventname' must include 'handlerfile' key'");
            unset($handlers[$eventname]);
            continue;
        }
        if (!isset($handler['handlerfunction'])) {
            debugging("Handler of '$eventname' must include 'handlerfunction' key'");
            unset($handlers[$eventname]);
            continue;
        }
        if (!isset($handler['schedule'])) {
            $handler['schedule'] = 'instant';
        }
        if ($handler['schedule'] !== 'instant' and $handler['schedule'] !== 'cron') {
            debugging("Handler of '$eventname' must include valid 'schedule' type (instant or cron)'");
            unset($handlers[$eventname]);
            continue;
        }
        if (!isset($handler['internal'])) {
            $handler['internal'] = 1;
        }
        $handlers[$eventname] = $handler;
    }

    return $handlers;
}

/**
 * Puts a handler on queue
 *
 * @access protected To be used from eventslib only
 * @deprecated since Moodle 3.1
 * @param stdClass $handler event handler object from db
 * @param stdClass $event event data object
 * @param string $errormessage The error message indicating the problem
 * @return int id number of new queue handler
 */
function events_queue_handler($handler, $event, $errormessage) {
    global $DB;

    if ($qhandler = $DB->get_record('events_queue_handlers', array('queuedeventid'=>$event->id, 'handlerid'=>$handler->id))) {
        debugging("Please check code: Event id $event->id is already queued in handler id $qhandler->id");
        return $qhandler->id;
    }

    // make a new queue handler
    $qhandler = new stdClass();
    $qhandler->queuedeventid  = $event->id;
    $qhandler->handlerid      = $handler->id;
    $qhandler->errormessage   = $errormessage;
    $qhandler->timemodified   = time();
    if ($handler->schedule === 'instant' and $handler->status == 1) {
        $qhandler->status     = 1; //already one failed attempt to dispatch this event
    } else {
        $qhandler->status     = 0;
    }

    return $DB->insert_record('events_queue_handlers', $qhandler);
}

/**
 * trigger a single event with a specified handler
 *
 * @access protected To be used from eventslib only
 * @deprecated since Moodle 3.1
 * @param stdClass $handler This shoudl be a row from the events_handlers table.
 * @param stdClass $eventdata An object containing information about the event
 * @param string $errormessage error message indicating problem
 * @return bool|null True means event processed, false means retry event later; may throw exception, NULL means internal error
 */
function events_dispatch($handler, $eventdata, &$errormessage) {
    global $CFG;

    debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.', DEBUG_DEVELOPER);

    $function = unserialize($handler->handlerfunction);

    if (is_callable($function)) {
        // oki, no need for includes

    } else if (file_exists($CFG->dirroot.$handler->handlerfile)) {
        include_once($CFG->dirroot.$handler->handlerfile);

    } else {
        $errormessage = "Handler file of component $handler->component: $handler->handlerfile can not be found!";
        return null;
    }

    // checks for handler validity
    if (is_callable($function)) {
        $result = call_user_func($function, $eventdata);
        if ($result === false) {
            $errormessage = "Handler function of component $handler->component: $handler->handlerfunction requested resending of event!";
            return false;
        }
        return true;

    } else {
        $errormessage = "Handler function of component $handler->component: $handler->handlerfunction not callable function or class method!";
        return null;
    }
}

/**
 * given a queued handler, call the respective event handler to process the event
 *
 * @access protected To be used from eventslib only
 * @deprecated since Moodle 3.1
 * @param stdClass $qhandler events_queued_handler row from db
 * @return boolean true means event processed, false means retry later, NULL means fatal failure
 */
function events_process_queued_handler($qhandler) {
    global $DB;

    // get handler
    if (!$handler = $DB->get_record('events_handlers', array('id'=>$qhandler->handlerid))) {
        debugging("Error processing queue handler $qhandler->id, missing handler id: $qhandler->handlerid");
        //irrecoverable error, remove broken queue handler
        events_dequeue($qhandler);
        return NULL;
    }

    // get event object
    if (!$event = $DB->get_record('events_queue', array('id'=>$qhandler->queuedeventid))) {
        // can't proceed with no event object - might happen when two crons running at the same time
        debugging("Error processing queue handler $qhandler->id, missing event id: $qhandler->queuedeventid");
        //irrecoverable error, remove broken queue handler
        events_dequeue($qhandler);
        return NULL;
    }

    // call the function specified by the handler
    try {
        $errormessage = 'Unknown error';
        if (events_dispatch($handler, unserialize(base64_decode($event->eventdata)), $errormessage)) {
            //everything ok
            events_dequeue($qhandler);
            return true;
        }
    } catch (Exception $e) {
        // the problem here is that we do not want one broken handler to stop all others,
        // cron handlers are very tricky because the needed data might have been deleted before the cron execution
        $errormessage = "Handler function of component $handler->component: $handler->handlerfunction threw exception :" .
                $e->getMessage() . "\n" . format_backtrace($e->getTrace(), true);
        if (!empty($e->debuginfo)) {
            $errormessage .= $e->debuginfo;
        }
    }

    //dispatching failed
    $qh = new stdClass();
    $qh->id           = $qhandler->id;
    $qh->errormessage = $errormessage;
    $qh->timemodified = time();
    $qh->status       = $qhandler->status + 1;
    $DB->update_record('events_queue_handlers', $qh);

    debugging($errormessage);

    return false;
}

/**
 * Updates all of the event definitions within the database.
 *
 * Unfortunately this isn't as simple as removing them all and then readding
 * the updated event definitions. Chances are queued items are referencing the
 * existing definitions.
 *
 * Note that the absence of the db/events.php event definition file
 * will cause any queued events for the component to be removed from
 * the database.
 *
 * @category event
 * @deprecated since Moodle 3.1
 * @param string $component examples: 'moodle', 'mod_forum', 'block_quiz_results'
 * @return boolean always returns true
 */
function events_update_definition($component='moodle') {
    global $DB;

    // load event definition from events.php
    $filehandlers = events_load_def($component);

    if ($filehandlers) {
        debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.', DEBUG_DEVELOPER);
    }

    // load event definitions from db tables
    // if we detect an event being already stored, we discard from this array later
    // the remaining needs to be removed
    $cachedhandlers = events_get_cached($component);

    foreach ($filehandlers as $eventname => $filehandler) {
        if (!empty($cachedhandlers[$eventname])) {
            if ($cachedhandlers[$eventname]['handlerfile'] === $filehandler['handlerfile'] &&
                $cachedhandlers[$eventname]['handlerfunction'] === serialize($filehandler['handlerfunction']) &&
                $cachedhandlers[$eventname]['schedule'] === $filehandler['schedule'] &&
                $cachedhandlers[$eventname]['internal'] == $filehandler['internal']) {
                // exact same event handler already present in db, ignore this entry

                unset($cachedhandlers[$eventname]);
                continue;

            } else {
                // same event name matches, this event has been updated, update the datebase
                $handler = new stdClass();
                $handler->id              = $cachedhandlers[$eventname]['id'];
                $handler->handlerfile     = $filehandler['handlerfile'];
                $handler->handlerfunction = serialize($filehandler['handlerfunction']); // static class methods stored as array
                $handler->schedule        = $filehandler['schedule'];
                $handler->internal        = $filehandler['internal'];

                $DB->update_record('events_handlers', $handler);

                unset($cachedhandlers[$eventname]);
                continue;
            }

        } else {
            // if we are here, this event handler is not present in db (new)
            // add it
            $handler = new stdClass();
            $handler->eventname       = $eventname;
            $handler->component       = $component;
            $handler->handlerfile     = $filehandler['handlerfile'];
            $handler->handlerfunction = serialize($filehandler['handlerfunction']); // static class methods stored as array
            $handler->schedule        = $filehandler['schedule'];
            $handler->status          = 0;
            $handler->internal        = $filehandler['internal'];

            $DB->insert_record('events_handlers', $handler);
        }
    }

    // clean up the left overs, the entries in cached events array at this points are deprecated event handlers
    // and should be removed, delete from db
    events_cleanup($component, $cachedhandlers);

    events_get_handlers('reset');

    return true;
}

/**
 * Events cron will try to empty the events queue by processing all the queued events handlers
 *
 * @access public Part of the public API
 * @deprecated since Moodle 3.1
 * @category event
 * @param string $eventname empty means all
 * @return int number of dispatched events
 */
function events_cron($eventname='') {
    global $DB;

    $failed = array();
    $processed = 0;

    if ($eventname) {
        $sql = "SELECT qh.*
                  FROM {events_queue_handlers} qh, {events_handlers} h
                 WHERE qh.handlerid = h.id AND h.eventname=?
              ORDER BY qh.id";
        $params = array($eventname);
    } else {
        $sql = "SELECT *
                  FROM {events_queue_handlers}
              ORDER BY id";
        $params = array();
    }

    $rs = $DB->get_recordset_sql($sql, $params);
    if ($rs->valid()) {
        debugging('Events API using $handlers array has been deprecated in favour of Events 2 API, please use it instead.', DEBUG_DEVELOPER);
    }

    foreach ($rs as $qhandler) {
        if (isset($failed[$qhandler->handlerid])) {
            // do not try to dispatch any later events when one already asked for retry or ended with exception
            continue;
        }
        $status = events_process_queued_handler($qhandler);
        if ($status === false) {
            // handler is asking for retry, do not send other events to this handler now
            $failed[$qhandler->handlerid] = $qhandler->handlerid;
        } else if ($status === NULL) {
            // means completely broken handler, event data was purged
            $failed[$qhandler->handlerid] = $qhandler->handlerid;
        } else {
            $processed++;
        }
    }
    $rs->close();

    // remove events that do not have any handlers waiting
    $sql = "SELECT eq.id
              FROM {events_queue} eq
              LEFT JOIN {events_queue_handlers} qh ON qh.queuedeventid = eq.id
             WHERE qh.id IS NULL";
    $rs = $DB->get_recordset_sql($sql);
    foreach ($rs as $event) {
        //debugging('Purging stale event '.$event->id);
        $DB->delete_records('events_queue', array('id'=>$event->id));
    }
    $rs->close();

    return $processed;
}

/**
 * Do not call directly, this is intended to be used from new event base only.
 *
 * @private
 * @deprecated since Moodle 3.1
 * @param string $eventname name of the event
 * @param mixed $eventdata event data object
 * @return int number of failed events
 */
function events_trigger_legacy($eventname, $eventdata) {
    global $CFG, $USER, $DB;

    $failedcount = 0; // number of failed events.

    // pull out all registered event handlers
    if ($handlers = events_get_handlers($eventname)) {
        foreach ($handlers as $handler) {
            $errormessage = '';

            if ($handler->schedule === 'instant') {
                if ($handler->status) {
                    //check if previous pending events processed
                    if (!$DB->record_exists('events_queue_handlers', array('handlerid'=>$handler->id))) {
                        // ok, queue is empty, lets reset the status back to 0 == ok
                        $handler->status = 0;
                        $DB->set_field('events_handlers', 'status', 0, array('id'=>$handler->id));
                        // reset static handler cache
                        events_get_handlers('reset');
                    }
                }

                // dispatch the event only if instant schedule and status ok
                if ($handler->status or (!$handler->internal and $DB->is_transaction_started())) {
                    // increment the error status counter
                    $handler->status++;
                    $DB->set_field('events_handlers', 'status', $handler->status, array('id'=>$handler->id));
                    // reset static handler cache
                    events_get_handlers('reset');

                } else {
                    $errormessage = 'Unknown error';
                    $result = events_dispatch($handler, $eventdata, $errormessage);
                    if ($result === true) {
                        // everything is fine - event dispatched
                        continue;
                    } else if ($result === false) {
                        // retry later - set error count to 1 == send next instant into cron queue
                        $DB->set_field('events_handlers', 'status', 1, array('id'=>$handler->id));
                        // reset static handler cache
                        events_get_handlers('reset');
                    } else {
                        // internal problem - ignore the event completely
                        $failedcount ++;
                        continue;
                    }
                }

                // update the failed counter
                $failedcount ++;

            } else if ($handler->schedule === 'cron') {
                //ok - use queueing of events only

            } else {
                // unknown schedule - ignore event completely
                debugging("Unknown handler schedule type: $handler->schedule");
                $failedcount ++;
                continue;
            }

            // if even type is not instant, or dispatch asked for retry, queue it
            $event = new stdClass();
            $event->userid      = $USER->id;
            $event->eventdata   = base64_encode(serialize($eventdata));
            $event->timecreated = time();
            if (debugging()) {
                $dump = '';
                $callers = debug_backtrace();
                foreach ($callers as $caller) {
                    if (!isset($caller['line'])) {
                        $caller['line'] = '?';
                    }
                    if (!isset($caller['file'])) {
                        $caller['file'] = '?';
                    }
                    $dump .= 'line ' . $caller['line'] . ' of ' . substr($caller['file'], strlen($CFG->dirroot) + 1);
                    if (isset($caller['function'])) {
                        $dump .= ': call to ';
                        if (isset($caller['class'])) {
                            $dump .= $caller['class'] . $caller['type'];
                        }
                        $dump .= $caller['function'] . '()';
                    }
                    $dump .= "\n";
                }
                $event->stackdump = $dump;
            } else {
                $event->stackdump = '';
            }
            $event->id = $DB->insert_record('events_queue', $event);
            events_queue_handler($handler, $event, $errormessage);
        }
    } else {
        // No handler found for this event name - this is ok!
    }

    return $failedcount;
}

/**
 * checks if an event is registered for this component
 *
 * @access public Part of the public API
 * @deprecated since Moodle 3.1
 * @param string $eventname name of the event
 * @param string $component component name, can be mod/data or moodle
 * @return bool
 */
function events_is_registered($eventname, $component) {
    global $DB;

    debugging('events_is_registered() has been deprecated along with all Events 1 API in favour of Events 2 API,' .
        ' please use it instead.', DEBUG_DEVELOPER);

    return $DB->record_exists('events_handlers', array('component'=>$component, 'eventname'=>$eventname));
}

/**
 * checks if an event is queued for processing - either cron handlers attached or failed instant handlers
 *
 * @access public Part of the public API
 * @deprecated since Moodle 3.1
 * @param string $eventname name of the event
 * @return int number of queued events
 */
function events_pending_count($eventname) {
    global $DB;

    debugging('events_pending_count() has been deprecated along with all Events 1 API in favour of Events 2 API,' .
        ' please use it instead.', DEBUG_DEVELOPER);

    $sql = "SELECT COUNT('x')
              FROM {events_queue_handlers} qh
              JOIN {events_handlers} h ON h.id = qh.handlerid
             WHERE h.eventname = ?";

    return $DB->count_records_sql($sql, array($eventname));
}

/**
 * Emails admins about a clam outcome
 *
 * @deprecated since Moodle 3.0 - this is a part of clamav plugin now.
 * @param string $notice The body of the email to be sent.
 * @return void
 */
function clam_message_admins($notice) {
    debugging('clam_message_admins() is deprecated, please use message_admins() method of \antivirus_clamav\scanner class.', DEBUG_DEVELOPER);

    $antivirus = \core\antivirus\manager::get_antivirus('clamav');
    $antivirus->message_admins($notice);
}

/**
 * Returns the string equivalent of a numeric clam error code
 *
 * @deprecated since Moodle 3.0 - this is a part of clamav plugin now.
 * @param int $returncode The numeric error code in question.
 * @return string The definition of the error code
 */
function get_clam_error_code($returncode) {
    debugging('get_clam_error_code() is deprecated, please use get_clam_error_code() method of \antivirus_clamav\scanner class.', DEBUG_DEVELOPER);

    $antivirus = \core\antivirus\manager::get_antivirus('clamav');
    return $antivirus->get_clam_error_code($returncode);
}

/**
 * Returns the rename action.
 *
 * @deprecated since 3.1
 * @param cm_info $mod The module to produce editing buttons for
 * @param int $sr The section to link back to (used for creating the links)
 * @return The markup for the rename action, or an empty string if not available.
 */
function course_get_cm_rename_action(cm_info $mod, $sr = null) {
    global $COURSE, $OUTPUT;

    static $str;
    static $baseurl;

    debugging('Function course_get_cm_rename_action() is deprecated. Please use inplace_editable ' .
        'https://docs.moodle.org/dev/Inplace_editable', DEBUG_DEVELOPER);

    $modcontext = context_module::instance($mod->id);
    $hasmanageactivities = has_capability('moodle/course:manageactivities', $modcontext);

    if (!isset($str)) {
        $str = get_strings(array('edittitle'));
    }

    if (!isset($baseurl)) {
        $baseurl = new moodle_url('/course/mod.php', array('sesskey' => sesskey()));
    }

    if ($sr !== null) {
        $baseurl->param('sr', $sr);
    }

    // AJAX edit title.
    if ($mod->has_view() && $hasmanageactivities && course_ajax_enabled($COURSE) &&
        (($mod->course == $COURSE->id) || ($mod->course == SITEID))) {
        // we will not display link if we are on some other-course page (where we should not see this module anyway)
        return html_writer::span(
            html_writer::link(
                new moodle_url($baseurl, array('update' => $mod->id)),
                $OUTPUT->pix_icon('t/editstring', '', 'moodle', array('class' => 'iconsmall visibleifjs', 'title' => '')),
                array(
                    'class' => 'editing_title',
                    'data-action' => 'edittitle',
                    'title' => $str->edittitle,
                )
            )
        );
    }
    return '';
}

/*
 * This function returns the number of activities using the given scale in the given course.
 *
 * @deprecated since Moodle 3.1
 * @param int $courseid The course ID to check.
 * @param int $scaleid The scale ID to check
 * @return int
 */
function course_scale_used($courseid, $scaleid) {
    global $CFG, $DB;

    debugging('course_scale_used() is deprecated and never used, plugins can implement <modname>_scale_used_anywhere, '.
        'all implementations of <modname>_scale_used are now ignored', DEBUG_DEVELOPER);

    $return = 0;

    if (!empty($scaleid)) {
        if ($cms = get_course_mods($courseid)) {
            foreach ($cms as $cm) {
                // Check cm->name/lib.php exists.
                if (file_exists($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php')) {
                    include_once($CFG->dirroot.'/mod/'.$cm->modname.'/lib.php');
                    $functionname = $cm->modname.'_scale_used';
                    if (function_exists($functionname)) {
                        if ($functionname($cm->instance, $scaleid)) {
                            $return++;
                        }
                    }
                }
            }
        }

        // Check if any course grade item makes use of the scale.
        $return += $DB->count_records('grade_items', array('courseid' => $courseid, 'scaleid' => $scaleid));

        // Check if any outcome in the course makes use of the scale.
        $return += $DB->count_records_sql("SELECT COUNT('x')
                                             FROM {grade_outcomes_courses} goc,
                                                  {grade_outcomes} go
                                            WHERE go.id = goc.outcomeid
                                                  AND go.scaleid = ? AND goc.courseid = ?",
            array($scaleid, $courseid));
    }
    return $return;
}

/**
 * This function returns the number of activities using scaleid in the entire site
 *
 * @deprecated since Moodle 3.1
 * @param int $scaleid
 * @param array $courses
 * @return int
 */
function site_scale_used($scaleid, &$courses) {
    $return = 0;

    debugging('site_scale_used() is deprecated and never used, plugins can implement <modname>_scale_used_anywhere, '.
        'all implementations of <modname>_scale_used are now ignored', DEBUG_DEVELOPER);

    if (!is_array($courses) || count($courses) == 0) {
        $courses = get_courses("all", false, "c.id, c.shortname");
    }

    if (!empty($scaleid)) {
        if (is_array($courses) && count($courses) > 0) {
            foreach ($courses as $course) {
                $return += course_scale_used($course->id, $scaleid);
            }
        }
    }
    return $return;
}

/**
 * Returns detailed function information
 *
 * @deprecated since Moodle 3.1
 * @param string|object $function name of external function or record from external_function
 * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
 *                        MUST_EXIST means throw exception if no record or multiple records found
 * @return stdClass description or false if not found or exception thrown
 * @since Moodle 2.0
 */
function external_function_info($function, $strictness=MUST_EXIST) {
    debugging('external_function_info() is deprecated. Please use external_api::external_function_info() instead.',
              DEBUG_DEVELOPER);
    return external_api::external_function_info($function, $strictness);
}

/**
 * Retrieves an array of records from a CSV file and places
 * them into a given table structure
 * This function is deprecated. Please use csv_import_reader() instead.
 *
 * @deprecated since Moodle 3.2 MDL-55126
 * @todo MDL-55195 for final deprecation in Moodle 3.6
 * @see csv_import_reader::load_csv_content()
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @param string $file The path to a CSV file
 * @param string $table The table to retrieve columns from
 * @return bool|array Returns an array of CSV records or false
 */
function get_records_csv($file, $table) {
    global $CFG, $DB;

    debugging('get_records_csv() is deprecated. Please use lib/csvlib.class.php csv_import_reader() instead.');

    if (!$metacolumns = $DB->get_columns($table)) {
        return false;
    }

    if(!($handle = @fopen($file, 'r'))) {
        print_error('get_records_csv failed to open '.$file);
    }

    $fieldnames = fgetcsv($handle, 4096);
    if(empty($fieldnames)) {
        fclose($handle);
        return false;
    }

    $columns = array();

    foreach($metacolumns as $metacolumn) {
        $ord = array_search($metacolumn->name, $fieldnames);
        if(is_int($ord)) {
            $columns[$metacolumn->name] = $ord;
        }
    }

    $rows = array();

    while (($data = fgetcsv($handle, 4096)) !== false) {
        $item = new stdClass;
        foreach($columns as $name => $ord) {
            $item->$name = $data[$ord];
        }
        $rows[] = $item;
    }

    fclose($handle);
    return $rows;
}

/**
 * Create a file with CSV contents
 * This function is deprecated. Please use download_as_dataformat() instead.
 *
 * @deprecated since Moodle 3.2 MDL-55126
 * @todo MDL-55195 for final deprecation in Moodle 3.6
 * @see download_as_dataformat (lib/dataformatlib.php)
 * @global stdClass $CFG
 * @global moodle_database $DB
 * @param string $file The file to put the CSV content into
 * @param array $records An array of records to write to a CSV file
 * @param string $table The table to get columns from
 * @return bool success
 */
function put_records_csv($file, $records, $table = NULL) {
    global $CFG, $DB;

    debugging('put_records_csv() is deprecated. Please use lib/dataformatlib.php download_as_dataformat()');

    if (empty($records)) {
        return true;
    }

    $metacolumns = NULL;
    if ($table !== NULL && !$metacolumns = $DB->get_columns($table)) {
        return false;
    }

    echo "x";

    if(!($fp = @fopen($CFG->tempdir.'/'.$file, 'w'))) {
        print_error('put_records_csv failed to open '.$file);
    }

    $proto = reset($records);
    if(is_object($proto)) {
        $fields_records = array_keys(get_object_vars($proto));
    }
    else if(is_array($proto)) {
        $fields_records = array_keys($proto);
    }
    else {
        return false;
    }
    echo "x";

    if(!empty($metacolumns)) {
        $fields_table = array_map(create_function('$a', 'return $a->name;'), $metacolumns);
        $fields = array_intersect($fields_records, $fields_table);
    }
    else {
        $fields = $fields_records;
    }

    fwrite($fp, implode(',', $fields));
    fwrite($fp, "\r\n");

    foreach($records as $record) {
        $array  = (array)$record;
        $values = array();
        foreach($fields as $field) {
            if(strpos($array[$field], ',')) {
                $values[] = '"'.str_replace('"', '\"', $array[$field]).'"';
            }
            else {
                $values[] = $array[$field];
            }
        }
        fwrite($fp, implode(',', $values)."\r\n");
    }

    fclose($fp);
    @chmod($CFG->tempdir.'/'.$file, $CFG->filepermissions);
    return true;
}

/**
 * Determines if the given value is a valid CSS colour.
 *
 * A CSS colour can be one of the following:
 *    - Hex colour:  #AA66BB
 *    - RGB colour:  rgb(0-255, 0-255, 0-255)
 *    - RGBA colour: rgba(0-255, 0-255, 0-255, 0-1)
 *    - HSL colour:  hsl(0-360, 0-100%, 0-100%)
 *    - HSLA colour: hsla(0-360, 0-100%, 0-100%, 0-1)
 *
 * Or a recognised browser colour mapping {@link css_optimiser::$htmlcolours}
 *
 * @deprecated since Moodle 3.2
 * @todo MDL-56173 for final deprecation in Moodle 3.6
 * @param string $value The colour value to check
 * @return bool
 */
function css_is_colour($value) {
    debugging('css_is_colour() is deprecated without a replacement. Please copy the implementation '.
        'into your plugin if you need this functionality.', DEBUG_DEVELOPER);

    $value = trim($value);

    $hex  = '/^#([a-fA-F0-9]{1,3}|[a-fA-F0-9]{6})$/';
    $rgb  = '#^rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$#i';
    $rgba = '#^rgba\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1}(\.\d+)?)\s*\)$#i';
    $hsl  = '#^hsl\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\%\s*,\s*(\d{1,3})\%\s*\)$#i';
    $hsla = '#^hsla\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\%\s*,\s*(\d{1,3})\%\s*,\s*(\d{1}(\.\d+)?)\s*\)$#i';

    if (in_array(strtolower($value), array('inherit'))) {
        return true;
    } else if (preg_match($hex, $value)) {
        return true;
    } else if (in_array(strtolower($value), array_keys(css_optimiser::$htmlcolours))) {
        return true;
    } else if (preg_match($rgb, $value, $m) && $m[1] < 256 && $m[2] < 256 && $m[3] < 256) {
        // It is an RGB colour.
        return true;
    } else if (preg_match($rgba, $value, $m) && $m[1] < 256 && $m[2] < 256 && $m[3] < 256) {
        // It is an RGBA colour.
        return true;
    } else if (preg_match($hsl, $value, $m) && $m[1] <= 360 && $m[2] <= 100 && $m[3] <= 100) {
        // It is an HSL colour.
        return true;
    } else if (preg_match($hsla, $value, $m) && $m[1] <= 360 && $m[2] <= 100 && $m[3] <= 100) {
        // It is an HSLA colour.
        return true;
    }
    // Doesn't look like a colour.
    return false;
}

/**
 * Returns true is the passed value looks like a CSS width.
 * In order to pass this test the value must be purely numerical or end with a
 * valid CSS unit term.
 *
 * @param string|int $value
 * @return boolean
 * @deprecated since Moodle 3.2
 * @todo MDL-56173 for final deprecation in Moodle 3.6
 */
function css_is_width($value) {
    debugging('css_is_width() is deprecated without a replacement. Please copy the implementation '.
        'into your plugin if you need this functionality.', DEBUG_DEVELOPER);

    $value = trim($value);
    if (in_array(strtolower($value), array('auto', 'inherit'))) {
        return true;
    }
    if ((string)$value === '0' || preg_match('#^(\-\s*)?(\d*\.)?(\d+)\s*(em|px|pt|\%|in|cm|mm|ex|pc)$#i', $value)) {
        return true;
    }
    return false;
}

/**
 * A simple sorting function to sort two array values on the number of items they contain
 *
 * @param array $a
 * @param array $b
 * @return int
 * @deprecated since Moodle 3.2
 * @todo MDL-56173 for final deprecation in Moodle 3.6
 */
function css_sort_by_count(array $a, array $b) {
    debugging('css_sort_by_count() is deprecated without a replacement. Please copy the implementation '.
        'into your plugin if you need this functionality.', DEBUG_DEVELOPER);

    $a = count($a);
    $b = count($b);
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

/**
 * A basic CSS optimiser that strips out unwanted things and then processes CSS organising and cleaning styles.
 * @deprecated since Moodle 3.2
 * @todo MDL-56173 for final deprecation in Moodle 3.6
 */
class css_optimiser {
    /**
     * An array of the common HTML colours that are supported by most browsers.
     *
     * This reference table is used to allow us to unify colours, and will aid
     * us in identifying buggy CSS using unsupported colours.
     *
     * @var string[]
     * @deprecated since Moodle 3.2
     * @todo MDL-56173 for final deprecation in Moodle 3.6
     */
    public static $htmlcolours = array(
        'aliceblue' => '#F0F8FF',
        'antiquewhite' => '#FAEBD7',
        'aqua' => '#00FFFF',
        'aquamarine' => '#7FFFD4',
        'azure' => '#F0FFFF',
        'beige' => '#F5F5DC',
        'bisque' => '#FFE4C4',
        'black' => '#000000',
        'blanchedalmond' => '#FFEBCD',
        'blue' => '#0000FF',
        'blueviolet' => '#8A2BE2',
        'brown' => '#A52A2A',
        'burlywood' => '#DEB887',
        'cadetblue' => '#5F9EA0',
        'chartreuse' => '#7FFF00',
        'chocolate' => '#D2691E',
        'coral' => '#FF7F50',
        'cornflowerblue' => '#6495ED',
        'cornsilk' => '#FFF8DC',
        'crimson' => '#DC143C',
        'cyan' => '#00FFFF',
        'darkblue' => '#00008B',
        'darkcyan' => '#008B8B',
        'darkgoldenrod' => '#B8860B',
        'darkgray' => '#A9A9A9',
        'darkgrey' => '#A9A9A9',
        'darkgreen' => '#006400',
        'darkKhaki' => '#BDB76B',
        'darkmagenta' => '#8B008B',
        'darkolivegreen' => '#556B2F',
        'arkorange' => '#FF8C00',
        'darkorchid' => '#9932CC',
        'darkred' => '#8B0000',
        'darksalmon' => '#E9967A',
        'darkseagreen' => '#8FBC8F',
        'darkslateblue' => '#483D8B',
        'darkslategray' => '#2F4F4F',
        'darkslategrey' => '#2F4F4F',
        'darkturquoise' => '#00CED1',
        'darkviolet' => '#9400D3',
        'deeppink' => '#FF1493',
        'deepskyblue' => '#00BFFF',
        'dimgray' => '#696969',
        'dimgrey' => '#696969',
        'dodgerblue' => '#1E90FF',
        'firebrick' => '#B22222',
        'floralwhite' => '#FFFAF0',
        'forestgreen' => '#228B22',
        'fuchsia' => '#FF00FF',
        'gainsboro' => '#DCDCDC',
        'ghostwhite' => '#F8F8FF',
        'gold' => '#FFD700',
        'goldenrod' => '#DAA520',
        'gray' => '#808080',
        'grey' => '#808080',
        'green' => '#008000',
        'greenyellow' => '#ADFF2F',
        'honeydew' => '#F0FFF0',
        'hotpink' => '#FF69B4',
        'indianred ' => '#CD5C5C',
        'indigo ' => '#4B0082',
        'ivory' => '#FFFFF0',
        'khaki' => '#F0E68C',
        'lavender' => '#E6E6FA',
        'lavenderblush' => '#FFF0F5',
        'lawngreen' => '#7CFC00',
        'lemonchiffon' => '#FFFACD',
        'lightblue' => '#ADD8E6',
        'lightcoral' => '#F08080',
        'lightcyan' => '#E0FFFF',
        'lightgoldenrodyellow' => '#FAFAD2',
        'lightgray' => '#D3D3D3',
        'lightgrey' => '#D3D3D3',
        'lightgreen' => '#90EE90',
        'lightpink' => '#FFB6C1',
        'lightsalmon' => '#FFA07A',
        'lightseagreen' => '#20B2AA',
        'lightskyblue' => '#87CEFA',
        'lightslategray' => '#778899',
        'lightslategrey' => '#778899',
        'lightsteelblue' => '#B0C4DE',
        'lightyellow' => '#FFFFE0',
        'lime' => '#00FF00',
        'limegreen' => '#32CD32',
        'linen' => '#FAF0E6',
        'magenta' => '#FF00FF',
        'maroon' => '#800000',
        'mediumaquamarine' => '#66CDAA',
        'mediumblue' => '#0000CD',
        'mediumorchid' => '#BA55D3',
        'mediumpurple' => '#9370D8',
        'mediumseagreen' => '#3CB371',
        'mediumslateblue' => '#7B68EE',
        'mediumspringgreen' => '#00FA9A',
        'mediumturquoise' => '#48D1CC',
        'mediumvioletred' => '#C71585',
        'midnightblue' => '#191970',
        'mintcream' => '#F5FFFA',
        'mistyrose' => '#FFE4E1',
        'moccasin' => '#FFE4B5',
        'navajowhite' => '#FFDEAD',
        'navy' => '#000080',
        'oldlace' => '#FDF5E6',
        'olive' => '#808000',
        'olivedrab' => '#6B8E23',
        'orange' => '#FFA500',
        'orangered' => '#FF4500',
        'orchid' => '#DA70D6',
        'palegoldenrod' => '#EEE8AA',
        'palegreen' => '#98FB98',
        'paleturquoise' => '#AFEEEE',
        'palevioletred' => '#D87093',
        'papayawhip' => '#FFEFD5',
        'peachpuff' => '#FFDAB9',
        'peru' => '#CD853F',
        'pink' => '#FFC0CB',
        'plum' => '#DDA0DD',
        'powderblue' => '#B0E0E6',
        'purple' => '#800080',
        'red' => '#FF0000',
        'rosybrown' => '#BC8F8F',
        'royalblue' => '#4169E1',
        'saddlebrown' => '#8B4513',
        'salmon' => '#FA8072',
        'sandybrown' => '#F4A460',
        'seagreen' => '#2E8B57',
        'seashell' => '#FFF5EE',
        'sienna' => '#A0522D',
        'silver' => '#C0C0C0',
        'skyblue' => '#87CEEB',
        'slateblue' => '#6A5ACD',
        'slategray' => '#708090',
        'slategrey' => '#708090',
        'snow' => '#FFFAFA',
        'springgreen' => '#00FF7F',
        'steelblue' => '#4682B4',
        'tan' => '#D2B48C',
        'teal' => '#008080',
        'thistle' => '#D8BFD8',
        'tomato' => '#FF6347',
        'transparent' => 'transparent',
        'turquoise' => '#40E0D0',
        'violet' => '#EE82EE',
        'wheat' => '#F5DEB3',
        'white' => '#FFFFFF',
        'whitesmoke' => '#F5F5F5',
        'yellow' => '#FFFF00',
        'yellowgreen' => '#9ACD32'
    );

    /**
     * Used to orocesses incoming CSS optimising it and then returning it. Now just returns
     * what is sent to it. Do not use.
     *
     * @param string $css The raw CSS to optimise
     * @return string The optimised CSS
     * @deprecated since Moodle 3.2
     * @todo MDL-56173 for final deprecation in Moodle 3.6
     */
    public function process($css) {
        debugging('class css_optimiser is deprecated and no longer does anything, '.
            'please consider using stylelint to optimise your css.', DEBUG_DEVELOPER);

        return $css;
    }
}

/**
 * Load the course contexts for all of the users courses
 *
 * @deprecated since Moodle 3.2
 * @param array $courses array of course objects. The courses the user is enrolled in.
 * @return array of course contexts
 */
function message_get_course_contexts($courses) {
    debugging('message_get_course_contexts() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    $coursecontexts = array();

    foreach($courses as $course) {
        $coursecontexts[$course->id] = context_course::instance($course->id);
    }

    return $coursecontexts;
}

/**
 * strip off action parameters like 'removecontact'
 *
 * @deprecated since Moodle 3.2
 * @param moodle_url/string $moodleurl a URL. Typically the current page URL.
 * @return string the URL minus parameters that perform actions (like adding/removing/blocking a contact).
 */
function message_remove_url_params($moodleurl) {
    debugging('message_remove_url_params() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    $newurl = new moodle_url($moodleurl);
    $newurl->remove_params('addcontact','removecontact','blockcontact','unblockcontact');
    return $newurl->out();
}

/**
 * Count the number of messages with a field having a specified value.
 * if $field is empty then return count of the whole array
 * if $field is non-existent then return 0
 *
 * @deprecated since Moodle 3.2
 * @param array $messagearray array of message objects
 * @param string $field the field to inspect on the message objects
 * @param string $value the value to test the field against
 */
function message_count_messages($messagearray, $field='', $value='') {
    debugging('message_count_messages() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    if (!is_array($messagearray)) return 0;
    if ($field == '' or empty($messagearray)) return count($messagearray);

    $count = 0;
    foreach ($messagearray as $message) {
        $count += ($message->$field == $value) ? 1 : 0;
    }
    return $count;
}

/**
 * Count the number of users blocked by $user1
 *
 * @deprecated since Moodle 3.2
 * @param object $user1 user object
 * @return int the number of blocked users
 */
function message_count_blocked_users($user1=null) {
    debugging('message_count_blocked_users() is deprecated, please use \core_message\api::count_blocked_users() instead.',
        DEBUG_DEVELOPER);

    return \core_message\api::count_blocked_users($user1);
}

/**
 * Print a message contact link
 *
 * @deprecated since Moodle 3.2
 * @param int $userid the ID of the user to apply to action to
 * @param string $linktype can be add, remove, block or unblock
 * @param bool $return if true return the link as a string. If false echo the link.
 * @param string $script the URL to send the user to when the link is clicked. If null, the current page.
 * @param bool $text include text next to the icons?
 * @param bool $icon include a graphical icon?
 * @return string  if $return is true otherwise bool
 */
function message_contact_link($userid, $linktype='add', $return=false, $script=null, $text=false, $icon=true) {
    debugging('message_contact_link() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    global $OUTPUT, $PAGE;

    //hold onto the strings as we're probably creating a bunch of links
    static $str;

    if (empty($script)) {
        //strip off previous action params like 'removecontact'
        $script = message_remove_url_params($PAGE->url);
    }

    if (empty($str->blockcontact)) {
        $str = new stdClass();
        $str->blockcontact   =  get_string('blockcontact', 'message');
        $str->unblockcontact =  get_string('unblockcontact', 'message');
        $str->removecontact  =  get_string('removecontact', 'message');
        $str->addcontact     =  get_string('addcontact', 'message');
    }

    $command = $linktype.'contact';
    $string  = $str->{$command};

    $safealttext = s($string);

    $safestring = '';
    if (!empty($text)) {
        $safestring = $safealttext;
    }

    $img = '';
    if ($icon) {
        $iconpath = null;
        switch ($linktype) {
            case 'block':
                $iconpath = 't/block';
                break;
            case 'unblock':
                $iconpath = 't/unblock';
                break;
            case 'remove':
                $iconpath = 't/removecontact';
                break;
            case 'add':
            default:
                $iconpath = 't/addcontact';
        }

        $img = $OUTPUT->pix_icon($iconpath, $safealttext);
    }

    $output = '<span class="'.$linktype.'contact">'.
        '<a href="'.$script.'&amp;'.$command.'='.$userid.
        '&amp;sesskey='.sesskey().'" title="'.$safealttext.'">'.
        $img.
        $safestring.'</a></span>';

    if ($return) {
        return $output;
    } else {
        echo $output;
        return true;
    }
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_recent_notifications($user, $limitfrom=0, $limitto=100) {
    throw new coding_exception('message_get_recent_notifications() can not be used any more.', DEBUG_DEVELOPER);
}

/**
 * echo or return a link to take the user to the full message history between themselves and another user
 *
 * @deprecated since Moodle 3.2
 * @param int $userid1 the ID of the user displayed on the left (usually the current user)
 * @param int $userid2 the ID of the other user
 * @param bool $return true to return the link as a string. False to echo the link.
 * @param string $keywords any keywords to highlight in the message history
 * @param string $position anchor name to jump to within the message history
 * @param string $linktext optionally specify the link text
 * @return string|bool. Returns a string if $return is true. Otherwise returns a boolean.
 */
function message_history_link($userid1, $userid2, $return=false, $keywords='', $position='', $linktext='') {
    debugging('message_history_link() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    global $OUTPUT, $PAGE;
    static $strmessagehistory;

    if (empty($strmessagehistory)) {
        $strmessagehistory = get_string('messagehistory', 'message');
    }

    if ($position) {
        $position = "#$position";
    }
    if ($keywords) {
        $keywords = "&search=".urlencode($keywords);
    }

    if ($linktext == 'icon') {  // Icon only
        $fulllink = $OUTPUT->pix_icon('t/messages', $strmessagehistory);
    } else if ($linktext == 'both') {  // Icon and standard name
        $fulllink = $OUTPUT->pix_icon('t/messages', '');
        $fulllink .= '&nbsp;'.$strmessagehistory;
    } else if ($linktext) {    // Custom name
        $fulllink = $linktext;
    } else {                   // Standard name only
        $fulllink = $strmessagehistory;
    }

    $popupoptions = array(
        'height' => 500,
        'width' => 500,
        'menubar' => false,
        'location' => false,
        'status' => true,
        'scrollbars' => true,
        'resizable' => true);

    $link = new moodle_url('/message/index.php?history='.MESSAGE_HISTORY_ALL."&user1=$userid1&user2=$userid2$keywords$position");
    if ($PAGE->url && $PAGE->url->get_param('viewing')) {
        $link->param('viewing', $PAGE->url->get_param('viewing'));
    }
    $action = null;
    $str = $OUTPUT->action_link($link, $fulllink, $action, array('title' => $strmessagehistory));

    $str = '<span class="history">'.$str.'</span>';

    if ($return) {
        return $str;
    } else {
        echo $str;
        return true;
    }
}

/**
 * @deprecated since Moodle 3.2
 */
function message_search($searchterms, $fromme=true, $tome=true, $courseid='none', $userid=0) {
    throw new coding_exception('message_search() can not be used any more.', DEBUG_DEVELOPER);
}

/**
 * Given a message object that we already know has a long message
 * this function truncates the message nicely to the first
 * sane place between $CFG->forum_longpost and $CFG->forum_shortpost
 *
 * @deprecated since Moodle 3.2
 * @param string $message the message
 * @param int $minlength the minimum length to trim the message to
 * @return string the shortened message
 */
function message_shorten_message($message, $minlength = 0) {
    debugging('message_shorten_message() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    $i = 0;
    $tag = false;
    $length = strlen($message);
    $count = 0;
    $stopzone = false;
    $truncate = 0;
    if ($minlength == 0) $minlength = MESSAGE_SHORTLENGTH;


    for ($i=0; $i<$length; $i++) {
        $char = $message[$i];

        switch ($char) {
            case "<":
                $tag = true;
                break;
            case ">":
                $tag = false;
                break;
            default:
                if (!$tag) {
                    if ($stopzone) {
                        if ($char == '.' or $char == ' ') {
                            $truncate = $i+1;
                            break 2;
                        }
                    }
                    $count++;
                }
                break;
        }
        if (!$stopzone) {
            if ($count > $minlength) {
                $stopzone = true;
            }
        }
    }

    if (!$truncate) {
        $truncate = $i;
    }

    return substr($message, 0, $truncate);
}

/**
 * Given a string and an array of keywords, this function looks
 * for the first keyword in the string, and then chops out a
 * small section from the text that shows that word in context.
 *
 * @deprecated since Moodle 3.2
 * @param string $message the text to search
 * @param array $keywords array of keywords to find
 */
function message_get_fragment($message, $keywords) {
    debugging('message_get_fragment() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    $fullsize = 160;
    $halfsize = (int)($fullsize/2);

    $message = strip_tags($message);

    foreach ($keywords as $keyword) {  // Just get the first one
        if ($keyword !== '') {
            break;
        }
    }
    if (empty($keyword)) {   // None found, so just return start of message
        return message_shorten_message($message, 30);
    }

    $leadin = $leadout = '';

/// Find the start of the fragment
    $start = 0;
    $length = strlen($message);

    $pos = strpos($message, $keyword);
    if ($pos > $halfsize) {
        $start = $pos - $halfsize;
        $leadin = '...';
    }
/// Find the end of the fragment
    $end = $start + $fullsize;
    if ($end > $length) {
        $end = $length;
    } else {
        $leadout = '...';
    }

/// Pull out the fragment and format it

    $fragment = substr($message, $start, $end - $start);
    $fragment = $leadin.highlight(implode(' ',$keywords), $fragment).$leadout;
    return $fragment;
}

/**
 * @deprecated since Moodle 3.2
 */
function message_get_history($user1, $user2, $limitnum=0, $viewingnewmessages=false) {
    throw new coding_exception('message_get_history() can not be used any more.', DEBUG_DEVELOPER);
}

/**
 * Constructs the add/remove contact link to display next to other users
 *
 * @deprecated since Moodle 3.2
 * @param bool $incontactlist is the user a contact
 * @param bool $isblocked is the user blocked
 * @param stdClass $contact contact object
 * @param string $script the URL to send the user to when the link is clicked. If null, the current page.
 * @param bool $text include text next to the icons?
 * @param bool $icon include a graphical icon?
 * @return string
 */
function message_get_contact_add_remove_link($incontactlist, $isblocked, $contact, $script=null, $text=false, $icon=true) {
    debugging('message_get_contact_add_remove_link() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    $strcontact = '';

    if($incontactlist){
        $strcontact = message_contact_link($contact->id, 'remove', true, $script, $text, $icon);
    } else if ($isblocked) {
        $strcontact = message_contact_link($contact->id, 'add', true, $script, $text, $icon);
    } else{
        $strcontact = message_contact_link($contact->id, 'add', true, $script, $text, $icon);
    }

    return $strcontact;
}

/**
 * Constructs the block contact link to display next to other users
 *
 * @deprecated since Moodle 3.2
 * @param bool $incontactlist is the user a contact?
 * @param bool $isblocked is the user blocked?
 * @param stdClass $contact contact object
 * @param string $script the URL to send the user to when the link is clicked. If null, the current page.
 * @param bool $text include text next to the icons?
 * @param bool $icon include a graphical icon?
 * @return string
 */
function message_get_contact_block_link($incontactlist, $isblocked, $contact, $script=null, $text=false, $icon=true) {
    debugging('message_get_contact_block_link() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    $strblock   = '';

    //commented out to allow the user to block a contact without having to remove them first
    /*if ($incontactlist) {
        //$strblock = '';
    } else*/
    if ($isblocked) {
        $strblock   = message_contact_link($contact->id, 'unblock', true, $script, $text, $icon);
    } else{
        $strblock   = message_contact_link($contact->id, 'block', true, $script, $text, $icon);
    }

    return $strblock;
}

/**
 * marks ALL messages being sent from $fromuserid to $touserid as read
 *
 * @deprecated since Moodle 3.2
 * @param int $touserid the id of the message recipient
 * @param int $fromuserid the id of the message sender
 * @return void
 */
function message_mark_messages_read($touserid, $fromuserid) {
    debugging('message_mark_messages_read() is deprecated and is no longer used, please use
        \core_message\api::mark_all_messages_as_read() instead.', DEBUG_DEVELOPER);

    \core_message\api::mark_all_messages_as_read($touserid, $fromuserid);
}

/**
 * Return a list of page types
 *
 * @deprecated since Moodle 3.2
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function message_page_type_list($pagetype, $parentcontext, $currentcontext) {
    debugging('message_page_type_list() is deprecated and is no longer used.', DEBUG_DEVELOPER);

    return array('messages-*'=>get_string('page-message-x', 'message'));
}

/**
 * Determines if a user is permitted to send another user a private message.
 * If no sender is provided then it defaults to the logged in user.
 *
 * @deprecated since Moodle 3.2
 * @param object $recipient User object.
 * @param object $sender User object.
 * @return bool true if user is permitted, false otherwise.
 */
function message_can_post_message($recipient, $sender = null) {
    debugging('message_can_post_message() is deprecated and is no longer used, please use
        \core_message\api::can_post_message() instead.', DEBUG_DEVELOPER);

    return \core_message\api::can_post_message($recipient, $sender);
}

/**
 * Checks if the recipient is allowing messages from users that aren't a
 * contact. If not then it checks to make sure the sender is in the
 * recipient's contacts.
 *
 * @deprecated since Moodle 3.2
 * @param object $recipient User object.
 * @param object $sender User object.
 * @return bool true if $sender is blocked, false otherwise.
 */
function message_is_user_non_contact_blocked($recipient, $sender = null) {
    debugging('message_is_user_non_contact_blocked() is deprecated and is no longer used, please use
        \core_message\api::is_user_non_contact_blocked() instead.', DEBUG_DEVELOPER);

    return \core_message\api::is_user_non_contact_blocked($recipient, $sender);
}

/**
 * Checks if the recipient has specifically blocked the sending user.
 *
 * Note: This function will always return false if the sender has the
 * readallmessages capability at the system context level.
 *
 * @deprecated since Moodle 3.2
 * @param object $recipient User object.
 * @param object $sender User object.
 * @return bool true if $sender is blocked, false otherwise.
 */
function message_is_user_blocked($recipient, $sender = null) {
    debugging('message_is_user_blocked() is deprecated and is no longer used, please use
        \core_message\api::is_user_blocked() instead.', DEBUG_DEVELOPER);

    $senderid = null;
    if ($sender !== null && isset($sender->id)) {
        $senderid = $sender->id;
    }
    return \core_message\api::is_user_blocked($recipient->id, $senderid);
}

/**
 * Display logs.
 *
 * @deprecated since 3.2
 */
function print_log($course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {
    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    global $CFG, $DB, $OUTPUT;

    if (!$logs = build_logs_array($course, $user, $date, $order, $page*$perpage, $perpage,
                       $modname, $modid, $modaction, $groupid)) {
        echo $OUTPUT->notification("No logs found!");
        echo $OUTPUT->footer();
        exit;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $totalcount = $logs['totalcount'];
    $ldcache = array();

    $strftimedatetime = get_string("strftimedatetime");

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");

    $table = new html_table();
    $table->classes = array('logtable','generaltable');
    $table->align = array('right', 'left', 'left');
    $table->head = array(
        get_string('time'),
        get_string('ip_address'),
        get_string('fullnameuser'),
        get_string('action'),
        get_string('info')
    );
    $table->data = array();

    if ($course->id == SITEID) {
        array_unshift($table->align, 'left');
        array_unshift($table->head, get_string('course'));
    }

    // Make sure that the logs array is an array, even it is empty, to avoid warnings from the foreach.
    if (empty($logs['logs'])) {
        $logs['logs'] = array();
    }

    foreach ($logs['logs'] as $log) {

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if ($ld->mtable == 'user' && $ld->field == $DB->sql_concat('firstname', "' '" , 'lastname')) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);

        // If $log->url has been trimmed short by the db size restriction
        // code in add_to_log, keep a note so we don't add a link to a broken url
        $brokenurl=(core_text::strlen($log->url)==100 && core_text::substr($log->url,97)=='...');

        $row = array();
        if ($course->id == SITEID) {
            if (empty($log->course)) {
                $row[] = get_string('site');
            } else {
                $row[] = "<a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">". format_string($courses[$log->course])."</a>";
            }
        }

        $row[] = userdate($log->time, '%a').' '.userdate($log->time, $strftimedatetime);

        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        $row[] = $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 440, 'width' => 700)));

        $row[] = html_writer::link(new moodle_url("/user/view.php?id={$log->userid}&course={$log->course}"), fullname($log, has_capability('moodle/site:viewfullnames', context_course::instance($course->id))));

        $displayaction="$log->module $log->action";
        if ($brokenurl) {
            $row[] = $displayaction;
        } else {
            $link = make_log_url($log->module,$log->url);
            $row[] = $OUTPUT->action_link($link, $displayaction, new popup_action('click', $link, 'fromloglive'), array('height' => 440, 'width' => 700));
        }
        $row[] = $log->info;
        $table->data[] = $row;
    }

    echo html_writer::table($table);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}

/**
 * Display MNET logs.
 *
 * @deprecated since 3.2
 */
function print_mnet_log($hostid, $course, $user=0, $date=0, $order="l.time ASC", $page=0, $perpage=100,
                   $url="", $modname="", $modid=0, $modaction="", $groupid=0) {
    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    global $CFG, $DB, $OUTPUT;

    if (!$logs = build_mnet_logs_array($hostid, $course, $user, $date, $order, $page*$perpage, $perpage,
                       $modname, $modid, $modaction, $groupid)) {
        echo $OUTPUT->notification("No logs found!");
        echo $OUTPUT->footer();
        exit;
    }

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname,c.visible')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    }

    $totalcount = $logs['totalcount'];
    $ldcache = array();

    $strftimedatetime = get_string("strftimedatetime");

    echo "<div class=\"info\">\n";
    print_string("displayingrecords", "", $totalcount);
    echo "</div>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");

    echo "<table class=\"logtable\" cellpadding=\"3\" cellspacing=\"0\">\n";
    echo "<tr>";
    if ($course->id == SITEID) {
        echo "<th class=\"c0 header\">".get_string('course')."</th>\n";
    }
    echo "<th class=\"c1 header\">".get_string('time')."</th>\n";
    echo "<th class=\"c2 header\">".get_string('ip_address')."</th>\n";
    echo "<th class=\"c3 header\">".get_string('fullnameuser')."</th>\n";
    echo "<th class=\"c4 header\">".get_string('action')."</th>\n";
    echo "<th class=\"c5 header\">".get_string('info')."</th>\n";
    echo "</tr>\n";

    if (empty($logs['logs'])) {
        echo "</table>\n";
        return;
    }

    $row = 1;
    foreach ($logs['logs'] as $log) {

        $log->info = $log->coursename;
        $row = ($row + 1) % 2;

        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if (0 && $ld && !empty($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);

        echo '<tr class="r'.$row.'">';
        if ($course->id == SITEID) {
            $courseshortname = format_string($courses[$log->course], true, array('context' => context_course::instance(SITEID)));
            echo "<td class=\"r$row c0\" >\n";
            echo "    <a href=\"{$CFG->wwwroot}/course/view.php?id={$log->course}\">".$courseshortname."</a>\n";
            echo "</td>\n";
        }
        echo "<td class=\"r$row c1\" align=\"right\">".userdate($log->time, '%a').
             ' '.userdate($log->time, $strftimedatetime)."</td>\n";
        echo "<td class=\"r$row c2\" >\n";
        $link = new moodle_url("/iplookup/index.php?ip=$log->ip&user=$log->userid");
        echo $OUTPUT->action_link($link, $log->ip, new popup_action('click', $link, 'iplookup', array('height' => 400, 'width' => 700)));
        echo "</td>\n";
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', context_course::instance($course->id)));
        echo "<td class=\"r$row c3\" >\n";
        echo "    <a href=\"$CFG->wwwroot/user/view.php?id={$log->userid}\">$fullname</a>\n";
        echo "</td>\n";
        echo "<td class=\"r$row c4\">\n";
        echo $log->action .': '.$log->module;
        echo "</td>\n";
        echo "<td class=\"r$row c5\">{$log->info}</td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";

    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, "$url&perpage=$perpage");
}

/**
 * Display logs in CSV format.
 *
 * @deprecated since 3.2
 */
function print_log_csv($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {
    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    global $DB, $CFG;

    require_once($CFG->libdir . '/csvlib.class.php');

    $csvexporter = new csv_export_writer('tab');

    $header = array();
    $header[] = get_string('course');
    $header[] = get_string('time');
    $header[] = get_string('ip_address');
    $header[] = get_string('fullnameuser');
    $header[] = get_string('action');
    $header[] = get_string('info');

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $csvexporter->set_filename('logs', '.txt');
    $title = array(get_string('savedat').userdate(time(), $strftimedatetime));
    $csvexporter->add_data($title);
    $csvexporter->add_data($header);

    if (empty($logs['logs'])) {
        return true;
    }

    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field ==  $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        //Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));    // Some XSS protection

        $coursecontext = context_course::instance($course->id);
        $firstField = format_string($courses[$log->course], true, array('context' => $coursecontext));
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
        $actionurl = $CFG->wwwroot. make_log_url($log->module,$log->url);
        $row = array($firstField, userdate($log->time, $strftimedatetime), $log->ip, $fullname, $log->module.' '.$log->action.' ('.$actionurl.')', $log->info);
        $csvexporter->add_data($row);
    }
    $csvexporter->download_file();
    return true;
}

/**
 * Display logs in XLS format.
 *
 * @deprecated since 3.2
 */
function print_log_xls($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {
    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    require_once("$CFG->libdir/excellib.class.php");

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $count=0;
    $ldcache = array();
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);

    $strftimedatetime = get_string("strftimedatetime");

    $nroPages = ceil(count($logs)/(EXCELROWS-FIRSTUSEDEXCELROW+1));
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat', 'langconfig'),99,false);
    $filename .= '.xls';

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullnameuser'),    get_string('action'), get_string('info'));

    // Creating worksheets
    for ($wsnumber = 1; $wsnumber <= $nroPages; $wsnumber++) {
        $sheettitle = get_string('logs').' '.$wsnumber.'-'.$nroPages;
        $worksheet[$wsnumber] = $workbook->add_worksheet($sheettitle);
        $worksheet[$wsnumber]->set_column(1, 1, 30);
        $worksheet[$wsnumber]->write_string(0, 0, get_string('savedat').
                                    userdate(time(), $strftimedatetime));
        $col = 0;
        foreach ($headers as $item) {
            $worksheet[$wsnumber]->write(FIRSTUSEDEXCELROW-1,$col,$item,'');
            $col++;
        }
    }

    if (empty($logs['logs'])) {
        $workbook->close();
        return true;
    }

    $formatDate =& $workbook->add_format();
    $formatDate->set_num_format(get_string('log_excel_date_format'));

    $row = FIRSTUSEDEXCELROW;
    $wsnumber = 1;
    $myxls =& $worksheet[$wsnumber];
    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        // Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection

        if ($nroPages>1) {
            if ($row > EXCELROWS) {
                $wsnumber++;
                $myxls =& $worksheet[$wsnumber];
                $row = FIRSTUSEDEXCELROW;
            }
        }

        $coursecontext = context_course::instance($course->id);

        $myxls->write($row, 0, format_string($courses[$log->course], true, array('context' => $coursecontext)), '');
        $myxls->write_date($row, 1, $log->time, $formatDate); // write_date() does conversion/timezone support. MDL-14934
        $myxls->write($row, 2, $log->ip, '');
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
        $myxls->write($row, 3, $fullname, '');
        $actionurl = $CFG->wwwroot. make_log_url($log->module,$log->url);
        $myxls->write($row, 4, $log->module.' '.$log->action.' ('.$actionurl.')', '');
        $myxls->write($row, 5, $log->info, '');

        $row++;
    }

    $workbook->close();
    return true;
}

/**
 * Display logs in ODS format.
 *
 * @deprecated since 3.2
 */
function print_log_ods($course, $user, $date, $order='l.time DESC', $modname,
                        $modid, $modaction, $groupid) {
    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    require_once("$CFG->libdir/odslib.class.php");

    if (!$logs = build_logs_array($course, $user, $date, $order, '', '',
                       $modname, $modid, $modaction, $groupid)) {
        return false;
    }

    $courses = array();

    if ($course->id == SITEID) {
        $courses[0] = '';
        if ($ccc = get_courses('all', 'c.id ASC', 'c.id,c.shortname')) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = $cc->shortname;
            }
        }
    } else {
        $courses[$course->id] = $course->shortname;
    }

    $ldcache = array();

    $strftimedatetime = get_string("strftimedatetime");

    $nroPages = ceil(count($logs)/(EXCELROWS-FIRSTUSEDEXCELROW+1));
    $filename = 'logs_'.userdate(time(),get_string('backupnameformat', 'langconfig'),99,false);
    $filename .= '.ods';

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();
    $headers = array(get_string('course'), get_string('time'), get_string('ip_address'),
                        get_string('fullnameuser'),    get_string('action'), get_string('info'));

    // Creating worksheets
    for ($wsnumber = 1; $wsnumber <= $nroPages; $wsnumber++) {
        $sheettitle = get_string('logs').' '.$wsnumber.'-'.$nroPages;
        $worksheet[$wsnumber] = $workbook->add_worksheet($sheettitle);
        $worksheet[$wsnumber]->set_column(1, 1, 30);
        $worksheet[$wsnumber]->write_string(0, 0, get_string('savedat').
                                    userdate(time(), $strftimedatetime));
        $col = 0;
        foreach ($headers as $item) {
            $worksheet[$wsnumber]->write(FIRSTUSEDEXCELROW-1,$col,$item,'');
            $col++;
        }
    }

    if (empty($logs['logs'])) {
        $workbook->close();
        return true;
    }

    $formatDate =& $workbook->add_format();
    $formatDate->set_num_format(get_string('log_excel_date_format'));

    $row = FIRSTUSEDEXCELROW;
    $wsnumber = 1;
    $myxls =& $worksheet[$wsnumber];
    foreach ($logs['logs'] as $log) {
        if (isset($ldcache[$log->module][$log->action])) {
            $ld = $ldcache[$log->module][$log->action];
        } else {
            $ld = $DB->get_record('log_display', array('module'=>$log->module, 'action'=>$log->action));
            $ldcache[$log->module][$log->action] = $ld;
        }
        if ($ld && is_numeric($log->info)) {
            // ugly hack to make sure fullname is shown correctly
            if (($ld->mtable == 'user') and ($ld->field == $DB->sql_concat('firstname', "' '" , 'lastname'))) {
                $log->info = fullname($DB->get_record($ld->mtable, array('id'=>$log->info)), true);
            } else {
                $log->info = $DB->get_field($ld->mtable, $ld->field, array('id'=>$log->info));
            }
        }

        // Filter log->info
        $log->info = format_string($log->info);
        $log->info = strip_tags(urldecode($log->info));  // Some XSS protection

        if ($nroPages>1) {
            if ($row > EXCELROWS) {
                $wsnumber++;
                $myxls =& $worksheet[$wsnumber];
                $row = FIRSTUSEDEXCELROW;
            }
        }

        $coursecontext = context_course::instance($course->id);

        $myxls->write_string($row, 0, format_string($courses[$log->course], true, array('context' => $coursecontext)));
        $myxls->write_date($row, 1, $log->time);
        $myxls->write_string($row, 2, $log->ip);
        $fullname = fullname($log, has_capability('moodle/site:viewfullnames', $coursecontext));
        $myxls->write_string($row, 3, $fullname);
        $actionurl = $CFG->wwwroot. make_log_url($log->module,$log->url);
        $myxls->write_string($row, 4, $log->module.' '.$log->action.' ('.$actionurl.')');
        $myxls->write_string($row, 5, $log->info);

        $row++;
    }

    $workbook->close();
    return true;
}

/**
 * Build an array of logs.
 *
 * @deprecated since 3.2
 */
function build_logs_array($course, $user=0, $date=0, $order="l.time ASC", $limitfrom='', $limitnum='',
                   $modname="", $modid=0, $modaction="", $groupid=0) {
    global $DB, $SESSION, $USER;

    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);
    // It is assumed that $date is the GMT time of midnight for that day,
    // and so the next 86400 seconds worth of logs are printed.

    // Setup for group handling.

    // If the group mode is separate, and this user does not have editing privileges,
    // then only the user's group can be viewed.
    if ($course->groupmode == SEPARATEGROUPS and !has_capability('moodle/course:managegroups', context_course::instance($course->id))) {
        if (isset($SESSION->currentgroup[$course->id])) {
            $groupid =  $SESSION->currentgroup[$course->id];
        } else {
            $groupid = groups_get_all_groups($course->id, $USER->id);
            if (is_array($groupid)) {
                $groupid = array_shift(array_keys($groupid));
                $SESSION->currentgroup[$course->id] = $groupid;
            } else {
                $groupid = 0;
            }
        }
    }
    // If this course doesn't have groups, no groupid can be specified.
    else if (!$course->groupmode) {
        $groupid = 0;
    }

    $joins = array();
    $params = array();

    if ($course->id != SITEID || $modid != 0) {
        $joins[] = "l.course = :courseid";
        $params['courseid'] = $course->id;
    }

    if ($modname) {
        $joins[] = "l.module = :modname";
        $params['modname'] = $modname;
    }

    if ('site_errors' === $modid) {
        $joins[] = "( l.action='error' OR l.action='infected' )";
    } else if ($modid) {
        $joins[] = "l.cmid = :modid";
        $params['modid'] = $modid;
    }

    if ($modaction) {
        $firstletter = substr($modaction, 0, 1);
        if ($firstletter == '-') {
            $joins[] = $DB->sql_like('l.action', ':modaction', false, true, true);
            $params['modaction'] = '%'.substr($modaction, 1).'%';
        } else {
            $joins[] = $DB->sql_like('l.action', ':modaction', false);
            $params['modaction'] = '%'.$modaction.'%';
        }
    }


    /// Getting all members of a group.
    if ($groupid and !$user) {
        if ($gusers = groups_get_members($groupid)) {
            $gusers = array_keys($gusers);
            $joins[] = 'l.userid IN (' . implode(',', $gusers) . ')';
        } else {
            $joins[] = 'l.userid = 0'; // No users in groups, so we want something that will always be false.
        }
    }
    else if ($user) {
        $joins[] = "l.userid = :userid";
        $params['userid'] = $user;
    }

    if ($date) {
        $enddate = $date + 86400;
        $joins[] = "l.time > :date AND l.time < :enddate";
        $params['date'] = $date;
        $params['enddate'] = $enddate;
    }

    $selector = implode(' AND ', $joins);

    $totalcount = 0;  // Initialise
    $result = array();
    $result['logs'] = get_logs($selector, $params, $order, $limitfrom, $limitnum, $totalcount);
    $result['totalcount'] = $totalcount;
    return $result;
}

/**
 * Select all log records for a given course and user.
 *
 * @deprecated since 3.2
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $courseid The id of the course as found in the 'course' table.
 * @param string $coursestart unix timestamp representing course start date and time.
 * @return array
 */
function get_logs_usercourse($userid, $courseid, $coursestart) {
    global $DB;

    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    $params = array();

    $courseselect = '';
    if ($courseid) {
        $courseselect = "AND course = :courseid";
        $params['courseid'] = $courseid;
    }
    $params['userid'] = $userid;
    // We have to sanitize this param ourselves here instead of relying on DB.
    // Postgres complains if you use name parameter or column alias in GROUP BY.
    // See MDL-27696 and 51c3e85 for details.
    $coursestart = (int)$coursestart;

    return $DB->get_records_sql("SELECT FLOOR((time - $coursestart)/". DAYSECS .") AS day, COUNT(*) AS num
                                   FROM {log}
                                  WHERE userid = :userid
                                        AND time > $coursestart $courseselect
                               GROUP BY FLOOR((time - $coursestart)/". DAYSECS .")", $params);
}

/**
 * Select all log records for a given course, user, and day.
 *
 * @deprecated since 3.2
 * @param int $userid The id of the user as found in the 'user' table.
 * @param int $courseid The id of the course as found in the 'course' table.
 * @param string $daystart unix timestamp of the start of the day for which the logs needs to be retrived
 * @return array
 */
function get_logs_userday($userid, $courseid, $daystart) {
    global $DB;

    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    $params = array('userid'=>$userid);

    $courseselect = '';
    if ($courseid) {
        $courseselect = "AND course = :courseid";
        $params['courseid'] = $courseid;
    }
    // Note: unfortunately pg complains if you use name parameter or column alias in GROUP BY.
    $daystart = (int) $daystart;

    return $DB->get_records_sql("SELECT FLOOR((time - $daystart)/". HOURSECS .") AS hour, COUNT(*) AS num
                                   FROM {log}
                                  WHERE userid = :userid
                                        AND time > $daystart $courseselect
                               GROUP BY FLOOR((time - $daystart)/". HOURSECS .") ", $params);
}

/**
 * Select all log records based on SQL criteria.
 *
 * @deprecated since 3.2
 * @param string $select SQL select criteria
 * @param array $params named sql type params
 * @param string $order SQL order by clause to sort the records returned
 * @param string $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set)
 * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set)
 * @param int $totalcount Passed in by reference.
 * @return array
 */
function get_logs($select, array $params=null, $order='l.time DESC', $limitfrom='', $limitnum='', &$totalcount) {
    global $DB;

    debugging(__FUNCTION__ . '() is deprecated. Please use the report_log framework instead.', DEBUG_DEVELOPER);

    if ($order) {
        $order = "ORDER BY $order";
    }

    if ($select) {
        $select = "WHERE $select";
    }

    $sql = "SELECT COUNT(*)
              FROM {log} l
           $select";

    $totalcount = $DB->count_records_sql($sql, $params);
    $allnames = get_all_user_name_fields(true, 'u');
    $sql = "SELECT l.*, $allnames, u.picture
              FROM {log} l
              LEFT JOIN {user} u ON l.userid = u.id
           $select
            $order";

    return $DB->get_records_sql($sql, $params, $limitfrom, $limitnum);
}

/**
 * Renders a hidden password field so that browsers won't incorrectly autofill password fields with the user's password.
 *
 * @deprecated since Moodle 3.2 MDL-53048
 */
function prevent_form_autofill_password() {
    debugging('prevent_form_autofill_password has been deprecated and is no longer in use.', DEBUG_DEVELOPER);
    return '';
}

/**
 * @deprecated since Moodle 3.3 MDL-57370
 */
function message_get_recent_conversations($userorid, $limitfrom = 0, $limitto = 100) {
    throw new coding_exception('message_get_recent_conversations() can not be used any more. ' .
        'Please use \core_message\api::get_conversations() instead.', DEBUG_DEVELOPER);
}

/**
 * Display calendar preference button.
 *
 * @param stdClass $course course object
 * @deprecated since Moodle 3.2
 * @return string return preference button in html
 */
function calendar_preferences_button(stdClass $course) {
    debugging('This should no longer be used, the calendar preferences are now linked to the user preferences page.');

    global $OUTPUT;

    // Guests have no preferences.
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    return $OUTPUT->single_button(new moodle_url('/user/calendar.php'), get_string("preferences", "calendar"));
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
 * Previous internal API, it was not supposed to be used anywhere.
 *
 * @access private
 * @deprecated since Moodle 3.4 and removed immediately. MDL-49398.
 * @param int $userid the id of the user
 * @param context_course $coursecontext course context
 * @param array $accessdata accessdata array (modified)
 * @return void modifies $accessdata parameter
 */
function load_course_context($userid, context_course $coursecontext, &$accessdata) {
    throw new coding_exception('load_course_context() is removed. Do not use private functions or data structures.');
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 *
 * @access private
 * @deprecated since Moodle 3.4 and removed immediately. MDL-49398.
 * @param int $roleid the id of the user
 * @param context $context needs path!
 * @param array $accessdata accessdata array (is modified)
 * @return array
 */
function load_role_access_by_context($roleid, context $context, &$accessdata) {
    throw new coding_exception('load_role_access_by_context() is removed. Do not use private functions or data structures.');
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 *
 * @access private
 * @deprecated since Moodle 3.4 and removed immediately. MDL-49398.
 * @return void
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
