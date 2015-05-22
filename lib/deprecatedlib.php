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
 * @return bool always returns false
 */
function password_compat_not_supported() {
    debugging('Do not use password_compat_not_supported() - bcrypt is now always available', DEBUG_DEVELOPER);
    return false;
}

/**
 * Factory method that was returning moodle_session object.
 *
 * @deprecated since 2.6
 * @return \core\session\manager
 */
function session_get_instance() {
    // Note: the new session manager includes all methods from the original session class.
    static $deprecatedinstance = null;

    debugging('session_get_instance() is deprecated, use \core\session\manager instead', DEBUG_DEVELOPER);

    if (!$deprecatedinstance) {
        $deprecatedinstance = new \core\session\manager();
    }

    return $deprecatedinstance;
}

/**
 * Returns true if legacy session used.
 *
 * @deprecated since 2.6
 * @return bool
 */
function session_is_legacy() {
    debugging('session_is_legacy() is deprecated, do not use any more', DEBUG_DEVELOPER);
    return false;
}

/**
 * Terminates all sessions, auth hooks are not executed.
 * Useful in upgrade scripts.
 *
 * @deprecated since 2.6
 */
function session_kill_all() {
    debugging('session_kill_all() is deprecated, use \core\session\manager::kill_all_sessions() instead', DEBUG_DEVELOPER);
    \core\session\manager::kill_all_sessions();
}

/**
 * Mark session as accessed, prevents timeouts.
 *
 * @deprecated since 2.6
 * @param string $sid
 */
function session_touch($sid) {
    debugging('session_touch() is deprecated, use \core\session\manager::touch_session() instead', DEBUG_DEVELOPER);
    \core\session\manager::touch_session($sid);
}

/**
 * Terminates one sessions, auth hooks are not executed.
 *
 * @deprecated since 2.6
 * @param string $sid session id
 */
function session_kill($sid) {
    debugging('session_kill() is deprecated, use \core\session\manager::kill_session() instead', DEBUG_DEVELOPER);
    \core\session\manager::kill_session($sid);
}

/**
 * Terminates all sessions of one user, auth hooks are not executed.
 * NOTE: This can not work for file based sessions!
 *
 * @deprecated since 2.6
 * @param int $userid user id
 */
function session_kill_user($userid) {
    debugging('session_kill_user() is deprecated, use \core\session\manager::kill_user_sessions() instead', DEBUG_DEVELOPER);
    \core\session\manager::kill_user_sessions($userid);
}

/**
 * Setup $USER object - called during login, loginas, etc.
 *
 * Call sync_user_enrolments() manually after log-in, or log-in-as.
 *
 * @deprecated since 2.6
 * @param stdClass $user full user record object
 * @return void
 */
function session_set_user($user) {
    debugging('session_set_user() is deprecated, use \core\session\manager::set_user() instead', DEBUG_DEVELOPER);
    \core\session\manager::set_user($user);
}

/**
 * Is current $USER logged-in-as somebody else?
 * @deprecated since 2.6
 * @return bool
 */
function session_is_loggedinas() {
    debugging('session_is_loggedinas() is deprecated, use \core\session\manager::is_loggedinas() instead', DEBUG_DEVELOPER);
    return \core\session\manager::is_loggedinas();
}

/**
 * Returns the $USER object ignoring current login-as session
 * @deprecated since 2.6
 * @return stdClass user object
 */
function session_get_realuser() {
    debugging('session_get_realuser() is deprecated, use \core\session\manager::get_realuser() instead', DEBUG_DEVELOPER);
    return \core\session\manager::get_realuser();
}

/**
 * Login as another user - no security checks here.
 * @deprecated since 2.6
 * @param int $userid
 * @param stdClass $context
 * @return void
 */
function session_loginas($userid, $context) {
    debugging('session_loginas() is deprecated, use \core\session\manager::loginas() instead', DEBUG_DEVELOPER);
    \core\session\manager::loginas($userid, $context);
}

/**
 * Minify JavaScript files.
 *
 * @deprecated since 2.6
 *
 * @param array $files
 * @return string
 */
function js_minify($files) {
    debugging('js_minify() is deprecated, use core_minify::js_files() or core_minify::js() instead.');
    return core_minify::js_files($files);
}

/**
 * Minify CSS files.
 *
 * @deprecated since 2.6
 *
 * @param array $files
 * @return string
 */
