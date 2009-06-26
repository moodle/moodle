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
 * @package moodlecore
 * @subpackage deprecated
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated
 */

/**
 * Determines if a user is a teacher (or better)
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @uses CONTEXT_SYSTEM
 * @param int $courseid The id of the course that is being viewed, if any
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param bool $obsolete_includeadmin Not used any more
 * @return bool
 */
function isteacher($courseid=0, $userid=0, $obsolete_includeadmin=true) {
/// Is the user able to access this course as a teacher?
    global $CFG;

    if ($courseid) {
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    } else {
        $context = get_context_instance(CONTEXT_SYSTEM);
    }

    return (has_capability('moodle/legacy:teacher', $context, $userid, false)
         or has_capability('moodle/legacy:editingteacher', $context, $userid, false)
         or has_capability('moodle/legacy:admin', $context, $userid, false));
}

/**
 * Determines if a user is a teacher in any course, or an admin
 *
 * @global object
 * @global object
 * @global object
 * @uses CAP_ALLOW
 * @uses CONTEXT_SYSTEM
 * @param int $userid The id of the user that is being tested against. Set this to 0 if you would just like to test against the currently logged in user.
 * @param bool $includeadmin Include anyone wo is an admin as well
 * @return bool
 */
function isteacherinanycourse($userid=0, $includeadmin=true) {
    global $USER, $CFG, $DB;

    if (!$userid) {
        if (empty($USER->id)) {
            return false;
        }
        $userid = $USER->id;
    }

    if (!$DB->record_exists('role_assignments', array('userid'=>$userid))) {    // Has no roles anywhere
        return false;
    }

/// If this user is assigned as an editing teacher anywhere then return true
    if ($roles = get_roles_with_capability('moodle/legacy:editingteacher', CAP_ALLOW)) {
        foreach ($roles as $role) {
            if ($DB->record_exists('role_assignments', array('roleid'=>$role->id, 'userid'=>$userid))) {
                return true;
            }
        }
    }

/// If this user is assigned as a non-editing teacher anywhere then return true
    if ($roles = get_roles_with_capability('moodle/legacy:teacher', CAP_ALLOW)) {
        foreach ($roles as $role) {
            if ($DB->record_exists('role_assignments', array('roleid'=>$role->id, 'userid'=>$userid))) {
                return true;
            }
        }
    }

/// Include admins if required
    if ($includeadmin) {
        $context = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/legacy:admin', $context, $userid, false)) {
            return true;
        }
    }

    return false;
}


/**
 * Determines if the specified user is logged in as guest.
 *
 * @global object
 * @uses CONTEXT_SYSTEM
 * @param int $userid The user being tested. You can set this to 0 or leave it blank to test the currently logged in user.
 * @return bool
 */
function isguest($userid=0) {
    global $CFG;

    $context = get_context_instance(CONTEXT_SYSTEM);

    return has_capability('moodle/legacy:guest', $context, $userid, false);
}


/**
 * Get the guest user information from the database
 *
 * @todo Is object(user) a correct return type? Or is array the proper return type with a 
 * note that the contents include all details for a user.
 *
 * @return object(user) An associative array with the details of the guest user account.
 */
function get_guest() {
    return get_complete_user_data('username', 'guest');
}

/**
 * Returns $user object of the main teacher for a course
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @param int $courseid The course in question.
 * @return user|false  A {@link $USER} record of the main teacher for the specified course or false if error.
 */
function get_teacher($courseid) {

    global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    // Pass $view=true to filter hidden caps if the user cannot see them
    if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                         '', '', '', '', false, true)) {
        $users = sort_by_roleassignment_authority($users, $context);
        return array_shift($users);
    }

    return false;
}

/**
 * Searches logs to find all enrolments since a certain date
 *
 * used to print recent activity
 *
 * @global object
 * @uses CONTEXT_COURSE
 * @param int $courseid The course in question.
 * @param int $timestart The date to check forward of
 * @return object|false  {@link $USER} records or false if error.
 */
function get_recent_enrolments($courseid, $timestart) {
    global $DB;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, l.time
              FROM {user} u, {role_assignments} ra, {log} l
             WHERE l.time > ?
                   AND l.course = ?
                   AND l.module = 'course'
                   AND l.action = 'enrol'
                   AND ".$DB->sql_cast_char2int('l.info')." = u.id
                   AND u.id = ra.userid
                   AND ra.contextid ".get_related_contexts_string($context)."
          ORDER BY l.time ASC";
    $params = array($timestart, $courseid);
    return $DB->get_records_sql($sql, $params);
}

########### FROM weblib.php ##########################################################################


