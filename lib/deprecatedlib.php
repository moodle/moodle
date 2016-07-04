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
 * @return array as (string)$type => (string)$plugin
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
 * @deprecated use $PAGE->https_required() instead
 */
function httpsrequired() {
    throw new coding_exception('httpsrequired() can not be used any more use $PAGE->https_required() instead.');
}

/**
 * Given a physical path to a file, returns the URL through which it can be reached in Moodle.
 *
 * @deprecated use moodle_url factory methods instead
 *
 * @param string $path Physical path to a file
 * @param array $options associative array of GET variables to append to the URL
 * @param string $type (questionfile|rssfile|httpscoursefile|coursefile)
 * @return string URL to file
 */
function get_file_url($path, $options=null, $type='coursefile') {
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
        case 'httpscoursefile':
            $url = $CFG->httpswwwroot."/file.php";
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
 */
function unzip_file($zipfile, $destination = '', $showstatus_ignored = true) {
    global $CFG;

    //Extract everything from zipfile
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
 */
function zip_files ($originalfiles, $destination) {
    global $CFG;

    //Extract everything from destination
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
 * Returns the current group mode for a given course or activity module
 *
 * Could be false, SEPARATEGROUPS or VISIBLEGROUPS    (<-- Martin)
 *
 * @deprecated since Moodle 2.0 MDL-14617 - please do not use this function any more.
 * @todo MDL-50273 This will be deleted in Moodle 3.2.
 *
 * @param object $course Course Object
 * @param object $cm Course Manager Object
 * @return mixed $course->groupmode
 */
function groupmode($course, $cm=null) {

    debugging('groupmode() is deprecated, please use groups_get_* instead', DEBUG_DEVELOPER);
    if (isset($cm->groupmode) && empty($course->groupmodeforce)) {
        return $cm->groupmode;
    }
    return $course->groupmode;
}

/**
 * Sets the current group in the session variable
 * When $SESSION->currentgroup[$courseid] is set to 0 it means, show all groups.
 * Sets currentgroup[$courseid] in the session variable appropriately.
 * Does not do any permission checking.
 *
 * @deprecated Since year 2006 - please do not use this function any more.
 * @todo MDL-50273 This will be deleted in Moodle 3.2.
 *
 * @global object
 * @global object
 * @param int $courseid The course being examined - relates to id field in
 * 'course' table.
 * @param int $groupid The group being examined.
 * @return int Current group id which was set by this function
 */
function set_current_group($courseid, $groupid) {
    global $SESSION;

    debugging('set_current_group() is deprecated, please use $SESSION->currentgroup[$courseid] instead', DEBUG_DEVELOPER);
    return $SESSION->currentgroup[$courseid] = $groupid;
}

/**
 * Gets the current group - either from the session variable or from the database.
 *
 * @deprecated Since year 2006 - please do not use this function any more.
 * @todo MDL-50273 This will be deleted in Moodle 3.2.
 *
 * @global object
 * @param int $courseid The course being examined - relates to id field in
 * 'course' table.
 * @param bool $full If true, the return value is a full record object.
 * If false, just the id of the record.
 * @return int|bool
 */
function get_current_group($courseid, $full = false) {
    global $SESSION;

    debugging('get_current_group() is deprecated, please use groups_get_* instead', DEBUG_DEVELOPER);
    if (isset($SESSION->currentgroup[$courseid])) {
        if ($full) {
            return groups_get_group($SESSION->currentgroup[$courseid]);
        } else {
            return $SESSION->currentgroup[$courseid];
        }
    }

    $mygroupid = mygroupid($courseid);
    if (is_array($mygroupid)) {
        $mygroupid = array_shift($mygroupid);
        set_current_group($courseid, $mygroupid);
        if ($full) {
            return groups_get_group($mygroupid);
        } else {
            return $mygroupid;
        }
    }

    if ($full) {
        return false;
    } else {
        return 0;
    }
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
 * Print a bold message in an optional color.
 *
 * @deprecated use $OUTPUT->notification instead.
 * @param string $message The message to print out
 * @param string $style Optional style to display message text in
 * @param string $align Alignment option
 * @param bool $return whether to return an output string or echo now
 * @return string|bool Depending on $result
 */
function notify($message, $classes = 'notifyproblem', $align = 'center', $return = false) {
    global $OUTPUT;

    if ($classes == 'green') {
        debugging('Use of deprecated class name "green" in notify. Please change to "notifysuccess".', DEBUG_DEVELOPER);
        $classes = 'notifysuccess'; // Backward compatible with old color system
    }

    $output = $OUTPUT->notification($message, $classes);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
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

    $return = ' <img src="'.$OUTPUT->pix_url('t/' . $direction) . '" alt="'.$strsort.'" /> ';

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
 * @deprecated since Moodle 2.0
 *
 * @param string $cmid the course_module id.
 * @param string $ignored not used any more. (Used to be courseid.)
 * @param string $string the module name - get_string('modulename', 'xxx')
 * @return string the HTML for the button, if this user has permission to edit it, else an empty string.
 */
function update_module_button($cmid, $ignored, $string) {
    global $CFG, $OUTPUT;

    // debugging('update_module_button() has been deprecated. Please change your code to use $OUTPUT->update_module_button().');

    //NOTE: DO NOT call new output method because it needs the module name we do not have here!

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
 * @todo MDL-49290 This will be deleted in Moodle 3.1.
 * @param stdClass the first user
 * @param stdClass the second user or null
 * @return bool True if the current user is one of either $user1 or $user2
 */
function message_current_user_is_involved($user1, $user2) {
    global $USER;

    debugging('message_current_user_is_involved() is deprecated, please do not use this function.', DEBUG_DEVELOPER);

    if (empty($user1->id) || (!empty($user2) && empty($user2->id))) {
        throw new coding_exception('Invalid user object detected. Missing id.');
    }

    if ($user1->id != $USER->id && (empty($user2) || $user2->id != $USER->id)) {
        return false;
    }
    return true;
}

/**
 * Print badges on user profile page.
 *
 * @deprecated since Moodle 2.9 MDL-45898 - please do not use this function any more.
 * @param int $userid User ID.
 * @param int $courseid Course if we need to filter badges (optional).
 */
function profile_display_badges($userid, $courseid = 0) {
    global $CFG, $PAGE, $USER, $SITE;
    require_once($CFG->dirroot . '/badges/renderer.php');

    debugging('profile_display_badges() is deprecated.', DEBUG_DEVELOPER);
    $context = context_user::instance($userid);

    if ($USER->id == $userid || has_capability('moodle/badges:viewotherbadges', $context)) {
        $records = badges_get_user_badges($userid, $courseid, null, null, null, true);
        $renderer = new core_badges_renderer($PAGE, '');

        // Print local badges.
        if ($records) {
            $left = get_string('localbadgesp', 'badges', format_string($SITE->fullname));
            $right = $renderer->print_badges_list($records, $userid, true);
            echo html_writer::tag('dt', $left);
            echo html_writer::tag('dd', $right);
        }

        // Print external badges.
        if ($courseid == 0 && !empty($CFG->badges_allowexternalbackpack)) {
            $backpack = get_backpack_settings($userid);
            if (isset($backpack->totalbadges) && $backpack->totalbadges !== 0) {
                $left = get_string('externalbadgesp', 'badges');
                $right = $renderer->print_badges_list($backpack->badges, $userid, true, true);
                echo html_writer::tag('dt', $left);
                echo html_writer::tag('dd', $right);
            }
        }
    }
}

/**
 * Adds user preferences elements to user edit form.
 *
 * @deprecated since Moodle 2.9 MDL-45774 - Please do not use this function any more.
 * @todo MDL-49784 Remove this function in Moodle 3.1
 * @param stdClass $user
 * @param moodleform $mform
 * @param array|null $editoroptions
 * @param array|null $filemanageroptions
 */
function useredit_shared_definition_preferences($user, &$mform, $editoroptions = null, $filemanageroptions = null) {
    global $CFG;

    debugging('useredit_shared_definition_preferences() is deprecated.', DEBUG_DEVELOPER, backtrace);

    $choices = array();
    $choices['0'] = get_string('emaildisplayno');
    $choices['1'] = get_string('emaildisplayyes');
    $choices['2'] = get_string('emaildisplaycourse');
    $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
    $mform->setDefault('maildisplay', $CFG->defaultpreference_maildisplay);

    $choices = array();
    $choices['0'] = get_string('textformat');
    $choices['1'] = get_string('htmlformat');
    $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
    $mform->setDefault('mailformat', $CFG->defaultpreference_mailformat);

    if (!empty($CFG->allowusermailcharset)) {
        $choices = array();
        $charsets = get_list_of_charsets();
        if (!empty($CFG->sitemailcharset)) {
            $choices['0'] = get_string('site').' ('.$CFG->sitemailcharset.')';
        } else {
            $choices['0'] = get_string('site').' (UTF-8)';
        }
        $choices = array_merge($choices, $charsets);
        $mform->addElement('select', 'preference_mailcharset', get_string('emailcharset'), $choices);
    }

    $choices = array();
    $choices['0'] = get_string('emaildigestoff');
    $choices['1'] = get_string('emaildigestcomplete');
    $choices['2'] = get_string('emaildigestsubjects');
    $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
    $mform->setDefault('maildigest', $CFG->defaultpreference_maildigest);
    $mform->addHelpButton('maildigest', 'emaildigest');

    $choices = array();
    $choices['1'] = get_string('autosubscribeyes');
    $choices['0'] = get_string('autosubscribeno');
    $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
    $mform->setDefault('autosubscribe', $CFG->defaultpreference_autosubscribe);

    if (!empty($CFG->forum_trackreadposts)) {
        $choices = array();
        $choices['0'] = get_string('trackforumsno');
        $choices['1'] = get_string('trackforumsyes');
        $mform->addElement('select', 'trackforums', get_string('trackforums'), $choices);
        $mform->setDefault('trackforums', $CFG->defaultpreference_trackforums);
    }

    $editors = editors_get_enabled();
    if (count($editors) > 1) {
        $choices = array('' => get_string('defaulteditor'));
        $firsteditor = '';
        foreach (array_keys($editors) as $editor) {
            if (!$firsteditor) {
                $firsteditor = $editor;
            }
            $choices[$editor] = get_string('pluginname', 'editor_' . $editor);
        }
        $mform->addElement('select', 'preference_htmleditor', get_string('textediting'), $choices);
        $mform->setDefault('preference_htmleditor', '');
    } else {
        // Empty string means use the first chosen text editor.
        $mform->addElement('hidden', 'preference_htmleditor');
        $mform->setDefault('preference_htmleditor', '');
        $mform->setType('preference_htmleditor', PARAM_PLUGIN);
    }

    $mform->addElement('select', 'lang', get_string('preferredlanguage'), get_string_manager()->get_list_of_translations());
    $mform->setDefault('lang', $CFG->lang);

}


/**
 * Convert region timezone to php supported timezone
 *
 * @deprecated since Moodle 2.9
 * @param string $tz value from ical file
 * @return string $tz php supported timezone
 */
function calendar_normalize_tz($tz) {
    debugging('calendar_normalize_tz() is deprecated, use core_date::normalise_timezone() instead', DEBUG_DEVELOPER);
    return core_date::normalise_timezone($tz);
}

/**
 * Returns a float which represents the user's timezone difference from GMT in hours
 * Checks various settings and picks the most dominant of those which have a value
 * @deprecated since Moodle 2.9
 * @param float|int|string $tz timezone user timezone
 * @return float
 */
function get_user_timezone_offset($tz = 99) {
    debugging('get_user_timezone_offset() is deprecated, use PHP DateTimeZone instead', DEBUG_DEVELOPER);
    $tz = core_date::get_user_timezone($tz);
    $date = new DateTime('now', new DateTimeZone($tz));
    return ($date->getOffset() - dst_offset_on(time(), $tz)) / (3600.0);
}

/**
 * Returns an int which represents the systems's timezone difference from GMT in seconds
 * @deprecated since Moodle 2.9
 * @param float|int|string $tz timezone for which offset is required.
 *        {@link http://docs.moodle.org/dev/Time_API#Timezone}
 * @return int|bool if found, false is timezone 99 or error
 */
function get_timezone_offset($tz) {
    debugging('get_timezone_offset() is deprecated, use PHP DateTimeZone instead', DEBUG_DEVELOPER);
    $date = new DateTime('now', new DateTimeZone(core_date::normalise_timezone($tz)));
    return $date->getOffset() - dst_offset_on(time(), $tz);
}

/**
 * Returns a list of timezones in the current language.
 * @deprecated since Moodle 2.9
 * @return array
 */
function get_list_of_timezones() {
    debugging('get_list_of_timezones() is deprecated, use core_date::get_list_of_timezones() instead', DEBUG_DEVELOPER);
    return core_date::get_list_of_timezones();
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 * @param array $timezones
 */
function update_timezone_records($timezones) {
    debugging('update_timezone_records() is not available any more, use standard PHP date/time code', DEBUG_DEVELOPER);
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 * @param int $fromyear
 * @param int $toyear
 * @param mixed $strtimezone
 * @return bool
 */
function calculate_user_dst_table($fromyear = null, $toyear = null, $strtimezone = null) {
    debugging('calculate_user_dst_table() is not available any more, use standard PHP date/time code', DEBUG_DEVELOPER);
    return false;
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 * @param int|string $year
 * @param mixed $timezone
 * @return null
 */
function dst_changes_for_year($year, $timezone) {
    debugging('dst_changes_for_year() is not available any more, use standard PHP date/time code', DEBUG_DEVELOPER);
    return null;
}

/**
 * Previous internal API, it was not supposed to be used anywhere.
 * @deprecated since Moodle 2.9
 * @param string $timezonename
 * @return array
 */
function get_timezone_record($timezonename) {
    debugging('get_timezone_record() is not available any more, use standard PHP date/time code', DEBUG_DEVELOPER);
    return array();
}

/* === Apis deprecated since Moodle 3.0 === */
/**
 * Returns the URL of the HTTP_REFERER, less the querystring portion if required.
 *
 * @deprecated since Moodle 3.0 MDL-49360 - please do not use this function any more.
 * @todo Remove this function in Moodle 3.2
 * @param boolean $stripquery if true, also removes the query part of the url.
 * @return string The resulting referer or empty string.
 */
function get_referer($stripquery = true) {
    debugging('get_referer() is deprecated. Please use get_local_referer() instead.', DEBUG_DEVELOPER);
    if (isset($_SERVER['HTTP_REFERER'])) {
        if ($stripquery) {
            return strip_querystring($_SERVER['HTTP_REFERER']);
        } else {
            return $_SERVER['HTTP_REFERER'];
        }
    } else {
        return '';
    }
}

/**
 * Checks if current user is a web crawler.
 *
 * This list can not be made complete, this is not a security
 * restriction, we make the list only to help these sites
 * especially when automatic guest login is disabled.
 *
 * If admin needs security they should enable forcelogin
 * and disable guest access!!
 *
 * @return bool
 * @deprecated since Moodle 3.0 use \core_useragent::is_web_crawler instead.
 */
function is_web_crawler() {
    debugging('is_web_crawler() has been deprecated, please use core_useragent::is_web_crawler() instead.', DEBUG_DEVELOPER);
    return core_useragent::is_web_crawler();
}

/**
 * Update user's course completion statuses
 *
 * First update all criteria completions, then aggregate all criteria completions
 * and update overall course completions.
 *
 * @deprecated since Moodle 3.0 MDL-50287 - please do not use this function any more.
 * @todo Remove this function in Moodle 3.2 MDL-51226.
 */
function completion_cron() {
    global $CFG;
    require_once($CFG->dirroot.'/completion/cron.php');

    debugging('completion_cron() is deprecated. Functionality has been moved to scheduled tasks.', DEBUG_DEVELOPER);
    completion_cron_mark_started();

    completion_cron_criteria();

    completion_cron_completions();
}

/**
 * Returns an ordered array of tags associated with visible courses
 * (boosted replacement of get_all_tags() allowing association with user and tagtype).
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param    int      $courseid A course id. Passing 0 will return all distinct tags for all visible courses
 * @param    int      $userid   (optional) the user id, a default of 0 will return all users tags for the course
 * @param    string   $tagtype  (optional) The type of tag, empty string returns all types. Currently (Moodle 2.2) there are two
 *                              types of tags which are used within Moodle, they are 'official' and 'default'.
 * @param    int      $numtags  (optional) number of tags to display, default of 80 is set in the block, 0 returns all
 * @param    string   $unused   (optional) was selected sorting, moved to tag_print_cloud()
 * @return   array
 */
function coursetag_get_tags($courseid, $userid=0, $tagtype='', $numtags=0, $unused = '') {
    debugging('Function coursetag_get_tags() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    // get visible course ids
    $courselist = array();
    if ($courseid === 0) {
        if ($courses = $DB->get_records_select('course', 'visible=1 AND category>0', null, '', 'id')) {
            foreach ($courses as $key => $value) {
                $courselist[] = $key;
            }
        }
    }

    // get tags from the db ordered by highest count first
    $params = array();
    $sql = "SELECT id as tkey, name, id, tagtype, rawname, f.timemodified, flag, count
              FROM {tag} t,
                 (SELECT tagid, MAX(timemodified) as timemodified, COUNT(id) as count
                    FROM {tag_instance}
                   WHERE itemtype = 'course' ";

    if ($courseid > 0) {
        $sql .= "    AND itemid = :courseid ";
        $params['courseid'] = $courseid;
    } else {
        if (!empty($courselist)) {
            list($usql, $uparams) = $DB->get_in_or_equal($courselist, SQL_PARAMS_NAMED);
            $sql .= "AND itemid $usql ";
            $params = $params + $uparams;
        }
    }

    if ($userid > 0) {
        $sql .= "    AND tiuserid = :userid ";
        $params['userid'] = $userid;
    }

    $sql .= "   GROUP BY tagid) f
             WHERE t.id = f.tagid ";
    if ($tagtype != '') {
        $sql .= "AND tagtype = :tagtype ";
        $params['tagtype'] = $tagtype;
    }
    $sql .= "ORDER BY count DESC, name ASC";

    // limit the number of tags for output
    if ($numtags == 0) {
        $tags = $DB->get_records_sql($sql, $params);
    } else {
        $tags = $DB->get_records_sql($sql, $params, 0, $numtags);
    }

    // prepare the return
    $return = array();
    if ($tags) {
        // avoid print_tag_cloud()'s ksort upsetting ordering by setting the key here
        foreach ($tags as $value) {
            $return[] = $value;
        }
    }

    return $return;

}

/**
 * Returns an ordered array of tags
 * (replaces popular_tags_count() allowing sorting).
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param    string $unused (optional) was selected sorting - moved to tag_print_cloud()
 * @param    int    $numtags (optional) number of tags to display, default of 20 is set in the block, 0 returns all
 * @return   array
 */
function coursetag_get_all_tags($unused='', $numtags=0) {
    debugging('Function coursetag_get_all_tag() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    // note that this selects all tags except for courses that are not visible
    $sql = "SELECT id, name, tagtype, rawname, f.timemodified, flag, count
        FROM {tag} t,
        (SELECT tagid, MAX(timemodified) as timemodified, COUNT(id) as count
            FROM {tag_instance} WHERE tagid NOT IN
                (SELECT tagid FROM {tag_instance} ti, {course} c
                WHERE c.visible = 0
                AND ti.itemtype = 'course'
                AND ti.itemid = c.id)
        GROUP BY tagid) f
        WHERE t.id = f.tagid
        ORDER BY count DESC, name ASC";
    if ($numtags == 0) {
        $tags = $DB->get_records_sql($sql);
    } else {
        $tags = $DB->get_records_sql($sql, null, 0, $numtags);
    }

    $return = array();
    if ($tags) {
        foreach ($tags as $value) {
            $return[] = $value;
        }
    }

    return $return;
}

/**
 * Returns javascript for use in tags block and supporting pages
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @return   null
 */
function coursetag_get_jscript() {
    debugging('Function coursetag_get_jscript() is deprecated and obsolete.', DEBUG_DEVELOPER);
    return '';
}

/**
 * Returns javascript to create the links in the tag block footer.
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param    string   $elementid       the element to attach the footer to
 * @param    array    $coursetagslinks links arrays each consisting of 'title', 'onclick' and 'text' elements
 * @return   string   always returns a blank string
 */
function coursetag_get_jscript_links($elementid, $coursetagslinks) {
    debugging('Function coursetag_get_jscript_links() is deprecated and obsolete.', DEBUG_DEVELOPER);
    return '';
}

/**
 * Returns all tags created by a user for a course
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param    int      $courseid tags are returned for the course that has this courseid
 * @param    int      $userid   return tags which were created by this user
 */
function coursetag_get_records($courseid, $userid) {
    debugging('Function coursetag_get_records() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    $sql = "SELECT t.id, name, rawname
              FROM {tag} t, {tag_instance} ti
             WHERE t.id = ti.tagid
                 AND ti.tiuserid = :userid
                 AND ti.itemid = :courseid
          ORDER BY name ASC";

    return $DB->get_records_sql($sql, array('userid'=>$userid, 'courseid'=>$courseid));
}

/**
 * Stores a tag for a course for a user
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param    array  $tags     simple array of keywords to be stored
 * @param    int    $courseid the id of the course we wish to store a tag for
 * @param    int    $userid   the id of the user we wish to store a tag for
 * @param    string $tagtype  official or default only
 * @param    string $myurl    (optional) for logging creation of course tags
 */
function coursetag_store_keywords($tags, $courseid, $userid=0, $tagtype='official', $myurl='') {
    debugging('Function coursetag_store_keywords() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    global $CFG;
    require_once $CFG->dirroot.'/tag/lib.php';

    if (is_array($tags) and !empty($tags)) {
        foreach ($tags as $tag) {
            $tag = trim($tag);
            if (strlen($tag) > 0) {
                //tag_set_add('course', $courseid, $tag, $userid); //deletes official tags

                //add tag if does not exist
                if (!$tagid = tag_get_id($tag)) {
                    $tag_id_array = tag_add(array($tag), $tagtype);
                    $tagid = $tag_id_array[core_text::strtolower($tag)];
                }
                //ordering
                $ordering = 0;
                if ($current_ids = tag_get_tags_ids('course', $courseid)) {
                    end($current_ids);
                    $ordering = key($current_ids) + 1;
                }
                //set type
                tag_type_set($tagid, $tagtype);

                //tag_instance entry
                tag_assign('course', $courseid, $tagid, $ordering, $userid, 'core', context_course::instance($courseid)->id);
            }
        }
    }

}

/**
 * Deletes a personal tag for a user for a course.
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param    int      $tagid    the tag we wish to delete
 * @param    int      $userid   the user that the tag is associated with
 * @param    int      $courseid the course that the tag is associated with
 */
function coursetag_delete_keyword($tagid, $userid, $courseid) {
    debugging('Function coursetag_delete_keyword() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    tag_delete_instance('course', $courseid, $tagid, $userid);
}

/**
 * Get courses tagged with a tag
 *
 * @deprecated since 3.0
 * @package  core_tag
 * @category tag
 * @param int $tagid
 * @return array of course objects
 */
function coursetag_get_tagged_courses($tagid) {
    debugging('Function coursetag_get_tagged_courses() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    global $DB;

    $courses = array();

    $ctxselect = context_helper::get_preload_record_columns_sql('ctx');

    $sql = "SELECT c.*, $ctxselect
            FROM {course} c
            JOIN {tag_instance} t ON t.itemid = c.id
            JOIN {context} ctx ON ctx.instanceid = c.id
            WHERE t.tagid = :tagid AND
            t.itemtype = 'course' AND
            ctx.contextlevel = :contextlevel
            ORDER BY c.sortorder ASC";
    $params = array('tagid' => $tagid, 'contextlevel' => CONTEXT_COURSE);
    $rs = $DB->get_recordset_sql($sql, $params);
    foreach ($rs as $course) {
        context_helper::preload_from_record($course);
        if ($course->visible == 1 || has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
            $courses[$course->id] = $course;
        }
    }
    return $courses;
}

/**
 * Course tagging function used only during the deletion of a course (called by lib/moodlelib.php) to clean up associated tags
 *
 * @package core_tag
 * @deprecated since 3.0
 * @param   int      $courseid     the course we wish to delete tag instances from
 * @param   bool     $showfeedback if we should output a notification of the delete to the end user
 */
function coursetag_delete_course_tags($courseid, $showfeedback=false) {
    debugging('Function coursetag_delete_course_tags() is deprecated. Userid is no longer used for tagging courses.', DEBUG_DEVELOPER);

    global $DB, $OUTPUT;

    if ($taginstances = $DB->get_recordset_select('tag_instance', "itemtype = 'course' AND itemid = :courseid",
        array('courseid' => $courseid), '', 'tagid, tiuserid')) {

        foreach ($taginstances as $record) {
            tag_delete_instance('course', $courseid, $record->tagid, $record->tiuserid);
        }
        $taginstances->close();
    }

    if ($showfeedback) {
        echo $OUTPUT->notification(get_string('deletedcoursetags', 'tag'), 'notifysuccess');
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
 * Add includes (js and css) into uploaded files before returning them,
 * useful for themes and utf.js includes.
 *
 * @param string $text text to search and replace
 * @return string text
 * @deprecated Moodle 3.0.5 See MDL-29738
 */
function file_modify_html_header($text) {
    debugging('file_modify_html_header() is deprecated and will not be replaced.', DEBUG_DEVELOPER);
    return $text;
}