function css_minify_css($files) {
    debugging('css_minify_css() is deprecated, use core_minify::css_files() or core_minify::css() instead.');
    return core_minify::css_files($files);
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


// === Deprecated before 2.6.0 ===

/**
 * Hack to find out the GD version by parsing phpinfo output
 *
 * @return int GD version (1, 2, or 0)
 */
function check_gd_version() {
    // TODO: delete function in Moodle 2.7
    debugging('check_gd_version() is deprecated, GD extension is always available now');

    $gdversion = 0;

    if (function_exists('gd_info')){
        $gd_info = gd_info();
        if (substr_count($gd_info['GD Version'], '2.')) {
            $gdversion = 2;
        } else if (substr_count($gd_info['GD Version'], '1.')) {
            $gdversion = 1;
        }

    } else {
        ob_start();
        phpinfo(INFO_MODULES);
        $phpinfo = ob_get_contents();
        ob_end_clean();

        $phpinfo = explode("\n", $phpinfo);


        foreach ($phpinfo as $text) {
            $parts = explode('</td>', $text);
            foreach ($parts as $key => $val) {
                $parts[$key] = trim(strip_tags($val));
            }
            if ($parts[0] == 'GD Version') {
                if (substr_count($parts[1], '2.0')) {
                    $parts[1] = '2.0';
                }
                $gdversion = intval($parts[1]);
            }
        }
    }

    return $gdversion;   // 1, 2 or 0
}

/**
 * Not used any more, the account lockout handling is now
 * part of authenticate_user_login().
 * @deprecated
 */
function update_login_count() {
    // TODO: delete function in Moodle 2.6
    debugging('update_login_count() is deprecated, all calls need to be removed');
}

/**
 * Not used any more, replaced by proper account lockout.
 * @deprecated
 */
function reset_login_count() {
    // TODO: delete function in Moodle 2.6
    debugging('reset_login_count() is deprecated, all calls need to be removed');
}

/**
 * Insert or update log display entry. Entry may already exist.
 * $module, $action must be unique
 * @deprecated
 *
 * @param string $module
 * @param string $action
 * @param string $mtable
 * @param string $field
 * @return void
 *
 */
function update_log_display_entry($module, $action, $mtable, $field) {
    global $DB;

    debugging('The update_log_display_entry() is deprecated, please use db/log.php description file instead.');
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
 * Searches logs to find all enrolments since a certain date
 *
 * used to print recent activity
 *
 * @param int $courseid The course in question.
 * @param int $timestart The date to check forward of
 * @return object|false  {@link $USER} records or false if error.
 */
function get_recent_enrolments($courseid, $timestart) {
    global $DB;

    debugging('get_recent_enrolments() is deprecated as it returned inaccurate results.', DEBUG_DEVELOPER);

    $context = context_course::instance($courseid);
    $sql = "SELECT u.id, u.firstname, u.lastname, MAX(l.time)
              FROM {user} u, {role_assignments} ra, {log} l
             WHERE l.time > ?
                   AND l.course = ?
                   AND l.module = 'course'
                   AND l.action = 'enrol'
                   AND ".$DB->sql_cast_char2int('l.info')." = u.id
                   AND u.id = ra.userid
                   AND ra.contextid ".get_related_contexts_string($context)."
          GROUP BY u.id, u.firstname, u.lastname
          ORDER BY MAX(l.time) ASC";
    $params = array($timestart, $courseid);
    return $DB->get_records_sql($sql, $params);
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
 * @param object $course Course Object
 * @param object $cm Course Manager Object
 * @return mixed $course->groupmode
 */
function groupmode($course, $cm=null) {

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
 * @global object
 * @param int $courseid The course being examined - relates to id field in
 * 'course' table.
 * @param int $groupid The group being examined.
 * @return int Current group id which was set by this function
 */
function set_current_group($courseid, $groupid) {
    global $SESSION;
    return $SESSION->currentgroup[$courseid] = $groupid;
}


/**
 * Gets the current group - either from the session variable or from the database.
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
 * Filter a user list and return only the users that can see the course module based on
 * groups/permissions etc. It is assumed that the users are pre-filtered to those who are enrolled in the course.
 *
 * @category group
 * @param stdClass|cm_info $cm The course module
 * @param array $users An array of users, indexed by userid
 * @return array A filtered list of users that can see the module, indexed by userid.
 * @deprecated Since Moodle 2.8
 */
function groups_filter_users_by_course_module_visible($cm, $users) {
    debugging('groups_filter_users_by_course_module_visible() is deprecated. ' .
            'Replace with a call to \core_availability\info_module::filter_user_list(), ' .
            'which does basically the same thing but includes other restrictions such ' .
            'as profile restrictions.', DEBUG_DEVELOPER);
    if (empty($users)) {
        return $users;
    }
    // Since this function allows stdclass, let's play it safe and ensure we
    // do have a cm_info.
    if (!($cm instanceof cm_info)) {
        $modinfo = get_fast_modinfo($cm->course);
        $cm = $modinfo->get_cm($cm->id);
    }
    $info = new \core_availability\info_module($cm);
    return $info->filter_user_list($users);
}

/**
 * Determine if a course module is currently visible to a user
 *
 * Deprecated (it was never very useful as it only took into account the
 * groupmembersonly option and no other way of hiding activities). Always
 * returns true.
 *
 * @category group
 * @param stdClass|cm_info $cm The course module
 * @param int $userid The user to check against the group.
 * @return bool True
 * @deprecated Since Moodle 2.8
 */
function groups_course_module_visible($cm, $userid=null) {
    debugging('groups_course_module_visible() is deprecated and always returns ' .
            'true; use $cm->uservisible to decide whether the current user can ' .
            'access an activity.', DEBUG_DEVELOPER);
    return true;
}

/**
 * Inndicates fatal error. This function was originally printing the
 * error message directly, since 2.0 it is throwing exception instead.
 * The error printing is handled in default exception handler.
 *
 * Old method, don't call directly in new code - use print_error instead.
 *
 * @param string $message The message to display to the user about the error.
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
 * @return void, always throws moodle_exception
 */
function error($message, $link='') {
    throw new moodle_exception('notlocalisederrormessage', 'error', $link, $message, 'error() is a deprecated function, please call print_error() instead of error()');
}


/**
 * @deprecated use $PAGE->theme->name instead.
 */
function current_theme() {
    throw new coding_exception('current_theme() can not be used any more, please use $PAGE->theme->name instead');
}

/**
 * Prints some red text using echo
 *
 * @deprecated
 * @param string $error The text to be displayed in red
 */
function formerr($error) {
    debugging('formerr() has been deprecated. Please change your code to use $OUTPUT->error_text($string).');
    global $OUTPUT;
    echo $OUTPUT->error_text($error);
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
 * Returns a string of html with an image of a help icon linked to a help page on a number of help topics.
 * Should be used only with htmleditor or textarea.
 *
 * @global object
 * @global object
 * @param mixed $helptopics variable amount of params accepted. Each param may be a string or an array of arguments for
 *                  helpbutton.
 * @return string Link to help button
 */
function editorhelpbutton(){
    return '';

    /// TODO: MDL-21215
}

/**
 * Print a help button.
 *
 * Prints a special help button for html editors (htmlarea in this case)
 *
 * @todo Write code into this function! detect current editor and print correct info
 * @global object
 * @return string Only returns an empty string at the moment
 */
function editorshortcutshelpbutton() {
    /// TODO: MDL-21215

    global $CFG;
    //TODO: detect current editor and print correct info
    return '';
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
 * Given an array of values, output the HTML for a select element with those options.
 *
 * @deprecated since Moodle 2.0
 *
 * Normally, you only need to use the first few parameters.
 *
 * @param array $options The options to offer. An array of the form
 *      $options[{value}] = {text displayed for that option};
 * @param string $name the name of this form control, as in &lt;select name="..." ...
 * @param string $selected the option to select initially, default none.
 * @param string $nothing The label for the 'nothing is selected' option. Defaults to get_string('choose').
 *      Set this to '' if you don't want a 'nothing is selected' option.
 * @param string $script if not '', then this is added to the &lt;select> element as an onchange handler.
 * @param string $nothingvalue The value corresponding to the $nothing option. Defaults to 0.
 * @param boolean $return if false (the default) the the output is printed directly, If true, the
 *      generated HTML is returned as a string.
 * @param boolean $disabled if true, the select is generated in a disabled state. Default, false.
 * @param int $tabindex if give, sets the tabindex attribute on the &lt;select> element. Default none.
 * @param string $id value to use for the id attribute of the &lt;select> element. If none is given,
 *      then a suitable one is constructed.
 * @param mixed $listbox if false, display as a dropdown menu. If true, display as a list box.
 *      By default, the list box will have a number of rows equal to min(10, count($options)), but if
 *      $listbox is an integer, that number is used for size instead.
 * @param boolean $multiple if true, enable multiple selections, else only 1 item can be selected. Used
 *      when $listbox display is enabled
 * @param string $class value to use for the class attribute of the &lt;select> element. If none is given,
 *      then a suitable one is constructed.
 * @return string|void If $return=true returns string, else echo's and returns void
 */
function choose_from_menu ($options, $name, $selected='', $nothing='choose', $script='',
                           $nothingvalue='0', $return=false, $disabled=false, $tabindex=0,
                           $id='', $listbox=false, $multiple=false, $class='') {

    global $OUTPUT;
    debugging('choose_from_menu() has been deprecated. Please change your code to use html_writer::select().');

    if ($script) {
        debugging('The $script parameter has been deprecated. You must use component_actions instead', DEBUG_DEVELOPER);
    }
    $attributes = array();
    $attributes['disabled'] = $disabled ? 'disabled' : null;
    $attributes['tabindex'] = $tabindex ? $tabindex : null;
    $attributes['multiple'] = $multiple ? $multiple : null;
    $attributes['class'] = $class ? $class : null;
    $attributes['id'] = $id ? $id : null;

    $output = html_writer::select($options, $name, $selected, array($nothingvalue=>$nothing), $attributes);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
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
 * Call this function to update an event in the calendar table
 * the event will be identified by the id field of the $event object.
 *
 * @param object $event An object representing an event from the calendar table. The event will be identified by the id field.
 * @return bool Success
 * @deprecated please calendar_event->update() instead.
 */
function update_event($event) {
    global $CFG;
    require_once($CFG->dirroot.'/calendar/lib.php');

    debugging('update_event() is deprecated, please use calendar_event->update() instead.', DEBUG_DEVELOPER);
    $event = (object)$event;
    $calendarevent = calendar_event::load($event->id);
    return $calendarevent->update($event);
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
 * Original singleton helper function, please use static methods instead,
 * ex: core_text::convert().
 *
 * @deprecated since Moodle 2.2 use core_text::xxxx() instead.
 * @see core_text
 */
function textlib_get_instance() {
    throw new coding_exception('textlib_get_instance() can not be used any more, please use '.
        'core_text::functioname() instead.');
}

/**
 * Gets the generic section name for a courses section
 *
 * The global function is deprecated. Each course format can define their own generic section name
 *
 * @deprecated since 2.4
 * @see get_section_name()
 * @see format_base::get_section_name()
 *
 * @param string $format Course format ID e.g. 'weeks' $course->format
 * @param stdClass $section Section object from database
 * @return Display name that the course format prefers, e.g. "Week 2"
 */
function get_generic_section_name($format, stdClass $section) {
    debugging('get_generic_section_name() is deprecated. Please use appropriate functionality from class format_base', DEBUG_DEVELOPER);
    return get_string('sectionname', "format_$format") . ' ' . $section->section;
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
 *
 * @param int $courseid
 * @return array Array of section_info objects
 */
function get_all_sections($courseid) {
    global $DB;
    debugging('get_all_sections() is deprecated. See phpdocs for this function', DEBUG_DEVELOPER);
    return get_fast_modinfo($courseid)->get_section_info_all();
}

/**
 * Given a full mod object with section and course already defined, adds this module to that section.
 *
 * This function is deprecated, please use {@link course_add_cm_to_section()}
 * Note that course_add_cm_to_section() also updates field course_modules.section and
 * calls rebuild_course_cache()
 *
 * @deprecated since 2.4
 *
 * @param object $mod
 * @param int $beforemod An existing ID which we will insert the new module before
 * @return int The course_sections ID where the mod is inserted
 */
function add_mod_to_section($mod, $beforemod = null) {
    debugging('Function add_mod_to_section() is deprecated, please use course_add_cm_to_section()', DEBUG_DEVELOPER);
    global $DB;
    return course_add_cm_to_section($mod->course, $mod->coursemodule, $mod->section, $beforemod);
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
 *
 * @param int $courseid id of the course to get info about
 * @param array $mods (return) list of course modules
 * @param array $modnames (return) list of names of all module types installed and available
 * @param array $modnamesplural (return) list of names of all module types installed and available in the plural form
 * @param array $modnamesused (return) list of names of all module types used in the course
 */
function get_all_mods($courseid, &$mods, &$modnames, &$modnamesplural, &$modnamesused) {
    debugging('Function get_all_mods() is deprecated. Use get_fast_modinfo() and get_module_types_names() instead. See phpdocs for details', DEBUG_DEVELOPER);

    global $COURSE;
    $modnames      = get_module_types_names();
    $modnamesplural= get_module_types_names(true);
    $modinfo = get_fast_modinfo($courseid);
    $mods = $modinfo->get_cms();
    $modnamesused = $modinfo->get_used_module_names();
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
 *
 * @param int $section relative section number (field course_sections.section)
 * @param int $courseid
 * @return stdClass record from table {course_sections}
 */
function get_course_section($section, $courseid) {
    global $DB;
    debugging('Function get_course_section() is deprecated. Please use course_create_sections_if_missing() and get_fast_modinfo() instead.', DEBUG_DEVELOPER);

    if ($cw = $DB->get_record("course_sections", array("section"=>$section, "course"=>$courseid))) {
        return $cw;
    }
    $cw = new stdClass();
    $cw->course   = $courseid;
    $cw->section  = $section;
    $cw->summary  = "";
    $cw->summaryformat = FORMAT_HTML;
    $cw->sequence = "";
    $id = $DB->insert_record("course_sections", $cw);
    rebuild_course_cache($courseid, true);
    return $DB->get_record("course_sections", array("id"=>$id));
}

/**
 * Return the start and end date of the week in Weekly course format
 *
 * It is not recommended to use this function outside of format_weeks plugin
 *
 * @deprecated since 2.4
 * @see format_weeks::get_section_dates()
 *
 * @param stdClass $section The course_section entry from the DB
 * @param stdClass $course The course entry from DB
 * @return stdClass property start for startdate, property end for enddate
 */
function format_weeks_get_section_dates($section, $course) {
    debugging('Function format_weeks_get_section_dates() is deprecated. It is not recommended to'.
            ' use it outside of format_weeks plugin', DEBUG_DEVELOPER);
    if (isset($course->format) && $course->format === 'weeks') {
        return course_get_format($course)->get_section_dates($section);
    }
    return null;
}

/**
 * Obtains shared data that is used in print_section when displaying a
 * course-module entry.
 *
 * Deprecated. Instead of:
 * list($content, $name) = get_print_section_cm_text($cm, $course);
 * use:
 * $content = $cm->get_formatted_content(array('overflowdiv' => true, 'noclean' => true));
 * $name = $cm->get_formatted_name();
 *
 * @deprecated since 2.5
 * @see cm_info::get_formatted_content()
 * @see cm_info::get_formatted_name()
 *
 * This data is also used in other areas of the code.
 * @param cm_info $cm Course-module data (must come from get_fast_modinfo)
 * @param object $course (argument not used)
 * @return array An array with the following values in this order:
 *   $content (optional extra content for after link),
 *   $instancename (text of link)
 */
function get_print_section_cm_text(cm_info $cm, $course) {
    debugging('Function get_print_section_cm_text() is deprecated. Please use '.
            'cm_info::get_formatted_content() and cm_info::get_formatted_name()',
            DEBUG_DEVELOPER);
    return array($cm->get_formatted_content(array('overflowdiv' => true, 'noclean' => true)),
        $cm->get_formatted_name());
}

/**
 * Prints the menus to add activities and resources.
 *
 * Deprecated. Please use:
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * $output = $courserenderer->course_section_add_cm_control($course, $section, $sectionreturn,
 *    array('inblock' => $vertical));
 * echo $output; // if $return argument in print_section_add_menus() set to false
 *
 * @deprecated since 2.5
 * @see core_course_renderer::course_section_add_cm_control()
 *
 * @param stdClass $course course object, must be the same as set on the page
 * @param int $section relative section number (field course_sections.section)
 * @param null|array $modnames (argument ignored) get_module_types_names() is used instead of argument
 * @param bool $vertical Vertical orientation
 * @param bool $return Return the menus or send them to output
 * @param int $sectionreturn The section to link back to
 * @return void|string depending on $return
 */
function print_section_add_menus($course, $section, $modnames = null, $vertical=false, $return=false, $sectionreturn=null) {
    global $PAGE;
    debugging('Function print_section_add_menus() is deprecated. Please use course renderer '.
            'function course_section_add_cm_control()', DEBUG_DEVELOPER);
    $output = '';
    $courserenderer = $PAGE->get_renderer('core', 'course');
    $output = $courserenderer->course_section_add_cm_control($course, $section, $sectionreturn,
            array('inblock' => $vertical));
    if ($return) {
        return $output;
    } else {
        echo $output;
        return !empty($output);
    }
}

/**
 * Produces the editing buttons for a module
 *
 * Deprecated. Please use:
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * $actions = course_get_cm_edit_actions($mod, $indent, $section);
 * return ' ' . $courserenderer->course_section_cm_edit_actions($actions);
 *
 * @deprecated since 2.5
 * @see course_get_cm_edit_actions()
 * @see core_course_renderer->course_section_cm_edit_actions()
 *
 * @param stdClass $mod The module to produce editing buttons for
 * @param bool $absolute_ignored (argument ignored) - all links are absolute
 * @param bool $moveselect (argument ignored)
 * @param int $indent The current indenting
 * @param int $section The section to link back to
 * @return string XHTML for the editing buttons
 */
function make_editing_buttons(stdClass $mod, $absolute_ignored = true, $moveselect = true, $indent=-1, $section=null) {
    global $PAGE;
    debugging('Function make_editing_buttons() is deprecated, please see PHPdocs in '.
            'lib/deprecatedlib.php on how to replace it', DEBUG_DEVELOPER);
    if (!($mod instanceof cm_info)) {
        $modinfo = get_fast_modinfo($mod->course);
        $mod = $modinfo->get_cm($mod->id);
    }
    $actions = course_get_cm_edit_actions($mod, $indent, $section);

    $courserenderer = $PAGE->get_renderer('core', 'course');
    // The space added before the <span> is a ugly hack but required to set the CSS property white-space: nowrap
    // and having it to work without attaching the preceding text along with it. Hopefully the refactoring of
    // the course page HTML will allow this to be removed.
    return ' ' . $courserenderer->course_section_cm_edit_actions($actions);
}

/**
 * Prints a section full of activity modules
 *
 * Deprecated. Please use:
 * $courserenderer = $PAGE->get_renderer('core', 'course');
 * echo $courserenderer->course_section_cm_list($course, $section, $sectionreturn,
 *     array('hidecompletion' => $hidecompletion));
 *
 * @deprecated since 2.5
 * @see core_course_renderer::course_section_cm_list()
 *
 * @param stdClass $course The course
 * @param stdClass|section_info $section The section object containing properties id and section
 * @param array $mods (argument not used)
 * @param array $modnamesused (argument not used)
 * @param bool $absolute (argument not used)
 * @param string $width (argument not used)
 * @param bool $hidecompletion Hide completion status
 * @param int $sectionreturn The section to return to
 * @return void
 */
function print_section($course, $section, $mods, $modnamesused, $absolute=false, $width="100%", $hidecompletion=false, $sectionreturn=null) {
    global $PAGE;
    debugging('Function print_section() is deprecated. Please use course renderer function '.
            'course_section_cm_list() instead.', DEBUG_DEVELOPER);
    $displayoptions = array('hidecompletion' => $hidecompletion);
    $courserenderer = $PAGE->get_renderer('core', 'course');
    echo $courserenderer->course_section_cm_list($course, $section, $sectionreturn, $displayoptions);
}

/**
 * Displays the list of courses with user notes
 *
 * This function is not used in core. It was replaced by block course_overview
 *
 * @deprecated since 2.5
 *
 * @param array $courses
 * @param array $remote_courses
 */
function print_overview($courses, array $remote_courses=array()) {
    global $CFG, $USER, $DB, $OUTPUT;
    debugging('Function print_overview() is deprecated. Use block course_overview to display this information', DEBUG_DEVELOPER);

    $htmlarray = array();
    if ($modules = $DB->get_records('modules')) {
        foreach ($modules as $mod) {
            if (file_exists(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php')) {
                include_once(dirname(dirname(__FILE__)).'/mod/'.$mod->name.'/lib.php');
                $fname = $mod->name.'_print_overview';
                if (function_exists($fname)) {
                    $fname($courses,$htmlarray);
                }
            }
        }
    }
    foreach ($courses as $course) {
        $fullname = format_string($course->fullname, true, array('context' => context_course::instance($course->id)));
        echo $OUTPUT->box_start('coursebox');
        $attributes = array('title' => s($fullname));
        if (empty($course->visible)) {
            $attributes['class'] = 'dimmed';
        }
        echo $OUTPUT->heading(html_writer::link(
            new moodle_url('/course/view.php', array('id' => $course->id)), $fullname, $attributes), 3);
        if (array_key_exists($course->id,$htmlarray)) {
            foreach ($htmlarray[$course->id] as $modname => $html) {
                echo $html;
            }
        }
        echo $OUTPUT->box_end();
    }

    if (!empty($remote_courses)) {
        echo $OUTPUT->heading(get_string('remotecourses', 'mnet'));
    }
    foreach ($remote_courses as $course) {
        echo $OUTPUT->box_start('coursebox');
        $attributes = array('title' => s($course->fullname));
        echo $OUTPUT->heading(html_writer::link(
            new moodle_url('/auth/mnet/jump.php', array('hostid' => $course->hostid, 'wantsurl' => '/course/view.php?id='.$course->remoteid)),
            format_string($course->shortname),
            $attributes) . ' (' . format_string($course->hostname) . ')', 3);
        echo $OUTPUT->box_end();
    }
}

/**
 * This function trawls through the logs looking for
 * anything new since the user's last login
 *
 * This function was only used to print the content of block recent_activity
 * All functionality is moved into class {@link block_recent_activity}
 * and renderer {@link block_recent_activity_renderer}
 *
 * @deprecated since 2.5
 * @param stdClass $course
 */
function print_recent_activity($course) {
    // $course is an object
    global $CFG, $USER, $SESSION, $DB, $OUTPUT;
    debugging('Function print_recent_activity() is deprecated. It is not recommended to'.
            ' use it outside of block_recent_activity', DEBUG_DEVELOPER);

    $context = context_course::instance($course->id);

    $viewfullnames = has_capability('moodle/site:viewfullnames', $context);

    $timestart = round(time() - COURSE_MAX_RECENT_PERIOD, -2); // better db caching for guests - 100 seconds

    if (!isguestuser()) {
        if (!empty($USER->lastcourseaccess[$course->id])) {
            if ($USER->lastcourseaccess[$course->id] > $timestart) {
                $timestart = $USER->lastcourseaccess[$course->id];
            }
        }
    }

    echo '<div class="activitydate">';
    echo get_string('activitysince', '', userdate($timestart));
    echo '</div>';
    echo '<div class="activityhead">';

    echo '<a href="'.$CFG->wwwroot.'/course/recent.php?id='.$course->id.'">'.get_string('recentactivityreport').'</a>';

    echo "</div>\n";

    $content = false;

/// Firstly, have there been any new enrolments?

    $users = get_recent_enrolments($course->id, $timestart);

    //Accessibility: new users now appear in an <OL> list.
    if ($users) {
        echo '<div class="newusers">';
        echo $OUTPUT->heading(get_string("newusers").':', 3);
        $content = true;
        echo "<ol class=\"list\">\n";
        foreach ($users as $user) {
            $fullname = fullname($user, $viewfullnames);
            echo '<li class="name"><a href="'."$CFG->wwwroot/user/view.php?id=$user->id&amp;course=$course->id\">$fullname</a></li>\n";
        }
        echo "</ol>\n</div>\n";
    }

/// Next, have there been any modifications to the course structure?

    $modinfo = get_fast_modinfo($course);

    $changelist = array();

    $logs = $DB->get_records_select('log', "time > ? AND course = ? AND
                                            module = 'course' AND
                                            (action = 'add mod' OR action = 'update mod' OR action = 'delete mod')",
                                    array($timestart, $course->id), "id ASC");

    if ($logs) {
        $actions  = array('add mod', 'update mod', 'delete mod');
        $newgones = array(); // added and later deleted items
        foreach ($logs as $key => $log) {
            if (!in_array($log->action, $actions)) {
                continue;
            }
            $info = explode(' ', $log->info);

            // note: in most cases I replaced hardcoding of label with use of
            // $cm->has_view() but it was not possible to do this here because
            // we don't necessarily have the $cm for it
            if ($info[0] == 'label') {     // Labels are ignored in recent activity
                continue;
            }

            if (count($info) != 2) {
                debugging("Incorrect log entry info: id = ".$log->id, DEBUG_DEVELOPER);
                continue;
            }

            $modname    = $info[0];
            $instanceid = $info[1];

            if ($log->action == 'delete mod') {
                // unfortunately we do not know if the mod was visible
                if (!array_key_exists($log->info, $newgones)) {
                    $strdeleted = get_string('deletedactivity', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array ('operation' => 'delete', 'text' => $strdeleted);
                }
            } else {
                if (!isset($modinfo->instances[$modname][$instanceid])) {
                    if ($log->action == 'add mod') {
                        // do not display added and later deleted activities
                        $newgones[$log->info] = true;
                    }
                    continue;
                }
                $cm = $modinfo->instances[$modname][$instanceid];
                if (!$cm->uservisible) {
                    continue;
                }

                if ($log->action == 'add mod') {
                    $stradded = get_string('added', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array('operation' => 'add', 'text' => "$stradded:<br /><a href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id={$cm->id}\">".format_string($cm->name, true)."</a>");

                } else if ($log->action == 'update mod' and empty($changelist[$log->info])) {
                    $strupdated = get_string('updated', 'moodle', get_string('modulename', $modname));
                    $changelist[$log->info] = array('operation' => 'update', 'text' => "$strupdated:<br /><a href=\"$CFG->wwwroot/mod/$cm->modname/view.php?id={$cm->id}\">".format_string($cm->name, true)."</a>");
                }
            }
        }
    }

    if (!empty($changelist)) {
        echo $OUTPUT->heading(get_string("courseupdates").':', 3);
        $content = true;
        foreach ($changelist as $changeinfo => $change) {
            echo '<p class="activity">'.$change['text'].'</p>';
        }
    }

/// Now display new things from each module

    $usedmodules = array();
    foreach($modinfo->cms as $cm) {
        if (isset($usedmodules[$cm->modname])) {
            continue;
        }
        if (!$cm->uservisible) {
            continue;
        }
        $usedmodules[$cm->modname] = $cm->modname;
    }

    foreach ($usedmodules as $modname) {      // Each module gets it's own logs and prints them
        if (file_exists($CFG->dirroot.'/mod/'.$modname.'/lib.php')) {
            include_once($CFG->dirroot.'/mod/'.$modname.'/lib.php');
            $print_recent_activity = $modname.'_print_recent_activity';
            if (function_exists($print_recent_activity)) {
                // NOTE: original $isteacher (second parameter below) was replaced with $viewfullnames!
                $content = $print_recent_activity($course, $viewfullnames, $timestart) || $content;
            }
        } else {
            debugging("Missing lib.php in lib/{$modname} - please reinstall files or uninstall the module");
        }
    }

    if (! $content) {
        echo '<p class="message">'.get_string('nothingnew').'</p>';
    }
}

/**
 * Delete a course module and any associated data at the course level (events)
 * Until 1.5 this function simply marked a deleted flag ... now it
 * deletes it completely.
 *
 * @deprecated since 2.5
 *
 * @param int $id the course module id
 * @return boolean true on success, false on failure
 */
function delete_course_module($id) {
    debugging('Function delete_course_module() is deprecated. Please use course_delete_module() instead.', DEBUG_DEVELOPER);

    global $CFG, $DB;

    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/blog/lib.php');

    if (!$cm = $DB->get_record('course_modules', array('id'=>$id))) {
        return true;
    }
    $modulename = $DB->get_field('modules', 'name', array('id'=>$cm->module));
    //delete events from calendar
    if ($events = $DB->get_records('event', array('instance'=>$cm->instance, 'modulename'=>$modulename))) {
        foreach($events as $event) {
            delete_event($event->id);
        }
    }
    //delete grade items, outcome items and grades attached to modules
    if ($grade_items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$modulename,
                                                   'iteminstance'=>$cm->instance, 'courseid'=>$cm->course))) {
        foreach ($grade_items as $grade_item) {
            $grade_item->delete('moddelete');
        }
    }
    // Delete completion and availability data; it is better to do this even if the
    // features are not turned on, in case they were turned on previously (these will be
    // very quick on an empty table)
    $DB->delete_records('course_modules_completion', array('coursemoduleid' => $cm->id));
    $DB->delete_records('course_completion_criteria', array('moduleinstance' => $cm->id,
                                                            'criteriatype' => COMPLETION_CRITERIA_TYPE_ACTIVITY));

    delete_context(CONTEXT_MODULE, $cm->id);
    return $DB->delete_records('course_modules', array('id'=>$cm->id));
}

/**
 * Prints the turn editing on/off button on course/index.php or course/category.php.
 *
 * @deprecated since 2.5
 *
 * @param integer $categoryid The id of the category we are showing, or 0 for system context.
 * @return string HTML of the editing button, or empty string, if this user is not allowed
 *      to see it.
 */
function update_category_button($categoryid = 0) {
    global $CFG, $PAGE, $OUTPUT;
    debugging('Function update_category_button() is deprecated. Pages to view '.
            'and edit courses are now separate and no longer depend on editing mode.',
            DEBUG_DEVELOPER);

    // Check permissions.
    if (!can_edit_in_category($categoryid)) {
        return '';
    }

    // Work out the appropriate action.
    if ($PAGE->user_is_editing()) {
        $label = get_string('turneditingoff');
        $edit = 'off';
    } else {
        $label = get_string('turneditingon');
        $edit = 'on';
    }

    // Generate the button HTML.
    $options = array('categoryedit' => $edit, 'sesskey' => sesskey());
    if ($categoryid) {
        $options['id'] = $categoryid;
        $page = 'category.php';
    } else {
        $page = 'index.php';
    }
    return $OUTPUT->single_button(new moodle_url('/course/' . $page, $options), $label, 'get');
}

/**
 * This function recursively travels the categories, building up a nice list
 * for display. It also makes an array that list all the parents for each
 * category.
 *
 * For example, if you have a tree of categories like:
 *   Miscellaneous (id = 1)
 *      Subcategory (id = 2)
 *         Sub-subcategory (id = 4)
 *   Other category (id = 3)
 * Then after calling this function you will have
 * $list = array(1 => 'Miscellaneous', 2 => 'Miscellaneous / Subcategory',
 *      4 => 'Miscellaneous / Subcategory / Sub-subcategory',
 *      3 => 'Other category');
 * $parents = array(2 => array(1), 4 => array(1, 2));
 *
 * If you specify $requiredcapability, then only categories where the current
 * user has that capability will be added to $list, although all categories
 * will still be added to $parents, and if you only have $requiredcapability
 * in a child category, not the parent, then the child catgegory will still be
 * included.
 *
 * If you specify the option $excluded, then that category, and all its children,
 * are omitted from the tree. This is useful when you are doing something like
 * moving categories, where you do not want to allow people to move a category
 * to be the child of itself.
 *
 * This function is deprecated! For list of categories use
 * coursecat::make_all_categories($requiredcapability, $excludeid, $separator)
 * For parents of one particular category use
 * coursecat::get($id)->get_parents()
 *
 * @deprecated since 2.5
 *
 * @param array $list For output, accumulates an array categoryid => full category path name
 * @param array $parents For output, accumulates an array categoryid => list of parent category ids.
 * @param string/array $requiredcapability if given, only categories where the current
 *      user has this capability will be added to $list. Can also be an array of capabilities,
 *      in which case they are all required.
 * @param integer $excludeid Omit this category and its children from the lists built.
 * @param object $category Not used
 * @param string $path Not used
 */
function make_categories_list(&$list, &$parents, $requiredcapability = '',
        $excludeid = 0, $category = NULL, $path = "") {
    global $CFG, $DB;
    require_once($CFG->libdir.'/coursecatlib.php');

    debugging('Global function make_categories_list() is deprecated. Please use '.
            'coursecat::make_categories_list() and coursecat::get_parents()',
            DEBUG_DEVELOPER);

    // For categories list use just this one function:
    if (empty($list)) {
        $list = array();
    }
    $list += coursecat::make_categories_list($requiredcapability, $excludeid);

    // Building the list of all parents of all categories in the system is highly undesirable and hardly ever needed.
    // Usually user needs only parents for one particular category, in which case should be used:
    // coursecat::get($categoryid)->get_parents()
    if (empty($parents)) {
        $parents = array();
    }
    $all = $DB->get_records_sql('SELECT id, parent FROM {course_categories} ORDER BY sortorder');
    foreach ($all as $record) {
        if ($record->parent) {
            $parents[$record->id] = array_merge($parents[$record->parent], array($record->parent));
        } else {
            $parents[$record->id] = array();
        }
    }
}

/**
 * Delete category, but move contents to another category.
 *
 * This function is deprecated. Please use
 * coursecat::get($category->id)->delete_move($newparentid, $showfeedback);
 *
 * @see coursecat::delete_move()
 * @deprecated since 2.5
 *
 * @param object $category
 * @param int $newparentid category id
 * @return bool status
 */
function category_delete_move($category, $newparentid, $showfeedback=true) {
    global $CFG;
    require_once($CFG->libdir.'/coursecatlib.php');

    debugging('Function category_delete_move() is deprecated. Please use coursecat::delete_move() instead.');

    return coursecat::get($category->id)->delete_move($newparentid, $showfeedback);
}

/**
 * Recursively delete category including all subcategories and courses.
 *
 * This function is deprecated. Please use
 * coursecat::get($category->id)->delete_full($showfeedback);
 *
 * @see coursecat::delete_full()
 * @deprecated since 2.5
 *
 * @param stdClass $category
 * @param boolean $showfeedback display some notices
 * @return array return deleted courses
 */
function category_delete_full($category, $showfeedback=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/coursecatlib.php');

    debugging('Function category_delete_full() is deprecated. Please use coursecat::delete_full() instead.');

    return coursecat::get($category->id)->delete_full($showfeedback);
}

/**
 * Efficiently moves a category - NOTE that this can have
 * a huge impact access-control-wise...
 *
 * This function is deprecated. Please use
 * $coursecat = coursecat::get($category->id);
 * if ($coursecat->can_change_parent($newparentcat->id)) {
 *     $coursecat->change_parent($newparentcat->id);
 * }
 *
 * Alternatively you can use
 * $coursecat->update(array('parent' => $newparentcat->id));
 *
 * Function update() also updates field course_categories.timemodified
 *
 * @see coursecat::change_parent()
 * @see coursecat::update()
 * @deprecated since 2.5
 *
 * @param stdClass|coursecat $category
 * @param stdClass|coursecat $newparentcat
 */
function move_category($category, $newparentcat) {
    global $CFG;
    require_once($CFG->libdir.'/coursecatlib.php');

    debugging('Function move_category() is deprecated. Please use coursecat::change_parent() instead.');

    return coursecat::get($category->id)->change_parent($newparentcat->id);
}

/**
 * Hide course category and child course and subcategories
 *
 * This function is deprecated. Please use
 * coursecat::get($category->id)->hide();
 *
 * @see coursecat::hide()
 * @deprecated since 2.5
 *
 * @param stdClass $category
 * @return void
 */
function course_category_hide($category) {
    global $CFG;
    require_once($CFG->libdir.'/coursecatlib.php');

    debugging('Function course_category_hide() is deprecated. Please use coursecat::hide() instead.');

    coursecat::get($category->id)->hide();
}

/**
 * Show course category and child course and subcategories
 *
 * This function is deprecated. Please use
 * coursecat::get($category->id)->show();
 *
 * @see coursecat::show()
 * @deprecated since 2.5
 *
 * @param stdClass $category
 * @return void
 */
function course_category_show($category) {
    global $CFG;
    require_once($CFG->libdir.'/coursecatlib.php');

    debugging('Function course_category_show() is deprecated. Please use coursecat::show() instead.');

    coursecat::get($category->id)->show();
}

/**
 * Return specified category, default if given does not exist
 *
 * This function is deprecated.
 * To get the category with the specified it please use:
 * coursecat::get($catid, IGNORE_MISSING);
 * or
 * coursecat::get($catid, MUST_EXIST);
 *
 * To get the first available category please use
 * coursecat::get_default();
 *
 * class coursecat will also make sure that at least one category exists in DB
 *
 * @deprecated since 2.5
 * @see coursecat::get()
 * @see coursecat::get_default()
 *
 * @param int $catid course category id
 * @return object caregory
 */
function get_course_category($catid=0) {
    global $DB;

    debugging('Function get_course_category() is deprecated. Please use coursecat::get(), see phpdocs for more details');

    $category = false;

    if (!empty($catid)) {
        $category = $DB->get_record('course_categories', array('id'=>$catid));
    }

    if (!$category) {
        // the first category is considered default for now
        if ($category = $DB->get_records('course_categories', null, 'sortorder', '*', 0, 1)) {
            $category = reset($category);

        } else {
            $cat = new stdClass();
            $cat->name         = get_string('miscellaneous');
            $cat->depth        = 1;
            $cat->sortorder    = MAX_COURSES_IN_CATEGORY;
            $cat->timemodified = time();
            $catid = $DB->insert_record('course_categories', $cat);
            // make sure category context exists
            context_coursecat::instance($catid);
            mark_context_dirty('/'.SYSCONTEXTID);
            fix_course_sortorder(); // Required to build course_categories.depth and .path.
            $category = $DB->get_record('course_categories', array('id'=>$catid));
        }
    }

    return $category;
}

/**
 * Create a new course category and marks the context as dirty
 *
 * This function does not set the sortorder for the new category and
 * {@link fix_course_sortorder()} should be called after creating a new course
 * category
 *
 * Please note that this function does not verify access control.
 *
 * This function is deprecated. It is replaced with the method create() in class coursecat.
 * {@link coursecat::create()} also verifies the data, fixes sortorder and logs the action
 *
 * @deprecated since 2.5
 *
 * @param object $category All of the data required for an entry in the course_categories table
 * @return object new course category
 */
function create_course_category($category) {
    global $DB;

    debugging('Function create_course_category() is deprecated. Please use coursecat::create(), see phpdocs for more details', DEBUG_DEVELOPER);

    $category->timemodified = time();
    $category->id = $DB->insert_record('course_categories', $category);
    $category = $DB->get_record('course_categories', array('id' => $category->id));

    // We should mark the context as dirty
    $category->context = context_coursecat::instance($category->id);
    $category->context->mark_dirty();

    return $category;
}

/**
 * Returns an array of category ids of all the subcategories for a given
 * category.
 *
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
 *
 * @global object
 * @param int $catid - The id of the category whose subcategories we want to find.
 * @return array of category ids.
 */
function get_all_subcategories($catid) {
    global $DB;

    debugging('Function get_all_subcategories() is deprecated. Please use appropriate methods() of coursecat class. See phpdocs for more details',
            DEBUG_DEVELOPER);

    $subcats = array();

    if ($categories = $DB->get_records('course_categories', array('parent' => $catid))) {
        foreach ($categories as $cat) {
            array_push($subcats, $cat->id);
            $subcats = array_merge($subcats, get_all_subcategories($cat->id));
        }
    }
    return $subcats;
}

/**
 * Gets the child categories of a given courses category
 *
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
 *
 * @param int $parentid the id of a course category.
 * @return array all the child course categories.
 */
function get_child_categories($parentid) {
    global $DB;
    debugging('Function get_child_categories() is deprecated. Use coursecat::get_children() or see phpdocs for more details.',
            DEBUG_DEVELOPER);

    $rv = array();
    $sql = context_helper::get_preload_record_columns_sql('ctx');
    $records = $DB->get_records_sql("SELECT c.*, $sql FROM {course_categories} c ".
            "JOIN {context} ctx on ctx.instanceid = c.id AND ctx.contextlevel = ? WHERE c.parent = ? ORDER BY c.sortorder",
            array(CONTEXT_COURSECAT, $parentid));
    foreach ($records as $category) {
        context_helper::preload_from_record($category);
        if (!$category->visible && !has_capability('moodle/category:viewhiddencategories', context_coursecat::instance($category->id))) {
            continue;
        }
        $rv[] = $category;
    }
    return $rv;
}

/**
 * Returns a sorted list of categories.
 *
 * When asking for $parent='none' it will return all the categories, regardless
 * of depth. Wheen asking for a specific parent, the default is to return
 * a "shallow" resultset. Pass false to $shallow and it will return all
 * the child categories as well.
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
 *
 * The code of this deprecated function is left as it is because coursecat::get_children()
 * returns categories as instances of coursecat and not stdClass. Also there is no
 * substitute for retrieving the category with all it's subcategories. Plugin developers
 * may re-use the code/queries from this function in their plugins if really necessary.
 *
 * @param string $parent The parent category if any
 * @param string $sort the sortorder
 * @param bool   $shallow - set to false to get the children too
 * @return array of categories
 */
function get_categories($parent='none', $sort=NULL, $shallow=true) {
    global $DB;

    debugging('Function get_categories() is deprecated. Please use coursecat::get_children() or see phpdocs for other alternatives',
            DEBUG_DEVELOPER);

    if ($sort === NULL) {
        $sort = 'ORDER BY cc.sortorder ASC';
    } elseif ($sort ==='') {
        // leave it as empty
    } else {
        $sort = "ORDER BY $sort";
    }

    list($ccselect, $ccjoin) = context_instance_preload_sql('cc.id', CONTEXT_COURSECAT, 'ctx');

    if ($parent === 'none') {
        $sql = "SELECT cc.* $ccselect
                  FROM {course_categories} cc
               $ccjoin
                $sort";
        $params = array();

    } elseif ($shallow) {
        $sql = "SELECT cc.* $ccselect
                  FROM {course_categories} cc
               $ccjoin
                 WHERE cc.parent=?
                $sort";
        $params = array($parent);

    } else {
        $sql = "SELECT cc.* $ccselect
                  FROM {course_categories} cc
               $ccjoin
                  JOIN {course_categories} ccp
                       ON ((cc.parent = ccp.id) OR (cc.path LIKE ".$DB->sql_concat('ccp.path',"'/%'")."))
                 WHERE ccp.id=?
                $sort";
        $params = array($parent);
    }
    $categories = array();

    $rs = $DB->get_recordset_sql($sql, $params);
    foreach($rs as $cat) {
        context_helper::preload_from_record($cat);
        $catcontext = context_coursecat::instance($cat->id);
        if ($cat->visible || has_capability('moodle/category:viewhiddencategories', $catcontext)) {
            $categories[$cat->id] = $cat;
        }
    }
    $rs->close();
    return $categories;
}

/**
* Displays a course search form
*
* This function is deprecated, please use course renderer:
* $renderer = $PAGE->get_renderer('core', 'course');
* echo $renderer->course_search_form($value, $format);
*
* @deprecated since 2.5
*
* @param string $value default value to populate the search field
* @param bool $return if true returns the value, if false - outputs
* @param string $format display format - 'plain' (default), 'short' or 'navbar'
* @return null|string
*/
function print_course_search($value="", $return=false, $format="plain") {
    global $PAGE;
    debugging('Function print_course_search() is deprecated, please use course renderer', DEBUG_DEVELOPER);
    $renderer = $PAGE->get_renderer('core', 'course');
    if ($return) {
        return $renderer->course_search_form($value, $format);
    } else {
        echo $renderer->course_search_form($value, $format);
    }
}

/**
 * Prints custom user information on the home page
 *
 * This function is deprecated, please use:
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->frontpage_my_courses()
 *
 * @deprecated since 2.5
 */
function print_my_moodle() {
    global $PAGE;
    debugging('Function print_my_moodle() is deprecated, please use course renderer function frontpage_my_courses()', DEBUG_DEVELOPER);

    $renderer = $PAGE->get_renderer('core', 'course');
    echo $renderer->frontpage_my_courses();
}

/**
 * Prints information about one remote course
 *
 * This function is deprecated, it is replaced with protected function
 * {@link core_course_renderer::frontpage_remote_course()}
 * It is only used from function {@link core_course_renderer::frontpage_my_courses()}
 *
 * @deprecated since 2.5
 */
function print_remote_course($course, $width="100%") {
    global $CFG, $USER;
    debugging('Function print_remote_course() is deprecated, please use course renderer', DEBUG_DEVELOPER);

    $linkcss = '';

    $url = "{$CFG->wwwroot}/auth/mnet/jump.php?hostid={$course->hostid}&amp;wantsurl=/course/view.php?id={$course->remoteid}";

    echo '<div class="coursebox remotecoursebox clearfix">';
    echo '<div class="info">';
    echo '<div class="name"><a title="'.get_string('entercourse').'"'.
         $linkcss.' href="'.$url.'">'
        .  format_string($course->fullname) .'</a><br />'
        . format_string($course->hostname) . ' : '
        . format_string($course->cat_name) . ' : '
        . format_string($course->shortname). '</div>';
    echo '</div><div class="summary">';
    $options = new stdClass();
    $options->noclean = true;
    $options->para = false;
    $options->overflowdiv = true;
    echo format_text($course->summary, $course->summaryformat, $options);
    echo '</div>';
    echo '</div>';
}

/**
 * Prints information about one remote host
 *
 * This function is deprecated, it is replaced with protected function
 * {@link core_course_renderer::frontpage_remote_host()}
 * It is only used from function {@link core_course_renderer::frontpage_my_courses()}
 *
 * @deprecated since 2.5
 */
function print_remote_host($host, $width="100%") {
    global $OUTPUT;
    debugging('Function print_remote_host() is deprecated, please use course renderer', DEBUG_DEVELOPER);

    $linkcss = '';

    echo '<div class="coursebox clearfix">';
    echo '<div class="info">';
    echo '<div class="name">';
    echo '<img src="'.$OUTPUT->pix_url('i/mnethost') . '" class="icon" alt="'.get_string('course').'" />';
    echo '<a title="'.s($host['name']).'" href="'.s($host['url']).'">'
        . s($host['name']).'</a> - ';
    echo $host['count'] . ' ' . get_string('courses');
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

/**
 * Recursive function to print out all the categories in a nice format
 * with or without courses included
 *
 * @deprecated since 2.5
 *
 * See http://docs.moodle.org/dev/Courses_lists_upgrade_to_2.5
 */
function print_whole_category_list($category=NULL, $displaylist=NULL, $parentslist=NULL, $depth=-1, $showcourses = true, $categorycourses=NULL) {
    global $PAGE;
    debugging('Function print_whole_category_list() is deprecated, please use course renderer', DEBUG_DEVELOPER);

    $renderer = $PAGE->get_renderer('core', 'course');
    if ($showcourses && $category) {
        echo $renderer->course_category($category);
    } else if ($showcourses) {
        echo $renderer->frontpage_combo_list();
    } else {
        echo $renderer->frontpage_categories_list();
    }
}

/**
 * Prints the category information.
 *
 * @deprecated since 2.5
 *
 * This function was only used by {@link print_whole_category_list()} but now
 * all course category rendering is moved to core_course_renderer.
 *
 * @param stdClass $category
 * @param int $depth The depth of the category.
 * @param bool $showcourses If set to true course information will also be printed.
 * @param array|null $courses An array of courses belonging to the category, or null if you don't have it yet.
 */
function print_category_info($category, $depth = 0, $showcourses = false, array $courses = null) {
    global $PAGE;
    debugging('Function print_category_info() is deprecated, please use course renderer', DEBUG_DEVELOPER);

    $renderer = $PAGE->get_renderer('core', 'course');
    echo $renderer->course_category($category);
}

/**
 * This function generates a structured array of courses and categories.
 *
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
 *
 * @param int $id
 * @param int $depth
 */
function get_course_category_tree($id = 0, $depth = 0) {
    global $DB, $CFG;
    if (!$depth) {
        debugging('Function get_course_category_tree() is deprecated, please use course renderer or coursecat class, see function phpdocs for more info', DEBUG_DEVELOPER);
    }

    $categories = array();
    $categoryids = array();
    $sql = context_helper::get_preload_record_columns_sql('ctx');
    $records = $DB->get_records_sql("SELECT c.*, $sql FROM {course_categories} c ".
            "JOIN {context} ctx on ctx.instanceid = c.id AND ctx.contextlevel = ? WHERE c.parent = ? ORDER BY c.sortorder",
            array(CONTEXT_COURSECAT, $id));
    foreach ($records as $category) {
        context_helper::preload_from_record($category);
        if (!$category->visible && !has_capability('moodle/category:viewhiddencategories', context_coursecat::instance($category->id))) {
            continue;
        }
        $categories[] = $category;
        $categoryids[$category->id] = $category;
        if (empty($CFG->maxcategorydepth) || $depth <= $CFG->maxcategorydepth) {
            list($category->categories, $subcategories) = get_course_category_tree($category->id, $depth+1);
            foreach ($subcategories as $subid=>$subcat) {
                $categoryids[$subid] = $subcat;
            }
            $category->courses = array();
        }
    }

    if ($depth > 0) {
        // This is a recursive call so return the required array
        return array($categories, $categoryids);
    }

    if (empty($categoryids)) {
        // No categories available (probably all hidden).
        return array();
    }

    // The depth is 0 this function has just been called so we can finish it off

    list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    list($catsql, $catparams) = $DB->get_in_or_equal(array_keys($categoryids));
    $sql = "SELECT
            c.id,c.sortorder,c.visible,c.fullname,c.shortname,c.summary,c.category
            $ccselect
            FROM {course} c
            $ccjoin
            WHERE c.category $catsql ORDER BY c.sortorder ASC";
    if ($courses = $DB->get_records_sql($sql, $catparams)) {
        // loop throught them
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            context_helper::preload_from_record($course);
            if (!empty($course->visible) || has_capability('moodle/course:viewhiddencourses', context_course::instance($course->id))) {
                $categoryids[$course->category]->courses[$course->id] = $course;
            }
        }
    }
    return $categories;
}

/**
 * Print courses in category. If category is 0 then all courses are printed.
 *
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
 *
 * @param int|stdClass $category category object or id.
 * @return bool true if courses found and printed, else false.
 */
function print_courses($category) {
    global $CFG, $OUTPUT, $PAGE;
    require_once($CFG->libdir. '/coursecatlib.php');
    debugging('Function print_courses() is deprecated, please use course renderer', DEBUG_DEVELOPER);

    if (!is_object($category) && $category==0) {
        $courses = coursecat::get(0)->get_courses(array('recursive' => true, 'summary' => true, 'coursecontacts' => true));
    } else {
        $courses = coursecat::get($category->id)->get_courses(array('summary' => true, 'coursecontacts' => true));
    }

    if ($courses) {
        $renderer = $PAGE->get_renderer('core', 'course');
        echo $renderer->courses_list($courses);
    } else {
        echo $OUTPUT->heading(get_string("nocoursesyet"));
        $context = context_system::instance();
        if (has_capability('moodle/course:create', $context)) {
            $options = array();
            if (!empty($category->id)) {
                $options['category'] = $category->id;
            } else {
                $options['category'] = $CFG->defaultrequestcategory;
            }
            echo html_writer::start_tag('div', array('class'=>'addcoursebutton'));
            echo $OUTPUT->single_button(new moodle_url('/course/edit.php', $options), get_string("addnewcourse"));
            echo html_writer::end_tag('div');
            return false;
        }
    }
    return true;
}

/**
 * Print a description of a course, suitable for browsing in a list.
 *
 * @deprecated since 2.5
 *
 * Please use course renderer to display a course information box.
 * $renderer = $PAGE->get_renderer('core', 'course');
 * echo $renderer->courses_list($courses); // will print list of courses
 * echo $renderer->course_info_box($course); // will print one course wrapped in div.generalbox
 *
 * @param object $course the course object.
 * @param string $highlightterms Ignored in this deprecated function!
 */
function print_course($course, $highlightterms = '') {
    global $PAGE;

    debugging('Function print_course() is deprecated, please use course renderer', DEBUG_DEVELOPER);
    $renderer = $PAGE->get_renderer('core', 'course');
    // Please note, correct would be to use $renderer->coursecat_coursebox() but this function is protected.
    // To print list of courses use $renderer->courses_list();
    echo $renderer->course_info_box($course);
}

/**
 * Gets an array whose keys are category ids and whose values are arrays of courses in the corresponding category.
 *
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
 *
 * @param int $categoryid
 * @return array
 */
function get_category_courses_array($categoryid = 0) {
    debugging('Function get_category_courses_array() is deprecated, please use methods of coursecat class', DEBUG_DEVELOPER);
    $tree = get_course_category_tree($categoryid);
    $flattened = array();
    foreach ($tree as $category) {
        get_category_courses_array_recursively($flattened, $category);
    }
    return $flattened;
}

/**
 * Recursive function to help flatten the course category tree.
 *
 * @deprecated since 2.5
 *
 * Was intended to be called from {@link get_category_courses_array()}
 *
 * @param array &$flattened An array passed by reference in which to store courses for each category.
 * @param stdClass $category The category to get courses for.
 */
function get_category_courses_array_recursively(array &$flattened, $category) {
    debugging('Function get_category_courses_array_recursively() is deprecated, please use methods of coursecat class', DEBUG_DEVELOPER);
    $flattened[$category->id] = $category->courses;
    foreach ($category->categories as $childcategory) {
        get_category_courses_array_recursively($flattened, $childcategory);
    }
}

/**
 * Returns a URL based on the context of the current page.
 * This URL points to blog/index.php and includes filter parameters appropriate for the current page.
 *
 * @param stdclass $context
 * @deprecated since Moodle 2.5 MDL-27814 - please do not use this function any more.
 * @todo Remove this in 2.7
 * @return string
 */
function blog_get_context_url($context=null) {
    global $CFG;

    debugging('Function  blog_get_context_url() is deprecated, getting params from context is not reliable for blogs.', DEBUG_DEVELOPER);
    $viewblogentriesurl = new moodle_url('/blog/index.php');

    if (empty($context)) {
        global $PAGE;
        $context = $PAGE->context;
    }

    // Change contextlevel to SYSTEM if viewing the site course
    if ($context->contextlevel == CONTEXT_COURSE && $context->instanceid == SITEID) {
        $context = context_system::instance();
    }

    $filterparam = '';
    $strlevel = '';

    switch ($context->contextlevel) {
        case CONTEXT_SYSTEM:
        case CONTEXT_BLOCK:
        case CONTEXT_COURSECAT:
            break;
        case CONTEXT_COURSE:
            $filterparam = 'courseid';
            $strlevel = get_string('course');
            break;
        case CONTEXT_MODULE:
            $filterparam = 'modid';
            $strlevel = $context->get_context_name();
            break;
        case CONTEXT_USER:
            $filterparam = 'userid';
            $strlevel = get_string('user');
            break;
    }

    if (!empty($filterparam)) {
        $viewblogentriesurl->param($filterparam, $context->instanceid);
    }

    return $viewblogentriesurl;
}

/**
 * Retrieve course records with the course managers and other related records
 * that we need for print_course(). This allows print_courses() to do its job
 * in a constant number of DB queries, regardless of the number of courses,
 * role assignments, etc.
 *
 * The returned array is indexed on c.id, and each course will have
 * - $course->managers - array containing RA objects that include a $user obj
 *                       with the minimal fields needed for fullname()
 *
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
 *
 * Please note that code of this function is not changed to use coursecat class because
 * coursecat::get_courses() returns result in slightly different format. Also note that
 * get_courses_wmanagers() DOES NOT check that users are enrolled in the course and
 * coursecat::get_courses() does.
 *
 * @global object
 * @global object
 * @global object
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_SYSTEM
 * @uses CONTEXT_COURSECAT
 * @uses SITEID
 * @param int|string $categoryid Either the categoryid for the courses or 'all'
 * @param string $sort A SQL sort field and direction
 * @param array $fields An array of additional fields to fetch
 * @return array
 */
function get_courses_wmanagers($categoryid=0, $sort="c.sortorder ASC", $fields=array()) {
    /*
     * The plan is to
     *
     * - Grab the courses JOINed w/context
     *
     * - Grab the interesting course-manager RAs
     *   JOINed with a base user obj and add them to each course
     *
     * So as to do all the work in 2 DB queries. The RA+user JOIN
     * ends up being pretty expensive if it happens over _all_
     * courses on a large site. (Are we surprised!?)
     *
     * So this should _never_ get called with 'all' on a large site.
     *
     */
    global $USER, $CFG, $DB;
    debugging('Function get_courses_wmanagers() is deprecated, please use coursecat::get_courses()', DEBUG_DEVELOPER);

    $params = array();
    $allcats = false; // bool flag
    if ($categoryid === 'all') {
        $categoryclause   = '';
        $allcats = true;
    } elseif (is_numeric($categoryid)) {
        $categoryclause = "c.category = :catid";
        $params['catid'] = $categoryid;
    } else {
        debugging("Could not recognise categoryid = $categoryid");
        $categoryclause = '';
    }

    $basefields = array('id', 'category', 'sortorder',
                        'shortname', 'fullname', 'idnumber',
                        'startdate', 'visible',
                        'newsitems', 'groupmode', 'groupmodeforce');

    if (!is_null($fields) && is_string($fields)) {
        if (empty($fields)) {
            $fields = $basefields;
        } else {
            // turn the fields from a string to an array that
            // get_user_courses_bycap() will like...
            $fields = explode(',',$fields);
            $fields = array_map('trim', $fields);
            $fields = array_unique(array_merge($basefields, $fields));
        }
    } elseif (is_array($fields)) {
        $fields = array_merge($basefields,$fields);
    }
    $coursefields = 'c.' .join(',c.', $fields);

    if (empty($sort)) {
        $sortstatement = "";
    } else {
        $sortstatement = "ORDER BY $sort";
    }

    $where = 'WHERE c.id != ' . SITEID;
    if ($categoryclause !== ''){
        $where = "$where AND $categoryclause";
    }

    // pull out all courses matching the cat
    list($ccselect, $ccjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $sql = "SELECT $coursefields $ccselect
              FROM {course} c
           $ccjoin
               $where
               $sortstatement";

    $catpaths = array();
    $catpath  = NULL;
    if ($courses = $DB->get_records_sql($sql, $params)) {
        // loop on courses materialising
        // the context, and prepping data to fetch the
        // managers efficiently later...
        foreach ($courses as $k => $course) {
            context_helper::preload_from_record($course);
            $coursecontext = context_course::instance($course->id);
            $courses[$k] = $course;
            $courses[$k]->managers = array();
            if ($allcats === false) {
                // single cat, so take just the first one...
                if ($catpath === NULL) {
                    $catpath = preg_replace(':/\d+$:', '', $coursecontext->path);
                }
            } else {
                // chop off the contextid of the course itself
                // like dirname() does...
                $catpaths[] = preg_replace(':/\d+$:', '', $coursecontext->path);
            }
        }
    } else {
        return array(); // no courses!
    }

    $CFG->coursecontact = trim($CFG->coursecontact);
    if (empty($CFG->coursecontact)) {
        return $courses;
    }

    $managerroles = explode(',', $CFG->coursecontact);
    $catctxids = '';
    if (count($managerroles)) {
        if ($allcats === true) {
            $catpaths  = array_unique($catpaths);
            $ctxids = array();
            foreach ($catpaths as $cpath) {
                $ctxids = array_merge($ctxids, explode('/',substr($cpath,1)));
            }
            $ctxids = array_unique($ctxids);
            $catctxids = implode( ',' , $ctxids);
            unset($catpaths);
            unset($cpath);
        } else {
            // take the ctx path from the first course
            // as all categories will be the same...
            $catpath = substr($catpath,1);
            $catpath = preg_replace(':/\d+$:','',$catpath);
            $catctxids = str_replace('/',',',$catpath);
        }
        if ($categoryclause !== '') {
            $categoryclause = "AND $categoryclause";
        }
        /*
         * Note: Here we use a LEFT OUTER JOIN that can
         * "optionally" match to avoid passing a ton of context
         * ids in an IN() clause. Perhaps a subselect is faster.
         *
         * In any case, this SQL is not-so-nice over large sets of
         * courses with no $categoryclause.
         *
         */
        $sql = "SELECT ctx.path, ctx.instanceid, ctx.contextlevel,
                       r.id AS roleid, r.name AS rolename, r.shortname AS roleshortname,
                       rn.name AS rolecoursealias, u.id AS userid, u.firstname, u.lastname
                  FROM {role_assignments} ra
                  JOIN {context} ctx ON ra.contextid = ctx.id
                  JOIN {user} u ON ra.userid = u.id
                  JOIN {role} r ON ra.roleid = r.id
             LEFT JOIN {role_names} rn ON (rn.contextid = ctx.id AND rn.roleid = r.id)
                  LEFT OUTER JOIN {course} c
                       ON (ctx.instanceid=c.id AND ctx.contextlevel=".CONTEXT_COURSE.")
                WHERE ( c.id IS NOT NULL";
        // under certain conditions, $catctxids is NULL
        if($catctxids == NULL){
            $sql .= ") ";
        }else{
            $sql .= " OR ra.contextid  IN ($catctxids) )";
        }

        $sql .= "AND ra.roleid IN ({$CFG->coursecontact})
                      $categoryclause
                ORDER BY r.sortorder ASC, ctx.contextlevel ASC, ra.sortorder ASC";
        $rs = $DB->get_recordset_sql($sql, $params);

        // This loop is fairly stupid as it stands - might get better
        // results doing an initial pass clustering RAs by path.
        foreach($rs as $ra) {
            $user = new stdClass;
            $user->id        = $ra->userid;    unset($ra->userid);
            $user->firstname = $ra->firstname; unset($ra->firstname);
            $user->lastname  = $ra->lastname;  unset($ra->lastname);
            $ra->user = $user;
            if ($ra->contextlevel == CONTEXT_SYSTEM) {
                foreach ($courses as $k => $course) {
                    $courses[$k]->managers[] = $ra;
                }
            } else if ($ra->contextlevel == CONTEXT_COURSECAT) {
                if ($allcats === false) {
                    // It always applies
                    foreach ($courses as $k => $course) {
                        $courses[$k]->managers[] = $ra;
                    }
                } else {
                    foreach ($courses as $k => $course) {
                        $coursecontext = context_course::instance($course->id);
                        // Note that strpos() returns 0 as "matched at pos 0"
                        if (strpos($coursecontext->path, $ra->path.'/') === 0) {
                            // Only add it to subpaths
                            $courses[$k]->managers[] = $ra;
                        }
                    }
                }
            } else { // course-level
                if (!array_key_exists($ra->instanceid, $courses)) {
                    //this course is not in a list, probably a frontpage course
                    continue;
                }
                $courses[$ra->instanceid]->managers[] = $ra;
            }
        }
        $rs->close();
    }

    return $courses;
}

/**
 * Converts a nested array tree into HTML ul:li [recursive]
 *
 * @deprecated since 2.5
 *
 * @param array $tree A tree array to convert
 * @param int $row Used in identifying the iteration level and in ul classes
 * @return string HTML structure
 */
function convert_tree_to_html($tree, $row=0) {
    debugging('Function convert_tree_to_html() is deprecated since Moodle 2.5. Consider using class tabtree and core_renderer::render_tabtree()', DEBUG_DEVELOPER);

    $str = "\n".'<ul class="tabrow'.$row.'">'."\n";

    $first = true;
    $count = count($tree);

    foreach ($tree as $tab) {
        $count--;   // countdown to zero

        $liclass = '';

        if ($first && ($count == 0)) {   // Just one in the row
            $liclass = 'first last';
            $first = false;
        } else if ($first) {
            $liclass = 'first';
            $first = false;
        } else if ($count == 0) {
            $liclass = 'last';
        }

        if ((empty($tab->subtree)) && (!empty($tab->selected))) {
            $liclass .= (empty($liclass)) ? 'onerow' : ' onerow';
        }

        if ($tab->inactive || $tab->active || $tab->selected) {
            if ($tab->selected) {
                $liclass .= (empty($liclass)) ? 'here selected' : ' here selected';
            } else if ($tab->active) {
                $liclass .= (empty($liclass)) ? 'here active' : ' here active';
            }
        }

        $str .= (!empty($liclass)) ? '<li class="'.$liclass.'">' : '<li>';

        if ($tab->inactive || $tab->active || ($tab->selected && !$tab->linkedwhenselected)) {
            // The a tag is used for styling
            $str .= '<a class="nolink"><span>'.$tab->text.'</span></a>';
        } else {
            $str .= '<a href="'.$tab->link.'" title="'.$tab->title.'"><span>'.$tab->text.'</span></a>';
        }

        if (!empty($tab->subtree)) {
            $str .= convert_tree_to_html($tab->subtree, $row+1);
        } else if ($tab->selected) {
            $str .= '<div class="tabrow'.($row+1).' empty">&nbsp;</div>'."\n";
        }

        $str .= ' </li>'."\n";
    }
    $str .= '</ul>'."\n";

    return $str;
}

/**
 * Convert nested tabrows to a nested array
 *
 * @deprecated since 2.5
 *
 * @param array $tabrows A [nested] array of tab row objects
 * @param string $selected The tabrow to select (by id)
 * @param array $inactive An array of tabrow id's to make inactive
 * @param array $activated An array of tabrow id's to make active
 * @return array The nested array
 */
function convert_tabrows_to_tree($tabrows, $selected, $inactive, $activated) {

    debugging('Function convert_tabrows_to_tree() is deprecated since Moodle 2.5. Consider using class tabtree', DEBUG_DEVELOPER);

    // Work backwards through the rows (bottom to top) collecting the tree as we go.
    $tabrows = array_reverse($tabrows);

    $subtree = array();

    foreach ($tabrows as $row) {
        $tree = array();

        foreach ($row as $tab) {
            $tab->inactive = in_array((string)$tab->id, $inactive);
            $tab->active = in_array((string)$tab->id, $activated);
            $tab->selected = (string)$tab->id == $selected;

            if ($tab->active || $tab->selected) {
                if ($subtree) {
                    $tab->subtree = $subtree;
                }
            }
            $tree[] = $tab;
        }
        $subtree = $tree;
    }

    return $subtree;
}

/**
 * Can handle rotated text. Whether it is safe to use the trickery in textrotate.js.
 *
 * @deprecated since 2.5 - do not use, the textrotate.js will work it out automatically
 * @return bool True for yes, false for no
 */
function can_use_rotated_text() {
    debugging('can_use_rotated_text() is deprecated since Moodle 2.5. JS feature detection is used automatically.', DEBUG_DEVELOPER);
    return true;
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

/**
 * Get a context instance as an object, from a given context id.
 *
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
 * Recursive function which, given a context, find all parent context ids,
 * and return the array in reverse order, i.e. parent first, then grand
 * parent, etc.
 *
 * @see context::get_parent_context_ids()
 * @deprecated since 2.2, use $context->get_parent_context_ids() instead
 * @param context $context
 * @param bool $includeself optional, defaults to false
 * @return array
 */
function get_parent_contexts(context $context, $includeself = false) {
    debugging('get_parent_contexts() is deprecated, please use $context->get_parent_context_ids() instead.', DEBUG_DEVELOPER);
    return $context->get_parent_context_ids($includeself);
}

/**
 * Return the id of the parent of this context, or false if there is no parent (only happens if this
 * is the site context.)
 *
 * @deprecated since Moodle 2.2
 * @see context::get_parent_context()
 * @param context $context
 * @return integer the id of the parent context.
 */
function get_parent_contextid(context $context) {
    debugging('get_parent_contextid() is deprecated, please use $context->get_parent_context() instead.', DEBUG_DEVELOPER);

    if ($parent = $context->get_parent_context()) {
        return $parent->id;
    } else {
        return false;
    }
}

/**
 * Recursive function which, given a context, find all its children contexts.
 *
 * For course category contexts it will return immediate children only categories and courses.
 * It will NOT recurse into courses or child categories.
 * If you want to do that, call it on the returned courses/categories.
 *
 * When called for a course context, it will return the modules and blocks
 * displayed in the course page.
 *
 * If called on a user/course/module context it _will_ populate the cache with the appropriate
 * contexts ;-)
 *
 * @see context::get_child_contexts()
 * @deprecated since 2.2
 * @param context $context
 * @return array Array of child records
 */
function get_child_contexts(context $context) {
    debugging('get_child_contexts() is deprecated, please use $context->get_child_contexts() instead.', DEBUG_DEVELOPER);
    return $context->get_child_contexts();
}

/**
 * Precreates all contexts including all parents.
 *
 * @see context_helper::create_instances()
 * @deprecated since 2.2
 * @param int $contextlevel empty means all
 * @param bool $buildpaths update paths and depths
 * @return void
 */
function create_contexts($contextlevel = null, $buildpaths = true) {
    debugging('create_contexts() is deprecated, please use context_helper::create_instances() instead.', DEBUG_DEVELOPER);
    context_helper::create_instances($contextlevel, $buildpaths);
}

/**
 * Remove stale context records.
 *
 * @see context_helper::cleanup_instances()
 * @deprecated since 2.2
 * @return bool
 */
function cleanup_contexts() {
    debugging('cleanup_contexts() is deprecated, please use context_helper::cleanup_instances() instead.', DEBUG_DEVELOPER);
    context_helper::cleanup_instances();
    return true;
}

/**
 * Populate context.path and context.depth where missing.
 *
 * @see context_helper::build_all_paths()
 * @deprecated since 2.2
 * @param bool $force force a complete rebuild of the path and depth fields, defaults to false
 * @return void
 */
function build_context_path($force = false) {
    debugging('build_context_path() is deprecated, please use context_helper::build_all_paths() instead.', DEBUG_DEVELOPER);
    context_helper::build_all_paths($force);
}

/**
 * Rebuild all related context depth and path caches.
 *
 * @see context::reset_paths()
 * @deprecated since 2.2
 * @param array $fixcontexts array of contexts, strongtyped
 * @return void
 */
function rebuild_contexts(array $fixcontexts) {
    debugging('rebuild_contexts() is deprecated, please use $context->reset_paths(true) instead.', DEBUG_DEVELOPER);
    foreach ($fixcontexts as $fixcontext) {
        $fixcontext->reset_paths(false);
    }
    context_helper::build_all_paths(false);
}

/**
 * Preloads all contexts relating to a course: course, modules. Block contexts
 * are no longer loaded here. The contexts for all the blocks on the current
 * page are now efficiently loaded by {@link block_manager::load_blocks()}.
 *
 * @deprecated since Moodle 2.2
 * @see context_helper::preload_course()
 * @param int $courseid Course ID
 * @return void
 */
function preload_course_contexts($courseid) {
    debugging('preload_course_contexts() is deprecated, please use context_helper::preload_course() instead.', DEBUG_DEVELOPER);
    context_helper::preload_course($courseid);
}

/**
 * Update the path field of the context and all dep. subcontexts that follow
 *
 * Update the path field of the context and
 * all the dependent subcontexts that follow
 * the move.
 *
 * The most important thing here is to be as
 * DB efficient as possible. This op can have a
 * massive impact in the DB.
 *
 * @deprecated since Moodle 2.2
 * @see context::update_moved()
 * @param context $context context obj
 * @param context $newparent new parent obj
 * @return void
 */
function context_moved(context $context, context $newparent) {
    debugging('context_moved() is deprecated, please use context::update_moved() instead.', DEBUG_DEVELOPER);
    $context->update_moved($newparent);
}

/**
 * Extracts the relevant capabilities given a contextid.
 * All case based, example an instance of forum context.
 * Will fetch all forum related capabilities, while course contexts
 * Will fetch all capabilities
 *
 * capabilities
 * `name` varchar(150) NOT NULL,
 * `captype` varchar(50) NOT NULL,
 * `contextlevel` int(10) NOT NULL,
 * `component` varchar(100) NOT NULL,
 *
 * @see context::get_capabilities()
 * @deprecated since 2.2
 * @param context $context
 * @return array
 */
function fetch_context_capabilities(context $context) {
    debugging('fetch_context_capabilities() is deprecated, please use $context->get_capabilities() instead.', DEBUG_DEVELOPER);
    return $context->get_capabilities();
}

/**
 * Preloads context information from db record and strips the cached info.
 * The db request has to contain both the $join and $select from context_instance_preload_sql()
 *
 * @deprecated since 2.2
 * @see context_helper::preload_from_record()
 * @param stdClass $rec
 * @return void (modifies $rec)
 */
function context_instance_preload(stdClass $rec) {
    debugging('context_instance_preload() is deprecated, please use context_helper::preload_from_record() instead.', DEBUG_DEVELOPER);
    context_helper::preload_from_record($rec);
}

/**
 * Returns context level name
 *
 * @deprecated since 2.2
 * @see context_helper::get_level_name()
 * @param integer $contextlevel $context->context level. One of the CONTEXT_... constants.
 * @return string the name for this type of context.
 */
function get_contextlevel_name($contextlevel) {
    debugging('get_contextlevel_name() is deprecated, please use context_helper::get_level_name() instead.', DEBUG_DEVELOPER);
    return context_helper::get_level_name($contextlevel);
}

/**
 * Prints human readable context identifier.
 *
 * @deprecated since 2.2
 * @see context::get_context_name()
 * @param context $context the context.
 * @param boolean $withprefix whether to prefix the name of the context with the
 *      type of context, e.g. User, Course, Forum, etc.
 * @param boolean $short whether to user the short name of the thing. Only applies
 *      to course contexts
 * @return string the human readable context name.
 */
function print_context_name(context $context, $withprefix = true, $short = false) {
    debugging('print_context_name() is deprecated, please use $context->get_context_name() instead.', DEBUG_DEVELOPER);
    return $context->get_context_name($withprefix, $short);
}

/**
 * Mark a context as dirty (with timestamp) so as to force reloading of the context.
 *
 * @deprecated since 2.2, use $context->mark_dirty() instead
 * @see context::mark_dirty()
 * @param string $path context path
 */
function mark_context_dirty($path) {
    global $CFG, $USER, $ACCESSLIB_PRIVATE;
    debugging('mark_context_dirty() is deprecated, please use $context->mark_dirty() instead.', DEBUG_DEVELOPER);

    if (during_initial_install()) {
        return;
    }

    // only if it is a non-empty string
    if (is_string($path) && $path !== '') {
        set_cache_flag('accesslib/dirtycontexts', $path, 1, time()+$CFG->sessiontimeout);
        if (isset($ACCESSLIB_PRIVATE->dirtycontexts)) {
            $ACCESSLIB_PRIVATE->dirtycontexts[$path] = 1;
        } else {
            if (CLI_SCRIPT) {
                $ACCESSLIB_PRIVATE->dirtycontexts = array($path => 1);
            } else {
                if (isset($USER->access['time'])) {
                    $ACCESSLIB_PRIVATE->dirtycontexts = get_cache_flags('accesslib/dirtycontexts', $USER->access['time']-2);
                } else {
                    $ACCESSLIB_PRIVATE->dirtycontexts = array($path => 1);
                }
                // flags not loaded yet, it will be done later in $context->reload_if_dirty()
            }
        }
    }
}

/**
 * Remove a context record and any dependent entries,
 * removes context from static context cache too
 *
 * @deprecated since Moodle 2.2
 * @see context_helper::delete_instance() or context::delete_content()
 * @param int $contextlevel
 * @param int $instanceid
 * @param bool $deleterecord false means keep record for now
 * @return bool returns true or throws an exception
 */
function delete_context($contextlevel, $instanceid, $deleterecord = true) {
    if ($deleterecord) {
        debugging('delete_context() is deprecated, please use context_helper::delete_instance() instead.', DEBUG_DEVELOPER);
        context_helper::delete_instance($contextlevel, $instanceid);
    } else {
        debugging('delete_context() is deprecated, please use $context->delete_content() instead.', DEBUG_DEVELOPER);
        $classname = context_helper::get_class_for_level($contextlevel);
        if ($context = $classname::instance($instanceid, IGNORE_MISSING)) {
            $context->delete_content();
        }
    }

    return true;
}

/**
 * Get a URL for a context, if there is a natural one. For example, for
 * CONTEXT_COURSE, this is the course page. For CONTEXT_USER it is the
 * user profile page.
 *
 * @deprecated since 2.2
 * @see context::get_url()
 * @param context $context the context
 * @return moodle_url
 */
function get_context_url(context $context) {
    debugging('get_context_url() is deprecated, please use $context->get_url() instead.', DEBUG_DEVELOPER);
    return $context->get_url();
}

/**
 * Is this context part of any course? if yes return course context,
 * if not return null or throw exception.
 *
 * @deprecated since 2.2
 * @see context::get_course_context()
 * @param context $context
 * @return context_course context of the enclosing course, null if not found or exception
 */
function get_course_context(context $context) {
    debugging('get_course_context() is deprecated, please use $context->get_course_context(true) instead.', DEBUG_DEVELOPER);
    return $context->get_course_context(true);
}

/**
 * Get an array of courses where cap requested is available
 * and user is enrolled, this can be relatively slow.
 *
 * @deprecated since 2.2
 * @see enrol_get_users_courses()
 * @param int    $userid A user id. By default (null) checks the permissions of the current user.
 * @param string $cap - name of the capability
 * @param array  $accessdata_ignored
 * @param bool   $doanything_ignored
 * @param string $sort - sorting fields - prefix each fieldname with "c."
 * @param array  $fields - additional fields you are interested in...
 * @param int    $limit_ignored
 * @return array $courses - ordered array of course objects - see notes above
 */
function get_user_courses_bycap($userid, $cap, $accessdata_ignored, $doanything_ignored, $sort = 'c.sortorder ASC', $fields = null, $limit_ignored = 0) {

    debugging('get_user_courses_bycap() is deprecated, please use enrol_get_users_courses() instead.', DEBUG_DEVELOPER);
    $courses = enrol_get_users_courses($userid, true, $fields, $sort);
    foreach ($courses as $id=>$course) {
        $context = context_course::instance($id);
        if (!has_capability($cap, $context, $userid)) {
            unset($courses[$id]);
        }
    }

    return $courses;
}

/**
 * This is really slow!!! do not use above course context level
 *
 * @deprecated since Moodle 2.2
 * @param int $roleid
 * @param context $context
 * @return array
 */
function get_role_context_caps($roleid, context $context) {
    global $DB;
    debugging('get_role_context_caps() is deprecated, it is really slow. Don\'t use it.', DEBUG_DEVELOPER);

    // This is really slow!!!! - do not use above course context level.
    $result = array();
    $result[$context->id] = array();

    // First emulate the parent context capabilities merging into context.
    $searchcontexts = array_reverse($context->get_parent_context_ids(true));
    foreach ($searchcontexts as $cid) {
        if ($capabilities = $DB->get_records('role_capabilities', array('roleid'=>$roleid, 'contextid'=>$cid))) {
            foreach ($capabilities as $cap) {
                if (!array_key_exists($cap->capability, $result[$context->id])) {
                    $result[$context->id][$cap->capability] = 0;
                }
                $result[$context->id][$cap->capability] += $cap->permission;
            }
        }
    }

    // Now go through the contexts below given context.
    $searchcontexts = array_keys($context->get_child_contexts());
    foreach ($searchcontexts as $cid) {
        if ($capabilities = $DB->get_records('role_capabilities', array('roleid'=>$roleid, 'contextid'=>$cid))) {
            foreach ($capabilities as $cap) {
                if (!array_key_exists($cap->contextid, $result)) {
                    $result[$cap->contextid] = array();
                }
                $result[$cap->contextid][$cap->capability] = $cap->permission;
            }
        }
    }

    return $result;
}

/**
 * Returns current course id or false if outside of course based on context parameter.
 *
 * @see context::get_course_context()
 * @deprecated since 2.2
 * @param context $context
 * @return int|bool related course id or false
 */
function get_courseid_from_context(context $context) {
    debugging('get_courseid_from_context() is deprecated, please use $context->get_course_context(false) instead.', DEBUG_DEVELOPER);
    if ($coursecontext = $context->get_course_context(false)) {
        return $coursecontext->instanceid;
    } else {
        return false;
    }
}

/**
 * Preloads context information together with instances.
 * Use context_instance_preload() to strip the context info from the record and cache the context instance.
 *
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
 * @param string $joinon for example 'u.id'
 * @param string $contextlevel context level of instance in $joinon
 * @param string $tablealias context table alias
 * @return array with two values - select and join part
 */
function context_instance_preload_sql($joinon, $contextlevel, $tablealias) {
    debugging('context_instance_preload_sql() is deprecated, please use context_helper::get_preload_record_columns_sql() instead.', DEBUG_DEVELOPER);
    $select = ", " . context_helper::get_preload_record_columns_sql($tablealias);
    $join = "LEFT JOIN {context} $tablealias ON ($tablealias.instanceid = $joinon AND $tablealias.contextlevel = $contextlevel)";
    return array($select, $join);
}

/**
 * Gets a string for sql calls, searching for stuff in this context or above.
 *
 * @deprecated since 2.2
 * @see context::get_parent_context_ids()
 * @param context $context
 * @return string
 */
function get_related_contexts_string(context $context) {
    debugging('get_related_contexts_string() is deprecated, please use $context->get_parent_context_ids(true) instead.', DEBUG_DEVELOPER);
    if ($parents = $context->get_parent_context_ids()) {
        return (' IN ('.$context->id.','.implode(',', $parents).')');
    } else {
        return (' ='.$context->id);
    }
}

/**
 * Get a list of all the plugins of a given type that contain a particular file.
 *
 * @param string $plugintype the type of plugin, e.g. 'mod' or 'report'.
 * @param string $file the name of file that must be present in the plugin.
 *      (e.g. 'view.php', 'db/install.xml').
 * @param bool $include if true (default false), the file will be include_once-ed if found.
 * @return array with plugin name as keys (e.g. 'forum', 'courselist') and the path
 *      to the file relative to dirroot as value (e.g. "$CFG->dirroot/mod/forum/view.php").
 * @deprecated since 2.6
 * @see core_component::get_plugin_list_with_file()
 */
function get_plugin_list_with_file($plugintype, $file, $include = false) {
    debugging('get_plugin_list_with_file() is deprecated, please use core_component::get_plugin_list_with_file() instead.',
        DEBUG_DEVELOPER);
    return core_component::get_plugin_list_with_file($plugintype, $file, $include);
}

/**
 * Checks to see if is the browser operating system matches the specified brand.
 *
 * Known brand: 'Windows','Linux','Macintosh','SGI','SunOS','HP-UX'
 *
 * @deprecated since 2.6
 * @param string $brand The operating system identifier being tested
 * @return bool true if the given brand below to the detected operating system
 */
function check_browser_operating_system($brand) {
    debugging('check_browser_operating_system has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::check_browser_operating_system($brand);
}

/**
 * Checks to see if is a browser matches the specified
 * brand and is equal or better version.
 *
 * @deprecated since 2.6
 * @param string $brand The browser identifier being tested
 * @param int $version The version of the browser, if not specified any version (except 5.5 for IE for BC reasons)
 * @return bool true if the given version is below that of the detected browser
 */
function check_browser_version($brand, $version = null) {
    debugging('check_browser_version has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::check_browser_version($brand, $version);
}

/**
 * Returns whether a device/browser combination is mobile, tablet, legacy, default or the result of
 * an optional admin specified regular expression.  If enabledevicedetection is set to no or not set
 * it returns default
 *
 * @deprecated since 2.6
 * @return string device type
 */
function get_device_type() {
    debugging('get_device_type has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::get_device_type();
}

/**
 * Returns a list of the device types supporting by Moodle
 *
 * @deprecated since 2.6
 * @param boolean $incusertypes includes types specified using the devicedetectregex admin setting
 * @return array $types
 */
function get_device_type_list($incusertypes = true) {
    debugging('get_device_type_list has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::get_device_type_list($incusertypes);
}

/**
 * Returns the theme selected for a particular device or false if none selected.
 *
 * @deprecated since 2.6
 * @param string $devicetype
 * @return string|false The name of the theme to use for the device or the false if not set
 */
function get_selected_theme_for_device_type($devicetype = null) {
    debugging('get_selected_theme_for_device_type has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::get_device_type_theme($devicetype);
}

/**
 * Returns the name of the device type theme var in $CFG because there is not a convention to allow backwards compatibility.
 *
 * @deprecated since 2.6
 * @param string $devicetype
 * @return string The config variable to use to determine the theme
 */
function get_device_cfg_var_name($devicetype = null) {
    debugging('get_device_cfg_var_name has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::get_device_type_cfg_var_name($devicetype);
}

/**
 * Allows the user to switch the device they are seeing the theme for.
 * This allows mobile users to switch back to the default theme, or theme for any other device.
 *
 * @deprecated since 2.6
 * @param string $newdevice The device the user is currently using.
 * @return string The device the user has switched to
 */
function set_user_device_type($newdevice) {
    debugging('set_user_device_type has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::set_user_device_type($newdevice);
}

/**
 * Returns the device the user is currently using, or if the user has chosen to switch devices
 * for the current device type the type they have switched to.
 *
 * @deprecated since 2.6
 * @return string The device the user is currently using or wishes to use
 */
function get_user_device_type() {
    debugging('get_user_device_type has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::get_user_device_type();
}

/**
 * Returns one or several CSS class names that match the user's browser. These can be put
 * in the body tag of the page to apply browser-specific rules without relying on CSS hacks
 *
 * @deprecated since 2.6
 * @return array An array of browser version classes
 */
function get_browser_version_classes() {
    debugging('get_browser_version_classes has been deprecated, please update your code to use core_useragent instead.', DEBUG_DEVELOPER);
    return core_useragent::get_browser_version_classes();
}

/**
 * Generate a fake user for emails based on support settings
 *
 * @deprecated since Moodle 2.6
 * @see core_user::get_support_user()
 * @return stdClass user info
 */
function generate_email_supportuser() {
    debugging('generate_email_supportuser is deprecated, please use core_user::get_support_user');
    return core_user::get_support_user();
}

/**
 * Get issued badge details for assertion URL
 *
 * @deprecated since Moodle 2.6
 * @param string $hash Unique hash of a badge
 * @return array Information about issued badge.
 */
function badges_get_issued_badge_info($hash) {
    debugging('Function badges_get_issued_badge_info() is deprecated. Please use core_badges_assertion class and methods to generate badge assertion.', DEBUG_DEVELOPER);
    $assertion = new core_badges_assertion($hash);
    return $assertion->get_badge_assertion();
}

/**
 * Does the user want and can edit using rich text html editor?
 * This function does not make sense anymore because a user can directly choose their preferred editor.
 *
 * @deprecated since 2.6
 * @return bool
 */
function can_use_html_editor() {
    debugging('can_use_html_editor has been deprecated please update your code to assume it returns true.', DEBUG_DEVELOPER);
    return true;
}


/**
 * Returns an object with counts of failed login attempts.
 *
 * @deprecated since Moodle 2.7, use {@link user_count_login_failures()} instead.
 */
function count_login_failures($mode, $username, $lastlogin) {
    throw new coding_exception('count_login_failures() can not be used any more, please use user_count_login_failures().');
}

/**
 * It should no longer be required to work without JavaScript enabled.
 *
 * @deprecated since 2.7 MDL-33099/MDL-44088 - please do not use this function any more.
 */
function ajaxenabled(array $browsers = null) {
    throw new coding_exception('ajaxenabled() can not be used anymore. Update your code to work with JS at all times.');
}

/**
 * Determine whether a course module is visible within a course.
 *
 * @deprecated Since Moodle 2.7 MDL-44070
 */
function coursemodule_visible_for_user($cm, $userid=0) {
    throw new coding_exception('coursemodule_visible_for_user() can not be used any more,
            please use \core_availability\info_module::is_user_visible()');
}

/**
 * Gets all the cohorts the user is able to view.
 *
 * @deprecated since Moodle 2.8 MDL-36014, MDL-35618 this functionality is removed
 *
 * @param course_enrolment_manager $manager
 * @return array
 */
function enrol_cohort_get_cohorts(course_enrolment_manager $manager) {
    global $CFG;
    debugging('Function enrol_cohort_get_cohorts() is deprecated, use enrol_cohort_search_cohorts() or '.
        'cohort_get_available_cohorts() instead', DEBUG_DEVELOPER);
    return enrol_cohort_search_cohorts($manager, 0, 0, '');
}

/**
 * Check if cohort exists and user is allowed to enrol it.
 *
 * This function is deprecated, use {@link cohort_can_view_cohort()} instead since it also
 * takes into account current context
 *
 * @deprecated since Moodle 2.8 MDL-36014 please use cohort_can_view_cohort()
 *
 * @param int $cohortid Cohort ID
 * @return boolean
 */
function enrol_cohort_can_view_cohort($cohortid) {
    global $CFG;
    require_once($CFG->dirroot . '/cohort/lib.php');
    debugging('Function enrol_cohort_can_view_cohort() is deprecated, use cohort_can_view_cohort() instead',
        DEBUG_DEVELOPER);
    return cohort_can_view_cohort($cohortid, null);
}

/**
 * Returns list of cohorts from course parent contexts.
 *
 * Note: this function does not implement any capability checks,
 *       it means it may disclose existence of cohorts,
 *       make sure it is displayed to users with appropriate rights only.
 *
 * It is advisable to use {@link cohort_get_available_cohorts()} instead.
 *
 * @deprecated since Moodle 2.8 MDL-36014 use cohort_get_available_cohorts() instead
 *
 * @param  stdClass $course
 * @param  bool $onlyenrolled true means include only cohorts with enrolled users
 * @return array of cohort names with number of enrolled users
 */
function cohort_get_visible_list($course, $onlyenrolled=true) {
    global $DB;

    debugging('Function cohort_get_visible_list() is deprecated. Please use function cohort_get_available_cohorts() ".
        "that correctly checks capabilities.', DEBUG_DEVELOPER);

    $context = context_course::instance($course->id);
    list($esql, $params) = get_enrolled_sql($context);
    list($parentsql, $params2) = $DB->get_in_or_equal($context->get_parent_context_ids(), SQL_PARAMS_NAMED);
    $params = array_merge($params, $params2);

    if ($onlyenrolled) {
        $left = "";
        $having = "HAVING COUNT(u.id) > 0";
    } else {
        $left = "LEFT";
        $having = "";
    }

    $sql = "SELECT c.id, c.name, c.contextid, c.idnumber, c.visible, COUNT(u.id) AS cnt
              FROM {cohort} c
        $left JOIN ({cohort_members} cm
                   JOIN ($esql) u ON u.id = cm.userid) ON cm.cohortid = c.id
             WHERE c.contextid $parentsql
          GROUP BY c.id, c.name, c.contextid, c.idnumber, c.visible
           $having
          ORDER BY c.name, c.idnumber, c.visible";

    $cohorts = $DB->get_records_sql($sql, $params);

    foreach ($cohorts as $cid=>$cohort) {
        $cohorts[$cid] = format_string($cohort->name, true, array('context'=>$cohort->contextid));
        if ($cohort->cnt) {
            $cohorts[$cid] .= ' (' . $cohort->cnt . ')';
        }
    }

    return $cohorts;
}

/**
 * Enrols all of the users in a cohort through a manual plugin instance.
 *
 * In order for this to succeed the course must contain a valid manual
 * enrolment plugin instance that the user has permission to enrol users through.
 *
 * @deprecated since Moodle 2.8 MDL-35618 this functionality is removed
 *
 * @global moodle_database $DB
 * @param course_enrolment_manager $manager
 * @param int $cohortid
 * @param int $roleid
 * @return int
 */
function enrol_cohort_enrol_all_users(course_enrolment_manager $manager, $cohortid, $roleid) {
    global $DB;
    debugging('enrol_cohort_enrol_all_users() is deprecated. This functionality is moved to enrol_manual.', DEBUG_DEVELOPER);

    $context = $manager->get_context();
    require_capability('moodle/course:enrolconfig', $context);

    $instance = false;
    $instances = $manager->get_enrolment_instances();
    foreach ($instances as $i) {
        if ($i->enrol == 'manual') {
            $instance = $i;
            break;
        }
    }
    $plugin = enrol_get_plugin('manual');
    if (!$instance || !$plugin || !$plugin->allow_enrol($instance) || !has_capability('enrol/'.$plugin->get_name().':enrol', $context)) {
        return false;
    }
    $sql = "SELECT com.userid
              FROM {cohort_members} com
         LEFT JOIN (
                SELECT *
                  FROM {user_enrolments} ue
                 WHERE ue.enrolid = :enrolid
                 ) ue ON ue.userid=com.userid
             WHERE com.cohortid = :cohortid AND ue.id IS NULL";
    $params = array('cohortid' => $cohortid, 'enrolid' => $instance->id);
    $rs = $DB->get_recordset_sql($sql, $params);
    $count = 0;
    foreach ($rs as $user) {
        $count++;
        $plugin->enrol_user($instance, $user->userid, $roleid);
    }
    $rs->close();
    return $count;
}

/**
 * Gets cohorts the user is able to view.
 *
 * @deprecated since Moodle 2.8 MDL-35618 this functionality is removed
 *
 * @global moodle_database $DB
 * @param course_enrolment_manager $manager
 * @param int $offset limit output from
 * @param int $limit items to output per load
 * @param string $search search string
 * @return array    Array(more => bool, offset => int, cohorts => array)
 */
function enrol_cohort_search_cohorts(course_enrolment_manager $manager, $offset = 0, $limit = 25, $search = '') {
    global $CFG;
    debugging('enrol_cohort_search_cohorts() is deprecated. This functionality is moved to enrol_manual.', DEBUG_DEVELOPER);
    require_once($CFG->dirroot . '/cohort/lib.php');

    $context = $manager->get_context();
    $cohorts = array();
    $instances = $manager->get_enrolment_instances();
    $enrolled = array();
    foreach ($instances as $instance) {
        if ($instance->enrol === 'cohort') {
            $enrolled[] = $instance->customint1;
        }
    }

    $rawcohorts = cohort_get_available_cohorts($context, COHORT_COUNT_MEMBERS, $offset, $limit, $search);

    // Produce the output respecting parameters.
    foreach ($rawcohorts as $c) {
        $cohorts[$c->id] = array(
            'cohortid' => $c->id,
            'name'     => shorten_text(format_string($c->name, true, array('context'=>context::instance_by_id($c->contextid))), 35),
            'users'    => $c->memberscnt,
            'enrolled' => in_array($c->id, $enrolled)
        );
    }
    return array('more' => !(bool)$limit, 'offset' => $offset, 'cohorts' => $cohorts);
}

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

    // Determine context.
    if (isloggedin()) {
        $context = context_user::instance($USER->id);
    } else {
        $context = context_system::instance();
    }

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