/**
 * Print a message in a standard themed box.
 * This old function used to implement boxes using tables.  Now it uses a DIV, but the old
 * parameters remain.  If possible, $align, $width and $color should not be defined at all.
 * Preferably just use print_box() in weblib.php
 *
 * @deprecated
 * @param string $message The message to display
 * @param string $align alignment of the box, not the text (default center, left, right).
 * @param string $width width of the box, including units %, for example '100%'.
 * @param string $color background colour of the box, for example '#eee'.
 * @param int $padding padding in pixels, specified without units.
 * @param string $class space-separated class names.
 * @param string $id space-separated id names.
 * @param boolean $return return as string or just print it
 * @return string|void Depending on $return
 */
function print_simple_box($message, $align='', $width='', $color='', $padding=5, $class='generalbox', $id='', $return=false) {
    $output = '';
    $output .= print_simple_box_start($align, $width, $color, $padding, $class, $id, true);
    $output .= $message;
    $output .= print_simple_box_end(true);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}



/**
 * This old function used to implement boxes using tables.  Now it uses a DIV, but the old
 * parameters remain.  If possible, $align, $width and $color should not be defined at all.
 * Even better, please use print_box_start() in weblib.php
 *
 * @param string $align alignment of the box, not the text (default center, left, right).   DEPRECATED
 * @param string $width width of the box, including % units, for example '100%'.            DEPRECATED
 * @param string $color background colour of the box, for example '#eee'.                   DEPRECATED
 * @param int $padding padding in pixels, specified without units.                          OBSOLETE
 * @param string $class space-separated class names.
 * @param string $id space-separated id names.
 * @param boolean $return return as string or just print it
 * @return string|void Depending on $return
 */
function print_simple_box_start($align='', $width='', $color='', $padding=5, $class='generalbox', $id='', $return=false) {
    debugging('print_simple_box(_star/_end) is deprecated. Please use $OUTPUT->box(_star/_end) instead', DEBUG_DEVELOPER);

    $output = '';

    $divclasses = 'box '.$class.' '.$class.'content';
    $divstyles  = '';

    if ($align) {
        $divclasses .= ' boxalign'.$align;    // Implement alignment using a class
    }
    if ($width) {    // Hopefully we can eliminate these in calls to this function (inline styles are bad)
        if (substr($width, -1, 1) == '%') {    // Width is a % value
            $width = (int) substr($width, 0, -1);    // Extract just the number
            if ($width < 40) {
                $divclasses .= ' boxwidthnarrow';    // Approx 30% depending on theme
            } else if ($width > 60) {
                $divclasses .= ' boxwidthwide';      // Approx 80% depending on theme
            } else {
                $divclasses .= ' boxwidthnormal';    // Approx 50% depending on theme
            }
        } else {
            $divstyles  .= ' width:'.$width.';';     // Last resort
        }
    }
    if ($color) {    // Hopefully we can eliminate these in calls to this function (inline styles are bad)
        $divstyles  .= ' background:'.$color.';';
    }
    if ($divstyles) {
        $divstyles = ' style="'.$divstyles.'"';
    }

    if ($id) {
        $id = ' id="'.$id.'"';
    }

    $output .= '<div'.$id.$divstyles.' class="'.$divclasses.'">';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}


/**
 * Print the end portion of a standard themed box.
 * Preferably just use print_box_end() in weblib.php
 *
 * @param boolean $return return as string or just print it
 * @return string|void Depending on $return
 */
