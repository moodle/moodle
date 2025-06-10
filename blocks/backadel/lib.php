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
 * @package    block_backadel
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Build the SQL query from the search params
 *
 * @return SQL
 */
function build_sql_from_search($query, $constraints) {
    $sql = "SELECT co.id, co.fullname, co.shortname, co.idnumber, cat.name
        AS category FROM {course} co, {course_categories} cat WHERE
        co.category = cat.id AND (";

    // Set up the SQL constraints.
    $constraintsqls = array();

    // Loop through the provided constraints and build the SQL contraints.
    foreach ($constraints as $c) {
        if (in_array($c->operator, array('LIKE', 'NOT LIKE'))) {
            $parts = array();

            foreach (explode('|', $c->search_terms) as $s) {
                $parts[] = "$c->criteria $c->operator '%{$s}%'";
            }

            $constraintsqls[] = '(' . implode(' OR ', $parts) . ')';
        } else {
            $instr = str_replace('|', "', '", $c->search_terms);

            $constraintsqls[] = "($c->criteria $c->operator ('$instr'))";
        }
    }

    // Return the appropriate SQL.
    return $sql . implode(" $query->type ", $constraintsqls) . ');';
}

/**
 * Delete courses based on supplied courseids
 *
 * @return bool
 */
function backadel_delete_course($courseid) {
    global $DB;
    // Get the course object based on the supplied courseid.
    $course = $DB->get_record('course', array('id' => $courseid));

    // Delete the course.
    if (delete_course($course, false)) {
        fix_course_sortorder();
        return true;
    } else {
        return false;
    }
}

/**
 * Generates the last bit of the backup .zip's filename based on the
 * pattern and roles that the admin chose in config.
 *
 * @return $suffix
 */
function generate_suffix($courseid) {
    $suffix = '';

    // Grab the allowed suffixes.
    $field = get_config('block_backadel', 'suffix');

    // Grab the administratively selected roles.
    $roleids = explode(',', get_config('block_backadel', 'roles'));

    // Grab the course context.
    $context = context_course::instance($courseid);

    // When NOT using fullname (which we might want to avoid anyway).
    if ($field != 'fullname') {
        // Loop through all the administratively selected roles.
        foreach ($roleids as $r) {
            // If the role has any users in the course, return them.
            if ($users = get_role_users($r, $context, false)) {
                // Loop through the users and grab the appropriate suffix.
                foreach ($users as $k => $v) {
                    $suffix .= '_' . $v->$field;
                }
            }
        }
    } else {
        // Loop through all the administratively selected roles.
        foreach ($roleids as $r) {
            // If the role has any users in the course, return them.
            if ($users = get_role_users($r, $context, false)) {
                // Loop through the users and grab the appropriate suffix.
                foreach ($users as $k => $v) {
                    $suffix .= '_' . $v->firstname . $v->lastname;
                }
            }
        }
    }
    return $suffix;
}

/**
 * Instantiate the moodle backup subsystem
 * and backup the course.
 *
 * @return true
 */
function backadel_backup_course($course) {
    global $CFG;

    // Required files for the backups.
    require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
    require_once($CFG->dirroot . '/backup/controller/backup_controller.class.php');
    require_once($CFG->dirroot . '/backup/util/helper/backup_cron_helper.class.php');

    // Generate the Filename suffix.
    $suffix = generate_suffix($course->id);
    $matchers = array('/\s/', '/\//');

    // Build the basis for the filename.
    $safeshort = preg_replace($matchers, '-', $course->shortname);

    // Assemble the filename from constituent parts.
    $backadelfile = "backadel-{$safeshort}{$suffix}.zip";

    // Build the path.
    $backadelpath = $CFG->dataroot . get_config('block_backadel', 'path');

    // Set the userid.
    $userid = 2;

    // Set up the config for backup.
    $config = get_config('backup');

    // Grab the specified directory for automated backups.
    $dir = $config->backup_auto_destination;

    // The default outcome here is success.
    $outcome = 1;

    // Grab the backup storage location (0: course, 1: specified dir, 2: both).
    $storage = (int)$config->backup_auto_storage;

    // Build the backup controller.
    $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE, backup::INTERACTIVE_NO,
        backup::MODE_AUTOMATED, $userid);

    // Try some stuff.
    try {

        // Set up the stuff to set the default filename.
        $format = $bc->get_format();
        $type = $bc->get_type();
        $id = $bc->get_id();
        $users = $bc->get_plan()->get_setting('users')->get_value();
        $anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
        $incfiles = (bool)$config->backup_auto_files;
        $bc->get_plan()->get_setting('filename')->set_value(backup_plan_dbops::get_default_backup_filename($format, $type,
            $id, $users, $anonymised, false, $incfiles));

        // Set the filename PRIOR to completing the backup, we'll do some checking later on.
        $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $course->id, $users, $anonymised,
            !$config->backup_shortname);

        // Set the status for the backup logs.
        $bc->set_status(backup::STATUS_AWAITING);

        // Do the backup.
        $bc->execute_plan();
        $results = $bc->get_results();
        $outcome = outcome_from_results($results);

        // May be empty if file already moved to target location.
        $file = $results['backup_destination'];

        // Get the full path filename for the Moodle backup.
        $mfname = $dir . '/' . $filename;

        // Due to moodle backup not returning filenames and naming them with the minute attached, we have to do stupid stuff.
        if ($storage !== 0 && !file_exists($mfname)) {

            // Print this to the task logs so we see it initially failed.
            mtrace('Moodle backup file does not exist at initial location - ' . $mfname);

            // Grab a secondary filename after the abckup is completed in case the initial location is incorrect..
            $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $course->id, $users, $anonymised,
                !$config->backup_shortname);

            // Set the new location based on the updated filename.
            $mfname = $dir . '/' . $filename;

            // Print the new location so we can see what's going on.
            mtrace('Trying secondary location - ' . $mfname);

            // Some extra sanity checking to make sure the file name has not changed since we updated it and now.
            if (!file_exists($mfname)) {

                // Reset the filename yet again.
                $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $course->id, $users, $anonymised, !$config->backup_shortname);

                // Rebuild the full path based on the new filename... again.
                $mfname = $dir . '/' . $filename;

                // Print the final location and hope we don't fail anymore.
                mtrace('Secondary location does not exist, using tertiary location - ' . $mfname);
            }
        }

        // Get the full path filename for the proposed backadel filename.
        $bdfname = $backadelpath . $backadelfile;

        // Copy the file from the course storage area to backadel and cleanly delete it.
        if ($storage === 0 && !empty($backadelpath) && is_dir($backadelpath) && is_writeable($backadelpath)) {

            // Try to copy the file from the course file-area to the backadel area.
            if ($file->copy_content_to($bdfname)) {

                // Yay! It worked. Log it.
                $bc->log('Backup file copied successfully to the specified backadel folder - ',
                        backup::LOG_INFO, $bdfname);

                // Generate different output if the course has no instructors.
                if (!empty($suffix)) {
                    mtrace('Copy successful from course automated backup area to - ' . $bdfname);
                } else {
                    mtrace('Copy successful for course without instructors - ' . $bdfname);
                }

                // Set the outcome and buresult for later use.
                $outcome = 1;
                $buresult = true;

                // Delete the file from the course backup area on successful save to backadel file area.
                if (!empty($file)) {
                    $file->delete();
                }
            } else {

                // The copy_contents_to failed. Log it accordingly.
                $bc->log('Attempt to copy backup file to the specified backadel failed - ',
                        backup::LOG_ERROR, $bdfname);
                mtrace('Copy failed from course automated backup area to - ' . $bdfname);

                // Set the outcome and buresult for later use.
                $outcome = 0;
                $buresult = false;

                // Delete the file from the course backup area on failed save to backadel file area.
                if (!empty($file)) {
                    $file->delete();
                }
            }

        // We're now working with a specified directory for backup storage.
        } else if ($storage !== 0 && (empty($backadelpath) || !is_dir($backadelpath) || !is_writable($backadelpath))) {

            // The backadel path is either not specified or not a directory or not writeable. Log it accordingly.
            $bc->log('Specified backup directory is not writable - ', backup::LOG_ERROR, $backadelpath);
            mtrace('Backadel failed: Specified backup directory is not writable - ' . $backadelpath);

            // Set the outcome and buresult for later use.
            $outcome = 0;
            $buresult = false;

            // Unlink the Moodle backup.
            @unlink($mfname);

        // Looks like we the backadel path is specified and a writeable directory. Let's double check to see if the Moodle backup is missing.
        } else if ($storage !== 0 && !empty($backadelpath) && is_dir($backadelpath) && is_writeable($backadelpath) && !file_exists($mfname)) {

            // The file does not exist, log accordingly.
            $bc->log('Source backup file does not exist - ', backup::LOG_ERROR, $mfname);
            mtrace('Backadel failed: Source backup file does not exist - ' . $mfname);

            // Set the outcome and buresult for later use.
            $outcome = 0;
            $buresult = false;

            // Unlink the Moodle backup.
            @unlink($mfname);

        // Now we're cooking with gas and everything looks good. Let's triple check everything is good to go.
        } else if ($storage !== 0 && !empty($backadelpath) && is_dir($backadelpath) && is_writeable($backadelpath) && file_exists($mfname)) {

            // Try to rename the file from the Moodle file location to the backadel file location.
            if (rename($mfname, $bdfname)) {

                // Yay! Everything worked. Let's log accordingly.
                $bc->log('Rename successful - ', backup::LOG_INFO, $backadelpath);

                // Generate different output if the course has no instructors.
                if (!empty($suffix)) {
                    mtrace('Rename successful - ' . $mfname . ' to ' . $bdfname);
                } else {
                    mtrace('Rename successful for course without instructors - ' . $bdfname);
                }

                // Set the outcome and buresult for later use.
                $outcome = 1;
                $buresult = true;
            } else {

                // The rename failed. Log accordingly.
                $bc->log('Rename failed - ', backup::LOG_ERROR, $bdfname);
                mtrace('Rename failed - ' . $mfname . ' to ' . $bdfname);

                // Set the outcome and buresult for later use.
                $outcome = 0;
                $buresult = false;

                // The rename failed, so let's delete this file from the Moodle specified directory for automated backups.
                if ($storage !== 0 && !empty($mfname) && file_exists($mfname)) {
                    @unlink($mfname);
                }
            }
        } else {
            // Super catch-all for something else failing. Better safe than ignorant.
            $bc->log('Something else failed - ', backup::LOG_ERROR, $bdfname);
            mtrace('Something else failed generating ' . $bdfname);

            // Set the outcome and buresult for later use.
            $outcome = 0;
            $buresult = false;
        }

    // Catch and log stuff.
    } catch (moodle_exception $e) {
        $bc->log('backup_auto_failed_on_course', backup::LOG_ERROR, $course->shortname); // Log error header.
        $bc->log('Exception: ' . $e->errorcode, backup::LOG_ERROR, $e->a, 1); // Log original exception problem.
        $bc->log('Debug: ' . $e->debuginfo, backup::LOG_DEBUG, null, 1); // Log original debug information.
        $outcome = 0;
    }

    // destroy and unset the backup controller.
    $bc->destroy();
    unset($bc);

    // Return either true or false.
    return $buresult;
}


    /**
     * Returns the backup outcome by analysing its results.
     *
     * @param array $results returned by a backup
     * @return int {@link self::BACKUP_STATUS_OK} and other constants
     */
    function outcome_from_results($results) {
        $outcome = 1;
        foreach ($results as $code => $value) {
            // Each possible error and warning code has to be specified in this switch
            // which basically analyses the results to return the correct backup status.
            switch ($code) {
                case 'missing_files_in_pool':
                    $outcome = 4;
                    break;
            }
            // If we found the highest error level, we exit the loop.
            if ($outcome == 0) {
                break;
            }
        }
        return $outcome;
    }


/**
 * Email the admins
 *
 */
function backadel_email_admins($errors) {
    $dellink = new moodle_url('/blocks/backadel/delete.php');

    $subject = get_string('email_subject', 'block_backadel');
    $from = get_string('email_from', 'block_backadel');
    $messagetext = $errors . "\n\n" . get_string('email_body', 'block_backadel') . $dellink;

    foreach (get_admins() as $admin) {
        email_to_user($admin, $from, $subject, $messagetext);
    }
}