function print_simple_box_end($return=false) {
    $output = '</div>';
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * deprecated - use clean_param($string, PARAM_FILE); instead
 * Check for bad characters ?
 *
 * @todo Finish documenting this function - more detail needed in description as well as details on arguments
 *
 * @param string $string ?
 * @param int $allowdots ?
 * @return bool
 */
function detect_munged_arguments($string, $allowdots=1) {
    if (substr_count($string, '..') > $allowdots) {   // Sometimes we allow dots in references
        return true;
    }
    if (ereg('[\|\`]', $string)) {  // check for other bad characters
        return true;
    }
    if (empty($string) or $string == '/') {
        return true;
    }

    return false;
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

/////////////////////////////////////////////////////////////
/// Old functions not used anymore - candidates for removal
/////////////////////////////////////////////////////////////


/** various deprecated groups function **/


/**
 * Get the IDs for the user's groups in the given course.
 *
 * @global object
 * @param int $courseid The course being examined - the 'course' table id field.
 * @return array|bool An _array_ of groupids, or false
 * (Was return $groupids[0] - consequences!)
 */
function mygroupid($courseid) {
    global $USER;
    if ($groups = groups_get_all_groups($courseid, $USER->id)) {
        return array_keys($groups);
    } else {
        return false;
    }
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
 * Print an error page displaying an error message.
 * Old method, don't call directly in new code - use print_error instead.
 *
 * @global object
 * @param string $message The message to display to the user about the error.
 * @param string $link The url where the user will be prompted to continue. If no url is provided the user will be directed to the site index page.
 * @return void Terminates script, does not return!
 */
function error($message, $link='') {
    global $UNITTEST;

    // If unittest running, throw exception instead
    if (!empty($UNITTEST->running)) {
        // Errors in unit test become exceptions, so you can unit test
        // code that might call error().
        throw new moodle_exception('notlocalisederrormessage', 'error', $link, $message);
    }

    _print_normal_error('notlocalisederrormessage', 'error', $message, $link, debug_backtrace(), null, true); // show debug warning
}


/// Deprecated DDL functions, to be removed soon ///
/**
 * @deprecated
 * @global object
 * @param string $table
 * @return bool
 */
function table_exists($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->table_exists($table);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function field_exists($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->field_exists($table, $field);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $index
 * @return bool
 */
function find_index_name($table, $index) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_index_name($table, $index);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $index
 * @return bool
 */
function index_exists($table, $index) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->index_exists($table, $index);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function find_check_constraint_name($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_check_constraint_name($table, $field);
}

/**
 * @deprecated
 * @global object
 */
function check_constraint_exists($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->check_constraint_exists($table, $field);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $xmldb_key
 * @return bool
 */
function find_key_name($table, $xmldb_key) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_key_name($table, $xmldb_key);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @return bool
 */
function find_sequence_name($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    return $DB->get_manager()->find_sequence_name($table);
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @return bool
 */
function drop_table($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->drop_table($table);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $file
 * @return bool
 */
function install_from_xmldb_file($file) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->install_from_xmldb_file($file);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @return bool
 */
function create_table($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->create_table($table);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @return bool
 */
function create_temp_table($table) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->create_temp_table($table);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $newname
 * @return bool
 */
function rename_table($table, $newname) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->rename_table($table, $newname);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function add_field($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->add_field($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function drop_field($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->drop_field($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function change_field_type($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->change_field_type($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function change_field_precision($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->change_field_precision($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function change_field_unsigned($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->change_field_unsigned($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function change_field_notnull($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->change_field_notnull($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function change_field_enum($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used! Only dropping of enums is allowed.');
    $DB->get_manager()->drop_enum_from_field($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @return bool
 */
function change_field_default($table, $field) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->change_field_default($table, $field);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $field
 * @param string $newname
 * @return bool
 */
function rename_field($table, $field, $newname) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->rename_field($table, $field, $newname);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $key
 * @return bool
 */
function add_key($table, $key) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->add_key($table, $key);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $key
 * @return bool
 */
function drop_key($table, $key) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->drop_key($table, $key);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $key
 * @param string $newname
 * @return bool
 */
function rename_key($table, $key, $newname) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->rename_key($table, $key, $newname);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $index
 * @return bool
 */
function add_index($table, $index) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->add_index($table, $index);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $index
 * @return bool
 */
function drop_index($table, $index) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->drop_index($table, $index);
    return true;
}

/**
 * @deprecated
 * @global object
 * @param string $table
 * @param string $index
 * @param string $newname
 * @return bool
 */
function rename_index($table, $index, $newname) {
    global $DB;
    debugging('Deprecated ddllib function used!');
    $DB->get_manager()->rename_index($table, $index, $newname);
    return true;
}


//////////////////////////
/// removed functions ////
//////////////////////////

/**
 * @deprecated
 * @param mixed $mixed
 * @return void Throws an error and does nothing
 */
function stripslashes_safe($mixed) {
    error('stripslashes_safe() not available anymore');
}
/**
 * @deprecated
 * @param mixed $var
 * @return void Throws an error and does nothing
 */
function stripslashes_recursive($var) {
    error('stripslashes_recursive() not available anymore');
}
/**
 * @deprecated
 * @param mixed $dataobject
 * @return void Throws an error and does nothing
 */
function addslashes_object($dataobject) {
    error('addslashes_object() not available anymore');
}
/**
 * @deprecated
 * @param mixed $var
 * @return void Throws an error and does nothing
 */
function addslashes_recursive($var) {
    error('addslashes_recursive() not available anymore');
}
/**
 * @deprecated
 * @param mixed $command
 * @param bool $feedback
 * @return void Throws an error and does nothing
 */
function execute_sql($command, $feedback=true) {
    error('execute_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $select
 * @return void Throws an error and does nothing
 */
function record_exists_select($table, $select='') {
    error('record_exists_select() not available anymore');
}
/**
 * @deprecated
 * @param mixed $sql
 * @return void Throws an error and does nothing
 */
function record_exists_sql($sql) {
    error('record_exists_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $select
 * @param mixed $countitem
 * @return void Throws an error and does nothing
 */
function count_records_select($table, $select='', $countitem='COUNT(*)') {
    error('count_records_select() not available anymore');
}
/**
 * @deprecated
 * @param mixed $sql
 * @return void Throws an error and does nothing
 */
function count_records_sql($sql) {
    error('count_records_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $sql
 * @param bool $expectmultiple
 * @param bool $nolimit
 * @return void Throws an error and does nothing
 */
function get_record_sql($sql, $expectmultiple=false, $nolimit=false) {
    error('get_record_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $select
 * @param mixed $fields
 * @return void Throws an error and does nothing
 */
function get_record_select($table, $select='', $fields='*') {
    error('get_record_select() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field
 * @param mixed $value
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_recordset($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_recordset() not available anymore');
}
/**
 * @deprecated
 * @param mixed $sql
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_recordset_sql($sql, $limitfrom=null, $limitnum=null) {
    error('get_recordset_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $rs
 * @return void Throws an error and does nothing
 */
function rs_fetch_record(&$rs) {
    error('rs_fetch_record() not available anymore');
}
/**
 * @deprecated
 * @param mixed $rs
 * @return void Throws an error and does nothing
 */
function rs_next_record(&$rs) {
    error('rs_next_record() not available anymore');
}
/**
 * @deprecated
 * @param mixed $rs
 * @return void Throws an error and does nothing
 */
function rs_fetch_next_record(&$rs) {
    error('rs_fetch_next_record() not available anymore');
}
/**
 * @deprecated
 * @param mixed $rs
 * @return void Throws an error and does nothing
 */
function rs_EOF($rs) {
    error('rs_EOF() not available anymore');
}
/**
 * @deprecated
 * @param mixed $rs
 * @return void Throws an error and does nothing
 */
function rs_close(&$rs) {
    error('rs_close() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $select
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_records_select($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_records_select() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $return
 * @param mixed $select
 * @return void Throws an error and does nothing
 */
function get_field_select($table, $return, $select) {
    error('get_field_select() not available anymore');
}
/**
 * @deprecated
 * @param mixed $sql
 * @return void Throws an error and does nothing
 */
function get_field_sql($sql) {
    error('get_field_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $sql
 * @param mixed $select
 * @return void Throws an error and does nothing
 */
function delete_records_select($table, $select='') {
    error('get_field_sql() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function configure_dbconnection() {
    error('configure_dbconnection() removed');
}
/**
 * @deprecated
 * @param mixed $field
 * @return void Throws an error and does nothing
 */
function sql_max($field) {
    error('sql_max() removed - use normal sql MAX() instead');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function sql_as() {
    error('sql_as() removed - do not use AS for tables at all');
}
/**
 * @deprecated
 * @param mixed $page
 * @param mixed $recordsperpage
 * @return void Throws an error and does nothing
 */
function sql_paging_limit($page, $recordsperpage) {
    error('Function sql_paging_limit() is deprecated. Replace it with the correct use of limitfrom, limitnum parameters');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function db_uppercase() {
    error('upper() removed - use normal sql UPPER()');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function db_lowercase() {
    error('upper() removed - use normal sql LOWER()');
}
/**
 * @deprecated
 * @param mixed $sqlfile
 * @param mixed $sqlstring
 * @return void Throws an error and does nothing
 */
function modify_database($sqlfile='', $sqlstring='') {
    error('modify_database() removed - use new XMLDB functions');
}
/**
 * @deprecated
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @return void Throws an error and does nothing
 */
function where_clause($field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    error('where_clause() removed - use new functions with $conditions parameter');
}
/**
 * @deprecated
 * @param mixed $sqlarr
 * @param mixed $continue
 * @param mixed $feedback
 * @return void Throws an error and does nothing
 */
function execute_sql_arr($sqlarr, $continue=true, $feedback=true) {
    error('execute_sql_arr() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field
 * @param mixed $values
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_records_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_records_list() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field
 * @param mixed $values
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_recordset_list($table, $field='', $values='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_recordset_list() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field
 * @param mixed $value
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_records_menu($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_records_menu() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $select
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_records_select_menu($table, $select='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_records_select_menu() removed');
}
/**
 * @deprecated
 * @param mixed $sql
 * @param mixed $limitfrom
 * @param mixed $limitnum
 * @return void Throws an error and does nothing
 */
function get_records_sql_menu($sql, $limitfrom='', $limitnum='') {
    error('get_records_sql_menu() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $column
 * @return void Throws an error and does nothing
 */
function column_type($table, $column) {
    error('column_type() removed');
}
/**
 * @deprecated
 * @param mixed $rs
 * @return void Throws an error and does nothing
 */
function recordset_to_menu($rs) {
    error('recordset_to_menu() removed');
}
/**
 * @deprecated
 * @param mixed $records
 * @param mixed $field1
 * @param mixed $field2
 * @return void Throws an error and does nothing
 */
function records_to_menu($records, $field1, $field2) {
    error('records_to_menu() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $newfield
 * @param mixed $newvalue
 * @param mixed $select
 * @param mixed $localcall
 * @return void Throws an error and does nothing
 */
function set_field_select($table, $newfield, $newvalue, $select, $localcall = false) {
    error('set_field_select() removed');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $return
 * @param mixed $select
 * @return void Throws an error and does nothing
 */
function get_fieldset_select($table, $return, $select) {
    error('get_fieldset_select() removed');
}
/**
 * @deprecated
 * @param mixed $sql
 * @return void Throws an error and does nothing
 */
function get_fieldset_sql($sql) {
    error('get_fieldset_sql() removed');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function sql_ilike() {
    error('sql_ilike() not available anymore');
}
/**
 * @deprecated
 * @param mixed $first
 * @param mixed $last
 * @return void Throws an error and does nothing
 */
function sql_fullname($first='firstname', $last='lastname') {
    error('sql_fullname() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function sql_concat() {
    error('sql_concat() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function sql_empty() {
    error('sql_empty() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function sql_substr() {
    error('sql_substr() not available anymore');
}
/**
 * @deprecated
 * @param mixed $int1
 * @param mixed $int2
 * @return void Throws an error and does nothing
 */
function sql_bitand($int1, $int2) {
    error('sql_bitand() not available anymore');
}
/**
 * @deprecated
 * @param mixed $int1
 * @return void Throws an error and does nothing
 */
function sql_bitnot($int1) {
    error('sql_bitnot() not available anymore');
}
/**
 * @deprecated
 * @param mixed $int1
 * @param mixed $int2
 * @return void Throws an error and does nothing
 */
function sql_bitor($int1, $int2) {
    error('sql_bitor() not available anymore');
}
/**
 * @deprecated
 * @param mixed $int1
 * @param mixed $int2
 * @return void Throws an error and does nothing
 */
function sql_bitxor($int1, $int2) {
    error('sql_bitxor() not available anymore');
}
/**
 * @deprecated
 * @param mixed $fieldname
 * @param mixed $text
 * @return void Throws an error and does nothing
 */
function sql_cast_char2int($fieldname, $text=false) {
    error('sql_cast_char2int() not available anymore');
}
/**
 * @deprecated
 * @param mixed $fieldname
 * @param mixed $numchars
 * @return void Throws an error and does nothing
 */
function sql_compare_text($fieldname, $numchars=32) {
    error('sql_compare_text() not available anymore');
}
/**
 * @deprecated
 * @param mixed $fieldname
 * @param mixed $numchars
 * @return void Throws an error and does nothing
 */
function sql_order_by_text($fieldname, $numchars=32) {
    error('sql_order_by_text() not available anymore');
}
/**
 * @deprecated
 * @param mixed $fieldname
 * @return void Throws an error and does nothing
 */
function sql_length($fieldname) {
    error('sql_length() not available anymore');
}
/**
 * @deprecated
 * @param mixed $separator
 * @param mixed $elements
 * @return void Throws an error and does nothing
 */
function sql_concat_join($separator="' '", $elements=array()) {
    error('sql_concat_join() not available anymore');
}
/**
 * @deprecated
 * @param mixed $tablename
 * @param mixed $fieldname
 * @param mixed $nullablefield
 * @param mixed $textfield
 * @return void Throws an error and does nothing
 */
function sql_isempty($tablename, $fieldname, $nullablefield, $textfield) {
    error('sql_isempty() not available anymore');
}
/**
 * @deprecated
 * @param mixed $tablename
 * @param mixed $fieldname
 * @param mixed $nullablefield
 * @param mixed $textfield
 * @return void Throws an error and does nothing
 */
function sql_isnotempty($tablename, $fieldname, $nullablefield, $textfield) {
    error('sql_isnotempty() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function begin_sql() {
    error('begin_sql() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function commit_sql() {
    error('commit_sql() not available anymore');
}
/**
 * @deprecated
 * @return void Throws an error and does nothing
 */
function rollback_sql() {
    error('rollback_sql() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $dataobject
 * @param mixed $returnid
 * @param mixed $primarykey
 * @return void Throws an error and does nothing
 */
function insert_record($table, $dataobject, $returnid=true, $primarykey='id') {
    error('insert_record() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $dataobject
 * @return void Throws an error and does nothing
 */
function update_record($table, $dataobject) {
    error('update_record() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field
 * @param mixed $value
 * @param mixed $sort
 * @param mixed $fields
 * @param mixed $limitfrom
 * @param mixed $limitnum
 
 * @return void Throws an error and does nothing
 */
function get_records($table, $field='', $value='', $sort='', $fields='*', $limitfrom='', $limitnum='') {
    error('get_records() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @param mixed $fields
 * @return void Throws an error and does nothing
 */
function get_record($table, $field1, $value1, $field2='', $value2='', $field3='', $value3='', $fields='*') {
    error('get_record() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $newfield
 * @param mixed $newvalue
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @return void Throws an error and does nothing
 */
function set_field($table, $newfield, $newvalue, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    error('set_field() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @return void Throws an error and does nothing
 */
function count_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    error('count_records() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @return void Throws an error and does nothing
 */
function record_exists($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    error('record_exists() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @return void Throws an error and does nothing
 */
function delete_records($table, $field1='', $value1='', $field2='', $value2='', $field3='', $value3='') {
    error('delete_records() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $return
 * @param mixed $field1
 * @param mixed $value1
 * @param mixed $field2
 * @param mixed $value2
 * @param mixed $field3
 * @param mixed $value3
 * @return void Throws an error and does nothing
 */
function get_field($table, $return, $field1, $value1, $field2='', $value2='', $field3='', $value3='') {
    error('get_field() not available anymore');
}
/**
 * @deprecated
 * @param mixed $table
 * @param mixed $oldfield
 * @param mixed $field
 * @param mixed $type
 * @param mixed $size
 * @param mixed $signed
 * @param mixed $default
 * @param mixed $null
 * @param mixed $after
 * @return void Throws an error and does nothing
 */
function table_column($table, $oldfield, $field, $type='integer', $size='10',
                      $signed='unsigned', $default='0', $null='not null', $after='') {
    error('table_column() was removed, please use new ddl functions');
}
/**
 * @deprecated
 * @param mixed $name
 * @param mixed $editorhidebuttons
 * @param mixed $id
 * @return void Throws an error and does nothing
 */
function use_html_editor($name='', $editorhidebuttons='', $id='') {
    error('use_html_editor() not available anymore');
}

/**
 * The old method that was used to include JavaScript libraries.
 * Please use $PAGE->requires->js() or $PAGE->requires->yui_lib() instead.
 *
 * @param mixed $lib The library or libraries to load (a string or array of strings)
 *      There are three way to specify the library:
 *      1. a shorname like 'yui_yahoo'. This translates into a call to $PAGE->requires->yui_lib('yahoo')->asap();
 *      2. the path to the library relative to wwwroot, for example 'lib/javascript-static.js'
 *      3. (legacy) a full URL like $CFG->wwwroot . '/lib/javascript-static.js'.
 *      2. and 3. lead to a call $PAGE->requires->js('/lib/javascript-static.js').
 */
function require_js($lib) {
    global $CFG, $PAGE;
    // Add the lib to the list of libs to be loaded, if it isn't already
    // in the list.
    if (is_array($lib)) {
        foreach($lib as $singlelib) {
            require_js($singlelib);
        }
        return;
    }

    // TODO uncomment this once we have eliminated the remaining calls to require_js from core.
    //debugging('Call to deprecated function require_js. Please use $PAGE->requires->js() ' .
    //        'or $PAGE->requires->yui_lib() instead.', DEBUG_DEVELOPER);

    if (strpos($lib, 'yui_') === 0) {
        echo $PAGE->requires->yui_lib(substr($lib, 4))->asap();
    } else if (preg_match('/^https?:/', $lib)) {
        echo $PAGE->requires->js(str_replace($CFG->wwwroot, '', $lib))->asap();
    } else {
        echo $PAGE->requires->js($lib)->asap();
    }
}

/**
 * Makes an upload directory for a particular module.
 *
 * This funciton has been deprecated by the file API changes in Moodle 2.0.
 *
 * @deprecated
 * @param int $courseid The id of the course in question - maps to id field of 'course' table.
 * @return string|false Returns full path to directory if successful, false if not
 */
function make_mod_upload_directory($courseid) {
    global $CFG;
    debugging('make_mod_upload_directory has been deprecated by the file API changes in Moodle 2.0.', DEBUG_DEVELOPER);
    return make_upload_directory($courseid .'/'. $CFG->moddata);
}

/**
 * Prints some red text using echo
 *
 * @deprecated
 * @param string $error The text to be displayed in red
 */
function formerr($error) {
    global $OUTPUT;
    echo $OUTPUT->error_text($error);
}

/**
 * Return the markup for the destination of the 'Skip to main content' links.
 * Accessibility improvement for keyboard-only users.
 *
 * Used in course formats, /index.php and /course/index.php
 *
 * @deprecated use $OUTPUT->skip_link_target() in instead.
 * @return string HTML element.
 */
function skip_main_destination() {
    global $OUTPUT;
    return $OUTPUT->skip_link_target();
}

/**
 * Prints a string in a specified size  (retained for backward compatibility)
 *
 * @deprecated
 * @param string $text The text to be displayed
 * @param int $size The size to set the font for text display.
 * @param bool $return If set to true output is returned rather than echoed Default false
 * @return string|void String if return is true
 */
function print_headline($text, $size=2, $return=false) {
    global $OUTPUT;
    $output = $OUTPUT->heading($text, $size);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Prints text in a format for use in headings.
 *
 * @deprecated
 * @param string $text The text to be displayed
 * @param string $deprecated No longer used. (Use to do alignment.)
 * @param int $size The size to set the font for text display.
 * @param string $class
 * @param bool $return If set to true output is returned rather than echoed, default false
 * @param string $id The id to use in the element
 * @return string|void String if return=true nothing otherwise
 */
function print_heading($text, $deprecated = '', $size = 2, $class = 'main', $return = false, $id = '') {
    global $OUTPUT;
    if (!empty($deprecated)) {
        debugging('Use of deprecated align attribute of print_heading. ' .
                'Please do not specify styling in PHP code like that.', DEBUG_DEVELOPER);
    }
    $output = $OUTPUT->heading($text, $size, $class, $id);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Output a standard heading block
 *
 * @deprecated
 * @param string $heading The text to write into the heading
 * @param string $class An additional Class Attr to use for the heading
 * @param bool $return If set to true output is returned rather than echoed, default false
 * @return string|void HTML String if return=true nothing otherwise
 */
function print_heading_block($heading, $class='', $return=false) {
    global $OUTPUT;
    $output = $OUTPUT->heading($heading, 2, 'headingblock header ' . moodle_renderer_base::prepare_classes($class));
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a message in a standard themed box.
 * Replaces print_simple_box (see deprecatedlib.php)
 *
 * @deprecated
 * @param string $message, the content of the box
 * @param string $classes, space-separated class names.
 * @param string $ids
 * @param boolean $return, return as string or just print it
 * @return string|void mixed string or void
 */
function print_box($message, $classes='generalbox', $ids='', $return=false) {
    global $OUTPUT;
    $output = $OUTPUT->box($message, $classes, $ids);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Starts a box using divs
 * Replaces print_simple_box_start (see deprecatedlib.php)
 *
 * @deprecated
 * @param string $classes, space-separated class names.
 * @param string $ids
 * @param boolean $return, return as string or just print it
 * @return string|void  string or void
 */
function print_box_start($classes='generalbox', $ids='', $return=false) {
    global $OUTPUT;
    $output = $OUTPUT->box_start($classes, $ids);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Simple function to end a box (see above)
 * Replaces print_simple_box_end (see deprecatedlib.php)
 *
 * @deprecated
 * @param boolean $return, return as string or just print it
 * @return string|void Depending on value of return
 */
function print_box_end($return=false) {
    global $OUTPUT;
    $output = $OUTPUT->box_end();
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Print a message in a standard themed container.
 *
 * @deprecated
 * @param string $message, the content of the container
 * @param boolean $clearfix clear both sides
 * @param string $classes, space-separated class names.
 * @param string $idbase
 * @param boolean $return, return as string or just print it
 * @return string|void Depending on value of $return
 */
function print_container($message, $clearfix=false, $classes='', $idbase='', $return=false) {
    global $OUTPUT;
    if ($clearfix) {
        $classes .= ' clearfix';
    }
    $output = $OUTPUT->container($message, $classes, $idbase);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Starts a container using divs
 *
 * @deprecated
 * @param boolean $clearfix clear both sides
 * @param string $classes, space-separated class names.
 * @param string $idbase
 * @param boolean $return, return as string or just print it
 * @return string|void Based on value of $return
 */
function print_container_start($clearfix=false, $classes='', $idbase='', $return=false) {
    global $OUTPUT;
    if ($clearfix) {
        $classes .= ' clearfix';
    }
    $output = $OUTPUT->container_start($classes, $idbase);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Simple function to end a container (see above)
 *
 * @deprecated
 * @param boolean $return, return as string or just print it
 * @return string|void Based on $return
 */
function print_container_end($return=false) {
    global $OUTPUT;
    $output = $OUTPUT->container_end();
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
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
 * Print a continue button that goes to a particular URL.
 *
 * @param string $link The url to create a link to.
 * @param bool $return If set to true output is returned rather than echoed, default false
 * @return string|void HTML String if return=true nothing otherwise
 */
function print_continue($link, $return = false) {
    global $CFG, $OUTPUT;

    if ($link == '') {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $link = $_SERVER['HTTP_REFERER'];
            $link = str_replace('&', '&amp;', $link); // make it valid XHTML
        } else {
            $link = $CFG->wwwroot .'/';
        }
    }

    $output = $OUTPUT->continue_button($link);
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

/**
 * Returns a string containing a link to the user documentation for the current
 * page. Also contains an icon by default. Shown to teachers and admin only.
 *
 * @global object
 * @global object
 * @param string $text The text to be displayed for the link
 * @param string $iconpath The path to the icon to be displayed
 * @return string The link to user documentation for this current page
 */
function page_doc_link($text='', $iconpath='') {
    global $CFG, $PAGE;

    if (empty($CFG->docroot) || during_initial_install()) {
        return '';
    }
    if (!has_capability('moodle/site:doclinks', $PAGE->context)) {
        return '';
    }

    $path = $PAGE->docspath;
    if (!$path) {
        return '';
    }
    return doc_link($path, $text, $iconpath);
}

/**
 * Print a standard header
 *
 * @param string  $title Appears at the top of the window
 * @param string  $heading Appears at the top of the page
 * @param string  $navigation Array of $navlinks arrays (keys: name, link, type) for use as breadcrumbs links
 * @param string  $focus Indicates form element to get cursor focus on load eg  inputform.password
 * @param string  $meta Meta tags to be added to the header
 * @param boolean $cache Should this page be cacheable?
 * @param string  $button HTML code for a button (usually for module editing)
 * @param string  $menu HTML code for a popup menu
 * @param boolean $usexml use XML for this page
 * @param string  $bodytags This text will be included verbatim in the <body> tag (useful for onload() etc)
 * @param bool    $return If true, return the visible elements of the header instead of echoing them.
 * @return string|void If return=true then string else void
 */
function print_header($title='', $heading='', $navigation='', $focus='',
                      $meta='', $cache=true, $button='&nbsp;', $menu='',
                      $usexml=false, $bodytags='', $return=false) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($title);
    $PAGE->set_heading($heading);
    $PAGE->set_cacheable($cache);
    $PAGE->set_focuscontrol($focus);
    if ($button == '') {
        $button = '&nbsp;';
    }
    $PAGE->set_button($button);

    if ($navigation == 'home') {
        $navigation = '';
    }
    if (gettype($navigation) == 'string' && strlen($navigation) != 0 && $navigation != 'home') {
        debugging("print_header() was sent a string as 3rd ($navigation) parameter. "
                . "This is deprecated in favour of an array built by build_navigation(). Please upgrade your code.", DEBUG_DEVELOPER);
    }

    // TODO $navigation
    // TODO $menu

    if ($meta) {
        throw new coding_exception('The $meta parameter to print_header is no longer supported. '.
                'You should be able to do weverything you want with $PAGE->requires and other such mechanisms.');
    }
    if ($usexml) {
        throw new coding_exception('The $usexml parameter to print_header is no longer supported.');
    }
    if ($bodytags) {
        throw new coding_exception('The $bodytags parameter to print_header is no longer supported.');
    }

    $output = $OUTPUT->header($navigation, $menu);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

function print_footer($course = NULL, $usercourse = NULL, $return = false) {
    global $PAGE, $OUTPUT;
    // TODO check arguments.
    if (is_string($course)) {
        debugging("Magic values like 'home', 'empty' passed to print_footer no longer have any effect. " .
                'To achieve a similar effect, call $PAGE->set_generaltype before you call print_header.', DEBUG_DEVELOPER);
    } else if (!empty($course->id) && $course->id != $PAGE->course->id) {
        throw new coding_exception('The $course object you passed to print_footer does not match $PAGE->course.');
    }
    if (!is_null($usercourse)) {
        debugging('The second parameter ($usercourse) to print_footer is no longer supported. ' .
                '(I did not think it was being used anywhere.)', DEBUG_DEVELOPER);
    }
    $output = $OUTPUT->footer();
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}